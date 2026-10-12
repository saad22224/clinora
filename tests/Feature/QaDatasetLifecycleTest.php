<?php

use App\Models\Clinic;
use App\Models\Finance;
use App\Models\MedicalFile;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Services\FinanceReportService;
use App\Support\Qa\QaAccountCatalog;
use App\Support\Qa\QaDatasetBuilder;
use App\Support\Qa\QaManifest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Auth::logout();

    config([
        'clinora.features.ai_enabled' => false,
        'clinora.features.whatsapp_enabled' => false,
        'clinora.qa.allow_writes' => true,
        // Match the phpunit sqlite database name so guarded writes can run in tests only.
        'clinora.qa.allowed_database' => ':memory:',
        'clinora.qa.files_disk' => 'local',
    ]);

    Storage::fake('local');

    $dir = storage_path('app/qa-manifests');
    if (File::isDirectory($dir)) {
        foreach (File::files($dir) as $file) {
            File::delete($file->getPathname());
        }
    }
});

function generateQaDataset(string $profile = 'small'): string
{
    $exit = Artisan::call('clinora:qa-generate', [
        '--profile' => $profile,
        '--force' => true,
        '--seed' => 42,
    ]);

    expect($exit)->toBe(0);

    $runId = app(QaManifest::class)->latestRunId();
    expect($runId)->not->toBeNull();

    return $runId;
}

test('generation is refused against clinora database name', function () {
    config([
        'clinora.qa.allowed_database' => 'clinora_qa',
        'database.connections.sqlite.database' => 'clinora',
    ]);

    $exit = Artisan::call('clinora:qa-generate', [
        '--profile' => 'small',
        '--force' => true,
    ]);

    expect($exit)->toBe(1)
        ->and(Artisan::output())->toContain('clinora_qa')
        ->and(User::query()->where('email', 'like', 'qa+%')->count())->toBe(0);
});

test('generated dataset has valid foreign keys tenant alignment and org inheritance', function () {
    $runId = generateQaDataset();
    $manifest = app(QaManifest::class)->read($runId);

    expect($manifest['accounts'])->not->toBeEmpty()
        ->and(collect($manifest['accounts'])->pluck('email'))->toContain('qa+org-owner-a@clinora.test');

    $organization = Organization::query()->where('name', 'سلسلة QA')->first();
    expect($organization)->not->toBeNull();

    $branchIds = $organization->clinics()->pluck('id');
    expect(Subscription::query()->whereIn('clinic_id', $branchIds)->exists())->toBeFalse()
        ->and(Subscription::query()->where('organization_id', $organization->id)->whereNull('clinic_id')->exists())->toBeTrue();

    foreach ($manifest['records']['appointments'] as $appointmentId) {
        $appointment = \App\Models\Appointment::withoutGlobalScope(TenantScope::class)->find($appointmentId);
        $patient = Patient::withoutGlobalScope(TenantScope::class)->find($appointment->patient_id);
        expect($patient->clinic_id)->toBe($appointment->clinic_id);
    }
});

test('finance totals match independently specified expected values', function () {
    generateQaDataset();

    $clinic = Clinic::query()->where('slug', 'like', 'qa-populated-clinic%')->first();
    expect($clinic)->not->toBeNull();

    $expected = QaDatasetBuilder::EXPECTED_FINANCE_POPULATED;
    $metrics = app(FinanceReportService::class)->forClinic(
        $clinic,
        Carbon::now()->startOfMonth(),
        Carbon::now()->endOfDay()
    );

    expect(round((float) $metrics['collected_income'], 2))->toBe($expected['collected_income'])
        ->and(round((float) $metrics['expenses'], 2))->toBe($expected['expenses'])
        ->and(round((float) $metrics['net_cash'], 2))->toBe($expected['net_cash'])
        ->and(round((float) $metrics['completed_service_value'], 2))->toBe($expected['completed_service_value'])
        ->and(round((float) $metrics['outstanding'], 2))->toBe($expected['outstanding']);

    $linked = Finance::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->where('type', 'income')
        ->whereNotNull('appointment_id')
        ->with('appointment')
        ->get();

    expect($linked->contains(fn (Finance $finance) => (float) $finance->amount !== (float) $finance->appointment->price))->toBeTrue();
});

test('medical file content is created on isolated disk and cleaned up with run', function () {
    $runId = generateQaDataset();
    $manifest = app(QaManifest::class)->read($runId);

    expect($manifest['private_files'])->not->toBeEmpty();

    foreach ($manifest['private_files'] as $path) {
        Storage::disk('local')->assertExists($path);
        expect($path)->toStartWith('medical-records/qa/'.$runId);
    }

    $unrelated = User::factory()->create([
        'email' => 'unrelated-owner@example.com',
        'role' => 'owner',
    ]);
    $unrelatedClinic = Clinic::factory()->create([
        'name' => 'Real Clinic',
        'slug' => 'real-clinic-keep',
    ]);

    $exit = Artisan::call('clinora:qa-cleanup', [
        '--run' => $runId,
        '--force' => true,
    ]);

    expect($exit)->toBe(0)
        ->and(User::query()->where('email', 'like', 'qa+%')->count())->toBe(0)
        ->and(Clinic::withTrashed()->where('slug', 'like', 'qa-%')->count())->toBe(0)
        ->and(User::query()->whereKey($unrelated->id)->exists())->toBeTrue()
        ->and(Clinic::query()->whereKey($unrelatedClinic->id)->exists())->toBeTrue();

    foreach ($manifest['private_files'] as $path) {
        Storage::disk('local')->assertMissing($path);
    }

    expect(app(QaManifest::class)->latestRunId())->toBeNull();
});

test('repeated generation replaces prior QA run without duplicate email failures', function () {
    $first = generateQaDataset();
    $second = generateQaDataset();

    expect($second)->not->toBe($first)
        ->and(User::query()->where('email', 'qa+org-owner-a@clinora.test')->count())->toBe(1)
        ->and(Clinic::query()->where('slug', 'like', 'qa-populated-clinic%')->count())->toBe(1)
        ->and(File::exists(app(QaManifest::class)->pathFor($first)))->toBeFalse()
        ->and(File::exists(app(QaManifest::class)->pathFor($second)))->toBeTrue();
});

test('account listing matches seeded credentials and roles', function () {
    generateQaDataset();

    $exit = Artisan::call('clinora:qa-accounts');
    $output = Artisan::output();

    expect($exit)->toBe(0)
        ->and($output)->toContain('qa+org-owner-a@clinora.test')
        ->and($output)->toContain('qa+populated-secretary@clinora.test')
        ->and($output)->toContain(QaAccountCatalog::PASSWORD);

    $owner = User::query()->where('email', 'qa+populated-owner@clinora.test')->first();
    $secretary = User::query()->where('email', 'qa+populated-secretary@clinora.test')->first();
    $orgOwner = User::query()->where('email', 'qa+org-owner-a@clinora.test')->first();

    expect($owner->hasRole('clinic_owner'))->toBeTrue()
        ->and($secretary->can('manage_appointments'))->toBeTrue()
        ->and($secretary->can('manage_patients'))->toBeFalse()
        ->and($orgOwner->clinic_id)->toBeNull()
        ->and($orgOwner->belongsToOrganization())->toBeTrue();
});

test('qa validate passes for a generated dataset', function () {
    generateQaDataset();

    $exit = Artisan::call('clinora:qa-validate');

    expect($exit)->toBe(0)
        ->and(Artisan::output())->toContain('QA validate passed');
});

test('generated accounts support authentication permission finance and medical workflows', function () {
    generateQaDataset();

    Role::findOrCreate('clinic_owner', 'web');
    Permission::findOrCreate('manage_patients', 'web');

    $owner = User::query()->where('email', 'qa+populated-owner@clinora.test')->first();
    $secretary = User::query()->where('email', 'qa+populated-secretary@clinora.test')->first();
    $orgOwner = User::query()->where('email', 'qa+org-owner-a@clinora.test')->first();
    $organization = $orgOwner->primaryOrganization();
    $clinic = Clinic::query()->where('slug', 'like', 'qa-populated-clinic%')->first();

    $this->post(route('login.post'), [
        'email' => $owner->email,
        'password' => QaAccountCatalog::PASSWORD,
    ])->assertRedirect(route('clinic.dashboard'));

    Auth::logout();

    $this->post(route('login.post'), [
        'email' => $orgOwner->email,
        'password' => QaAccountCatalog::PASSWORD,
    ])->assertRedirect(route('organization.dashboard', $organization));

    Auth::logout();

    $this->actingAs($secretary)->get(route('clinic.patients.index'))->assertForbidden();
    $secretary->givePermissionTo('manage_patients');
    $this->actingAs($secretary)->get(route('clinic.patients.index'))->assertOk();

    $file = MedicalFile::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->first();

    expect($file)->not->toBeNull();
    Storage::disk('local')->assertExists($file->file_path);

    $this->actingAs($owner)->get(route('clinic.records.download', $file))->assertOk();

    $peerOwner = User::query()->where('email', 'qa+peer-owner@clinora.test')->first();
    $this->actingAs($peerOwner)->get(route('clinic.records.download', $file))->assertForbidden();
});

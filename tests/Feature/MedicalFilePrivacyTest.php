<?php

use App\Models\Clinic;
use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function seedClinicWithOwner(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة الملفات',
        'slug' => 'files-clinic',
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'single',
        'type' => 'single',
        'price' => 0,
        'duration_in_days' => 30,
        'max_clinics' => 1,
        'is_active' => true,
    ]);

    Subscription::create([
        'clinic_id' => $clinic->id,
        'plan_id' => $plan->id,
        'plan' => $plan->name,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => $clinic->id,
    ]);
    $owner->assignRole('clinic_owner');

    $patient = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض',
        'phone' => '01000000000',
    ]);

    return [$clinic, $owner, $patient];
}

test('medical file is stored on private disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    [$clinic, $owner, $patient] = seedClinicWithOwner();

    $file = UploadedFile::fake()->create('lab.pdf', 100, 'application/pdf');

    $this->actingAs($owner)->post(route('clinic.records.store'), [
        'patient_id' => $patient->id,
        'type' => 'تحليل',
        'file' => $file,
    ])->assertRedirect();

    $record = MedicalFile::first();
    expect($record)->not->toBeNull();
    Storage::disk('local')->assertExists($record->file_path);
    Storage::disk('public')->assertMissing($record->file_path);
});

test('cross tenant cannot download medical file', function () {
    Storage::fake('local');

    [$clinicA, $ownerA, $patientA] = seedClinicWithOwner();

    $path = 'medical-records/secret.pdf';
    Storage::disk('local')->put($path, 'secret');

    $file = MedicalFile::create([
        'clinic_id' => $clinicA->id,
        'patient_id' => $patientA->id,
        'type' => 'أشعة',
        'file_path' => $path,
    ]);

    $clinicB = Clinic::create([
        'name' => 'عيادة ثانية',
        'slug' => 'files-clinic-b',
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'single-b',
        'type' => 'single',
        'price' => 0,
        'duration_in_days' => 30,
        'max_clinics' => 1,
        'is_active' => true,
    ]);

    Subscription::create([
        'clinic_id' => $clinicB->id,
        'plan_id' => $plan->id,
        'plan' => $plan->name,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
    ]);

    $ownerB = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => $clinicB->id,
    ]);
    $ownerB->assignRole('clinic_owner');

    // Tenant scope hides foreign records (404) rather than revealing existence via 403.
    $this->actingAs($ownerB)->get(route('clinic.records.download', $file))->assertNotFound();
});

test('guest cannot download medical file', function () {
    Storage::fake('local');

    [$clinic, $owner, $patient] = seedClinicWithOwner();
    $path = 'medical-records/private.pdf';
    Storage::disk('local')->put($path, 'private');

    $file = MedicalFile::create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'تحليل',
        'file_path' => $path,
    ]);

    $this->get(route('clinic.records.download', $file))->assertRedirect(route('login'));
});

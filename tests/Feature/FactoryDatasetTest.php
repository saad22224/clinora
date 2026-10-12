<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Drug;
use App\Models\Finance;
use App\Models\MedicalFile;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use App\Models\Plan;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use Database\Seeders\QaDatasetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    Auth::logout();
});

test('clinic organization plan and subscription factories create valid records', function () {
    $organization = Organization::factory()->create();
    $chainPlan = Plan::factory()->chain(5)->create();
    $singlePlan = Plan::factory()->single()->create();

    $orgSubscription = Subscription::factory()
        ->forOrganization($organization)
        ->withPlan($chainPlan)
        ->active()
        ->create();

    $standalone = Clinic::factory()->standalone()->create();
    $clinicSubscription = Subscription::factory()
        ->forClinic($standalone)
        ->withPlan($singlePlan)
        ->active()
        ->create();

    $branch = Clinic::factory()->forOrganization($organization)->create();

    expect($orgSubscription->clinic_id)->toBeNull()
        ->and($orgSubscription->organization_id)->toBe($organization->id)
        ->and($clinicSubscription->organization_id)->toBeNull()
        ->and($clinicSubscription->clinic_id)->toBe($standalone->id)
        ->and($branch->organization_id)->toBe($organization->id)
        ->and(Subscription::query()->where('clinic_id', $branch->id)->exists())->toBeFalse();
});

test('appointment factory keeps patient doctor and clinic aligned', function () {
    $clinic = Clinic::factory()->create();
    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $patient = Patient::factory()->forClinic($clinic)->create();
    $visitType = VisitType::factory()->forClinic($clinic)->create(['price' => 180]);

    $appointment = Appointment::factory()
        ->forClinic($clinic)
        ->completed()
        ->partial()
        ->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_type_id' => $visitType->id,
            'price' => $visitType->price,
        ]);

    expect($appointment->clinic_id)->toBe($clinic->id)
        ->and($appointment->patient->clinic_id)->toBe($clinic->id)
        ->and($appointment->doctor->clinic_id)->toBe($clinic->id)
        ->and($appointment->visitType->clinic_id)->toBe($clinic->id)
        ->and($appointment->status)->toBe('completed')
        ->and($appointment->payment_status)->toBe('partial')
        ->and((float) $appointment->amount_paid)->toBe(90.0);
});

test('finance factory can link income without equating appointment price', function () {
    $clinic = Clinic::factory()->create();
    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $patient = Patient::factory()->forClinic($clinic)->create();

    $appointment = Appointment::factory()
        ->forClinic($clinic)
        ->completed()
        ->unpaid()
        ->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'price' => 200,
        ]);

    $finance = Finance::factory()
        ->forAppointment($appointment, 50)
        ->create();

    expect($finance->type)->toBe('income')
        ->and((float) $finance->amount)->toBe(50.0)
        ->and((float) $appointment->price)->toBe(200.0)
        ->and($finance->appointment_id)->toBe($appointment->id)
        ->and($finance->clinic_id)->toBe($clinic->id);
});

test('prescription medical file diagnosis and drug factories create related rows', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->forClinic($clinic)->create();
    $appointment = Appointment::factory()
        ->forClinic($clinic)
        ->create(['patient_id' => $patient->id]);

    $prescription = Prescription::factory()
        ->forAppointment($appointment)
        ->withItems(3)
        ->create();

    $file = MedicalFile::factory()->forPatient($patient)->xray()->create();
    $diagnosis = PatientDiagnosis::factory()->forPatient($patient)->create();
    $drug = Drug::factory()->create();

    expect($prescription->items)->toHaveCount(3)
        ->and($prescription->clinic_id)->toBe($clinic->id)
        ->and($file->clinic_id)->toBe($clinic->id)
        ->and($diagnosis->clinic_id)->toBe($clinic->id)
        ->and($drug->commercial_name_en)->not->toBeEmpty()
        ->and(PrescriptionItem::query()->where('prescription_id', $prescription->id)->count())->toBe(3);
});

test('user factory role states assign spatie roles', function () {
    $clinic = Clinic::factory()->create();

    $owner = User::factory()->clinicOwner()->forClinic($clinic)->create();
    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $secretary = User::factory()->restrictedSecretary()->forClinic($clinic)->create();
    $admin = User::factory()->superadmin()->create();

    expect($owner->hasRole('clinic_owner'))->toBeTrue()
        ->and($doctor->hasRole('doctor'))->toBeTrue()
        ->and($secretary->hasRole('secretary'))->toBeTrue()
        ->and($secretary->can('manage_appointments'))->toBeTrue()
        ->and($secretary->can('manage_patients'))->toBeFalse()
        ->and($admin->hasRole('superadmin'))->toBeTrue()
        ->and($admin->clinic_id)->toBeNull();
});

test('qa dataset seeder builds interconnected multi-tenant data with correct inheritance', function () {
    $this->seed(QaDatasetSeeder::class);

    $organization = Organization::query()->where('name', 'سلسلة QA')->first();
    expect($organization)->not->toBeNull();

    $orgSubscription = Subscription::query()
        ->where('organization_id', $organization->id)
        ->whereNull('clinic_id')
        ->first();

    expect($orgSubscription)->not->toBeNull()
        ->and($orgSubscription->is_active)->toBeTrue();

    $branchIds = $organization->clinics()->pluck('id');
    expect($branchIds)->toHaveCount(3)
        ->and(Subscription::query()->whereIn('clinic_id', $branchIds)->exists())->toBeFalse();

    $populated = Clinic::query()->where('slug', 'like', 'qa-populated-clinic%')->first();
    $empty = Clinic::query()->where('slug', 'like', 'qa-empty-clinic%')->first();
    $expired = Clinic::query()->where('slug', 'like', 'qa-expired-clinic%')->first();
    $peer = Clinic::query()->where('slug', 'like', 'qa-peer-clinic%')->first();

    expect($populated)->not->toBeNull()
        ->and($empty)->not->toBeNull()
        ->and($expired)->not->toBeNull()
        ->and($peer)->not->toBeNull();

    expect(Patient::withoutGlobalScope(TenantScope::class)->where('clinic_id', $populated->id)->count())->toBeGreaterThan(0)
        ->and(Patient::withoutGlobalScope(TenantScope::class)->where('clinic_id', $empty->id)->count())->toBe(0)
        ->and(Appointment::withoutGlobalScope(TenantScope::class)->where('clinic_id', $populated->id)->count())->toBeGreaterThan(0)
        ->and(Finance::withoutGlobalScope(TenantScope::class)->where('clinic_id', $populated->id)->count())->toBeGreaterThan(0)
        ->and(Prescription::withoutGlobalScope(TenantScope::class)->where('clinic_id', $populated->id)->count())->toBeGreaterThan(0)
        ->and(MedicalFile::withoutGlobalScope(TenantScope::class)->where('clinic_id', $populated->id)->count())->toBeGreaterThan(0);

    $expiredSubscription = Subscription::query()->where('clinic_id', $expired->id)->first();
    expect($expiredSubscription)->not->toBeNull()
        ->and($expiredSubscription->ends_at->isPast())->toBeTrue()
        ->and($expiredSubscription->is_active)->toBeFalse();

    $restricted = User::query()->where('email', 'qa+populated-secretary@clinora.test')->first();
    expect($restricted)->not->toBeNull()
        ->and($restricted->can('manage_appointments'))->toBeTrue()
        ->and($restricted->can('manage_billing'))->toBeFalse();

    expect(User::query()->where('email', 'qa+org-owner-a@clinora.test')->exists())->toBeTrue()
        ->and(Clinic::query()->where('slug', 'like', 'qa-inactive-clinic%')->exists())->toBeTrue();

    $orgMembership = OrganizationUser::query()
        ->where('organization_id', $organization->id)
        ->where('role', 'admin')
        ->first();

    expect($orgMembership)->not->toBeNull()
        ->and($orgMembership->canEnterClinic())->toBeFalse()
        ->and($orgMembership->canViewReports())->toBeTrue();

    // Cross-tenant: peer patients must not share clinic_id with populated clinic.
    $populatedPatientIds = Patient::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $populated->id)
        ->pluck('id');
    $peerPatients = Patient::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $peer->id)
        ->pluck('id');

    expect($populatedPatientIds->intersect($peerPatients))->toBeEmpty()
        ->and(Drug::query()->count())->toBeGreaterThan(0);
});

test('subscription expired and paid appointment states match schema semantics', function () {
    $clinic = Clinic::factory()->create();
    $plan = Plan::factory()->single()->create();

    $expired = Subscription::factory()
        ->forClinic($clinic)
        ->withPlan($plan)
        ->expired()
        ->create();

    $appointment = Appointment::factory()
        ->forClinic($clinic)
        ->paid()
        ->create(['price' => 250]);

    expect($expired->is_active)->toBeFalse()
        ->and($expired->ends_at->lt(now()))->toBeTrue()
        ->and($appointment->payment_status)->toBe('paid')
        ->and((float) $appointment->amount_paid)->toBe(250.0);
});

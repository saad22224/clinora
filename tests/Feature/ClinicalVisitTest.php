<?php

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use App\Models\Plan;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function visitClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة الزيارات',
        'slug' => 'visit-clinic-'.uniqid(),
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
        'name' => 'طبيب الزيارة',
    ]);
    $owner->assignRole('clinic_owner');

    $patient = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض زيارة',
        'phone' => '01055556666',
    ]);

    return compact('clinic', 'owner', 'patient');
}

test('visit can be created from patient medical file and listed chronologically', function () {
    ['clinic' => $clinic, 'owner' => $owner, 'patient' => $patient] = visitClinic();

    $visitedAt = now()->subDay()->format('Y-m-d H:i:s');

    $this->actingAs($owner)
        ->post(route('clinic.visits.store', $patient), [
            'visited_at' => $visitedAt,
        ])
        ->assertRedirect();

    $visit = Visit::withoutGlobalScope(TenantScope::class)->sole();

    expect($visit->patient_id)->toBe($patient->id)
        ->and($visit->clinic_id)->toBe($clinic->id)
        ->and($visit->chief_complaint)->toBeNull()
        ->and($visit->clinical_notes)->toBeNull();

    expect(PatientDiagnosis::withoutGlobalScope(TenantScope::class)->where('visit_id', $visit->id)->count())->toBe(0);

    $this->actingAs($owner)
        ->put(route('clinic.visits.update', [$patient, $visit]), [
            'chief_complaint' => 'صداع',
            'clinical_notes' => 'ملاحظات أولية',
        ])
        ->assertRedirect();

    $this->actingAs($owner)
        ->post(route('clinic.visits.diagnoses.store', [$patient, $visit]), [
            'diagnosis' => 'صداع توتري',
        ])
        ->assertRedirect();

    expect($visit->fresh()->chief_complaint)->toBe('صداع')
        ->and(PatientDiagnosis::withoutGlobalScope(TenantScope::class)->where('visit_id', $visit->id)->sole()->diagnosis)->toBe('صداع توتري');

    $this->actingAs($owner)
        ->get(route('clinic.patients.medical-file', $patient))
        ->assertOk()
        ->assertSee('صداع', false)
        ->assertDontSee('تشخيص أولي (اختياري)', false);

    $this->actingAs($owner)
        ->get(route('clinic.visits.show', [$patient, $visit]))
        ->assertOk()
        ->assertSee('صداع توتري', false);
});

test('completed visits still accept later diagnosis amendments', function () {
    ['owner' => $owner, 'patient' => $patient] = visitClinic();

    $visit = Visit::create([
        'clinic_id' => $patient->clinic_id,
        'patient_id' => $patient->id,
        'visited_at' => now()->subDays(3),
        'status' => 'completed',
        'clinical_notes' => 'زيارة قديمة',
    ]);

    $this->actingAs($owner)
        ->post(route('clinic.visits.diagnoses.store', [$patient, $visit]), [
            'diagnosis' => 'تشخيص لاحق',
            'notes' => 'أُضيف بعد الإكمال',
        ])
        ->assertRedirect();

    expect(PatientDiagnosis::withoutGlobalScope(TenantScope::class)->where('visit_id', $visit->id)->sole()->diagnosis)
        ->toBe('تشخيص لاحق');
});

test('visits are isolated between clinics', function () {
    ['owner' => $ownerA, 'patient' => $patientA] = visitClinic();
    ['owner' => $ownerB] = visitClinic();

    $visit = Visit::create([
        'clinic_id' => $patientA->clinic_id,
        'patient_id' => $patientA->id,
        'visited_at' => now(),
        'status' => 'open',
    ]);

    $this->actingAs($ownerB)
        ->get(route('clinic.visits.show', [$patientA, $visit]))
        ->assertNotFound();
});

test('standalone diagnosis create from medical file is rejected', function () {
    ['owner' => $owner, 'patient' => $patient] = visitClinic();

    $this->actingAs($owner)
        ->post(route('clinic.patients.diagnoses.store', $patient), [
            'diagnosis' => 'تشخيص يتيم',
            'diagnosed_at' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('diagnosis');

    expect(PatientDiagnosis::withoutGlobalScope(TenantScope::class)->count())->toBe(0);
});

test('diagnosis can be updated inside its visit', function () {
    ['owner' => $owner, 'patient' => $patient] = visitClinic();

    $visit = Visit::create([
        'clinic_id' => $patient->clinic_id,
        'patient_id' => $patient->id,
        'visited_at' => now(),
        'status' => 'open',
    ]);

    $diagnosis = PatientDiagnosis::create([
        'clinic_id' => $patient->clinic_id,
        'patient_id' => $patient->id,
        'visit_id' => $visit->id,
        'diagnosis' => 'قديم',
        'diagnosed_at' => now(),
    ]);

    $this->actingAs($owner)
        ->put(route('clinic.visits.diagnoses.update', [$patient, $visit, $diagnosis]), [
            'diagnosis' => 'محدث',
            'notes' => 'ملاحظة',
        ])
        ->assertRedirect();

    expect($diagnosis->fresh()->diagnosis)->toBe('محدث');
});

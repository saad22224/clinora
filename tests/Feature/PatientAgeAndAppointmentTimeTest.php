<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function ageTimeClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة العمر والوقت',
        'slug' => 'age-time-clinic-'.uniqid(),
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

    $visitType = VisitType::create([
        'clinic_id' => $clinic->id,
        'name' => 'كشف',
        'price' => 100,
    ]);

    return compact('clinic', 'owner', 'visitType');
}

test('patients index can be filtered by name or phone search', function () {
    ['clinic' => $clinic, 'owner' => $owner] = ageTimeClinic();

    Patient::withoutGlobalScope(TenantScope::class)->create([
        'clinic_id' => $clinic->id,
        'name' => 'أحمد حسن',
        'phone' => '01099998888',
    ]);
    Patient::withoutGlobalScope(TenantScope::class)->create([
        'clinic_id' => $clinic->id,
        'name' => 'سارة علي',
        'phone' => '01122223333',
    ]);

    $this->actingAs($owner)
        ->get(route('clinic.patients.index', ['search' => 'أحمد']))
        ->assertOk()
        ->assertSee('أحمد حسن', false)
        ->assertDontSee('سارة علي', false);

    $this->actingAs($owner)
        ->get(route('clinic.patients.index', ['search' => '0112222']))
        ->assertOk()
        ->assertSee('سارة علي', false)
        ->assertDontSee('أحمد حسن', false);
});

test('patient age is filled from dob when age is left empty', function () {
    ['clinic' => $clinic, 'owner' => $owner] = ageTimeClinic();

    $dob = now()->subYears(49)->subMonths(2)->toDateString();

    $this->actingAs($owner)
        ->post(route('clinic.patients.store'), [
            'name' => 'مريض عمر',
            'phone' => '01011112222',
            'dob' => $dob,
            'age' => null,
        ])
        ->assertRedirect(route('clinic.patients.index'));

    $patient = Patient::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->sole();

    expect($patient->age)->toBe(49)
        ->and($patient->displayAge())->toBe(49);
});

test('display age follows birthday even when stored age is stale', function () {
    ['clinic' => $clinic] = ageTimeClinic();

    $patient = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض عيد ميلاد',
        'phone' => '01011113333',
        'dob' => now()->subYears(50)->addDay()->toDateString(),
        'age' => 50,
    ]);

    expect($patient->displayAge())->toBe(49);
});

test('dashboard booking preserves selected hour and allows duplicate times', function () {
    ['clinic' => $clinic, 'owner' => $owner, 'visitType' => $visitType] = ageTimeClinic();

    $patientA = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض أ',
        'phone' => '01011114444',
    ]);
    $patientB = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض ب',
        'phone' => '01011115555',
    ]);

    $day = now()->addDay()->toDateString();

    $this->actingAs($owner)
        ->post(route('clinic.appointments.store'), [
            'patient_id' => $patientA->id,
            'visit_type_id' => $visitType->id,
            'scheduled_date' => $day,
            'scheduled_time' => '10:00',
        ])
        ->assertRedirect();

    $this->actingAs($owner)
        ->post(route('clinic.appointments.store'), [
            'patient_id' => $patientB->id,
            'visit_type_id' => $visitType->id,
            'scheduled_date' => $day,
            'scheduled_time' => '10:00',
        ])
        ->assertRedirect();

    $appointments = Appointment::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->orderBy('id')
        ->get();

    expect($appointments)->toHaveCount(2)
        ->and($appointments[0]->scheduled_at->format('Y-m-d H:i'))->toBe($day.' 10:00')
        ->and($appointments[1]->scheduled_at->format('Y-m-d H:i'))->toBe($day.' 10:00')
        ->and($appointments[0]->created_at->ne($appointments[1]->created_at) || $appointments[0]->id < $appointments[1]->id)->toBeTrue();
});

test('public booking preserves the selected hour', function () {
    ['clinic' => $clinic, 'visitType' => $visitType] = ageTimeClinic();

    $day = now()->addDays(3)->toDateString();

    $this->post(route('clinic.public.book', $clinic->slug), [
        'patient_name' => 'حجز عام',
        'patient_phone' => '01011116666',
        'visit_type_id' => $visitType->id,
        'preferred_date' => $day,
        'preferred_time' => '15:45',
    ])->assertRedirect();

    $appointment = Appointment::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->sole();

    expect($appointment->scheduled_at->format('Y-m-d H:i'))->toBe($day.' 15:45')
        ->and($appointment->source)->toBe('website_landing_page');
});

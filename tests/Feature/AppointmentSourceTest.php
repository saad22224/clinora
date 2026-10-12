<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('reception appointment stores source column', function () {
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة المواعيد',
        'slug' => 'appointments-clinic',
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
        'phone' => '01022222222',
    ]);

    $visitType = VisitType::create([
        'clinic_id' => $clinic->id,
        'name' => 'كشف',
        'price' => 100,
    ]);

    $this->actingAs($owner)->post(route('clinic.appointments.store'), [
        'patient_id' => $patient->id,
        'visit_type_id' => $visitType->id,
        'scheduled_at' => now()->addDay()->toDateString(),
        'notes' => null,
    ])->assertRedirect();

    $appointment = Appointment::first();
    expect($appointment->source)->toBe('reception_manual_entry');
});

test('public booking stores website source', function () {
    $clinic = Clinic::create([
        'name' => 'عيادة حجز',
        'slug' => 'booking-clinic',
        'is_active' => true,
        'phone' => '01000000000',
    ]);

    $doctor = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => $clinic->id,
    ]);

    $visitType = VisitType::create([
        'clinic_id' => $clinic->id,
        'name' => 'استشارة',
        'price' => 150,
    ]);

    $this->post(route('clinic.public.book', $clinic->slug), [
        'patient_name' => 'مريض جديد',
        'patient_phone' => '01033333333',
        'visit_type_id' => $visitType->id,
        'preferred_date' => now()->addDays(2)->toDateString(),
        'notes' => 'اختبار',
    ])->assertRedirect();

    $appointment = Appointment::first();
    expect($appointment)->not->toBeNull()
        ->and($appointment->source)->toBe('website_landing_page')
        ->and($appointment->doctor_id)->toBe($doctor->id);
});

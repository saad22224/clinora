<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
use App\Models\VisitType;
use App\Services\AppointmentQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('confirmation and tracking pages work with token only', function () {
    $clinic = Clinic::factory()->create(['is_active' => true, 'slug' => 'track-clinic']);
    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $visitType = VisitType::factory()->forClinic($clinic)->create();

    $appointment = app(AppointmentQueueService::class)->assignOnCreate(
        Appointment::factory()->forClinic($clinic)->create([
            'doctor_id' => $doctor->id,
            'visit_type_id' => $visitType->id,
            'scheduled_at' => now(),
            'status' => 'pending',
            'source' => 'website_landing_page',
        ])
    );

    $this->get(route('clinic.public.booking.confirm', [
        'slug' => $clinic->slug,
        'token' => $appointment->tracking_token,
    ]))->assertOk()->assertSee((string) $appointment->queue_number)->assertDontSee($appointment->patient->phone);

    $this->get(route('clinic.public.booking.track', [
        'slug' => $clinic->slug,
        'token' => $appointment->tracking_token,
    ]))->assertOk();

    $this->getJson(route('clinic.public.booking.status', [
        'slug' => $clinic->slug,
        'token' => $appointment->tracking_token,
    ]))->assertOk()
        ->assertJsonMissingPath('patient_phone')
        ->assertJsonPath('queue_number', $appointment->queue_number);
});

test('invalid tracking token returns 404', function () {
    $clinic = Clinic::factory()->create(['is_active' => true, 'slug' => 'bad-token-clinic']);

    $this->get(route('clinic.public.booking.confirm', [
        'slug' => $clinic->slug,
        'token' => str_repeat('a', 64),
    ]))->assertNotFound();
});

test('tracking token from another clinic cannot be used on this slug', function () {
    $clinicA = Clinic::factory()->create(['is_active' => true, 'slug' => 'clinic-a-track']);
    $clinicB = Clinic::factory()->create(['is_active' => true, 'slug' => 'clinic-b-track']);
    $doctor = User::factory()->doctor()->forClinic($clinicA)->create();

    $appointment = app(AppointmentQueueService::class)->assignOnCreate(
        Appointment::factory()->forClinic($clinicA)->create([
            'doctor_id' => $doctor->id,
            'scheduled_at' => now(),
            'status' => 'confirmed',
        ])
    );

    $this->get(route('clinic.public.booking.confirm', [
        'slug' => $clinicB->slug,
        'token' => $appointment->tracking_token,
    ]))->assertNotFound();
});

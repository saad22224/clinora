<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use App\Services\AppointmentQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function seedQueueClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::factory()->create(['is_active' => true]);
    $plan = Plan::factory()->single()->create();
    Subscription::factory()->forClinic($clinic)->withPlan($plan)->active()->create();

    $doctorA = User::factory()->doctor()->forClinic($clinic)->create();
    $doctorB = User::factory()->doctor()->forClinic($clinic)->create();
    $owner = User::factory()->clinicOwner()->forClinic($clinic)->create();

    $patient = Patient::factory()->forClinic($clinic)->create();
    $visitType = VisitType::factory()->forClinic($clinic)->create(['price' => 100]);

    return compact('clinic', 'doctorA', 'doctorB', 'owner', 'patient', 'visitType');
}

test('queue service assigns distinct numbers for the same doctor and day', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor, 'patient' => $patient] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $first = Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]);
    $secondPatient = Patient::factory()->forClinic($clinic)->create();
    $second = Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $secondPatient->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]);

    $first = $queue->assignOnCreate($first);
    $second = $queue->assignOnCreate($second);

    expect($first->queue_number)->toBe(1)
        ->and($second->queue_number)->toBe(2)
        ->and($first->queue_date->toDateString())->toBe($second->queue_date->toDateString());
});

test('independent doctor queues do not share numbers', function () {
    ['clinic' => $clinic, 'doctorA' => $doctorA, 'doctorB' => $doctorB] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $a = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctorA->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));
    $b = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctorB->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));

    expect($a->queue_number)->toBe(1)
        ->and($b->queue_number)->toBe(1)
        ->and($a->doctor_id)->not->toBe($b->doctor_id);
});

test('failed public booking does not create an appointment', function () {
    $clinic = Clinic::factory()->create(['is_active' => true, 'slug' => 'fail-book']);
    User::factory()->doctor()->forClinic($clinic)->create();

    $this->from(route('clinic.public.show', $clinic->slug))
        ->post(route('clinic.public.book', $clinic->slug), [
            'patient_name' => 'x',
            'patient_phone' => '',
            'preferred_date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors();

    expect(Appointment::count())->toBe(0);
});

test('invalid queue transitions are rejected', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor, 'owner' => $owner] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $appointment = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));
    $queue->complete($appointment);

    $this->actingAs($owner)
        ->post(route('clinic.appointments.queue-action', $appointment), ['action' => 'call'])
        ->assertSessionHasErrors('queue');
});

test('call next and position ahead reflect queue changes', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor, 'owner' => $owner] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $first = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));
    $second = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));

    expect($queue->positionAhead($second))->toBe(1);

    $this->actingAs($owner)->post(route('clinic.appointments.call-next'), [
        'doctor_id' => $doctor->id,
        'date' => now(config('clinora.clinic_timezone'))->toDateString(),
    ])->assertRedirect();

    expect($first->fresh()->queue_status)->toBe('called')
        ->and($queue->positionAhead($second->fresh()))->toBe(0);

    $queue->complete($first->fresh());
    expect($queue->positionAhead($second->fresh()))->toBe(0);
});

test('reschedule assigns a new queue number for the new day', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $appointment = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));

    $newDate = now()->addDays(3);
    $updated = $queue->reassignForReschedule($appointment, $newDate);

    expect($updated->queue_number)->toBe(1)
        ->and($updated->queue_date->toDateString())->toBe($newDate->timezone(config('clinora.clinic_timezone'))->toDateString())
        ->and($updated->queue_status)->toBe('waiting');
});

test('skip moves appointment out of the waiting queue', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor, 'owner' => $owner] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $appointment = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));

    $this->actingAs($owner)
        ->post(route('clinic.appointments.queue-action', $appointment), ['action' => 'skip'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($appointment->fresh()->queue_status)->toBe('skipped')
        ->and($queue->daySummary($clinic->id, $doctor->id, now())['next'])->toBeNull();
});

test('cancelling an appointment removes it from the day queue list', function () {
    ['clinic' => $clinic, 'doctorA' => $doctor, 'owner' => $owner] = seedQueueClinic();
    $queue = app(AppointmentQueueService::class);

    $appointment = $queue->assignOnCreate(Appointment::factory()->forClinic($clinic)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'confirmed',
    ]));

    $this->actingAs($owner)
        ->patch(route('clinic.appointments.update-status', $appointment), ['status' => 'cancelled'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($appointment->fresh()->status)->toBe('cancelled')
        ->and($queue->dayQueue($clinic->id, $doctor->id, now())->pluck('id'))->not->toContain($appointment->id);
});

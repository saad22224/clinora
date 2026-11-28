<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\LabOrder;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitType;
use Database\Seeders\MedicalCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function followUpClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);
    Permission::findOrCreate('manage_patients', 'web');
    Permission::findOrCreate('manage_records', 'web');

    $clinic = Clinic::create([
        'name' => 'عيادة المتابعة',
        'slug' => 'fu-clinic-'.uniqid(),
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
        'name' => 'مريض متابعة',
        'phone' => '01077776666',
    ]);

    $visitType = VisitType::create([
        'clinic_id' => $clinic->id,
        'name' => 'مراجعة',
        'price' => 150,
    ]);

    $visit = Visit::create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'visited_at' => now()->subDay(),
        'status' => 'completed',
    ]);

    return compact('clinic', 'owner', 'patient', 'visitType', 'visit');
}

test('follow-up appointment can be created from patient file and linked to visit', function () {
    ['owner' => $owner, 'patient' => $patient, 'visitType' => $visitType, 'visit' => $visit] = followUpClinic();

    $day = now()->addWeek()->toDateString();

    $this->actingAs($owner)
        ->post(route('clinic.patients.appointments.store', $patient), [
            'scheduled_date' => $day,
            'scheduled_time' => '11:30',
            'visit_type_id' => $visitType->id,
            'follow_up_of_visit_id' => $visit->id,
            'notes' => 'مراجعة',
        ])
        ->assertRedirect();

    $appointment = Appointment::withoutGlobalScope(TenantScope::class)->sole();

    expect($appointment->follow_up_of_visit_id)->toBe($visit->id)
        ->and($appointment->scheduled_at->format('Y-m-d H:i'))->toBe($day.' 11:30')
        ->and($appointment->patient_id)->toBe($patient->id);
});

test('lab orders can be created for a visit and results added after completion', function () {
    ['owner' => $owner, 'patient' => $patient, 'visit' => $visit] = followUpClinic();
    $this->seed(MedicalCatalogSeeder::class);

    $test = LabTest::query()->where('code', 'CBC')->firstOrFail();

    $this->actingAs($owner)
        ->post(route('clinic.lab-orders.store', [$patient, $visit]), [
            'lab_test_ids' => [$test->id],
            'notes' => 'صائم',
        ])
        ->assertRedirect();

    $order = LabOrder::withoutGlobalScope(TenantScope::class)->sole();
    expect($order->visit_id)->toBe($visit->id)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->name_snapshot)->not->toBeEmpty();

    Storage::fake('local');

    $this->actingAs($owner)
        ->post(route('clinic.lab-orders.results', [$patient, $visit, $order]), [
            'result_notes' => 'CBC within normal limits',
            'file' => UploadedFile::fake()->create('cbc.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('completed')
        ->and($order->fresh()->result_notes)->toBe('CBC within normal limits')
        ->and($order->medicalFiles()->count())->toBe(1);
});

test('multiple lab orders are allowed on one visit', function () {
    ['owner' => $owner, 'patient' => $patient, 'visit' => $visit] = followUpClinic();
    $this->seed(MedicalCatalogSeeder::class);
    $ids = LabTest::query()->limit(2)->pluck('id');

    $this->actingAs($owner)
        ->post(route('clinic.lab-orders.store', [$patient, $visit]), ['lab_test_ids' => [$ids[0]]])
        ->assertRedirect();

    $this->actingAs($owner)
        ->post(route('clinic.lab-orders.store', [$patient, $visit]), ['lab_test_ids' => [$ids[1]]])
        ->assertRedirect();

    expect(LabOrder::withoutGlobalScope(TenantScope::class)->where('visit_id', $visit->id)->count())->toBe(2);
});

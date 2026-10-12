<?php

use App\Models\Clinic;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function seedClinicOwnerContext(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage_patients', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage_appointments', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة الصلاحيات',
        'slug' => 'permissions-clinic',
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

    return [$clinic, $plan];
}

test('secretary without permission cannot access patients', function () {
    [$clinic] = seedClinicOwnerContext();

    $secretary = User::factory()->create([
        'role' => 'secretary',
        'clinic_id' => $clinic->id,
    ]);
    $secretary->assignRole('secretary');

    $this->actingAs($secretary)->get(route('clinic.patients.index'))->assertForbidden();
});

test('secretary with manage_patients can access patients', function () {
    [$clinic] = seedClinicOwnerContext();

    $secretary = User::factory()->create([
        'role' => 'secretary',
        'clinic_id' => $clinic->id,
    ]);
    $secretary->assignRole('secretary');
    $secretary->givePermissionTo('manage_patients');

    $this->actingAs($secretary)->get(route('clinic.patients.index'))->assertOk();
});

test('owner can access patients without explicit permission', function () {
    [$clinic] = seedClinicOwnerContext();

    $owner = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => $clinic->id,
    ]);
    $owner->assignRole('clinic_owner');

    $this->actingAs($owner)->get(route('clinic.patients.index'))->assertOk();
});

<?php

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('admin cannot create a standalone clinic with a chain plan', function () {
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $admin = User::factory()->create(['role' => 'superadmin']);
    $admin->assignRole('superadmin');

    $chainPlan = Plan::create([
        'name' => 'سلاسل',
        'type' => 'chain',
        'price' => 100,
        'duration_in_days' => 365,
        'max_clinics' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($admin)->post(route('admin.clinics.store'), [
        'clinic_name' => 'عيادة خاطئة',
        'owner_name' => 'مالك',
        'owner_email' => 'wrong-chain@example.com',
        'owner_password' => 'password123',
        'plan_id' => $chainPlan->id,
    ])->assertSessionHasErrors('plan_id');

    expect(Clinic::query()->where('name', 'عيادة خاطئة')->exists())->toBeFalse();
});

test('convert command turns misassigned chain clinic into organization', function () {
    $clinic = Clinic::create([
        'name' => 'saad',
        'slug' => 'saad-convert',
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'سلاسل العيادات',
        'type' => 'chain',
        'price' => 200,
        'duration_in_days' => 365,
        'max_clinics' => 10,
        'is_active' => true,
    ]);

    Subscription::create([
        'clinic_id' => $clinic->id,
        'organization_id' => null,
        'plan_id' => $plan->id,
        'plan' => $plan->name,
        'starts_at' => now(),
        'ends_at' => now()->addYear(),
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'email' => 'saadon@example.com',
        'password' => Hash::make('password'),
        'clinic_id' => $clinic->id,
        'role' => 'owner',
    ]);

    $this->artisan('clinora:convert-misassigned-chain-clinics')
        ->assertSuccessful();

    $clinic->refresh();
    $owner->refresh();

    expect($clinic->organization_id)->not->toBeNull();
    expect($owner->clinic_id)->toBeNull();
    expect($owner->belongsToOrganization())->toBeTrue();

    $organization = Organization::findOrFail($clinic->organization_id);

    expect(Subscription::query()->where('organization_id', $organization->id)->whereNull('clinic_id')->exists())->toBeTrue();

    $this->post(route('login.post'), [
        'email' => 'saadon@example.com',
        'password' => 'password',
    ])->assertRedirect(route('organization.dashboard', $organization));
});

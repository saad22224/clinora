<?php

use App\Models\Clinic;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('super admin can assign any clinic to any subscription plan and calculate duration automatically', function () {
    Role::firstOrCreate(['name' => 'superadmin']);

    $admin = User::factory()->create(['role' => 'superadmin']);
    $admin->assignRole('superadmin');

    $clinic = Clinic::create([
        'name' => 'عيادة الاختبار الطبية',
        'slug' => 'test-clinic',
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'الباقة السنوية',
        'price' => 299.99,
        'duration_in_days' => 365,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.subscriptions.assign'), [
        'clinic_id' => $clinic->id,
        'plan_id' => $plan->id,
        'is_active' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('subscriptions', [
        'clinic_id' => $clinic->id,
        'plan_id' => $plan->id,
        'plan' => 'الباقة السنوية',
        'is_active' => 1,
    ]);

    $subscription = Subscription::where('clinic_id', $clinic->id)->first();
    expect($subscription)->not->toBeNull();
    expect($subscription->starts_at->toDateString())->toBe(now()->toDateString());
    expect($subscription->ends_at->toDateString())->toBe(now()->addDays(365)->toDateString());
});

test('super admin can assign a free trial plan when plan_id is null', function () {
    Role::firstOrCreate(['name' => 'superadmin']);

    $admin = User::factory()->create(['role' => 'superadmin']);
    $admin->assignRole('superadmin');

    $clinic = Clinic::create([
        'name' => 'عيادة الاختبار الطبية',
        'slug' => 'test-clinic-2',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.subscriptions.assign'), [
        'clinic_id' => $clinic->id,
        'plan_id' => null,
        'is_active' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('subscriptions', [
        'clinic_id' => $clinic->id,
        'plan_id' => null,
        'plan' => 'free',
        'is_active' => 1,
    ]);

    $subscription = Subscription::where('clinic_id', $clinic->id)->first();
    expect($subscription->ends_at->toDateString())->toBe(now()->addDays(14)->toDateString());
});

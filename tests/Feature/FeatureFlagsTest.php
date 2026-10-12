<?php

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('ai chat route is blocked when feature flag disabled', function () {
    config(['clinora.features.ai_enabled' => false]);
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة AI',
        'slug' => 'ai-clinic',
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
        'phone' => '01044444444',
    ]);

    $this->actingAs($owner)->post(route('clinic.patients.ai-chat', $patient), [
        'message' => 'مرحبا',
    ])->assertNotFound();
});

test('whatsapp service does not send when feature flag disabled', function () {
    config(['clinora.features.whatsapp_enabled' => false]);
    SystemSetting::set('whatsapp_enabled', '1');

    Http::fake();

    $sent = app(WhatsAppService::class)->sendMessage('01000000000', 'test');

    expect($sent)->toBeFalse();
    Http::assertNothingSent();
});

test('registration works without whatsapp', function () {
    config(['clinora.features.whatsapp_enabled' => false]);

    Plan::create([
        'name' => 'free plan',
        'type' => 'single',
        'price' => 0,
        'duration_in_days' => 30,
        'max_clinics' => 1,
        'is_active' => true,
    ]);

    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $response = $this->post(route('register.post'), [
        'clinic_name' => 'عيادة جديدة',
        'name' => 'مالك',
        'email' => 'newowner@example.com',
        'phone' => '01055555555',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'newowner@example.com', 'role' => 'owner']);
});

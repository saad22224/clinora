<?php

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedOrganizationWithPlan(int $maxClinics = 2): array
{
    $organization = Organization::create([
        'name' => 'سلسلة الاختبار',
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'سلسلة صغيرة',
        'type' => 'chain',
        'price' => 100,
        'duration_in_days' => 365,
        'max_clinics' => $maxClinics,
        'is_active' => true,
    ]);

    Subscription::create([
        'organization_id' => $organization->id,
        'clinic_id' => null,
        'plan_id' => $plan->id,
        'plan' => $plan->name,
        'starts_at' => now(),
        'ends_at' => now()->addYear(),
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => null,
    ]);

    $organization->memberships()->create([
        'user_id' => $owner->id,
        'role' => 'owner',
        'permissions' => ['enter_clinic', 'manage_org_clinics', 'view_org_reports'],
    ]);

    return [$organization, $owner, $plan];
}

test('organization owner can create clinic within plan limit', function () {
    [$organization, $owner] = seedOrganizationWithPlan(2);

    $response = $this->actingAs($owner)->post(route('organization.clinics.store', $organization), [
        'clinic_name' => 'فرع أول',
        'phone' => '01000000000',
        'address' => 'المعادي',
    ]);

    $clinic = $organization->clinics()->first();

    $response->assertRedirect(route('organization.clinics.show', [$organization, $clinic]));
    expect($organization->clinics()->count())->toBe(1);
    expect(User::where('clinic_id', $clinic->id)->exists())->toBeFalse();
    expect($clinic->address)->toBe('المعادي');
    expect($clinic->phone)->toBe('01000000000');

    $service = app(SubscriptionService::class);
    $entitlement = $service->entitlementSummaryForClinic($clinic);

    expect($entitlement['inherited_from_organization'])->toBeTrue();
    expect($entitlement['is_active'])->toBeTrue();
    expect($entitlement['ends_at']?->toDateString())->toBe($organization->subscription->ends_at->toDateString());
    expect($service->hasActiveAccessForClinic($clinic))->toBeTrue();
    expect(Subscription::query()->where('clinic_id', $clinic->id)->exists())->toBeFalse();
});

test('organization owner lands on organization dashboard after login', function () {
    [$organization, $owner] = seedOrganizationWithPlan(2);

    Clinic::create([
        'organization_id' => $organization->id,
        'name' => 'فرع',
        'slug' => 'branch-login',
        'is_active' => true,
    ]);

    $this->post(route('login.post'), [
        'email' => $owner->email,
        'password' => 'password',
    ])->assertRedirect(route('organization.dashboard', $organization));
});

test('organization cannot create clinic beyond plan limit', function () {
    [$organization, $owner] = seedOrganizationWithPlan(1);

    Clinic::create([
        'organization_id' => $organization->id,
        'name' => 'فرع موجود',
        'slug' => 'existing-branch',
        'is_active' => true,
    ]);

    $this->actingAs($owner)->post(route('organization.clinics.store', $organization), [
        'clinic_name' => 'فرع زائد',
    ])->assertStatus(422);

    expect($organization->clinics()->count())->toBe(1);
});

test('user cannot enter clinic from unrelated organization', function () {
    [$organization, $owner] = seedOrganizationWithPlan(2);

    $otherOrg = Organization::create(['name' => 'منظمة أخرى', 'is_active' => true]);
    $otherClinic = Clinic::create([
        'organization_id' => $otherOrg->id,
        'name' => 'عيادة أخرى',
        'slug' => 'other-clinic',
        'is_active' => true,
    ]);

    $this->actingAs($owner)
        ->post(route('organization.clinics.enter', [$organization, $otherClinic]))
        ->assertForbidden();
});

test('organization owner can update and soft delete a clinic', function () {
    [$organization, $owner] = seedOrganizationWithPlan(2);

    $clinic = Clinic::create([
        'organization_id' => $organization->id,
        'name' => 'فرع قديم',
        'slug' => 'old-branch',
        'phone' => '01000000000',
        'is_active' => true,
    ]);

    $this->actingAs($owner)->get(route('organization.clinics.show', [$organization, $clinic]))
        ->assertOk()
        ->assertSee('فرع قديم')
        ->assertSee('دخول لوحة العيادة');

    $this->actingAs($owner)->put(route('organization.clinics.update', [$organization, $clinic]), [
        'clinic_name' => 'فرع محدّث',
        'phone' => '01111111111',
        'address' => 'المعادي',
        'is_active' => 1,
    ])->assertRedirect(route('organization.clinics.show', [$organization, $clinic]));

    $clinic->refresh();
    expect($clinic->name)->toBe('فرع محدّث');
    expect($clinic->phone)->toBe('01111111111');
    expect($clinic->address)->toBe('المعادي');

    $this->actingAs($owner)->delete(route('organization.clinics.destroy', [$organization, $clinic]))
        ->assertRedirect(route('organization.dashboard', $organization));

    expect(Clinic::query()->whereKey($clinic->id)->exists())->toBeFalse();
    expect(Clinic::withTrashed()->whereKey($clinic->id)->exists())->toBeTrue();
});

test('independent clinic owner still reaches clinic dashboard', function () {
    $clinic = Clinic::create([
        'name' => 'عيادة مستقلة',
        'slug' => 'independent',
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

    $this->actingAs($owner)->get(route('clinic.dashboard'))->assertOk();
});

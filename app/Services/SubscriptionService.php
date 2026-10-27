<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionService
{
    public function hasActiveSubscription(?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        $activeClinicId = session('active_clinic_id');
        if ($activeClinicId) {
            $clinic = Clinic::query()->find($activeClinicId);
            if ($clinic && $this->userCanAccessClinic($user, $clinic)) {
                return $this->hasActiveAccessForClinic($clinic);
            }
        }

        if ($user->clinic_id) {
            $clinic = $user->clinic;
            if ($clinic) {
                return $this->hasActiveAccessForClinic($clinic);
            }
        }

        $subscription = $this->resolveSubscriptionForUser($user);

        return $this->isSubscriptionActive($subscription);
    }

    public function hasActiveAccessForClinic(Clinic $clinic): bool
    {
        if (! $clinic->is_active) {
            return false;
        }

        if ($clinic->organization_id) {
            $clinic->loadMissing('organization');

            if (! $clinic->organization || ! $clinic->organization->is_active) {
                return false;
            }
        }

        return $this->isSubscriptionActive($this->resolveSubscriptionForClinic($clinic));
    }

    public function hasActiveAccessForOrganization(Organization $organization): bool
    {
        if (! $organization->is_active) {
            return false;
        }

        return $this->isSubscriptionActive($organization->subscription);
    }

    /**
     * Branch clinics inherit the organization chain plan (same plan + end date).
     * They do not get a separate clinic subscription row.
     */
    public function entitlementSummaryForClinic(Clinic $clinic): array
    {
        $subscription = $this->resolveSubscriptionForClinic($clinic);
        $subscription?->loadMissing('planDetails');

        return [
            'source' => $clinic->organization_id ? 'organization' : 'clinic',
            'plan_name' => $subscription?->planDetails?->name ?? $subscription?->plan,
            'plan_id' => $subscription?->plan_id,
            'starts_at' => $subscription?->starts_at,
            'ends_at' => $subscription?->ends_at,
            'is_active' => $this->isSubscriptionActive($subscription),
            'inherited_from_organization' => (bool) $clinic->organization_id,
        ];
    }

    public function resolveSubscriptionForUser(User $user): ?Subscription
    {
        $activeClinicId = session('active_clinic_id');
        if ($activeClinicId) {
            $clinic = Clinic::query()->find($activeClinicId);
            if ($clinic && $this->userCanAccessClinic($user, $clinic)) {
                return $this->resolveSubscriptionForClinic($clinic);
            }
        }

        if ($user->clinic_id) {
            $clinic = $user->clinic;
            if ($clinic) {
                return $this->resolveSubscriptionForClinic($clinic);
            }
        }

        $organization = $user->organizations()->with('subscription.planDetails')->first();

        return $organization?->subscription;
    }

    public function resolveSubscriptionForClinic(Clinic $clinic): ?Subscription
    {
        if ($clinic->organization_id) {
            $clinic->loadMissing('organization.subscription.planDetails');

            return $clinic->organization?->subscription;
        }

        $clinic->loadMissing('subscription.planDetails');

        return $clinic->subscription;
    }

    public function isSubscriptionActive(?Subscription $subscription): bool
    {
        if (! $subscription || ! $subscription->is_active) {
            return false;
        }

        if ($subscription->ends_at && $subscription->ends_at->endOfDay()->isPast()) {
            return false;
        }

        if ($subscription->plan_id && (! $subscription->planDetails || ! $subscription->planDetails->is_active)) {
            return false;
        }

        return true;
    }

    public function maxClinics(Organization $organization): int
    {
        $subscription = $organization->subscription;
        if (! $subscription || ! $this->isSubscriptionActive($subscription)) {
            return 0;
        }

        $subscription->loadMissing('planDetails');

        return (int) ($subscription->planDetails?->max_clinics ?? 1);
    }

    public function clinicUsage(Organization $organization): int
    {
        return $organization->clinics()->count();
    }

    public function remainingClinicSlots(Organization $organization): int
    {
        return max(0, $this->maxClinics($organization) - $this->clinicUsage($organization));
    }

    public function canCreateClinic(Organization $organization): bool
    {
        return $this->hasActiveAccessForOrganization($organization)
            && $this->remainingClinicSlots($organization) > 0;
    }

    public function userCanAccessClinic(User $user, Clinic $clinic): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if ($user->clinic_id && (int) $user->clinic_id === (int) $clinic->id) {
            return true;
        }

        if (! $clinic->organization_id) {
            return false;
        }

        $membership = $clinic->organization?->memberships()
            ->where('user_id', $user->id)
            ->first();

        return $membership !== null && $membership->canEnterClinic();
    }

    public function processExpirations(): int
    {
        $activeSubscriptions = Subscription::with('planDetails')->where('is_active', true)->get();
        $expiredCount = 0;

        foreach ($activeSubscriptions as $subscription) {
            if (! $this->isSubscriptionActive($subscription->fresh(['planDetails']))) {
                // isSubscriptionActive already false for expired; mark inactive when ends_at past or plan inactive
            }

            $isExpired = false;

            if ($subscription->ends_at && $subscription->ends_at->endOfDay()->isPast()) {
                $isExpired = true;
            }

            if ($subscription->plan_id && (! $subscription->planDetails || ! $subscription->planDetails->is_active)) {
                $isExpired = true;
            }

            if ($isExpired && $subscription->is_active) {
                $subscription->update(['is_active' => false]);
                $expiredCount++;
            }
        }

        return $expiredCount;
    }
}

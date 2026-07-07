<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionService
{
    public function hasActiveSubscription(): bool
    {
        if (! Auth::hasUser() || ! Auth::user()->clinic_id) {
            return false;
        }

        $subscription = Auth::user()->clinic->subscription;

        if (! $subscription || ! $subscription->is_active) {
            return false;
        }

        if ($subscription->ends_at && $subscription->ends_at->endOfDay()->isPast()) {
            return false;
        }

        if ($subscription->plan_id && (! $subscription->plan || ! $subscription->plan->is_active)) {
            return false;
        }

        return true;
    }

    public function processExpirations(): int
    {
        $activeSubscriptions = Subscription::with('plan')->where('is_active', true)->get();
        $expiredCount = 0;

        foreach ($activeSubscriptions as $subscription) {
            $isExpired = false;

            if ($subscription->ends_at && $subscription->ends_at->endOfDay()->isPast()) {
                $isExpired = true;
            }

            if ($subscription->plan_id && (! $subscription->plan || ! $subscription->plan->is_active)) {
                $isExpired = true;
            }

            if ($isExpired) {
                $subscription->update(['is_active' => false]);
                $expiredCount++;
            }
        }

        return $expiredCount;
    }
}

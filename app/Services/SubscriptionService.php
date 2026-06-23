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

        if (! $subscription) {
            return false;
        }

        return $subscription->is_active && ($subscription->ends_at === null || $subscription->ends_at->isFuture());
    }

    public function processExpirations(): void
    {
        Subscription::where('ends_at', '<', now())
            ->where('is_active', true)
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['is_active' => false]);
                    // Dispatch SubscriptionExpired event here
                }
            });
    }
}

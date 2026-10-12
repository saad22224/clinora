<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $hasClinicContext = session('active_clinic_id') || $user->clinic_id;

        // Org branch clinics inherit the organization chain plan end date.
        if ($hasClinicContext && ! $this->subscriptionService->hasActiveSubscription($user)) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}

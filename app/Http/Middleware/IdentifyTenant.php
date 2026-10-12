<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
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
        $clinic = null;

        $activeClinicId = session('active_clinic_id');
        if ($activeClinicId) {
            $candidate = Clinic::query()->find($activeClinicId);
            if ($candidate && $this->subscriptionService->userCanAccessClinic($user, $candidate)) {
                $clinic = $candidate;
            } else {
                session()->forget('active_clinic_id');
            }
        }

        if (! $clinic && $user->clinic_id) {
            $clinic = $user->clinic;
        }

        if ($clinic) {
            app()->instance('tenant', $clinic);
        }

        return $next($request);
    }
}

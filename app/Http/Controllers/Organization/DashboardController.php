<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index(Request $request, Organization $organization)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($this->subscriptionService->hasActiveAccessForOrganization($organization), 403, 'اشتراك المنظمة غير نشط.');

        $organization->load(['subscription.planDetails', 'clinics']);

        $maxClinics = $this->subscriptionService->maxClinics($organization);
        $usedClinics = $this->subscriptionService->clinicUsage($organization);
        $remaining = $this->subscriptionService->remainingClinicSlots($organization);

        $clinics = $organization->clinics()
            ->withCount(['patients', 'appointments'])
            ->latest()
            ->get();

        return view('organization.dashboard', [
            'organization' => $organization,
            'membership' => $membership,
            'subscription' => $organization->subscription,
            'plan' => $organization->subscription?->planDetails,
            'maxClinics' => $maxClinics,
            'usedClinics' => $usedClinics,
            'remaining' => $remaining,
            'canCreateClinic' => $membership?->canManageClinics() && $this->subscriptionService->canCreateClinic($organization),
            'clinics' => $clinics,
        ]);
    }
}

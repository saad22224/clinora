<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Clinic;
use App\Models\Organization;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class ClinicSwitchController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function enter(Request $request, Organization $organization, Clinic $clinic)
    {
        $organization = $request->attributes->get('organization', $organization);
        $membership = $request->attributes->get('organization_membership');

        abort_unless($membership?->canEnterClinic(), 403, 'ليس لديك صلاحية دخول العيادة.');
        abort_if((int) $clinic->organization_id !== (int) $organization->id, 403);
        abort_unless($this->subscriptionService->hasActiveAccessForOrganization($organization), 403);

        session([
            'active_clinic_id' => $clinic->id,
            'active_organization_id' => $organization->id,
        ]);

        AuditLog::record('organization.clinic_enter', $clinic, [
            'organization_id' => $organization->id,
        ]);

        return redirect()->route('clinic.dashboard')
            ->with('success', 'تم الدخول إلى عيادة: '.$clinic->name);
    }

    public function leave(Request $request, Organization $organization)
    {
        $organization = $request->attributes->get('organization', $organization);

        session()->forget(['active_clinic_id']);

        AuditLog::record('organization.clinic_leave', $organization);

        return redirect()->route('organization.dashboard', $organization)
            ->with('success', 'تمت العودة إلى لوحة المنظمة.');
    }
}

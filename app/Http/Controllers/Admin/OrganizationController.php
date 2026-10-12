<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrganizationController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $organizations = Organization::query()
            ->with(['subscription.planDetails'])
            ->withCount('clinics')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $chainPlans = Plan::query()->where('type', 'chain')->where('is_active', true)->get();

        return view('admin.organizations', compact('organizations', 'chainPlans', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::query()->findOrFail($validated['plan_id']);
        abort_unless($plan->type === 'chain', 422, 'يجب اختيار باقة سلاسل عيادات.');

        $organization = DB::transaction(function () use ($validated, $plan) {
            $organization = Organization::create([
                'name' => $validated['name'],
                'is_active' => true,
            ]);

            $owner = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'role' => 'owner',
                'clinic_id' => null,
            ]);

            $organization->memberships()->create([
                'user_id' => $owner->id,
                'role' => 'owner',
                'permissions' => ['enter_clinic', 'manage_org_clinics', 'view_org_reports'],
            ]);

            Subscription::updateOrCreate(
                ['organization_id' => $organization->id],
                [
                    'clinic_id' => null,
                    'plan_id' => $plan->id,
                    'plan' => $plan->name,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_in_days),
                    'is_active' => true,
                ]
            );

            return $organization;
        });

        AuditLog::record('admin.organization_created', $organization);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', 'تم إنشاء سلسلة العيادات وتعيين الباقة.');
    }

    public function show(Organization $organization)
    {
        $organization->load(['subscription.planDetails', 'clinics', 'memberships.user']);
        $chainPlans = Plan::query()->where('type', 'chain')->where('is_active', true)->get();

        return view('admin.organization-show', [
            'organization' => $organization,
            'chainPlans' => $chainPlans,
            'maxClinics' => $this->subscriptionService->maxClinics($organization),
            'usedClinics' => $this->subscriptionService->clinicUsage($organization),
            'remaining' => $this->subscriptionService->remainingClinicSlots($organization),
        ]);
    }

    public function toggle(Organization $organization)
    {
        $organization->update(['is_active' => ! $organization->is_active]);

        if ($organization->subscription) {
            $organization->subscription->update(['is_active' => $organization->is_active]);
        }

        AuditLog::record('admin.organization_toggled', $organization, [
            'is_active' => $organization->is_active,
        ]);

        return back()->with('success', $organization->is_active ? 'تم تفعيل المنظمة.' : 'تم تعليق المنظمة.');
    }

    public function assignPlan(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan = Plan::query()->findOrFail($validated['plan_id']);
        abort_unless($plan->type === 'chain', 422, 'يجب اختيار باقة سلاسل عيادات.');

        Subscription::updateOrCreate(
            ['organization_id' => $organization->id],
            [
                'clinic_id' => null,
                'plan_id' => $plan->id,
                'plan' => $plan->name,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_in_days),
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]
        );

        AuditLog::record('admin.organization_plan_assigned', $organization, [
            'plan_id' => $plan->id,
        ]);

        return back()->with('success', 'تم تحديث باقة المنظمة.');
    }
}

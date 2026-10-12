<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::query()->latest()->get();
        $clinics = Clinic::with('subscription')->whereNull('organization_id')->get();
        $organizations = Organization::with('subscription.planDetails')->withCount('clinics')->get();
        $subscriptions = Subscription::with(['clinic', 'organization', 'planDetails', 'clinic.users', 'clinic.marketer'])
            ->latest()
            ->paginate(15);

        return view('admin.subscriptions', compact('plans', 'clinics', 'organizations', 'subscriptions'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:clinic,organization'],
            'clinic_id' => ['nullable', 'required_if:target_type,clinic', 'exists:clinics,id'],
            'organization_id' => ['nullable', 'required_if:target_type,organization', 'exists:organizations,id'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan = ! empty($validated['plan_id']) ? Plan::find($validated['plan_id']) : null;

        if ($validated['target_type'] === 'organization') {
            abort_if($plan && $plan->type !== 'chain', 422, 'باقات السلاسل فقط للمنظمات.');

            $subscription = Subscription::updateOrCreate(
                ['organization_id' => $validated['organization_id']],
                [
                    'clinic_id' => null,
                    'plan_id' => $plan?->id,
                    'plan' => $plan?->name ?? 'free',
                    'starts_at' => now(),
                    'ends_at' => $plan ? now()->addDays($plan->duration_in_days) : now()->addDays(14),
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
                ]
            );

            AuditLog::record('admin.subscription_assigned', $subscription, [
                'organization_id' => $validated['organization_id'],
            ]);
        } else {
            abort_if($plan && $plan->type === 'chain', 422, 'لا يمكن تعيين باقة سلسلة لعيادة مستقلة.');

            $clinic = Clinic::findOrFail($validated['clinic_id']);
            abort_if($clinic->organization_id, 422, 'عيادات السلسلة تتبع اشتراك المنظمة.');

            $subscription = Subscription::updateOrCreate(
                ['clinic_id' => $validated['clinic_id']],
                [
                    'organization_id' => null,
                    'plan_id' => $plan?->id,
                    'plan' => $plan?->name ?? 'free',
                    'starts_at' => now(),
                    'ends_at' => $plan ? now()->addDays($plan->duration_in_days) : now()->addDays(14),
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
                ]
            );

            AuditLog::record('admin.subscription_assigned', $subscription, [
                'clinic_id' => $validated['clinic_id'],
            ]);
        }

        return back()->with('success', 'تم تسكين وتحديث الاشتراك بنجاح!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $clinics = Clinic::with('subscription')->get();
        $subscriptions = Subscription::with(['clinic', 'clinic.users', 'clinic.marketer'])
            ->latest()
            ->paginate(15);

        return view('admin.subscriptions', compact('plans', 'clinics', 'subscriptions'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'clinic_id' => ['required', 'exists:clinics,id'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan = ! empty($validated['plan_id']) ? Plan::find($validated['plan_id']) : null;

        $startsAt = now();
        $endsAt = $plan ? now()->addDays($plan->duration_in_days) : now()->addDays(14);

        Subscription::updateOrCreate(
            ['clinic_id' => $validated['clinic_id']],
            [
                'plan_id' => $plan?->id,
                'plan' => $plan?->name ?? 'free',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]
        );

        return back()->with('success', 'تم تسكين وتحديث اشتراك العيادة بنجاح!');
    }
}

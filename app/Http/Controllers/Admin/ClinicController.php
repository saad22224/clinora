<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Marketer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::with(['subscription', 'users', 'marketer'])
            ->latest()
            ->paginate(15);

        $plans = Plan::where('is_active', true)->get();
        $marketers = Marketer::where('is_active', true)->get();

        return view('admin.clinics', compact('clinics', 'plans', 'marketers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'marketer_id' => ['nullable', 'exists:marketers,id'],
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['clinic_name']);
        $originalSlug = $slug;
        $count = 1;
        while (Clinic::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $clinic = Clinic::create([
            'name' => $validated['clinic_name'],
            'slug' => $slug,
            'marketer_id' => $validated['marketer_id'] ?? null,
            'is_active' => true,
        ]);

        // Determine subscription plan
        $plan = $validated['plan_id'] ? Plan::find($validated['plan_id']) : null;
        Subscription::create([
            'clinic_id' => $clinic->id,
            'plan' => $plan?->name ?? 'free',
            'plan_id' => $plan?->id,
            'starts_at' => now(),
            'ends_at' => $plan ? now()->addDays($plan->duration_in_days) : now()->addDays(14),
            'is_active' => true,
        ]);

        // Create owner user
        User::create([
            'name' => $validated['owner_name'],
            'email' => $validated['owner_email'],
            'password' => Hash::make($validated['owner_password']),
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        return redirect()->route('admin.clinics.index')
            ->with('success', 'تم إنشاء العيادة بنجاح!');
    }

    public function toggle(Clinic $clinic)
    {
        $clinic->update(['is_active' => ! $clinic->is_active]);

        $status = $clinic->is_active ? 'تفعيل' : 'تعطيل';

        return back()->with('success', "تم {$status} العيادة بنجاح.");
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();

        return redirect()->route('admin.clinics.index')
            ->with('success', 'تم حذف العيادة بنجاح.');
    }

    public function impersonate(Clinic $clinic)
    {
        $user = $clinic->users()->first();
        if (! $user) {
            return back()->withErrors(['error' => 'لا يوجد مستخدم مرتبط بهذه العيادة.']);
        }

        session(['superadmin_impersonator_id' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('clinic.dashboard')->with('success', 'تم الدخول إلى لوحة تحكم عيادة: '.$clinic->name);
    }

    public function leaveImpersonation()
    {
        $adminId = session('superadmin_impersonator_id');
        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin && $admin->role == 'superadmin') {
                auth()->login($admin);
                session()->forget('superadmin_impersonator_id');

                return redirect()->route('admin.clinics.index')->with('success', 'تم العودة إلى لوحة السوبر أدمن بنجاح.');
            }
        }

        return redirect()->route('login');
    }
}

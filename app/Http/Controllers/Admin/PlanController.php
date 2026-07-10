<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_in_days' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
        ]);

        // Parse features JSON safely
        $features = null;
        if (! empty($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            $features = json_last_error() == JSON_ERROR_NONE ? $decoded : null;
        }

        Plan::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_in_days' => $validated['duration_in_days'],
            'features' => $features,
            'is_active' => true,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تمت إضافة الباقة بنجاح!');
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_in_days' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
        ]);

        $features = null;
        if (! empty($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            $features = json_last_error() == JSON_ERROR_NONE ? $decoded : null;
        }

        $plan->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_in_days' => $validated['duration_in_days'],
            'features' => $features,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث الباقة بنجاح!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم حذف الباقة بنجاح.');
    }
}

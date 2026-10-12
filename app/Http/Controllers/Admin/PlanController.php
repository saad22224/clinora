<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['single', 'chain'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_in_days' => ['required', 'integer', 'min:1'],
            'max_clinics' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $features = null;
        if (! empty($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            $features = json_last_error() == JSON_ERROR_NONE ? $decoded : null;
        }

        $maxClinics = $validated['type'] === 'single' ? 1 : (int) $validated['max_clinics'];

        Plan::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_in_days' => $validated['duration_in_days'],
            'max_clinics' => $maxClinics,
            'features' => $features,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تمت إضافة الباقة بنجاح!');
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['single', 'chain'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_in_days' => ['required', 'integer', 'min:1'],
            'max_clinics' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $features = null;
        if (! empty($validated['features'])) {
            $decoded = json_decode($validated['features'], true);
            $features = json_last_error() == JSON_ERROR_NONE ? $decoded : null;
        }

        $maxClinics = $validated['type'] === 'single' ? 1 : (int) $validated['max_clinics'];

        $plan->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_in_days' => $validated['duration_in_days'],
            'max_clinics' => $maxClinics,
            'features' => $features,
            'is_active' => $request->boolean('is_active', $plan->is_active),
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

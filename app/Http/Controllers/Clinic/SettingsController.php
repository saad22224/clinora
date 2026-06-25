<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $clinic = Auth::user()->clinic;

        return view('clinic.settings', compact('clinic'));
    }

    public function update(Request $request)
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'doctor_title' => ['nullable', 'string', 'max:255'],
            'doctor_bio' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'theme_color' => ['nullable', 'string', 'max:7'],
            'doctor_image' => ['nullable', 'image', 'max:2048'],
            'working_hours' => ['nullable', 'array'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'about_us' => ['nullable', 'string'],
            'social_links' => ['nullable', 'array'],
        ]);

        // Handle image upload
        if ($request->hasFile('doctor_image')) {
            if ($clinic->doctor_image) {
                Storage::disk('public')->delete($clinic->doctor_image);
            }
            $validated['doctor_image'] = $request->file('doctor_image')->store('clinic-images', 'public');
        }

        // Format working hours
        if (isset($validated['working_hours']) && is_array($validated['working_hours'])) {
            $formattedHours = [];
            foreach ($validated['working_hours'] as $day => $data) {
                $enabled = ! empty($data['enabled']);
                $formattedHours[$day] = [
                    'enabled' => $enabled,
                    'periods' => [],
                ];
                if ($enabled && isset($data['periods']) && is_array($data['periods'])) {
                    // Filter out invalid periods
                    foreach ($data['periods'] as $period) {
                        if (! empty($period['open']) && ! empty($period['close'])) {
                            $formattedHours[$day]['periods'][] = [
                                'open' => $period['open'],
                                'close' => $period['close'],
                            ];
                        }
                    }
                }
            }
            $validated['working_hours'] = $formattedHours;
        }

        // Remove the file from validated so we don't overwrite with null
        if (! $request->hasFile('doctor_image')) {
            unset($validated['doctor_image']);
        }

        $clinic->update($validated);

        return redirect()->route('clinic.settings.index')
            ->with('success', 'تم حفظ إعدادات العيادة بنجاح.');
    }
}

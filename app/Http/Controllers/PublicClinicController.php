<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Clinic;
use Illuminate\Http\Request;

class PublicClinicController extends Controller
{
    public function show(string $slug)
    {
        $clinic = Clinic::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('pages.clinic-landing', compact('clinic'));
    }

    public function book(Request $request, string $slug)
    {
        $clinic = Clinic::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:30'],
            'patient_email' => ['nullable', 'email', 'max:255'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        BookingRequest::create([
            ...$validated,
            'clinic_id' => $clinic->id,
            'status' => 'new',
        ]);

        return back()->with('booking_success', 'تم إرسال طلب الحجز بنجاح! سيتصل بك الفريق قريباً.');
    }
}

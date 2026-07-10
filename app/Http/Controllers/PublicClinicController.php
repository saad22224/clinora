<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\VisitType;
use App\Services\WhatsAppService;
use Carbon\Carbon;
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
            'visit_type_id' => ['required', 'exists:visit_types,id'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitType = VisitType::findOrFail($validated['visit_type_id']);
        abort_if($visitType->clinic_id != $clinic->id, 403);

        $patient = Patient::firstOrCreate(
            [
                'clinic_id' => $clinic->id,
                'phone' => $validated['patient_phone'],
            ],
            [
                'name' => $validated['patient_name'],
                'notes' => 'تم إنشاء المريض من طلب حجز صفحة الهبوط.',
            ],
        );

        if ($patient->wasRecentlyCreated == false && blank($patient->name)) {
            $patient->update(['name' => $validated['patient_name']]);
        }

        $appointmentNotes = collect([
            $validated['notes'] ?? null,
            ! empty($validated['patient_email']) ? 'البريد الإلكتروني للمريض: '.$validated['patient_email'] : null,
        ])->filter()->implode(PHP_EOL);

        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $clinic->users()->value('id'),
            'visit_type_id' => $visitType?->id,
            'price' => $visitType?->price,
            'scheduled_at' => Carbon::parse($validated['preferred_date'])->startOfDay(),
            'status' => 'pending',
            'source' => 'website_landing_page',
            'notes' => $appointmentNotes ?: null,
        ]);

        // Send WhatsApp confirmation to patient
        $dateStr = Carbon::parse($validated['preferred_date'])->translatedFormat('l، d F Y');
        app(WhatsAppService::class)->sendMessage(
            $validated['patient_phone'],
            "مرحباً بك في *{$clinic->name}* 🏥\n\nلقد تم استلام طلب حجزك المبدئي بنجاح:\n• الخدمة: {$visitType->name}\n• التاريخ المقترح: {$dateStr}\n\nسيقوم فريق الاستقبال بمراجعة الموعد وتأكيده معك قريباً. نتمنى لك دوام الصحة والعافية."
        );

        return back()->with('booking_success', 'تم إرسال طلب الموعد بنجاح، وسيقوم فريق العيادة بتأكيده قريباً.');
    }
}

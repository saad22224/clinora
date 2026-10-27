<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Scopes\TenantScope;
use App\Models\VisitType;
use App\Services\AppointmentQueueService;
use App\Services\AppointmentTrackingService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicClinicController extends Controller
{
    public function __construct(
        private AppointmentQueueService $queue,
        private AppointmentTrackingService $tracking,
    ) {}

    public function show(string $slug)
    {
        $clinic = Clinic::where('slug', $slug)
            ->where('is_active', true)
            ->with('visitTypes')
            ->firstOrFail();

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
        abort_if((int) $visitType->clinic_id !== (int) $clinic->id, 403);

        $appointment = DB::transaction(function () use ($clinic, $validated, $visitType) {
            $patient = Patient::withoutGlobalScope(TenantScope::class)->firstOrCreate(
                [
                    'clinic_id' => $clinic->id,
                    'phone' => $validated['patient_phone'],
                ],
                [
                    'name' => $validated['patient_name'],
                    'notes' => 'تم إنشاء المريض من طلب حجز صفحة الهبوط.',
                ],
            );

            if ($patient->wasRecentlyCreated === false && blank($patient->name)) {
                $patient->update(['name' => $validated['patient_name']]);
            }

            $appointmentNotes = collect([
                $validated['notes'] ?? null,
                ! empty($validated['patient_email']) ? 'البريد الإلكتروني للمريض: '.$validated['patient_email'] : null,
            ])->filter()->implode(PHP_EOL);

            $doctorId = $this->queue->resolveDoctorId($clinic);

            $created = Appointment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'visit_type_id' => $visitType->id,
                'price' => $visitType->price,
                'scheduled_at' => Carbon::parse($validated['preferred_date'])->startOfDay(),
                'status' => 'pending',
                'source' => 'website_landing_page',
                'notes' => $appointmentNotes ?: null,
            ]);

            return $this->queue->assignOnCreate($created);
        });

        $confirmUrl = route('clinic.public.booking.confirm', [
            'slug' => $clinic->slug,
            'token' => $appointment->tracking_token,
        ]);

        $dateStr = Carbon::parse($validated['preferred_date'])->timezone($this->queue->timezone())->translatedFormat('l، d F Y');
        $queueLabel = $appointment->queue_number ? "• رقم الدور: *{$appointment->queue_number}*\n" : '';

        app(WhatsAppService::class)->sendMessage(
            $validated['patient_phone'],
            "مرحباً بك في *{$clinic->name}* 🏥\n\nلقد تم استلام طلب حجزك بنجاح:\n• الخدمة: {$visitType->name}\n• التاريخ: {$dateStr}\n{$queueLabel}\nرابط متابعة الدور:\n{$confirmUrl}\n\nسيقوم فريق الاستقبال بمراجعة الموعد عند الحاجة. نتمنى لك دوام الصحة والعافية."
        );

        return redirect()->route('clinic.public.booking.confirm', [
            'slug' => $clinic->slug,
            'token' => $appointment->tracking_token,
        ]);
    }

    public function bookingConfirm(string $slug, string $token)
    {
        $clinic = Clinic::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $appointment = $this->tracking->findByToken($token);
        abort_unless($appointment && (int) $appointment->clinic_id === (int) $clinic->id, 404);

        $payload = $this->tracking->publicPayload($appointment);
        $trackingUrl = route('clinic.public.booking.track', [
            'slug' => $clinic->slug,
            'token' => $token,
        ]);

        return view('pages.booking-confirm', [
            'clinic' => $clinic,
            'payload' => $payload,
            'trackingUrl' => $trackingUrl,
            'confirmUrl' => url()->current(),
        ]);
    }

    public function bookingTrack(string $slug, string $token)
    {
        $clinic = Clinic::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $appointment = $this->tracking->findByToken($token);
        abort_unless($appointment && (int) $appointment->clinic_id === (int) $clinic->id, 404);

        $payload = $this->tracking->publicPayload($appointment);
        $pollUrl = route('clinic.public.booking.status', [
            'slug' => $clinic->slug,
            'token' => $token,
        ]);

        return view('pages.booking-track', [
            'clinic' => $clinic,
            'payload' => $payload,
            'pollUrl' => $pollUrl,
            'confirmUrl' => route('clinic.public.booking.confirm', ['slug' => $slug, 'token' => $token]),
        ]);
    }

    public function bookingStatus(string $slug, string $token)
    {
        $clinic = Clinic::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $appointment = $this->tracking->findByToken($token);
        abort_unless($appointment && (int) $appointment->clinic_id === (int) $clinic->id, 404);

        return response()->json($this->tracking->publicPayload($appointment));
    }
}

<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\VisitType;
use App\Services\AppointmentQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientAppointmentController extends Controller
{
    public function __construct(
        private AppointmentQueueService $queue,
    ) {}

    public function store(Request $request, Patient $patient)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'visit_type_id' => ['required', 'exists:visit_types,id'],
            'follow_up_of_visit_id' => ['nullable', 'exists:visits,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'period' => ['nullable', 'in:custom,1w,2w,1m'],
        ]);

        $visitType = VisitType::findOrFail($validated['visit_type_id']);
        abort_if((int) $visitType->clinic_id !== $this->currentClinicId(), 403);

        $followUpVisitId = null;
        if (filled($validated['follow_up_of_visit_id'] ?? null)) {
            $visit = Visit::findOrFail($validated['follow_up_of_visit_id']);
            abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
            abort_if((int) $visit->patient_id !== (int) $patient->id, 403);
            $followUpVisitId = $visit->id;
        }

        $scheduledAt = Carbon::parse($validated['scheduled_date'].' '.$validated['scheduled_time']);
        $clinic = $this->currentClinic();
        $doctorId = $this->queue->resolveDoctorId(
            $clinic,
            Auth::user()?->role === 'doctor' ? Auth::id() : null
        );

        DB::transaction(function () use ($validated, $visitType, $doctorId, $scheduledAt, $patient, $followUpVisitId) {
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'visit_type_id' => $visitType->id,
                'follow_up_of_visit_id' => $followUpVisitId,
                'notes' => $validated['notes'] ?? ($followUpVisitId ? 'موعد مراجعة' : null),
                'scheduled_at' => $scheduledAt,
                'price' => $visitType->price,
                'clinic_id' => $this->currentClinicId(),
                'doctor_id' => $doctorId,
                'status' => 'confirmed',
                'source' => 'reception_manual_entry',
            ]);

            $this->queue->assignOnCreate($appointment);
        });

        return back()->with('success', $followUpVisitId ? 'تم حجز موعد المراجعة.' : 'تم حجز الموعد من ملف المريض.');
    }
}

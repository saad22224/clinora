<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\VisitType;
use App\Services\AppointmentQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentQueueService $queue,
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $clinicId = $this->currentClinicId();
        $clinic = $this->currentClinic();

        $selectedDate = isset($validated['date'])
            ? $this->queue->queueDateFor($validated['date'])
            : $this->queue->today();

        $doctors = User::query()
            ->where('clinic_id', $clinicId)
            ->whereIn('role', ['doctor', 'owner'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $doctorId = $this->queue->resolveDoctorId(
            $clinic,
            isset($validated['doctor_id'])
                ? (int) $validated['doctor_id']
                : (Auth::user()?->role === 'doctor' ? Auth::id() : null)
        );

        $queueSummary = $this->queue->daySummary($clinicId, $doctorId, $selectedDate);
        $dayQueue = $this->queue->dayQueue($clinicId, $doctorId, $selectedDate);

        $patients = Patient::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $visitTypes = VisitType::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        $prevDate = $selectedDate->copy()->subDay()->toDateString();
        $nextDate = $selectedDate->copy()->addDay()->toDateString();

        return view('clinic.appointments', compact(
            'patients',
            'visitTypes',
            'queueSummary',
            'dayQueue',
            'selectedDate',
            'prevDate',
            'nextDate',
            'doctors',
            'doctorId',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'scheduled_at' => ['required', 'date', 'after_or_equal:today'],
            'visit_type_id' => ['required', 'exists:visit_types,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitType = VisitType::findOrFail($validated['visit_type_id']);
        abort_if((int) $visitType->clinic_id !== $this->currentClinicId(), 403);

        $patient = Patient::findOrFail($validated['patient_id']);
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);

        $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at'])->startOfDay();

        $clinic = $this->currentClinic();
        $doctorId = $this->queue->resolveDoctorId(
            $clinic,
            Auth::user()?->role === 'doctor' ? Auth::id() : null
        );

        DB::transaction(function () use ($validated, $visitType, $doctorId) {
            $appointment = Appointment::create([
                ...$validated,
                'price' => $visitType->price,
                'clinic_id' => $this->currentClinicId(),
                'doctor_id' => $doctorId,
                'status' => 'confirmed',
                'source' => 'reception_manual_entry',
            ]);

            $this->queue->assignOnCreate($appointment);
        });

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تمت إضافة الموعد بنجاح!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        abort_if((int) $appointment->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,rejected,cancelled,completed'],
            'scheduled_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if (filled($validated['scheduled_at'] ?? null)) {
            $this->queue->reassignForReschedule($appointment, $validated['scheduled_at']);
            $appointment->refresh();
            $appointment->update(['status' => 'pending']);
        } else {
            $appointment->update(['status' => $validated['status']]);

            if ($validated['status'] === 'completed' && $appointment->queue_status !== AppointmentQueueService::STATUS_COMPLETED) {
                try {
                    $this->queue->complete($appointment);
                } catch (Throwable) {
                    $appointment->update(['queue_status' => AppointmentQueueService::STATUS_COMPLETED]);
                }
            }

            if (in_array($validated['status'], ['cancelled', 'rejected'], true)) {
                return back()->with('success', 'تم إلغاء الموعد بنجاح.');
            }
        }

        return back()->with('success', 'تم تحديث حالة الموعد بنجاح.');
    }

    public function queueAction(Request $request, Appointment $appointment)
    {
        abort_if((int) $appointment->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'action' => ['required', 'in:call,in_progress,complete,skip'],
        ]);

        try {
            $updated = match ($validated['action']) {
                'call' => $this->queue->call($appointment),
                'in_progress' => $this->queue->markInProgress($appointment),
                'complete' => $this->queue->complete($appointment),
                'skip' => $this->queue->skip($appointment),
            };
        } catch (InvalidArgumentException|RuntimeException $e) {
            return back()->withErrors(['queue' => $e->getMessage()]);
        }

        $wantsJson = $request->wantsJson() || $request->ajax();

        if ($wantsJson) {
            return response()->json([
                'ok' => true,
                'appointment' => [
                    'id' => $updated->id,
                    'queue_number' => $updated->queue_number,
                    'queue_status' => $updated->queue_status,
                    'patient_name' => $updated->patient?->name,
                ],
            ]);
        }

        $messages = [
            'call' => 'تمت مناداة المريض.',
            'in_progress' => 'تم بدء الكشف.',
            'complete' => 'تم إكمال الموعد.',
            'skip' => 'تم تخطّي المريض في الطابور.',
        ];

        return back()->with('success', $messages[$validated['action']] ?? 'تم تحديث الدور بنجاح.');
    }

    public function callNext(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'date' => ['nullable', 'date'],
        ]);

        $clinic = $this->currentClinic();
        $doctorId = $this->queue->resolveDoctorId(
            $clinic,
            isset($validated['doctor_id']) ? (int) $validated['doctor_id'] : Auth::id()
        );

        try {
            $next = $this->queue->callNext(
                $clinic,
                $doctorId,
                isset($validated['date']) ? Carbon::parse($validated['date']) : null,
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['queue' => $e->getMessage()]);
        }

        if (! $next) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'appointment' => null,
                    'message' => 'لا يوجد مرضى في الانتظار.',
                ]);
            }

            return back()->with('success', 'لا يوجد مرضى في الانتظار.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'appointment' => [
                    'id' => $next->id,
                    'queue_number' => $next->queue_number,
                    'queue_status' => $next->queue_status,
                    'patient_name' => $next->patient?->name,
                ],
            ]);
        }

        return back()->with('success', "تمت مناداة رقم الدور {$next->queue_number} — {$next->patient?->name}");
    }

    public function pauseQueue(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'date' => ['nullable', 'date'],
            'paused' => ['required', 'boolean'],
        ]);

        $clinic = $this->currentClinic();
        $doctorId = $this->queue->resolveDoctorId(
            $clinic,
            isset($validated['doctor_id']) ? (int) $validated['doctor_id'] : Auth::id()
        );

        $this->queue->setPaused(
            $clinic,
            $doctorId,
            (bool) $validated['paused'],
            isset($validated['date']) ? Carbon::parse($validated['date']) : null,
        );

        return back()->with('success', $validated['paused'] ? 'تم إيقاف المناداة مؤقتًا.' : 'تم استئناف المناداة.');
    }

    public function destroy(Appointment $appointment)
    {
        abort_if((int) $appointment->clinic_id !== $this->currentClinicId(), 403);
        $appointment->delete();

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تم حذف الموعد بنجاح.');
    }
}

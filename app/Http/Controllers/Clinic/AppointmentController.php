<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\VisitType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'source' => ['nullable', 'in:website_landing_page,reception_manual_entry'],
            'status' => ['nullable', 'in:pending,confirmed,completed,rejected,cancelled'],
            'visit_type' => ['nullable', 'integer', 'exists:visit_types,id'],
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $clinicId = $this->currentClinicId();

        $query = Appointment::with(['patient', 'visitType'])
            ->where('clinic_id', $clinicId)
            ->when($validated['source'] ?? null, fn ($q, $v) => $q->where('source', $v))
            ->when($validated['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($validated['visit_type'] ?? null, fn ($q, $v) => $q->where('visit_type_id', $v))
            ->when($validated['date_from'] ?? null, fn ($q, $v) => $q->whereDate('scheduled_at', '>=', $v))
            ->when($validated['date_to'] ?? null, fn ($q, $v) => $q->whereDate('scheduled_at', '<=', $v))
            ->when($validated['search'] ?? null, function ($q, $search) {
                $q->whereHas('patient', fn ($pq) => $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            });

        // Summary counts (unfiltered by page, but respecting same clinic)
        $summaryBase = Appointment::where('clinic_id', $clinicId);
        $summary = [
            'total' => (clone $summaryBase)->count(),
            'pending' => (clone $summaryBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $summaryBase)->where('status', 'confirmed')->count(),
            'completed' => (clone $summaryBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $summaryBase)->whereIn('status', ['cancelled', 'rejected'])->count(),
        ];

        $appointments = $query->latest('scheduled_at')->paginate(20)->withQueryString();

        $patients = Patient::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $visitTypes = VisitType::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('clinic.appointments', compact('appointments', 'patients', 'visitTypes', 'summary'));
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
        abort_if($visitType->clinic_id != $this->currentClinicId(), 403);

        $patient = Patient::findOrFail($validated['patient_id']);
        abort_if($patient->clinic_id != $this->currentClinicId(), 403);

        // Normalize date-only to start of day
        $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at'])->startOfDay();

        Appointment::create([
            ...$validated,
            'price' => $visitType->price,
            'clinic_id' => $this->currentClinicId(),
            'doctor_id' => Auth::id(),
            'status' => 'confirmed',
            'source' => 'reception_manual_entry',
        ]);

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تمت إضافة الموعد بنجاح!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        abort_if($appointment->clinic_id != $this->currentClinicId(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,rejected,cancelled,completed'],
            'scheduled_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if (filled($validated['scheduled_at'] ?? null)) {
            $validated['status'] = 'pending';
            $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at'])->startOfDay();
        } else {
            unset($validated['scheduled_at']);
        }

        $appointment->update($validated);

        return back()->with('success', 'تم تحديث حالة الموعد بنجاح.');
    }

    public function destroy(Appointment $appointment)
    {
        abort_if($appointment->clinic_id != $this->currentClinicId(), 403);
        $appointment->delete();

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تم حذف الموعد بنجاح.');
    }
}

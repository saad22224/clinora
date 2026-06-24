<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('patient')
            ->where('clinic_id', Auth::user()->clinic_id)
            ->latest('scheduled_at')
            ->paginate(20);

        $patients = Patient::where('clinic_id', Auth::user()->clinic_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $visitTypes = \App\Models\VisitType::where('clinic_id', Auth::user()->clinic_id)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('clinic.appointments', compact('appointments', 'patients', 'visitTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'visit_type_id' => ['nullable', 'exists:visit_types,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (isset($validated['visit_type_id'])) {
            $visitType = \App\Models\VisitType::findOrFail($validated['visit_type_id']);
            abort_if($visitType->clinic_id !== Auth::user()->clinic_id, 403);
            if (!isset($validated['price'])) {
                $validated['price'] = $visitType->price;
            }
        }

        // Ensure the patient belongs to this clinic
        $patient = Patient::findOrFail($validated['patient_id']);
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        Appointment::create([
            ...$validated,
            'clinic_id' => Auth::user()->clinic_id,
            'doctor_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تمت إضافة الموعد بنجاح!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        abort_if($appointment->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $appointment->update($validated);

        return back()->with('success', 'تم تحديث حالة الموعد بنجاح.');
    }

    public function destroy(Appointment $appointment)
    {
        abort_if($appointment->clinic_id !== Auth::user()->clinic_id, 403);
        $appointment->delete();

        return redirect()->route('clinic.appointments.index')
            ->with('success', 'تم حذف الموعد بنجاح.');
    }
}

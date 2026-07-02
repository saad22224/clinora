<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(20);

        return view('clinic.patients', compact('patients'));
    }

    public function medicalFile(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $patient->load([
            'medicalFiles' => fn ($q) => $q->latest(),
            'prescriptions' => fn ($q) => $q->with('items')->latest(),
            'diagnoses',
        ]);

        return view('clinic.patient-medical-file', compact('patient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'medical_history' => ['nullable', 'string', 'max:5000'],
            'surgical_history' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Patient::create([
            ...$validated,
            'clinic_id' => Auth::user()->clinic_id,
        ]);

        return redirect()->route('clinic.patients.index')
            ->with('success', 'تمت إضافة المريض بنجاح!');
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorizePatient($patient);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'medical_history' => ['nullable', 'string', 'max:5000'],
            'surgical_history' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $patient->update($validated);

        return redirect()->route('clinic.patients.index')
            ->with('success', 'تم تحديث بيانات المريض بنجاح!');
    }

    public function destroy(Patient $patient)
    {
        $this->authorizePatient($patient);
        $patient->delete();

        return redirect()->route('clinic.patients.index')
            ->with('success', 'تم حذف المريض بنجاح.');
    }

    private function authorizePatient(Patient $patient): void
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);
    }
}

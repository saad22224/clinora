<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\VisitType;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('search', ''));

        $patients = Patient::where('clinic_id', $this->currentClinicId())
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%")
                        ->orWhere('notes', 'LIKE', "%{$query}%")
                        ->orWhere('medical_history', 'LIKE', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('clinic.patients', compact('patients'));
    }

    public function medicalFile(Patient $patient)
    {
        abort_if($patient->clinic_id != $this->currentClinicId(), 403);

        $patient->load([
            'medicalFiles' => fn ($q) => $q->latest(),
            'prescriptions' => fn ($q) => $q->with(['items', 'visit'])->latest(),
            'diagnoses' => fn ($q) => $q->with('visit')->latest('diagnosed_at'),
            'visits' => fn ($q) => $q->with('doctor')->latest('visited_at'),
            'appointments' => fn ($q) => $q->with('visitType')->latest('scheduled_at')->limit(20),
        ]);

        $visitTypes = VisitType::query()
            ->where('clinic_id', $this->currentClinicId())
            ->orderBy('name')
            ->get();

        return view('clinic.patient-medical-file', compact('patient', 'visitTypes'));
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

        $validated = Patient::fillAgeFromDobWhenEmpty($validated);

        Patient::create([
            ...$validated,
            'clinic_id' => $this->currentClinicId(),
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

        $validated = Patient::fillAgeFromDobWhenEmpty($validated);

        $patient->update($validated);

        return redirect()->back()
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
        abort_if($patient->clinic_id != $this->currentClinicId(), 403);
    }
}

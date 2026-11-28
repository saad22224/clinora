<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use App\Models\PrescriptionTemplate;
use App\Models\RadiologyExam;
use App\Models\Visit;
use App\Models\VisitType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'visited_at' => ['nullable', 'date'],
        ]);

        $clinic = $this->currentClinic();
        $doctorId = $clinic->users()->where('role', 'owner')->value('id')
            ?? $clinic->users()->where('role', 'doctor')->value('id')
            ?? Auth::id();

        $visit = Visit::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctorId,
            'created_by' => Auth::id(),
            'visited_at' => isset($validated['visited_at'])
                ? Carbon::parse($validated['visited_at'])
                : now(),
            'status' => 'open',
        ]);

        return redirect()->route('clinic.visits.show', [$patient, $visit])
            ->with('success', 'تم إنشاء الزيارة الطبية.');
    }

    public function show(Patient $patient, Visit $visit)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);

        $visit->load([
            'diagnoses',
            'prescriptions.items',
            'medicalFiles',
            'doctor',
            'labOrders.items',
            'labOrders.medicalFiles',
            'radiologyOrders.items',
            'radiologyOrders.medicalFiles',
            'followUpAppointments',
        ]);

        $labTests = LabTest::query()->where('is_active', true)->orderBy('category')->orderBy('name_ar')->get();
        $radiologyExams = RadiologyExam::query()->where('is_active', true)->orderBy('category')->orderBy('name_ar')->get();
        $visitTypes = VisitType::query()->where('clinic_id', $this->currentClinicId())->orderBy('name')->get();
        $templates = PrescriptionTemplate::query()
            ->with('items')
            ->where('clinic_id', $this->currentClinicId())
            ->latest()
            ->get();

        return view('clinic.visits.show', compact(
            'patient',
            'visit',
            'labTests',
            'radiologyExams',
            'visitTypes',
            'templates',
        ));
    }

    public function update(Request $request, Patient $patient, Visit $visit)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);

        $validated = $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:2000'],
            'clinical_notes' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'in:open,completed'],
            'visited_at' => ['nullable', 'date'],
        ]);

        $visit->update([
            'chief_complaint' => $validated['chief_complaint'] ?? $visit->chief_complaint,
            'clinical_notes' => $validated['clinical_notes'] ?? $visit->clinical_notes,
            'status' => $validated['status'] ?? $visit->status,
            'visited_at' => isset($validated['visited_at'])
                ? Carbon::parse($validated['visited_at'])
                : $visit->visited_at,
        ]);

        return $this->redirectBackToVisitTab($request, 'visit-info', 'تم تحديث الزيارة.');
    }

    public function storeDiagnosis(Request $request, Patient $patient, Visit $visit)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);

        $validated = $request->validate([
            'diagnosis' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        PatientDiagnosis::create([
            'clinic_id' => $this->currentClinicId(),
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
            'diagnosed_at' => now(),
        ]);

        return $this->redirectBackToVisitTab($request, 'visit-diagnosis', 'تم إضافة التشخيص للزيارة.');
    }

    public function updateDiagnosis(Request $request, Patient $patient, Visit $visit, PatientDiagnosis $diagnosis)
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);
        abort_if((int) $diagnosis->patient_id !== (int) $patient->id, 404);
        abort_if((int) $diagnosis->visit_id !== (int) $visit->id, 404);

        $validated = $request->validate([
            'diagnosis' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $diagnosis->update([
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return $this->redirectBackToVisitTab($request, 'visit-diagnosis', 'تم تحديث التشخيص.');
    }

    private function redirectBackToVisitTab(Request $request, string $defaultTab, string $message): \Illuminate\Http\RedirectResponse
    {
        $allowed = [
            'visit-info',
            'visit-diagnosis',
            'visit-prescriptions',
            'visit-lab',
            'visit-radiology',
            'visit-results',
            'visit-followup',
        ];
        $tab = (string) $request->input('return_tab', $defaultTab);
        if (! in_array($tab, $allowed, true)) {
            $tab = $defaultTab;
        }

        return back()->withFragment($tab)->with('success', $message);
    }
}

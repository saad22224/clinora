<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDiagnosisController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id != Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'diagnosis' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'diagnosed_at' => ['required', 'date'],
        ]);

        PatientDiagnosis::create([
            ...$validated,
            'clinic_id' => Auth::user()->clinic_id,
            'patient_id' => $patient->id,
        ]);

        return back()->with('success', 'تم إضافة التشخيص بنجاح!');
    }

    public function destroy(Patient $patient, PatientDiagnosis $diagnosis)
    {
        abort_if($patient->clinic_id != Auth::user()->clinic_id, 403);
        abort_if($diagnosis->patient_id != $patient->id, 404);

        $diagnosis->delete();

        return back()->with('success', 'تم حذف التشخيص.');
    }
}

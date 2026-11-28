<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use Illuminate\Http\Request;

class PatientDiagnosisController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id != $this->currentClinicId(), 403);

        return back()
            ->withErrors(['diagnosis' => 'يجب إضافة التشخيص من داخل زيارة طبية مرتبطة بالمريض.'])
            ->withInput();
    }

    public function destroy(Patient $patient, PatientDiagnosis $diagnosis)
    {
        abort_if($patient->clinic_id != $this->currentClinicId(), 403);
        abort_if($diagnosis->patient_id != $patient->id, 404);

        $diagnosis->delete();

        return back()->with('success', 'تم حذف التشخيص.');
    }
}

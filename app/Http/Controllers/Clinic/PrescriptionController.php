<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index()
    {
        $clinic = Auth::user()->clinic;
        $prescriptions = $clinic->prescriptions()->with('patient')->latest()->paginate(15);
        $patients = $clinic->patients()->latest()->get();

        return view('clinic.prescriptions', compact('prescriptions', 'patients'));
    }

    public function store(Request $request)
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        // Verify patient belongs to this clinic
        if ($clinic->patients()->where('id', $validated['patient_id'])->doesntExist()) {
            abort(403, 'غير مصرح');
        }

        Prescription::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $validated['patient_id'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('clinic.prescriptions.index')
            ->with('success', 'تم إضافة الوصفة الطبية بنجاح!');
    }

    public function destroy(Prescription $prescription)
    {
        if ($prescription->clinic_id !== Auth::user()->clinic_id) {
            abort(403);
        }

        $prescription->delete();

        return back()->with('success', 'تم حذف الوصفة الطبية بنجاح.');
    }

    public function print(Prescription $prescription)
    {
        if ($prescription->clinic_id !== Auth::user()->clinic_id) {
            abort(403);
        }

        $prescription->load('patient');
        $clinic = Auth::user()->clinic;

        return view('clinic.prescriptions-print', compact('prescription', 'clinic'));
    }
}

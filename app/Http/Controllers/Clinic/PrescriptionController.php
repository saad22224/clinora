<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $clinic = Auth::user()->clinic;
        $query = trim($request->query('search', ''));

        $prescriptions = $clinic->prescriptions()
            ->with(['patient', 'items'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sq) use ($query) {
                    $sq->whereHas('patient', function ($pq) use ($query) {
                        $pq->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('phone', 'LIKE', "%{$query}%");
                    })->orWhere('notes', 'LIKE', "%{$query}%")
                        ->orWhereHas('items', function ($iq) use ($query) {
                            $iq->where('medicine_name', 'LIKE', "%{$query}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $patients = $clinic->patients()->latest()->get();

        return view('clinic.prescriptions', compact('prescriptions', 'patients'));
    }

    public function searchDrugs(Request $request)
    {
        $query = trim($request->query('q', ''));
        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $drugs = Drug::query()
            ->where(function ($q) use ($query) {
                $q->where('commercial_name_en', 'LIKE', "%{$query}%")
                    ->orWhere('commercial_name_ar', 'LIKE', "%{$query}%")
                    ->orWhere('scientific_name', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'commercial_name_en', 'commercial_name_ar', 'scientific_name', 'route', 'price_egp']);

        return response()->json($drugs);
    }

    public function store(Request $request)
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['nullable', 'array'],
            'items.*.medicine_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string', 'max:255'],
        ]);

        // Verify patient belongs to this clinic
        if ($clinic->patients()->where('id', $validated['patient_id'])->doesntExist()) {
            abort(403, 'غير مصرح');
        }

        if (empty($validated['notes']) && empty($validated['items'])) {
            return back()->withErrors(['notes' => 'يرجى إدخال أدوية أو ملاحظات للروشتة على الأقل.'])->withInput();
        }

        $prescription = Prescription::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $validated['patient_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (! empty(trim($item['medicine_name']))) {
                    $prescription->items()->create([
                        'medicine_name' => trim($item['medicine_name']),
                        'dosage' => ! empty($item['dosage']) ? trim($item['dosage']) : null,
                        'instructions' => ! empty($item['instructions']) ? trim($item['instructions']) : null,
                    ]);
                }
            }
        }

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

        $prescription->load(['patient', 'items']);
        $clinic = Auth::user()->clinic;

        return view('clinic.prescriptions-print', compact('prescription', 'clinic'));
    }
}

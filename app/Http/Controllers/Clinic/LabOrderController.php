<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\LabTest;
use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabOrderController extends Controller
{
    private const DISK = 'local';

    public function store(Request $request, Patient $patient, Visit $visit)
    {
        $this->authorizeVisit($patient, $visit);

        $validated = $request->validate([
            'lab_test_ids' => ['required', 'array', 'min:1'],
            'lab_test_ids.*' => ['integer', 'exists:lab_tests,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tests = LabTest::query()
            ->whereIn('id', $validated['lab_test_ids'])
            ->where('is_active', true)
            ->get();

        abort_if($tests->isEmpty(), 422);

        $order = DB::transaction(function () use ($validated, $tests, $patient, $visit) {
            $order = LabOrder::create([
                'clinic_id' => $this->currentClinicId(),
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'ordered_by' => Auth::id(),
                'status' => 'ordered',
                'notes' => $validated['notes'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($tests as $test) {
                $order->items()->create([
                    'lab_test_id' => $test->id,
                    'name_snapshot' => $test->name_en ?: $test->name_ar,
                    'status' => 'ordered',
                ]);
            }

            return $order;
        });

        return redirect()->route('clinic.lab-orders.print', [$patient, $visit, $order])
            ->with('success', 'تم إنشاء طلب التحاليل.');
    }

    public function print(Patient $patient, Visit $visit, LabOrder $labOrder)
    {
        $this->authorizeVisit($patient, $visit);
        abort_if((int) $labOrder->visit_id !== (int) $visit->id, 404);
        abort_if((int) $labOrder->clinic_id !== $this->currentClinicId(), 403);

        $labOrder->load(['items', 'orderedBy']);
        $clinic = $this->currentClinic();

        return view('clinic.orders.lab-print', [
            'patient' => $patient,
            'visit' => $visit,
            'order' => $labOrder,
            'clinic' => $clinic,
            'title' => 'طلب تحاليل مخبرية',
        ]);
    }

    public function updateResults(Request $request, Patient $patient, Visit $visit, LabOrder $labOrder)
    {
        $this->authorizeVisit($patient, $visit);
        abort_if((int) $labOrder->visit_id !== (int) $visit->id, 404);
        abort_if((int) $labOrder->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'result_notes' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'in:ordered,partial,completed'],
            'file' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,heic'],
        ]);

        $labOrder->update([
            'result_notes' => $validated['result_notes'] ?? $labOrder->result_notes,
            'status' => $validated['status'] ?? ($request->hasFile('file') || filled($validated['result_notes'] ?? null) ? 'completed' : $labOrder->status),
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if (str_starts_with((string) $file->getMimeType(), 'image/') || in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic'], true)) {
                $path = app(ImageOptimizationService::class)->upload($file, 'medical-records', null, null, self::DISK);
            } else {
                $path = $file->store('medical-records', self::DISK);
            }

            MedicalFile::create([
                'clinic_id' => $this->currentClinicId(),
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'lab_order_id' => $labOrder->id,
                'type' => 'lab_result',
                'file_path' => $path,
            ]);
        }

        $tab = (string) $request->input('return_tab', 'visit-results');
        if (! in_array($tab, ['visit-results', 'visit-lab'], true)) {
            $tab = 'visit-results';
        }

        return back()->withFragment($tab)->with('success', 'تم حفظ نتيجة التحليل.');
    }

    private function authorizeVisit(Patient $patient, Visit $visit): void
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);
    }
}

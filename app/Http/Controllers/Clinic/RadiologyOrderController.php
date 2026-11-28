<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\RadiologyExam;
use App\Models\RadiologyOrder;
use App\Models\Visit;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RadiologyOrderController extends Controller
{
    private const DISK = 'local';

    public function store(Request $request, Patient $patient, Visit $visit)
    {
        $this->authorizeVisit($patient, $visit);

        $validated = $request->validate([
            'radiology_exam_ids' => ['required', 'array', 'min:1'],
            'radiology_exam_ids.*' => ['integer', 'exists:radiology_exams,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $exams = RadiologyExam::query()
            ->whereIn('id', $validated['radiology_exam_ids'])
            ->where('is_active', true)
            ->get();

        abort_if($exams->isEmpty(), 422);

        $order = DB::transaction(function () use ($validated, $exams, $patient, $visit) {
            $order = RadiologyOrder::create([
                'clinic_id' => $this->currentClinicId(),
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'ordered_by' => Auth::id(),
                'status' => 'ordered',
                'notes' => $validated['notes'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($exams as $exam) {
                $order->items()->create([
                    'radiology_exam_id' => $exam->id,
                    'name_snapshot' => $exam->name_en ?: $exam->name_ar,
                    'status' => 'ordered',
                ]);
            }

            return $order;
        });

        return redirect()->route('clinic.radiology-orders.print', [$patient, $visit, $order])
            ->with('success', 'تم إنشاء طلب الأشعة.');
    }

    public function print(Patient $patient, Visit $visit, RadiologyOrder $radiologyOrder)
    {
        $this->authorizeVisit($patient, $visit);
        abort_if((int) $radiologyOrder->visit_id !== (int) $visit->id, 404);
        abort_if((int) $radiologyOrder->clinic_id !== $this->currentClinicId(), 403);

        $radiologyOrder->load(['items', 'orderedBy']);
        $clinic = $this->currentClinic();

        return view('clinic.orders.radiology-print', [
            'patient' => $patient,
            'visit' => $visit,
            'order' => $radiologyOrder,
            'clinic' => $clinic,
            'title' => 'طلب أشعة وتصوير',
        ]);
    }

    public function updateResults(Request $request, Patient $patient, Visit $visit, RadiologyOrder $radiologyOrder)
    {
        $this->authorizeVisit($patient, $visit);
        abort_if((int) $radiologyOrder->visit_id !== (int) $visit->id, 404);
        abort_if((int) $radiologyOrder->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'result_notes' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'in:ordered,partial,completed'],
            'file' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,heic'],
        ]);

        $radiologyOrder->update([
            'result_notes' => $validated['result_notes'] ?? $radiologyOrder->result_notes,
            'status' => $validated['status'] ?? ($request->hasFile('file') || filled($validated['result_notes'] ?? null) ? 'completed' : $radiologyOrder->status),
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
                'radiology_order_id' => $radiologyOrder->id,
                'type' => 'radiology_result',
                'file_path' => $path,
            ]);
        }

        $tab = (string) $request->input('return_tab', 'visit-results');
        if (! in_array($tab, ['visit-results', 'visit-radiology'], true)) {
            $tab = 'visit-results';
        }

        return back()->withFragment($tab)->with('success', 'تم حفظ نتيجة الأشعة.');
    }

    private function authorizeVisit(Patient $patient, Visit $visit): void
    {
        abort_if((int) $patient->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->clinic_id !== $this->currentClinicId(), 403);
        abort_if((int) $visit->patient_id !== (int) $patient->id, 404);
    }
}

<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Models\Prescription;
use App\Models\PrescriptionTemplate;
use App\Models\Visit;
use App\Support\PrescriptionPrint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $clinic = $this->currentClinic();
        $query = trim($request->query('search', ''));

        $prescriptions = $clinic->prescriptions()
            ->with(['patient', 'items', 'visit'])
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

        $templates = PrescriptionTemplate::query()
            ->with('items')
            ->where('clinic_id', $clinic->id)
            ->latest()
            ->get();

        return view('clinic.prescriptions', compact('prescriptions', 'templates'));
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
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['required', 'integer', Rule::exists('visits', 'id')],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['nullable', 'array'],
            'items.*.medicine_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string', 'max:255'],
        ]);

        if ($clinic->patients()->where('id', $validated['patient_id'])->doesntExist()) {
            abort(403, 'غير مصرح');
        }

        $visit = Visit::query()
            ->whereKey($validated['visit_id'])
            ->where('clinic_id', $clinic->id)
            ->where('patient_id', $validated['patient_id'])
            ->firstOrFail();

        if (empty($validated['notes']) && empty($validated['items'])) {
            return back()->withErrors(['notes' => 'يرجى إدخال أدوية أو ملاحظات للروشتة على الأقل.'])->withInput();
        }

        $prescription = Prescription::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $validated['patient_id'],
            'visit_id' => $visit->id,
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

        return redirect()
            ->route('clinic.prescriptions.print', $prescription)
            ->with('success', 'تم إضافة الوصفة الطبية. يمكنك طباعتها الآن.');
    }

    public function destroy(Prescription $prescription)
    {
        if ($prescription->clinic_id != $this->currentClinicId()) {
            abort(403);
        }

        $prescription->delete();

        return back()->with('success', 'تم حذف الوصفة الطبية بنجاح.');
    }

    public function print(Prescription $prescription)
    {
        if ($prescription->clinic_id != $this->currentClinicId()) {
            abort(403);
        }

        $prescription->load(['patient', 'items']);
        $clinic = $this->currentClinic();
        $layout = PrescriptionPrint::normalizeLayout($clinic->prescription_layout);
        $locale = PrescriptionPrint::normalizeLocale($clinic->prescription_print_locale);
        $labels = PrescriptionPrint::labels($locale);
        $doctorName = PrescriptionPrint::doctorDisplayName($clinic, $prescription);
        $isPreview = false;

        return view('clinic.prescriptions-print', compact(
            'prescription',
            'clinic',
            'layout',
            'locale',
            'labels',
            'doctorName',
            'isPreview',
        ));
    }

    public function previewPrint(Request $request)
    {
        $clinic = $this->currentClinic();
        $layout = PrescriptionPrint::normalizeLayout($request->query('layout', $clinic->prescription_layout));
        $locale = PrescriptionPrint::normalizeLocale($request->query('locale', $clinic->prescription_print_locale));
        $labels = PrescriptionPrint::labels($locale);
        $doctorName = PrescriptionPrint::doctorDisplayName($clinic);
        $isPreview = true;

        $prescription = new Prescription([
            'notes' => $locale === 'en'
                ? 'Take with food. Follow up in one week.'
                : 'يُؤخذ بعد الأكل. مراجعة بعد أسبوع.',
            'created_at' => now(),
        ]);
        $previewPatient = new class($locale)
        {
            public string $name;

            public ?int $age = 35;

            public mixed $dob = null;

            public function __construct(string $locale)
            {
                $this->name = $locale === 'en' ? 'Sample Patient' : 'مريض تجريبي';
            }

            public function displayAge(): ?int
            {
                return 35;
            }
        };
        $prescription->setRelation('patient', $previewPatient);
        // Medicine names stay English in both AR/EN print locales.
        $prescription->setRelation('items', collect([
            (object) [
                'medicine_name' => 'Amoxicillin 500mg',
                'dosage' => $locale === 'en' ? '1 capsule every 8 hours for 7 days' : 'كبسولة كل 8 ساعات لمدة 7 أيام',
                'instructions' => $locale === 'en' ? 'After meals' : 'بعد الأكل',
            ],
            (object) [
                'medicine_name' => 'Paracetamol 500mg',
                'dosage' => $locale === 'en' ? '1 tablet when needed' : 'قرص عند اللزوم',
                'instructions' => $locale === 'en' ? 'Max 3 times daily' : 'بحد أقصى 3 مرات يوميًا',
            ],
        ]));

        return view('clinic.prescriptions-print', compact(
            'prescription',
            'clinic',
            'layout',
            'locale',
            'labels',
            'doctorName',
            'isPreview',
        ));
    }
}

<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\PrescriptionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionTemplateController extends Controller
{
    public function index()
    {
        $templates = PrescriptionTemplate::query()
            ->with('items')
            ->where('clinic_id', $this->currentClinicId())
            ->latest()
            ->get();

        return response()->json([
            'templates' => $templates->map(fn (PrescriptionTemplate $template): array => [
                'id' => $template->id,
                'name' => $template->name,
                'items' => $template->items->map(fn ($item): array => [
                    'medicine_name' => $item->medicine_name,
                    'dosage' => $item->dosage,
                    'instructions' => $item->instructions,
                ])->values(),
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string', 'max:255'],
        ]);

        $template = DB::transaction(function () use ($validated, $request) {
            $template = PrescriptionTemplate::create([
                'clinic_id' => $this->currentClinicId(),
                'created_by' => $request->user()?->id,
                'name' => $validated['name'],
            ]);

            foreach (array_values($validated['items']) as $index => $item) {
                $template->items()->create([
                    'medicine_name' => trim($item['medicine_name']),
                    'dosage' => filled($item['dosage'] ?? null) ? trim($item['dosage']) : null,
                    'instructions' => filled($item['instructions'] ?? null) ? trim($item['instructions']) : null,
                    'sort_order' => $index,
                ]);
            }

            return $template->load('items');
        });

        if ($request->expectsJson()) {
            return response()->json(['template' => $template], 201);
        }

        return back()->with('success', 'تم حفظ قالب الأدوية بنجاح.');
    }

    public function update(Request $request, PrescriptionTemplate $prescriptionTemplate)
    {
        abort_if((int) $prescriptionTemplate->clinic_id !== $this->currentClinicId(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($prescriptionTemplate, $validated) {
            $prescriptionTemplate->update(['name' => $validated['name']]);
            $prescriptionTemplate->items()->delete();

            foreach (array_values($validated['items']) as $index => $item) {
                $prescriptionTemplate->items()->create([
                    'medicine_name' => trim($item['medicine_name']),
                    'dosage' => filled($item['dosage'] ?? null) ? trim($item['dosage']) : null,
                    'instructions' => filled($item['instructions'] ?? null) ? trim($item['instructions']) : null,
                    'sort_order' => $index,
                ]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['template' => $prescriptionTemplate->fresh('items')]);
        }

        return back()->with('success', 'تم تحديث قالب الأدوية.');
    }

    public function destroy(PrescriptionTemplate $prescriptionTemplate)
    {
        abort_if((int) $prescriptionTemplate->clinic_id !== $this->currentClinicId(), 403);

        $prescriptionTemplate->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'تم حذف قالب الأدوية.');
    }
}

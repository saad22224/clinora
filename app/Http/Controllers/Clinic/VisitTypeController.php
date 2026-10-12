<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\VisitType;
use Illuminate\Http\Request;

class VisitTypeController extends Controller
{
    public function index()
    {
        $clinic = $this->currentClinic();
        $visitTypes = $clinic->visitTypes()->latest()->paginate(15);

        return view('clinic.visit-types', compact('visitTypes'));
    }

    public function store(Request $request)
    {
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $clinic->visitTypes()->create($validated);

        return redirect()->route('clinic.visit-types.index')
            ->with('success', 'تم إضافة نوع الزيارة بنجاح!');
    }

    public function update(Request $request, VisitType $visitType)
    {
        if ($visitType->clinic_id != $this->currentClinicId()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $visitType->update($validated);

        return redirect()->route('clinic.visit-types.index')
            ->with('success', 'تم تحديث نوع الزيارة بنجاح!');
    }

    public function destroy(VisitType $visitType)
    {
        if ($visitType->clinic_id != $this->currentClinicId()) {
            abort(403);
        }

        $visitType->delete();

        return back()->with('success', 'تم حذف نوع الزيارة بنجاح.');
    }
}

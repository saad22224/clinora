<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\MedicalFile;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalFileController extends Controller
{
    public function index(Request $request)
    {
        $clinic = Auth::user()->clinic;
        $query = trim($request->query('search', ''));

        $records = $clinic->medicalFiles()
            ->with('patient')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sq) use ($query) {
                    $sq->whereHas('patient', function ($pq) use ($query) {
                        $pq->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('phone', 'LIKE', "%{$query}%");
                    })->orWhere('type', 'LIKE', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $patients = $clinic->patients()->latest()->get();

        return view('clinic.records', compact('records', 'patients'));
    }

    public function store(Request $request)
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'type' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // Max 10MB
        ]);

        // Verify patient belongs to this clinic
        if ($clinic->patients()->where('id', $validated['patient_id'])->doesntExist()) {
            abort(403, 'غير مصرح');
        }

        $file = $request->file('file');
        if (str_starts_with($file->getMimeType(), 'image/') || in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic'])) {
            $path = app(ImageOptimizationService::class)->upload($file, 'medical-records');
        } else {
            $path = $file->store('medical-records', 'public');
        }

        MedicalFile::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $validated['patient_id'],
            'type' => $validated['type'],
            'file_path' => $path,
        ]);

        return back()
            ->with('success', 'تم إضافة السجل الطبي بنجاح!');
    }

    public function destroy(MedicalFile $medicalFile)
    {
        if ($medicalFile->clinic_id !== Auth::user()->clinic_id) {
            abort(403);
        }

        Storage::disk('public')->delete($medicalFile->file_path);
        $medicalFile->delete();

        return back()->with('success', 'تم حذف السجل الطبي بنجاح.');
    }
}

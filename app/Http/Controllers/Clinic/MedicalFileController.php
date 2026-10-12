<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\MedicalFile;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalFileController extends Controller
{
    private const DISK = 'local';

    public function index(Request $request)
    {
        $clinic = $this->currentClinic();
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
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'type' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,heic'],
        ]);

        if ($clinic->patients()->where('id', $validated['patient_id'])->doesntExist()) {
            abort(403, 'غير مصرح');
        }

        $file = $request->file('file');
        if (str_starts_with((string) $file->getMimeType(), 'image/') || in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic'], true)) {
            $path = app(ImageOptimizationService::class)->upload($file, 'medical-records', null, null, self::DISK);
        } else {
            $path = $file->store('medical-records', self::DISK);
        }

        MedicalFile::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $validated['patient_id'],
            'type' => $validated['type'],
            'file_path' => $path,
        ]);

        return back()->with('success', 'تم إضافة السجل الطبي بنجاح!');
    }

    public function download(MedicalFile $medicalFile): StreamedResponse
    {
        $this->authorizeMedicalFile($medicalFile);

        $disk = $this->resolveDisk($medicalFile->file_path);

        abort_unless(Storage::disk($disk)->exists($medicalFile->file_path), 404);

        return Storage::disk($disk)->download($medicalFile->file_path);
    }

    public function preview(MedicalFile $medicalFile): StreamedResponse
    {
        $this->authorizeMedicalFile($medicalFile);

        $disk = $this->resolveDisk($medicalFile->file_path);

        abort_unless(Storage::disk($disk)->exists($medicalFile->file_path), 404);

        return Storage::disk($disk)->response($medicalFile->file_path);
    }

    public function destroy(MedicalFile $medicalFile)
    {
        $this->authorizeMedicalFile($medicalFile);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($medicalFile->file_path)) {
                Storage::disk($disk)->delete($medicalFile->file_path);
            }
        }

        $medicalFile->delete();

        return back()->with('success', 'تم حذف السجل الطبي بنجاح.');
    }

    private function authorizeMedicalFile(MedicalFile $medicalFile): void
    {
        abort_if((int) $medicalFile->clinic_id !== $this->currentClinicId(), 403);
    }

    private function resolveDisk(string $path): string
    {
        if (Storage::disk('local')->exists($path)) {
            return 'local';
        }

        if (Storage::disk('public')->exists($path)) {
            return 'public';
        }

        return 'local';
    }
}

@php
    use App\Support\PrescriptionPrint;
    use Illuminate\Support\Facades\Storage;

    $layout = PrescriptionPrint::normalizeLayout($layout ?? $clinic->prescription_layout ?? 'classic');
    $locale = PrescriptionPrint::normalizeLocale($locale ?? $clinic->prescription_print_locale ?? 'ar');
    $labels = $labels ?? PrescriptionPrint::labels($locale);
    $doctorName = $doctorName ?? PrescriptionPrint::doctorDisplayName($clinic, $prescription ?? null);
    $isPreview = $isPreview ?? false;
    $dir = $locale === 'en' ? 'ltr' : 'rtl';
    $patientAge = null;
    if (isset($prescription) && $prescription->patient) {
        $patientAge = method_exists($prescription->patient, 'displayAge')
            ? $prescription->patient->displayAge()
            : ($prescription->patient->dob->age ?? $prescription->patient->age ?? null);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $labels['print'] }} - {{ $prescription->patient->name ?? '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f1f5f9; color: #111; }
        .print-sheet { width: 210mm; min-height: 297mm; margin: 20px auto; background: #fff; position: relative; overflow: hidden; }
        .layout-elegant { font-family: 'Libre Baskerville', 'Cairo', serif; }
        .layout-modern .accent-bar { height: 8px; background: {{ $clinic->theme_color ?? '#1e3a5f' }}; }
        .layout-compact .rx-item { padding-bottom: 0.4rem; margin-bottom: 0.4rem; }
        .layout-detailed .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        @media print {
            body { background: #fff; margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-sheet { width: 100%; min-height: auto; margin: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="fixed top-4 {{ $dir === 'rtl' ? 'left-4' : 'right-4' }} z-50 no-print flex gap-2">
        <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-xl shadow-lg">{{ $labels['print'] }}</button>
        <button onclick="window.close()" class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl border">{{ $labels['close'] }}</button>
    </div>

    @if($isPreview)
        <div class="no-print text-center text-sm font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl max-w-xl mx-auto mt-4 px-4 py-2">
            {{ $labels['preview_sample'] }} — {{ PrescriptionPrint::layoutLabels()[$layout] ?? $layout }}
        </div>
    @endif

    <div class="print-sheet layout-{{ $layout }} shadow-lg rounded-xl">
        @include('clinic.prescriptions.print.'.$layout, [
            'clinic' => $clinic,
            'prescription' => $prescription,
            'labels' => $labels,
            'locale' => $locale,
            'dir' => $dir,
            'doctorName' => $doctorName,
            'patientAge' => $patientAge,
        ])
    </div>
</body>
</html>

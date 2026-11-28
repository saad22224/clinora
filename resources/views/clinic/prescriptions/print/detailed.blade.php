<div class="p-10 min-h-[297mm] flex flex-col">
    <header class="mb-6 border-b-4 border-double border-gray-800 pb-4">
        <div class="flex justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black">{{ $doctorName }}</h1>
                <p class="font-bold text-gray-700">{{ $clinic->name }}</p>
                @if($clinic->doctor_bio)<p class="text-xs text-gray-500 mt-2 max-w-md leading-relaxed">{{ \Illuminate\Support\Str::limit($clinic->doctor_bio, 160) }}</p>@endif
            </div>
            <div class="text-xs text-gray-600 space-y-1 text-end">
                @if($clinic->address)<p>{{ $labels['address'] }}: {{ $clinic->address }}</p>@endif
                @if($clinic->phone)<p class="dir-ltr">{{ $labels['phone'] }}: {{ $clinic->phone }}</p>@endif
            </div>
        </div>
    </header>

    <div class="meta-grid mb-6 text-sm border border-gray-300 rounded-lg p-4">
        <p><span class="font-bold text-gray-500">{{ $labels['patient'] }}:</span> {{ $prescription->patient->name ?? '—' }}</p>
        <p><span class="font-bold text-gray-500">{{ $labels['age'] }}:</span> {{ $patientAge !== null ? $patientAge.' '.$labels['years'] : '—' }}</p>
        <p><span class="font-bold text-gray-500">{{ $labels['date'] }}:</span> {{ optional($prescription->created_at)->format('Y/m/d') }}</p>
        <p><span class="font-bold text-gray-500">{{ $labels['clinic'] }}:</span> {{ $clinic->name }}</p>
    </div>

    <h2 class="text-sm font-black uppercase tracking-wider mb-3 border-b pb-1">{{ $labels['medications'] }}</h2>
    <div class="flex-1">
        @include('clinic.prescriptions.print._items', ['titleClass' => 'text-base font-black', 'listClass' => 'space-y-4'])
    </div>

    <footer class="mt-10 grid grid-cols-2 gap-8 text-sm">
        <div>
            <p class="font-bold mb-2">{{ $labels['wish'] }}</p>
            <p class="text-xs text-gray-500">{{ $clinic->address }}</p>
        </div>
        <div class="text-center">
            <div class="h-16 border-b border-gray-400 mb-2"></div>
            <p class="font-bold">{{ $labels['signature'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $doctorName }}</p>
        </div>
    </footer>
</div>

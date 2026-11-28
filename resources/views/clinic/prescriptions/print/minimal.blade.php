<div class="p-12 min-h-[297mm] flex flex-col">
    <header class="mb-10">
        <p class="text-xs tracking-[0.3em] uppercase text-gray-400 mb-2">{{ $labels['clinic'] }}</p>
        <h1 class="text-2xl font-light text-gray-900">{{ $clinic->name }}</h1>
        <p class="text-base text-gray-600 mt-1">{{ $doctorName }}</p>
    </header>

    <div class="border-y border-gray-200 py-4 mb-8 text-sm space-y-1">
        <p><span class="text-gray-400">{{ $labels['patient'] }}</span> · <strong>{{ $prescription->patient->name ?? '—' }}</strong></p>
        <p><span class="text-gray-400">{{ $labels['date'] }}</span> · {{ optional($prescription->created_at)->format('Y/m/d') }}
            @if($patientAge !== null) · {{ $labels['age'] }} {{ $patientAge }}@endif
        </p>
    </div>

    <div class="flex-1">
        @include('clinic.prescriptions.print._items', ['titleClass' => 'text-lg font-semibold text-gray-900', 'itemClass' => 'pb-4 mb-4 border-b border-gray-100'])
    </div>

    <footer class="pt-8 text-xs text-gray-400 flex justify-between">
        <span>{{ $clinic->phone }}</span>
        <span>{{ $labels['signature'] }}</span>
    </footer>
</div>

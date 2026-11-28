<div class="p-12 min-h-[297mm] flex flex-col relative">
    <div class="absolute inset-x-8 top-8 bottom-8 border border-gray-300 pointer-events-none"></div>
    <div class="relative z-10 flex flex-col flex-1 px-4">
        <header class="text-center mb-10">
            @if($clinic->logo || $clinic->doctor_image)
                <img src="{{ Storage::url($clinic->logo ?: $clinic->doctor_image) }}" alt="" class="h-16 mx-auto mb-4 object-contain">
            @endif
            <h1 class="text-3xl font-bold tracking-wide">{{ $clinic->name }}</h1>
            <p class="mt-2 text-lg italic text-gray-700">{{ $doctorName }}</p>
            <div class="mx-auto mt-4 w-24 border-t border-gray-400"></div>
        </header>

        <div class="text-center mb-8 text-sm">
            <p class="text-xl font-semibold">{{ $prescription->patient->name ?? '—' }}</p>
            <p class="text-gray-500 mt-1">
                {{ $labels['date'] }}: {{ optional($prescription->created_at)->format('Y/m/d') }}
                @if($patientAge !== null) · {{ $labels['age'] }}: {{ $patientAge }} {{ $labels['years'] }}@endif
            </p>
        </div>

        <div class="flex-1 max-w-xl mx-auto w-full">
            <p class="text-center text-4xl font-serif mb-6">℞</p>
            @include('clinic.prescriptions.print._items', ['titleClass' => 'text-lg font-semibold', 'itemClass' => 'pb-4 mb-4 border-b border-dotted border-gray-300'])
        </div>

        <footer class="mt-12 text-center">
            <div class="mx-auto w-56 border-b border-gray-500 mb-2 h-12"></div>
            <p class="text-sm tracking-wide">{{ $labels['signature'] }}</p>
            <p class="text-xs text-gray-500 mt-6">{{ $labels['wish'] }}</p>
        </footer>
    </div>
</div>

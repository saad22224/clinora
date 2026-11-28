<div class="p-10 md:p-12 min-h-[297mm] flex flex-col">
    <header class="flex justify-between items-start border-b-2 border-gray-900 pb-6 mb-6">
        <div>
            <h1 class="text-3xl font-black mb-1">{{ $doctorName }}</h1>
            <p class="text-lg font-bold text-gray-700">{{ $clinic->name }}</p>
            @if($clinic->address)<p class="text-sm text-gray-500 mt-2">{{ $clinic->address }}</p>@endif
            @if($clinic->phone)<p class="text-sm text-gray-500 dir-ltr">{{ $clinic->phone }}</p>@endif
        </div>
        <div>
            @if($clinic->logo)
                <img src="{{ Storage::url($clinic->logo) }}" alt="" class="max-h-24 object-contain">
            @elseif($clinic->doctor_image)
                <img src="{{ Storage::url($clinic->doctor_image) }}" alt="" class="max-h-24 object-contain rounded-xl">
            @else
                <div class="w-20 h-20 rounded-2xl border-2 border-gray-900 flex items-center justify-center font-black text-3xl">{{ mb_substr($clinic->name, 0, 1) }}</div>
            @endif
        </div>
    </header>

    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-8 flex flex-wrap gap-6 justify-between">
        <div><span class="text-sm font-bold text-gray-500">{{ $labels['patient'] }}:</span> <strong class="text-lg">{{ $prescription->patient->name ?? '—' }}</strong></div>
        <div><span class="text-sm font-bold text-gray-500">{{ $labels['age'] }}:</span> <strong class="text-lg">{{ $patientAge !== null ? $patientAge.' '.$labels['years'] : '—' }}</strong></div>
        <div><span class="text-sm font-bold text-gray-500">{{ $labels['date'] }}:</span> <strong class="text-lg">{{ optional($prescription->created_at)->format('Y/m/d') }}</strong></div>
    </div>

    <div class="flex-1">
        <div class="text-5xl font-black mb-6 font-serif">Rx</div>
        @include('clinic.prescriptions.print._items')
    </div>

    <footer class="mt-12 pt-6 border-t-2 border-gray-900 flex justify-between items-end">
        <p class="text-sm font-bold text-gray-500">{{ $labels['wish'] }}</p>
        <div class="text-center w-48">
            <div class="border-b-2 border-dashed border-gray-400 mb-2 h-10"></div>
            <p class="text-sm font-bold">{{ $labels['signature'] }}</p>
        </div>
    </footer>
</div>

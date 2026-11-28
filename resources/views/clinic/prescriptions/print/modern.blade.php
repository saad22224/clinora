<div class="min-h-[297mm] flex flex-col">
    <div class="accent-bar"></div>
    <div class="p-10 flex-1 flex flex-col">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-black" style="color: {{ $clinic->theme_color ?? '#1e3a5f' }}">{{ $clinic->name }}</h1>
                <p class="font-bold text-gray-700 mt-1">{{ $doctorName }}</p>
                @if($clinic->doctor_title)<p class="text-sm text-gray-500">{{ $clinic->doctor_title }}</p>@endif
            </div>
            @if($clinic->logo || $clinic->doctor_image)
                <img src="{{ Storage::url($clinic->logo ?: $clinic->doctor_image) }}" alt="" class="h-16 w-16 object-cover rounded-2xl shadow">
            @endif
        </header>

        <div class="rounded-2xl bg-slate-50 p-5 mb-8 grid grid-cols-3 gap-3 text-sm">
            <div><p class="text-gray-400 text-xs">{{ $labels['patient'] }}</p><p class="font-black">{{ $prescription->patient->name ?? '—' }}</p></div>
            <div><p class="text-gray-400 text-xs">{{ $labels['age'] }}</p><p class="font-black">{{ $patientAge !== null ? $patientAge.' '.$labels['years'] : '—' }}</p></div>
            <div><p class="text-gray-400 text-xs">{{ $labels['date'] }}</p><p class="font-black">{{ optional($prescription->created_at)->format('Y/m/d') }}</p></div>
        </div>

        <p class="text-xs font-black tracking-widest uppercase text-gray-400 mb-4">{{ $labels['medications'] }}</p>
        <div class="flex-1">
            @include('clinic.prescriptions.print._items', ['titleClass' => 'text-lg font-black', 'itemClass' => 'rounded-xl border border-slate-100 p-3 mb-3'])
        </div>

        <footer class="mt-8 flex justify-between items-end border-t pt-6">
            <p class="text-sm text-gray-500">{{ $labels['wish'] }}</p>
            <div class="w-40 text-center text-sm font-bold border-t border-dashed pt-2">{{ $labels['signature'] }}</div>
        </footer>
    </div>
</div>

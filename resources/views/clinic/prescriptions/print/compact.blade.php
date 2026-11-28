<div class="p-8 min-h-[297mm] flex flex-col text-sm">
    <header class="flex justify-between border-b border-gray-800 pb-3 mb-4">
        <div>
            <p class="font-black text-base">{{ $clinic->name }}</p>
            <p class="font-bold">{{ $doctorName }}</p>
        </div>
        <div class="text-xs text-gray-600 text-end">
            @if($clinic->phone)<p class="dir-ltr">{{ $clinic->phone }}</p>@endif
            <p>{{ optional($prescription->created_at)->format('Y/m/d H:i') }}</p>
        </div>
    </header>

    <p class="mb-3 font-bold">{{ $labels['patient'] }}: {{ $prescription->patient->name ?? '—' }}
        @if($patientAge !== null) | {{ $labels['age'] }}: {{ $patientAge }}@endif
    </p>

    <div class="flex-1">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-300 text-gray-500">
                    <th class="py-1 text-start w-8">#</th>
                    <th class="py-1 text-start">{{ $labels['medications'] }}</th>
                    <th class="py-1 text-start">{{ $labels['dosage'] }}</th>
                    <th class="py-1 text-start">{{ $labels['instructions'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $index => $item)
                    <tr class="border-b border-gray-100 align-top">
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2 font-bold">{{ $item->medicine_name }}</td>
                        <td class="py-2">{{ $item->dosage }}</td>
                        <td class="py-2">{{ $item->instructions }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($prescription->notes)
            <p class="mt-3 text-xs"><span class="font-bold">{{ $labels['notes'] }}:</span> {{ $prescription->notes }}</p>
        @endif
    </div>

    <footer class="mt-6 flex justify-between text-xs text-gray-500">
        <span>{{ $labels['wish'] }}</span>
        <span>{{ $labels['signature'] }} ____________</span>
    </footer>
</div>

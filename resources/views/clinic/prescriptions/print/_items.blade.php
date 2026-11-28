@if($prescription->items->isNotEmpty())
    <div class="{{ $listClass ?? 'space-y-5' }}" dir="{{ $itemsDir ?? ($locale === 'ar' ? 'rtl' : 'ltr') }}">
        @foreach($prescription->items as $index => $item)
            <div class="rx-item {{ $itemClass ?? 'border-b border-gray-200 pb-3' }}">
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-gray-500">{{ $index + 1 }}.</span>
                    <div class="flex-1">
                        <h3 class="{{ $titleClass ?? 'text-xl font-black text-gray-900' }}">{{ $item->medicine_name }}</h3>
                        @if($item->dosage || $item->instructions)
                            <p class="mt-1 text-sm font-semibold text-gray-700">
                                @if($item->dosage)<span>{{ $item->dosage }}</span>@endif
                                @if($item->dosage && $item->instructions)<span> — </span>@endif
                                @if($item->instructions)<span class="font-medium text-gray-600">{{ $item->instructions }}</span>@endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if($prescription->notes)
    <div class="mt-6 pt-4 border-t border-dashed border-gray-300">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $labels['notes'] }}</h4>
        <div class="leading-relaxed whitespace-pre-wrap">{!! nl2br(e($prescription->notes)) !!}</div>
    </div>
@endif

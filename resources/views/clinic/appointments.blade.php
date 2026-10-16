<x-layouts.clinic>
    <x-slot:title>المواعيد والدور</x-slot>

    @php
        $statusMap = [
            'pending'   => ['label' => 'بانتظار التأكيد', 'class' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'],
            'confirmed' => ['label' => 'مؤكد',           'class' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'],
            'completed' => ['label' => 'مكتمل',          'class' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'],
            'rejected'  => ['label' => 'مرفوض',          'class' => 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400'],
            'cancelled' => ['label' => 'ملغي',           'class' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'],
        ];
        $queueStatusMap = [
            'waiting'     => ['label' => 'انتظار', 'class' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400'],
            'called'      => ['label' => 'تم النداء', 'class' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400'],
            'in_progress' => ['label' => 'قيد الكشف', 'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'],
            'completed'   => ['label' => 'مكتمل', 'class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'],
            'skipped'     => ['label' => 'تخطّي', 'class' => 'bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-300'],
        ];
        $dateStr = $selectedDate->toDateString();
    @endphp

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->has('queue'))
        <div class="px-4 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 rounded-xl text-sm font-semibold text-rose-700">{{ $errors->first('queue') }}</div>
    @endif

    {{-- Date navigation --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('clinic.appointments.index', ['date' => $prevDate, 'doctor_id' => $doctorId]) }}"
               class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-bold hover:bg-gray-50 dark:hover:bg-white/5">اليوم السابق</a>
            <form method="GET" action="{{ route('clinic.appointments.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
                <input type="date" name="date" value="{{ $dateStr }}"
                       class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm dark:text-white"
                       onchange="this.form.submit()">
            </form>
            <a href="{{ route('clinic.appointments.index', ['date' => $nextDate, 'doctor_id' => $doctorId]) }}"
               class="px-3 py-2 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-bold hover:bg-gray-50 dark:hover:bg-white/5">اليوم التالي</a>
            <a href="{{ route('clinic.appointments.index', ['date' => now()->timezone(config('clinora.clinic_timezone'))->toDateString(), 'doctor_id' => $doctorId]) }}"
               class="px-3 py-2 rounded-xl bg-brand-50 text-brand-700 text-sm font-bold">اليوم</a>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('clinic.appointments.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="date" value="{{ $dateStr }}">
                <select name="doctor_id" onchange="this.form.submit()"
                        class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-bold dark:text-white">
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" @selected($doctorId == $doc->id)>{{ $doc->name }}</option>
                    @endforeach
                </select>
            </form>
            <button data-modal-open="modal-add-appointment"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl">
                إضافة موعد
            </button>
        </div>
    </div>

    {{-- Queue summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'إجمالي اليوم', 'value' => $queueSummary['total']],
            ['label' => 'انتظار', 'value' => $queueSummary['waiting']],
            ['label' => 'تم النداء', 'value' => $queueSummary['called']],
            ['label' => 'قيد الكشف', 'value' => $queueSummary['in_progress']],
            ['label' => 'مكتمل', 'value' => $queueSummary['completed']],
            ['label' => 'التالي', 'value' => $queueSummary['next']?->queue_number ? '#'.$queueSummary['next']->queue_number : '—'],
        ] as $card)
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500 mb-1">{{ $card['label'] }}</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="space-y-4">
        <div class="space-y-4">
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-lg">طابور اليوم</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $selectedDate->translatedFormat('l d F Y') }}
                            @if($queueSummary['is_paused'])
                                <span class="text-amber-600 font-bold">· المناداة متوقفة</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('clinic.appointments.call-next') }}" data-queue-voice-form>
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
                            <input type="hidden" name="date" value="{{ $dateStr }}">
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold text-white bg-violet-600 hover:bg-violet-500">مناداة التالي</button>
                        </form>
                        <form method="POST" action="{{ route('clinic.appointments.queue-pause') }}">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
                            <input type="hidden" name="date" value="{{ $dateStr }}">
                            <input type="hidden" name="paused" value="{{ $queueSummary['is_paused'] ? 0 : 1 }}">
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold border border-gray-200 dark:border-white/10">
                                {{ $queueSummary['is_paused'] ? 'استئناف المناداة' : 'إيقاف مؤقت' }}
                            </button>
                        </form>
                    </div>
                </div>

                @if($dayQueue->isEmpty())
                    <div class="py-16 text-center text-sm text-gray-400">لا مواعيد في طابور هذا اليوم لهذا الطبيب.</div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                        @foreach($dayQueue as $apt)
                            @php
                                $qStatus = $queueStatusMap[$apt->queue_status] ?? ['label' => $apt->queue_status ?? '—', 'class' => 'bg-gray-100 text-gray-600'];
                                $bStatus = $statusMap[$apt->status] ?? ['label' => $apt->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300 flex items-center justify-center font-black text-lg shrink-0">
                                        {{ $apt->queue_number ?? '—' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white truncate">{{ $apt->patient?->name ?? 'مريض' }}</p>
                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-bold {{ $qStatus['class'] }}">{{ $qStatus['label'] }}</span>
                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-bold {{ $bStatus['class'] }}">{{ $bStatus['label'] }}</span>
                                            @if($apt->visitType)
                                                <span class="text-[11px] text-gray-400">{{ $apt->visitType->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @if(! in_array($apt->queue_status, ['completed'], true))
                                        <button type="button"
                                                data-announce-patient
                                                data-call-url="{{ route('clinic.appointments.queue-action', $apt) }}"
                                                data-patient-name="{{ $apt->patient?->name }}"
                                                data-queue-number="{{ $apt->queue_number }}"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-violet-700 bg-violet-50 hover:bg-violet-100">
                                            {{ $apt->queue_status === 'skipped' ? 'إعادة المناداة' : 'مناداة المريض' }}
                                        </button>
                                        @if($apt->queue_status !== 'skipped')
                                            <form method="POST" action="{{ route('clinic.appointments.queue-action', $apt) }}">
                                                @csrf
                                                <input type="hidden" name="action" value="complete">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-700 hover:bg-emerald-50">إكمال</button>
                                            </form>
                                            <form method="POST" action="{{ route('clinic.appointments.queue-action', $apt) }}">
                                                @csrf
                                                <input type="hidden" name="action" value="skip">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100">تخطّي</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}"
                                              onsubmit="return confirm('هل تريد إلغاء هذا الموعد نهائيًا؟');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100">إلغاء الموعد</button>
                                        </form>
                                    @endif
                                    @if($apt->status === 'pending')
                                        <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-blue-600 hover:bg-blue-50">تأكيد الحجز</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add Appointment Modal --}}
    <div id="modal-add-appointment" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة موعد يدوي</h3>
                    <p class="text-xs text-gray-400 mt-0.5">يُمنح رقم دور تلقائيًا</p>
                </div>
                <button data-modal-close="modal-add-appointment" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.appointments.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any() && !$errors->has('queue'))
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-700">• {{ $error }}</p>@endforeach
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-bold mb-1.5">المريض *</label>
                        <select name="patient_id" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">اختر مريضاً</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" @selected(old('patient_id') == $p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">تاريخ الموعد *</label>
                        <input type="date" name="scheduled_at" value="{{ old('scheduled_at', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">الخدمة *</label>
                        <select name="visit_type_id" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">اختر الخدمة</option>
                            @foreach($visitTypes as $vt)
                                <option value="{{ $vt->id }}" @selected(old('visit_type_id') == $vt->id)>{{ $vt->name }} — {{ number_format($vt->price, 0) }} ج.م</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-appointment" class="px-4 py-2 text-sm font-bold text-gray-500 rounded-xl">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl">حفظ الموعد</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        @if($errors->any() && !$errors->has('queue'))
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-appointment');
        });
        @endif
    </script>
    @endpush
</x-layouts.clinic>

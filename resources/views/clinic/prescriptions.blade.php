<x-layouts.clinic>
    <x-slot:title>الوصفات الطبية</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700/30 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">الوصفات الطبية (الروشتات)</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $prescriptions->total() }} وصفة محفوظة</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input id="prescriptions-live-search" type="text" placeholder="🔍 ابحث بالاسم، الدواء، أو الملاحظات..." value="{{ request('search') }}"
                           class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-9 pr-4 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg id="pres-search-spinner" style="display: none;" class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <button data-modal-open="modal-add-prescription"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    كتابة وصفة
                </button>
            </div>
        </div>
        <div id="prescriptions-list-wrapper">

        @if($prescriptions->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد وصفات طبية</h4>
                <p class="text-sm text-gray-400 max-w-xs mb-5">قم بكتابة وحفظ الوصفات الطبية لمرضاك للرجوع إليها مستقبلاً.</p>
                @if($patients->isNotEmpty())
                    <button data-modal-open="modal-add-prescription" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">كتابة وصفة الآن</button>
                @else
                    <p class="text-xs text-amber-600 bg-amber-50 px-4 py-2 rounded-xl dark:bg-amber-900/20 dark:text-amber-400">أضف مرضى أولاً لكي تتمكن من كتابة وصفات طبية لهم.</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($prescriptions as $pres)
                    <div class="relative bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl p-5 hover:border-brand-500/30 transition-all group">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-xs shrink-0">
                                    {{ mb_substr($pres->patient->name ?? '؟', 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="font-bold text-sm text-gray-900 dark:text-white">{{ $pres->patient->name ?? 'مريض محذوف' }}</h5>
                                    <p class="text-xs text-gray-400">{{ $pres->created_at->format('Y/m/d h:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('clinic.prescriptions.print', $pres) }}" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="طباعة">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <form method="POST" action="{{ route('clinic.prescriptions.destroy', $pres) }}" data-confirm="هل تريد حذف هذه الوصفة؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/5 rounded-xl p-3 text-sm text-gray-700 dark:text-gray-300 max-h-40 overflow-y-auto">
                            @if($pres->items->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach($pres->items as $item)
                                        <div class="flex items-start gap-2 border-b border-gray-50 dark:border-white/[0.04] pb-1.5 last:border-0 last:pb-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mt-1.5 shrink-0"></span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-900 dark:text-white text-xs">{{ $item->medicine_name }}</div>
                                                @if($item->dosage || $item->instructions)
                                                    <div class="text-[11px] text-gray-400 mt-0.5 flex flex-wrap gap-x-2">
                                                        @if($item->dosage)<span class="text-brand-600 dark:text-brand-400 font-semibold">{{ $item->dosage }}</span>@endif
                                                        @if($item->instructions)<span>— {{ $item->instructions }}</span>@endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($pres->notes)
                                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-white/5 text-xs text-gray-500 whitespace-pre-wrap font-mono text-left dir-ltr">{{ $pres->notes }}</div>
                                @endif
                            @else
                                <div class="whitespace-pre-wrap font-mono text-left dir-ltr">{{ $pres->notes }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($prescriptions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $prescriptions->links() }}</div>
            @endif
        @endif
        </div>
    </div>

    {{-- Add Prescription Modal --}}
    <div id="modal-add-prescription" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-3xl rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">وصفة طبية جديدة (روشتة ذكية)</h3>
                    <p class="text-xs text-gray-400 mt-0.5">ابحث في قاعدة بيانات الأدوية وأضف الجرعات والتعليمات بدقة</p>
                </div>
                <button data-modal-close="modal-add-prescription" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.prescriptions.store') }}" class="flex-1 overflow-y-auto flex flex-col">
                @csrf
                <div class="p-6 space-y-6 flex-1 overflow-y-auto">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    {{-- Patient Selection (Searchable Autocomplete) --}}
                    <div class="relative" id="patient-select-container">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">المريض <span class="text-red-500">*</span></label>
                        <input type="hidden" name="patient_id" id="hidden-patient-id" required value="{{ old('patient_id') }}">
                        <div class="relative">
                            <input type="text" id="patient-search-input" placeholder="🔍 ابحث واقتر المريض (الاسم، رقم الهاتف)..." autocomplete="off"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                            <button type="button" id="btn-clear-patient" style="display: none;" class="absolute left-3 top-2.5 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div id="patient-search-dropdown" style="display: none;"
                             class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1A1A2E] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
                        </div>
                    </div>

                    {{-- Section 1: Drug Search & Adder --}}
                    <div class="bg-gray-50/70 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 rounded-2xl p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                إضافة دواء للروشتة
                            </h4>
                            <span class="text-[11px] text-gray-400">ابحث أو اكتب الدواء يدوياً</span>
                        </div>

                        {{-- Search Input --}}
                        <div class="relative">
                            <div class="relative">
                                <input id="drug-search-input" type="text"
                                       placeholder="🔍 ابحث في قاعدة أدوية مصر (الاسم التجاري أو العلمي مثلاً: Panadol, Augmentin, 1 2 3)..."
                                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg id="drug-search-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <svg id="drug-spinner-icon" style="display: none;" class="w-5 h-5 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </div>

                            {{-- Autocomplete Dropdown --}}
                            <div id="drug-search-dropdown" style="display: none;"
                                 class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1A1A2E] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
                            </div>
                        </div>

                        {{-- Manual Inputs for Drug, Dosage, Instructions --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-gray-200/50 dark:border-white/5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">اسم الدواء / التركيبة <span class="text-red-500">*</span></label>
                                <input id="custom-medicine-name" type="text" placeholder="مثال: Panadol 500mg"
                                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">الجرعة (Dosage)</label>
                                <input id="custom-dosage" type="text" placeholder="مثال: قرص كل 8 ساعات"
                                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                {{-- Quick Chips --}}
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    <button type="button" data-val="قرص كل 8 ساعات" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">كل 8 ساعات</button>
                                    <button type="button" data-val="قرص كل 12 ساعة" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">كل 12 ساعة</button>
                                    <button type="button" data-val="قرص مرة يومياً" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">مرة يومياً</button>
                                    <button type="button" data-val="5 سم 3 مرات يومياً" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">5 سم 3 مرات</button>
                                    <button type="button" data-val="عند اللزوم" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">عند اللزوم</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">المدة / التعليمات</label>
                                <input id="custom-instructions" type="text" placeholder="مثال: لمدة 5 أيام بعد الأكل"
                                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                {{-- Quick Chips --}}
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    <button type="button" data-val="لمدة 3 أيام بعد الأكل" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">3 أيام بعد الأكل</button>
                                    <button type="button" data-val="لمدة 5 أيام" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">5 أيام</button>
                                    <button type="button" data-val="لمدة أسبوع" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">أسبوع</button>
                                    <button type="button" data-val="قبل الأكل" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">قبل الأكل</button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button id="btn-add-item" type="button" disabled
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                إضافة للقائمة
                            </button>
                        </div>
                    </div>

                    {{-- Section 2: Added Items List --}}
                    <div>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-2 flex items-center justify-between">
                            <span>الأدوية المضافة للروشتة (<span id="items-count">0</span>)</span>
                            <span id="items-print-note" style="display: none;" class="text-xs text-emerald-600 dark:text-emerald-400 font-normal">سيتم طباعتها في جدول مخصص بالروشتة</span>
                        </h4>

                        <div id="items-empty-state" class="bg-gray-50 dark:bg-white/[0.02] border border-dashed border-gray-200 dark:border-white/10 rounded-xl p-6 text-center text-gray-400 text-xs">
                            لم تقم بإضافة أدوية للروشتة بعد. ابحث وأضف الأدوية من المربع أعلاه، أو يمكنك كتابة ملاحظات عامة في المربع أدناه.
                        </div>

                        <div id="items-list-container" style="display: none;" class="space-y-2"></div>
                    </div>

                    {{-- Section 3: General Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات طبية أخرى / توصيات عامة</label>
                        <textarea id="prescription-notes" name="notes" rows="3" dir="ltr" required
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white font-mono text-left resize-none"
                                  placeholder="ملاحظات للمريض، قياس الضغط، أو توصيات غذائية...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3 shrink-0">
                    <button type="button" data-modal-close="modal-add-prescription" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">حفظ الوصفة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-prescription');
        });
    </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Live Search for Prescriptions List
            const presSearchInput = document.getElementById('prescriptions-live-search');
            const presSearchSpinner = document.getElementById('pres-search-spinner');
            const presWrapper = document.getElementById('prescriptions-list-wrapper');
            let presTimeout = null;

            if (presSearchInput && presWrapper) {
                presSearchInput.addEventListener('input', (e) => {
                    clearTimeout(presTimeout);
                    if (presSearchSpinner) presSearchSpinner.style.display = 'block';
                    
                    presTimeout = setTimeout(async () => {
                        try {
                            const url = `{{ route('clinic.prescriptions.index', [], false) }}?search=${encodeURIComponent(e.target.value.trim())}`;
                            const res = await fetch(url, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                credentials: 'same-origin'
                            });
                            if (res.ok) {
                                const html = await res.text();
                                const doc = new DOMParser().parseFromString(html, 'text/html');
                                const newWrapper = doc.getElementById('prescriptions-list-wrapper');
                                if (newWrapper) {
                                    presWrapper.innerHTML = newWrapper.innerHTML;
                                }
                            }
                        } catch (err) {
                            console.error('Live search error:', err);
                        } finally {
                            if (presSearchSpinner) presSearchSpinner.style.display = 'none';
                        }
                    }, 300);
                });
            }

            // 2. Searchable Patient Dropdown
            const allPatients = @json($patients);
            const patientSearchInput = document.getElementById('patient-search-input');
            const hiddenPatientId = document.getElementById('hidden-patient-id');
            const patientDropdown = document.getElementById('patient-search-dropdown');
            const btnClearPatient = document.getElementById('btn-clear-patient');

            if (patientSearchInput && hiddenPatientId && patientDropdown) {
                if (hiddenPatientId.value) {
                    const found = allPatients.find(p => p.id == hiddenPatientId.value);
                    if (found) {
                        patientSearchInput.value = found.name + (found.phone ? ` (${found.phone})` : '');
                        if (btnClearPatient) btnClearPatient.style.display = 'block';
                    }
                }

                function renderPatientDropdown(list) {
                    if (!list || list.length === 0) {
                        patientDropdown.innerHTML = `<div class="p-3 text-center text-xs text-gray-400">لا يوجد مريض مطابق للبحث</div>`;
                        patientDropdown.style.display = 'block';
                        return;
                    }
                    patientDropdown.innerHTML = '';
                    list.forEach(p => {
                        const item = document.createElement('div');
                        item.className = 'p-3 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-pointer transition-colors flex items-center justify-between gap-3 border-b border-gray-50 dark:border-white/5 last:border-0';
                        item.innerHTML = `
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-white">${p.name}</div>
                                <div class="text-xs text-gray-400 mt-0.5">${p.phone || 'بدون رقم هاتف'}</div>
                            </div>
                        `;
                        item.addEventListener('click', () => {
                            hiddenPatientId.value = p.id;
                            patientSearchInput.value = p.name + (p.phone ? ` (${p.phone})` : '');
                            patientDropdown.style.display = 'none';
                            if (btnClearPatient) btnClearPatient.style.display = 'block';
                        });
                        patientDropdown.appendChild(item);
                    });
                    patientDropdown.style.display = 'block';
                }

                patientSearchInput.addEventListener('input', (e) => {
                    const val = e.target.value.trim().toLowerCase();
                    if (!val) {
                        hiddenPatientId.value = '';
                        if (btnClearPatient) btnClearPatient.style.display = 'none';
                        renderPatientDropdown(allPatients);
                        return;
                    }
                    const filtered = allPatients.filter(p => {
                        return (p.name && p.name.toLowerCase().includes(val)) ||
                               (p.phone && p.phone.includes(val));
                    });
                    renderPatientDropdown(filtered);
                });

                patientSearchInput.addEventListener('focus', () => {
                    const val = patientSearchInput.value.trim().toLowerCase();
                    const filtered = !val ? allPatients : allPatients.filter(p => (p.name && p.name.toLowerCase().includes(val)) || (p.phone && p.phone.includes(val)));
                    renderPatientDropdown(filtered);
                });

                if (btnClearPatient) {
                    btnClearPatient.addEventListener('click', () => {
                        hiddenPatientId.value = '';
                        patientSearchInput.value = '';
                        btnClearPatient.style.display = 'none';
                        renderPatientDropdown(allPatients);
                        patientSearchInput.focus();
                    });
                }

                document.addEventListener('click', (e) => {
                    if (!patientSearchInput.contains(e.target) && !patientDropdown.contains(e.target) && (!btnClearPatient || !btnClearPatient.contains(e.target))) {
                        patientDropdown.style.display = 'none';
                    }
                });
            }

            let prescriptionItems = [];
            let searchTimeout = null;
            
            const searchInput = document.getElementById('drug-search-input');
            const searchIcon = document.getElementById('drug-search-icon');
            const spinnerIcon = document.getElementById('drug-spinner-icon');
            const dropdown = document.getElementById('drug-search-dropdown');
            
            const medNameInput = document.getElementById('custom-medicine-name');
            const dosageInput = document.getElementById('custom-dosage');
            const instInput = document.getElementById('custom-instructions');
            const addItemBtn = document.getElementById('btn-add-item');
            
            const itemsCountSpan = document.getElementById('items-count');
            const itemsPrintNote = document.getElementById('items-print-note');
            const emptyStateDiv = document.getElementById('items-empty-state');
            const itemsListDiv = document.getElementById('items-list-container');
            const notesTextarea = document.getElementById('prescription-notes');

            if (!searchInput || !medNameInput) return;

            // Quick chips
            document.querySelectorAll('.quick-dosage-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    dosageInput.value = btn.getAttribute('data-val');
                });
            });
            document.querySelectorAll('.quick-inst-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    instInput.value = btn.getAttribute('data-val');
                });
            });

            // Debounced Search
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                    return;
                }
                
                searchIcon.style.display = 'none';
                spinnerIcon.style.display = 'block';
                
                searchTimeout = setTimeout(async () => {
                    try {
                        const url = `{{ route('clinic.drugs.search', [], false) }}?q=${encodeURIComponent(query)}`;
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (res.ok) {
                            const drugs = await res.json();
                            renderDropdown(drugs);
                        }
                    } catch (err) {
                        console.error('Search error:', err);
                    } finally {
                        spinnerIcon.style.display = 'none';
                        searchIcon.style.display = 'block';
                    }
                }, 300);
            });

            function renderDropdown(drugs) {
                if (!drugs || drugs.length === 0) {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                    return;
                }
                
                dropdown.innerHTML = '';
                drugs.forEach(drug => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-pointer transition-colors flex items-center justify-between gap-3';
                    
                    let displayName = drug.commercial_name_en || '';
                    let subName = drug.commercial_name_ar || drug.scientific_name || '';
                    let routeBadge = drug.route ? `<span class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded text-[10px] font-semibold">${drug.route}</span>` : '';
                    let priceText = drug.price_egp ? `<div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">${drug.price_egp} ج.م</div>` : '';
                    
                    item.innerHTML = `
                        <div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">${displayName}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${subName}</div>
                        </div>
                        <div class="text-left shrink-0">
                            ${routeBadge}
                            ${priceText}
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        let nameToSet = drug.commercial_name_en;
                        if (drug.commercial_name_ar) {
                            nameToSet += ` (${drug.commercial_name_ar})`;
                        }
                        medNameInput.value = nameToSet;
                        dropdown.style.display = 'none';
                        searchInput.value = '';
                        checkAddBtnState();
                    });
                    
                    dropdown.appendChild(item);
                });
                
                dropdown.style.display = 'block';
            }

            // Close dropdown on outside click
            document.addEventListener('click', (e) => {
                if (searchInput && !searchInput.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            searchInput.addEventListener('focus', () => {
                if (dropdown.children.length > 0) {
                    dropdown.style.display = 'block';
                }
            });

            // Check add button state
            function checkAddBtnState() {
                if (medNameInput.value.trim().length > 0) {
                    addItemBtn.disabled = false;
                    addItemBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    addItemBtn.disabled = true;
                    addItemBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
            medNameInput.addEventListener('input', checkAddBtnState);

            // Add item
            addItemBtn.addEventListener('click', () => {
                const name = medNameInput.value.trim();
                if (!name) return;
                
                const dosage = dosageInput.value.trim();
                const instructions = instInput.value.trim();
                
                prescriptionItems.push({
                    id: Date.now() + Math.random(),
                    name,
                    dosage,
                    instructions
                });
                
                medNameInput.value = '';
                dosageInput.value = '';
                instInput.value = '';
                checkAddBtnState();
                renderItems();
            });

            function renderItems() {
                itemsCountSpan.textContent = prescriptionItems.length;
                
                if (prescriptionItems.length === 0) {
                    emptyStateDiv.style.display = 'block';
                    itemsListDiv.style.display = 'none';
                    itemsPrintNote.style.display = 'none';
                    itemsListDiv.innerHTML = '';
                    if (notesTextarea) notesTextarea.required = true;
                } else {
                    emptyStateDiv.style.display = 'none';
                    itemsListDiv.style.display = 'block';
                    itemsPrintNote.style.display = 'inline';
                    itemsListDiv.innerHTML = '';
                    if (notesTextarea) notesTextarea.required = false;
                    
                    prescriptionItems.forEach((item, idx) => {
                        const el = document.createElement('div');
                        el.className = 'bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 shadow-sm';
                        
                        let dosageChip = item.dosage ? `<span class="px-2 py-0.5 bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300 rounded-md font-semibold">💊 الجرعة: ${item.dosage}</span>` : '';
                        let instChip = item.instructions ? `<span class="px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded-md">🕒 التعليمات: ${item.instructions}</span>` : '';
                        
                        el.innerHTML = `
                            <input type="hidden" name="items[${idx}][medicine_name]" value="${item.name}">
                            <input type="hidden" name="items[${idx}][dosage]" value="${item.dosage}">
                            <input type="hidden" name="items[${idx}][instructions]" value="${item.instructions}">
                            
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">${idx + 1}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white">${item.name}</div>
                                    <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                        ${dosageChip}
                                        ${instChip}
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn-remove-item p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="حذف الدواء">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        `;
                        
                        el.querySelector('.btn-remove-item').addEventListener('click', () => {
                            prescriptionItems.splice(idx, 1);
                            renderItems();
                        });
                        
                        itemsListDiv.appendChild(el);
                    });
                }
            }

            renderItems();
            checkAddBtnState();
        });
    </script>
    @endpush
</x-layouts.clinic>

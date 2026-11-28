<x-layouts.clinic>
    <x-slot:title>الوصفات الطبية</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700/30 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded-xl border border-brand-200 bg-brand-50/80 dark:bg-brand-900/20 dark:border-brand-800/40 px-4 py-3 text-sm text-brand-800 dark:text-brand-200">
        إنشاء وصفة لمريض يتم من داخل <strong>الزيارة الطبية</strong>. هذه الصفحة لاستعراض الوصفات وإدارة قوالب الأدوية.
    </div>

    {{-- Prescriptions list --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">الوصفات الطبية (الروشتات)</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $prescriptions->total() }} وصفة محفوظة</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input id="prescriptions-live-search" type="text" placeholder="ابحث بالاسم، الدواء، أو الملاحظات..." value="{{ request('search') }}"
                           class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg id="pres-search-spinner" style="display: none;" class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div id="prescriptions-list-wrapper">
            @if($prescriptions->isEmpty())
                <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                    <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد وصفات طبية</h4>
                    <p class="text-sm text-gray-400 max-w-xs">أنشئ الوصفة من داخل زيارة المريض في الملف الطبي.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    @foreach($prescriptions as $pres)
                        <div class="relative bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl p-5 hover:border-brand-500/30 transition-all group">
                            <div class="flex items-start justify-between mb-3">
                                @if($pres->patient)
                                    <a href="{{ route('clinic.patients.medical-file', $pres->patient) }}" class="flex items-center gap-2.5 group/link">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-xs shrink-0 group-hover/link:scale-105 transition-transform">
                                            {{ mb_substr($pres->patient->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-sm text-gray-900 dark:text-white group-hover/link:text-brand-600 transition-colors">{{ $pres->patient->name }}</h5>
                                            <p class="text-xs text-gray-400">{{ $pres->created_at->format('Y/m/d h:i A') }}</p>
                                        </div>
                                    </a>
                                @else
                                    <div>
                                        <h5 class="font-bold text-sm">مريض محذوف</h5>
                                        <p class="text-xs text-gray-400">{{ $pres->created_at->format('Y/m/d h:i A') }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('clinic.prescriptions.print', $pres) }}" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="طباعة">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <form method="POST" action="{{ route('clinic.prescriptions.destroy', $pres) }}" data-confirm="هل تريد حذف هذه الوصفة؟">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($pres->visit && $pres->patient)
                                <a href="{{ route('clinic.visits.show', [$pres->patient, $pres->visit]) }}" class="inline-flex mb-3 text-[11px] font-bold px-2 py-1 rounded-lg bg-brand-50 text-brand-700 border border-brand-100 dark:bg-brand-900/20 dark:text-brand-300 dark:border-brand-800/40">
                                    زيارة {{ $pres->visit->visited_at?->format('Y/m/d H:i') ?? ('#'.$pres->visit_id) }}
                                </a>
                            @elseif(!$pres->visit_id)
                                <span class="inline-flex mb-3 text-[11px] font-bold px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">بدون زيارة (سجل قديم)</span>
                            @endif

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
                                        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-white/5 text-xs text-gray-500 whitespace-pre-wrap">{{ $pres->notes }}</div>
                                    @endif
                                @else
                                    <div class="whitespace-pre-wrap text-xs text-gray-500">{{ $pres->notes }}</div>
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

    {{-- Templates section --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">قوالب الأدوية</h3>
                <p class="text-sm text-gray-400 mt-0.5">قوالب مشتركة على مستوى العيادة — تُستخدم داخل الزيارة</p>
            </div>
            <button type="button" id="btn-open-template-modal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة قالب
            </button>
        </div>

        @if(($templates ?? collect())->isEmpty())
            <div class="py-14 text-center px-6">
                <p class="text-sm text-gray-400 mb-4">لا توجد قوالب بعد. أنشئ قالبًا بنفس طريقة إضافة أدوية الروشتة.</p>
                <button type="button" id="btn-open-template-modal-empty" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl">إنشاء أول قالب</button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($templates as $template)
                    <div class="border border-gray-100 dark:border-white/10 rounded-2xl p-5 bg-gray-50 dark:bg-white/5 hover:border-brand-500/30 transition-all group">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <p class="font-black text-sm text-gray-900 dark:text-white">{{ $template->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $template->items->count() }} أدوية</p>
                            </div>
                            <div class="flex items-center gap-1">
                                @php
                                    $templateEditPayload = [
                                        'id' => $template->id,
                                        'name' => $template->name,
                                        'items' => $template->items->map(static function ($item): array {
                                            return [
                                                'medicine_name' => $item->medicine_name,
                                                'dosage' => $item->dosage,
                                                'instructions' => $item->instructions,
                                            ];
                                        })->values()->all(),
                                    ];
                                @endphp
                                <button type="button"
                                        class="btn-edit-template p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20"
                                        title="تعديل"
                                        data-template="{{ e(json_encode($templateEditPayload, JSON_UNESCAPED_UNICODE)) }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form method="POST" action="{{ route('clinic.prescription-templates.destroy', $template) }}" data-confirm="حذف هذا القالب؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach($template->items as $item)
                                <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/5 rounded-xl px-3 py-2">
                                    <p class="font-bold text-xs text-gray-900 dark:text-white">{{ $item->medicine_name }}</p>
                                    @if($item->dosage || $item->instructions)
                                        <p class="text-[11px] text-gray-400 mt-0.5">
                                            @if($item->dosage)<span class="text-brand-600 font-semibold">{{ $item->dosage }}</span>@endif
                                            @if($item->instructions)<span> — {{ $item->instructions }}</span>@endif
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Template create/edit modal (same drug UX as prescription) --}}
    <div id="modal-medication-template" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-3xl rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center shrink-0">
                <div>
                    <h3 id="template-modal-title" class="font-black text-lg text-gray-900 dark:text-white">قالب أدوية جديد</h3>
                    <p class="text-xs text-gray-400 mt-0.5">ابحث عن الدواء بالاسم الإنجليزي وأضف الجرعة والتعليمات كما في الروشتة</p>
                </div>
                <button type="button" data-modal-close="modal-medication-template" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                <input type="hidden" id="template-edit-id" value="">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">اسم القالب <span class="text-red-500">*</span></label>
                    <input id="template-name-input" type="text" required placeholder="مثال: قالب ضغط الدم"
                           class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                </div>

                <div class="bg-gray-50/70 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            إضافة دواء للقالب
                        </h4>
                        <span class="text-[11px] text-gray-400">الأسماء الإنجليزية من قاعدة الأدوية</span>
                    </div>

                    <div class="relative">
                        <input id="tpl-drug-search-input" type="text" autocomplete="off"
                               placeholder="ابحث في قاعدة الأدوية (Panadol, Augmentin...)"
                               class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                        <div class="absolute left-3 top-2.5 text-gray-400 pointer-events-none">
                            <svg id="tpl-drug-search-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg id="tpl-drug-spinner-icon" style="display:none;" class="w-5 h-5 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div id="tpl-drug-search-dropdown" style="display:none;" class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1A1A2E] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-gray-200/50 dark:border-white/5">
                        <div>
                            <label class="block text-xs font-bold mb-1">اسم الدواء (English) *</label>
                            <input id="tpl-medicine-name" type="text" dir="ltr" placeholder="Augmentin 1g" class="w-full bg-white dark:bg-[#0F0F1A] border rounded-xl px-3 py-2 text-sm dark:border-white/10">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">الجرعة</label>
                            <input id="tpl-dosage" type="text" placeholder="قرص كل 12 ساعة" class="w-full bg-white dark:bg-[#0F0F1A] border rounded-xl px-3 py-2 text-sm dark:border-white/10">
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                <button type="button" data-val="قرص كل 8 ساعات" class="tpl-quick-dosage text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">كل 8 ساعات</button>
                                <button type="button" data-val="قرص كل 12 ساعة" class="tpl-quick-dosage text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">كل 12 ساعة</button>
                                <button type="button" data-val="قرص مرة يومياً" class="tpl-quick-dosage text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">مرة يومياً</button>
                                <button type="button" data-val="عند اللزوم" class="tpl-quick-dosage text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">عند اللزوم</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">المدة / التعليمات</label>
                            <input id="tpl-instructions" type="text" placeholder="لمدة 7 أيام بعد الأكل" class="w-full bg-white dark:bg-[#0F0F1A] border rounded-xl px-3 py-2 text-sm dark:border-white/10">
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                <button type="button" data-val="لمدة 5 أيام" class="tpl-quick-inst text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">5 أيام</button>
                                <button type="button" data-val="لمدة أسبوع" class="tpl-quick-inst text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">أسبوع</button>
                                <button type="button" data-val="بعد الأكل" class="tpl-quick-inst text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">بعد الأكل</button>
                                <button type="button" data-val="قبل الأكل" class="tpl-quick-inst text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md">قبل الأكل</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" id="tpl-btn-add-item" disabled
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl">
                            إضافة للقائمة
                        </button>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-sm mb-2">أدوية القالب (<span id="tpl-items-count">0</span>)</h4>
                    <div id="tpl-items-empty" class="bg-gray-50 dark:bg-white/[0.02] border border-dashed border-gray-200 dark:border-white/10 rounded-xl p-6 text-center text-gray-400 text-xs">
                        لم تُضف أدوية بعد. ابحث وأضف من المربع أعلاه.
                    </div>
                    <div id="tpl-items-list" style="display:none;" class="space-y-2"></div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3 shrink-0">
                <button type="button" data-modal-close="modal-medication-template" class="px-4 py-2 text-sm font-bold text-gray-500 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5">إلغاء</button>
                <button type="button" id="tpl-btn-save" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl">حفظ القالب</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Live search
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
                            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                            if (res.ok) {
                                const html = await res.text();
                                const doc = new DOMParser().parseFromString(html, 'text/html');
                                const newWrapper = doc.getElementById('prescriptions-list-wrapper');
                                if (newWrapper) presWrapper.innerHTML = newWrapper.innerHTML;
                            }
                        } finally {
                            if (presSearchSpinner) presSearchSpinner.style.display = 'none';
                        }
                    }, 300);
                });
            }

            // Template composer state
            let tplItems = [];
            const nameInput = document.getElementById('template-name-input');
            const editIdInput = document.getElementById('template-edit-id');
            const medInput = document.getElementById('tpl-medicine-name');
            const dosageInput = document.getElementById('tpl-dosage');
            const instInput = document.getElementById('tpl-instructions');
            const addBtn = document.getElementById('tpl-btn-add-item');
            const listDiv = document.getElementById('tpl-items-list');
            const emptyDiv = document.getElementById('tpl-items-empty');
            const countSpan = document.getElementById('tpl-items-count');
            const modalTitle = document.getElementById('template-modal-title');
            const drugSearch = document.getElementById('tpl-drug-search-input');
            const drugDropdown = document.getElementById('tpl-drug-search-dropdown');
            const drugIcon = document.getElementById('tpl-drug-search-icon');
            const drugSpinner = document.getElementById('tpl-drug-spinner-icon');
            let drugTimeout = null;

            function openTemplateModal(template = null) {
                tplItems = [];
                editIdInput.value = '';
                nameInput.value = '';
                medInput.value = '';
                dosageInput.value = '';
                instInput.value = '';
                if (template) {
                    editIdInput.value = template.id;
                    nameInput.value = template.name || '';
                    modalTitle.textContent = 'تعديل قالب الأدوية';
                    (template.items || []).forEach((item) => {
                        tplItems.push({
                            id: Date.now() + Math.random(),
                            name: item.medicine_name,
                            dosage: item.dosage || '',
                            instructions: item.instructions || '',
                        });
                    });
                } else {
                    modalTitle.textContent = 'قالب أدوية جديد';
                }
                renderTplItems();
                checkAddBtn();
                if (typeof CModal !== 'undefined') CModal.open('modal-medication-template');
            }

            document.getElementById('btn-open-template-modal')?.addEventListener('click', () => openTemplateModal());
            document.getElementById('btn-open-template-modal-empty')?.addEventListener('click', () => openTemplateModal());
            document.querySelectorAll('.btn-edit-template').forEach((btn) => {
                btn.addEventListener('click', () => {
                    try {
                        openTemplateModal(JSON.parse(btn.getAttribute('data-template')));
                    } catch (e) {
                        console.error(e);
                    }
                });
            });

            function checkAddBtn() {
                addBtn.disabled = !(medInput.value || '').trim();
            }
            medInput?.addEventListener('input', checkAddBtn);

            function renderTplItems() {
                countSpan.textContent = String(tplItems.length);
                if (!tplItems.length) {
                    emptyDiv.style.display = 'block';
                    listDiv.style.display = 'none';
                    listDiv.innerHTML = '';
                    return;
                }
                emptyDiv.style.display = 'none';
                listDiv.style.display = 'block';
                listDiv.innerHTML = '';
                tplItems.forEach((item, idx) => {
                    const el = document.createElement('div');
                    el.className = 'bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 shadow-sm';
                    const dosageChip = item.dosage ? `<span class="px-2 py-0.5 bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300 rounded-md font-semibold">الجرعة: ${item.dosage}</span>` : '';
                    const instChip = item.instructions ? `<span class="px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded-md">${item.instructions}</span>` : '';
                    el.innerHTML = `
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">${idx + 1}</div>
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-gray-900 dark:text-white">${item.name}</div>
                                <div class="flex flex-wrap gap-2 mt-1 text-xs">${dosageChip}${instChip}</div>
                            </div>
                        </div>
                        <button type="button" class="tpl-remove p-1.5 text-gray-400 hover:text-red-500 rounded-lg" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    `;
                    el.querySelector('.tpl-remove').addEventListener('click', () => {
                        tplItems.splice(idx, 1);
                        renderTplItems();
                    });
                    listDiv.appendChild(el);
                });
            }

            addBtn?.addEventListener('click', () => {
                const name = (medInput.value || '').trim();
                if (!name) return;
                tplItems.push({
                    id: Date.now() + Math.random(),
                    name,
                    dosage: (dosageInput.value || '').trim(),
                    instructions: (instInput.value || '').trim(),
                });
                medInput.value = '';
                dosageInput.value = '';
                instInput.value = '';
                checkAddBtn();
                renderTplItems();
            });

            document.querySelectorAll('.tpl-quick-dosage').forEach((btn) => {
                btn.addEventListener('click', () => { dosageInput.value = btn.dataset.val; });
            });
            document.querySelectorAll('.tpl-quick-inst').forEach((btn) => {
                btn.addEventListener('click', () => { instInput.value = btn.dataset.val; });
            });

            drugSearch?.addEventListener('input', (e) => {
                clearTimeout(drugTimeout);
                const q = e.target.value.trim();
                if (q.length < 2) {
                    drugDropdown.style.display = 'none';
                    return;
                }
                drugIcon.style.display = 'none';
                drugSpinner.style.display = 'block';
                drugTimeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`{{ route('clinic.drugs.search') }}?q=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) return;
                        const drugs = await res.json();
                        if (!drugs.length) {
                            drugDropdown.innerHTML = '<div class="p-3 text-center text-xs text-gray-400">لا نتائج</div>';
                            drugDropdown.style.display = 'block';
                            return;
                        }
                        drugDropdown.innerHTML = '';
                        drugs.forEach((drug) => {
                            const nameEn = drug.commercial_name_en || drug.scientific_name || '';
                            const row = document.createElement('button');
                            row.type = 'button';
                            row.className = 'w-full text-left p-3 hover:bg-brand-50 dark:hover:bg-brand-900/20 border-b last:border-0';
                            row.innerHTML = `<div class="font-bold text-sm text-gray-900 dark:text-white">${nameEn}</div><div class="text-xs text-gray-400">${drug.commercial_name_ar || drug.scientific_name || ''}</div>`;
                            row.addEventListener('click', () => {
                                medInput.value = nameEn;
                                drugSearch.value = '';
                                drugDropdown.style.display = 'none';
                                checkAddBtn();
                            });
                            drugDropdown.appendChild(row);
                        });
                        drugDropdown.style.display = 'block';
                    } finally {
                        drugIcon.style.display = 'block';
                        drugSpinner.style.display = 'none';
                    }
                }, 250);
            });

            document.getElementById('tpl-btn-save')?.addEventListener('click', async () => {
                const name = (nameInput.value || '').trim();
                if (!name) {
                    alert('أدخل اسم القالب.');
                    return;
                }
                if (!tplItems.length) {
                    alert('أضف دواء واحدًا على الأقل.');
                    return;
                }
                const editId = editIdInput.value;
                const url = editId
                    ? @json(url('/clinic/prescription-templates')) + '/' + editId
                    : @json(route('clinic.prescription-templates.store'));
                const response = await fetch(url, {
                    method: editId ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        name,
                        items: tplItems.map((item) => ({
                            medicine_name: item.name,
                            dosage: item.dosage || null,
                            instructions: item.instructions || null,
                        })),
                    }),
                });
                if (!response.ok) {
                    alert('تعذر حفظ القالب.');
                    return;
                }
                window.location.reload();
            });
        });
    </script>
    @endpush
</x-layouts.clinic>

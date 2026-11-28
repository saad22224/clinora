<x-layouts.clinic>
    <x-slot:title>زيارة طبية — {{ $patient->name }}</x-slot>

    @php
        $composerOpen = $errors->any() && (string) old('visit_id') === (string) $visit->id;
    @endphp

    @if(session('success'))
        <div data-flash class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Visit header --}}
    <div class="mb-6 rounded-3xl border border-gray-100 dark:border-white/10 bg-white dark:bg-[#0F0F1A] p-5 md:p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-700 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-brand-500/20 shrink-0">
                    {{ mb_substr($patient->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <a href="{{ route('clinic.patients.medical-file', $patient) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        العودة لملف المريض
                    </a>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-1 truncate">{{ $patient->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-300">
                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $visit->visited_at->format('Y/m/d H:i') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-300">
                            {{ $visit->doctor?->name ?? 'الطبيب' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold {{ $visit->isCompleted() ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                            {{ $visit->isCompleted() ? 'مكتملة' : 'مفتوحة' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky tab bar --}}
    <div class="sticky top-0 z-30 -mx-1 mb-6 px-1 py-3 bg-[#F8F9FC]/90 dark:bg-[#0A0A12]/90 backdrop-blur-md">
        <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-thin p-1 rounded-2xl bg-white/80 dark:bg-[#0F0F1A]/80 border border-gray-100 dark:border-white/10 shadow-sm" role="tablist" id="visit-tab-bar">
            @foreach([
                ['id' => 'visit-info', 'label' => 'بيانات الزيارة'],
                ['id' => 'visit-diagnosis', 'label' => 'التشخيص'],
                ['id' => 'visit-prescriptions', 'label' => 'الروشتة'],
                ['id' => 'visit-lab', 'label' => 'التحاليل'],
                ['id' => 'visit-radiology', 'label' => 'الأشعة'],
                ['id' => 'visit-results', 'label' => 'النتائج'],
                ['id' => 'visit-followup', 'label' => 'مراجعة'],
            ] as $i => $tab)
                <button type="button" role="tab" data-visit-tab="{{ $tab['id'] }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                        class="visit-tab-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $i === 0 ? 'bg-brand-600 text-white shadow-md shadow-brand-500/25' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10' }}">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="visit-panels">
        {{-- بيانات الزيارة --}}
        <section id="visit-info" data-visit-panel role="tabpanel" class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 dark:text-brand-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="font-black text-lg text-gray-900 dark:text-white">بيانات الزيارة</h2>
            </div>
            <form method="POST" action="{{ route('clinic.visits.update', [$patient, $visit]) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">تاريخ ووقت الزيارة</label>
                        <input type="datetime-local" name="visited_at" value="{{ $visit->visited_at->format('Y-m-d\\TH:i') }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                            <option value="open" @selected($visit->status === 'open')>مفتوحة</option>
                            <option value="completed" @selected($visit->status === 'completed')>مكتملة</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">الشكوى الرئيسية</label>
                    <textarea name="chief_complaint" rows="2" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">{{ old('chief_complaint', $visit->chief_complaint) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">ملاحظات سريرية</label>
                    <textarea name="clinical_notes" rows="4" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">{{ old('clinical_notes', $visit->clinical_notes) }}</textarea>
                </div>
                <p class="text-xs text-gray-400">يمكن إضافة نتائج ومرفقات حتى بعد إكمال الزيارة.</p>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold transition-colors">حفظ التغييرات</button>
            </form>
        </section>

        {{-- التشخيص --}}
        <section id="visit-diagnosis" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center text-violet-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h2 class="font-black text-lg text-gray-900 dark:text-white">التشخيص</h2>
                </div>
                <button type="button" data-toggle-composer="diagnosis-composer" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-violet-600 hover:bg-violet-500 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة تشخيص
                </button>
            </div>
            <div class="space-y-3">
                @forelse($visit->diagnoses as $diagnosis)
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10">
                        <form method="POST" action="{{ route('clinic.visits.diagnoses.update', [$patient, $visit, $diagnosis]) }}" class="space-y-3">
                            @csrf @method('PUT')
                            <input type="text" name="diagnosis" value="{{ $diagnosis->diagnosis }}" required
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                            <textarea name="notes" rows="2" placeholder="ملاحظات التشخيص" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-3 py-2 text-sm outline-none dark:text-white">{{ $diagnosis->notes }}</textarea>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-[11px] text-gray-400">{{ optional($diagnosis->diagnosed_at)->format('Y/m/d') ?? '—' }}</p>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-brand-600 text-white hover:bg-brand-500">حفظ التعديل</button>
                                    <button type="submit" form="destroy-diagnosis-{{ $diagnosis->id }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">حذف</button>
                                </div>
                            </div>
                        </form>
                        <form id="destroy-diagnosis-{{ $diagnosis->id }}" method="POST" action="{{ route('clinic.patients.diagnoses.destroy', [$patient, $diagnosis]) }}" data-confirm="حذف هذا التشخيص؟">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                @empty
                    <div class="text-center py-10 text-sm text-gray-400 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا توجد تشخيصات لهذه الزيارة بعد.</div>
                @endforelse
            </div>
            <div id="diagnosis-composer" class="hidden mt-5 border-t border-gray-100 dark:border-white/10 pt-5">
                <form method="POST" action="{{ route('clinic.visits.diagnoses.store', [$patient, $visit]) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="diagnosis" required placeholder="التشخيص السريري"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 outline-none dark:text-white">
                    <textarea name="notes" rows="2" placeholder="ملاحظات (اختياري)" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm outline-none dark:text-white"></textarea>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold">حفظ التشخيص</button>
                </form>
            </div>
        </section>

        {{-- الروشتة --}}
        <section id="visit-prescriptions" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h2 class="font-black text-lg text-gray-900 dark:text-white">الروشتة</h2>
                </div>
                <button type="button" id="btn-toggle-rx-composer" aria-expanded="{{ $composerOpen ? 'true' : 'false' }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    كتابة روشتة
                </button>
            </div>

            <div class="space-y-3 mb-6">
                @forelse($visit->prescriptions as $rx)
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 hover:border-brand-300 dark:hover:border-brand-700/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">روشتة {{ $rx->created_at->format('Y/m/d H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ $rx->items->count() }} أدوية @if($rx->notes)<span class="text-gray-400">· ملاحظات</span>@endif</p>
                            </div>
                        </div>
                        <a href="{{ route('clinic.prescriptions.print', $rx) }}" target="_blank"
                           class="px-3 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition-colors">طباعة</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا توجد روشتات لهذه الزيارة بعد.</p>
                @endforelse
            </div>

            <div id="visit-rx-composer-wrap" class="{{ $composerOpen ? '' : 'hidden' }} border-t border-gray-100 dark:border-white/10 pt-6">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">روشتة ذكية — مرتبطة بهذه الزيارة</p>
                @include('clinic.prescriptions._composer', ['patient' => $patient, 'visit' => $visit, 'templates' => $templates ?? collect()])
            </div>
        </section>

        {{-- التحاليل --}}
        <section id="visit-lab" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center text-violet-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-black text-lg text-gray-900 dark:text-white">طلبات التحاليل</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $visit->labOrders->count() }} طلب</p>
                    </div>
                </div>
                <button type="button" data-toggle-composer="lab-composer" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-violet-600 hover:bg-violet-500 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة طلب تحليل
                </button>
            </div>

            <div id="lab-composer" class="hidden mb-6 space-y-4 p-4 rounded-2xl bg-violet-50/50 dark:bg-violet-900/10 border border-violet-100 dark:border-violet-800/30">
                <form method="POST" action="{{ route('clinic.lab-orders.store', [$patient, $visit]) }}" class="space-y-4">
                    @csrf
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">بحث في كتالوج التحاليل</label>
                    <input type="search" placeholder="ابحث بالاسم العربي أو الإنجليزي..." class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-4 py-2.5 text-sm focus:ring-2 focus:ring-violet-500/30 outline-none dark:text-white" oninput="filterCatalog(this, 'lab-catalog')">
                    <div id="lab-catalog" class="max-h-52 overflow-y-auto space-y-1 border border-gray-200 dark:border-white/10 rounded-xl p-3 bg-white dark:bg-[#0F0F1A]">
                        @foreach($labTests as $test)
                            <label class="flex items-center gap-2 text-sm p-2 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 cursor-pointer transition-colors" data-label="{{ $test->name_ar }} {{ $test->name_en }}">
                                <input type="checkbox" name="lab_test_ids[]" value="{{ $test->id }}" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="flex-1"><span class="font-semibold">{{ $test->name_en }}</span>@if($test->name_ar)<span class="text-xs text-gray-400 mr-1">({{ $test->name_ar }})</span>@endif</span>
                            </label>
                        @endforeach
                    </div>
                    <textarea name="notes" rows="2" placeholder="تعليمات الطلب (صيام، وقت، إلخ)" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-3 py-2 text-sm outline-none dark:text-white"></textarea>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold transition-colors">إنشاء وطباعة الطلب</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($visit->labOrders as $order)
                    <div class="border border-gray-100 dark:border-white/10 rounded-2xl p-4 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex flex-wrap justify-between gap-2 text-sm font-bold">
                            <span class="text-gray-900 dark:text-white">{{ $order->ordered_at->format('Y/m/d H:i') }}</span>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-lg text-xs {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    {{ $order->status === 'completed' ? 'مكتمل' : 'قيد الانتظار' }}
                                </span>
                                <a href="{{ route('clinic.lab-orders.print', [$patient, $visit, $order]) }}" target="_blank" class="text-brand-600 text-xs font-bold hover:underline">طباعة</a>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $order->items->pluck('name_snapshot')->join('، ') }}</p>
                        @if($order->result_notes)
                            <p class="text-xs mt-2 p-2 rounded-lg bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/5 whitespace-pre-wrap">{{ $order->result_notes }}</p>
                        @endif
                        <button type="button" data-toggle-composer="lab-result-{{ $order->id }}" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg border border-emerald-200 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100">
                            إضافة نتيجة
                        </button>
                        <form id="lab-result-{{ $order->id }}" method="POST" action="{{ route('clinic.lab-orders.results', [$patient, $visit, $order]) }}" enctype="multipart/form-data" class="hidden mt-3 space-y-2 pt-3 border-t border-gray-100 dark:border-white/10">
                            @csrf
                            <textarea name="result_notes" rows="2" placeholder="نتيجة نصية" class="w-full rounded-xl border border-gray-200 dark:border-white/10 px-3 py-2 text-xs outline-none dark:bg-[#0F0F1A] dark:text-white">{{ $order->result_notes }}</textarea>
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">حفظ النتيجة</button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-10 text-sm text-gray-400 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا توجد طلبات تحاليل لهذه الزيارة.</div>
                @endforelse
            </div>
        </section>

        {{-- الأشعة --}}
        <section id="visit-radiology" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-black text-lg text-gray-900 dark:text-white">طلبات الأشعة</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $visit->radiologyOrders->count() }} طلب</p>
                    </div>
                </div>
                <button type="button" data-toggle-composer="rad-composer" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-sky-600 hover:bg-sky-500 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة طلب أشعة
                </button>
            </div>

            <div id="rad-composer" class="hidden mb-6 space-y-4 p-4 rounded-2xl bg-sky-50/50 dark:bg-sky-900/10 border border-sky-100 dark:border-sky-800/30">
                <form method="POST" action="{{ route('clinic.radiology-orders.store', [$patient, $visit]) }}" class="space-y-4">
                    @csrf
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">بحث في كتالوج الأشعة</label>
                    <input type="search" placeholder="ابحث بالاسم..." class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500/30 outline-none dark:text-white" oninput="filterCatalog(this, 'rad-catalog')">
                    <div id="rad-catalog" class="max-h-52 overflow-y-auto space-y-1 border border-gray-200 dark:border-white/10 rounded-xl p-3 bg-white dark:bg-[#0F0F1A]">
                        @foreach($radiologyExams as $exam)
                            <label class="flex items-center gap-2 text-sm p-2 rounded-lg hover:bg-sky-50 dark:hover:bg-sky-900/20 cursor-pointer transition-colors" data-label="{{ $exam->name_ar }} {{ $exam->name_en }}">
                                <input type="checkbox" name="radiology_exam_ids[]" value="{{ $exam->id }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                <span class="flex-1"><span class="font-semibold">{{ $exam->name_en }}</span>@if($exam->name_ar)<span class="text-xs text-gray-400 mr-1">({{ $exam->name_ar }})</span>@endif</span>
                            </label>
                        @endforeach
                    </div>
                    <textarea name="notes" rows="2" placeholder="تعليمات الطلب" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0F0F1A] px-3 py-2 text-sm outline-none dark:text-white"></textarea>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-sm font-bold transition-colors">إنشاء وطباعة الطلب</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($visit->radiologyOrders as $order)
                    <div class="border border-gray-100 dark:border-white/10 rounded-2xl p-4 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex flex-wrap justify-between gap-2 text-sm font-bold">
                            <span class="text-gray-900 dark:text-white">{{ $order->ordered_at->format('Y/m/d H:i') }}</span>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-lg text-xs {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    {{ $order->status === 'completed' ? 'مكتمل' : 'قيد الانتظار' }}
                                </span>
                                <a href="{{ route('clinic.radiology-orders.print', [$patient, $visit, $order]) }}" target="_blank" class="text-brand-600 text-xs font-bold hover:underline">طباعة</a>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $order->items->pluck('name_snapshot')->join('، ') }}</p>
                        @if($order->result_notes)
                            <p class="text-xs mt-2 p-2 rounded-lg bg-white dark:bg-[#0F0F1A] border whitespace-pre-wrap">{{ $order->result_notes }}</p>
                        @endif
                        <button type="button" data-toggle-composer="rad-result-{{ $order->id }}" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg border border-emerald-200 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100">
                            إضافة نتيجة
                        </button>
                        <form id="rad-result-{{ $order->id }}" method="POST" action="{{ route('clinic.radiology-orders.results', [$patient, $visit, $order]) }}" enctype="multipart/form-data" class="hidden mt-3 space-y-2 pt-3 border-t border-gray-100 dark:border-white/10">
                            @csrf
                            <textarea name="result_notes" rows="2" placeholder="نتيجة نصية" class="w-full rounded-xl border px-3 py-2 text-xs outline-none dark:bg-[#0F0F1A] dark:text-white">{{ $order->result_notes }}</textarea>
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold">حفظ النتيجة</button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-10 text-sm text-gray-400 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا توجد طلبات أشعة لهذه الزيارة.</div>
                @endforelse
            </div>
        </section>

        {{-- النتائج --}}
        <section id="visit-results" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </div>
                <h2 class="font-black text-lg text-gray-900 dark:text-white">النتائج والمرفقات</h2>
            </div>
            @php
                $resultFiles = $visit->medicalFiles
                    ->merge($visit->labOrders->flatMap->medicalFiles)
                    ->merge($visit->radiologyOrders->flatMap->medicalFiles)
                    ->unique('id')
                    ->sortByDesc('created_at');
            @endphp
            <div class="space-y-2">
                @forelse($resultFiles as $file)
                    <a href="{{ route('clinic.records.download', $file) }}" class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-sm font-bold hover:border-brand-400 dark:hover:border-brand-600 transition-colors group">
                        <span class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                            <span class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-900/30 text-brand-600 flex items-center justify-center group-hover:bg-brand-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            {{ $file->type }} — {{ $file->created_at->format('Y/m/d H:i') }}
                        </span>
                        <span class="text-xs text-brand-600 font-bold">تحميل</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 text-center py-10 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا توجد نتائج أو مرفقات مرتبطة بهذه الزيارة بعد.</p>
                @endforelse
            </div>
        </section>

        {{-- مراجعة --}}
        <section id="visit-followup" data-visit-panel role="tabpanel" hidden class="visit-panel bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/10 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center text-brand-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="font-black text-lg text-gray-900 dark:text-white">موعد مراجعة</h2>
                </div>
                <button type="button" data-toggle-composer="followup-composer" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة موعد مراجعة
                </button>
            </div>

            @if($visit->followUpAppointments->isNotEmpty())
                <div class="mb-5 space-y-2">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">مواعيد مرتبطة</p>
                    @foreach($visit->followUpAppointments as $fu)
                        <p class="text-sm font-bold text-brand-700 dark:text-brand-300 px-3 py-2 rounded-xl bg-brand-50 dark:bg-brand-900/20">{{ $fu->scheduled_at->format('Y/m/d h:i A') }}</p>
                    @endforeach
                </div>
            @else
                <div class="mb-5 text-center py-10 text-sm text-gray-400 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl">لا يوجد موعد مراجعة مرتبط بهذه الزيارة بعد.</div>
            @endif

            <div id="followup-composer" class="hidden border-t border-gray-100 dark:border-white/10 pt-5">
                <form method="POST" action="{{ route('clinic.patients.appointments.store', $patient) }}" class="space-y-4 max-w-xl">
                    @csrf
                    <input type="hidden" name="follow_up_of_visit_id" value="{{ $visit->id }}">
                    <p class="text-xs text-gray-500">موعد المراجعة يُربط تلقائياً بهذه الزيارة.</p>
                    <div class="flex flex-wrap gap-2 text-xs font-bold" id="followup-presets">
                        <button type="button" data-days="7" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-brand-500 hover:text-white transition-colors">أسبوع</button>
                        <button type="button" data-days="14" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-brand-500 hover:text-white transition-colors">أسبوعان</button>
                        <button type="button" data-days="30" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/10 hover:bg-brand-500 hover:text-white transition-colors">شهر</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold mb-1 text-gray-600 dark:text-gray-400">التاريخ</label>
                            <input type="date" name="scheduled_date" id="followup-date" value="{{ now()->addWeek()->toDateString() }}" min="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-2.5 text-sm outline-none dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1 text-gray-600 dark:text-gray-400">الوقت</label>
                            <input type="time" name="scheduled_time" value="10:00" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-2.5 text-sm outline-none dark:text-white">
                        </div>
                    </div>
                    <select name="visit_type_id" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-2.5 text-sm outline-none dark:text-white">
                        <option value="">اختر نوع الخدمة</option>
                        @foreach($visitTypes as $vt)
                            <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold">حجز مراجعة</button>
                </form>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        function filterCatalog(input, catalogId) {
            const q = (input.value || '').toLowerCase();
            document.querySelectorAll('#' + catalogId + ' [data-label]').forEach((el) => {
                el.style.display = el.getAttribute('data-label').toLowerCase().includes(q) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tabButtons = document.querySelectorAll('[data-visit-tab]');
            const panels = document.querySelectorAll('[data-visit-panel]');
            const activeClasses = ['bg-brand-600', 'text-white', 'shadow-md', 'shadow-brand-500/25'];
            const inactiveClasses = ['text-gray-600', 'dark:text-gray-300'];

            const visitStorageKey = 'visitActiveTab-{{ $visit->id }}';
            const validIds = ['visit-info', 'visit-diagnosis', 'visit-prescriptions', 'visit-lab', 'visit-radiology', 'visit-results', 'visit-followup'];

            function activateTab(tabId) {
                if (!validIds.includes(tabId)) {
                    return;
                }
                tabButtons.forEach((btn) => {
                    const isActive = btn.dataset.visitTab === tabId;
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    btn.classList.remove(...activeClasses, ...inactiveClasses);
                    if (isActive) {
                        btn.classList.add(...activeClasses);
                    } else {
                        btn.classList.add(...inactiveClasses, 'hover:bg-gray-100', 'dark:hover:bg-white/10');
                    }
                });
                panels.forEach((panel) => {
                    panel.hidden = panel.id !== tabId;
                });
                try {
                    history.replaceState(null, '', '#' + tabId);
                    sessionStorage.setItem(visitStorageKey, tabId);
                } catch (e) {}
            }

            tabButtons.forEach((btn) => {
                btn.addEventListener('click', () => activateTab(btn.dataset.visitTab));
            });

            document.querySelectorAll('.visit-panel form').forEach((form) => {
                form.addEventListener('submit', () => {
                    const panel = form.closest('[data-visit-panel]');
                    if (!panel) {
                        return;
                    }
                    let input = form.querySelector('input[name="return_tab"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'return_tab';
                        form.appendChild(input);
                    }
                    input.value = panel.id;
                    try {
                        sessionStorage.setItem(visitStorageKey, panel.id);
                        history.replaceState(null, '', '#' + panel.id);
                    } catch (e) {}
                });
            });

            const hash = (window.location.hash || '').replace('#', '');
            let savedTab = null;
            try {
                savedTab = sessionStorage.getItem(visitStorageKey);
            } catch (e) {}

            if (hash && validIds.includes(hash)) {
                activateTab(hash);
            } else if (savedTab && validIds.includes(savedTab)) {
                activateTab(savedTab);
            } else if (@json($composerOpen)) {
                activateTab('visit-prescriptions');
            }

            const composerWrap = document.getElementById('visit-rx-composer-wrap');
            const toggleBtn = document.getElementById('btn-toggle-rx-composer');
            toggleBtn?.addEventListener('click', () => {
                if (!composerWrap) return;
                const willShow = composerWrap.classList.contains('hidden');
                composerWrap.classList.toggle('hidden');
                toggleBtn.setAttribute('aria-expanded', willShow ? 'true' : 'false');
                if (willShow) {
                    composerWrap.querySelector('#drug-search-input')?.focus();
                }
            });

            document.querySelectorAll('[data-toggle-composer]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const target = document.getElementById(btn.dataset.toggleComposer);
                    if (!target) return;
                    const willShow = target.classList.contains('hidden');
                    target.classList.toggle('hidden');
                    if (willShow) {
                        target.querySelector('input:not([type="hidden"]), textarea, select')?.focus();
                    }
                });
            });

            document.querySelectorAll('#followup-presets [data-days]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const d = new Date();
                    d.setDate(d.getDate() + Number(btn.dataset.days));
                    const el = document.getElementById('followup-date');
                    if (el) el.value = d.toISOString().slice(0, 10);
                });
            });
        });
    </script>
    @endpush
</x-layouts.clinic>
<x-layouts.clinic>
    <x-slot:title>الملف الطبي — {{ $patient->name }}</x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         HEADER — Patient Identity
    ════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-br from-brand-600 via-brand-700 to-indigo-800 rounded-2xl p-6 mb-6 shadow-xl shadow-brand-500/20 relative overflow-hidden">
        {{-- Background orbs --}}
        <div class="absolute -top-8 -left-8 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-5">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center font-black text-white text-2xl shadow-lg shrink-0">
                    {{ mb_substr($patient->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-2xl font-black text-white">{{ $patient->name }}</h1>
                        @if($patient->gender === 'male')
                            <span class="px-2 py-0.5 rounded-lg bg-blue-400/30 text-blue-100 text-xs font-bold">ذكر</span>
                        @elseif($patient->gender === 'female')
                            <span class="px-2 py-0.5 rounded-lg bg-pink-400/30 text-pink-100 text-xs font-bold">أنثى</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-white/70 text-sm">
                        @if($patient->displayAge() !== null)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $patient->displayAge() }} سنة
                                @if($patient->dob)
                                    ({{ $patient->dob->format('Y/m/d') }})
                                @endif
                            </span>
                        @endif
                        @if($patient->phone)
                            <span class="flex items-center gap-1.5 dir-ltr">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $patient->phone }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            مسجل منذ {{ $patient->created_at->format('Y/m/d') }}
                        </span>
                    </div>
                    @if($patient->notes)
                        <p class="mt-2 text-white/60 text-xs max-w-lg">{{ $patient->notes }}</p>
                    @endif
                </div>
            </div>

            {{-- Stats & Actions --}}
            <div class="flex items-center gap-3">
                <button type="button" data-modal-open="modal-edit-patient-{{ $patient->id }}"
                        class="px-4 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-sm transition-all flex items-center gap-1.5 shadow-sm hover:shadow cursor-pointer"
                        title="تعديل بيانات المريض">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>تعديل البيانات</span>
                </button>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $patient->visits->count() }}</p>
                    <p class="text-xs text-white/60 mt-0.5">زيارة</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $patient->appointments->count() }}</p>
                    <p class="text-xs text-white/60 mt-0.5">موعد</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PATIENT REGISTRATION DETAILS (always visible)
    ════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white">بيانات تسجيل المريض</h3>
                <p class="text-xs text-gray-400 mt-0.5">المعلومات المسجّلة عند إضافة المريض</p>
            </div>
            <button type="button" data-modal-open="modal-edit-patient-{{ $patient->id }}"
                    class="px-3 py-1.5 text-xs font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-xl hover:bg-brand-100">
                تعديل
            </button>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06] p-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-1">العمر</p>
                <p class="text-sm font-black text-gray-900 dark:text-white">
                    @if($patient->displayAge() !== null)
                        {{ $patient->displayAge() }} سنة
                    @else
                        <span class="text-gray-400 font-semibold">غير محدد</span>
                    @endif
                </p>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06] p-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-1">تاريخ الميلاد</p>
                <p class="text-sm font-black text-gray-900 dark:text-white">
                    {{ $patient->dob?->format('Y/m/d') ?? '—' }}
                </p>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06] p-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-1">الجنس</p>
                <p class="text-sm font-black text-gray-900 dark:text-white">
                    @if($patient->gender === 'male') ذكر
                    @elseif($patient->gender === 'female') أنثى
                    @else —
                    @endif
                </p>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06] p-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-1">الهاتف</p>
                <p class="text-sm font-black text-gray-900 dark:text-white dir-ltr text-right">{{ $patient->phone ?: '—' }}</p>
            </div>
        </div>
        <div class="px-6 pb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-orange-100 dark:border-orange-900/30 bg-orange-50/40 dark:bg-orange-900/10 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-gray-900 dark:text-white">التاريخ المرضي</h4>
                        <p class="text-[11px] text-gray-400">الأمراض المزمنة والحالات الصحية</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $patient->medical_history ?: 'لا يوجد تاريخ مرضي مسجّل.' }}</p>
            </div>
            <div class="rounded-xl border border-red-100 dark:border-red-900/30 bg-red-50/40 dark:bg-red-900/10 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-gray-900 dark:text-white">التاريخ الجراحي</h4>
                        <p class="text-[11px] text-gray-400">العمليات الجراحية السابقة</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $patient->surgical_history ?: 'لا يوجد تاريخ جراحي مسجّل.' }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-gray-100 dark:border-white/[0.06] bg-gray-50/60 dark:bg-white/[0.02] p-4">
                <h4 class="font-black text-sm text-gray-900 dark:text-white mb-1">ملاحظات عامة</h4>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $patient->notes ?: 'لا توجد ملاحظات.' }}</p>
            </div>
        </div>
    </div>

    {{-- Clinical visits timeline --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white">الزيارات الطبية</h3>
                <p class="text-xs text-gray-400 mt-0.5">سجل زمني يمكن الرجوع إليه وإضافة نتائج لاحقًا</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-modal-open="modal-add-appointment-patient" class="px-4 py-2 text-sm font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-xl">حجز موعد</button>
                <button type="button" data-modal-open="modal-add-visit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl">زيارة جديدة</button>
            </div>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-white/[0.04]">
            @forelse($patient->visits as $visit)
                <a href="{{ route('clinic.visits.show', [$patient, $visit]) }}" class="flex items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $visit->visited_at->format('Y/m/d H:i') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $visit->doctor?->name ?? '—' }}
                            @if($visit->chief_complaint) · {{ \Illuminate\Support\Str::limit($visit->chief_complaint, 60) }}@endif
                        </p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $visit->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $visit->status === 'completed' ? 'مكتملة' : 'مفتوحة' }}
                    </span>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">لا توجد زيارات بعد. أنشئ أول زيارة من زر «زيارة جديدة».</div>
            @endforelse
        </div>
    </div>

    <div id="modal-add-appointment-patient" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg">حجز موعد / مراجعة</h3>
                <button type="button" data-modal-close="modal-add-appointment-patient" class="p-1.5 rounded-lg text-gray-400">✕</button>
            </div>
            <form method="POST" action="{{ route('clinic.patients.appointments.store', $patient) }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="flex flex-wrap gap-2 text-xs font-bold">
                        <button type="button" class="px-2 py-1 rounded-lg bg-gray-100" onclick="document.getElementById('patient-appt-date').value='{{ now()->addWeek()->toDateString() }}'">أسبوع</button>
                        <button type="button" class="px-2 py-1 rounded-lg bg-gray-100" onclick="document.getElementById('patient-appt-date').value='{{ now()->addWeeks(2)->toDateString() }}'">أسبوعان</button>
                        <button type="button" class="px-2 py-1 rounded-lg bg-gray-100" onclick="document.getElementById('patient-appt-date').value='{{ now()->addMonth()->toDateString() }}'">شهر</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold mb-1.5">التاريخ</label>
                            <input id="patient-appt-date" type="date" name="scheduled_date" value="{{ now()->addWeek()->toDateString() }}" min="{{ date('Y-m-d') }}" required class="w-full rounded-xl border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1.5">الساعة</label>
                            <input type="time" name="scheduled_time" value="10:00" required class="w-full rounded-xl border px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">الخدمة</label>
                        <select name="visit_type_id" required class="w-full rounded-xl border px-3 py-2 text-sm">
                            <option value="">اختر الخدمة</option>
                            @foreach(($visitTypes ?? []) as $vt)
                                <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">ربط بمراجعة لزيارة سابقة (اختياري)</label>
                        <select name="follow_up_of_visit_id" class="w-full rounded-xl border px-3 py-2 text-sm">
                            <option value="">بدون ربط</option>
                            @foreach($patient->visits as $v)
                                <option value="{{ $v->id }}">{{ $v->visited_at->format('Y/m/d H:i') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <textarea name="notes" rows="2" placeholder="ملاحظات" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
                </div>
                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-appointment-patient" class="px-4 py-2 text-sm font-bold text-gray-500">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl">حفظ الموعد</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-visit" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg">زيارة طبية جديدة</h3>
                <button type="button" data-modal-close="modal-add-visit" class="p-1.5 rounded-lg text-gray-400">✕</button>
            </div>
            <form method="POST" action="{{ route('clinic.visits.store', $patient) }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">تاريخ ووقت الزيارة</label>
                        <input type="datetime-local" name="visited_at" value="{{ now()->format('Y-m-d\\TH:i') }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-4 py-2.5 text-sm">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        الشكوى والتشخيص والملاحظات تُضاف من داخل صفحة الزيارة بعد الإنشاء.
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-visit" class="px-4 py-2 text-sm font-bold text-gray-500">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl">إنشاء الزيارة</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MAIN GRID  (medical data + chatbot)
    ════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_420px] gap-6">

        {{-- ─── LEFT COLUMN --}}
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-brand-50 to-indigo-50 dark:from-brand-900/20 dark:to-indigo-900/10 border border-brand-100 dark:border-brand-800/40 rounded-2xl p-5 shadow-sm">
                <h3 class="font-black text-gray-900 dark:text-white mb-1">الإجراءات الطبية داخل الزيارة</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    التشخيص، الروشتة، التحاليل، الأشعة، والنتائج تُدار من داخل الزيارة. افتح زيارة من القائمة أعلاه للبدء.
                </p>
            </div>
        </div>{{-- end left column --}}

        {{-- ─── RIGHT COLUMN: AI Chatbot ─── --}}
        @if(config('clinora.features.ai_enabled'))
        <div class="xl:sticky xl:top-6 xl:self-start">
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden flex flex-col" style="height: calc(100vh - 120px); min-height: 560px;">

                {{-- Chatbot Header --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/10 dark:to-violet-900/10 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md shrink-0 border border-indigo-500/20 bg-white">
                                <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="ClinoraAI" class="w-full h-full object-cover">
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-white dark:border-[#0F0F1A] rounded-full" id="ai-status-dot"></span>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">ClinoraAI</h3>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">مساعد طبي ذكي — على علم بكل تفاصيل المريض</p>
                        </div>
                        <button id="btn-clear-chat" class="mr-auto p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors" title="مسح المحادثة">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
                    {{-- Welcome message --}}
                    <div class="chat-msg-ai flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-xl overflow-hidden shrink-0 shadow-sm border border-indigo-500/20 bg-white">
                            <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="AI" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/[0.06] rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]">
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                مرحباً دكتور! 👋<br>
                                أنا ClinoraAI، مساعدك الطبي الذكي. لدي كامل معلومات المريض <strong>{{ $patient->name }}</strong> من أشعة وتحاليل وروشتات وتشخيصات.<br>
                                كيف يمكنني مساعدتك اليوم؟
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Typing indicator (hidden) --}}
                <div id="typing-indicator" class="hidden px-4 pb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl overflow-hidden shrink-0 shadow-sm border border-indigo-500/20 bg-white">
                            <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="AI" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/[0.06] rounded-2xl rounded-tr-sm px-4 py-3">
                            <div class="flex gap-1 items-center h-4">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Suggested questions --}}
                <div id="suggested-prompts" class="px-4 pb-3 shrink-0">
                    <div class="flex flex-wrap gap-1.5">
                        <button class="suggestion-btn text-[11px] font-bold px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors border border-indigo-100 dark:border-indigo-700/30">
                            ما التشخيص المحتمل؟
                        </button>
                        <button class="suggestion-btn text-[11px] font-bold px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors border border-indigo-100 dark:border-indigo-700/30">
                            هل الأدوية الحالية مناسبة؟
                        </button>
                        <button class="suggestion-btn text-[11px] font-bold px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors border border-indigo-100 dark:border-indigo-700/30">
                            ما الفحوصات المقترحة؟
                        </button>
                        <button class="suggestion-btn text-[11px] font-bold px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors border border-indigo-100 dark:border-indigo-700/30">
                            لخّص الحالة الطبية
                        </button>
                    </div>
                </div>

                {{-- Input Area --}}
                <div class="px-4 pb-4 shrink-0 border-t border-gray-100 dark:border-white/[0.06] pt-3">
                    <form id="chat-form" class="flex items-end gap-2">
                        @csrf
                        <textarea id="chat-input" rows="1"
                                  placeholder="اكتب سؤالك للـ AI..."
                                  class="flex-1 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none dark:text-white resize-none overflow-hidden transition-shadow leading-relaxed"
                                  style="min-height: 44px; max-height: 120px;"></textarea>
                        <button type="submit" id="btn-send-chat"
                                class="w-10 h-10 shrink-0 bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-500/30 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>{{-- end right column --}}
        @endif

    </div>{{-- end grid --}}

    {{-- Back button --}}
    <div class="mt-6">
        <a href="{{ route('clinic.patients.index') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7l-7 7 7 7"></path></svg>
            العودة لقائمة المرضى
        </a>
    </div>

    {{-- ══════════════════════════════════════
         ADD RECORD / MEDICAL FILE MODAL
    ════════════════════════════════════════ --}}
    <div id="modal-edit-patient-{{ $patient->id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">تعديل بيانات المريض</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $patient->name }}</p>
                </div>
                <button type="button" data-modal-close="modal-edit-patient-{{ $patient->id }}" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('clinic.patients.update', $patient) }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $patient->name }}" required autocomplete="off"
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                               placeholder="مثال: أحمد محمد علي">
                    </div>

                    {{-- Phone + Gender --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ $patient->phone }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="05xxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الجنس</label>
                            <select name="gender" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                                <option value="">— اختر —</option>
                                <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>

                    {{-- DOB + Age --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ الميلاد</label>
                            <input type="date" name="dob" data-dob-age-source value="{{ $patient->dob?->format('Y-m-d') }}"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                العمر
                                <span class="text-xs font-normal text-gray-400">(يُحسب من تاريخ الميلاد إن وُجد)</span>
                            </label>
                            <input type="number" name="age" data-dob-age-target value="{{ $patient->displayAge() }}" min="0" max="150"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="مثال: 45">
                        </div>
                    </div>

                    {{-- Medical History --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            التاريخ المرضي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <div class="flex flex-wrap gap-2 mb-2.5">
                            @foreach(['سكري', 'ضغط دم', 'قلب وشرايين', 'فشل كلوي', 'أمراض كبد', 'غدة درقية', 'ربو', 'حساسية'] as $cond)
                                <button type="button" data-chip data-target="medical_history_edit_{{ $patient->id }}" data-value="{{ $cond }}"
                                        class="chip-btn px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:border-brand-400 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-all">
                                    {{ $cond }}
                                </button>
                            @endforeach
                        </div>
                        <textarea name="medical_history" id="medical_history_edit_{{ $patient->id }}" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: سكري نوع 2، ضغط دم مرتفع...">{{ $patient->medical_history }}</textarea>
                    </div>

                    {{-- Surgical History --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            التاريخ الجراحي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <textarea name="surgical_history" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: استئصال زائدة 2018، عملية قلب مفتوح 2021...">{{ $patient->surgical_history }}</textarea>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات عامة</label>
                        <textarea name="notes" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="أي ملاحظات خاصة بالمريض...">{{ $patient->notes }}</textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3 shrink-0">
                    <button type="button" data-modal-close="modal-edit-patient-{{ $patient->id }}"
                            class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Quick-select chips for medical history
        document.querySelectorAll('[data-chip]').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.dataset.target;
                const value = btn.dataset.value;
                const textarea = document.getElementById(targetId);
                if (!textarea) return;
                const current = textarea.value.trim();
                if (current.includes(value)) {
                    textarea.value = current.replace(new RegExp(',?\\s*' + value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*,?', 'g'), '').replace(/^,\s*|,\s*$/g, '').trim();
                    btn.classList.remove('border-brand-400', 'text-brand-600', 'bg-brand-50', 'dark:text-brand-400', 'dark:bg-brand-900/20');
                } else {
                    textarea.value = current ? current + '، ' + value : value;
                    btn.classList.add('border-brand-400', 'text-brand-600', 'bg-brand-50', 'dark:text-brand-400', 'dark:bg-brand-900/20');
                }
            });
        });
        // ══════════════════════════════════
        //  AI CHATBOT LOGIC
        // ══════════════════════════════════
        @if(config('clinora.features.ai_enabled'))
        const chatMessages  = document.getElementById('chat-messages');
        const chatInput     = document.getElementById('chat-input');
        const chatForm      = document.getElementById('chat-form');
        const typingIndicator = document.getElementById('typing-indicator');
        const btnClearChat  = document.getElementById('btn-clear-chat');
        const suggestedBox  = document.getElementById('suggested-prompts');
        const csrfToken     = document.querySelector('meta[name="csrf-token"]')?.content
                              || '{{ csrf_token() }}';
        const chatEndpoint  = '{{ route("clinic.patients.ai-chat", $patient) }}';

        // Conversation history for multi-turn
        let conversationHistory = [];
        let isLoading = false;

        // Auto-resize textarea
        chatInput.addEventListener('input', () => {
            chatInput.style.height = 'auto';
            chatInput.style.height = Math.min(chatInput.scrollHeight, 120) + 'px';
        });

        // Send on Enter (not Shift+Enter)
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Suggested prompts
        document.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                chatInput.value = btn.textContent.trim();
                chatInput.dispatchEvent(new Event('input'));
                sendMessage();
            });
        });

        // Submit form
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            sendMessage();
        });

        // Clear chat
        btnClearChat.addEventListener('click', () => {
            conversationHistory = [];
            // Keep only the welcome message
            const welcome = chatMessages.querySelector('.chat-msg-ai');
            chatMessages.innerHTML = '';
            if (welcome) chatMessages.appendChild(welcome);
            suggestedBox.style.display = '';
        });

        function sendMessage() {
            const text = chatInput.value.trim();
            if (!text || isLoading) return;

            // Hide suggestions after first real message
            suggestedBox.style.display = 'none';

            // Append user message
            appendMessage('user', text);
            conversationHistory.push({ role: 'user', message: text });

            // Clear input
            chatInput.value = '';
            chatInput.style.height = '44px';

            // Show typing
            setLoading(true);

            // Call API
            fetch(chatEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    message: text,
                    history: conversationHistory.slice(0, -1), // All except last (just sent)
                }),
            })
            .then(res => res.json())
            .then(data => {
                setLoading(false);
                const reply = data.message || 'لم أتمكن من الرد، يرجى المحاولة.';
                appendMessage('ai', reply);
                conversationHistory.push({ role: 'model', message: reply });
            })
            .catch(() => {
                setLoading(false);
                appendMessage('ai', '⚠️ عذراً دكتور، حدث عائق مؤقت في معالجة الطلب. يرجى إعادة المحاولة خلال ثوانٍ.');
            });
        }

        function appendMessage(role, text) {
            const wrapper = document.createElement('div');
            wrapper.className = role === 'user'
                ? 'flex justify-start flex-row-reverse items-start gap-2.5 chat-msg-user'
                : 'flex items-start gap-2.5 chat-msg-ai';

            // Avatar
            const avatar = document.createElement('div');
            if (role === 'user') {
                avatar.className = 'w-7 h-7 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shrink-0 mt-0.5 font-bold text-white text-xs shadow-sm';
                avatar.textContent = '{{ mb_substr(Auth::user()->name ?? "D", 0, 1) }}';
            } else {
                avatar.className = 'w-7 h-7 rounded-xl overflow-hidden shrink-0 mt-0.5 shadow-sm border border-indigo-500/20 bg-white';
                avatar.innerHTML = '<img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="AI" class="w-full h-full object-cover">';
            }

            // Bubble
            const bubble = document.createElement('div');
            bubble.className = role === 'user'
                ? 'bg-gradient-to-br from-brand-500 to-brand-600 text-white rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%] shadow-sm shadow-brand-500/20'
                : 'bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/[0.06] rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]';

            // Format AI text with markdown-like rendering
            const formatted = formatText(text);
            bubble.innerHTML = `<div class="text-sm leading-relaxed ${role === 'user' ? '' : 'text-gray-700 dark:text-gray-300'}">${formatted}</div>`;

            wrapper.appendChild(avatar);
            wrapper.appendChild(bubble);

            // Insert before typing indicator or at end
            chatMessages.appendChild(wrapper);

            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function formatText(text) {
            return text
                // Bold **text**
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                // Italic *text*
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                // Bullet lists
                .replace(/^[-•]\s(.+)$/gm, '<span class="flex gap-1.5 my-0.5"><span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-indigo-400 shrink-0"></span><span>$1</span></span>')
                // Numbered lists
                .replace(/^\d+\.\s(.+)$/gm, '<span class="flex gap-1.5 my-0.5"><span class="font-bold text-indigo-400">•</span><span>$1</span></span>')
                // Line breaks
                .replace(/\n/g, '<br>');
        }

        function setLoading(state) {
            isLoading = state;
            typingIndicator.classList.toggle('hidden', !state);
            document.getElementById('btn-send-chat').disabled = state;
            if (state) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }
        @endif
    });
    </script>
    @endpush

</x-layouts.clinic>

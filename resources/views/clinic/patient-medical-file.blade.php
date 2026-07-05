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
                        @if($patient->dob)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $patient->dob->diffInYears(now()) }} سنة ({{ $patient->dob->format('Y/m/d') }})
                            </span>
                        @elseif($patient->age)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $patient->age }} سنة
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

            {{-- Stats --}}
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $patient->medicalFiles->count() }}</p>
                    <p class="text-xs text-white/60 mt-0.5">ملف طبي</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $patient->prescriptions->count() }}</p>
                    <p class="text-xs text-white/60 mt-0.5">روشتة</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $patient->diagnoses->count() }}</p>
                    <p class="text-xs text-white/60 mt-0.5">تشخيص</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MEDICAL & SURGICAL HISTORY
    ════════════════════════════════════════════ --}}
    @if($patient->medical_history || $patient->surgical_history)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @if($patient->medical_history)
        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-gray-900 dark:text-white text-sm">التاريخ المرضي</h3>
                    <p class="text-xs text-gray-400">الأمراض المزمنة والحالات الصحية</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $patient->medical_history }}</p>
        </div>
        @endif
        @if($patient->surgical_history)
        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-gray-900 dark:text-white text-sm">التاريخ الجراحي</h3>
                    <p class="text-xs text-gray-400">العمليات الجراحية السابقة</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $patient->surgical_history }}</p>
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════
         MAIN GRID  (medical data + chatbot)
    ════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_420px] gap-6">

        {{-- ─── LEFT COLUMN ─── --}}
        <div class="space-y-6">

            {{-- ══ Section 1: Medical Files (Scans / Labs) ══ --}}
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">الأشعة والتحاليل</h3>
                            <p class="text-xs text-gray-400">{{ $patient->medicalFiles->count() }} ملف مرفوع</p>
                        </div>
                    </div>
                    <a href="{{ route('clinic.records.index') }}"
                       class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">رفع ملف جديد ←</a>
                </div>

                @if($patient->medicalFiles->isEmpty())
                    <div class="py-12 flex flex-col items-center justify-center text-center px-6">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-sm text-gray-400 dark:text-gray-500">لا توجد ملفات طبية لهذا المريض بعد</p>
                    </div>
                @else
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($patient->medicalFiles as $file)
                            @php
                                $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                                $isPdf = $ext === 'pdf';
                                $fileUrl = \Illuminate\Support\Facades\Storage::url($file->file_path);
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank"
                               class="group relative bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06] rounded-xl overflow-hidden hover:border-brand-400/50 dark:hover:border-brand-500/30 hover:shadow-md transition-all">
                                {{-- Preview --}}
                                <div class="h-28 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-50 dark:from-white/5 dark:to-transparent overflow-hidden">
                                    @if($isImage)
                                        <img src="{{ $fileUrl }}" alt="{{ $file->type }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @elseif($isPdf)
                                        <svg class="w-10 h-10 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 16h2v-2H8v-2h2a2 2 0 110 4H8zm6 0h-1v-4h1a2 2 0 110 4h-1zm-2-4v4h-2v-4h2z"/></svg>
                                    @else
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </div>
                                {{-- Info --}}
                                <div class="p-3">
                                    <p class="font-bold text-xs text-gray-800 dark:text-gray-200 truncate">{{ $file->type }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $file->created_at->format('Y/m/d') }}</p>
                                </div>
                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 bg-brand-600/0 group-hover:bg-brand-600/5 transition-colors flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white dark:bg-gray-800 rounded-full p-2 shadow-lg">
                                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ══ Section 2: Prescriptions ══ --}}
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">الروشتات الطبية</h3>
                            <p class="text-xs text-gray-400">{{ $patient->prescriptions->count() }} وصفة محفوظة</p>
                        </div>
                    </div>
                    <a href="{{ route('clinic.prescriptions.index') }}"
                       class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">كتابة روشتة ←</a>
                </div>

                @if($patient->prescriptions->isEmpty())
                    <div class="py-10 flex flex-col items-center justify-center text-center px-6">
                        <p class="text-sm text-gray-400 dark:text-gray-500">لا توجد وصفات طبية لهذا المريض</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                        @foreach($patient->prescriptions as $pres)
                            <div class="px-6 py-4 flex items-start justify-between gap-4 group hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold text-gray-400">{{ $pres->created_at->format('Y/m/d') }}</span>
                                        @if($pres->items->count())
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold">
                                                {{ $pres->items->count() }} دواء
                                            </span>
                                        @endif
                                    </div>
                                    @if($pres->items->count())
                                        <div class="space-y-1">
                                            @foreach($pres->items as $item)
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $item->medicine_name }}</span>
                                                    @if($item->dosage)
                                                        <span class="text-gray-400">{{ $item->dosage }}</span>
                                                    @endif
                                                    @if($item->instructions)
                                                        <span class="text-gray-400 text-xs">— {{ $item->instructions }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($pres->notes)
                                        <pre class="text-xs text-gray-600 dark:text-gray-400 font-mono whitespace-pre-wrap text-left dir-ltr">{{ $pres->notes }}</pre>
                                    @endif
                                </div>
                                <a href="{{ route('clinic.prescriptions.print', $pres) }}" target="_blank"
                                   class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 shrink-0"
                                   title="طباعة">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ══ Section 3: Diagnoses & Notes ══ --}}
            <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white text-sm">التشخيصات والملاحظات</h3>
                            <p class="text-xs text-gray-400">{{ $patient->diagnoses->count() }} تشخيص مسجل</p>
                        </div>
                    </div>
                    <button data-modal-open="modal-add-diagnosis"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-400 rounded-xl transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        إضافة تشخيص
                    </button>
                </div>

                @if($patient->diagnoses->isEmpty())
                    <div class="py-10 flex flex-col items-center justify-center text-center px-6">
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-3">لم يُسجَّل أي تشخيص لهذا المريض بعد</p>
                        <button data-modal-open="modal-add-diagnosis"
                                class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-400 rounded-xl transition-colors">
                            إضافة أول تشخيص
                        </button>
                    </div>
                @else
                    <div class="p-5 space-y-3">
                        @foreach($patient->diagnoses as $diag)
                            <div class="relative border border-gray-100 dark:border-white/[0.06] rounded-xl p-4 hover:border-amber-300/50 dark:hover:border-amber-500/30 transition-colors group bg-gradient-to-r from-amber-50/0 to-amber-50/0 hover:from-amber-50/50 dark:hover:from-amber-900/10">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="mt-0.5 w-2 h-2 rounded-full bg-amber-400 shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-lg">
                                                    {{ $diag->diagnosed_at->format('Y/m/d') }}
                                                </span>
                                            </div>
                                            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $diag->diagnosis }}</p>
                                            @if($diag->notes)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">{{ $diag->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('clinic.patients.diagnoses.destroy', [$patient, $diag]) }}"
                                          data-confirm="هل تريد حذف هذا التشخيص؟">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>{{-- end left column --}}

        {{-- ─── RIGHT COLUMN: AI Chatbot ─── --}}
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
         ADD DIAGNOSIS MODAL
    ════════════════════════════════════════ --}}
    <div id="modal-add-diagnosis" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة تشخيص جديد</h3>
                    <p class="text-xs text-gray-400 mt-0.5">للمريض: {{ $patient->name }}</p>
                </div>
                <button data-modal-close="modal-add-diagnosis" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('clinic.patients.diagnoses.store', $patient) }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">التشخيص <span class="text-red-500">*</span></label>
                        <textarea name="diagnosis" rows="3" required
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: التهاب المفاصل الروماتويدي، ارتفاع ضغط الدم...">{{ old('diagnosis') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="تفاصيل إضافية، توصيات، متابعة مطلوبة...">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ التشخيص <span class="text-red-500">*</span></label>
                        <input type="date" name="diagnosed_at" value="{{ old('diagnosed_at', date('Y-m-d')) }}" required
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-none dark:text-white transition-shadow">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-diagnosis"
                            class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-amber-500 hover:bg-amber-400 rounded-xl transition-all shadow-sm hover:shadow-amber-500/30 hover:shadow-lg active:scale-95">
                        حفظ التشخيص
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ── Re-open modal if validation failed ──
        @if($errors->any())
        if (typeof CModal !== 'undefined') CModal.open('modal-add-diagnosis');
        @endif

        // ══════════════════════════════════
        //  AI CHATBOT LOGIC
        // ══════════════════════════════════
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
    });
    </script>
    @endpush

</x-layouts.clinic>

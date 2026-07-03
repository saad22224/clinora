<x-layouts.clinic>
    <x-slot:title>نظرة عامة</x-slot>

    {{-- Top Barcode Banner --}}
    <div class="mb-6 bg-gradient-to-r from-emerald-900 via-teal-800 to-brand-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 border border-white/10">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        
        <div class="flex-1 text-center md:text-right z-10 space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-emerald-300 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                باركود الحجز الذكي للعيادة
            </div>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
                {{ $clinic->name }}
            </h2>
            <p class="text-emerald-100 text-sm max-w-xl leading-relaxed">
                اعرض هذا الباركود في استقبال العيادة أو اطبعه ليتمكن مرضاك من مسحه بهواتفهم والدخول لصفحة العيادة وحجز مواعيدهم وإدارة ملفاتهم الطبية مباشرة.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3">
                <button onclick="printClinicQR()" type="button"
                    class="inline-flex items-center gap-2 bg-white text-teal-900 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-emerald-50 shadow-md transition-all duration-200 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة بطاقة الباركود
                </button>
                <a href="{{ route('clinic.public.show', $clinic->slug) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-5 py-2.5 rounded-xl font-bold text-sm backdrop-blur-md transition-all duration-200">
                    <span>زيارة صفحة الهبوط</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        <div class="z-10 shrink-0 flex flex-col items-center bg-white p-4 rounded-2xl shadow-xl border border-white/20 text-gray-900">
            <div class="bg-white p-1 rounded-xl">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate(route('clinic.public.show', $clinic->slug)) !!}
            </div>
            <span class="text-[11px] font-bold text-gray-500 mt-2">امسح الكود عبر الكاميرا</span>
        </div>
    </div>

    {{-- Top Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Patients --}}
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 px-2 py-1 rounded-md">مرضى</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المرضى</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_patients']) }}</h3>
        </div>

        {{-- Today Appointments --}}
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 dark:bg-orange-500/10 dark:text-orange-400 px-2 py-1 rounded-md">اليوم</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مواعيد اليوم</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['today_appointments']) }}</h3>
        </div>

        {{-- Revenue with filter --}}
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow relative group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <form action="{{ route('clinic.dashboard') }}" method="GET" class="z-10">
                    <select name="filter" onchange="this.form.submit()" class="text-xs bg-gray-50 border border-gray-200 text-gray-700 py-1 px-2 rounded-lg cursor-pointer outline-none hover:bg-gray-100 dark:bg-white/5 dark:border-white/10 dark:text-gray-300 transition-colors appearance-none">
                        <option value="1" {{ $stats['current_filter'] == '1' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="2" {{ $stats['current_filter'] == '2' ? 'selected' : '' }}>شهران</option>
                        <option value="3" {{ $stats['current_filter'] == '3' ? 'selected' : '' }}>3 أشهر</option>
                    </select>
                </form>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stats['revenue_label'] }}</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['revenue_amount'], 0) }} <span class="text-sm font-normal text-gray-400">ج.م</span></h3>
        </div>

        {{-- Pending --}}
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @if($stats['pending_appointments'] > 0)
                    <a href="{{ route('clinic.appointments.index', ['status'=>'pending']) }}" class="text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400 px-2 py-1 rounded-md hover:underline">مراجعة</a>
                @endif
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">تنتظر التأكيد</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['pending_appointments']) }}</h3>
        </div>
    </div>

    {{-- Secondary Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">مواعيد مؤكدة</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($stats['confirmed_appointments']) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">مواعيد مكتملة</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($stats['completed_appointments']) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">ملغاة هذا الشهر</p>
                <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($stats['cancelled_appointments']) }}</p>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Upcoming Appointments --}}
        <div class="lg:col-span-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-white/5 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-gray-900 dark:text-white">المواعيد القادمة</h3>
                <a href="{{ route('clinic.appointments.index') }}" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700">&larr; عرض الكل</a>
            </div>
            @if($upcoming_appointments->isEmpty())
                <div class="flex-1 p-10 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">لا توجد مواعيد قادمة</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">الجدول فارغ حالياً.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-white/5 overflow-y-auto max-h-[400px]">
                    @foreach($upcoming_appointments as $appointment)
                        <div class="p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ mb_substr($appointment->patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $appointment->patient->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $appointment->scheduled_at->format('Y-m-d') }}
                                        @if($appointment->visitType) • {{ $appointment->visitType->name }} @endif
                                    </p>
                                </div>
                            </div>
                            @php $s = ['pending'=>'bg-amber-100 text-amber-700','confirmed'=>'bg-blue-100 text-blue-700'][$appointment->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $s }} dark:bg-opacity-20">
                                {{ $appointment->status === 'pending' ? 'بانتظار التأكيد' : 'مؤكد' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="flex flex-col gap-6">
            {{-- Quick Actions --}}
            <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                <h3 class="text-lg font-bold mb-2">إجراء سريع</h3>
                <p class="text-brand-100 text-sm mb-6 leading-relaxed">إضافة مريض أو موعد بضغطة واحدة.</p>
                <div class="space-y-3 relative z-10">
                    <a href="{{ route('clinic.patients.index') }}" class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl py-2.5 px-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        تسجيل مريض جديد
                    </a>
                    <a href="{{ route('clinic.appointments.index') }}" class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl py-2.5 px-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        إضافة موعد جديد
                    </a>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm flex-1">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">آخر المواعيد</h3>
                @if($recent_appointments->isEmpty())
                    <div class="flex items-center justify-center h-32">
                        <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد نشاطات بعد.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recent_appointments as $apt)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center text-brand-600 dark:text-brand-400 font-bold text-xs shrink-0">
                                    {{ mb_substr($apt->patient?->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $apt->patient?->name ?? 'مريض' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $apt->scheduled_at->format('Y/m/d') }} • {{ $apt->visitType?->name ?? '—' }}</p>
                                </div>
                                @php $c=['pending'=>'text-amber-600','confirmed'=>'text-blue-600','completed'=>'text-emerald-600'][$apt->status]??'text-gray-400'; @endphp
                                <span class="w-2 h-2 rounded-full bg-current {{ $c }} shrink-0"></span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Printable QR Area (Visible only when printing) --}}
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #printable-qr-section {
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                visibility: visible !important;
                position: absolute !important;
                left: 0;
                top: 0;
                width: 100%;
                background: #ffffff !important;
                color: #000000 !important;
                padding: 40px !important;
                z-index: 999999 !important;
            }
            #printable-qr-section * {
                visibility: visible !important;
            }
        }
    </style>
    <div id="printable-qr-section" class="hidden text-center">
        <div class="border-4 border-[#0f766e] rounded-3xl p-10 max-w-md mx-auto my-8 bg-white shadow-none text-center">
            <h1 class="text-3xl font-black text-[#0f766e] mb-1">{{ $clinic->name }}</h1>
            @if($clinic->doctor_title)
                <p class="text-base font-bold text-gray-600 mb-6">{{ $clinic->doctor_title }}</p>
            @endif
            <div class="inline-block p-6 bg-white border-2 border-dashed border-gray-300 rounded-2xl mb-6">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->margin(1)->generate(route('clinic.public.show', $clinic->slug)) !!}
            </div>
            <h2 class="text-xl font-black text-gray-900 mb-2">امسح الباركود لحجز موعدك الآن</h2>
            <p class="text-sm text-gray-600 mb-6">وجه كاميرا هاتفك نحو الرمز للانتقال لصفحة الحجز المباشر</p>
            @if($clinic->phone)
                <div class="pt-4 border-t border-gray-200 text-sm font-bold text-gray-700">
                    هاتـف: {{ $clinic->phone }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function printClinicQR() {
            window.print();
        }
    </script>
</x-layouts.clinic>

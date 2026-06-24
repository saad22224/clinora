<x-layouts.clinic>
    <x-slot:title>نظرة عامة</x-slot>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat Card 1 -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-2 py-1 rounded-md">
                    +12% <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المرضى</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_patients']) }}</h3>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مواعيد اليوم</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['today_appointments']) }}</h3>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow relative group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <!-- Filter Dropdown -->
                <form action="{{ route('clinic.dashboard') }}" method="GET" class="absolute top-4 left-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                    <select name="filter" onchange="this.form.submit()" class="text-xs bg-gray-50 border border-gray-200 text-gray-700 py-1 pl-6 pr-2 rounded-lg cursor-pointer outline-none hover:bg-gray-100 dark:bg-white/5 dark:border-white/10 dark:text-gray-300 transition-colors appearance-none">
                        <option value="1" {{ $stats['current_filter'] == '1' ? 'selected' : '' }}>آخر شهر</option>
                        <option value="2" {{ $stats['current_filter'] == '2' ? 'selected' : '' }}>آخر شهرين</option>
                        <option value="3" {{ $stats['current_filter'] == '3' ? 'selected' : '' }}>آخر 3 أشهر</option>
                    </select>
                </form>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stats['revenue_label'] }}</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['revenue_amount'], 2) }} <span class="text-sm font-normal text-gray-400">ج.م</span></h3>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المواعيد الملغاة (هذا الشهر)</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['cancelled_appointments']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upcoming Appointments -->
        <div class="lg:col-span-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-white/5 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-gray-900 dark:text-white">المواعيد القادمة</h3>
                <a href="{{ route('clinic.appointments.index') }}" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700">عرض الكل &larr;</a>
            </div>
            
            @if($upcoming_appointments->isEmpty())
                <div class="flex-1 p-10 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">لا توجد مواعيد قادمة</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm">الجدول فارغ حالياً، قم بإضافة موعد جديد للبدء.</p>
                    <button class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        موعد جديد
                    </button>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-white/5 overflow-y-auto max-h-[400px]">
                    @foreach($upcoming_appointments as $appointment)
                        <div class="p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                                    {{ mb_substr($appointment->patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $appointment->patient->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $appointment->scheduled_at->format('Y-m-d \a\t h:i A') }} • الطبيب: {{ $appointment->doctor->name ?? 'غير محدد' }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                قادم
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Quick Actions / Mini Reports -->
        <div class="flex flex-col gap-6">
            <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                <h3 class="text-lg font-bold mb-2">إجراء سريع</h3>
                <p class="text-brand-100 text-sm mb-6 leading-relaxed">أضف مريض جديد أو قم بإنشاء فاتورة بضغطة زر واحدة.</p>
                <div class="space-y-3 relative z-10">
                    <button class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl py-2.5 px-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        تسجيل مريض جديد
                    </button>
                    <button class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl py-2.5 px-4 text-sm font-bold transition-colors flex items-center justify-center gap-2 backdrop-blur-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        إنشاء فاتورة
                    </button>
                </div>
            </div>
            
            <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-6 shadow-sm flex-1">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">نشاطات أخيرة</h3>
                <div class="flex items-center justify-center h-40">
                    <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد نشاطات مسجلة اليوم.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.clinic>

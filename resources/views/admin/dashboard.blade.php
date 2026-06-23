<x-layouts.admin>
    <x-slot:title>نظرة عامة على المنصة</x-slot>

    <!-- Top Stats Row (Admin) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat Card 1 -->
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-2 py-1 rounded-md">
                    +{{ $stats['new_clinics_this_month'] }} هذا الشهر
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">إجمالي العيادات المسجلة</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total_clinics']) }}</h3>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">الاشتراكات النشطة</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['active_subscriptions']) }}</h3>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">إجمالي إيرادات المنصة</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total_revenue'], 2) }} <span class="text-sm font-normal text-slate-400">USD</span></h3>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">تذاكر الدعم المفتوحة</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">0</h3>
            </div>
        </div>
    </div>

    <!-- Main Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Clinics -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/5 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-slate-900 dark:text-white">أحدث العيادات المسجلة</h3>
                <a href="{{ route('admin.clinics.index') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700">عرض الكل &larr;</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/5">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">اسم العيادة</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">الرابط (Slug)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">حالة الاشتراك</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-[#0f1115] divide-y divide-slate-200 dark:divide-white/5">
                        @forelse($recent_clinics as $clinic)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xs">
                                            {{ mb_substr($clinic->name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $clinic->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ $clinic->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($clinic->subscription && $clinic->subscription->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            نشط ({{ $clinic->subscription->plan }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">
                                            منتهي
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ $clinic->created_at->format('Y-m-d') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد عيادات مسجلة حتى الآن.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm flex flex-col">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-white/5 shrink-0">
                <h3 class="font-bold text-slate-900 dark:text-white">حالة النظام</h3>
            </div>
            <div class="p-6 flex-1 space-y-6">
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">استهلاك الخادم (CPU)</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">12%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: 12%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">استهلاك الذاكرة (RAM)</span>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">45%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">مساحة التخزين</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">28%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: 28%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

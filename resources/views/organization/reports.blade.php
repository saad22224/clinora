<x-layouts.organization :organization="$organization" title="تقارير السلسلة">
    <form method="GET" class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-4 sm:p-5 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">من تاريخ</label>
            <input type="date" name="date_from" value="{{ $date_from }}" class="form-control">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ $date_to }}" class="form-control">
        </div>
        <div class="md:col-span-2 flex gap-2">
            <button class="flex-1 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold transition-colors">تطبيق الفترة</button>
            <a href="{{ route('organization.reports.index', $organization) }}" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">إعادة ضبط</a>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 px-2 py-1 rounded-md">محصّل</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الإيرادات المحصلة</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totals['collected_income'], 2) }}</h3>
        </div>

        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400 px-2 py-1 rounded-md">مصروف</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المصروفات</p>
            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($totals['expenses'], 2) }}</h3>
        </div>

        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-600 bg-brand-50 dark:bg-brand-500/10 dark:text-brand-400 px-2 py-1 rounded-md">صافي</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي النتيجة</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($totals['net_cash'], 2) }}</h3>
        </div>
    </div>

    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06]">
            <h3 class="font-black text-gray-900 dark:text-white text-lg">مقارنة الفروع</h3>
            <p class="text-sm text-gray-500 mt-0.5">أداء كل عيادة خلال الفترة المحددة</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">العيادة</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">المرضى</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">المواعيد</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">محصّل</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">مصروف</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">صافي</th>
                        <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">مستحق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.04]">
                    @forelse($branches as $branch)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $branch['clinic_name'] }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($branch['patients_count']) }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($branch['appointments_count']) }}</td>
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($branch['collected_income'], 2) }}</td>
                            <td class="px-6 py-4 font-bold text-rose-600 dark:text-rose-400">{{ number_format($branch['expenses'], 2) }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ number_format($branch['net_cash'], 2) }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($branch['outstanding'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">لا توجد بيانات فروع لهذه الفترة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.organization>

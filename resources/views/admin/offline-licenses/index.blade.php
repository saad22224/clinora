<x-layouts.admin>
    <x-slot:title>تراخيص Offline</x-slot:title>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/30 text-sm font-semibold text-rose-700 dark:text-rose-300">{{ session('error') }}</div>
    @endif

    @unless($keyStatus['configured'] && $keyStatus['is_production'])
        <div class="px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 text-sm text-amber-900 dark:text-amber-200">
            <p class="font-black mb-1">تحذير مفتاح التوقيع</p>
            <p>{{ $keyStatus['message'] }}</p>
            <a href="{{ route('admin.offline-licenses.keys') }}" class="inline-block mt-2 font-bold text-amber-800 dark:text-amber-100 underline">حالة المفتاح والإرشادات</a>
        </div>
    @endunless

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] p-5">
            <p class="text-xs font-bold text-slate-500">إجمالي الإصدارات</p>
            <p class="text-2xl font-black mt-1">{{ $counts['total'] }}</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] p-5">
            <p class="text-xs font-bold text-slate-500">سارية الآن</p>
            <p class="text-2xl font-black mt-1 text-emerald-600">{{ $counts['active'] }}</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] p-5">
            <p class="text-xs font-bold text-slate-500">تنتهي خلال 30 يومًا</p>
            <p class="text-2xl font-black mt-1 text-amber-600">{{ $counts['expiring_soon'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-white/[0.06] flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-lg text-slate-900 dark:text-white">التراخيص الصادرة</h3>
                <p class="text-sm text-slate-500 mt-0.5">تراخيص Clinora Offline الموقّعة — منفصلة عن اشتراكات SaaS</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.offline-licenses.keys') }}" class="px-4 py-2 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5">حالة المفتاح</a>
                <a href="{{ route('admin.offline-licenses.create') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500">إصدار ترخيص</a>
            </div>
        </div>

        <form method="GET" class="p-4 flex flex-wrap gap-2 border-b border-slate-100 dark:border-white/[0.06]">
            <input type="search" name="q" value="{{ $search }}" placeholder="بحث برقم الترخيص أو اسم العيادة" class="flex-1 min-w-[200px] rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
            <select name="status" class="rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                @foreach(['active' => 'ساري', 'not_started' => 'لم يبدأ', 'grace' => 'سماح', 'expired' => 'منتهٍ', 'replaced' => 'استُبدل'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-xl text-sm font-bold bg-slate-900 text-white dark:bg-white dark:text-slate-900">تصفية</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-white/[0.03] text-slate-500">
                    <tr>
                        <th class="text-right px-4 py-3 font-bold">الترخيص</th>
                        <th class="text-right px-4 py-3 font-bold">العيادة</th>
                        <th class="text-right px-4 py-3 font-bold">الانتهاء</th>
                        <th class="text-right px-4 py-3 font-bold">الحالة</th>
                        <th class="text-right px-4 py-3 font-bold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.06]">
                    @forelse($licenses as $license)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs" dir="ltr">{{ $license->license_id }}</td>
                            <td class="px-4 py-3 font-bold">{{ $license->clinic_name }}</td>
                            <td class="px-4 py-3">{{ $license->expires_at->toDateString() }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-black
                                    {{ $license->computedStatus() === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ in_array($license->computedStatus(), ['grace', 'not_started'], true) ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                    {{ in_array($license->computedStatus(), ['expired', 'replaced'], true) ? 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300' : '' }}
                                ">{{ $license->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.offline-licenses.show', $license) }}" class="font-bold text-indigo-600 dark:text-indigo-400">تفاصيل</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد تراخيص Offline صادرة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($licenses->hasPages())
            <div class="p-4">{{ $licenses->links() }}</div>
        @endif
    </div>
</x-layouts.admin>

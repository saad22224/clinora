<x-layouts.clinic>
    <x-slot:title>التقارير</x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">تقارير العيادة</h1>
            <p class="text-sm text-gray-500 mt-1">الإيرادات المحصلة من سجل المالية فقط، وقيمة الخدمات المكتملة من المواعيد بشكل منفصل.</p>
        </div>

        <form method="GET" class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="date" name="date_from" value="{{ $date_from }}" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
            <input type="date" name="date_to" value="{{ $date_to }}" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
            <button class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold">تطبيق الفترة</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] p-5">
                <p class="text-xs font-bold text-gray-500">الإيرادات المحصلة</p>
                <p class="text-2xl font-black mt-2 text-emerald-600">{{ number_format($metrics['collected_income'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] p-5">
                <p class="text-xs font-bold text-gray-500">المصروفات</p>
                <p class="text-2xl font-black mt-2 text-rose-600">{{ number_format($metrics['expenses'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] p-5">
                <p class="text-xs font-bold text-gray-500">صافي النتيجة</p>
                <p class="text-2xl font-black mt-2">{{ number_format($metrics['net_cash'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] p-5">
                <p class="text-xs font-bold text-gray-500">قيمة الخدمات المكتملة</p>
                <p class="text-2xl font-black mt-2 text-brand-600">{{ number_format($metrics['completed_service_value'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] p-5">
                <p class="text-xs font-bold text-gray-500">مستحقات غير محصلة</p>
                <p class="text-2xl font-black mt-2 text-amber-600">{{ number_format($metrics['outstanding'], 2) }}</p>
            </div>
        </div>
    </div>
</x-layouts.clinic>

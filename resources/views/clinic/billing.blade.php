<x-layouts.clinic>
    <x-slot:title>المالية</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">الحركة المالية</h1>
                <p class="text-sm text-gray-500 mt-1">تسجيل الإيرادات المحصلة والمصروفات بشكل منفصل عن أسعار المواعيد.</p>
            </div>
            <button type="button" data-modal-open="finance-create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold shadow-sm">
                إضافة حركة مالية
            </button>
        </div>

        @if(session('success'))
            <div data-flash class="rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-3 text-sm font-bold">{{ session('success') }}</div>
        @endif

        <form method="GET" class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="type" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <option value="">كل الأنواع</option>
                <option value="income" @selected(request('type') === 'income')>إيراد</option>
                <option value="expense" @selected(request('type') === 'expense')>مصروف</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الوصف أو التصنيف" class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
            <button class="md:col-span-4 sm:w-auto w-full px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-bold">تصفية</button>
        </form>

        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-white/5 text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-right font-bold">التاريخ</th>
                            <th class="px-4 py-3 text-right font-bold">النوع</th>
                            <th class="px-4 py-3 text-right font-bold">المبلغ</th>
                            <th class="px-4 py-3 text-right font-bold">الوصف</th>
                            <th class="px-4 py-3 text-right font-bold">التصنيف</th>
                            <th class="px-4 py-3 text-right font-bold">المريض</th>
                            <th class="px-4 py-3 text-right font-bold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                        @forelse($finances as $finance)
                            <tr>
                                <td class="px-4 py-3">{{ $finance->transaction_date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $finance->type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $finance->type === 'income' ? 'إيراد' : 'مصروف' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold">{{ number_format((float) $finance->amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $finance->description }}</td>
                                <td class="px-4 py-3">{{ $finance->category ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $finance->patient?->name ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('clinic.billing.destroy', $finance) }}" data-confirm="هل أنت متأكد من الحذف؟">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-rose-600 text-xs font-bold">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">لا توجد حركات مالية بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $finances->links() }}</div>
        </div>
    </div>

    <div id="finance-create" data-modal class="hidden fixed inset-0 z-50 bg-black/50 opacity-0 flex items-center justify-center p-4">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] rounded-2xl w-full max-w-lg p-6 scale-95 opacity-0 transition">
            <h3 class="text-lg font-black mb-4">إضافة حركة مالية</h3>
            <form method="POST" action="{{ route('clinic.billing.store') }}" class="space-y-3">
                @csrf
                <select name="type" required class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                    <option value="income">إيراد محصل</option>
                    <option value="expense">مصروف</option>
                </select>
                <input type="number" step="0.01" min="0.01" name="amount" required placeholder="المبلغ" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <input type="text" name="description" required placeholder="الوصف" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <input type="text" name="category" placeholder="التصنيف (اختياري)" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <input type="text" name="payment_method" placeholder="طريقة الدفع (اختياري)" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                <select name="patient_id" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                    <option value="">بدون مريض</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>
                <select name="appointment_id" class="w-full rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5">
                    <option value="">ربط بموعد مستحق (اختياري)</option>
                    @foreach($appointments as $appointment)
                        <option value="{{ $appointment->id }}">
                            #{{ $appointment->id }} — {{ $appointment->patient?->name }} — {{ number_format((float) $appointment->price, 2) }}
                        </option>
                    @endforeach
                </select>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" data-modal-close="finance-create" class="px-4 py-2 rounded-xl text-sm font-bold text-gray-500">إلغاء</button>
                    <button class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.clinic>

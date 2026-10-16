<x-layouts.clinic>
    <x-slot:title>المالية</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">الحركة المالية</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">تسجيل الإيرادات المحصلة والمصروفات بشكل منفصل عن أسعار المواعيد.</p>
            </div>
            <button type="button" data-modal-open="finance-create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold shadow-sm">
                إضافة حركة مالية
            </button>
        </div>

        @if(session('success'))
            <div data-flash class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-700/30 px-4 py-3 text-sm font-bold">{{ session('success') }}</div>
        @endif

        <form method="GET" class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="type" class="form-control">
                <option value="">كل الأنواع</option>
                <option value="income" @selected(request('type') === 'income')>إيراد</option>
                <option value="expense" @selected(request('type') === 'expense')>مصروف</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الوصف أو التصنيف" class="form-control">
            <button type="submit" class="md:col-span-4 sm:w-auto w-full px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white/10 dark:hover:bg-white/15 text-white text-sm font-bold">تصفية</button>
        </form>

        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
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
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06] text-gray-800 dark:text-gray-200">
                        @forelse($finances as $finance)
                            <tr>
                                <td class="px-4 py-3">{{ $finance->transaction_date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $finance->type === 'income' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400' }}">
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
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 text-xs font-bold">حذف</button>
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
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] rounded-2xl w-full max-w-lg p-6 scale-95 opacity-0 transition border border-gray-100 dark:border-white/10">
            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">إضافة حركة مالية</h3>
            <form method="POST" action="{{ route('clinic.billing.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label">النوع *</label>
                    <select name="type" required class="form-control">
                        <option value="income">إيراد محصل</option>
                        <option value="expense">مصروف</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">المبلغ *</label>
                    <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="form-control">
                </div>
                <div>
                    <label class="form-label">الوصف *</label>
                    <input type="text" name="description" required placeholder="الوصف" class="form-control">
                </div>
                <div>
                    <label class="form-label">التصنيف</label>
                    <input type="text" name="category" placeholder="اختياري" class="form-control">
                </div>
                <div>
                    <label class="form-label">طريقة الدفع</label>
                    <input type="text" name="payment_method" placeholder="اختياري" class="form-control">
                </div>
                <div>
                    <label class="form-label">تاريخ الحركة *</label>
                    <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">المريض</label>
                    <select name="patient_id" class="form-control">
                        <option value="">بدون مريض</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">ربط بموعد</label>
                    <select name="appointment_id" class="form-control">
                        <option value="">اختياري</option>
                        @foreach($appointments as $appointment)
                            <option value="{{ $appointment->id }}">
                                #{{ $appointment->id }} — {{ $appointment->patient?->name }} — {{ number_format((float) $appointment->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" data-modal-close="finance-create" class="px-4 py-2 rounded-xl text-sm font-bold text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.clinic>

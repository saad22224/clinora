<x-layouts.clinic>
    <x-slot:title>المواعيد</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())<div class="server-error hidden"></div>@endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">إدارة المواعيد</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $appointments->total() }} موعد</p>
            </div>
            <button data-modal-open="modal-add-appointment"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة موعد
            </button>
        </div>

        @if($appointments->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد مواعيد</h4>
                <p class="text-sm text-gray-400 max-w-xs mb-5">ابدأ بجدولة أول موعد لعيادتك.</p>
                @if($patients->isNotEmpty())
                    <button data-modal-open="modal-add-appointment" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">إضافة موعد</button>
                @else
                    <p class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-4 py-2 rounded-xl font-medium">⚠ أضف مرضى أولاً من <a href="{{ route('clinic.patients.index') }}" class="underline">صفحة المرضى</a> قبل إنشاء مواعيد.</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">المريض</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">تاريخ الموعد</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">نوع الزيارة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الحالة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">ملاحظات</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                        @foreach($appointments as $apt)
                            @php
                                $statusMap = [
                                    'pending'   => ['label' => 'انتظار',  'class' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'],
                                    'confirmed' => ['label' => 'مؤكد',   'class' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'],
                                    'completed' => ['label' => 'مكتمل',  'class' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'],
                                    'cancelled' => ['label' => 'ملغي',   'class' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'],
                                ];
                                $s = $statusMap[$apt->status] ?? ['label' => $apt->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-sm shrink-0">
                                            {{ mb_substr($apt->patient?->name ?? '?', 0, 1) }}
                                        </div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $apt->patient?->name ?? 'مريض محذوف' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 dark:text-gray-200">{{ $apt->scheduled_at->format('Y/m/d') }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $apt->scheduled_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($apt->visitType)
                                        <p class="font-bold text-gray-800 dark:text-gray-200">{{ $apt->visitType->name }}</p>
                                        <p class="text-xs text-brand-600 dark:text-brand-400 font-bold mt-0.5">{{ number_format($apt->price ?? $apt->visitType->price, 0) }} ج.م</p>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $s['class'] }}">{{ $s['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-sm max-w-[180px] truncate">{{ $apt->notes ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($apt->status === 'pending')
                                        <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">تأكيد</button>
                                        </form>
                                        @endif
                                        @if(in_array($apt->status, ['pending', 'confirmed']))
                                        <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">إكمال</button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{ route('clinic.appointments.destroy', $apt) }}"
                                              data-confirm="هل تريد حذف هذا الموعد؟">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $appointments->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Add Appointment Modal --}}
    <div id="modal-add-appointment" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة موعد جديد</h3>
                    <p class="text-xs text-gray-400 mt-0.5">اختر المريض وحدد تاريخ ووقت الموعد</p>
                </div>
                <button data-modal-close="modal-add-appointment" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.appointments.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">المريض <span class="text-red-500">*</span></label>
                        @if($patients->isEmpty())
                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-sm text-amber-700 dark:text-amber-400">
                                ⚠ لا يوجد مرضى مسجلون. <a href="{{ route('clinic.patients.index') }}" class="underline font-bold">أضف مرضى أولاً</a>
                            </div>
                        @else
                            <select name="patient_id" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                <option value="">— اختر مريض —</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ ووقت الموعد <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">نوع الزيارة / الخدمة</label>
                            <select name="visit_type_id" id="visit_type_select" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                <option value="" data-price="">— اختر نوع الزيارة (اختياري) —</option>
                                @foreach($visitTypes as $vt)
                                    <option value="{{ $vt->id }}" data-price="{{ $vt->price }}" {{ old('visit_type_id') == $vt->id ? 'selected' : '' }}>{{ $vt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">السعر</label>
                            <div class="relative">
                                <input type="number" name="price" id="visit_price_input" value="{{ old('price') }}" min="0" step="0.01"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-12 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white dir-ltr">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-bold pointer-events-none">EGP</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none"
                                  placeholder="أي ملاحظات خاصة بهذا الموعد...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-appointment" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">حفظ الموعد</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        window.reopenModalOnError('modal-add-appointment');

        // Auto-fill price based on visit type
        document.getElementById('visit_type_select')?.addEventListener('change', function() {
            const priceInput = document.getElementById('visit_price_input');
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price !== null && price !== '') {
                priceInput.value = price;
            }
        });
    </script>
    @endpush
</x-layouts.clinic>

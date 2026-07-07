<x-layouts.clinic>
    <x-slot:title>المواعيد</x-slot>

    @php
        $sourceLabels = [
            'website_landing_page'   => ['label' => 'صفحة الويب', 'class' => 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400'],
            'reception_manual_entry' => ['label' => 'الاستقبال', 'class' => 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300'],
        ];
        $statusMap = [
            'pending'   => ['label' => 'بانتظار التأكيد', 'class' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'],
            'confirmed' => ['label' => 'مؤكد',           'class' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'],
            'completed' => ['label' => 'مكتمل',          'class' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'],
            'rejected'  => ['label' => 'مرفوض',          'class' => 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400'],
            'cancelled' => ['label' => 'ملغي',           'class' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'],
        ];
    @endphp

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach([
            ['label'=>'الإجمالي',     'value'=>$summary['total'],     'icon'=>'calendar',  'color'=>'brand'],
            ['label'=>'بانتظار التأكيد','value'=>$summary['pending'], 'icon'=>'clock',     'color'=>'amber'],
            ['label'=>'مؤكد',         'value'=>$summary['confirmed'], 'icon'=>'check',     'color'=>'blue'],
            ['label'=>'مكتمل',        'value'=>$summary['completed'], 'icon'=>'check-all', 'color'=>'emerald'],
            ['label'=>'ملغي/مرفوض',  'value'=>$summary['cancelled'], 'icon'=>'x',         'color'=>'red'],
        ] as $card)
        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-4 shadow-sm">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">{{ $card['label'] }}</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($card['value']) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters + Actions --}}
    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-black text-gray-900 dark:text-white text-lg">إدارة المواعيد</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $appointments->total() }} موعد</p>
                </div>
                <button data-modal-open="modal-add-appointment"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                    إضافة موعد
                </button>
            </div>

            <form method="GET" action="{{ route('clinic.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث باسم المريض أو الهاتف..."
                       class="lg:col-span-1 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none">
                <select name="source" class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 outline-none">
                    <option value="">كل المصادر</option>
                    <option value="website_landing_page"   @selected(request('source')==='website_landing_page')>صفحة الويب</option>
                    <option value="reception_manual_entry" @selected(request('source')==='reception_manual_entry')>الاستقبال</option>
                </select>
                <select name="status" class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 outline-none">
                    <option value="">كل الحالات</option>
                    @foreach(['pending'=>'بانتظار التأكيد','confirmed'=>'مؤكد','completed'=>'مكتمل','cancelled'=>'ملغي','rejected'=>'مرفوض'] as $v=>$l)
                        <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
                    @endforeach
                </select>
                <select name="visit_type" class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 outline-none">
                    <option value="">كل الخدمات</option>
                    @foreach($visitTypes as $vt)
                        <option value="{{ $vt->id }}" @selected(request('visit_type')==$vt->id)>{{ $vt->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" title="من تاريخ"
                           class="flex-1 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm outline-none dark:text-white">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="إلى تاريخ"
                           class="flex-1 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm outline-none dark:text-white">
                </div>
                <div class="sm:col-span-2 lg:col-span-5 flex gap-2 justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">بحث وتصفية</button>
                    @if(request()->hasAny(['search','source','status','visit_type','date_from','date_to']))
                        <a href="{{ route('clinic.appointments.index') }}" class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-brand-600 rounded-xl border border-gray-200 dark:border-white/10 transition-colors">مسح الفلاتر</a>
                    @endif
                </div>
            </form>
        </div>

        @if($appointments->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد مواعيد</h4>
                <p class="text-sm text-gray-400 max-w-xs mb-5">لا توجد نتائج تطابق الفلاتر المحددة.</p>
                @if($patients->isNotEmpty())
                    <button data-modal-open="modal-add-appointment" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">إضافة موعد</button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">المريض</th>
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">التاريخ</th>
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">المصدر</th>
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الخدمة / السعر</th>
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الحالة</th>
                            <th class="text-right px-5 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">إجراءات</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                        @foreach($appointments as $apt)
                            @php
                                $status      = $statusMap[$apt->status] ?? ['label' => $apt->status, 'class' => 'bg-gray-100 text-gray-600'];
                                $srcInfo     = $sourceLabels[$apt->source ?? 'reception_manual_entry'] ?? $sourceLabels['reception_manual_entry'];
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-5 py-4">
                                    @if($apt->patient)
                                    <a href="{{ route('clinic.patients.medical-file', $apt->patient) }}" class="flex items-center gap-3 group/link">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-sm shrink-0 shadow-sm group-hover/link:scale-105 transition-transform">
                                            {{ mb_substr($apt->patient->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white group-hover/link:text-brand-600 dark:group-hover/link:text-brand-400 transition-colors">{{ $apt->patient->name }}</p>
                                            @if($apt->patient->phone)
                                                <p class="text-xs text-gray-400 dir-ltr text-right">{{ $apt->patient->phone }}</p>
                                            @endif
                                        </div>
                                    </a>
                                    @else
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gray-400 flex items-center justify-center font-bold text-white text-sm shrink-0">؟</div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">مريض محذوف</p>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="font-bold text-gray-800 dark:text-gray-200">{{ $apt->scheduled_at->format('Y/m/d') }}</p>
                                    <p class="text-xs text-gray-400">{{ $apt->scheduled_at->translatedFormat('l') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $srcInfo['class'] }}">{{ $srcInfo['label'] }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($apt->visitType)
                                        <p class="font-bold text-gray-800 dark:text-gray-200">{{ $apt->visitType->name }}</p>
                                        <p class="text-xs text-brand-600 dark:text-brand-400 font-bold mt-0.5">{{ number_format($apt->price ?? $apt->visitType->price, 0) }} ج.م</p>
                                    @else
                                        <span class="text-gray-400 text-xs">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="px-5 py-4 min-w-64">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($apt->status === 'pending')
                                            <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">تأكيد</button>
                                            </form>
                                            <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">رفض</button>
                                            </form>
                                        @endif
                                        @if(in_array($apt->status, ['pending','confirmed']))
                                            <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}" class="flex items-center gap-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $apt->status }}">
                                                <input type="date" name="scheduled_at" value="{{ $apt->scheduled_at->format('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                                       class="w-36 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">إعادة جدولة</button>
                                            </form>
                                        @endif
                                        @if($apt->status === 'confirmed')
                                            <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">إكمال</button>
                                            </form>
                                        @endif
                                        @if(in_array($apt->status, ['pending','confirmed']))
                                            <form method="POST" action="{{ route('clinic.appointments.update-status', $apt) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('clinic.appointments.destroy', $apt) }}" data-confirm="هل تريد حذف هذا الموعد؟">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $appointments->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Add Appointment Modal --}}
    <div id="modal-add-appointment" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة موعد يدوي</h3>
                    <p class="text-xs text-gray-400 mt-0.5">المصدر: الاستقبال — الحالة: مؤكد تلقائياً</p>
                </div>
                <button data-modal-close="modal-add-appointment" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                                لا يوجد مرضى. <a href="{{ route('clinic.patients.index') }}" class="underline font-bold">أضف مرضى أولاً</a>
                            </div>
                        @else
                            <select name="patient_id" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                <option value="">اختر مريضاً</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" @selected(old('patient_id') == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ الموعد <span class="text-red-500">*</span></label>
                        <input type="date" name="scheduled_at" value="{{ old('scheduled_at', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الخدمة <span class="text-red-500">*</span></label>
                        <select name="visit_type_id" id="apt_visit_type" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                            <option value="" data-price="">اختر الخدمة</option>
                            @foreach($visitTypes as $vt)
                                <option value="{{ $vt->id }}" data-price="{{ $vt->price }}" @selected(old('visit_type_id') == $vt->id)>
                                    {{ $vt->name }} — {{ number_format($vt->price, 0) }} ج.م
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">السعر يُحدَّد تلقائياً من الخدمة المختارة</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none">{{ old('notes') }}</textarea>
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
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-appointment');
        });
        @endif
    </script>
    @endpush
</x-layouts.clinic>

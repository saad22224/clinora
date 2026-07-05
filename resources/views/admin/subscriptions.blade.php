<x-layouts.admin>
    <x-slot:title>الباقات والاشتراكات</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-sm font-semibold text-red-600 dark:text-red-400 space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- ══════ SECTION 1: CLINIC SUBSCRIPTIONS TABLE ══════ --}}
    <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm dark:shadow-xl">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-lg">اشتراكات العيادات الحالية</h3>
                <p class="text-sm text-slate-500 mt-0.5">إدارة وتسكين العيادات في الباقات المتاحة</p>
            </div>
            <button data-modal-open="modal-assign-subscription"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-sm hover:shadow-indigo-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                تسكين عيادة في باقة
            </button>
        </div>

        @if(empty($subscriptions) || $subscriptions->isEmpty())
            <div class="py-16 flex flex-col items-center justify-center text-center px-6">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">لا توجد اشتراكات مسجلة</h4>
                <p class="text-sm text-slate-500 max-w-xs mb-4">قم بتسكين العيادات في الباقات المتاحة لبدء إدارة اشتراكاتها.</p>
                <button data-modal-open="modal-assign-subscription" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors">تسكين عيادة الآن</button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">العيادة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">المالك</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الباقة الحالية</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">تاريخ البداية</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($subscriptions as $sub)
                            @php $clinic = $sub->clinic; @endphp
                            @if($clinic)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-black text-white text-base shrink-0 shadow-sm">
                                            {{ mb_substr($clinic->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ $clinic->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $clinic->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($clinic->users->isNotEmpty())
                                        <p class="font-medium text-slate-800 dark:text-slate-300">{{ $clinic->users->first()?->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $clinic->users->first()?->email }}</p>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $sub->plan === 'free' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' }}">
                                        {{ $sub->plan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-xs">{{ $sub->starts_at?->format('Y/m/d') ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($sub->ends_at)
                                        <span class="{{ $sub->ends_at->isPast() ? 'text-red-500 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                            {{ $sub->ends_at->format('Y/m/d') }}
                                            @if($sub->ends_at->isPast()) (منتهي) @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400">∞ غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($sub->is_active && (!$sub->ends_at || !$sub->ends_at->isPast()))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>نشط
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>معطل / منتهي
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <button type="button" data-modal-open="modal-assign-clinic-{{ $clinic->id }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-xl transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        تغيير الباقة
                                    </button>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($subscriptions->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06]">{{ $subscriptions->links() }}</div>
            @endif
        @endif
    </div>

    {{-- ══════ SECTION 2: PLANS MANAGEMENT ══════ --}}
    <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm dark:shadow-xl">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-lg">إدارة الباقات والأسعار</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $plans->count() }} باقة متاحة</p>
            </div>
            <button data-modal-open="modal-add-plan"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-white bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة باقة جديدة
            </button>
        </div>

        <div class="p-6">
            @if($plans->isEmpty())
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">لا توجد باقات بعد</h4>
                    <p class="text-sm text-slate-500 max-w-xs mb-4">أضف أول باقة اشتراك للعيادات.</p>
                    <button data-modal-open="modal-add-plan" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors">إضافة باقة الآن</button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                        <div class="relative border border-slate-200 dark:border-white/[0.08] rounded-2xl p-5 bg-slate-50 dark:bg-white/[0.03] hover:border-indigo-500/30 transition-all group hover:bg-slate-100/50 dark:hover:bg-white/[0.05]">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h5 class="font-black text-base text-slate-900 dark:text-white">{{ $plan->name }}</h5>
                                    <p class="text-2xl font-black mt-1 text-indigo-600 dark:text-indigo-400">
                                        ${{ number_format($plan->price, 2) }}
                                        <span class="text-sm font-normal text-slate-500">/ {{ $plan->duration_in_days }} يوم</span>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold {{ $plan->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-200 text-slate-600 dark:bg-white/5 dark:text-slate-400' }}">
                                    {{ $plan->is_active ? 'نشطة' : 'معطلة' }}
                                </span>
                            </div>

                            @if(!empty($plan->features))
                                <div class="space-y-1.5 border-t border-slate-200 dark:border-white/[0.06] pt-3">
                                    @foreach($plan->features as $key => $value)
                                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $key }}: {{ $value === 'unlimited' ? 'غير محدود' : $value }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                      data-confirm="هل تريد حذف باقة '{{ $plan->name }}'؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-red-500 bg-white dark:bg-[#0A0A12] rounded-lg shadow-sm border border-slate-200 dark:border-white/10 hover:border-red-500/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ══════ MODAL: ASSIGN SUBSCRIPTION (GENERAL) ══════ --}}
    <div id="modal-assign-subscription" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex justify-between items-center bg-slate-50 dark:bg-white/[0.02]">
                <h3 class="font-black text-lg text-slate-900 dark:text-white">تسكين عيادة في باقة</h3>
                <button data-modal-close="modal-assign-subscription" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.subscriptions.assign') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اختر العيادة <span class="text-red-500">*</span></label>
                        <select name="clinic_id" required class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                            <option value="">-- اختر العيادة --</option>
                            @foreach($clinics as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->slug }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اختر الباقة <span class="text-red-500">*</span></label>
                        <select name="plan_id" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                            <option value="">باقة تجريبية / مجانية (14 يوم)</option>
                            @foreach($plans as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} — ${{ $p->price }} ({{ $p->duration_in_days }} يوم)</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1.5">💡 سيتم حساب تاريخ انتهاء الاشتراك تلقائياً بناءً على مدة الباقة المختارة.</p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active_gen" checked
                               class="w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-white/20 focus:ring-indigo-500">
                        <label for="is_active_gen" class="text-sm font-bold text-slate-700 dark:text-slate-300">تفعيل الاشتراك فورا</label>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-assign-subscription" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95">حفظ وتسكين العيادة</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════ MODALS: INDIVIDUAL CLINIC ASSIGNMENT ══════ --}}
    @if(!empty($clinics))
        @foreach($clinics as $c)
        <div id="modal-assign-clinic-{{ $c->id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
            <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex justify-between items-center bg-slate-50 dark:bg-white/[0.02]">
                    <div>
                        <h3 class="font-black text-lg text-slate-900 dark:text-white">تغيير باقة العيادة</h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $c->name }}</p>
                    </div>
                    <button data-modal-close="modal-assign-clinic-{{ $c->id }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.subscriptions.assign') }}">
                    @csrf
                    <input type="hidden" name="clinic_id" value="{{ $c->id }}">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اختر الباقة الجديدة <span class="text-red-500">*</span></label>
                            <select name="plan_id" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                                <option value="">باقة تجريبية / مجانية (14 يوم)</option>
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}" {{ ($c->subscription?->plan_id == $p->id || $c->subscription?->plan == $p->name) ? 'selected' : '' }}>
                                        {{ $p->name }} — ${{ $p->price }} ({{ $p->duration_in_days }} يوم)
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1.5">💡 سيتم حساب تاريخ الانتهاء تلقائياً بناءً على مدة الباقة المختارة.</p>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active_{{ $c->id }}" {{ ($c->subscription?->is_active ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-white/20 focus:ring-indigo-500">
                            <label for="is_active_{{ $c->id }}" class="text-sm font-bold text-slate-700 dark:text-slate-300">تفعيل الاشتراك فورا</label>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02] flex justify-end gap-3">
                        <button type="button" data-modal-close="modal-assign-clinic-{{ $c->id }}" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @endif

    {{-- ══════ MODAL: ADD NEW PLAN ══════ --}}
    <div id="modal-add-plan" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex justify-between items-center bg-slate-50 dark:bg-white/[0.02]">
                <h3 class="font-black text-lg text-slate-900 dark:text-white">إضافة باقة جديدة</h3>
                <button data-modal-close="modal-add-plan" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.plans.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اسم الباقة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                               placeholder="مثال: الباقة المتقدمة">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">السعر ($) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price', '0') }}" required min="0" step="0.01"
                                   class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">المدة (أيام) <span class="text-red-500">*</span></label>
                            <input type="number" name="duration_in_days" value="{{ old('duration_in_days', '30') }}" required min="1"
                                   class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">المميزات (JSON اختياري)</label>
                        <textarea name="features" rows="4" dir="ltr"
                                  class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white font-mono placeholder-slate-400 dark:placeholder-slate-600"
                                  placeholder='{"max_patients": 500, "support": "24/7"}'>{{ old('features') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-plan" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95">حفظ الباقة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-plan');
        });
    </script>
    @endif
    @endpush
</x-layouts.admin>

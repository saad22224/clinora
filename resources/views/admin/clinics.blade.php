<x-layouts.admin>
    <x-slot:title>إدارة العيادات</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())<div class="server-error hidden"></div>@endif

    <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm dark:shadow-xl">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-lg">إدارة العيادات</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $clinics->total() }} عيادة مسجلة</p>
            </div>
            <button data-modal-open="modal-add-clinic"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-sm hover:shadow-indigo-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة عيادة
            </button>
        </div>

        @if($clinics->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">لا توجد عيادات بعد</h4>
                <p class="text-sm text-slate-500 max-w-xs mb-5">أضف أول عيادة الآن لبدء إدارة المنصة.</p>
                <button data-modal-open="modal-add-clinic" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors">إضافة عيادة الآن</button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">العيادة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">المالك</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الاشتراك</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الإنشاء</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($clinics as $clinic)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-black text-white text-base shrink-0 shadow-md">
                                            {{ mb_substr($clinic->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ $clinic->name }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <p class="text-xs text-slate-500">{{ $clinic->slug }}</p>
                                                <a href="{{ route('clinic.public.show', $clinic->slug) }}" target="_blank"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            </div>
                                            @if($clinic->marketer)
                                                <div class="mt-1.5 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-[11px] font-bold">
                                                    <span>🎁 إحالة من:</span>
                                                    <span>{{ $clinic->marketer->name }}</span>
                                                </div>
                                            @endif
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
                                    @if($clinic->subscription)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                                {{ $clinic->subscription->plan === 'free' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' }}">
                                                {{ $clinic->subscription->plan }}
                                            </span>
                                            <button type="button" data-modal-open="modal-assign-clinic-{{ $clinic->id }}" title="تغيير الباقة"
                                                    class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1">{{ $clinic->subscription->ends_at?->format('Y/m/d') ?? '∞' }}</p>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-400 text-xs">بدون اشتراك</span>
                                            <button type="button" data-modal-open="modal-assign-clinic-{{ $clinic->id }}"
                                                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                + تسكين في باقة
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($clinic->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>نشط
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>معطل
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">{{ $clinic->created_at->format('Y/m/d') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.clinics.impersonate', $clinic) }}" title="الدخول إلى لوحة تحكم العيادة"
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <button type="button" data-modal-open="modal-assign-clinic-{{ $clinic->id }}" title="تغيير الباقة"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.clinics.toggle', $clinic) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $clinic->is_active ? 'تعطيل' : 'تفعيل' }}"
                                                    class="p-1.5 rounded-lg transition-colors {{ $clinic->is_active ? 'text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20' : 'text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20' }}">
                                                @if($clinic->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.clinics.destroy', $clinic) }}"
                                              data-confirm="هل أنت متأكد من حذف عيادة '{{ $clinic->name }}'؟">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors">
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
            @if($clinics->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06]">{{ $clinics->links() }}</div>
            @endif
        @endif
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
                                {{-- <option value="">باقة تجريبية / مجانية (14 يوم)</option> --}}
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
                            <input type="checkbox" name="is_active" value="1" id="is_active_cl_{{ $c->id }}" {{ ($c->subscription?->is_active ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded border-slate-300 dark:border-white/20 focus:ring-indigo-500">
                            <label for="is_active_cl_{{ $c->id }}" class="text-sm font-bold text-slate-700 dark:text-slate-300">تفعيل الاشتراك فورا</label>
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

    {{-- ══════ MODAL: ADD CLINIC ══════ --}}
    <div id="modal-add-clinic" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex justify-between items-center bg-slate-50 dark:bg-white/[0.02]">
                <div>
                    <h3 class="font-black text-lg text-slate-900 dark:text-white">إضافة عيادة جديدة</h3>
                    <p class="text-xs text-slate-500 mt-0.5">سيتم إنشاء حساب المالك تلقائياً</p>
                </div>
                <button data-modal-close="modal-add-clinic" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.clinics.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-600 dark:text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اسم العيادة <span class="text-red-500">*</span></label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name') }}" required
                               class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                               placeholder="مثال: عيادة الأمل الطبية">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">اسم المالك <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                                   class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                                   placeholder="د. أحمد محمد">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">الباقة</label>
                            <select name="plan_id" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                                <option value="">تجريبي مجاني (14 يوم)</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }} — ${{ $plan->price }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if(isset($marketers) && $marketers->isNotEmpty())
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">المسوق المحيل (اختياري)</label>
                            <select name="marketer_id" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white">
                                <option value="">بدون إحالة</option>
                                @foreach($marketers as $marketer)
                                    <option value="{{ $marketer->id }}" {{ old('marketer_id') == $marketer->id ? 'selected' : '' }}>{{ $marketer->name }} ({{ $marketer->referral_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" name="owner_email" value="{{ old('owner_email') }}" required
                               class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                               placeholder="doctor@clinic.com">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" name="owner_password" required minlength="8"
                               class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600"
                               placeholder="8 أحرف على الأقل">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-clinic" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95">إنشاء العيادة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-clinic');
        });
    </script>
    @endif
    @endpush
</x-layouts.admin>

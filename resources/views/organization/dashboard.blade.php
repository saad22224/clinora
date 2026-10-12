<x-layouts.organization :organization="$organization" title="لوحة السلسلة">
    {{-- Hero --}}
    <div class="mb-2 bg-gradient-to-r from-brand-900 via-teal-800 to-emerald-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-white/10">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-emerald-300 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    سلسلة عيادات
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $organization->name }}</h2>
                <p class="text-emerald-100 text-sm max-w-xl leading-relaxed">
                    أدر فروعك من مكان واحد، راقب الاستخدام مقابل الباقة، وادخل لأي عيادة بنقرة واحدة.
                </p>
            </div>

            @if($canCreateClinic)
                <button type="button" data-modal-open="create-clinic"
                    class="inline-flex items-center justify-center gap-2 bg-white text-teal-900 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-emerald-50 shadow-md transition-all duration-200 active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    إنشاء عيادة
                </button>
            @else
                <span class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-sm font-bold text-white/80">
                    تم استهلاك حد العيادات
                </span>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-600 bg-brand-50 dark:bg-brand-500/10 dark:text-brand-400 px-2 py-1 rounded-md">الباقة</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الباقة الحالية</p>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mt-1 truncate">{{ $plan?->name ?? 'غير معيّنة' }}</h3>
            <p class="text-xs text-gray-400 mt-2">
                @if($subscription?->is_active)
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">نشط</span>
                @else
                    <span class="text-rose-600 dark:text-rose-400 font-bold">غير نشط</span>
                @endif
                · ينتهي {{ $subscription?->ends_at?->format('Y/m/d') ?? '—' }}
            </p>
        </div>

        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 dark:bg-orange-500/10 dark:text-orange-400 px-2 py-1 rounded-md">الاستخدام</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">العيادات المستخدمة</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1" dir="ltr">{{ $usedClinics }} <span class="text-base text-gray-400 font-bold">/ {{ $maxClinics }}</span></h3>
            <p class="text-xs text-gray-400 mt-2">المتبقي: <strong class="text-gray-700 dark:text-gray-200">{{ $remaining }}</strong></p>
        </div>

        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 px-2 py-1 rounded-md">المرضى</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي مرضى الفروع</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($clinics->sum('patients_count')) }}</h3>
        </div>

        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-violet-600 bg-violet-50 dark:bg-violet-500/10 dark:text-violet-400 px-2 py-1 rounded-md">المواعيد</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المواعيد</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($clinics->sum('appointments_count')) }}</h3>
        </div>
    </div>

    {{-- Clinics table --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">عيادات السلسلة</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $clinics->count() }} فرع مسجّل</p>
            </div>
            @if($canCreateClinic)
                <button type="button" data-modal-open="create-clinic"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    إضافة فرع
                </button>
            @endif
        </div>

        @if($clinics->isEmpty())
            <div class="py-16 flex flex-col items-center justify-center text-center px-6">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">لا توجد عيادات بعد</h4>
                <p class="text-sm text-gray-500 max-w-sm mb-5">أنشئ أول فرع ضمن باقتك وابدأ إدارة السلسلة من هنا.</p>
                @if($canCreateClinic)
                    <button type="button" data-modal-open="create-clinic" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">إنشاء أول عيادة</button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">العيادة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">المرضى</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">المواعيد</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.04]">
                        @foreach($clinics as $clinic)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-black text-sm shrink-0">
                                            {{ mb_substr($clinic->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('organization.clinics.show', [$organization, $clinic]) }}" class="font-bold text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                                {{ $clinic->name }}
                                            </a>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $clinic->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700 dark:text-gray-300">{{ number_format($clinic->patients_count) }}</td>
                                <td class="px-6 py-4 font-bold text-gray-700 dark:text-gray-300">{{ number_format($clinic->appointments_count) }}</td>
                                <td class="px-6 py-4">
                                    @if($clinic->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            نشطة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            معطلة
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <a href="{{ route('organization.clinics.show', [$organization, $clinic]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 text-xs font-bold transition-colors">
                                            التفاصيل والتقارير
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Create clinic modal --}}
    <div id="create-clinic" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center bg-gray-50 dark:bg-white/[0.02]">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إنشاء عيادة جديدة</h3>
                    <p class="text-xs text-gray-500 mt-0.5">باقي التفاصيل يضيفها الطبيب من لوحة العيادة</p>
                </div>
                <button type="button" data-modal-close="create-clinic" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('organization.clinics.store', $organization) }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">اسم العيادة <span class="text-red-500">*</span></label>
                        <input type="text" name="clinic_name" required value="{{ old('clinic_name') }}" class="form-control" placeholder="مثال: فرع المعادي" autocomplete="organization">
                    </div>
                    <div>
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" autocomplete="tel">
                    </div>
                    <div>
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control" placeholder="المدينة، الحي">
                    </div>

                    <div class="rounded-xl border border-gray-100 dark:border-white/10 p-4 space-y-3">
                        <label class="flex items-start gap-3 text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" name="create_branch_manager" value="1" id="create_branch_manager"
                                   class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                   onchange="document.getElementById('branch-manager-fields').classList.toggle('hidden', !this.checked)">
                            <span>
                                إضافة مدير فرع بحساب مستقل
                                <span class="block text-xs font-medium text-gray-500 mt-0.5">اختياري — يدخل لوحة العيادة فقط وليس لوحة السلسلة</span>
                            </span>
                        </label>

                        <div id="branch-manager-fields" class="hidden space-y-3 pt-1">
                            <div>
                                <label class="form-label">اسم مدير الفرع</label>
                                <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="form-control" autocomplete="name">
                            </div>
                            <div>
                                <label class="form-label">بريد مدير الفرع</label>
                                <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="form-control" autocomplete="off">
                            </div>
                            <div>
                                <label class="form-label">كلمة مرور مدير الفرع</label>
                                <input type="password" name="owner_password" class="form-control" minlength="8" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="create-clinic" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-md active:scale-95">حفظ العيادة</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.organization>

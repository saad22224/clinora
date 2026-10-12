<x-layouts.organization :organization="$organization" title="تفاصيل العيادة">
    {{-- Header / actions --}}
    <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
            <div class="flex items-start gap-4 min-w-0">
                <a href="{{ route('organization.dashboard', $organization) }}"
                   class="w-10 h-10 rounded-xl border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shrink-0"
                   title="العودة للوحة السلسلة">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <div class="w-14 h-14 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-black text-xl shrink-0">
                    {{ mb_substr($clinic->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white truncate">{{ $clinic->name }}</h2>
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
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ $clinic->slug }} · ضمن {{ $organization->name }}</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                        @if($clinic->phone)
                            <span>الهاتف: <strong class="text-gray-700 dark:text-gray-300">{{ $clinic->phone }}</strong></span>
                        @endif
                        @if($clinic->address)
                            <span>العنوان: <strong class="text-gray-700 dark:text-gray-300">{{ $clinic->address }}</strong></span>
                        @endif
                        <span>المرضى: <strong class="text-gray-700 dark:text-gray-300">{{ number_format($clinic->patients_count) }}</strong></span>
                        <span>الفريق: <strong class="text-gray-700 dark:text-gray-300">{{ number_format($clinic->users_count) }}</strong></span>
                    </div>
                    <div class="mt-3 inline-flex flex-wrap items-center gap-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/20 px-3 py-2 text-xs">
                        <span class="font-black text-brand-700 dark:text-brand-300">باقة السلسلة:</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $entitlement['plan_name'] ?? '—' }}</span>
                        <span class="text-gray-400">·</span>
                        <span class="text-gray-600 dark:text-gray-300">تنتهي {{ $entitlement['ends_at']?->format('Y/m/d') ?? '—' }}</span>
                        @if($entitlement['is_active'])
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">نشطة (موروثة من المنظمة)</span>
                        @else
                            <span class="text-rose-600 dark:text-rose-400 font-bold">غير نشطة</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($canEnter)
                    <form method="POST" action="{{ route('organization.clinics.enter', [$organization, $clinic]) }}">
                        @csrf
                        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14M5 20h10a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            دخول لوحة العيادة
                        </button>
                    </form>
                @endif
                @if($canManage)
                    <button type="button" data-modal-open="edit-clinic"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </button>
                    <form method="POST" action="{{ route('organization.clinics.destroy', [$organization, $clinic]) }}"
                          data-confirm="تحذير: هل أنت متأكد من حذف عيادة «{{ $clinic->name }}»؟ سيتم أرشفتها ولن تظهر في السلسلة.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-rose-200 dark:border-rose-500/30 text-sm font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            حذف
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Period filter --}}
    @if($canViewReports)
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
                <a href="{{ route('organization.clinics.show', [$organization, $clinic]) }}" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">إعادة ضبط</a>
            </div>
        </form>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مواعيد الفترة</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($appointmentsInPeriod) }}</h3>
            <p class="text-xs text-gray-400 mt-2">اليوم: <strong>{{ number_format($todayAppointments) }}</strong></p>
        </div>
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إيراد محصّل</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($finance['collected_income'], 2) }}</h3>
        </div>
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المصروفات</p>
            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($finance['expenses'], 2) }}</h3>
        </div>
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي الفترة</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($finance['net_cash'], 2) }}</h3>
            <p class="text-xs text-gray-400 mt-2">مستحق: <strong>{{ number_format($finance['outstanding'], 2) }}</strong></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Details card --}}
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                <h3 class="font-black text-gray-900 dark:text-white">بيانات العيادة</h3>
            </div>
            <dl class="divide-y divide-gray-100 dark:divide-white/[0.04] text-sm">
                <div class="px-5 py-3 flex justify-between gap-3">
                    <dt class="text-gray-500">الاسم</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-left">{{ $clinic->name }}</dd>
                </div>
                <div class="px-5 py-3 flex justify-between gap-3">
                    <dt class="text-gray-500">المعرّف</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-left" dir="ltr">{{ $clinic->slug }}</dd>
                </div>
                <div class="px-5 py-3 flex justify-between gap-3">
                    <dt class="text-gray-500">الهاتف</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-left">{{ $clinic->phone ?: '—' }}</dd>
                </div>
                <div class="px-5 py-3 flex justify-between gap-3">
                    <dt class="text-gray-500">العنوان</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-left">{{ $clinic->address ?: '—' }}</dd>
                </div>
                <div class="px-5 py-3 flex justify-between gap-3">
                    <dt class="text-gray-500">قيمة الخدمات المكتملة</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-left">{{ number_format($finance['completed_service_value'], 2) }}</dd>
                </div>
                @if($clinic->slug)
                    <div class="px-5 py-3">
                        <a href="{{ route('clinic.public.show', $clinic->slug) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">
                            فتح الصفحة العامة
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Recent appointments --}}
        <div class="xl:col-span-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-black text-gray-900 dark:text-white">أحدث المواعيد</h3>
                    <p class="text-xs text-gray-500 mt-0.5">آخر 8 مواعيد مسجّلة في هذه العيادة</p>
                </div>
                @if($canEnter)
                    <form method="POST" action="{{ route('organization.clinics.enter', [$organization, $clinic]) }}">
                        @csrf
                        <button class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">إدارة كاملة ←</button>
                    </form>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                            <th class="text-right px-5 py-3 text-xs font-black text-gray-500 uppercase">المريض</th>
                            <th class="text-right px-5 py-3 text-xs font-black text-gray-500 uppercase">الموعد</th>
                            <th class="text-right px-5 py-3 text-xs font-black text-gray-500 uppercase">الحالة</th>
                            <th class="text-right px-5 py-3 text-xs font-black text-gray-500 uppercase">السعر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.04]">
                        @forelse($recentAppointments as $appointment)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                                <td class="px-5 py-3">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $appointment->patient?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $appointment->patient?->phone }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $appointment->scheduled_at?->format('Y/m/d H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-xs font-bold px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300">
                                        {{ $appointment->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-bold text-gray-900 dark:text-white">
                                    {{ number_format((float) $appointment->price, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">لا توجد مواعيد بعد لهذه العيادة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($canManage)
        <div id="edit-clinic" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
            <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 transition-all duration-200">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center bg-gray-50 dark:bg-white/[0.02]">
                    <div>
                        <h3 class="font-black text-lg text-gray-900 dark:text-white">تعديل العيادة</h3>
                        <p class="text-xs text-gray-500 mt-0.5">باقي التفاصيل من لوحة العيادة</p>
                    </div>
                    <button type="button" data-modal-close="edit-clinic" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('organization.clinics.update', [$organization, $clinic]) }}">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="form-label">اسم العيادة <span class="text-red-500">*</span></label>
                            <input type="text" name="clinic_name" required value="{{ old('clinic_name', $clinic->name) }}" class="form-control">
                        </div>
                        <div>
                            <label class="form-label">الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}" class="form-control">
                        </div>
                        <div>
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address" value="{{ old('address', $clinic->address) }}" class="form-control">
                        </div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $clinic->is_active)) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            العيادة نشطة
                        </label>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] flex justify-end gap-3">
                        <button type="button" data-modal-close="edit-clinic" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-md active:scale-95">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layouts.organization>

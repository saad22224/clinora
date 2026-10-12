<x-layouts.admin>
    <x-slot:title>سلاسل العيادات</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-600 dark:text-red-400 space-y-1">
            @foreach($errors->all() as $error)
                <p class="font-semibold">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm dark:shadow-xl">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/[0.06] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-lg">سلاسل العيادات</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $organizations->total() }} سلسلة — إدارة الاشتراكات وحدود الفروع</p>
            </div>
            <button type="button" data-modal-open="create-org"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-sm hover:shadow-indigo-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إنشاء سلسلة
            </button>
        </div>

        <form method="GET" class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="بحث باسم المنظمة" class="form-control flex-1" autocomplete="off">
            <button class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-sm font-bold hover:opacity-90 transition-opacity shrink-0">بحث</button>
        </form>

        @if($organizations->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">لا توجد سلاسل بعد</h4>
                <p class="text-sm text-slate-500 max-w-xs mb-5">أنشئ سلسلة وعيّن باقة chain ثم أضف الفروع من لوحة السلسلة.</p>
                <button type="button" data-modal-open="create-org" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors">إنشاء سلسلة الآن</button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02]">
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">المنظمة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الباقة</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">العيادات</th>
                            <th class="text-right px-6 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($organizations as $organization)
                            @php
                                $max = (int) ($organization->subscription?->planDetails?->max_clinics ?? 0);
                                $used = (int) $organization->clinics_count;
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm shrink-0">
                                            {{ mb_substr($organization->name, 0, 1) }}
                                        </div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $organization->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                        {{ $organization->subscription?->planDetails?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">{{ $used }} / {{ $max }}</td>
                                <td class="px-6 py-4">
                                    @if($organization->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            نشطة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            معلقة
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <a href="{{ route('admin.organizations.show', $organization) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10 transition-colors">
                                        تفاصيل
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($organizations->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06]">{{ $organizations->links() }}</div>
            @endif
        @endif
    </div>

    <div id="create-org" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden scale-95 opacity-0 transition-all duration-200">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/[0.06] flex justify-between items-center bg-slate-50 dark:bg-white/[0.02]">
                <div>
                    <h3 class="font-black text-lg text-slate-900 dark:text-white">إنشاء سلسلة عيادات</h3>
                    <p class="text-xs text-slate-500 mt-0.5">سيتم إنشاء حساب مالك السلسلة تلقائياً</p>
                </div>
                <button type="button" data-modal-close="create-org" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.organizations.store') }}" autocomplete="off">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">اسم السلسلة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" class="form-control" placeholder="مثال: مجموعة النور الطبية" autocomplete="organization">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">اسم المالك <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" required value="{{ old('owner_name') }}" class="form-control" autocomplete="name">
                        </div>
                        <div>
                            <label class="form-label">باقة السلسلة <span class="text-red-500">*</span></label>
                            <select name="plan_id" required class="form-control">
                                <option value="">اختر باقة</option>
                                @foreach($chainPlans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->name }} — حد {{ $plan->max_clinics }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">بريد المالك <span class="text-red-500">*</span></label>
                        <input type="email" name="owner_email" required value="{{ old('owner_email') }}" class="form-control" placeholder="owner@chain.com" autocomplete="off">
                    </div>
                    <div>
                        <label class="form-label">كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" name="owner_password" required minlength="8" class="form-control" placeholder="8 أحرف على الأقل" autocomplete="new-password">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/[0.06] bg-slate-50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="create-org" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-md active:scale-95">حفظ السلسلة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('create-org');
        });
    </script>
    @endif
    @endpush
</x-layouts.admin>

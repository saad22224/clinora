<x-layouts.admin>
    <x-slot:title>تفاصيل السلسلة</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.organizations.index') }}" class="w-10 h-10 rounded-xl border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $organization->name }}</h1>
                <p class="text-sm text-slate-500">{{ $usedClinics }} من {{ $maxClinics }} عيادات — المتبقي {{ $remaining }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.organizations.toggle', $organization) }}">
            @csrf
            @method('PATCH')
            <button class="px-4 py-2.5 rounded-xl text-sm font-bold transition-colors {{ $organization->is_active ? 'bg-rose-600 hover:bg-rose-500 text-white' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                {{ $organization->is_active ? 'تعليق السلسلة' : 'تفعيل السلسلة' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 mb-2">الباقة</p>
            <p class="text-lg font-black text-slate-900 dark:text-white">{{ $organization->subscription?->planDetails?->name ?? '—' }}</p>
            <p class="text-xs text-slate-500 mt-2">
                @if($organization->subscription?->is_active)
                    <span class="text-emerald-600 font-bold">نشط</span>
                @else
                    <span class="text-rose-600 font-bold">غير نشط</span>
                @endif
                · ينتهي {{ $organization->subscription?->ends_at?->format('Y/m/d') ?? '—' }}
            </p>
        </div>
        <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 mb-2">العيادات</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $usedClinics }} <span class="text-base text-slate-400">/ {{ $maxClinics }}</span></p>
        </div>
        <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 mb-2">الأعضاء</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $organization->memberships->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-white/[0.06]">
                <h2 class="font-black text-slate-900 dark:text-white">تعيين باقة</h2>
            </div>
            <form method="POST" action="{{ route('admin.organizations.assign-plan', $organization) }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">باقة السلسلة</label>
                    <select name="plan_id" required class="form-control">
                        @foreach($chainPlans as $plan)
                            <option value="{{ $plan->id }}" @selected($organization->subscription?->plan_id == $plan->id)>
                                {{ $plan->name }} ({{ $plan->max_clinics }} عيادات / {{ $plan->duration_in_days }} يوم)
                            </option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" @checked($organization->subscription?->is_active) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    تفعيل الاشتراك
                </label>
                <button class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold transition-colors">حفظ الباقة</button>
            </form>
        </div>

        <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-white/[0.06]">
                <h2 class="font-black text-slate-900 dark:text-white">الأعضاء</h2>
            </div>
            <ul class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                @forelse($organization->memberships as $membership)
                    <li class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($membership->user?->name ?? 'U') }}&background=6366f1&color=fff&size=64" alt="" class="w-9 h-9 rounded-full shrink-0">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 dark:text-white truncate">{{ $membership->user?->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $membership->user?->email }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 shrink-0">{{ $membership->role }}</span>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-slate-500 text-sm">لا يوجد أعضاء.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white dark:bg-[#0A0A12] border border-slate-200 dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-white/[0.06]">
            <h2 class="font-black text-slate-900 dark:text-white">عيادات السلسلة</h2>
        </div>
        <ul class="divide-y divide-slate-100 dark:divide-white/[0.04]">
            @forelse($organization->clinics as $clinic)
                <li class="px-5 py-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-sm">
                            {{ mb_substr($clinic->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $clinic->name }}</p>
                            <p class="text-xs text-slate-500">{{ $clinic->slug }}</p>
                        </div>
                    </div>
                    @if($clinic->is_active)
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">نشطة</span>
                    @else
                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400">معطلة</span>
                    @endif
                </li>
            @empty
                <li class="px-5 py-10 text-center text-slate-500 text-sm">لا توجد عيادات. تُضاف من لوحة السلسلة بواسطة المالك.</li>
            @endforelse
        </ul>
    </div>
</x-layouts.admin>

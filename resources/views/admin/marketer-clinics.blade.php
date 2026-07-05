<x-layouts.admin>
    <x-slot:title>العيادات المحالة - {{ $marketer->name }}</x-slot>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top Bar & Breadcrumb --}}
    <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#0f1115] border border-white/5 rounded-3xl p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <a href="{{ route('admin.marketers.index') }}" class="hover:text-indigo-400 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <span>قائمة المسوقين</span>
                </a>
                <span>/</span>
                <span class="text-white font-bold">{{ $marketer->name }}</span>
            </div>
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <span>العيادات المحالة من: <span class="text-indigo-400">{{ $marketer->name }}</span></span>
                @if($marketer->is_active)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>نشط
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-500/10 text-slate-400 text-xs font-bold border border-slate-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>غير نشط
                    </span>
                @endif
            </h2>
            <p class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>{{ $marketer->phone ?: 'لا يوجد هاتف' }}</span>
                </span>
                @if($marketer->email)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>{{ $marketer->email }}</span>
                    </span>
                @endif
                @if($marketer->notes)
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>{{ $marketer->notes }}</span>
                    </span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('admin.marketers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-sm transition-all active:scale-95 border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                العودة للمسوقين
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    @php
        $totalClinics = $clinics->total();
        $activeClinics = $marketer->clinics()->where('is_active', true)->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">إجمالي العيادات المحالة</p>
                <h3 class="text-3xl font-black text-white">{{ $totalClinics }} <span class="text-xs font-normal text-slate-500">عيادة</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>

        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">العيادات النشطة حالياً</p>
                <h3 class="text-3xl font-black text-emerald-400">{{ $activeClinics }} <span class="text-xs font-normal text-slate-500">عيادة</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">كود الإحالة الخاص بالمسوق</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xl font-black font-mono text-amber-400 bg-amber-500/10 px-3 py-1 rounded-xl border border-amber-500/20">{{ $marketer->referral_code }}</span>
                    <button onclick="copyMarketerLink(this, '{{ url('/register?ref=' . $marketer->referral_code) }}')" type="button" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 transition-colors" title="نسخ رابط الإحالة">
                        <svg class="default-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <svg class="copied-icon hidden w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            </div>
        </div>
    </div>

    {{-- Clinics Table --}}
    <div class="bg-[#0f1115] border border-white/5 rounded-3xl shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-white/5 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-white">العيادات المسجلة عبر هذا المسوق</h3>
                <p class="text-xs text-slate-400 mt-0.5">تفاصيل وحالة جميع العيادات التي انضمت من خلال كود أو رابط الإحالة.</p>
            </div>
        </div>

        @if($clinics->isEmpty())
            <div class="py-16 flex flex-col items-center justify-center text-center px-6">
                <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h4 class="text-base font-bold text-white mb-1">لا توجد عيادات مسجلة بعد</h4>
                <p class="text-sm text-slate-500 max-w-md mb-6">لم يقم أي طبيب أو عيادة بالتسجيل باستخدام كود الإحالة (<span class="font-mono text-indigo-400">{{ $marketer->referral_code }}</span>) حتى الآن.</p>
                <button onclick="copyMarketerLink(this, '{{ url('/register?ref=' . $marketer->referral_code) }}')" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-all shadow-lg shadow-indigo-600/30">
                    <svg class="default-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <svg class="copied-icon hidden w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>نسخ رابط الإحالة للمشاركة</span>
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/[0.02] text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">العيادة</th>
                            <th class="py-4 px-6">المالك / الطبيب</th>
                            <th class="py-4 px-6">الاشتراك والباقة</th>
                            <th class="py-4 px-6">الحالة</th>
                            <th class="py-4 px-6">تاريخ الانضمام</th>
                            <th class="py-4 px-6 text-left">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($clinics as $clinic)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-black text-white text-base shrink-0 shadow-lg shadow-indigo-500/20">
                                            {{ mb_substr($clinic->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-white">{{ $clinic->name }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <p class="text-xs text-slate-500">{{ $clinic->slug }}</p>
                                                <a href="{{ route('clinic.public.show', $clinic->slug) }}" target="_blank" title="عرض صفحة العيادة"
                                                   class="text-indigo-400 hover:text-indigo-300 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($clinic->users->isNotEmpty())
                                        <p class="font-bold text-slate-200">{{ $clinic->users->first()?->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $clinic->users->first()?->email }}</p>
                                    @else
                                        <span class="text-slate-600 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($clinic->subscription)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                            {{ $clinic->subscription->plan === 'free' ? 'bg-slate-800 text-slate-400' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' }}">
                                            {{ $clinic->subscription->plan }}
                                        </span>
                                        <p class="text-xs text-slate-500 mt-1">ينتهي: {{ $clinic->subscription->ends_at?->format('Y/m/d') ?? '∞' }}</p>
                                    @else
                                        <span class="text-slate-500 text-xs bg-white/5 px-2.5 py-1 rounded-lg">بدون اشتراك</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($clinic->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>نشط
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>معطل
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs font-mono">
                                    {{ $clinic->created_at->format('Y/m/d') }}
                                </td>
                                <td class="py-4 px-6 text-left">
                                    <div class="flex items-center justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <form method="POST" action="{{ route('admin.clinics.toggle', $clinic) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $clinic->is_active ? 'تعطيل العيادة' : 'تفعيل العيادة' }}"
                                                    class="p-2 rounded-xl transition-colors {{ $clinic->is_active ? 'bg-white/5 hover:bg-rose-500/10 text-slate-300 hover:text-rose-400' : 'bg-white/5 hover:bg-emerald-500/10 text-slate-300 hover:text-emerald-400' }}">
                                                @if($clinic->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.clinics.destroy', $clinic) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف عيادة ({{ $clinic->name }})؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-white/5 hover:bg-rose-500/10 text-slate-300 hover:text-rose-400 transition-colors" title="حذف العيادة">
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
                <div class="px-6 py-4 border-t border-white/5">{{ $clinics->links() }}</div>
            @endif
        @endif
    </div>

    <script>
        function copyMarketerLink(btn, url) {
            navigator.clipboard.writeText(url).then(() => {
                const defaultIcon = btn.querySelector('.default-icon');
                const copiedIcon = btn.querySelector('.copied-icon');
                
                if (defaultIcon && copiedIcon) {
                    defaultIcon.classList.add('hidden');
                    copiedIcon.classList.remove('hidden');
                    
                    setTimeout(() => {
                        defaultIcon.classList.remove('hidden');
                        copiedIcon.classList.add('hidden');
                    }, 2000);
                }
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>
</x-layouts.admin>

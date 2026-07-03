<x-layouts.admin>
    <x-slot:title>إدارة المسوقين والإحالات</x-slot>

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

    {{-- Stats Row --}}
    @php
        $totalMarketers = $marketers->count();
        $activeMarketers = $marketers->where('is_active', true)->count();
        $totalReferredClinics = $marketers->sum('clinics_count');
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">إجمالي المسوقين</p>
                <h3 class="text-3xl font-black text-white">{{ $totalMarketers }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>

        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">المسوقون النشطون</p>
                <h3 class="text-3xl font-black text-emerald-400">{{ $activeMarketers }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-[#0f1115] border border-white/5 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 mb-1">العيادات المحالة بنجاح</p>
                <h3 class="text-3xl font-black text-amber-400">{{ $totalReferredClinics }} <span class="text-xs font-normal text-slate-500">عيادة</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div x-data="{ showCreateModal: false, showEditModal: false, editMarketer: {} }" class="bg-[#0f1115] border border-white/5 rounded-3xl shadow-xl overflow-hidden">
        
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-white/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-white">قائمة المسوقين وشركاء النجاح</h3>
                <p class="text-xs text-slate-400 mt-0.5">قم بإضافة المسوقين وتتبع العيادات التي تم تسجيلها من خلال روابط الإحالة الخاصة بهم.</p>
            </div>
            <button @click="showCreateModal = true" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                إضافة مسوق جديد
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02] text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">المسوق</th>
                        <th class="py-4 px-6">بيانات التواصل</th>
                        <th class="py-4 px-6">كود ورابط الإحالة</th>
                        <th class="py-4 px-6 text-center">العيادات المحالة</th>
                        <th class="py-4 px-6">الحالة</th>
                        <th class="py-4 px-6 text-left">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($marketers as $marketer)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="py-4 px-6 font-bold text-white">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-black text-sm">
                                        {{ mb_substr($marketer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div>{{ $marketer->name }}</div>
                                        @if($marketer->notes)
                                            <div class="text-[11px] text-slate-500 font-normal mt-0.5">{{ $marketer->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                <div class="text-xs space-y-1" dir="ltr">
                                    <div class="text-right font-mono text-slate-300">{{ $marketer->phone ?: '---' }}</div>
                                    @if($marketer->email)
                                        <div class="text-right text-slate-500">{{ $marketer->email }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6" x-data="{ copied: false }">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-mono font-bold text-xs">
                                        {{ $marketer->referral_code }}
                                    </span>
                                    <button @click="navigator.clipboard.writeText('{{ url('/register?ref=' . $marketer->referral_code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        type="button" title="نسخ رابط الإحالة"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs transition-colors">
                                        <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="copied ? 'تم النسخ!' : 'نسخ الرابط'"></span>
                                    </button>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 font-black text-xs">
                                    {{ $marketer->clinics_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($marketer->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        نشط
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-500/10 text-slate-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-left">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editMarketer = {{ json_encode($marketer) }}; showEditModal = true" type="button" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 transition-colors" title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.marketers.destroy', $marketer->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المسوق؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <p class="font-bold text-base text-slate-400">لا يوجد مسوقين مضافين حتى الآن</p>
                                    <p class="text-xs text-slate-600 mt-1">اضغط على زر "إضافة مسوق جديد" لبدء إنشاء شبكة شركاء النجاح.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Create Marketer Modal --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showCreateModal = false" class="bg-[#14171f] border border-white/10 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-white/10">
                    <h3 class="text-lg font-black text-white">إضافة مسوق جديد</h3>
                    <button @click="showCreateModal = false" type="button" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="{{ route('admin.marketers.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">اسم المسوق <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: أحمد محمد" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white placeholder-slate-600 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">رقم الهاتف</label>
                            <input type="text" name="phone" placeholder="01012345678" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-right placeholder-slate-600 text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">البريد الإلكتروني</label>
                            <input type="email" name="email" placeholder="marketer@example.com" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-right placeholder-slate-600 text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">كود الإحالة (اختياري)</label>
                        <input type="text" name="referral_code" placeholder="مثال: AHMED2026 (سيتم توليده تلقائياً إن ترك فارغاً)" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-indigo-300 uppercase font-mono text-right placeholder-slate-600 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">ملاحظات المسوق</label>
                        <textarea name="notes" rows="2" placeholder="أية تفاصيل إضافية أو نسبة الاتفاق..." class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white placeholder-slate-600 text-sm focus:outline-none focus:border-indigo-500"></textarea>
                    </div>
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button @click="showCreateModal = false" type="button" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-sm transition-colors">إلغاء</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all">حفظ وإصدار الرابط</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Marketer Modal --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showEditModal = false" class="bg-[#14171f] border border-white/10 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-white/10">
                    <h3 class="text-lg font-black text-white">تعديل بيانات المسوق</h3>
                    <button @click="showEditModal = false" type="button" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form :action="'{{ url('admin/marketers') }}/' + editMarketer.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">اسم المسوق <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editMarketer.name" required class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">رقم الهاتف</label>
                            <input type="text" name="phone" x-model="editMarketer.phone" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-right text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">البريد الإلكتروني</label>
                            <input type="email" name="email" x-model="editMarketer.email" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-right text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">كود الإحالة</label>
                        <input type="text" name="referral_code" x-model="editMarketer.referral_code" required dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-indigo-300 uppercase font-mono text-right text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">حالة المسوق</label>
                        <select name="is_active" :value="editMarketer.is_active ? 1 : 0" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="1">نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">ملاحظات المسوق</label>
                        <textarea name="notes" x-model="editMarketer.notes" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500"></textarea>
                    </div>
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button @click="showEditModal = false" type="button" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-sm transition-colors">إلغاء</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.admin>

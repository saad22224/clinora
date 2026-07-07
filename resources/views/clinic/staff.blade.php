<x-layouts.clinic>
    <x-slot:title>إدارة فريق العمل والصلاحيات</x-slot:title>

    <div class="space-y-8">

        {{-- Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#0F0F14] p-6 rounded-2xl border border-gray-100 dark:border-white/[0.06] shadow-sm">
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-2.5 h-6 bg-brand-500 rounded-full inline-block"></span>
                    فريق العمل والمساعدين
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">قم بإضافة السكرتارية أو المساعدين الطبيين وحدد بدقة صلاحيات الوصول لكل موظف.</p>
            </div>

            <button data-modal-open="modal-create-staff" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold text-sm shadow-lg shadow-brand-500/25 hover:opacity-95 active:scale-95 transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                إضافة موظف / سكرتيرة جديدة
            </button>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div data-flash class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 font-bold text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 dark:bg-red-950/30 dark:border-red-800/50 text-red-800 dark:text-red-300 font-bold text-sm">
                <p class="mb-1">يرجى تصحيح الأخطاء التالية:</p>
                @foreach($errors->all() as $error)
                    <p class="text-xs font-normal">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Staff Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($staff as $member)
                <div class="bg-white dark:bg-[#0F0F14] rounded-2xl border border-gray-100 dark:border-white/[0.06] p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between relative overflow-hidden group">
                    {{-- Role Ribbon --}}
                    <div class="absolute top-0 left-0 w-24 h-24 overflow-hidden pointer-events-none">
                        <div class="absolute -rotate-45 -top-6 -left-8 bg-brand-500/10 text-brand-600 dark:text-brand-400 font-black text-[10px] py-1 w-32 text-center border-b border-brand-500/20">
                            {{ $member->role === 'secretary' ? 'سكرتيرة' : 'مساعد' }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0D9488&color=fff&size=100" alt="{{ $member->name }}" class="w-14 h-14 rounded-2xl shrink-0 ring-4 ring-slate-50 dark:ring-white/5 object-cover">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-black text-base text-gray-900 dark:text-white truncate">{{ $member->name }}</h3>
                                <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                                @if($member->phone)
                                <p class="text-[11px] font-bold text-brand-600 dark:text-brand-400 mt-0.5 dir-ltr text-right">📱 {{ $member->phone }}</p>
                                @endif
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                    أضيف {{ $member->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Permissions List --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-white/[0.06]">
                            <p class="text-[11px] font-extrabold text-gray-400 dark:text-gray-500 mb-2 uppercase">الصلاحيات الممنوحة:</p>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($member->permissions as $perm)
                                    <span class="px-2 py-1 rounded-lg bg-brand-50 dark:bg-brand-950/40 border border-brand-200/60 dark:border-brand-800/40 text-[11px] font-bold text-brand-700 dark:text-brand-300">
                                        {{ $permissionsList[$perm->name]['label'] ?? $perm->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold italic">لا توجد صلاحيات وصول محددة</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-5 mt-4 border-t border-gray-100 dark:border-white/[0.06]">
                        <button type="button" data-modal-open="modal-edit-staff-{{ $member->id }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-xs font-bold text-gray-700 dark:text-gray-200 transition-colors flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            تعديل الصلاحيات
                        </button>

                        <form method="POST" action="{{ route('clinic.staff.destroy', $member) }}" data-confirm="هل أنت متأكد من رغبتك في حذف هذا الموظف نهائياً؟">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/30 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 transition-colors cursor-pointer" title="حذف الموظف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ══════ EDIT MODAL PER MEMBER ══════ --}}
                @php
                    $memberPermNames = $member->permissions->pluck('name')->toArray();
                @endphp
                <div id="modal-edit-staff-{{ $member->id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
                    <div data-modal-panel class="bg-white dark:bg-[#12121A] w-full max-w-2xl rounded-3xl border border-gray-100 dark:border-white/10 shadow-2xl overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center shrink-0">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white">تعديل بيانات وصلاحيات الموظف</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $member->name }}</p>
                            </div>
                            <button type="button" data-modal-close="modal-edit-staff-{{ $member->id }}" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('clinic.staff.update', $member) }}" class="p-6 sm:p-8 space-y-6 overflow-y-auto flex-1">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">اسم الموظف *</label>
                                    <input type="text" name="name" value="{{ $member->name }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">البريد الإلكتروني للدخول *</label>
                                    <input type="email" name="email" value="{{ $member->email }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white text-left dir-ltr">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">رقم الواتساب ( لاستعادة كلمة المرور )</label>
                                <input type="text" name="phone" value="{{ $member->phone }}" dir="ltr" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white text-left" placeholder="010XXXXXXXX">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">تغيير كلمة المرور (اتركه فارغاً للاحتفاظ بكلمة المرور الحالية)</label>
                                <input type="password" name="password" minlength="8" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white" placeholder="••••••••">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-brand-600 dark:text-brand-400 mb-3">صلاحيات الوصول المسموح بها:</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                                    @foreach($permissionsList as $key => $perm)
                                    <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-white/5 cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all select-none">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}" {{ in_array($perm['name'], $memberPermNames) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                                        <div class="flex-1 min-w-0">
                                            <span class="block text-xs font-extrabold text-gray-900 dark:text-white">{{ $perm['label'] }}</span>
                                            <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">{{ $perm['desc'] }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/10 shrink-0">
                                <button type="button" data-modal-close="modal-edit-staff-{{ $member->id }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-extrabold shadow-lg shadow-brand-500/20 cursor-pointer">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-[#0F0F14] rounded-3xl border border-dashed border-gray-200 dark:border-white/10 p-16 text-center">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-brand-50 dark:bg-brand-950/30 text-brand-500 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1">لا يوجد موظفين أو مساعدين بعد</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6">أضف سكرتيرة أو موظف استقبال لعيادتك لمساعدتك في تنظيم المواعيد وتسجيل المرضى.</p>
                    <button type="button" data-modal-open="modal-create-staff" class="px-6 py-3 rounded-xl bg-brand-500 text-white font-bold text-xs hover:bg-brand-600 transition-colors shadow-lg shadow-brand-500/20 cursor-pointer">
                        إضافة أول موظف الآن
                    </button>
                </div>
            @endforelse
        </div>


        {{-- ══════ CREATE MODAL ══════ --}}
        <div id="modal-create-staff" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
            <div data-modal-panel class="bg-white dark:bg-[#12121A] w-full max-w-2xl rounded-3xl border border-gray-100 dark:border-white/10 shadow-2xl overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">إضافة موظف / سكرتيرة جديدة</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تحديد البيانات والصلاحيات داخل اللوحة</p>
                    </div>
                    <button type="button" data-modal-close="modal-create-staff" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('clinic.staff.store') }}" class="p-6 sm:p-8 space-y-6 overflow-y-auto flex-1">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">اسم الموظف *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white" placeholder="مثال: سارة أحمد">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">البريد الإلكتروني للدخول *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white text-left dir-ltr" placeholder="secretary@clinic.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">رقم الواتساب ( لاستعادة كلمة المرور )</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white text-left" placeholder="010XXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-700 dark:text-gray-300 mb-1.5">كلمة المرور * (8 أحرف على الأقل)</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 focus:ring-2 focus:ring-brand-500 text-sm font-bold text-gray-900 dark:text-white" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-brand-600 dark:text-brand-400 mb-3">صلاحيات الوصول المسموح بها:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                            @foreach($permissionsList as $key => $perm)
                            <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-white/5 cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all select-none">
                                <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}" {{ (is_array(old('permissions')) && in_array($perm['name'], old('permissions'))) ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-extrabold text-gray-900 dark:text-white">{{ $perm['label'] }}</span>
                                    <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">{{ $perm['desc'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/10 shrink-0">
                        <button type="button" data-modal-close="modal-create-staff" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">إلغاء</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-extrabold shadow-lg shadow-brand-500/20 cursor-pointer">حفظ وحساب الموظف</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    @if($errors->any() && !request()->is('*/staff/*'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-create-staff');
        });
    </script>
    @endif
    @endpush
</x-layouts.clinic>

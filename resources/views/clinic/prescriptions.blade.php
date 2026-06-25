<x-layouts.clinic>
    <x-slot:title>الوصفات الطبية</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700/30 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">الوصفات الطبية (الروشتات)</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $prescriptions->total() }} وصفة محفوظة</p>
            </div>
            <button data-modal-open="modal-add-prescription"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                كتابة وصفة
            </button>
        </div>

        @if($prescriptions->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد وصفات طبية</h4>
                <p class="text-sm text-gray-400 max-w-xs mb-5">قم بكتابة وحفظ الوصفات الطبية لمرضاك للرجوع إليها مستقبلاً.</p>
                @if($patients->isNotEmpty())
                    <button data-modal-open="modal-add-prescription" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">كتابة وصفة الآن</button>
                @else
                    <p class="text-xs text-amber-600 bg-amber-50 px-4 py-2 rounded-xl dark:bg-amber-900/20 dark:text-amber-400">أضف مرضى أولاً لكي تتمكن من كتابة وصفات طبية لهم.</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($prescriptions as $pres)
                    <div class="relative bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl p-5 hover:border-brand-500/30 transition-all group">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-xs shrink-0">
                                    {{ mb_substr($pres->patient->name ?? '؟', 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="font-bold text-sm text-gray-900 dark:text-white">{{ $pres->patient->name ?? 'مريض محذوف' }}</h5>
                                    <p class="text-xs text-gray-400">{{ $pres->created_at->format('Y/m/d h:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('clinic.prescriptions.print', $pres) }}" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="طباعة">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <form method="POST" action="{{ route('clinic.prescriptions.destroy', $pres) }}" data-confirm="هل تريد حذف هذه الوصفة؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/5 rounded-xl p-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap max-h-32 overflow-y-auto font-mono text-left dir-ltr">{{ $pres->notes }}</div>
                    </div>
                @endforeach
            </div>
            @if($prescriptions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $prescriptions->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Add Prescription Modal --}}
    <div id="modal-add-prescription" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg text-gray-900 dark:text-white">وصفة طبية جديدة</h3>
                <button data-modal-close="modal-add-prescription" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.prescriptions.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">المريض <span class="text-red-500">*</span></label>
                        <select name="patient_id" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                            <option value="">— اختر المريض —</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الأدوية والجرعات <span class="text-red-500">*</span></label>
                        <textarea name="notes" rows="6" required dir="ltr"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white font-mono text-left resize-none"
                                  placeholder="Rx:
1. Augmentin 1g tab, 1x2, for 7 days
2. Panadol 500mg, PRN">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-prescription" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">حفظ الوصفة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-prescription');
        });
    </script>
    @endif
    @endpush
</x-layouts.clinic>

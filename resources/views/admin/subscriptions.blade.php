<x-layouts.admin>
    <x-slot:title>الباقات والاشتراكات</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())<div class="server-error hidden"></div>@endif

    @php $plans = \App\Models\Plan::all(); @endphp

    <div class="bg-[#0A0A12] border border-white/[0.06] rounded-2xl overflow-hidden shadow-xl">
        <div class="px-6 py-5 border-b border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-white text-lg">إدارة الباقات</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $plans->count() }} باقة متاحة</p>
            </div>
            <button data-modal-open="modal-add-plan"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all hover:shadow-indigo-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                إضافة باقة
            </button>
        </div>

        <div class="p-6">
            @if($plans->isEmpty())
                <div class="py-16 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-white/5 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-white mb-1">لا توجد باقات بعد</h4>
                    <p class="text-sm text-slate-500 max-w-xs mb-5">أضف أول باقة اشتراك للعيادات.</p>
                    <button data-modal-open="modal-add-plan" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors">إضافة باقة الآن</button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                        <div class="relative border border-white/[0.08] rounded-2xl p-5 bg-white/[0.03] hover:border-indigo-500/30 transition-all group hover:bg-white/[0.05]">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h5 class="font-black text-base text-white">{{ $plan->name }}</h5>
                                    <p class="text-2xl font-black mt-1" style="color: #818cf8">
                                        ${{ number_format($plan->price, 2) }}
                                        <span class="text-sm font-normal text-slate-500">/ {{ $plan->duration_in_days }} يوم</span>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold {{ $plan->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white/5 text-slate-500' }}">
                                    {{ $plan->is_active ? 'نشطة' : 'معطلة' }}
                                </span>
                            </div>

                            @if(!empty($plan->features))
                                <div class="space-y-1.5 border-t border-white/[0.06] pt-3">
                                    @foreach($plan->features as $key => $value)
                                        <div class="flex items-center gap-2 text-xs text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $key }}: {{ $value === 'unlimited' ? 'غير محدود' : $value }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                      data-confirm="هل تريد حذف باقة '{{ $plan->name }}'؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-red-400 bg-[#0A0A12] rounded-lg shadow-sm border border-white/10 hover:border-red-500/20 transition-colors">
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

    {{-- Add Plan Modal --}}
    <div id="modal-add-plan" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-[#0A0A12] w-full max-w-lg rounded-2xl shadow-2xl border border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg text-white">إضافة باقة جديدة</h3>
                <button data-modal-close="modal-add-plan" class="p-1.5 rounded-lg text-slate-500 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.plans.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-900/20 border border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-1.5">اسم الباقة <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-white placeholder-slate-600"
                               placeholder="مثال: الباقة المتقدمة">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-1.5">السعر ($) <span class="text-red-400">*</span></label>
                            <input type="number" name="price" value="{{ old('price', '0') }}" required min="0" step="0.01"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-1.5">المدة (أيام) <span class="text-red-400">*</span></label>
                            <input type="number" name="duration_in_days" value="{{ old('duration_in_days', '30') }}" required min="1"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-1.5">المميزات (JSON اختياري)</label>
                        <textarea name="features" rows="4" dir="ltr"
                                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none text-white font-mono placeholder-slate-600"
                                  placeholder='{"max_patients": 500, "support": "24/7"}'>{{ old('features') }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-white/[0.06] bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-plan" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-white rounded-xl hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all hover:shadow-indigo-500/30 hover:shadow-lg active:scale-95">حفظ الباقة</button>
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

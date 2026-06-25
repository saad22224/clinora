<x-layouts.clinic>
    <x-slot:title>أنواع الكشوفات</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700/30 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">الخدمات وأنواع الزيارات</h3>
                <p class="text-sm text-gray-400 mt-0.5">أضف الخدمات وأسعارها لكي تتمكن من ربطها بالمواعيد وتسجيل إيراداتها.</p>
            </div>
            <button data-modal-open="modal-add-visit-type"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                خدمة جديدة
            </button>
        </div>

        @if($visitTypes->isEmpty())
            <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد خدمات مضافة</h4>
                <p class="text-sm text-gray-400 max-w-xs mb-5">قم بإضافة أنواع الزيارات (كشف، استشارة، متابعة...) لتسهيل عملية الحجز وتسجيل الإيرادات.</p>
                <button data-modal-open="modal-add-visit-type" class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">أضف خدمة الآن</button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($visitTypes as $visit)
                    <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl p-5 relative group hover:border-brand-500/30 transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/5 shadow-sm flex items-center justify-center text-brand-600 dark:text-brand-400">
                                @if($visit->icon)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $visit->icon !!}</svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button data-modal-open="modal-edit-visit-type-{{ $visit->id }}" class="p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors" title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form method="POST" action="{{ route('clinic.visit-types.destroy', $visit) }}" data-confirm="هل تريد حذف هذا الكشف حقاً؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1">{{ $visit->name }}</h4>
                        @if($visit->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">{{ $visit->description }}</p>
                        @endif
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/10 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">سعر الكشف</span>
                            <span class="font-black text-brand-600 dark:text-brand-400">{{ number_format($visit->price, 0) }} ج.م</span>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div id="modal-edit-visit-type-{{ $visit->id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
                        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                                <h3 class="font-black text-lg text-gray-900 dark:text-white">تعديل الخدمة</h3>
                                <button data-modal-close="modal-edit-visit-type-{{ $visit->id }}" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('clinic.visit-types.update', $visit) }}">
                                @csrf @method('PUT')
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">اسم الخدمة <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" value="{{ $visit->name }}" required
                                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">السعر <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="number" name="price" value="{{ $visit->price }}" required min="0" step="0.01"
                                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-12 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white dir-ltr">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-bold pointer-events-none">EGP</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الوصف <span class="text-xs text-gray-400 font-normal">(يظهر في الصفحة العامة)</span></label>
                                        <textarea name="description" rows="3"
                                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none">{{ $visit->description }}</textarea>
                                    </div>
                                    <div class="icon-dropdown-wrap" data-icon-input="icon-edit-{{ $visit->id }}" data-initial="{{ $visit->icon }}">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">أيقونة الخدمة <span class="text-xs text-gray-400 font-normal">(اختياري)</span></label>
                                        <input type="hidden" name="icon" id="icon-edit-{{ $visit->id }}">
                                        <div class="icon-dropdown relative">
                                            <button type="button" class="icon-trigger w-full flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-200 hover:border-brand-500 transition-colors">
                                                <svg class="icon-trigger-svg w-5 h-5 text-brand-600 dark:text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                                <span class="icon-trigger-label flex-1 text-right">اختر أيقونة...</span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div class="icon-panel hidden absolute z-50 mt-1 w-full bg-white dark:bg-[#1a1a2e] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                                                <div class="p-3 border-b border-gray-100 dark:border-white/10">
                                                    <input type="text" placeholder="🔍 ابحث عن أيقونة..." class="icon-search w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-500/30 dark:text-white">
                                                </div>
                                                <div class="icon-list max-h-56 overflow-y-auto p-2 space-y-0.5"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                                    <button type="button" data-modal-close="modal-edit-visit-type-{{ $visit->id }}" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($visitTypes->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $visitTypes->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Add Visit Type Modal --}}
    <div id="modal-add-visit-type" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة خدمة جديدة</h3>
                <button data-modal-close="modal-add-visit-type" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.visit-types.store') }}">
                @csrf
                <div class="p-6 space-y-4">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)<p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">اسم الخدمة <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                                   placeholder="مثال: كشف عادي">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">السعر <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-12 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white dir-ltr" placeholder="0.00">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-sm text-gray-500 dark:text-gray-400 font-bold pointer-events-none">EGP</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الوصف <span class="text-xs text-gray-400 font-normal">(يظهر في الصفحة العامة)</span></label>
                        <textarea name="description" rows="3"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none"
                                  placeholder="وصف موجز للخدمة يشرح ما تتضمنه للمريض...">{{ old('description') }}</textarea>
                    </div>

                    <div class="icon-dropdown-wrap" data-icon-input="icon-add" data-initial="">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">أيقونة الخدمة <span class="text-xs text-gray-400 font-normal">(اختياري)</span></label>
                        <input type="hidden" name="icon" id="icon-add">
                        <div class="icon-dropdown relative">
                            <button type="button" class="icon-trigger w-full flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-200 hover:border-brand-500 transition-colors">
                                <svg class="icon-trigger-svg w-5 h-5 text-brand-600 dark:text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                <span class="icon-trigger-label flex-1 text-right">اختر أيقونة...</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="icon-panel hidden absolute z-50 mt-1 w-full bg-white dark:bg-[#1a1a2e] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                                <div class="p-3 border-b border-gray-100 dark:border-white/10">
                                    <input type="text" placeholder="🔍 ابحث عن أيقونة..." class="icon-search w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-500/30 dark:text-white">
                                </div>
                                <div class="icon-list max-h-56 overflow-y-auto p-2 space-y-0.5"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-visit-type" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">إضافة الخدمة</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-visit-type');
        });
        @endif

        const ICONS = {
            'كشف عام': 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'قلب': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'مُخة': 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'عين': 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            'أسنان': 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'عظم': 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'جلد': 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
            'أطفال': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'تغذية': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            'مختبر': 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547',
            'حقنة': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'موجات دماغ': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1',
            'سماع': 'M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m2.828-9.9a9 9 0 0112.728 0',
            'دم': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'خطة علاج': 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'موعد': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'شخص': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'طوارئ': 'M13 10V3L4 14h7v7l9-11h-7z',
            'عيادة': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        };

        function buildIconDropdown(wrap) {
            const inputId  = wrap.dataset.iconInput;
            const initial  = wrap.dataset.initial || '';
            const input    = document.getElementById(inputId);
            const trigger  = wrap.querySelector('.icon-trigger');
            const panel    = wrap.querySelector('.icon-panel');
            const search   = wrap.querySelector('.icon-search');
            const list     = wrap.querySelector('.icon-list');
            const trigSvg  = wrap.querySelector('.icon-trigger-svg path');
            const trigLbl  = wrap.querySelector('.icon-trigger-label');

            if (!input || !trigger || !panel) return;

            // Set initial value
            input.value = initial;
            if (initial) {
                const name = Object.keys(ICONS).find(k => ICONS[k] === initial) || 'أيقونة مختارة';
                trigSvg?.setAttribute('d', initial);
                trigLbl.textContent = name;
            }

            function renderList(q) {
                list.innerHTML = '';
                Object.entries(ICONS).forEach(([name, path]) => {
                    if (q && !name.includes(q)) return;
                    const row = document.createElement('button');
                    row.type = 'button';
                    const selected = input.value === path;
                    row.className = 'w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-bold transition-colors ' +
                        (selected ? 'bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5');
                    row.innerHTML = `<svg class="w-5 h-5 shrink-0 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="${path}"/></svg><span>${name}</span>` +
                        (selected ? '<svg class="w-4 h-4 ms-auto text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' : '');
                    row.addEventListener('click', () => {
                        input.value = path;
                        trigSvg?.setAttribute('d', path);
                        trigLbl.textContent = name;
                        panel.classList.add('hidden');
                        renderList(search.value.trim());
                    });
                    list.appendChild(row);
                });
                if (!list.children.length) {
                    list.innerHTML = '<p class="text-center text-sm text-gray-400 py-4">لا توجد نتائج</p>';
                }
            }

            renderList('');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = panel.classList.contains('hidden');
                document.querySelectorAll('.icon-panel').forEach(p => p.classList.add('hidden'));
                if (isHidden) { panel.classList.remove('hidden'); search.focus(); }
            });

            search.addEventListener('input', e => renderList(e.target.value.trim()));
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.icon-dropdown-wrap').forEach(buildIconDropdown);
            document.addEventListener('click', () => {
                document.querySelectorAll('.icon-panel').forEach(p => p.classList.add('hidden'));
            });
        });
    </script>
    @endpush
</x-layouts.clinic>

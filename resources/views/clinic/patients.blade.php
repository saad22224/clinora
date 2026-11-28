<x-layouts.clinic>
    <x-slot:title>المرضى</x-slot>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 rounded-xl text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="server-error hidden"></div>
    @endif

    <div class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">إدارة المرضى</h3>
                <p id="patients-total-label" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $patients->total() }} مريض مسجل</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input id="patients-live-search" type="text" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الهاتف..."
                           autocomplete="off"
                           class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                    <div class="absolute left-3 top-2.5 text-gray-400 pointer-events-none">
                        <svg id="patients-search-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <svg id="patients-search-spinner" style="display:none;" class="w-4 h-4 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <button data-modal-open="modal-add-patient"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة مريض
                </button>
            </div>
        </div>

        <div id="patients-list-wrapper" data-total="{{ $patients->total() }}">
            @include('clinic.patients._list', ['patients' => $patients])
        </div>
    </div>

    {{-- ══════ ADD PATIENT MODAL ══════ --}}
    {{-- ══════ ADD PATIENT MODAL ══════ --}}
    <div id="modal-add-patient" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">إضافة مريض جديد</h3>
                    <p class="text-xs text-gray-400 mt-0.5">أدخل بيانات المريض لتسجيله في النظام</p>
                </div>
                <button data-modal-close="modal-add-patient" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('clinic.patients.store') }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    @if($errors->any())
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="off"
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                               placeholder="مثال: أحمد محمد علي">
                    </div>

                    {{-- Phone + Gender --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="05xxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الجنس</label>
                            <select name="gender" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                                <option value="">— اختر —</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>

                    {{-- DOB + Age --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ الميلاد</label>
                            <input type="date" name="dob" data-dob-age-source value="{{ old('dob') }}"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                العمر
                                <span class="text-xs font-normal text-gray-400">(يُحسب من تاريخ الميلاد إن وُجد)</span>
                            </label>
                            <input type="number" name="age" data-dob-age-target value="{{ old('age') }}" min="0" max="150"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="مثال: 45">
                        </div>
                    </div>

                    {{-- Medical History --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            التاريخ المرضي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <div class="flex flex-wrap gap-2 mb-2.5">
                            @foreach(['سكري', 'ضغط دم', 'قلب وشرايين', 'فشل كلوي', 'أمراض كبد', 'غدة درقية', 'ربو', 'حساسية'] as $cond)
                                <button type="button" data-chip data-target="medical_history_add" data-value="{{ $cond }}"
                                        class="chip-btn px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:border-brand-400 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-all">
                                    {{ $cond }}
                                </button>
                            @endforeach
                        </div>
                        <textarea name="medical_history" id="medical_history_add" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: سكري نوع 2، ضغط دم مرتفع...">{{ old('medical_history') }}</textarea>
                    </div>

                    {{-- Surgical History --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            التاريخ الجراحي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <textarea name="surgical_history" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: استئصال زائدة 2018، عملية قلب مفتوح 2021...">{{ old('surgical_history') }}</textarea>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات عامة</label>
                        <textarea name="notes" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="أي ملاحظات خاصة بالمريض...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3 shrink-0">
                    <button type="button" data-modal-close="modal-add-patient"
                            class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                        حفظ المريض
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof CModal !== 'undefined') CModal.open('modal-add-patient');
        });
        @endif

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('patients-live-search');
            const searchIcon = document.getElementById('patients-search-icon');
            const searchSpinner = document.getElementById('patients-search-spinner');
            const listWrapper = document.getElementById('patients-list-wrapper');
            const totalLabel = document.getElementById('patients-total-label');
            let searchTimeout = null;

            function bindChips(root = document) {
                root.querySelectorAll('[data-chip]').forEach((btn) => {
                    if (btn.dataset.bound === '1') return;
                    btn.dataset.bound = '1';
                    btn.addEventListener('click', () => {
                        const targetId = btn.dataset.target;
                        const value = btn.dataset.value;
                        const textarea = document.getElementById(targetId);
                        if (!textarea) return;
                        const current = textarea.value.trim();
                        if (current.includes(value)) {
                            textarea.value = current.replace(new RegExp(',?\\s*' + value.replace(/[.*+?^${}()|[\]\\]/g, '\\\$&') + '\\s*,?', 'g'), '').replace(/^,\s*|,\s*$/g, '').trim();
                            btn.classList.remove('border-brand-400', 'text-brand-600', 'bg-brand-50', 'dark:text-brand-400', 'dark:bg-brand-900/20');
                        } else {
                            textarea.value = current ? current + '، ' + value : value;
                            btn.classList.add('border-brand-400', 'text-brand-600', 'bg-brand-50', 'dark:text-brand-400', 'dark:bg-brand-900/20');
                        }
                    });
                });
            }

            bindChips();

            if (searchInput && listWrapper) {
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    if (searchIcon) searchIcon.style.display = 'none';
                    if (searchSpinner) searchSpinner.style.display = 'block';
                    searchTimeout = setTimeout(async () => {
                        try {
                            const q = e.target.value.trim();
                            const url = `{{ route('clinic.patients.index', [], false) }}?search=${encodeURIComponent(q)}`;
                            const res = await fetch(url, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                                credentials: 'same-origin',
                            });
                            if (!res.ok) return;
                            const html = await res.text();
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const next = doc.getElementById('patients-list-wrapper');
                            if (!next) return;
                            listWrapper.innerHTML = next.innerHTML;
                            listWrapper.dataset.total = next.dataset.total || '0';
                            if (totalLabel) {
                                totalLabel.textContent = `${listWrapper.dataset.total} مريض مسجل`;
                            }
                            const nextUrl = q ? `{{ route('clinic.patients.index', [], false) }}?search=${encodeURIComponent(q)}` : `{{ route('clinic.patients.index', [], false) }}`;
                            history.replaceState(null, '', nextUrl);
                            bindChips(listWrapper);
                        } finally {
                            if (searchIcon) searchIcon.style.display = 'block';
                            if (searchSpinner) searchSpinner.style.display = 'none';
                        }
                    }, 280);
                });
            }
        });
    </script>
    @endpush

</x-layouts.clinic>

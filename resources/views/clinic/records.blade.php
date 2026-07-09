<x-layouts.clinic>
    <x-slot:title>السجلات الطبية</x-slot>

    @if (session('success'))
        <div data-flash
            class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700/30 dark:text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div
        class="bg-white dark:bg-[#0F0F1A] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-sm overflow-hidden">
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-white/[0.06] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-gray-900 dark:text-white text-lg">السجلات الطبية</h3>
                <p class="text-sm text-gray-400 mt-0.5">{{ $records->total() }} ملف مرفوع</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input id="records-live-search" type="text" placeholder="🔍 ابحث بالمريض أو نوع السجل..."
                        value="{{ request('search') }}"
                        class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl pl-9 pr-4 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg id="rec-search-spinner" style="display: none;" class="w-4 h-4 animate-spin text-brand-500"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>
                <button data-modal-open="modal-add-record"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    رفع سجل طبي
                </button>
            </div>
        </div>
        <div id="records-list-wrapper">

            @if ($records->isEmpty())
                <div class="py-20 flex flex-col items-center justify-center text-center px-6">
                    <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا توجد سجلات طبية</h4>
                    <p class="text-sm text-gray-400 max-w-xs mb-5">قم برفع تحاليل، أشعة، أو تقارير لمرضاك للاحتفاظ بها
                        في ملفاتهم.</p>
                    @if ($patients->isNotEmpty())
                        <button data-modal-open="modal-add-record"
                            class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">رفع
                            ملف الآن</button>
                    @else
                        <p
                            class="text-xs text-amber-600 bg-amber-50 px-4 py-2 rounded-xl dark:bg-amber-900/20 dark:text-amber-400">
                            أضف مرضى أولاً لكي تتمكن من رفع سجلات طبية لهم.</p>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                                <th
                                    class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">
                                    المريض</th>
                                <th
                                    class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">
                                    نوع السجل</th>
                                <th
                                    class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">
                                    تاريخ الرفع</th>
                                <th class="px-6 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                            @foreach ($records as $record)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        @if ($record->patient)
                                            <a href="{{ route('clinic.patients.medical-file', $record->patient) }}"
                                                class="flex items-center gap-3 group/link">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400 shrink-0 group-hover/link:bg-brand-50 dark:group-hover/link:bg-brand-900/20 group-hover/link:text-brand-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <p
                                                    class="font-bold text-gray-900 dark:text-white group-hover/link:text-brand-600 dark:group-hover/link:text-brand-400 transition-colors">
                                                    {{ $record->patient->name }}</p>
                                            </a>
                                        @else
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400 shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <p class="font-bold text-gray-900 dark:text-white">مريض محذوف</p>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">{{ $record->type }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $record->created_at->format('Y/m/d') }}</td>
                                    <td class="px-6 py-4 text-left">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ Storage::url($record->file_path) }}" download target="_blank"
                                                class="p-1.5 rounded-lg text-gray-500 hover:text-brand-600 hover:bg-brand-50 dark:text-gray-400 dark:hover:bg-brand-900/20 transition-colors"
                                                title="عرض/تحميل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('clinic.records.destroy', $record) }}"
                                                data-confirm="هل تريد حذف هذا السجل الطبي؟">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                    title="حذف">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($records->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">{{ $records->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Add Record Modal --}}
    <div id="modal-add-record" data-modal
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel
            class="bg-white dark:bg-[#0F0F1A] w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center">
                <h3 class="font-black text-lg text-gray-900 dark:text-white">رفع سجل طبي جديد</h3>
                <button data-modal-close="modal-add-record"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('clinic.records.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    @if ($errors->any())
                        <div
                            class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Patient Selection (Searchable Autocomplete) --}}
                    <div class="relative" id="patient-select-container">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">المريض <span
                                class="text-red-500">*</span></label>
                        <input type="hidden" name="patient_id" id="hidden-patient-id" required
                            value="{{ old('patient_id') }}">
                        <div class="relative">
                            <input type="text" id="patient-search-input"
                                placeholder="🔍 ابحث واختر المريض (الاسم، رقم الهاتف)..." autocomplete="off"
                                class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                            <button type="button" id="btn-clear-patient" style="display: none;"
                                class="absolute left-3 top-2.5 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="patient-search-dropdown" style="display: none;"
                            class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-[#1A1A2E] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">نوع السجل <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="type" value="{{ old('type') }}" required
                            class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white"
                            placeholder="مثال: تحليل دم، أشعة مقطعية...">
                    </div>

                    <x-clinic.file-camera-upload name="file" :required="true" />
                </div>
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3">
                    <button type="button" data-modal-close="modal-add-record"
                        class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">حفظ
                        الملف</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if ($errors->any())
                    if (typeof CModal !== 'undefined') CModal.open('modal-add-record');
                @endif

                // 1. Live Search for Records List
                const recSearchInput = document.getElementById('records-live-search');
                const recSearchSpinner = document.getElementById('rec-search-spinner');
                const recWrapper = document.getElementById('records-list-wrapper');
                let recTimeout = null;

                if (recSearchInput && recWrapper) {
                    recSearchInput.addEventListener('input', (e) => {
                        clearTimeout(recTimeout);
                        if (recSearchSpinner) recSearchSpinner.style.display = 'block';

                        recTimeout = setTimeout(async () => {
                            try {
                                const url =
                                    `{{ route('clinic.records.index', [], false) }}?search=${encodeURIComponent(e.target.value.trim())}`;
                                const res = await fetch(url, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    credentials: 'same-origin'
                                });
                                if (res.ok) {
                                    const html = await res.text();
                                    const doc = new DOMParser().parseFromString(html, 'text/html');
                                    const newWrapper = doc.getElementById('records-list-wrapper');
                                    if (newWrapper) {
                                        recWrapper.innerHTML = newWrapper.innerHTML;
                                    }
                                }
                            } catch (err) {
                                console.error('Live search error:', err);
                            } finally {
                                if (recSearchSpinner) recSearchSpinner.style.display = 'none';
                            }
                        }, 300);
                    });
                }

                // 2. Searchable Patient Dropdown
                const allPatients = @json($patients);
                const patientSearchInput = document.getElementById('patient-search-input');
                const hiddenPatientId = document.getElementById('hidden-patient-id');
                const patientDropdown = document.getElementById('patient-search-dropdown');
                const btnClearPatient = document.getElementById('btn-clear-patient');

                if (patientSearchInput && hiddenPatientId && patientDropdown) {
                    if (hiddenPatientId.value) {
                        const found = allPatients.find(p => p.id == hiddenPatientId.value);
                        if (found) {
                            patientSearchInput.value = found.name + (found.phone ? ` (${found.phone})` : '');
                            if (btnClearPatient) btnClearPatient.style.display = 'block';
                        }
                    }

                    function renderPatientDropdown(list) {
                        if (!list || list.length === 0) {
                            patientDropdown.innerHTML =
                                `<div class="p-3 text-center text-xs text-gray-400">لا يوجد مريض مطابق للبحث</div>`;
                            patientDropdown.style.display = 'block';
                            return;
                        }
                        patientDropdown.innerHTML = '';
                        list.forEach(p => {
                            const item = document.createElement('div');
                            item.className =
                                'p-3 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-pointer transition-colors flex items-center justify-between gap-3 border-b border-gray-50 dark:border-white/5 last:border-0';
                            item.innerHTML = `
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-white">${p.name}</div>
                                <div class="text-xs text-gray-400 mt-0.5">${p.phone || 'بدون رقم هاتف'}</div>
                            </div>
                        `;
                            item.addEventListener('click', () => {
                                hiddenPatientId.value = p.id;
                                patientSearchInput.value = p.name + (p.phone ? ` (${p.phone})` : '');
                                patientDropdown.style.display = 'none';
                                if (btnClearPatient) btnClearPatient.style.display = 'block';
                            });
                            patientDropdown.appendChild(item);
                        });
                        patientDropdown.style.display = 'block';
                    }

                    patientSearchInput.addEventListener('input', (e) => {
                        const val = e.target.value.trim().toLowerCase();
                        if (!val) {
                            hiddenPatientId.value = '';
                            if (btnClearPatient) btnClearPatient.style.display = 'none';
                            renderPatientDropdown(allPatients);
                            return;
                        }
                        const filtered = allPatients.filter(p => {
                            return (p.name && p.name.toLowerCase().includes(val)) ||
                                (p.phone && p.phone.includes(val));
                        });
                        renderPatientDropdown(filtered);
                    });

                    patientSearchInput.addEventListener('focus', () => {
                        const val = patientSearchInput.value.trim().toLowerCase();
                        const filtered = !val ? allPatients : allPatients.filter(p => (p.name && p.name
                            .toLowerCase().includes(val)) || (p.phone && p.phone.includes(val)));
                        renderPatientDropdown(filtered);
                    });

                    if (btnClearPatient) {
                        btnClearPatient.addEventListener('click', () => {
                            hiddenPatientId.value = '';
                            patientSearchInput.value = '';
                            btnClearPatient.style.display = 'none';
                            renderPatientDropdown(allPatients);
                            patientSearchInput.focus();
                        });
                    }

                    document.addEventListener('click', (e) => {
                        if (!patientSearchInput.contains(e.target) && !patientDropdown.contains(e.target) && (!
                                btnClearPatient || !btnClearPatient.contains(e.target))) {
                            patientDropdown.style.display = 'none';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-layouts.clinic>

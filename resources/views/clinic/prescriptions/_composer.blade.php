{{--
    Visit-scoped prescription composer (smart روشتة UX).
    Requires: $patient, $visit, $templates
--}}
<form method="POST" action="{{ route('clinic.prescriptions.store') }}" id="visit-prescription-form" class="space-y-6">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
    <input type="hidden" name="visit_id" value="{{ $visit->id }}">

    @if($errors->any() && (string) old('visit_id') === (string) $visit->id)
        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/30 rounded-xl">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-400">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div>
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">قالب أدوية جاهز (اختياري)</label>
        <div class="flex flex-wrap gap-2">
            <select id="medication-template-select"
                    class="flex-1 min-w-[12rem] rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                <option value="">بدون قالب</option>
                @foreach(($templates ?? collect()) as $template)
                    <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->items->count() }} أدوية)</option>
                @endforeach
            </select>
            <button type="button" id="btn-apply-template"
                    class="px-4 py-2.5 text-sm font-bold rounded-xl border border-brand-200 dark:border-brand-700/40 text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 dark:hover:bg-brand-900/30 transition-colors">
                تطبيق القالب
            </button>
        </div>
    </div>

    <div class="bg-gray-50/70 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 rounded-2xl p-4 md:p-5 space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h4 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                إضافة دواء للروشتة
            </h4>
            <span class="text-[11px] text-gray-400">الأسماء الإنجليزية من قاعدة الأدوية</span>
        </div>

        <div class="relative">
            <div class="relative">
                <input id="drug-search-input" type="text" autocomplete="off"
                       placeholder="ابحث في قاعدة الأدوية (Panadol, Augmentin, الاسم العلمي...)"
                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400 pointer-events-none">
                    <svg id="drug-search-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <svg id="drug-spinner-icon" style="display: none;" class="w-5 h-5 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>
            <div id="drug-search-dropdown" style="display: none;"
                 class="absolute z-40 left-0 right-0 mt-1 bg-white dark:bg-[#1A1A2E] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-gray-200/50 dark:border-white/5">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">اسم الدواء (English) <span class="text-red-500">*</span></label>
                <input id="custom-medicine-name" type="text" dir="ltr" placeholder="Panadol 500mg"
                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">الجرعة</label>
                <input id="custom-dosage" type="text" placeholder="قرص كل 12 ساعة"
                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                <div class="flex flex-wrap gap-1 mt-1.5">
                    <button type="button" data-val="قرص كل 8 ساعات" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">كل 8 ساعات</button>
                    <button type="button" data-val="قرص كل 12 ساعة" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">كل 12 ساعة</button>
                    <button type="button" data-val="قرص مرة يومياً" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">مرة يومياً</button>
                    <button type="button" data-val="5 سم 3 مرات يومياً" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">5 سم 3 مرات</button>
                    <button type="button" data-val="عند اللزوم" class="quick-dosage-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">عند اللزوم</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">المدة / التعليمات</label>
                <input id="custom-instructions" type="text" placeholder="لمدة 7 أيام بعد الأكل"
                       class="w-full bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white">
                <div class="flex flex-wrap gap-1 mt-1.5">
                    <button type="button" data-val="لمدة 3 أيام بعد الأكل" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">3 أيام بعد الأكل</button>
                    <button type="button" data-val="لمدة 5 أيام" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">5 أيام</button>
                    <button type="button" data-val="لمدة أسبوع" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">أسبوع</button>
                    <button type="button" data-val="قبل الأكل" class="quick-inst-btn text-[10px] px-2 py-0.5 bg-gray-200/70 dark:bg-white/10 hover:bg-brand-500 hover:text-white rounded-md transition-colors">قبل الأكل</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button id="btn-add-item" type="button" disabled
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                إضافة للقائمة
            </button>
        </div>
    </div>

    <div>
        <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-2 flex flex-wrap items-center justify-between gap-2">
            <span>الأدوية المضافة (<span id="items-count">0</span>)</span>
            <span id="items-print-note" style="display: none;" class="text-xs text-emerald-600 dark:text-emerald-400 font-normal">جاهزة للطباعة في الروشتة</span>
        </h4>
        <div id="items-empty-state" class="bg-gray-50 dark:bg-white/[0.02] border border-dashed border-gray-200 dark:border-white/10 rounded-xl p-6 text-center text-gray-400 text-xs">
            لم تُضف أدوية بعد. ابحث أعلاه أو اكتب اسم الدواء يدوياً بالإنجليزية.
        </div>
        <div id="items-list-container" style="display: none;" class="space-y-2"></div>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات طبية / توصيات عامة</label>
        <textarea name="notes" id="prescription-notes" rows="3" dir="ltr"
                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white font-mono text-left resize-none"
                  placeholder="توصيات للمريض، قياسات، أو ملاحظات إضافية...">{{ old('notes') }}</textarea>
    </div>

    <div class="flex flex-wrap justify-between gap-3 pt-2 border-t border-gray-100 dark:border-white/10">
        <button type="button" id="btn-save-medication-template"
                class="px-4 py-2.5 text-sm font-bold text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700/40 rounded-xl hover:bg-brand-100 dark:hover:bg-brand-900/30 transition-colors">
            حفظ كقالب عيادة
        </button>
        <button type="submit"
                class="px-6 py-2.5 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-lg active:scale-95">
            حفظ الروشتة للزيارة
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const formRoot = document.getElementById('visit-prescription-form');
    if (!formRoot) return;

    let prescriptionItems = [];
    let searchTimeout = null;

    const searchInput = document.getElementById('drug-search-input');
    const searchIcon = document.getElementById('drug-search-icon');
    const spinnerIcon = document.getElementById('drug-spinner-icon');
    const dropdown = document.getElementById('drug-search-dropdown');
    const medNameInput = document.getElementById('custom-medicine-name');
    const dosageInput = document.getElementById('custom-dosage');
    const instInput = document.getElementById('custom-instructions');
    const addItemBtn = document.getElementById('btn-add-item');
    const itemsCountSpan = document.getElementById('items-count');
    const itemsPrintNote = document.getElementById('items-print-note');
    const emptyStateDiv = document.getElementById('items-empty-state');
    const itemsListDiv = document.getElementById('items-list-container');
    const notesTextarea = document.getElementById('prescription-notes');

    function escAttr(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }

    formRoot.querySelectorAll('.quick-dosage-btn').forEach((btn) => {
        btn.addEventListener('click', () => { dosageInput.value = btn.getAttribute('data-val'); });
    });
    formRoot.querySelectorAll('.quick-inst-btn').forEach((btn) => {
        btn.addEventListener('click', () => { instInput.value = btn.getAttribute('data-val'); });
    });

    function checkAddBtnState() {
        const hasName = medNameInput.value.trim().length > 0;
        addItemBtn.disabled = !hasName;
        addItemBtn.classList.toggle('opacity-50', !hasName);
        addItemBtn.classList.toggle('cursor-not-allowed', !hasName);
    }
    medNameInput.addEventListener('input', checkAddBtnState);

  searchInput?.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);
        if (query.length < 2) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }
        searchIcon.style.display = 'none';
        spinnerIcon.style.display = 'block';
        searchTimeout = setTimeout(async () => {
            try {
                const url = `{{ route('clinic.drugs.search', [], false) }}?q=${encodeURIComponent(query)}`;
                const res = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (res.ok) {
                    renderDropdown(await res.json());
                }
            } catch (err) {
                console.error('Drug search error:', err);
            } finally {
                spinnerIcon.style.display = 'none';
                searchIcon.style.display = 'block';
            }
        }, 300);
    });

    function renderDropdown(drugs) {
        if (!drugs || drugs.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-center text-xs text-gray-400">لا نتائج — أدخل الاسم يدوياً بالإنجليزية</div>';
            dropdown.style.display = 'block';
            return;
        }
        dropdown.innerHTML = '';
        drugs.forEach((drug) => {
            const displayName = drug.commercial_name_en || drug.scientific_name || '';
            const subName = drug.commercial_name_ar || drug.scientific_name || '';
            const routeBadge = drug.route
                ? `<span class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded text-[10px] font-semibold">${drug.route}</span>`
                : '';
            const priceText = drug.price_egp
                ? `<div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">${drug.price_egp} ج.م</div>`
                : '';
            const item = document.createElement('div');
            item.className = 'p-3 hover:bg-brand-50 dark:hover:bg-brand-900/20 cursor-pointer transition-colors flex items-center justify-between gap-3';
            item.innerHTML = `
                <div class="min-w-0">
                    <div class="font-bold text-sm text-gray-900 dark:text-white dir-ltr text-left">${displayName}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${subName}</div>
                </div>
                <div class="text-left shrink-0">${routeBadge}${priceText}</div>
            `;
            item.addEventListener('click', () => {
                const nameEn = drug.commercial_name_en || drug.scientific_name || '';
                medNameInput.value = nameEn;
                dropdown.style.display = 'none';
                searchInput.value = '';
                checkAddBtnState();
            });
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    }

    document.addEventListener('click', (e) => {
        if (searchInput && dropdown && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
    searchInput?.addEventListener('focus', () => {
        if (dropdown.children.length > 0) {
            dropdown.style.display = 'block';
        }
    });

    addItemBtn.addEventListener('click', () => {
        const name = medNameInput.value.trim();
        if (!name) return;
        prescriptionItems.push({
            id: Date.now() + Math.random(),
            name,
            dosage: dosageInput.value.trim(),
            instructions: instInput.value.trim(),
        });
        medNameInput.value = '';
        dosageInput.value = '';
        instInput.value = '';
        checkAddBtnState();
        renderItems();
    });

    function renderItems() {
        itemsCountSpan.textContent = String(prescriptionItems.length);
        if (prescriptionItems.length === 0) {
            emptyStateDiv.style.display = 'block';
            itemsListDiv.style.display = 'none';
            itemsPrintNote.style.display = 'none';
            itemsListDiv.innerHTML = '';
            if (notesTextarea) notesTextarea.required = true;
        } else {
            emptyStateDiv.style.display = 'none';
            itemsListDiv.style.display = 'block';
            itemsPrintNote.style.display = 'inline';
            itemsListDiv.innerHTML = '';
            if (notesTextarea) notesTextarea.required = false;
            prescriptionItems.forEach((item, idx) => {
                const el = document.createElement('div');
                el.className = 'bg-white dark:bg-[#0F0F1A] border border-gray-200 dark:border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 shadow-sm';
                const dosageChip = item.dosage
                    ? `<span class="px-2 py-0.5 bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300 rounded-md font-semibold text-xs">${escAttr(item.dosage)}</span>`
                    : '';
                const instChip = item.instructions
                    ? `<span class="px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded-md text-xs">${escAttr(item.instructions)}</span>`
                    : '';
                el.innerHTML = `
                    <input type="hidden" name="items[${idx}][medicine_name]" value="${escAttr(item.name)}">
                    <input type="hidden" name="items[${idx}][dosage]" value="${escAttr(item.dosage)}">
                    <input type="hidden" name="items[${idx}][instructions]" value="${escAttr(item.instructions)}">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">${idx + 1}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white dir-ltr text-left">${escAttr(item.name)}</div>
                            <div class="flex flex-wrap gap-2 mt-1">${dosageChip}${instChip}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-remove-item p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                el.querySelector('.btn-remove-item').addEventListener('click', () => {
                    prescriptionItems.splice(idx, 1);
                    renderItems();
                });
                itemsListDiv.appendChild(el);
            });
        }
    }

    @php
        $clinicTemplatesJson = ($templates ?? collect())->map(static function ($template): array {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'items' => $template->items->map(static function ($item): array {
                    return [
                        'medicine_name' => $item->medicine_name,
                        'dosage' => $item->dosage,
                        'instructions' => $item->instructions,
                    ];
                })->values(),
            ];
        })->values();
    @endphp
    const clinicTemplates = @json($clinicTemplatesJson);

    document.getElementById('btn-apply-template')?.addEventListener('click', () => {
        const selectedId = document.getElementById('medication-template-select')?.value;
        if (!selectedId) return;
        const template = clinicTemplates.find((t) => String(t.id) === String(selectedId));
        if (!template) return;
        template.items.forEach((item) => {
            prescriptionItems.push({
                id: Date.now() + Math.random(),
                name: item.medicine_name,
                dosage: item.dosage || '',
                instructions: item.instructions || '',
            });
        });
        renderItems();
    });

    document.getElementById('btn-save-medication-template')?.addEventListener('click', async () => {
        if (prescriptionItems.length === 0) {
            alert('أضف دواء واحداً على الأقل قبل حفظ القالب.');
            return;
        }
        const name = prompt('اسم قالب الأدوية:');
        if (!name || !name.trim()) return;
        const response = await fetch(@json(route('clinic.prescription-templates.store')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                name: name.trim(),
                items: prescriptionItems.map((item) => ({
                    medicine_name: item.name,
                    dosage: item.dosage || null,
                    instructions: item.instructions || null,
                })),
            }),
        });
        if (!response.ok) {
            alert('تعذر حفظ القالب.');
            return;
        }
        alert('تم حفظ قالب العيادة بنجاح.');
    });

    renderItems();
    checkAddBtnState();
});
</script>
@endpush
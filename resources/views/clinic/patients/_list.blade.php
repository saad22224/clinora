@if($patients->isEmpty())
    <div class="py-20 flex flex-col items-center justify-center text-center px-6">
        <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        @if(request('search'))
            <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا نتائج للبحث</h4>
            <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs mb-5">جرّب اسمًا أو رقم هاتف مختلف، أو امسح البحث لعرض كل المرضى.</p>
        @else
            <h4 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-1">لا يوجد مرضى بعد</h4>
            <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs mb-5">ابدأ بإضافة أول مريض لعيادتك لتتبع سجلاته وإدارة مواعيده.</p>
            <button data-modal-open="modal-add-patient"
                    class="px-5 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-colors">
                إضافة أول مريض
            </button>
        @endif
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02]">
                    <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">المريض</th>
                    <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الهاتف</th>
                    <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">الجنس</th>
                    <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">تاريخ الميلاد</th>
                    <th class="text-right px-6 py-3.5 text-xs font-black text-gray-400 uppercase tracking-wider">تاريخ التسجيل</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                @foreach($patients as $patient)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4">
                            <a href="{{ route('clinic.patients.medical-file', $patient) }}" class="flex items-center gap-3 group/link">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center font-bold text-white text-sm shrink-0 shadow-sm group-hover/link:scale-105 transition-transform">
                                    {{ mb_substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white group-hover/link:text-brand-600 dark:group-hover/link:text-brand-400 transition-colors">{{ $patient->name }}</p>
                                    @if($patient->notes)
                                        <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $patient->notes }}</p>
                                    @endif
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-700 dark:text-gray-300 dir-ltr">{{ $patient->phone ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($patient->gender === 'male')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">ذكر</span>
                            @elseif($patient->gender === 'female')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400">أنثى</span>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                            {{ $patient->dob?->format('Y/m/d') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 dark:text-gray-500 text-xs">
                            {{ $patient->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('clinic.patients.medical-file', $patient) }}"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors"
                                   title="الملف الطبي">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <button type="button" data-modal-open="modal-edit-patient-{{ $patient->id }}"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors"
                                        title="تعديل البيانات">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('clinic.patients.destroy', $patient) }}"
                                      data-confirm="هل أنت متأكد من حذف مريض '{{ $patient->name }}'؟ لا يمكن التراجع.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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

    @if($patients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06]">
            {{ $patients->links() }}
        </div>
    @endif
@endif

@foreach($patients as $patient)
    <div id="modal-edit-patient-{{ $patient->id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
        <div data-modal-panel class="bg-white dark:bg-[#0F0F1A] w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden scale-95 opacity-0 translate-y-4 transition-all duration-200 max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-lg text-gray-900 dark:text-white">تعديل بيانات المريض</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $patient->name }}</p>
                </div>
                <button type="button" data-modal-close="modal-edit-patient-{{ $patient->id }}" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('clinic.patients.update', $patient) }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $patient->name }}" required autocomplete="off"
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                               placeholder="مثال: أحمد محمد علي">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ $patient->phone }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="05xxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">الجنس</label>
                            <select name="gender" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                                <option value="">— اختر —</option>
                                <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">تاريخ الميلاد</label>
                            <input type="date" name="dob" data-dob-age-source value="{{ $patient->dob?->format('Y-m-d') }}"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                العمر
                                <span class="text-xs font-normal text-gray-400">(يُحسب من تاريخ الميلاد إن وُجد)</span>
                            </label>
                            <input type="number" name="age" data-dob-age-target value="{{ $patient->displayAge() }}" min="0" max="150"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white transition-shadow"
                                   placeholder="مثال: 45">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            التاريخ المرضي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <div class="flex flex-wrap gap-2 mb-2.5">
                            @foreach(['سكري', 'ضغط دم', 'قلب وشرايين', 'فشل كلوي', 'أمراض كبد', 'غدة درقية', 'ربو', 'حساسية'] as $cond)
                                <button type="button" data-chip data-target="medical_history_edit_{{ $patient->id }}" data-value="{{ $cond }}"
                                        class="chip-btn px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:border-brand-400 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-all">
                                    {{ $cond }}
                                </button>
                            @endforeach
                        </div>
                        <textarea name="medical_history" id="medical_history_edit_{{ $patient->id }}" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: سكري نوع 2، ضغط دم مرتفع...">{{ $patient->medical_history }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            التاريخ الجراحي
                            <span class="text-xs font-normal text-gray-400 mr-1">— اختياري</span>
                        </label>
                        <textarea name="surgical_history" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="مثال: استئصال زائدة 2018، عملية قلب مفتوح 2021...">{{ $patient->surgical_history }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات عامة</label>
                        <textarea name="notes" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 outline-none dark:text-white resize-none transition-shadow"
                                  placeholder="أي ملاحظات خاصة بالمريض...">{{ $patient->notes }}</textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50/50 dark:bg-white/[0.02] flex justify-end gap-3 shrink-0">
                    <button type="button" data-modal-close="modal-edit-patient-{{ $patient->id }}"
                            class="px-4 py-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition-all shadow-sm hover:shadow-brand-500/30 hover:shadow-lg active:scale-95">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach

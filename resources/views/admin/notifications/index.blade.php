<x-layouts.admin>
    <x-slot:title>إدارة إشعارات العيادات</x-slot:title>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm font-semibold text-red-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>يرجى التأكد من ملء جميع الحقول المطلوبة بشكل صحيح.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Send Notification Form --}}
        <div class="lg:col-span-1">
            <div class="bg-[#0A0A12] border border-white/[0.06] rounded-3xl p-6 shadow-2xl sticky top-24">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/[0.06]">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">إرسال إشعار جديد</h2>
                        <p class="text-xs text-slate-400">سيظهر الإشعار فوراً في لوحة العيادة</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">العيادة المستهدفة</label>
                        <select name="clinic_id" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
                            <option value="all">🌐 إلى جميع العيادات في المنصة ({{ $clinics->count() }})</option>
                            @foreach($clinics as $clinic)
                                <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>🏥 {{ $clinic->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">عنوان الإشعار <span class="text-red-400">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="مثال: تحديث جديد في النظام / تنبيه هام..." class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">نوع الإشعار <span class="text-red-400">*</span></label>
                        <select name="type" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>ℹ️ معلومات عامة (أزرق)</option>
                            <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>✨ تحديث جديد / خبر سار (أخضر)</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>⚠️ تنبيه هام (أصفر)</option>
                            <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>🚨 تحذير / أمر عاجل (أحمر)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">نص الإشعار <span class="text-red-400">*</span></label>
                        <textarea name="message" rows="4" required placeholder="اكتب تفاصيل الإشعار هنا..." class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 resize-none">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 text-sm">
                        <span>إرسال الإشعار الآن</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Sent Notifications Table --}}
        <div class="lg:col-span-2">
            <div class="bg-[#0A0A12] border border-white/[0.06] rounded-3xl p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                    <div>
                        <h2 class="text-base font-bold text-white">سجل الإشعارات المرسلة</h2>
                        <p class="text-xs text-slate-400">قائمة بجميع الإشعارات التي تم إرسالها وحالة قراءتها</p>
                    </div>
                    <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs font-bold text-slate-300">
                        الإجمالي: {{ $notifications->total() }}
                    </span>
                </div>

                @if($notifications->isEmpty())
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-4 text-slate-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-white mb-1">لا توجد إشعارات مرسلة بعد</h3>
                        <p class="text-xs text-slate-500">استخدم النموذج الجانبي لإرسال أول إشعار للعيادات.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 text-[11px] font-bold text-slate-400 uppercase">
                                    <th class="py-3 px-3">العيادة</th>
                                    <th class="py-3 px-3">العنوان والرسالة</th>
                                    <th class="py-3 px-3">النوع</th>
                                    <th class="py-3 px-3">الحالة (is_read)</th>
                                    <th class="py-3 px-3">التاريخ</th>
                                    <th class="py-3 px-3 text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.04] text-xs">
                                @foreach($notifications as $item)
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="py-3.5 px-3 font-bold text-white whitespace-nowrap">
                                            @if($item->clinic)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-slate-200">
                                                    🏥 {{ $item->clinic->name }}
                                                </span>
                                            @else
                                                <span class="text-slate-500">عيادة محذوفة</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3 max-w-xs">
                                            <div class="font-bold text-slate-200 mb-0.5">{{ $item->title }}</div>
                                            <div class="text-[11px] text-slate-400 line-clamp-2">{{ $item->message }}</div>
                                        </td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            @switch($item->type)
                                                @case('success')
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold text-[10px]">✨ تحديث / سار</span>
                                                    @break
                                                @case('warning')
                                                    <span class="px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold text-[10px]">⚠️ تنبيه</span>
                                                    @break
                                                @case('danger')
                                                    <span class="px-2 py-0.5 rounded-md bg-red-500/10 border border-red-500/20 text-red-400 font-bold text-[10px]">🚨 عاجل</span>
                                                    @break
                                                @default
                                                    <span class="px-2 py-0.5 rounded-md bg-blue-500/10 border border-blue-500/20 text-blue-400 font-bold text-[10px]">ℹ️ معلومات</span>
                                            @endswitch
                                        </td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            @if($item->is_read)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[10px]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    مقروء (true)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[10px]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    غير مقروء (false)
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-400 whitespace-nowrap text-[11px]">
                                            {{ $item->created_at->diffForHumans() }}
                                        </td>
                                        <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                            <form method="POST" action="{{ route('admin.notifications.destroy', $item->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="حذف" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin>
    <x-slot:title>إعدادات المنصة</x-slot:title>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-400 mb-6">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- WhatsApp Service & Messaging Global Switch Card --}}
        <div class="bg-white dark:bg-[#0f1115] border border-slate-200 dark:border-white/5 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/10">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">إعدادات إشعارات وخدمة الواتساب التلقائية</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $whatsappEnabled ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-red-500/10 text-red-500 border border-red-500/20' }}">
                                {{ $whatsappEnabled ? 'مفعل حالياً 🟢' : 'متوقف ومُعطل 🔴' }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed max-w-2xl">
                            تحكم في تشغيل أو إيقاف كافة الرسائل والإشعارات التلقائية عبر واتساب في جميع أنحاء المنصة بضغطة زر واحدة.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="pt-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/5 rounded-2xl p-5 sm:p-6 space-y-4">
                    <label class="relative flex items-start gap-4 cursor-pointer select-none">
                        <input type="checkbox" name="whatsapp_enabled" value="1" class="sr-only peer" {{ $whatsappEnabled ? 'checked' : '' }}>
                        <div class="w-14 h-8 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                        <div class="flex-1">
                            <span class="block font-black text-base text-slate-900 dark:text-white">تفعيل خدمة إرسال رسائل الواتساب (Global WhatsApp Switch)</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                عند تفعيل هذا الخيار: يتم إرسال رسائل الترحيب وكود التحقق عند إنشاء عيادة، ورموز استعادة كلمة المرور، ورسائل تأكيد الحجز، وتذكيرات المواعيد تلقائياً عبر الواتساب.
                            </span>
                        </div>
                    </label>

                    <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/10 text-xs text-amber-600 dark:text-amber-400 leading-relaxed space-y-1">
                        <p class="font-bold">⚡ ما الذي يحدث عند إيقاف (تعطيل) هذا الإعداد؟</p>
                        <ul class="list-disc list-inside space-y-1 text-slate-600 dark:text-slate-400">
                            <li><strong>إنشاء عيادة جديدة:</strong> لا يطلب من المستخدم كود تحقق واتساب، بل يتم إدخاله وتسجيل دخوله مباشرة للوحة التحكم فور التسجيل.</li>
                            <li><strong>نسيت كلمة المرور:</strong> يتم إرسال رمز التحقق عبر <strong>البريد الإلكتروني فقط</strong> بدلاً من الواتساب.</li>
                            <li><strong>تذكيرات المواعيد ورسائل الحجوزات:</strong> تتوقف تماماً جميع الرسائل التلقائية وتذكيرات المواعيد التي ترسل للمرضى عبر الواتساب.</li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-sm shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-98 cursor-pointer flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        حفظ الإعدادات وتطبيق التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

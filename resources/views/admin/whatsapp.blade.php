<x-layouts.admin>
    <x-slot:title>إدارة اتصال خدمة الواتساب السريعة</x-slot:title>

    @if(session('success'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div data-flash class="flex items-center gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm font-semibold text-red-400">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- STATUS & QR CARD --}}
        <div class="lg:col-span-2 bg-[#0A0A12] border border-white/[0.06] rounded-3xl p-8 shadow-2xl relative overflow-hidden flex flex-col items-center justify-center min-h-[480px] text-center">
            
            {{-- Background decorative glow --}}
            <div id="status-glow" class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-indigo-500/10 blur-[100px] pointer-events-none transition-all duration-700"></div>

            {{-- 1. STATE: INITIALIZING / DISCONNECTED --}}
            <div id="state-loading" class="space-y-4 max-w-md mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto animate-spin text-indigo-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <h3 class="text-lg font-black text-white">جاري التحقق من حالة خادم الواتساب...</h3>
                <p class="text-xs text-slate-400 leading-relaxed">يتأكد النظام حالياً من تشغيل خدمة (whatsapp-web.js Microservice) في الخلفية.</p>
            </div>

            {{-- 2. STATE: QR RECEIVED (SCAN REQUIRED) --}}
            <div id="state-qr" class="hidden space-y-6 max-w-md mx-auto animate-fade-in">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    في انتظار تصوير الباركود (Scan Required)
                </div>

                <div>
                    <h3 class="text-xl font-black text-white mb-2">امسح كود الـ QR لربط الواتساب</h3>
                    <p class="text-xs text-slate-400">افتح تطبيق الواتساب في هاتفك > الأجهزة المرتبطة > ربط جهاز، ثم وجه الكاميرا للمربعات أدناه.</p>
                </div>

                <div class="p-4 bg-white rounded-3xl shadow-2xl mx-auto inline-block border-4 border-indigo-500/30">
                    <img id="qr-image" src="" alt="WhatsApp QR Code" class="w-64 h-64 object-contain">
                </div>

                <p class="text-[11px] text-slate-500">يتغير رمز الباركود تلقائياً في حال انتهاء صلاحيته.</p>
            </div>

            {{-- 3. STATE: CONNECTED / READY --}}
            <div id="state-ready" class="hidden space-y-6 max-w-md mx-auto animate-fade-in">
                <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/10">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    متصل ونشط حالياً (Connected 🟢)
                </div>

                <div>
                    <h3 class="text-2xl font-black text-white" id="connected-name">حساب الواتساب المرتبط</h3>
                    <p class="text-sm font-mono text-indigo-300 dir-ltr mt-1" id="connected-phone">+201XXXXXXXXX</p>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed">الخدمة تعمل بكفاءة. يتم الآن إرسال كود التحقق (OTP) للعيادات، ورسائل تأكيد الحجوزات، وتنبيهات المواعيد تلقائياً للعملاء.</p>

                <div class="pt-4 border-t border-white/[0.06]">
                    <form method="POST" action="{{ route('admin.whatsapp.logout') }}" data-confirm="هل أنت متأكد من رغبتك في فصل حساب الواتساب الحالي؟ سيطلب منك تصوير باركود جديد.">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer">
                            فصل الحساب وإعادة التصوير (Logout Session)
                        </button>
                    </form>
                </div>
            </div>

            {{-- 4. STATE: SERVICE DOWN / ERROR --}}
            <div id="state-error" class="hidden space-y-4 max-w-md mx-auto animate-fade-in">
                <div class="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg font-black text-white">خادم الواتساب (Microservice) غير متصل</h3>
                <p class="text-xs text-slate-400 leading-relaxed">تعذر الاتصال بخادم Node.js السريع على البورت 3000. قم بتشغيله في موجه الأوامر:</p>
                <div class="p-3 bg-black/60 rounded-xl border border-white/10 font-mono text-xs text-amber-300 dir-ltr select-all">
                    node whatsapp-server.cjs
                </div>
            </div>

        </div>

        {{-- TEST MESSAGE CARD --}}
        <div class="bg-[#0A0A12] border border-white/[0.06] rounded-3xl p-6 sm:p-8 shadow-2xl flex flex-col justify-between h-fit">
            <div>
                <h3 class="text-lg font-black text-white flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    إرسال رسالة اختبار فورية
                </h3>
                <p class="text-xs text-slate-500 mb-6">جرب إرسال رسالة لأي رقم للتأكد من سرعة وصول التنبيهات.</p>

                <form method="POST" action="{{ route('admin.whatsapp.test') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">رقم الهاتف (مع كود الدولة مثلاً 2010...)</label>
                        <input type="text" name="phone" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white dir-ltr font-mono" placeholder="201556477029">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">نصف الرسالة</label>
                        <textarea name="message" required rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="مرحباً بك من نظام Clinora SaaS"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-sm shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-98 cursor-pointer">
                        إرسال رسالة الاختبار الآن
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-6 border-t border-white/[0.06] text-center">
                <p class="text-[11px] text-slate-500">يعتمد النظام على حزمة whatsapp-web.js مع حفظ دائم للجلسة داخل مجلد .wwebjs_auth</p>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const elLoading = document.getElementById('state-loading');
            const elQR      = document.getElementById('state-qr');
            const elReady   = document.getElementById('state-ready');
            const elError   = document.getElementById('state-error');
            const glow      = document.getElementById('status-glow');
            const qrImage   = document.getElementById('qr-image');

            function hideAll() {
                elLoading.classList.add('hidden');
                elQR.classList.add('hidden');
                elReady.classList.add('hidden');
                elError.classList.add('hidden');
            }

            async function checkStatus() {
                try {
                    const res = await fetch('{{ route("admin.whatsapp.status") }}', { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    hideAll();

                    if (!data.success && data.status === 'disconnected' && data.error?.includes('متوقف')) {
                        elError.classList.remove('hidden');
                        glow.className = 'absolute -top-32 -left-32 w-80 h-80 rounded-full bg-red-500/10 blur-[100px] pointer-events-none';
                        return;
                    }

                    if (data.status === 'ready' || data.status === 'authenticated') {
                        elReady.classList.remove('hidden');
                        glow.className = 'absolute -top-32 -left-32 w-80 h-80 rounded-full bg-emerald-500/15 blur-[100px] pointer-events-none';
                        if (data.info) {
                            document.getElementById('connected-name').textContent = data.info.pushname || 'حساب الواتساب المرتبط';
                            document.getElementById('connected-phone').textContent = '+' + (data.info.wid?.split('@')[0] || '');
                        }
                    } else if (data.status === 'qr' && data.qr) {
                        elQR.classList.remove('hidden');
                        glow.className = 'absolute -top-32 -left-32 w-80 h-80 rounded-full bg-amber-500/15 blur-[100px] pointer-events-none';
                        qrImage.src = data.qr;
                    } else {
                        elLoading.classList.remove('hidden');
                        glow.className = 'absolute -top-32 -left-32 w-80 h-80 rounded-full bg-indigo-500/10 blur-[100px] pointer-events-none';
                    }

                } catch (err) {
                    hideAll();
                    elError.classList.remove('hidden');
                }
            }

            // Check immediately and poll every 3 seconds
            checkStatus();
            setInterval(checkStatus, 3000);
        });
    </script>
    @endpush
</x-layouts.admin>

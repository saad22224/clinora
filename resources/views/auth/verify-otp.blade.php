<x-layout>
    <x-slot:title>تفعيل حساب العيادة عبر الواتساب</x-slot>

    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-10 rounded-[2.5rem] shadow-xl border border-gray-200 dark:border-gray-800">
            <div class="text-center">
                <svg class="w-12 h-12 text-brand-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">تفعيل حساب العيادة</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    أدخل كود التحقق (OTP) المرسل لواتساب العيادة
                </p>
            </div>
            
            @if(config('app.debug') && session('whatsapp_otp'))
                <div class="bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 p-4 rounded-xl text-sm border border-amber-200 dark:border-amber-800 text-center font-mono font-bold select-all">
                    🛠️ [وضع التطوير]: كود التفعيل هو ({{ session('whatsapp_otp') }})
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-200 text-center font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('otp.verify.post') }}" method="POST">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 text-center">كود التحقق المكون من 6 أرقام</label>
                    <input id="otp" name="otp" type="text" required maxlength="6" dir="ltr" autocomplete="one-time-code" autofocus
                           class="appearance-none block w-full text-center tracking-[0.5em] font-mono text-2xl font-black px-4 py-4 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-800 dark:text-white"
                           placeholder="••••••">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        تأكيد الكود والدخول للنظام
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <p class="text-xs text-gray-400">
                    لم يصلك الكود؟ يمكنك نسخ الكود من المستطيل الأصفر أعلاه للدخول فوراً.
                </p>
            </div>
        </div>
    </div>
</x-layout>

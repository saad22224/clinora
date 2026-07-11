@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-8 sm:p-12 rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10"
            data-aos="zoom-in" data-aos-duration="600">
            @php
                $isWhatsApp = isset($isWhatsAppEnabled) ? $isWhatsAppEnabled : \App\Models\SystemSetting::isWhatsAppEnabled();
            @endphp
            <div class="text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold mb-3 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>{{ $isWhatsApp ? 'التحقق عبر الواتساب' : 'التحقق عبر البريد الإلكتروني' }}</span>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">تأكيد كود التحقق</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    {{ $isWhatsApp ? 'أدخل كود التحقق (OTP) المرسل لواتساب العيادة للمتابعة لتغيير كلمة المرور' : 'أدخل كود التحقق (OTP) المرسل إلى بريدك الإلكتروني للمتابعة لتغيير كلمة المرور' }}
                </p>
            </div>

            {{-- @if (config('app.debug') && session('password_reset_otp'))
                <div class="bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 p-4 rounded-2xl text-sm border border-amber-200/80 dark:border-amber-800/80 text-center font-mono font-bold select-all shadow-sm">
                    <span class="block text-xs font-sans text-amber-600 dark:text-amber-400 mb-1">🛠️ [وضع التطوير السريع]</span>
                    كود التحقق: <span class="text-base font-black bg-amber-100 dark:bg-amber-900/80 px-3 py-1 rounded-lg">{{ session('password_reset_otp') }}</span>
                </div>
            @endif --}}

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-2xl text-sm font-bold border border-red-200 dark:border-red-800 text-center flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('password.whatsapp.verify') }}" method="POST">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-3 text-center">كود التحقق المكون من 6 أرقام</label>
                    <input id="otp" name="otp" type="text" required maxlength="6" dir="ltr"
                        autocomplete="one-time-code" autofocus
                        class="appearance-none block w-full text-center tracking-[0.5em] font-mono text-2xl font-black px-4 py-4 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900/90 dark:text-white transition-all"
                        placeholder="••••••">
                </div>

                <div class="pt-2">
                    <button style="background-color: red;" type="submit"
                        class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5 cursor-pointer">
                        <span>تأكيد الكود والمتابعة</span>
                        <span class="material-symbols-outlined text-xl">arrow_back_ios</span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                    لم يصلك الكود على الواتساب؟ <a href="{{ route('password.request') }}" class="text-brand-600 hover:underline font-bold">إعادة المحاولة</a> أو التواصل مع <a
                        href="{{ url('/#contact') }}" class="text-brand-600 hover:underline font-bold">الدعم الفني</a>.
                </p>
            </div>
        </div>
    </div>
@endsection

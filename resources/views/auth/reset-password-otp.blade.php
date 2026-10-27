@extends('components.layout')

@section('content')
    <div class="min-h-[82vh] flex items-center justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-7" data-aos="fade-up" data-aos-duration="500">
            @php
                $isWhatsApp = isset($isWhatsAppEnabled) ? $isWhatsAppEnabled : \App\Models\SystemSetting::isWhatsAppEnabled();
            @endphp
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-extrabold mb-1">
                    <span class="material-symbols-outlined text-sm">pin</span>
                    <span>خطوة التحقق من الهوية</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    تأكيد كود التحقق
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    أدخل كود التحقق (OTP) المكون من 6 أرقام للمتابعة إلى تعيين كلمة المرور الجديدة.
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="bg-rose-500/10 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-bold border border-rose-500/30 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('password.whatsapp.verify') }}" method="POST">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-3 text-center">
                        كود التحقق المكون من 6 أرقام
                    </label>
                    <input id="otp" name="otp" type="text" required maxlength="6" dir="ltr"
                           autocomplete="one-time-code" autofocus
                           class="clinora-input block w-full text-center tracking-[0.5em] font-mono text-2xl font-black px-4 py-4 rounded-xl"
                           placeholder="••••••">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-6 rounded-xl shadow-md text-base font-extrabold text-white bg-[#34548A] hover:bg-[#2B4675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#34548A] transition-all hover:-translate-y-0.5 cursor-pointer">
                        <span>تأكيد الكود والمتابعة</span>
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                    </button>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-slate-200/80 dark:border-white/[0.07]">
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    لم يصلك الكود؟
                    <a href="{{ route('password.request') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-extrabold">إعادة المحاولة</a>
                    أو التواصل مع
                    <a href="{{ url('/#contact') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-extrabold">الدعم الفني</a>.
                </p>
            </div>
        </div>
    </div>
@endsection

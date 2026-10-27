@extends('components.layout')

@section('content')
    <div class="min-h-[82vh] flex items-center justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-7" data-aos="fade-up" data-aos-duration="500">
            @php
                $isWhatsApp = isset($isWhatsAppEnabled) ? $isWhatsAppEnabled : \App\Models\SystemSetting::isWhatsAppEnabled();
            @endphp
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-700 dark:text-indigo-300 text-xs font-extrabold mb-1">
                    <span class="material-symbols-outlined text-sm">lock_reset</span>
                    <span>استعادة حساب العيادة</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    نسيت كلمة المرور؟
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    {{ $isWhatsApp ? 'أدخل البريد الإلكتروني أو رقم الهاتف المسجل لإرسال كود التحقق واستعادة حسابك.' : 'أدخل البريد الإلكتروني المسجل في حسابك وسنرسل لك كود التحقق فوراً.' }}
                </p>
            </div>

            @if(session('error'))
                <div role="alert" class="bg-rose-500/10 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-bold border border-rose-500/30 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form id="forgot-password-form" class="space-y-5" action="{{ route('password.whatsapp.send') }}" method="POST">
                @csrf
                <div>
                    <label for="login_input" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                        {{ $isWhatsApp ? 'البريد الإلكتروني أو رقم الهاتف المسجل' : 'البريد الإلكتروني المسجل في الحساب' }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </span>
                        <input id="login_input" name="login_input" type="{{ $isWhatsApp ? 'text' : 'email' }}" value="{{ old('login_input') }}" required dir="ltr" autofocus
                               aria-invalid="{{ $errors->has('login_input') ? 'true' : 'false' }}"
                               @error('login_input') aria-describedby="login-input-error" @enderror
                               class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right {{ $errors->has('login_input') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                               placeholder="{{ $isWhatsApp ? 'doctor@clinic.com أو 010XXXXXXXX' : 'doctor@clinic.com' }}">
                    </div>
                    @error('login_input')
                        <span id="login-input-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span>{{ $message }}</span>
                        </span>
                    @enderror
                </div>

                <div class="pt-2">
                    <button id="forgot-submit-btn" type="submit"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-6 rounded-xl shadow-md text-base font-extrabold text-white bg-[#34548A] hover:bg-[#2B4675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#34548A] transition-all hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span class="material-symbols-outlined text-xl">send</span>
                        <span id="forgot-submit-label">إرسال كود التحقق</span>
                    </button>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-slate-200/80 dark:border-white/[0.07]">
                <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">
                    تذكرت كلمة المرور؟
                    <a href="{{ route('login') }}" class="font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">العودة لتسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('forgot-password-form');
            var submitBtn = document.getElementById('forgot-submit-btn');
            var submitLabel = document.getElementById('forgot-submit-label');
            if (form && submitBtn && submitLabel) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitLabel.textContent = 'جاري إرسال الكود...';
                });
            }
        });
    </script>
@endsection

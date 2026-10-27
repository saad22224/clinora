@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-12 sm:py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-7 glass dark:glass-dark p-6 sm:p-10 rounded-[2rem] sm:rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-extrabold mb-3 border border-brand-200/60 dark:border-brand-800/60">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                    <span>بوابة الدخول الآمنة للعيادات</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">مرحباً بعودتك دكتورنا</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    سجل دخولك للوصول إلى لوحة تحكم عيادتك وملفات مرضاك
                </p>
            </div>

            @if(session('success'))
                <div role="status" class="bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl text-sm font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-2xl text-sm font-bold border border-red-200 dark:border-red-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form id="login-form" class="mt-6 space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">
                            البريد الإلكتروني للعيادة / الطبيب <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required dir="ltr"
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   @error('email') aria-describedby="email-error" @enderror
                                   class="appearance-none block w-full px-4 py-3.5 border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                                   placeholder="admin@clinic.com">
                        </div>
                        @error('email')
                            <span id="email-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300">
                                كلمة المرور <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-extrabold text-brand-600 hover:text-brand-500 dark:text-brand-400 transition-colors">
                                نسيت كلمة المرور؟
                            </a>
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="current-password" required dir="ltr"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   @error('password') aria-describedby="password-error" @enderror
                                   class="appearance-none block w-full pl-12 pr-4 py-3.5 border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                                   placeholder="••••••••">
                            <button type="button" id="toggle-login-password" aria-label="إظهار أو إخفاء كلمة المرور"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 hover:text-brand-600 focus:outline-none">
                                <span class="material-symbols-outlined text-xl" id="toggle-login-password-icon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <span id="password-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                               class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800">
                        <label for="remember-me" class="mr-2 block text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                            تذكر دخول هذا الجهاز
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button id="login-submit-btn" type="submit"
                            class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-base sm:text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span id="login-submit-label">تسجيل الدخول للنظام</span>
                        <span class="material-symbols-outlined text-xl">login</span>
                    </button>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-gray-200/80 dark:border-gray-800 space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    ليس لدى عيادتك حساب بعد؟
                    <a href="{{ route('register') }}" class="font-black text-brand-600 hover:text-brand-500 dark:text-brand-400 hover:underline">أنشئ حسابك مجاناً الآن</a>
                </p>
                <div class="flex items-center justify-center gap-2 text-xs text-gray-400 font-medium">
                    <span class="material-symbols-outlined text-sm text-emerald-600">lock</span>
                    <span>اتصال آمن ومشفّر لحماية بيانات المرضى</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle-login-password');
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-login-password-icon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }

            const form = document.getElementById('login-form');
            const submitBtn = document.getElementById('login-submit-btn');
            const submitLabel = document.getElementById('login-submit-label');
            if (form && submitBtn && submitLabel) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitLabel.textContent = 'جاري تسجيل الدخول...';
                });
            }
        });
    </script>
@endsection
@endsection
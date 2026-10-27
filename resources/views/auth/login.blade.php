@extends('components.layout')

@section('content')
    <div class="min-h-[82vh] flex items-center justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-7" data-aos="fade-up" data-aos-duration="500">
            <!-- Header (Matches Reference Image 4) -->
            <div class="text-center space-y-2">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    تسجيل الدخول
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    مرحباً بعودتك! أدخل بيانات حسابك للوصول إلى لوحة تحكم عيادتك.
                </p>
            </div>

            @if(session('success'))
                <div role="status" class="bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl text-sm font-bold border border-emerald-500/30 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="bg-rose-500/10 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-bold border border-rose-500/30 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form id="login-form" class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            البريد الإلكتروني
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">mail</span>
                            </span>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required dir="ltr"
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   @error('email') aria-describedby="email-error" @enderror
                                   class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right {{ $errors->has('email') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="doctor@clinic.com">
                        </div>
                        @error('email')
                            <span id="email-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200">
                                كلمة المرور
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline transition-colors">
                                نسيت كلمة المرور؟
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">lock</span>
                            </span>
                            <input id="password" name="password" type="password" autocomplete="current-password" required dir="ltr"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   @error('password') aria-describedby="password-error" @enderror
                                   class="clinora-input block w-full pr-11 pl-11 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right {{ $errors->has('password') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="••••••••••••••••">
                            <button type="button" id="toggle-login-password" aria-label="إظهار أو إخفاء كلمة المرور" aria-pressed="false"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                                <span class="material-symbols-outlined text-xl" id="toggle-login-password-icon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <span id="password-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember-me" class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <input id="remember-me" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-[#34548A] focus:ring-[#34548A] dark:bg-[#181E31]">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                            تذكر دخول هذا الجهاز
                        </span>
                    </label>
                </div>

                <!-- Submit Button (Matches Reference Image 4 Slate-Blue Button) -->
                <div class="pt-2">
                    <button id="login-submit-btn" type="submit"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-6 rounded-xl shadow-md text-base font-extrabold text-white bg-[#34548A] hover:bg-[#2B4675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#34548A] transition-all hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span class="material-symbols-outlined text-xl">login</span>
                        <span id="login-submit-label">تسجيل الدخول</span>
                    </button>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-slate-200/80 dark:border-white/[0.07] space-y-2">
                <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">
                    ليس لدى عيادتك حساب بعد؟
                    <a href="{{ route('register') }}" class="font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">إنشاء حساب عيادة جديد</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('toggle-login-password');
            var passwordInput = document.getElementById('password');
            var icon = document.getElementById('toggle-login-password-icon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }

            var form = document.getElementById('login-form');
            var submitBtn = document.getElementById('login-submit-btn');
            var submitLabel = document.getElementById('login-submit-label');
            if (form && submitBtn && submitLabel) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitLabel.textContent = 'جاري تسجيل الدخول...';
                });
            }
        });
    </script>
@endsection

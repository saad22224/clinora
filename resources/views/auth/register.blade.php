@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-12 sm:py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-7 glass dark:glass-dark p-6 sm:p-10 rounded-[2rem] sm:rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold mb-3 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>تجربة مجانية لمدة 30 يوماً — بدون بطاقة ائتمان</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">إنشاء عيادة جديدة</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    أنشئ حساب عيادتك في دقيقة واحدة وابدأ إدارة المرضى والمواعيد فوراً
                </p>
            </div>

            <form id="register-form" class="mt-6 space-y-4" action="{{ route('register.post') }}" method="POST">
                @csrf
                @php $currentRef = $ref ?? request()->query('ref', session('referral_code')); @endphp
                @if($currentRef)
                    <input type="hidden" name="ref" value="{{ $currentRef }}">
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="clinic_name" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">
                            اسم العيادة أو المركز الطبي <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input id="clinic_name" name="clinic_name" type="text" autocomplete="organization" value="{{ old('clinic_name') }}" required
                               aria-invalid="{{ $errors->has('clinic_name') ? 'true' : 'false' }}"
                               @error('clinic_name') aria-describedby="clinic-name-error" @enderror
                               class="appearance-none block w-full px-4 py-3.5 border {{ $errors->has('clinic_name') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white font-medium transition-all"
                               placeholder="مثال: عيادات الشفاء التخصصية">
                        @error('clinic_name')
                            <span id="clinic-name-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">
                            اسم الطبيب أو المدير المسؤول <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required
                               aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                               @error('name') aria-describedby="name-error" @enderror
                               class="appearance-none block w-full px-4 py-3.5 border {{ $errors->has('name') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white font-medium transition-all"
                               placeholder="مثال: د. أحمد المنصور">
                        @error('name')
                            <span id="name-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">
                            البريد الإلكتروني للعيادة <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required dir="ltr"
                               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                               @error('email') aria-describedby="email-error" @enderror
                               class="appearance-none block w-full px-4 py-3.5 border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                               placeholder="admin@clinic.com">
                        @error('email')
                            <span id="email-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">
                            رقم هاتف العيادة أو الواتساب <span class="text-red-500" aria-hidden="true">*</span>
                            <span class="text-xs text-brand-600 dark:text-brand-400 font-normal">(للتواصل وتفعيل الحساب)</span>
                        </label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" required dir="ltr"
                               aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                               @error('phone') aria-describedby="phone-error" @enderror
                               class="appearance-none block w-full px-4 py-3.5 border {{ $errors->has('phone') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-mono font-medium transition-all"
                               placeholder="01012345678">
                        @error('phone')
                            <span id="phone-error" role="alert" class="text-red-600 text-xs font-bold mt-1.5 flex items-center gap-1">
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
                            <span id="password-hint" class="text-xs text-gray-500 font-medium">8 أحرف على الأقل</span>
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required dir="ltr"
                                   aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   class="appearance-none block w-full pl-12 pr-4 py-3.5 border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-brand-500 focus:border-brand-500' }} rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                                   placeholder="••••••••">
                            <button type="button" id="toggle-register-password" aria-label="إظهار أو إخفاء كلمة المرور"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 hover:text-brand-600 focus:outline-none">
                                <span class="material-symbols-outlined text-xl" id="toggle-register-password-icon">visibility</span>
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

                <div class="pt-3">
                    <button id="register-submit-btn" type="submit"
                            class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-base sm:text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span id="register-submit-label">تسجيل واشتراك مجاني</span>
                        <span class="material-symbols-outlined text-xl">rocket_launch</span>
                    </button>
                    <p class="text-[11px] text-gray-500 text-center mt-3 font-semibold">
                        بإنشاء حسابك أنت توافق على <a href="{{ route('terms') }}" class="underline hover:text-brand-600">شروط الخدمة</a> و<a href="{{ route('privacy') }}" class="underline hover:text-brand-600">سياسة الخصوصية</a>.
                    </p>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-gray-200/80 dark:border-gray-800 space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    لدى عيادتك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="font-black text-brand-600 hover:text-brand-500 dark:text-brand-400 hover:underline">سجل الدخول من هنا</a>
                </p>
                <div class="flex items-center justify-center gap-2 text-xs text-gray-400 font-medium">
                    <span class="material-symbols-outlined text-sm text-emerald-600">verified_user</span>
                    <span>بيانات عيادتك ومرضاك معزولة ومحمية بالكامل</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle-register-password');
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-register-password-icon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }

            const form = document.getElementById('register-form');
            const submitBtn = document.getElementById('register-submit-btn');
            const submitLabel = document.getElementById('register-submit-label');
            if (form && submitBtn && submitLabel) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitLabel.textContent = 'جاري إنشاء الحساب...';
                });
            }
        });
    </script>
@endsection


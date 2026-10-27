@extends('components.layout')

@section('content')
    <div class="min-h-[84vh] flex items-center justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-7" data-aos="fade-up" data-aos-duration="500">
            <!-- Header -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-extrabold mb-1">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    <span>تجربة مجانية لمدة 30 يوماً — بدون بطاقة ائتمان</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    إنشاء عيادة جديدة
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    أدخل بيانات عيادتك للبدء فوراً في إدارة المواعيد والملفات الطبية.
                </p>
            </div>

            <form id="register-form" class="space-y-4" action="{{ route('register.post') }}" method="POST">
                @csrf
                @php $currentRef = $ref ?? request()->query('ref', session('referral_code')); @endphp
                @if($currentRef)
                    <input type="hidden" name="ref" value="{{ $currentRef }}">
                @endif

                <div class="space-y-4">
                    <!-- Clinic Name -->
                    <div>
                        <label for="clinic_name" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            اسم العيادة أو المركز الطبي
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">domain</span>
                            </span>
                            <input id="clinic_name" name="clinic_name" type="text" autocomplete="organization" value="{{ old('clinic_name') }}" required
                                   aria-invalid="{{ $errors->has('clinic_name') ? 'true' : 'false' }}"
                                   @error('clinic_name') aria-describedby="clinic-name-error" @enderror
                                   class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-medium {{ $errors->has('clinic_name') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="مثال: عيادات الشفاء التخصصية">
                        </div>
                        @error('clinic_name')
                            <span id="clinic-name-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <!-- Doctor / Owner Name -->
                    <div>
                        <label for="name" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            اسم الطبيب أو المدير المسؤول
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">person</span>
                            </span>
                            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required
                                   aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                   @error('name') aria-describedby="name-error" @enderror
                                   class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-medium {{ $errors->has('name') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="مثال: د. أحمد المنصور">
                        </div>
                        @error('name')
                            <span id="name-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            البريد الإلكتروني للعيادة
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

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            رقم هاتف العيادة
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">call</span>
                            </span>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" required dir="ltr"
                                   aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                   @error('phone') aria-describedby="phone-error" @enderror
                                   class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-mono font-medium text-right {{ $errors->has('phone') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="01012345678">
                        </div>
                        @error('phone')
                            <span id="phone-error" role="alert" class="text-rose-600 dark:text-rose-400 text-xs font-bold mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>{{ $message }}</span>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200">
                                كلمة المرور
                            </label>
                            <span id="password-hint" class="text-xs text-slate-500 dark:text-slate-400 font-semibold">8 أحرف على الأقل</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">lock</span>
                            </span>
                            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required dir="ltr"
                                   aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                   class="clinora-input block w-full pr-11 pl-11 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right {{ $errors->has('password') ? '!border-rose-500 focus:!ring-rose-500/20' : '' }}"
                                   placeholder="••••••••••••••••">
                            <button type="button" id="toggle-register-password" aria-label="إظهار أو إخفاء كلمة المرور" aria-pressed="false"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                                <span class="material-symbols-outlined text-xl" id="toggle-register-password-icon">visibility</span>
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

                <!-- Submit Button -->
                <div class="pt-3">
                    <button id="register-submit-btn" type="submit"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-6 rounded-xl shadow-md shadow-amber-500/20 text-base font-black text-white bg-[#F59E0B] hover:bg-[#D97706] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span class="material-symbols-outlined text-xl">person_add</span>
                        <span id="register-submit-label">إنشاء حساب العيادة مجاناً</span>
                    </button>
                    <p class="text-xs text-slate-500 dark:text-slate-400 text-center mt-3 font-medium">
                        بإنشاء حسابك أنت توافق على <a href="{{ route('terms') }}" class="underline hover:text-indigo-600 dark:hover:text-indigo-400 font-bold">شروط الاستخدام</a> و<a href="{{ route('privacy') }}" class="underline hover:text-indigo-600 dark:hover:text-indigo-400 font-bold">سياسة الخصوصية</a>.
                    </p>
                </div>
            </form>

            <div class="text-center pt-5 border-t border-slate-200/80 dark:border-white/[0.07] space-y-2">
                <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">
                    لدى عيادتك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">تسجيل الدخول من هنا</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('toggle-register-password');
            var passwordInput = document.getElementById('password');
            var icon = document.getElementById('toggle-register-password-icon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }

            var form = document.getElementById('register-form');
            var submitBtn = document.getElementById('register-submit-btn');
            var submitLabel = document.getElementById('register-submit-label');
            if (form && submitBtn && submitLabel) {
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitLabel.textContent = 'جاري إنشاء الحساب...';
                });
            }
        });
    </script>
@endsection

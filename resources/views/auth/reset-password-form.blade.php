@extends('components.layout')

@section('content')
    <div class="min-h-[82vh] flex items-center justify-center py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-7" data-aos="fade-up" data-aos-duration="500">
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-extrabold mb-1">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <span>تم التحقق بنجاح</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    كلمة المرور الجديدة
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    أدخل كلمة المرور الجديدة وتأكيدها لحفظها والدخول إلى حسابك.
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="bg-rose-500/10 text-rose-600 dark:text-rose-400 p-4 rounded-xl text-sm font-bold border border-rose-500/30 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form class="space-y-5" action="{{ route('password.whatsapp.reset') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            كلمة المرور الجديدة
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">lock</span>
                            </span>
                            <input id="password" name="password" type="password" required dir="ltr" autofocus autocomplete="new-password"
                                   class="clinora-input block w-full pr-11 pl-11 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right"
                                   placeholder="••••••••••••••••">
                            <button type="button" id="toggle-reset-password" aria-label="إظهار أو إخفاء كلمة المرور" aria-pressed="false"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                                <span class="material-symbols-outlined text-xl" id="toggle-reset-password-icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                            تأكيد كلمة المرور الجديدة
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 dark:text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-xl">lock_Person</span>
                            </span>
                            <input id="password_confirmation" name="password_confirmation" type="password" required dir="ltr" autocomplete="new-password"
                                   class="clinora-input block w-full pr-11 pl-4 py-3.5 rounded-xl text-sm sm:text-base font-medium text-right"
                                   placeholder="••••••••••••••••">
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-6 rounded-xl shadow-md text-base font-extrabold text-white bg-[#34548A] hover:bg-[#2B4675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#34548A] transition-all hover:-translate-y-0.5 cursor-pointer">
                        <span class="material-symbols-outlined text-xl">verified</span>
                        <span>حفظ كلمة المرور والدخول</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('toggle-reset-password');
            var passwordInput = document.getElementById('password');
            var icon = document.getElementById('toggle-reset-password-icon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function () {
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }
        });
    </script>
@endsection

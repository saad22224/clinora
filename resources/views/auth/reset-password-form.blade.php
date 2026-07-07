@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-8 sm:p-12 rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10"
            data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold mb-3 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>تم التحقق بنجاح</span>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">كلمة المرور الجديدة</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    أدخل كلمة المرور الجديدة وتأكيدها لتغيير كلمة سر حسابك
                </p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-2xl text-sm font-bold border border-red-200 dark:border-red-800 text-center flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('password.whatsapp.reset') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="password" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور الجديدة</label>
                        <input id="password" name="password" type="password" required dir="ltr" autofocus
                            class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">تأكيد كلمة المرور الجديدة</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required dir="ltr"
                            class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button style="background-color: red;" type="submit"
                        class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5 cursor-pointer">
                        <span>حفظ كلمة المرور والدخول</span>
                        <span class="material-symbols-outlined text-xl">verified</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

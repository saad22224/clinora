@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-8 sm:p-12 rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-extrabold mb-3 border border-brand-200/60 dark:border-brand-800/60">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                    <span>استعادة كلمة المرور</span>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">نسيت كلمة المرور؟</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    أدخل البريد الإلكتروني أو رقم هاتف العيادة المسجل وسنرسل لك كود التحقق عبر واتساب
                </p>
            </div>
            
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-2xl text-sm font-bold border border-red-200 dark:border-red-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('password.whatsapp.send') }}" method="POST">
                @csrf
                <div>
                    <label for="login_input" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني أو رقم هاتف العيادة</label>
                    <div class="relative">
                        <input id="login_input" name="login_input" type="text" value="{{ old('login_input') }}" required dir="ltr" autofocus
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all" 
                               placeholder="admin@clinic.com أو 010XXXXXXXX">
                    </div>
                    @error('login_input')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                </div>

                <div class="pt-2">
                    <button style="background-color: red;" type="submit" class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5 cursor-pointer">
                        <span>إرسال كود التحقق عبر واتساب</span>
                        <span class="material-symbols-outlined text-xl">send</span>
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-8 pt-6 border-t border-gray-200/80 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    تذكرت كلمة المرور؟ 
                    <a href="{{ route('login') }}" class="font-black text-brand-600 hover:text-brand-500 dark:text-brand-400 hover:underline">تسجيل الدخول الآن</a>
                </p>
            </div>
        </div>
    </div>
@endsection

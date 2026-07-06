@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-8 sm:p-12 rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">
                {{-- <a href="{{ url('/#hero') }}" class="inline-block group">
                    <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="شعار كلينورا" class="w-16 h-16 object-contain mx-auto rounded-2xl shadow-lg mb-4 group-hover:scale-105 transition-transform duration-300 border border-white/20">
                </a> --}}
                {{-- <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-extrabold mb-3 border border-brand-200/60 dark:border-brand-800/60">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                    <span>بوابة الدخول الموحدة</span>
                </div> --}}
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">مرحباً بعودتك دكتورنا</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    سجل دخولك للوصول إلى لوحة تحكم عيادتك وملفات مرضاك
                </p>
            </div>
            
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-2xl text-sm font-bold border border-red-200 dark:border-red-800 flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني للعيادة / الطبيب</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" autocomplete="email" required dir="ltr" 
                                   class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all" 
                                   placeholder="admin@clinic.com">
                        </div>
                        @error('email')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="current-password" required dir="ltr" 
                                   class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all" 
                                   placeholder="••••••••">
                        </div>
                        @error('password')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800">
                        <label for="remember-me" class="mr-2 block text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                            تذكر دخول هذا الجهاز
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-extrabold text-brand-600 hover:text-brand-500 dark:text-brand-400 transition-colors">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                </div>

                <div class="pt-2">
                    <button style="    background-color: red;" type="submit" class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5">
                        <span>تسجيل الدخول للنظام</span>
                        <span class="material-symbols-outlined text-xl">login</span>
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-8 pt-6 border-t border-gray-200/80 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    ليس لدى عيادتك حساب بعد؟ 
                    <a href="{{ route('register') }}" class="font-black text-brand-600 hover:text-brand-500 dark:text-brand-400 hover:underline">أنشئ حسابك مجاناً الآن</a>
                </p>
            </div>
        </div>
    </div>
@endsection
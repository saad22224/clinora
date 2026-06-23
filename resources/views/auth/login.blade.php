<x-layout>
    <x-slot:title>تسجيل الدخول</x-slot>

    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-10 rounded-[2.5rem] shadow-xl border border-gray-200 dark:border-gray-800">
            <div class="text-center">
                <svg class="w-12 h-12 text-brand-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">مرحباً بعودتك</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    سجل دخولك لإدارة عيادتك
                </p>
            </div>
            
            @if(session('error'))
                <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" autocomplete="email" required dir="ltr" class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white text-right" placeholder="admin@clinic.com">
                        @error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required dir="ltr" class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white text-right" placeholder="••••••••">
                        @error('password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                        <label for="remember-me" class="mr-2 block text-sm text-gray-900 dark:text-gray-300">
                            تذكرني
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-brand-600 hover:text-brand-500">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5">
                        تسجيل الدخول
                    </button>
                </div>
            </form>
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    ليس لديك حساب؟ 
                    <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-500">قم بإنشاء حساب جديد</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>

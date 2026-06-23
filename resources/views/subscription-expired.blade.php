<x-layout>
    <x-slot:title>تجديد الاشتراك</x-slot>

    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-10 rounded-[2.5rem] shadow-xl border border-red-200 dark:border-red-900">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/50 rounded-2xl flex items-center justify-center text-red-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">انتهت فترة الاشتراك</h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                    عذراً، لقد انتهت فترة اشتراكك في النظام. يرجى تجديد الاشتراك للتمكن من الوصول إلى لوحة التحكم والبيانات الخاصة بعيادتك.
                </p>
            </div>
            
            <div class="flex flex-col space-y-4 mt-8">
                <a href="{{ route('pricing') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5">
                    تجديد الاشتراك الآن
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm text-lg font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition-all">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>

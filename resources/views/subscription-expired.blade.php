@extends('components.layout')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-950">
        <div
            class="max-w-md w-full space-y-8 glass dark:glass-dark p-10 rounded-[2.5rem] shadow-xl border border-red-200 dark:border-red-900">
            <div class="text-center">
                <div
                    class="w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/50 rounded-2xl flex items-center justify-center text-red-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">انتهت فترة الاشتراك</h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                    عذراً، لقد انتهت فترة اشتراكك في النظام. يرجى تجديد الاشتراك للتمكن من الوصول إلى لوحة التحكم والبيانات
                    الخاصة بعيادتك.
                </p>
                <p class="mt-3 text-base font-bold text-gray-800 dark:text-gray-200">
                    يرجي التواصل مع الدعم لتجديد الإشتراك
                </p>
            </div>

            <div class="flex flex-col space-y-4 mt-8">
                <a href="{{ route('pricing') }}"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5">
                    تجديد الاشتراك الآن
                </a>

                <a href="https://wa.me/201099615358?text={{ urlencode('مرحباً، أود الاستفسار عن تجديد اشتراك العيادة') }}" target="_blank" rel="noopener noreferrer"
                    class="w-full flex items-center justify-center gap-2.5 py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-[#25D366] hover:bg-[#20b858] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#25D366] transition-all hover:-translate-y-0.5">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.98-.365-1.739-.767-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.28.072.383-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 1.856.001 3.601.723 4.913 2.034 1.312 1.311 2.035 3.058 2.035 4.915-.001 3.824-3.113 6.931-6.948 6.931z"/>
                    </svg>
                    تواصل عبر واتساب
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm text-lg font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition-all">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

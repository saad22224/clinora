@extends('components.layout')

@section('content')
    <div class="min-h-[82vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full clinora-card rounded-3xl p-7 sm:p-10 space-y-6">
            <div class="text-center space-y-3">
                <div class="w-16 h-16 mx-auto bg-rose-500/10 border border-rose-500/30 rounded-2xl flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined text-3xl">warning</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">انتهت فترة الاشتراك</h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    عذراً، لقد انتهت فترة اشتراكك أو تم إيقاف الحساب مؤقتاً. يرجى تجديد الاشتراك للتمكن من الوصول إلى لوحة التحكم والبيانات الخاصة بعيادتك.
                </p>
                <p class="text-sm font-extrabold text-slate-800 dark:text-slate-200">
                    يرجى التواصل مع إدارة المنصة لتجديد الاشتراك
                </p>
            </div>

            <div class="flex flex-col space-y-3 pt-2">
                <a href="{{ route('pricing') }}"
                    class="w-full flex justify-center items-center gap-2 py-3.5 px-6 rounded-xl shadow-md text-base font-black text-white bg-[#F59E0B] hover:bg-[#D97706] transition-all hover:-translate-y-0.5">
                    <span>عرض الباقات وتجديد الاشتراك</span>
                </a>

                <a href="{{ url('/#contact') }}"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl shadow-sm text-base font-bold text-white bg-[#34548A] hover:bg-[#2B4675] transition-all hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                    <span>التواصل مع الدعم الفني</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100/80 dark:bg-white/5 hover:bg-slate-200/70 dark:hover:bg-white/10 transition-all cursor-pointer">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

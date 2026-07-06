@extends('components.layout')

@section('content')
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decorative Background Glow -->
        <div class="absolute top-1/4 left-1/3 -translate-x-1/2 w-96 h-96 bg-gradient-to-tr from-brand-500/15 via-teal-500/10 to-transparent blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-8 sm:p-12 rounded-[2.5rem] shadow-2xl border border-gray-200/80 dark:border-gray-800/80 relative z-10" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center">

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold mb-3 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>تجربة مجانية لمدة 30 يوماً</span>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">إنشاء عيادة جديدة</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                    انضم إلى آلاف الأطباء وابدأ أتمتة عيادتك اليوم
                </p>
            </div>
            
            <form class="mt-8 space-y-5" action="{{ route('register.post') }}" method="POST">
                @csrf
                @php $currentRef = $ref ?? request()->query('ref', session('referral_code')); @endphp
                @if($currentRef)
                    <input type="hidden" name="ref" value="{{ $currentRef }}">
                @endif
                
                <div class="space-y-4">
                    <div>
                        <label for="clinic_name" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">اسم العيادة أو المركز الطبي *</label>
                        <input id="clinic_name" name="clinic_name" type="text" required 
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white font-medium transition-all" 
                               placeholder="مثال: عيادات الشفاء التخصصية">
                        @error('clinic_name')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">اسم الطبيب أو المدير المسؤول *</label>
                        <input id="name" name="name" type="text" required 
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white font-medium transition-all" 
                               placeholder="مثال: د. أحمد المنصور">
                        @error('name')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني للعيادة *</label>
                        <input id="email" name="email" type="email" required dir="ltr" 
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all" 
                               placeholder="admin@clinic.com">
                        @error('email')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">رقم هاتف العيادة أو الواتساب * <span class="text-xs text-brand-600 dark:text-brand-400 font-normal">(لإرسال كود التفعيل)</span></label>
                        <input id="phone" name="phone" type="text" required dir="ltr" 
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-mono font-medium transition-all" 
                               placeholder="0501234567">
                        @error('phone')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-extrabold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور *</label>
                        <input id="password" name="password" type="password" required dir="ltr" 
                               class="appearance-none block w-full px-4 py-3.5 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-900/90 dark:text-white text-right font-medium transition-all" 
                               placeholder="••••••••">
                        @error('password')<span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="pt-3">
                    <button style="    background-color: red;" type="submit" class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-black text-white bg-gradient-to-l from-brand-600 to-teal-600 hover:from-brand-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 transition-all hover:-translate-y-0.5">
                        <span>تسجيل واشتراك مجاني</span>
                        <span class="material-symbols-outlined text-xl">rocket_launch</span>
                    </button>
                    <p class="text-[11px] text-gray-400 text-center mt-3 font-semibold">
                        بإنشاء حسابك أنت توافق على <a href="{{ route('terms') }}" class="underline hover:text-brand-600">شروط الخدمة</a> و<a href="{{ route('privacy') }}" class="underline hover:text-brand-600">سياسة الخصوصية</a>.
                    </p>
                </div>
            </form>
            
            <div class="text-center mt-8 pt-6 border-t border-gray-200/80 dark:border-gray-800">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                    لدى عيادتك حساب بالفعل؟ 
                    <a href="{{ route('login') }}" class="font-black text-brand-600 hover:text-brand-500 dark:text-brand-400 hover:underline">سجل الدخول من هنا</a>
                </p>
            </div>
        </div>
    </div>
@endsection

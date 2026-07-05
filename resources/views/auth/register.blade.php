<x-layout>
    <x-slot:title>حساب جديد</x-slot>

    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-md w-full space-y-8 glass dark:glass-dark p-10 rounded-[2.5rem] shadow-xl border border-gray-200 dark:border-gray-800">
            <div class="text-center">
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">إنشاء عيادة جديدة</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    انضم إلى كلينورا وابدأ تجربتك المجانية
                </p>
            </div>
            
            <form class="mt-8 space-y-6" action="{{ route('register.post') }}" method="POST">
                @csrf
                @php $currentRef = $ref ?? request()->query('ref', session('referral_code')); @endphp
                @if($currentRef)
                    <input type="hidden" name="ref" value="{{ $currentRef }}">
                @endif
                <div class="space-y-4">
                    <div>
                        <label for="clinic_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم العيادة</label>
                        <input id="clinic_name" name="clinic_name" type="text" required class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white" placeholder="عيادة الشفاء">
                        @error('clinic_name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم المدير (أو الطبيب)</label>
                        <input id="name" name="name" type="text" required class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white" placeholder="د. أحمد">
                        @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" required dir="ltr" class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white text-right" placeholder="admin@clinic.com">
                        @error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم هاتف العيادة (إرسال كود تفعيل الواتساب)</label>
                        <input id="phone" name="phone" type="text" required dir="ltr" class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white text-right font-mono" placeholder="01012345678">
                        @error('phone')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور</label>
                        <input id="password" name="password" type="password" required dir="ltr" class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-base dark:bg-gray-800 dark:text-white text-right" placeholder="••••••••">
                        @error('password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5">
                        تسجيل واشتراك مجاني
                    </button>
                </div>
            </form>
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    لديك حساب بالفعل؟ 
                    <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-500">سجل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>

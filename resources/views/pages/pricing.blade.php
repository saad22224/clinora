<x-layout>
    <x-slot:title>الأسعار</x-slot>
    <x-slot:description>أسعار بسيطة وشفافة للعيادات بمختلف أحجامها. ابدأ مجاناً.</x-slot>

    <!-- Pricing Header -->
    <div class="pt-32 pb-16 text-center max-w-3xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-6 leading-tight">أسعار بسيطة، بدون تعقيدات</h1>
        <p class="text-xl text-gray-500 dark:text-gray-400 leading-relaxed">لا توجد رسوم خفية. اختر الباقة التي تتناسب مع حجم وطموح عيادتك.</p>
        
        <!-- Toggle (Visual Only) -->
        <div class="mt-10 flex justify-center items-center gap-3">
            <span class="text-base font-bold text-gray-900 dark:text-white">شهري</span>
            <button type="button" class="bg-brand-600 relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                <span aria-hidden="true" class="-translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
            </button>
            <span class="text-base font-bold text-gray-500 dark:text-gray-400">سنوي <span class="text-green-500 font-extrabold mr-1">خصم ٢٠٪</span></span>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            
            <!-- Free Plan -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-3xl p-8 bg-white dark:bg-gray-900 shadow-sm flex flex-col hover:shadow-lg transition-shadow">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">التجربة المجانية</h3>
                <p class="text-gray-500 dark:text-gray-400 text-base mb-6 leading-relaxed">مثالية لتجربة وتقييم النظام بالكامل.</p>
                <div class="mb-6">
                    <span class="text-5xl font-extrabold text-gray-900 dark:text-white">٠</span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium"> دولار / ١٤ يوم</span>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        حتى ٥٠ مريض
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        صفحة حجز مبسطة
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        حساب طبيب واحد
                    </li>
                </ul>
                <a href="#" class="w-full py-4 px-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-bold rounded-xl text-center transition-colors">ابدأ التجربة المجانية</a>
            </div>

            <!-- Pro Plan -->
            <div class="border-2 border-brand-600 rounded-3xl p-8 bg-white dark:bg-gray-900 shadow-2xl relative flex flex-col transform md:-translate-y-4">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-brand-600 text-white px-4 py-1.5 rounded-full text-sm font-bold tracking-wide uppercase">الأكثر شعبية</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">الباقة الاحترافية</h3>
                <p class="text-gray-500 dark:text-gray-400 text-base mb-6 leading-relaxed">للعيادات والمراكز الطبية النامية.</p>
                <div class="mb-6">
                    <span class="text-5xl font-extrabold text-brand-600 dark:text-brand-500">٤٩</span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium"> دولار / شهرياً</span>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-center text-gray-800 dark:text-gray-200 font-medium">
                        <svg class="w-6 h-6 text-brand-500 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        عدد لامحدود من المرضى
                    </li>
                    <li class="flex items-center text-gray-800 dark:text-gray-200 font-medium">
                        <svg class="w-6 h-6 text-brand-500 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        نطاق مخصص لصفحة الحجز
                    </li>
                    <li class="flex items-center text-gray-800 dark:text-gray-200 font-medium">
                        <svg class="w-6 h-6 text-brand-500 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        ٣ حسابات للموظفين
                    </li>
                    <li class="flex items-center text-gray-800 dark:text-gray-200 font-medium">
                        <svg class="w-6 h-6 text-brand-500 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        تقارير وفواتير متقدمة
                    </li>
                </ul>
                <a href="#" class="w-full py-4 px-4 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-center transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">اشترك الآن</a>
            </div>

            <!-- Enterprise Plan -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-3xl p-8 bg-white dark:bg-gray-900 shadow-sm flex flex-col hover:shadow-lg transition-shadow">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">باقة المستشفيات</h3>
                <p class="text-gray-500 dark:text-gray-400 text-base mb-6 leading-relaxed">للمستشفيات وشبكات الرعاية الصحية الكبيرة.</p>
                <div class="mb-6">
                    <span class="text-5xl font-extrabold text-gray-900 dark:text-white">١٩٩</span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium"> دولار / شهرياً</span>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        جميع مميزات الباقة الاحترافية
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        عدد لامحدود من الموظفين
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        صلاحية الوصول للواجهة البرمجية API
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-6 h-6 text-gray-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        دعم فني مخصص على مدار الساعة
                    </li>
                </ul>
                <a href="#" class="w-full py-4 px-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-bold rounded-xl text-center transition-colors">تواصل مع المبيعات</a>
            </div>

        </div>
    </div>
</x-layout>

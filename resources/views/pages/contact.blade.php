<x-layout>
    <x-slot:title>اتصل بنا</x-slot>
    <x-slot:description>تواصل مع فريق المبيعات أو الدعم الفني.</x-slot>

    <!-- Header -->
    <div class="pt-32 pb-16 text-center max-w-3xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-6 leading-tight">تواصل معنا</h1>
        <p class="text-xl text-gray-500 dark:text-gray-400 leading-relaxed">فريقنا مستعد دائماً للإجابة على استفساراتك ومساعدتك في تطوير عيادتك.</p>
    </div>

    <!-- Contact Info Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-800 text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 mx-auto bg-brand-50 dark:bg-brand-900/50 rounded-2xl flex items-center justify-center text-brand-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">الدعم عبر البريد</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">نقوم بالرد عادةً خلال أقل من ساعتين.</p>
                <a href="mailto:support@clinora.com" class="text-brand-600 font-bold hover:text-brand-500 transition-colors" dir="ltr">support@clinora.com</a>
            </div>
            <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-800 text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 mx-auto bg-indigo-50 dark:bg-indigo-900/50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">الهاتف المباشر</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">من الأحد للخميس (٩ صباحاً - ٦ مساءً).</p>
                <a href="tel:+1234567890" class="text-brand-600 font-bold hover:text-brand-500 transition-colors" dir="ltr">+966 55 123 4567</a>
            </div>
            <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-800 text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 mx-auto bg-green-50 dark:bg-green-900/50 rounded-2xl flex items-center justify-center text-green-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">المقر الرئيسي</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">طريق الملك فهد، برج الابتكار<br>الرياض، المملكة العربية السعودية</p>
                <a href="#" class="text-brand-600 font-bold hover:text-brand-500 transition-colors">احصل على الاتجاهات</a>
            </div>
        </div>
    </div>

    <!-- Contact Form & Map -->
    <div class="bg-gray-50 dark:bg-gray-950 py-24 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-xl overflow-hidden flex flex-col lg:flex-row border border-gray-200 dark:border-gray-800">
                <!-- Form -->
                <div class="p-10 lg:p-16 lg:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">أرسل لنا رسالة</h2>
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الاسم الأول</label>
                                <input type="text" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-base dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3" placeholder="أحمد">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم العائلة</label>
                                <input type="text" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-base dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3" placeholder="محمد">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                            <input type="email" dir="ltr" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-base dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 text-right" placeholder="ahmed@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الرسالة</label>
                            <textarea rows="5" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-base dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 resize-none" placeholder="كيف يمكننا مساعدتك؟"></textarea>
                        </div>
                        <button type="button" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-md text-lg font-bold text-white bg-brand-600 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all hover:-translate-y-0.5">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
                <!-- Map Placeholder -->
                <div class="bg-gray-100 dark:bg-gray-800 lg:w-1/2 min-h-[400px] flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80')] bg-cover bg-center opacity-50 dark:opacity-20 mix-blend-multiply dark:mix-blend-overlay"></div>
                    <div class="relative z-10 text-center p-8 glass dark:glass-dark rounded-2xl mx-8">
                        <svg class="w-12 h-12 text-brand-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <p class="text-gray-900 dark:text-white font-bold text-lg">الرياض، المملكة العربية السعودية</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">نحن متواجدون في قلب المركز التكنولوجي</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

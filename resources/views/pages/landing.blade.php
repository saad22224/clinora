<x-layout>
    <x-slot:title>الرئيسية</x-slot>
    <x-slot:description>برنامج إدارة العيادات الأحدث والأكثر تطوراً، صُمم خصيصاً لتسهيل العمليات وتسريع نمو عيادتك.</x-slot>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-brand-100 via-white to-white dark:from-brand-900/20 dark:via-gray-950 dark:to-gray-950"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-8 leading-tight">
                أدِر عيادتك بـ <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-l from-brand-500 to-indigo-600">دقة متناهية.</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl leading-relaxed text-gray-500 dark:text-gray-400 mb-10">
                كلينورا هو النظام السحابي المتكامل الذي يقوم بأتمتة الحجوزات، وإدارة السجلات الطبية، والفوترة، لتتفرغ أنت لما هو أهم: مرضاك.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#" class="px-8 py-4 text-lg font-bold rounded-xl text-white bg-brand-600 hover:bg-brand-500 transition-all shadow-lg hover:shadow-brand-500/30 hover:-translate-y-1">ابدأ تجربتك المجانية</a>
                <a href="#features" class="px-8 py-4 text-lg font-bold rounded-xl text-gray-700 bg-white dark:text-gray-200 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">احجز عرضاً توضيحياً</a>
            </div>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">لا تتطلب بطاقة ائتمان • تجربة مجانية لمدة ١٤ يوماً</p>
        </div>
    </section>

    <!-- Trust Indicators -->
    <section class="py-12 border-y border-gray-100 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-bold text-gray-500 tracking-wide uppercase mb-8">موثوق من قبل أكثر من ٥,٠٠٠ ممارس صحي في الشرق الأوسط</p>
            <div class="flex justify-center items-center gap-12 md:gap-24 flex-wrap opacity-50 grayscale dark:opacity-40">
                <!-- Placeholder Logos -->
                <div class="text-2xl font-black text-gray-400">الرعاية بلس+</div>
                <div class="text-2xl font-black text-gray-400">نبض الطب</div>
                <div class="text-2xl font-black text-gray-400">عيادات الأفق</div>
                <div class="text-2xl font-black text-gray-400">الشفاء التخصصية</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-brand-600 font-bold tracking-wide text-sm uppercase">المميزات الأساسية</h2>
                <p class="mt-2 text-3xl leading-snug font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">كل ما تحتاجه لإدارة عيادتك بنجاح</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-shadow duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-brand-50 dark:bg-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">جدولة ذكية للمواعيد</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">صفحات حجز تلقائية تدعم المناطق الزمنية المختلفة، مع مزامنة التقويم وإرسال رسائل تذكير نصية للمرضى.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-shadow duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">سجلات طبية إلكترونية</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">ملفات مرضى منظمة وآمنة تحتوي على التاريخ الطبي، والوصفات الطبية، وتخزين مشفر للملفات الطبية والأشعة.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-shadow duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-green-50 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">نظام فوترة متكامل</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">إنشاء الفواتير بسهولة، تتبع النفقات والإيرادات، وعرض لوحات تحكم مرئية للأرباح في الوقت الفعلي.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Benefits -->
    <section class="py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl leading-tight">مصمم لقطاع الرعاية الصحية الحديث.</h2>
                    <p class="mt-4 text-lg text-gray-500 dark:text-gray-400 leading-relaxed">لقد صممنا كلينورا للقضاء على الأعمال الإدارية الروتينية. تضمن بنيتنا السحابية متعددة المستأجرين أن تكون بياناتك معزولة تماماً، آمنة، وفائقة السرعة.</p>
                    
                    <dl class="mt-10 space-y-8">
                        <div class="flex">
                            <div class="shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-brand-500 text-white shadow-lg">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            <div class="mr-4">
                                <dt class="text-lg leading-6 font-bold text-gray-900 dark:text-white">صفحة هبوط مخصصة لعيادتك</dt>
                                <dd class="mt-2 text-base text-gray-500 dark:text-gray-400 leading-relaxed">تحصل كل عيادة تلقائياً على صفحة حجز جميلة ومحسنة لمحركات البحث (SEO) بألوان متناسقة مع هويتك التجارية.</dd>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-brand-500 text-white shadow-lg">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            <div class="mr-4">
                                <dt class="text-lg leading-6 font-bold text-gray-900 dark:text-white">صلاحيات وصول دقيقة</dt>
                                <dd class="mt-2 text-base text-gray-500 dark:text-gray-400 leading-relaxed">أذونات دقيقة للملاك، الأطباء، وموظفي الاستقبال للحفاظ على سير عمل عيادتك بسلاسة دون المساومة على الأمان.</dd>
                            </div>
                        </div>
                    </dl>
                </div>
                <div class="mt-12 lg:mt-0 relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-brand-100 to-indigo-50 dark:from-brand-900/30 dark:to-indigo-900/30 rounded-[2rem] transform -rotate-3 scale-105 transition-transform hover:rotate-0 duration-500"></div>
                    <img class="relative rounded-[2rem] shadow-2xl border border-gray-200/50 dark:border-gray-800/50" src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="لوحة تحكم نظام إدارة العيادات">
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="bg-brand-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center relative z-10">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl leading-tight">
                هل أنت مستعد لتطوير عيادتك؟
            </h2>
            <p class="mt-4 text-xl leading-relaxed text-brand-100">
                انضم إلى آلاف الأطباء الذين قاموا بتحديث عياداتهم. ابدأ تجربتك المجانية لمدة ١٤ يوماً اليوم. لا تتطلب بطاقة ائتمان.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <a href="#" class="px-8 py-4 text-lg font-bold rounded-xl text-brand-600 bg-white hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">أنشئ حسابك الآن</a>
            </div>
        </div>
    </section>
</x-layout>

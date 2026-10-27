@extends('components.layout')

@section('content')
    <main>
        <!-- Background Blobs -->
        <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden opacity-30 pointer-events-none">
            <div
                class="absolute -top-[10%] -right-[5%] w-[40%] h-[40%] bg-primary rounded-full blur-[120px] mix-blend-multiply">
            </div>
            <div
                class="absolute top-[20%] -left-[10%] w-[35%] h-[35%] bg-secondary-container rounded-full blur-[120px] mix-blend-multiply">
            </div>
            <div
                class="absolute -bottom-[10%] right-[20%] w-[30%] h-[30%] bg-tertiary-fixed-dim rounded-full blur-[120px] mix-blend-multiply">
            </div>
        </div>

        <!-- Section 2: Hero -->
        <section
            class="px-4 sm:px-6 md:px-margin-desktop py-8 sm:py-12 md:py-section-gap max-w-container-max mx-auto flex flex-col md:flex-row items-center gap-8 md:gap-12 overflow-hidden">
            <div class="w-full md:w-1/2 space-y-4 sm:space-y-6 text-center md:text-right" data-aos="fade-up"
                data-aos-duration="800">
                <span
                    class="inline-block px-4 py-1.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-md text-xs sm:text-sm font-bold shadow-sm">
                    الإصدار 2.0 الآن مع الذكاء الاصطناعي الطبي
                </span>
                <h1
                    class="font-display-lg text-3xl sm:text-4xl md:text-5xl lg:text-display-lg gradient-text leading-tight font-black">
                    نمِّ عيادتك<br />بالذكاء
                    الاصطناعي</h1>
                <p
                    class="font-body-lg text-sm sm:text-base md:text-body-lg text-on-surface-variant max-w-lg mx-auto md:mx-0 leading-relaxed font-medium">
                    كلينورا هي منصة متكاملة لإدارة العيادات تجمع بين إدارة المرضى، الحجز الإلكتروني، أتمتة واتساب،
                    الذكاء الاصطناعي الطبي، الروشتات، المواعيد، والتحليلات في لوحة تحكم واحدة أنيقة.
                </p>
                <div
                    class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center md:justify-start gap-3 sm:gap-4 pt-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}"
                        class="bg-primary-container text-on-primary px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl font-bold font-label-md text-sm sm:text-base shadow-xl shadow-primary/30 hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-center inline-block">ابدأ
                        الآن مجانًا</a>
                    <a href="#how-it-works"
                        class="glass text-on-surface px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl font-bold font-label-md text-sm sm:text-base hover:bg-surface-variant/50 transition-all duration-300 w-full sm:w-auto text-center inline-block">اطلب
                        عرضًا تجريبيًا</a>
                </div>
            </div>
            <div class="w-full md:w-1/2 relative min-h-[350px] sm:min-h-[450px] md:min-h-[500px] mt-6 md:mt-0"
                data-aos="fade-up" data-aos-duration="900" data-aos-delay="150">
                <!-- Dashboard Mockup -->
                <div
                    class="glass rounded-2xl sm:rounded-3xl w-full aspect-video soft-bloom p-4 sm:p-6 relative overflow-hidden border border-white/60 shadow-xl">
                    <div
                        class="flex items-center justify-between border-b border-outline-variant pb-3 sm:pb-4 mb-3 sm:mb-4">
                        <div class="flex gap-1.5 sm:gap-2">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-error"></div>
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-secondary-container"></div>
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-tertiary-fixed-dim"></div>
                        </div>
                        <div class="w-32 sm:w-48 h-3 sm:h-4 bg-surface-container-high rounded-full"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:gap-4">
                        <div class="col-span-2 space-y-2 sm:space-y-4">
                            <div class="h-20 sm:h-32 bg-surface-container-low rounded-xl"></div>
                            <div class="grid grid-cols-2 gap-2 sm:gap-4">
                                <div class="h-14 sm:h-24 bg-surface-container-low rounded-xl"></div>
                                <div class="h-14 sm:h-24 bg-surface-container-low rounded-xl"></div>
                            </div>
                        </div>
                        <div class="space-y-2 sm:space-y-4">
                            <div class="h-full bg-surface-container-low rounded-xl"></div>
                        </div>
                    </div>
                </div>
                <!-- Floating Cards -->
                <div
                    class="absolute -top-4 sm:-top-8 left-1 sm:-left-4 glass p-2.5 sm:p-4 rounded-xl soft-bloom float-animation float-delayed-1 w-36 sm:w-48 shadow-lg border border-white/80 z-10">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-primary-container flex items-center justify-center text-on-primary shrink-0">
                            <span class="material-symbols-outlined text-sm sm:text-base">calendar_today</span>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-label-sm font-label-sm text-on-surface-variant font-bold">المواعيد
                            </p>
                            <p class="text-sm sm:text-headline-md font-headline-md font-extrabold text-primary">24</p>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute top-1/4 -right-1 sm:-right-6 glass p-2.5 sm:p-4 rounded-xl soft-bloom float-animation float-delayed-2 w-36 sm:w-48 shadow-lg border border-white/80 z-10">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-tertiary-container flex items-center justify-center text-on-tertiary shrink-0">
                            <span class="material-symbols-outlined text-sm sm:text-base">payments</span>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-label-sm font-label-sm text-on-surface-variant font-bold">إيرادات
                                اليوم</p>
                            <p class="text-sm sm:text-headline-md font-headline-md font-extrabold text-tertiary-container">
                                1.2 ألف $</p>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute -bottom-4 sm:bottom-6 left-1 sm:-left-6 glass p-2.5 sm:p-4 rounded-xl soft-bloom float-animation float-delayed-3 w-44 sm:w-56 border border-cyan-400/40 shadow-lg z-10">
                    <div class="flex items-center gap-2 sm:gap-3 mb-1.5 sm:mb-2">
                        <div
                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-xs sm:text-sm"
                                style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        </div>
                        <p class="text-xs sm:text-label-md font-label-md font-bold text-on-surface">المساعد الذكي</p>
                    </div>
                    <div class="space-y-1.5 sm:space-y-2">
                        <div class="h-1.5 sm:h-2 w-full bg-outline-variant/60 rounded-full"></div>
                        <div class="h-1.5 sm:h-2 w-3/4 bg-outline-variant/60 rounded-full"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Trust Bar -->
        <section id="about" class="bg-surface-container-low py-8 sm:py-12 border-y border-outline-variant/30">
            <div class="max-w-container-max mx-auto px-4 sm:px-6 md:px-margin-desktop text-center">
                <p class="font-label-md text-xs sm:text-label-md text-on-surface-variant mb-6 sm:mb-8 font-bold"
                    data-aos="fade-up">كل ما تحتاجه
                    عيادتك في منصة واحدة</p>
                <div class="flex flex-wrap justify-center gap-6 sm:gap-10 md:gap-16 lg:gap-24 items-center opacity-75">
                    <div class="flex items-center gap-2" data-aos="zoom-in" data-aos-delay="0">
                        <span class="material-symbols-outlined text-primary text-xl sm:text-2xl">verified_user</span>
                        <span class="font-bold text-on-surface text-xs sm:text-base">آمن ومشفّر</span>
                    </div>
                    <div class="flex items-center gap-2" data-aos="zoom-in" data-aos-delay="100">
                        <span class="material-symbols-outlined text-primary text-xl sm:text-2xl">cloud</span>
                        <span class="font-bold text-on-surface text-xs sm:text-base">مستضاف سحابيًا</span>
                    </div>
                    <div class="flex items-center gap-2" data-aos="zoom-in" data-aos-delay="200">
                        <span class="material-symbols-outlined text-primary text-xl sm:text-2xl">psychology</span>
                        <span class="font-bold text-on-surface text-xs sm:text-base">مدعوم بالذكاء الاصطناعي</span>
                    </div>
                    <div class="flex items-center gap-2" data-aos="zoom-in" data-aos-delay="300">
                        <span class="material-symbols-outlined text-primary text-xl sm:text-2xl">devices</span>
                        <span class="font-bold text-on-surface text-xs sm:text-base">مزامنة عبر جميع الأجهزة</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4: Features Grid -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-container-max mx-auto"
            id="features">
            <div class="text-center mb-12 sm:mb-16 space-y-3 sm:space-y-4" data-aos="fade-up">
                <h2 class="font-display-lg text-2xl sm:text-3xl md:text-headline-lg font-extrabold text-on-surface">أدوات
                    قوية للعيادات الحديثة</h2>
                <p
                    class="font-body-lg text-sm sm:text-base md:text-body-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
                    ركّز على رعاية مرضاك ودع
                    كلينورا تتولى الباقي بأتمتة ذكية ورؤى تحليلية دقيقة.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-gutter">
                <!-- AI Assistant -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="0">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-primary-fixed-dim flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl"
                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">المساعد الذكي
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">لخّص تاريخ المريض
                        وحلّل ملفات PDF
                        ونتائج التحاليل فورًا بالذكاء الاصطناعي الطبي.</p>
                </div>
                <!-- Online Booking -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-surface-container-high flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">event</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">الحجز الإلكتروني
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">امنح عيادتك رابط حجز
                        خاص بها ليتمكن
                        المرضى من حجز المواعيد على مدار الساعة.</p>
                </div>
                <!-- WhatsApp Automation -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-tertiary-fixed flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-tertiary text-2xl sm:text-3xl">chat</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">مزامنة واتساب
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">تأكيدات وتذكيرات
                        مواعيد آلية تُرسل
                        مباشرة إلى المرضى.</p>
                </div>
                <!-- EMR -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-surface-container-high flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">folder_shared</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">سجل طبي إلكتروني
                        متقدم</h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">تاريخ المريض الكامل
                        والسجلات والوسائط
                        منظمة في جدول زمني أنيق.</p>
                </div>
                <!-- Appointments -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="0">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-surface-container-high flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">calendar_month</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">طابور ذكي وإدارة مواعيد</h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">إدارة مرئية لطابور الانتظار اليومي لكل طبيب، مع مناداة صوتية وإعادة جدولة مرنة ورابط تتبّع لحظي للمريض.</p>
                </div>
                <!-- Digital Prescriptions -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-secondary-fixed flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-secondary text-2xl sm:text-3xl">description</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">روشتات إلكترونية جاهزة للطباعة
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">أنشئ روشتات رقمية احترافية باسم عيادتك مدعومة بقاعدة بيانات الأدوية المصرية وجاهزة للطباعة الفورية.</p>
                </div>
                <!-- Reception Dashboard -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-surface-container-high flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">monitoring</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">مركز الاستقبال وتواصل الفريق
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">حالة لحظية لغرف الانتظار، رسائل فورية بين الطبيب والسكرتارية، وصلاحيات مخصصة لكل موظف.</p>
                </div>
                <!-- Financial & Multi-Branch System -->
                <div class="glass p-6 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom hover:-translate-y-2 transition-all duration-300 group hover:border-primary/30 border border-white/60"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-surface-container-high flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform shadow-md">
                        <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">account_balance_wallet</span>
                    </div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-on-surface">الإدارة المالية وسلاسل العيادات
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm leading-relaxed">تتبّع الإيرادات والمصروفات والمديونيات بدقة، مع لوحة تحكم مجمعة لإدارة الفروع المتعددة.</p>
                </div>
            </div>
        </section>

        <!-- Section 5: Comparison Table -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-4xl mx-auto">
            <h2 class="font-display-lg text-2xl sm:text-3xl md:text-headline-lg font-extrabold text-center mb-8 sm:mb-12 text-on-surface"
                data-aos="fade-up">ميزة كلينورا التنافسية</h2>
            <div class="overflow-x-auto w-full rounded-2xl sm:rounded-3xl border border-outline-variant/60 glass soft-bloom shadow-xl"
                data-aos="fade-up" data-aos-delay="100">
                <table class="min-w-[500px] w-full border-collapse text-right">
                    <thead>
                        <tr class="bg-surface-container/80">
                            <th class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">الميزة</th>
                            <th
                                class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface-variant opacity-60">
                                العيادة
                                التقليدية</th>
                            <th class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-primary">كلينورا SaaS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/40">
                        <tr>
                            <td class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">حجز المرضى
                            </td>
                            <td class="p-4 sm:p-6 text-error text-xs sm:text-sm md:text-base font-semibold"><span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">close</span>
                                عبر الهاتف
                                فقط</td>
                            <td class="p-4 sm:p-6 text-tertiary-container text-xs sm:text-sm md:text-base font-extrabold">
                                <span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">check_circle</span>
                                إلكترونيًا على
                                مدار الساعة</td>
                        </tr>
                        <tr>
                            <td class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">الملخصات
                                الطبية</td>
                            <td class="p-4 sm:p-6 text-error text-xs sm:text-sm md:text-base font-semibold"><span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">close</span>
                                كتابة
                                يدوية</td>
                            <td class="p-4 sm:p-6 text-tertiary-container text-xs sm:text-sm md:text-base font-extrabold">
                                <span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">check_circle</span>
                                إنشاء فوري
                                بالذكاء الاصطناعي</td>
                        </tr>
                        <tr>
                            <td class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">الإشعارات</td>
                            <td class="p-4 sm:p-6 text-error text-xs sm:text-sm md:text-base font-semibold"><span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">close</span>
                                رسائل SMS
                                يدوية</td>
                            <td class="p-4 sm:p-6 text-tertiary-container text-xs sm:text-sm md:text-base font-extrabold">
                                <span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">check_circle</span>
                                واتساب تلقائي
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">الوصول إلى
                                البيانات</td>
                            <td class="p-4 sm:p-6 text-error text-xs sm:text-sm md:text-base font-semibold"><span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">close</span>
                                سيرفر محلي
                                فقط</td>
                            <td class="p-4 sm:p-6 text-tertiary-container text-xs sm:text-sm md:text-base font-extrabold">
                                <span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">check_circle</span>
                                سحابي من أي جهاز
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 sm:p-6 font-bold text-xs sm:text-sm md:text-base text-on-surface">سرعة الروشتة
                            </td>
                            <td class="p-4 sm:p-6 text-error text-xs sm:text-sm md:text-base font-semibold"><span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">close</span>
                                كتابة
                                يدوية</td>
                            <td class="p-4 sm:p-6 text-tertiary-container text-xs sm:text-sm md:text-base font-extrabold">
                                <span
                                    class="material-symbols-outlined align-middle ml-1 text-base sm:text-lg">check_circle</span>
                                قالب رقمي</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section 6: AI Section -->
        <section
            class="mx-3 sm:mx-6 md:mx-margin-desktop py-10 sm:py-16 md:py-section-gap rounded-2xl sm:rounded-3xl md:rounded-[40px] bg-gradient-to-br from-inverse-surface to-[#0b101c] text-on-primary-container overflow-hidden relative shadow-2xl"
            id="ai">
            <div class="absolute inset-0 pointer-events-none opacity-20">
                <div class="absolute top-0 left-0 w-96 h-96 bg-primary rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-cyan-500 rounded-full blur-[100px]"></div>
            </div>
            <div
                class="max-w-container-max mx-auto px-4 sm:px-8 md:px-margin-desktop flex flex-col lg:flex-row items-center gap-8 sm:gap-12 md:gap-16 relative z-10">
                <div class="w-full lg:w-2/5 space-y-4 sm:space-y-6 text-center lg:text-right" data-aos="fade-up">
                    <div class="flex items-center justify-center lg:justify-start gap-2 text-cyan-400 font-bold">
                        <span class="material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        <span class="font-label-md text-xs sm:text-label-md tracking-widest uppercase">الجيل القادم من
                            الذكاء</span>
                    </div>
                    <h2
                        class="font-display-lg text-2xl sm:text-3xl md:text-4xl lg:text-display-lg text-white leading-tight font-black">
                        تعرّف على مساعدك الطبي الذكي
                    </h2>
                    <p
                        class="font-body-lg text-sm sm:text-base md:text-body-lg text-outline-variant font-medium leading-relaxed">
                        يفهم الذكاء الاصطناعي المتخصص لدينا المصطلحات الطبية. يمكنه معالجة سنوات من تاريخ المريض ونتائج
                        التحاليل وقوائم الأدوية لتقديم ملخصات دقيقة وتحديد المخاطر المحتملة في ثوانٍ.
                    </p>
                    <ul class="space-y-3 sm:space-y-4 text-right inline-block md:block">
                        <li class="flex items-center gap-3 text-white font-semibold text-xs sm:text-sm md:text-base">
                            <span class="material-symbols-outlined text-cyan-400 shrink-0">check_circle</span>
                            <span>تلخيص الملاحظات السريرية فورًا وبدقة</span>
                        </li>
                        <li class="flex items-center gap-3 text-white font-semibold text-xs sm:text-sm md:text-base">
                            <span class="material-symbols-outlined text-cyan-400 shrink-0">check_circle</span>
                            <span>إبراز نتائج التحاليل والقراءات الحرجة</span>
                        </li>
                        <li class="flex items-center gap-3 text-white font-semibold text-xs sm:text-sm md:text-base">
                            <span class="material-symbols-outlined text-cyan-400 shrink-0">check_circle</span>
                            <span>التحقق من تفاعلات وتداخلات الأدوية</span>
                        </li>
                    </ul>
                </div>
                <div class="w-full lg:w-3/5" data-aos="fade-up" data-aos-delay="150">
                    <div
                        class="bg-surface-container-lowest/5 backdrop-blur-md rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-white/10 ai-glow shadow-2xl">
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Message 1 -->
                            <div class="flex gap-2.5 sm:gap-4 items-start">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-xs sm:text-sm">person</span>
                                </div>
                                <div class="bg-white/10 p-3 sm:p-4 rounded-2xl rounded-tr-none max-w-[90%] sm:max-w-[80%]">
                                    <p class="text-white text-xs sm:text-sm md:text-body-md font-medium leading-relaxed">هل
                                        يمكنك تلخيص تاريخ السيد أحمد مع التركيز على
                                        عوامل خطر القلب؟</p>
                                </div>
                            </div>
                            <!-- Message 2 -->
                            <div class="flex gap-2.5 sm:gap-4 items-start justify-end">
                                <div
                                    class="bg-primary/20 border border-primary/30 p-3.5 sm:p-5 rounded-2xl rounded-tl-none max-w-[92%] sm:max-w-[85%]">
                                    <div class="flex items-center gap-1.5 sm:gap-2 mb-2 sm:mb-3 text-cyan-400">
                                        <span class="material-symbols-outlined text-[10px] sm:text-xs"
                                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                        <span
                                            class="text-[10px] sm:text-xs font-extrabold uppercase tracking-tighter">كلينورا
                                            AI</span>
                                    </div>
                                    <p class="text-white text-xs sm:text-sm md:text-body-md mb-2 sm:mb-3 font-bold">
                                        استنادًا إلى السجلات من 2021 إلى 2024:</p>
                                    <ul
                                        class="space-y-1 sm:space-y-2 text-outline-variant text-xs sm:text-sm list-disc pr-4 sm:pr-5 font-medium leading-relaxed">
                                        <li>ارتفاع ضغط الدم المزمن (آخر قراءة 145/95)</li>
                                        <li>تاريخ من فرط شحميات الدم (LDL بمعدل 160 في أكتوبر 2023)</li>
                                        <li>تاريخ عائلي: والده أصيب بنوبة قلبية في عمر 52</li>
                                        <li>مدخّن حاليًا (حوالي 10 سجائر يوميًا)</li>
                                    </ul>
                                    <div
                                        class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-white/10 flex items-center gap-2 sm:gap-3">
                                        <div
                                            class="px-2 py-0.5 sm:py-1 bg-yellow-500/20 text-yellow-400 text-[9px] sm:text-[11px] rounded uppercase font-extrabold shrink-0">
                                            تنبيه حرج</div>
                                        <p class="text-[11px] sm:text-xs text-yellow-400 font-bold leading-tight">المريض لم
                                            يصرف روشتة أتورفاستاتين منذ 45
                                            يومًا.</p>
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shrink-0 shadow-lg">
                                    <span class="material-symbols-outlined text-white text-xs sm:text-sm"
                                        style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 7: How it Works -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-container-max mx-auto"
            id="how-it-works">
            <h2 class="font-display-lg text-2xl sm:text-3xl md:text-headline-lg font-extrabold text-center mb-12 sm:mb-16 text-on-surface"
                data-aos="fade-up">ابدأ خلال دقائق</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-12 text-center">
                <div class="space-y-4 sm:space-y-6" data-aos="fade-up" data-aos-delay="0">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 bg-primary-fixed rounded-full flex items-center justify-center mx-auto text-primary text-xl sm:text-2xl font-black shadow-lg">
                        1</div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface">أنشئ حسابك</h3>
                    <p
                        class="text-on-surface-variant font-body-md text-xs sm:text-sm md:text-base leading-relaxed max-w-xs mx-auto">
                        سجّل واضبط ملف عيادتك، وساعات العمل، والخدمات
                        التخصصية.</p>
                </div>
                <div class="space-y-4 sm:space-y-6" data-aos="fade-up" data-aos-delay="150">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 bg-secondary-fixed rounded-full flex items-center justify-center mx-auto text-secondary text-xl sm:text-2xl font-black shadow-lg">
                        2</div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface">شارك رابطك</h3>
                    <p
                        class="text-on-surface-variant font-body-md text-xs sm:text-sm md:text-base leading-relaxed max-w-xs mx-auto">
                        ضع رابط الحجز على جوجل أو إنستغرام أو واتساب. يحجز
                        المرضى مباشرة دون اتصال.</p>
                </div>
                <div class="space-y-4 sm:space-y-6" data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-14 h-14 sm:w-16 sm:h-16 bg-tertiary-fixed rounded-full flex items-center justify-center mx-auto text-tertiary text-xl sm:text-2xl font-black shadow-lg">
                        3</div>
                    <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface">أدر كل شيء</h3>
                    <p
                        class="text-on-surface-variant font-body-md text-xs sm:text-sm md:text-base leading-relaxed max-w-xs mx-auto">
                        ادخل إلى لوحة التحكم لعرض المواعيد وإدارة المرضى
                        والاستفادة من رؤى الذكاء الاصطناعي.</p>
                </div>
            </div>
        </section>

        <!-- Section 8: Benefits & Statistics -->
        <section class="bg-primary/5 py-12 md:py-section-gap border-y border-primary/10">
            <div class="max-w-container-max mx-auto px-4 sm:px-6 md:px-margin-desktop">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                    <div class="glass bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl soft-bloom text-center border border-white/80 shadow-md"
                        data-aos="zoom-in" data-aos-delay="0">
                        <p class="text-3xl sm:text-4xl md:text-display-lg font-display-lg text-primary mb-2 font-black">40%
                        </p>
                        <p class="text-xs sm:text-label-md font-bold text-on-surface uppercase tracking-wider">تقليل حالات
                            عدم
                            الحضور</p>
                        <p class="text-xs sm:text-body-md text-on-surface-variant mt-2 font-medium">من خلال تذكيرات واتساب
                            الآلية.</p>
                    </div>
                    <div class="glass bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl soft-bloom text-center border border-white/80 shadow-md"
                        data-aos="zoom-in" data-aos-delay="100">
                        <p class="text-3xl sm:text-4xl md:text-display-lg font-display-lg text-secondary mb-2 font-black">
                            +10 ساعات</p>
                        <p class="text-xs sm:text-label-md font-bold text-on-surface uppercase tracking-wider">توفير أسبوعي
                        </p>
                        <p class="text-xs sm:text-body-md text-on-surface-variant mt-2 font-medium">تقليل الأعمال الإدارية
                            والرد على
                            المكالمات.</p>
                    </div>
                    <div class="glass bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl soft-bloom text-center border border-white/80 shadow-md"
                        data-aos="zoom-in" data-aos-delay="200">
                        <p class="text-3xl sm:text-4xl md:text-display-lg font-display-lg text-tertiary mb-2 font-black">2x
                        </p>
                        <p class="text-xs sm:text-label-md font-bold text-on-surface uppercase tracking-wider">سرعة الحجز
                        </p>
                        <p class="text-xs sm:text-body-md text-on-surface-variant mt-2 font-medium">يحب المرضى تجربة الحجز
                            السلسة.</p>
                    </div>
                    <div class="glass bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl soft-bloom text-center border border-white/80 shadow-md"
                        data-aos="zoom-in" data-aos-delay="300">
                        <p class="text-3xl sm:text-4xl md:text-display-lg font-display-lg text-primary mb-2 font-black">
                            100%</p>
                        <p class="text-xs sm:text-label-md font-bold text-on-surface uppercase tracking-wider">بلا أوراق
                        </p>
                        <p class="text-xs sm:text-body-md text-on-surface-variant mt-2 font-medium">تحوّل رقمي كامل
                            للعيادات.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 9: Who Clinora Is For (Clinical Roles & Specialties) -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-container-max mx-auto">
            <div class="text-center mb-12 sm:mb-16 space-y-3 sm:space-y-4" data-aos="fade-up">
                <h2 class="font-display-lg text-2xl sm:text-3xl md:text-headline-lg font-extrabold text-on-surface">
                    مصممة لتناسب كل دور داخل منشأتك الطبية
                </h2>
                <p class="font-body-lg text-sm sm:text-base md:text-body-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
                    سواء كنت تدير عيادة فردية، مركزاً طبياً متخصصاً، أو سلسلة عيادات متعددة الفروع، توفر كلينورا صلاحيات وأدوات مخصصة لكل فرد في فريقك.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="glass p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom relative border border-white/60 shadow-lg flex flex-col justify-between"
                    data-aos="fade-up" data-aos-delay="0">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-primary-fixed flex items-center justify-center text-primary mb-5 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">stethoscope</span>
                        </div>
                        <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface mb-3">للأطباء والاستشاريين</h3>
                        <p class="font-body-lg text-xs sm:text-sm md:text-body-md mb-6 text-on-surface-variant leading-relaxed font-medium">
                            ملف طبي متكامل يشمل التاريخ المرضي والجراحي، التشخيصات، المرفقات الطبية الخاصة، والروشتات الإلكترونية مع مساعد ذكي لمراجعة الحالة السريرية.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-outline-variant/30 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined text-base">verified</span>
                        <span>سجل مرضي آمن ومعزول لكل عيادة</span>
                    </div>
                </div>
                <div class="glass p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom relative border border-white/60 shadow-lg flex flex-col justify-between"
                    data-aos="fade-up" data-aos-delay="150">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-tertiary-fixed flex items-center justify-center text-tertiary mb-5 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">support_agent</span>
                        </div>
                        <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface mb-3">لفريق الاستقبال والسكرتارية</h3>
                        <p class="font-body-lg text-xs sm:text-sm md:text-body-md mb-6 text-on-surface-variant leading-relaxed font-medium">
                            إدارة حجوزات اليوم، نداء الدور الصوتي، إيقاف واستئناف الطابور، تسجيل التحصيلات والمصروفات، والتواصل الفوري مع الطبيب بصلاحيات دقيقة قابلة للتخصيص.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-outline-variant/30 text-xs font-bold text-tertiary">
                        <span class="material-symbols-outlined text-base">tune</span>
                        <span>صلاحيات مرنة حسب مهام كل موظف</span>
                    </div>
                </div>
                <div class="glass p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom relative border border-white/60 shadow-lg flex flex-col justify-between"
                    data-aos="fade-up" data-aos-delay="300">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-secondary-fixed flex items-center justify-center text-secondary mb-5 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">domain</span>
                        </div>
                        <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface mb-3">لمديري المراكز وسلاسل العيادات</h3>
                        <p class="font-body-lg text-xs sm:text-sm md:text-body-md mb-6 text-on-surface-variant leading-relaxed font-medium">
                            لوحة تحكم مركزية لإدارة عدة فروع تحت اشتراك موحد، مع إمكانية الدخول لأي فرع ومتابعة التقارير المالية المجمعة ومؤشرات الأداء لكل عيادة.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-outline-variant/30 text-xs font-bold text-secondary">
                        <span class="material-symbols-outlined text-base">analytics</span>
                        <span>تقارير مالية وتشغيلية لكل فرع وللسلسلة</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 9.5: Pricing -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-container-max mx-auto"
            id="pricing">
            <div class="text-center mb-12 sm:mb-16 space-y-3 sm:space-y-4" data-aos="fade-up">
                <h2 class="font-display-lg text-2xl sm:text-3xl md:text-headline-lg font-extrabold text-on-surface">خطط
                    أسعار تناسب عيادتك</h2>
                <p
                    class="font-body-lg text-sm sm:text-base md:text-body-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
                    تجربة مجانية لمدة 30 يومًا بجميع المميزات، وخطط سنوية مرنة تناسب نمو عيادتك أو فروعك الطبية.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                <!-- Free Trial -->
                <div class="glass p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom flex flex-col border border-white/60 shadow-md"
                    data-aos="fade-up" data-aos-delay="0">
                    <h3 class="font-headline-md text-xl sm:text-headline-md font-bold mb-1 text-on-surface">تجريبية مجانية
                    </h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm md:text-body-md mb-6">لتجربة جميع
                        مميزات النظام بدون أي التزامات.
                    </p>
                    <div class="mb-6">
                        <span
                            class="text-3xl sm:text-4xl md:text-display-lg font-display-lg font-black text-primary">مجاناً</span>
                        <span class="text-on-surface-variant font-body-md text-sm sm:text-base font-bold"> / لمدة 30
                            يومًا</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">مفتوح فيها جميع
                                مميزات النظام بالكامل</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">حجز إلكتروني وإدارة
                                مواعيد وتقويم ذكي</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">المساعد الطبي الذكي
                                والروشتات الإلكترونية</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">تذكيرات واتساب وسجل
                                طبي إلكتروني متقدم</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">صلاحية كاملة لمدة 30
                                يومًا مجانًا</span></li>
                    </ul>
                    <a class="glass text-on-surface text-center px-6 py-3.5 rounded-xl font-bold font-label-md text-sm sm:text-base hover:bg-surface-variant/50 transition-all duration-300 border border-outline-variant/40"
                        href="{{ route('register') }}">ابدأ تجربتك المجانية</a>
                </div>
                <!-- Pro (highlighted) -->
                <div class="bg-primary-container text-on-primary p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom flex flex-col relative sm:-translate-y-2 shadow-2xl shadow-primary/30"
                    data-aos="fade-up" data-aos-delay="150">
                    <span
                        class="absolute -top-3 right-8 bg-white text-primary text-xs font-black px-3 py-1 rounded-full shadow-md">الأكثر
                        طلبًا</span>
                    <h3 class="font-headline-md text-xl sm:text-headline-md font-bold mb-1 text-white">احترافية</h3>
                    <p class="text-white/80 font-body-md text-xs sm:text-sm md:text-body-md mb-6">مثالية للعيادات والمراكز
                        الطبية الموحدة.</p>
                    <div class="mb-6">
                        <span
                            class="text-3xl sm:text-4xl md:text-display-lg font-display-lg font-black text-white">5,000</span>
                        <span class="text-white/80 font-body-md text-sm sm:text-base font-bold"> جنيه / سنويًا</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-cyan-300 text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-white">مفتوح فيها جميع مميزات
                                النظام بالكامل</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-cyan-300 text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-white">حجز إلكتروني وإدارة
                                مواعيد غير محدودة</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-cyan-300 text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-white">المساعد الطبي الذكي
                                والروشتات الإلكترونية</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-cyan-300 text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-white">تذكيرات واتساب وسجل طبي
                                إلكتروني متكامل</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-cyan-300 text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-white">دعم فني مستمر وتحديثات
                                تلقائية</span></li>
                    </ul>
                    <a class="bg-white text-primary text-center px-6 py-3.5 rounded-xl font-bold font-label-md text-sm sm:text-base hover:scale-105 transition-transform shadow-lg"
                        href="https://wa.me/201099615358" target="_blank" rel="noopener noreferrer">اشترك الآن</a>
                </div>
                <!-- Enterprise -->
                <div class="glass p-6 sm:p-8 md:p-card-padding rounded-2xl sm:rounded-3xl soft-bloom flex flex-col border border-white/60 shadow-md"
                    data-aos="fade-up" data-aos-delay="300">
                    <h3 class="font-headline-md text-xl sm:text-headline-md font-bold mb-1 text-on-surface">المؤسسات
                        والفروع</h3>
                    <p class="text-on-surface-variant font-body-md text-xs sm:text-sm md:text-body-md mb-6">للأفرع المتعددة
                        والمجمعات الطبية الكبرى.
                    </p>
                    <div class="mb-6">
                        <span
                            class="text-3xl sm:text-4xl md:text-display-lg font-display-lg font-black text-primary">10,000</span>
                        <span class="text-on-surface-variant font-body-md text-sm sm:text-base font-bold"> جنيه /
                            سنويًا</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">تشمل جميع مميزات
                                النظام بالكامل</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-bold text-primary">ميزة إدارة أكثر من فرع
                                وأكثر من عيادة</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-bold text-primary">لوحة تحكم خاصة لمدير
                                الأفرع والعيادات</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">تقارير وتحليلات
                                مجمعة لجميع الفروع</span></li>
                        <li class="flex items-center gap-2.5"><span
                                class="material-symbols-outlined text-tertiary text-lg">check_circle</span><span
                                class="text-xs sm:text-sm md:text-body-md font-medium text-on-surface">مدير حساب مخصص ودعم
                                فني ذو أولوية</span></li>
                    </ul>
                    <a class="glass text-on-surface text-center px-6 py-3.5 rounded-xl font-bold font-label-md text-sm sm:text-base hover:bg-surface-variant/50 transition-all duration-300 border border-outline-variant/40"
                        href="https://wa.me/201099615358" target="_blank" rel="noopener noreferrer">اشترك الآن</a>
                </div>
            </div>
        </section>

        <!-- Section 10: CTA Section -->
        <section class="px-4 sm:px-6 md:px-margin-desktop py-12 md:py-section-gap max-w-container-max mx-auto">
            <div class="bg-gradient-to-r from-primary-container to-blue-700 rounded-2xl sm:rounded-3xl md:rounded-[40px] p-6 sm:p-12 md:p-24 text-center text-on-primary relative overflow-hidden shadow-2xl shadow-primary/30"
                data-aos="zoom-in">
                <div
                    class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 -translate-x-1/2 blur-[80px]">
                </div>
                <div
                    class="absolute bottom-0 right-0 w-64 h-64 bg-cyan-500/20 rounded-full translate-y-1/2 translate-x-1/2 blur-[80px]">
                </div>
                <h2
                    class="font-display-lg text-2xl sm:text-3xl md:text-4xl lg:text-display-lg mb-4 sm:mb-6 relative z-10 font-black">
                    جاهز لتنمية عيادتك؟</h2>
                <p
                    class="font-body-lg text-xs sm:text-base md:text-body-lg mb-8 sm:mb-10 max-w-2xl mx-auto opacity-90 relative z-10 font-medium leading-relaxed">
                    انضم إلى مئات
                    العيادات حول العالم التي تعمل على تحديث رعاية مرضاها بمنصة كلينورا الذكية.</p>
                <div
                    class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3 sm:gap-4 relative z-10 w-full sm:w-auto max-w-md mx-auto sm:max-w-none">
                    <a class="bg-white text-primary px-6 sm:px-8 md:px-10 py-3.5 sm:py-4 md:py-5 rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg md:text-headline-md hover:scale-105 transition-transform inline-block w-full sm:w-auto text-center shadow-lg"
                        href="{{ route('register') }}">ابدأ تجربتك
                        المجانية</a>
                    <a class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-6 sm:px-8 md:px-10 py-3.5 sm:py-4 md:py-5 rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg md:text-headline-md hover:bg-white/20 transition-all inline-block w-full sm:w-auto text-center"
                        href="#contact">اطلب عرضًا تجريبيًا</a>
                </div>
                <p class="mt-6 sm:mt-8 text-xs sm:text-sm opacity-75 font-medium">لا حاجة لبطاقة ائتمان. يمكنك الإلغاء في
                    أي وقت.</p>
            </div>
        </section>
    </main>
@endsection

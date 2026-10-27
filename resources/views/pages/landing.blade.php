@extends('components.layout')

@section('content')
    <main class="space-y-20 sm:space-y-28 pb-20">
        <!-- Section 1: Hero (Matches Reference Images 2 & 3 in Light & Dark Mode) -->
        <section class="pt-8 sm:pt-14 md:pt-16 px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Primary Column (Right in RTL) -->
                <div class="lg:col-span-6 space-y-6 text-right" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-extrabold">
                        <span class="material-symbols-outlined text-base">bolt</span>
                        <span>نظام سحابي متكامل لإدارة العيادات والمراكز الطبية</span>
                    </div>

                    <div class="space-y-3">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#0D9488] dark:text-[#14B8A6] tracking-tight leading-tight">
                            كلينورا
                        </h1>
                        <p class="text-sm sm:text-base md:text-lg font-bold text-slate-500 dark:text-slate-400 tracking-wide leading-relaxed">
                            الحجز الإلكتروني • الطابور اللحظي • الملف الطبي • الروشتات • الحسابات • سلاسل العيادات
                        </p>
                    </div>

                    <!-- Instant Access Hero Card (Matches Reference Images 2 & 3) -->
                    <div class="clinora-card rounded-3xl p-6 sm:p-8 relative overflow-hidden max-w-xl">
                        <!-- Top Accent Gradient Bar -->
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-l from-indigo-600 via-blue-500 to-teal-400"></div>

                        <div class="flex items-center justify-between gap-4 pb-4 mb-5 border-b border-slate-100 dark:border-white/[0.06]">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-extrabold text-sm sm:text-base">
                                <span class="material-symbols-outlined text-xl">medical_services</span>
                                <span>وصول فوري لمنصتك الطبية</span>
                            </div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">تجربة مجانية • 30 يوماً</span>
                        </div>

                        <div class="space-y-3.5">
                            <a href="{{ route('register') }}"
                                class="w-full bg-[#F59E0B] hover:bg-[#D97706] text-white font-black py-3.5 px-6 rounded-xl shadow-md shadow-amber-500/20 flex items-center justify-center gap-2.5 text-sm sm:text-base transition-all hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-xl">person_add</span>
                                <span>إنشاء حساب عيادة جديد</span>
                            </a>
                            <a href="{{ route('login') }}"
                                class="w-full bg-[#34548A] hover:bg-[#2B4675] text-white font-bold py-3.5 px-6 rounded-xl shadow-sm flex items-center justify-center gap-2.5 text-sm sm:text-base transition-all hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-xl">login</span>
                                <span>تسجيل الدخول للعيادة</span>
                            </a>
                        </div>

                        <ul class="pt-5 mt-5 border-t border-slate-100 dark:border-white/[0.06] space-y-2.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span>صفحة حجز إلكتروني خاصة بعيادتك مع تتبع لحظي لرقم الدور</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span>ملف طبي إلكتروني شامل وروشتات جاهزة للطباعة الفورية</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span>إدارة دقيقة للإيرادات والمصروفات وصلاحيات فريق العمل</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Secondary Visual Showcase Column (Left in RTL — Matches Reference Images 2 & 3) -->
                <div class="lg:col-span-6 relative space-y-5" data-aos="fade-up" data-aos-delay="120">
                    <!-- Card 1: Top Operational Metric Card -->
                    <div class="clinora-card rounded-3xl p-6 sm:p-7 max-w-md mr-auto lg:mr-6">
                        <div class="flex items-center justify-between gap-4 mb-3">
                            <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">طابور الانتظار اليومي</span>
                            <span class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">trending_up</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">format_list_numbered</span>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight">نداء الدور اللحظي</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-0.5">تنظيم مرن لحركة المرضى بين الاستقبال وغرفة الكشف</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Highlighted Indigo/Royal-Blue Gradient Card -->
                    <div class="bg-gradient-to-br from-[#4F46E5] to-[#3730A3] text-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-indigo-500/30 max-w-md mr-auto">
                        <div class="flex items-center gap-3.5 mb-5">
                            <div class="w-12 h-12 rounded-full bg-white/15 border border-white/30 flex items-center justify-center text-lg font-black text-white shrink-0">
                                د
                            </div>
                            <div>
                                <p class="text-base sm:text-lg font-extrabold text-white">الملف الطبي والروشتة الرقمية</p>
                                <p class="text-xs text-indigo-100/80 font-medium">سجل تشخيصي ومرفقات طبية معزولة لكل عيادة</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-200">دليل الأدوية والطباعة الفورية</p>
                            <p class="text-2xl sm:text-3xl font-black text-white">روشتة جاهزة بنقرة واحدة</p>
                        </div>
                    </div>

                    <!-- Card 3: Bottom Live Status Card -->
                    <div class="clinora-card rounded-3xl p-5 sm:p-6 max-w-md mr-auto lg:mr-10">
                        <p class="text-xs font-extrabold text-slate-700 dark:text-slate-200 mb-3">حالة الحجز الإلكتروني والفواتير</p>
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">check</span>
                                </span>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900 dark:text-white">تأكيد الحجز وإصدار الفاتورة</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">مزامنة فورية بين الاستقبال والطبيب</p>
                                </div>
                            </div>
                            <span class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 shrink-0">+مفعل فورياً</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Platform Capabilities & Architecture Overview (Matches Reference Image 1's 3-Card Row) -->
        <section id="about" class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    ركائز تشغيلية مصممة للعيادات الحديثة
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    بنية سحابية تجمع بين سهولة الاستخدام اليومي، الصلاحيات الدقيقة لفريق العمل، والعزل الكامل لبيانات كل عيادة.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="clinora-card rounded-2xl p-6 sm:p-7 flex items-center gap-5" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">groups</span>
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">صلاحيات فريق العيادة</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">7 صلاحيات</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">تحكم مرن للأطباء والسكرتارية والمحاسبة</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="clinora-card rounded-2xl p-6 sm:p-7 flex items-center gap-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">verified_user</span>
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">عزل وحماية السجلات</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">100% مستقل</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">بيانات ومرفقات كل عيادة معزولة بالكامل</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="clinora-card rounded-2xl p-6 sm:p-7 flex items-center gap-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 rounded-2xl bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">calendar_clock</span>
                    </div>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">فترة التجربة المجانية</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">30 يوماً</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">تجربة شاملة لجميع الأدوات بدون بطاقة ائتمان</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Simple 3-Step Process (Matches Reference Image 1's "Simple 3-Step Process") -->
        <section id="how-it-works" class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    ابدأ في 3 خطوات بسيطة
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    أنشئ حساب عيادتك، شارك صفحة الحجز الخاصة بك، وأدر المواعيد والملفات الطبية بسهولة.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="clinora-card rounded-2xl p-7 text-right space-y-4 relative overflow-hidden" data-aos="fade-up" data-aos-delay="0">
                    <div class="flex items-center justify-between">
                        <span class="w-12 h-12 rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xl">1</span>
                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 text-3xl">app_registration</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">أنشئ حساب عيادتك</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        سجّل اسم العيادة وبيانات الطبيب المسؤول، واضبط مواعيد العمل وأنواع الكشوفات وأسعارها خلال دقائق.
                    </p>
                </div>

                <div class="clinora-card rounded-2xl p-7 text-right space-y-4 relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between">
                        <span class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-xl">2</span>
                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 text-3xl">share</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">فعّل الحجز والطابور اللحظي</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        استقبل حجوزات المرضى عبر صفحة عيادتك الإلكترونية أو من مكتب الاستقبال، مع منح كل مريض رابطاً لمتابعة دوره مباشرة.
                    </p>
                </div>

                <div class="clinora-card rounded-2xl p-7 text-right space-y-4 relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between">
                        <span class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center font-black text-xl">3</span>
                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 text-3xl">clinical_notes</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">أدر الكشوفات والتقارير المالية</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        سجّل التشخيصات، اطبع الروشتات الإلكترونية، ارفع التحاليل والأشعة، وتابع إيرادات ومصروفات عيادتك بدقة.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 4: Core Features Grid (Only Genuine Implemented Capabilities) -->
        <section id="features" class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    أدوات متكاملة لإدارة جميع أعمال عيادتك
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    كل وحدة في كلينورا مصممة لتسهيل العمل اليومي للطبيب وفريق الاستقبال والإدارة المالية.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                <!-- Feature 1 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">event_available</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">صفحة حجز إلكتروني خاصة</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        صفحة ويب مخصصة باسم عيادتك تتيح للمرضى حجز المواعيد واختيار نوع الكشف والحصول على رمز تأكيد فوري.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="50">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">queue_play_next</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">طابور انتظار وتتبع لحظي</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        إدارة مرئية لطابور الانتظار اليومي مع إمكانية نداء المريض التالي، إيقاف الطابور مؤقتاً، ومتابعة المريض لدوره مباشرة.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">folder_shared</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">ملف طبي إلكتروني شامل</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        توثيق التاريخ المرضي، الحساسية، العمليات السابقة، والتشخيصات السريرية لكل زيارة في ملف منظم لكل مريض.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="150">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/15 text-amber-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">description</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">روشتات إلكترونية جاهزة للطباعة</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        إصدار روشتات رقمية دقيقة مدعومة بالبحث الفوري في قاعدة بيانات الأدوية مع قالب طباعة احترافي باسم العيادة.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">cloud_upload</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">أرشيف المرفقات والتقارير الطبية</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        رفع وحفظ صور الأشعة ونتائج التحاليل وملفات PDF في مساحة تخزين خاصة لا يمكن الوصول إليها إلا للمصرح لهم بالعيادة.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="50">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">forum</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">مركز الاستقبال وتواصل الفريق</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        قناة رسائل داخلية فورية بين الطبيب ومكتب الاستقبال لتنسيق دخول المرضى والملاحظات العاجلة داخل العيادة.
                    </p>
                </div>

                <!-- Feature 7 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">payments</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">الفواتير والتقارير المالية</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        تسجيل الإيرادات والمصروفات، متابعة المدفوعات النقدية والإلكترونية، واستخراج تقارير مالية تفصيلية لكل فترة.
                    </p>
                </div>

                <!-- Feature 8 -->
                <div class="clinora-card rounded-2xl p-6 space-y-4 hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="150">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">domain</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">إدارة سلاسل العيادات والفروع</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        لوحة تحكم مركزية لأصحاب المراكز الطبية لإدارة عدة فروع، الانتقال السلس بينها، وعرض التقارير المجمعة.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 5: Comparison Table -->
        <section class="px-4 sm:px-6 md:px-12 max-w-5xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    ميزة كلينورا التنافسية
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium">
                    مقارنة عملية بين الإدارة الورقية التقليدية وإدارة عيادتك عبر نظام كلينورا السحابي.
                </p>
            </div>

            <div class="clinora-card rounded-3xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                <div class="overflow-x-auto">
                    <table class="min-w-[540px] w-full border-collapse text-right">
                        <thead>
                            <tr class="bg-slate-100/80 dark:bg-white/[0.04] border-b border-slate-200/80 dark:border-white/[0.07]">
                                <th class="p-4 sm:p-5 font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">مجال العمل</th>
                                <th class="p-4 sm:p-5 font-bold text-xs sm:text-sm text-slate-500 dark:text-slate-400">الإدارة التقليدية</th>
                                <th class="p-4 sm:p-5 font-extrabold text-xs sm:text-sm text-teal-600 dark:text-teal-400">منصة كلينورا</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-white/[0.06] text-xs sm:text-sm">
                            <tr>
                                <td class="p-4 sm:p-5 font-extrabold text-slate-900 dark:text-white">حجز المواعيد ومتابعة الدور</td>
                                <td class="p-4 sm:p-5 text-rose-600 dark:text-rose-400 font-semibold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">close</span>
                                    حجز هاتفي يدوي وتكدس في غرفة الانتظار
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">check_circle</span>
                                    حجز إلكتروني على مدار الساعة وتتبع لحظي لرقم الدور
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 font-extrabold text-slate-900 dark:text-white">السجل الطبي والمرفقات</td>
                                <td class="p-4 sm:p-5 text-rose-600 dark:text-rose-400 font-semibold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">close</span>
                                    ملفات ورقية معرضة للتلف أو الضياع
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">check_circle</span>
                                    أرشيف إلكتروني مؤمّن للتشخيصات والتحاليل والأشعة
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 font-extrabold text-slate-900 dark:text-white">إصدار الروشتات الطبية</td>
                                <td class="p-4 sm:p-5 text-rose-600 dark:text-rose-400 font-semibold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">close</span>
                                    كتابة يدوية متكررة بدون سجل دوائي
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">check_circle</span>
                                    روشتة رقمية مرتبطة بقاعدة الأدوية وجاهزة للطباعة
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 font-extrabold text-slate-900 dark:text-white">الحسابات والتقارير المالية</td>
                                <td class="p-4 sm:p-5 text-rose-600 dark:text-rose-400 font-semibold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">close</span>
                                    جرد يدوي يصعب معه تتبع صافي الربح
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">check_circle</span>
                                    تقارير فورية للإيرادات والمصروفات وطرق الدفع
                                </td>
                            </tr>
                            <tr>
                                <td class="p-4 sm:p-5 font-extrabold text-slate-900 dark:text-white">إدارة الفروع وفريق العمل</td>
                                <td class="p-4 sm:p-5 text-rose-600 dark:text-rose-400 font-semibold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">close</span>
                                    صعوبة متابعة الفروع وتداخل صلاحيات الموظفين
                                </td>
                                <td class="p-4 sm:p-5 text-emerald-600 dark:text-emerald-400 font-extrabold">
                                    <span class="material-symbols-outlined align-middle ml-1 text-base">check_circle</span>
                                    صلاحيات مخصصة لكل موظف ولوحة مجمعة لسلاسل العيادات
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section 6: Who Clinora Is For (Clinical Roles & Specialties) -->
        <section class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    مصممة لتناسب كل دور داخل منشأتك الطبية
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    سواء كنت تدير عيادة فردية، مركزاً طبياً متخصصاً، أو سلسلة عيادات متعددة الفروع، توفر كلينورا صلاحيات وأدوات مخصصة لكل فرد في فريقك.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Role 1 -->
                <div class="clinora-card rounded-2xl p-7 flex flex-col justify-between space-y-6" data-aos="fade-up" data-aos-delay="0">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">stethoscope</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">للأطباء والاستشاريين</h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            ملف طبي متكامل يشمل التاريخ المرضي والجراحي، التشخيصات، المرفقات الطبية الخاصة، والروشتات الإلكترونية الجاهزة للطباعة.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200/70 dark:border-white/[0.06] text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-base">verified</span>
                        <span>سجل مرضي آمن ومعزول لكل عيادة</span>
                    </div>
                </div>

                <!-- Role 2 -->
                <div class="clinora-card rounded-2xl p-7 flex flex-col justify-between space-y-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">support_agent</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">لفريق الاستقبال والسكرتارية</h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            إدارة حجوزات اليوم، نداء الدور اللحظي، إيقاف واستئناف الطابور، تسجيل التحصيلات والمصروفات، والتواصل الفوري مع الطبيب بصلاحيات دقيقة.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200/70 dark:border-white/[0.06] text-xs font-extrabold text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-base">tune</span>
                        <span>صلاحيات مرنة حسب مهام كل موظف</span>
                    </div>
                </div>

                <!-- Role 3 -->
                <div class="clinora-card rounded-2xl p-7 flex flex-col justify-between space-y-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">domain</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">لمديري المراكز وسلاسل العيادات</h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            لوحة تحكم مركزية لإدارة عدة فروع تحت اشتراك موحد، مع إمكانية الدخول لأي فرع ومتابعة التقارير المالية المجمعة ومؤشرات الأداء لكل عيادة.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200/70 dark:border-white/[0.06] text-xs font-extrabold text-amber-600 dark:text-amber-400">
                        <span class="material-symbols-outlined text-base">analytics</span>
                        <span>تقارير مالية وتشغيلية لكل فرع وللسلسلة</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 7: Pricing Plans -->
        <section id="pricing" class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    خطط أسعار واضحة ومرنة
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    ابدأ بتجربة مجانية لمدة 30 يوماً بجميع المميزات، واختر الباقة المناسبة لعيادتك الفردية أو سلسلة فروعك الطبية.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                <!-- Plan 1: Free Trial -->
                <div class="clinora-card rounded-3xl p-7 sm:p-8 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="0">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-extrabold mb-3">
                            بدون التزامات
                        </span>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-1">تجريبية مجانية</h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 font-medium">
                            لتجربة جميع أدوات إدارة العيادة عملياً قبل الاشتراك.
                        </p>
                        <div class="mb-6 pb-6 border-b border-slate-200/70 dark:border-white/[0.06]">
                            <span class="text-3xl sm:text-4xl font-black text-teal-600 dark:text-teal-400">مجاناً</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400"> / لمدة 30 يوماً</span>
                        </div>
                        <ul class="space-y-3 mb-8 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>تفعيل فوري لحساب العيادة وصفحة الحجز العام</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>إدارة المواعيد وطابور الانتظار اللحظي</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>ملف طبي إلكتروني شامل وروشتات جاهزة للطباعة</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>إدارة الفواتير والتقارير المالية وصلاحيات الفريق</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}"
                        class="w-full text-center bg-[#34548A] hover:bg-[#2B4675] text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-sm">
                        ابدأ تجربتك المجانية
                    </a>
                </div>

                <!-- Plan 2: Pro Single Clinic (Highlighted) -->
                <div class="bg-gradient-to-b from-[#1E293B] to-[#0F172A] dark:from-[#182038] dark:to-[#101526] text-white rounded-3xl p-7 sm:p-8 flex flex-col justify-between relative shadow-2xl border border-indigo-500/40 lg:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <span class="absolute -top-3.5 right-7 bg-[#F59E0B] text-white text-xs font-black px-3.5 py-1 rounded-full shadow-md">
                        الأكثر اختياراً للعيادات
                    </span>
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-extrabold mb-3">
                            للعيادات المستقلة
                        </span>
                        <h3 class="text-xl font-black text-white mb-1">الباقة الاحترافية</h3>
                        <p class="text-xs sm:text-sm text-slate-300 mb-6 font-medium">
                            مثالية للعيادات الفردية والمراكز الطبية الموحدة.
                        </p>
                        <div class="mb-6 pb-6 border-b border-white/10">
                            <span class="text-3xl sm:text-4xl font-black text-white">5,000</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-300"> جنيه / سنوياً</span>
                        </div>
                        <ul class="space-y-3 mb-8 text-xs sm:text-sm font-semibold text-slate-200">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-teal-400 text-lg">check_circle</span>
                                <span>جميع مميزات النظام للعيادة بدون حدود للمواعيد</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-teal-400 text-lg">check_circle</span>
                                <span>حسابات متعددة للأطباء والسكرتارية بصلاحيات مخصصة</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-teal-400 text-lg">check_circle</span>
                                <span>أرشيف خاص للمرفقات الطبية والتحاليل والأشعة</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-teal-400 text-lg">check_circle</span>
                                <span>تقارير مالية وتشغيلية مفصلة ودعم فني مستمر</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}"
                        class="w-full text-center bg-[#F59E0B] hover:bg-[#D97706] text-white font-black py-3.5 px-6 rounded-xl transition-all shadow-lg shadow-amber-500/25">
                        اشترك وابدأ الآن
                    </a>
                </div>

                <!-- Plan 3: Enterprise / Multi-Branch -->
                <div class="clinora-card rounded-3xl p-7 sm:p-8 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="200">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-extrabold mb-3">
                            للسلاسل والمجمعات
                        </span>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-1">المؤسسات والفروع</h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 font-medium">
                            للمراكز الطبية التي تدير أكثر من فرع تحت إدارة واحدة.
                        </p>
                        <div class="mb-6 pb-6 border-b border-slate-200/70 dark:border-white/[0.06]">
                            <span class="text-3xl sm:text-4xl font-black text-indigo-600 dark:text-indigo-400">10,000</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400"> جنيه / سنوياً</span>
                        </div>
                        <ul class="space-y-3 mb-8 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>لوحة تحكم مركزية لإدارة سلاسل العيادات والفروع</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>انتقال فوري بين الفروع مع عزل مستقل لبيانات كل فرع</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>تقارير مالية وتشغيلية مجمعة على مستوى السلسلة</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                <span>إدارة صلاحيات مديري الفروع والموظفين بمرونة</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ url('/#contact') }}"
                        class="w-full text-center bg-[#34548A] hover:bg-[#2B4675] text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-sm">
                        تواصل لتفعيل باقة الفروع
                    </a>
                </div>
            </div>
        </section>

        <!-- Section 8: FAQ -->
        <section id="faq" class="px-4 sm:px-6 md:px-12 max-w-4xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                    الأسئلة الشائعة
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium">
                    إجابات واضحة حول كيفية عمل منصة كلينورا وإدارة بيانات عيادتك.
                </p>
            </div>

            <div class="space-y-4">
                <div class="clinora-card rounded-2xl p-6" data-aos="fade-up">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white mb-2">
                        كيف تعمل الفترة التجريبية المجانية؟
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        بمجرد إنشاء حساب عيادتك، يتم تفعيل اشتراك تجريبي مجاني تلقائياً يتيح لك استخدام لوحة التحكم، إدارة المرضى والمواعيد، إصدار الروشتات، وتجربة صفحة الحجز العام بدون الحاجة لبطاقة ائتمان.
                    </p>
                </div>

                <div class="clinora-card rounded-2xl p-6" data-aos="fade-up" data-aos-delay="50">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white mb-2">
                        هل بيانات المرضى والمرفقات الطبية معزولة لكل عيادة؟
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        نعم، يطبق النظام عزلاً كاملاً للبيانات (Multi-Tenant Isolation) بحيث ترتبط سجلات المرضى، المواعيد، الفواتير، والملفات الطبية المرفوعة بعيادتك فقط ولا يمكن لأي عيادة أخرى الاطلاع عليها.
                    </p>
                </div>

                <div class="clinora-card rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white mb-2">
                        هل يمكنني تحديد صلاحيات مختلفة للسكرتارية والأطباء؟
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                        نعم، يمكن لمالك العيادة إضافة حسابات للأطباء وموظفي الاستقبال وتحديد الصلاحيات المسموحة لكل مستخدم بدقة (مثل إدارة المواعيد فقط، أو إدارة المرضى، أو الفواتير والتقارير).
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 9: Final CTA Banner -->
        <section class="px-4 sm:px-6 md:px-12 max-w-[1360px] mx-auto">
            <div class="clinora-card rounded-3xl p-8 sm:p-12 md:p-16 text-center relative overflow-hidden" data-aos="zoom-in">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-l from-indigo-600 via-teal-500 to-amber-500"></div>
                <div class="max-w-2xl mx-auto space-y-5 relative z-10">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                        ابدأ تنظيم عيادتك رقمياً اليوم
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                        أنشئ حساب عيادتك في دقيقة واحدة واحصل على صفحة حجز إلكتروني خاصة، نظام طابور لحظي، وسجل طبي متكامل لمرضاك.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3.5 pt-2 max-w-md mx-auto sm:max-w-none">
                        <a href="{{ route('register') }}"
                            class="bg-[#F59E0B] hover:bg-[#D97706] text-white font-black px-8 py-4 rounded-xl shadow-md shadow-amber-500/20 text-sm sm:text-base transition-all hover:-translate-y-0.5">
                            ابدأ تجربتك المجانية
                        </a>
                        <a href="{{ route('login') }}"
                            class="bg-[#34548A] hover:bg-[#2B4675] text-white font-bold px-8 py-4 rounded-xl shadow-sm text-sm sm:text-base transition-all hover:-translate-y-0.5">
                            تسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

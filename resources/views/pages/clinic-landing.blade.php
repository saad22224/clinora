<!DOCTYPE html>
<html dir="rtl" lang="ar" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $clinic->name }}{{ $clinic->doctor_title ? ' | '.$clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->name, 160) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor   = $clinic->theme_color ?? '#005c55';
        $doctorImage    = !empty($clinic->doctor_image)
            ? (str_starts_with($clinic->doctor_image, 'http') ? $clinic->doctor_image : Storage::url($clinic->doctor_image))
            : 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1200&q=85';
        $visitTypes     = $clinic->visitTypes ?? collect();
        $patientCount   = $clinic->patients()->count() + 1250;
        $apptCount      = $clinic->appointments()->count() + 5400;
        $days           = ['saturday'=>'السبت','sunday'=>'الأحد','monday'=>'الإثنين','tuesday'=>'الثلاثاء','wednesday'=>'الأربعاء','thursday'=>'الخميس','friday'=>'الجمعة'];
        $faqs = [
            'كيف يتم تأكيد الموعد؟' => 'بعد إرسال الطلب يراجعه فريق العيادة ويؤكد الموعد أو يقترح وقتاً بديلاً من صفحة المواعيد.',
            'هل يمكنني اختيار نوع الخدمة؟' => 'نعم، اختر الخدمة من نموذج الحجز وستُربط بالموعد داخل النظام تلقائياً.',
            'ماذا أفعل في الحالات العاجلة؟' => 'للحالات العاجلة اتصل بالعيادة مباشرةً عبر رقم الهاتف الموضح في الصفحة.',
        ];
    @endphp

    <!-- Tailwind CDN & Configuration to ensure identical aesthetics -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <style>
        :root {
            --font-cairo: 'Cairo', sans-serif;
            --p-color: {{ $primaryColor }};
        }

        body {
            font-family: var(--font-cairo);
            scroll-behavior: smooth;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .floating-label-input:focus-within label,
        .floating-label-input input:not(:placeholder-shown)+label,
        .floating-label-input textarea:not(:placeholder-shown)+label {
            transform: translateY(-1.5rem) scale(0.85);
            color: {{ $primaryColor }};
        }

        .gradient-border {
            position: relative;
        }

        .gradient-border::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.1));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "{{ $primaryColor }}",
                        "primary-container": "#0f766e",
                        "on-primary-container": "#a3faef",
                        "secondary": "#006b5f",
                        "tertiary": "#005a6a",
                        "background": "#f7f9fb",
                        "surface": "#ffffff",
                        "outline": "#6e7977",
                        "surface-variant": "#e0e3e5",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#3e4947",
                    },
                    borderRadius: {
                        "3xl": "32px",
                    },
                    spacing: {
                        "container-padding-desktop": "40px",
                        "stack-lg": "80px",
                    },
                    fontFamily: {
                        "display": ["Cairo", "sans-serif"],
                        "body": ["Cairo", "sans-serif"],
                    }
                },
            },
        }
    </script>
</head>

<body class="bg-background text-on-surface overflow-x-hidden">
    <!-- TopNavBar -->
    <header id="navbar"
        class="fixed top-0 w-full z-50 bg-surface/85 backdrop-blur-xl border-b border-white/20 shadow-[0_20px_40px_rgba(15,118,110,0.05)] h-24 transition-all duration-300">
        <div class="flex justify-between items-center px-4 md:px-container-padding-desktop max-w-[1440px] mx-auto h-full">
            <div class="flex items-center gap-8">
                <a href="#hero" class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center text-white font-black text-xl shadow-lg">
                        {{ mb_substr($clinic->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xl md:text-[24px] tracking-tight text-primary leading-tight">{{ $clinic->name }}</h1>
                        @if($clinic->doctor_title)
                            <span class="text-xs text-on-surface-variant block font-semibold">{{ $clinic->doctor_title }}</span>
                        @endif
                    </div>
                </a>
                <nav class="hidden lg:flex items-center gap-6">
                    <a class="text-primary border-b-2 border-primary pb-1 font-semibold text-sm" href="#hero">الرئيسية</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold" href="#services">التخصصات والخدمات</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold" href="#about">عن العيادة</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold" href="#hours">المواعيد</a>
                    <!-- <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold" href="#reviews">آراء المرضى</a> -->
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold" href="#contact">اتصل بنا</a>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <a href="#contact"
                    class="hidden sm:inline-flex bg-primary text-white px-7 py-3 rounded-xl font-bold text-sm hover:opacity-90 hover:scale-[1.02] transition-all duration-300 active:scale-95 shadow-md">
                    احجز موعداً
                </a>
                <!-- HAMBURGER TOGGLE BUTTON FOR MOBILE -->
                <button id="hamburger" aria-label="القائمة" aria-expanded="false"
                    class="lg:hidden w-11 h-11 rounded-xl border border-outline/20 bg-surface flex items-center justify-center text-on-surface hover:border-primary hover:text-primary transition-colors shadow-sm">
                    <span class="material-symbols-outlined icon-menu text-2xl">menu</span>
                    <span class="material-symbols-outlined icon-close text-2xl hidden">close</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MOBILE DRAWER -->
    <div id="nav-drawer"
        class="fixed inset-x-0 top-24 bg-surface/95 backdrop-blur-2xl border-b border-outline/20 shadow-2xl z-40 hidden flex-col px-6 py-6 space-y-4 lg:hidden transition-all duration-300">
        <a href="#hero" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">الرئيسية</a>
        <a href="#services" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">التخصصات والخدمات</a>
        <a href="#about" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">عن العيادة والأطباء</a>
        <a href="#hours" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">ساعات العمل</a>
        <a href="#reviews" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">آراء المرضى</a>
        <a href="#faq" class="text-base font-bold text-on-surface hover:text-primary border-b border-outline/10 pb-3 block">الأسئلة الشائعة</a>
        <a href="#contact" class="bg-primary text-white text-center py-3.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition-all block mt-2">
            احجز موعدك الآن ←
        </a>
    </div>

    <main class="pt-24">
        <!-- Hero Section -->
        <section id="hero" class="relative min-h-[90vh] flex items-center overflow-hidden py-12">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-l from-background via-background/40 to-transparent z-10"></div>
                <img class="w-full h-full object-cover opacity-20"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAEntlbeKt50B5Nhnk7lJ5bQSldkHpT-5TLWxD0Y6jHURQDCkEg1Wos1xEdMXgdBDV7uqtPpCCHAs7hETp7b6LcRXlQuuZxNnwWo3qCNuxyzotiepJRJ9J5fmQ5LnTtmJ4elhV5lIQ6xfXRVDifSnb9LDtNiuGsHp7zhBldu_Vk8BIyI0NBoyX8yOBK-5Qug5TEfD7tHSLVn_uDggTga421zHHp_OSzpWfwrpSY4H-hRhATpJ3_Dsb87zv0aE0OZ7ypM5jbXHWS6XY" alt="Clinic Background" />
            </div>
            <div class="relative z-20 max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop w-full grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="hero-content space-y-6 md:space-y-8">
                    <div data-aos="fade-down" data-aos-duration="800"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary border border-primary/20">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        <span class="text-xs md:text-sm font-bold tracking-wide">المركز الأول في الرعاية الطبية الفاخرة</span>
                    </div>
                    <h2 data-aos="fade-up" data-aos-duration="800" data-aos-delay="100"
                        class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-on-surface leading-[1.15] tracking-tight">
                        @if($clinic->hero_title)
                            {!! nl2br(e($clinic->hero_title)) !!}
                        @else
                            مستقبل الرعاية الطبية <br />
                            <span class="text-primary text-3xl md:text-5xl lg:text-6xl block mt-2">بين يديك في {{ $clinic->name }}</span>
                        @endif
                        @if($clinic->doctor_title)
                            <span class="text-xl md:text-2xl text-on-surface-variant font-bold block mt-3">{{ $clinic->doctor_title }}</span>
                        @endif
                    </h2>
                    <p data-aos="fade-up" data-aos-duration="800" data-aos-delay="200"
                        class="text-base md:text-lg text-on-surface-variant max-w-xl leading-relaxed font-medium">
                        {{ $clinic->hero_subtitle ?? $clinic->doctor_bio ?? 'نقدم في عيادتنا تجربة طبية فريدة تجمع بين التكنولوجيا المتطورة واللمسة الإنسانية الدافئة، لنضمن لك رعاية صحية فائقة الدقة والراحة.' }}
                    </p>
                    <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300" class="flex flex-wrap gap-4 pt-2">
                        <a href="#contact"
                            class="bg-primary text-white px-8 md:px-10 py-4 rounded-xl font-bold text-base md:text-lg hover:scale-105 transition-transform shadow-lg inline-flex items-center gap-2">
                            <span>احجز موعدك الآن</span>
                            <span class="material-symbols-outlined text-lg">arrow_back</span>
                        </a>
                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}"
                                class="flex items-center gap-3 px-8 md:px-10 py-4 rounded-xl font-bold text-base md:text-lg border border-outline/20 hover:bg-surface transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">call</span>
                                <span>تواصل هاتفياً</span>
                            </a>
                        @endif
                    </div>
                    <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="400"
                        class="grid grid-cols-3 gap-6 pt-6 border-t border-outline/10">
                        <div>
                            <div class="text-3xl md:text-4xl font-extrabold text-primary counter" data-target="{{ $patientCount }}">0</div>
                            <div class="text-xs md:text-sm text-on-surface-variant font-medium mt-1">مريض تمت معالجته</div>
                        </div>
                        <div>
                            <div class="text-3xl md:text-4xl font-extrabold text-primary counter" data-target="{{ $apptCount }}">0</div>
                            <div class="text-xs md:text-sm text-on-surface-variant font-medium mt-1">موعد محجوز</div>
                        </div>
                        <div>
                            <div class="text-3xl md:text-4xl font-extrabold text-primary counter" data-target="15">0</div>
                            <div class="text-xs md:text-sm text-on-surface-variant font-medium mt-1">سنة من التميز</div>
                        </div>
                    </div>
                </div>
                <div data-aos="zoom-in-left" data-aos-duration="1000" class="hero-image relative">
                    <div class="absolute -top-12 -right-12 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-tertiary/10 rounded-full blur-3xl"></div>
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-[12px] border-white/60 backdrop-blur-md">
                        <img alt="{{ $clinic->name }}" class="w-full aspect-[4/5] object-cover" src="{{ $doctorImage }}" />
                        <!-- Floating Badges -->
                        <div data-aos="flip-up" data-aos-duration="800" data-aos-delay="600"
                            class="absolute bottom-6 right-6 glass-card p-5 rounded-2xl shadow-xl border border-white/40 max-w-[240px]">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary">award_star</span>
                                </div>
                                <span class="font-bold text-sm text-on-surface">استشاري متخصص</span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-tight font-medium">{{ $clinic->doctor_title ?? $clinic->name }}، رعاية طبية متكاملة وموثوقة.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-stack-lg bg-surface-variant/30" id="services">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div data-aos="fade-up" data-aos-duration="800" class="text-center mb-16 space-y-3">
                    <h3 class="text-primary font-bold tracking-widest text-sm">خدماتنا المتميزة</h3>
                    <h2 class="text-3xl md:text-4xl font-extrabold">رعاية طبية تتجاوز التوقعات</h2>
                    <p class="text-on-surface-variant text-base max-w-xl mx-auto">اختر الخدمة المطلوبة واحجز موعدك بسهولة وسرعة، وسيصلك تأكيد مباشر من العيادة.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($visitTypes as $visitType)
                        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="{{ $loop->index * 100 }}"
                            class="group glass-card p-8 rounded-3xl hover:translate-y-[-10px] transition-all duration-500 relative overflow-hidden border border-white/40 shadow-sm hover:shadow-xl flex flex-col justify-between">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10">
                                <div class="w-14 h-14 bg-gradient-to-br from-primary to-primary-container rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                                    <span class="material-symbols-outlined text-3xl">medical_services</span>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-on-surface">{{ $visitType->name }}</h4>
                                <p class="text-on-surface-variant text-sm leading-relaxed mb-6">{{ $visitType->description ?? 'خدمة طبية فائقة الجودة ضمن بيئة استشفائية مريحة ومواعيد دقيقة.' }}</p>
                            </div>
                            <div class="relative z-10 pt-4 border-t border-outline/10 flex items-center justify-between">
                                <span class="font-extrabold text-primary text-lg">{{ number_format($visitType->price, 0) }} ج.م</span>
                                <a href="#contact" onclick="selectVisitType({{ $visitType->id }})"
                                    class="flex items-center gap-1.5 text-primary font-bold text-sm group-hover:gap-2.5 transition-all">
                                    <span>احجز الآن</span>
                                    <span class="material-symbols-outlined text-xs">arrow_back</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        @foreach([
                            ['title' => 'الكشف الشامل والتشخيص', 'icon' => 'cardiology', 'desc' => 'أحدث تقنيات الفحص والتشخيص الدقيق لتحديد خطة العلاج المناسبة لحالتك.'],
                            ['title' => 'المتابعة الطبية المستمرة', 'icon' => 'ecg_heart', 'desc' => 'برامج متابعة دورية ومستمرة لضمان استقرار الحالة الصحية والتعافي السريع.'],
                            ['title' => 'الاستشارات المتخصصة', 'icon' => 'stethoscope', 'desc' => 'جلسات استشارية مع نخبة الأطباء للإجابة على كافة استفساراتك الصحية.'],
                            ['title' => 'الفحوصات الدقيقة', 'icon' => 'biotech', 'desc' => 'إجراء كافة الفحوصات الطبية والتحاليل الدقيقة بأحدث الأجهزة والتقنيات.']
                        ] as $fallback)
                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="{{ $loop->index * 100 }}"
                                class="group glass-card p-8 rounded-3xl hover:translate-y-[-10px] transition-all duration-500 relative overflow-hidden border border-white/40 shadow-sm hover:shadow-xl flex flex-col justify-between">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full group-hover:scale-150 transition-transform duration-700"></div>
                                <div class="relative z-10">
                                    <div class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                                        <span class="material-symbols-outlined text-3xl">{{ $fallback['icon'] }}</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-3 text-on-surface">{{ $fallback['title'] }}</h4>
                                    <p class="text-on-surface-variant text-sm leading-relaxed mb-6">{{ $fallback['desc'] }}</p>
                                </div>
                                <div class="relative z-10 pt-4 border-t border-outline/10 flex items-center justify-between">
                                    <a href="#contact" class="flex items-center gap-1.5 text-primary font-bold text-sm group-hover:gap-2.5 transition-all">
                                        <span>احجز موعداً</span>
                                        <span class="material-symbols-outlined text-xs">arrow_back</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Why Choose Us / Bento Grid Section -->
        <section class="py-stack-lg overflow-hidden" id="about">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div class="grid lg:grid-cols-12 gap-12 items-center">
                    <!-- Left: Text content -->
                    <div data-aos="fade-right" data-aos-duration="800" class="lg:col-span-5 flex flex-col justify-center space-y-6 md:space-y-8">
                        <h3 class="text-primary font-bold text-sm tracking-widest">لماذا اختيار عيادتنا؟</h3>
                        <h2 class="text-3xl md:text-5xl font-extrabold leading-tight">معايير عالمية <br />في قلب مدينتكم</h2>
                        <p class="text-on-surface-variant leading-relaxed text-base">
                            {{ $clinic->about_us ?? $clinic->doctor_bio ?? 'نحن لا نقدم العلاج فحسب، بل نبني علاقة ثقة طويلة الأمد مع مرضانا، موفرين بيئة استشفائية تجمع بين الهدوء النفسي والكفاءة الطبية الفائقة.' }}
                        </p>
                        <div class="space-y-4">
                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="100"
                                class="flex items-start gap-4 p-4 rounded-2xl glass-card hover:bg-primary/5 transition-colors group border border-white/40 shadow-sm">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-md flex items-center justify-center text-primary shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined">health_metrics</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-base md:text-lg">دقة فائقة في التشخيص</h5>
                                    <p class="text-xs md:text-sm text-on-surface-variant mt-1">اعتماد أحدث المعايير الطبية والتقنيات الحديثة للوصول إلى أدق النتائج.</p>
                                </div>
                            </div>
                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="200"
                                class="flex items-start gap-4 p-4 rounded-2xl glass-card hover:bg-primary/5 transition-colors group border border-white/40 shadow-sm">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-md flex items-center justify-center text-primary shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined">hotel_class</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-base md:text-lg">بيئة مريحة وخصوصية تامة</h5>
                                    <p class="text-xs md:text-sm text-on-surface-variant mt-1">تجهيزات عصرية وأجواء هادئة تضمن راحة المريض وخصوصيته من اللحظة الأولى.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Bento Layout -->
                    <div class="lg:col-span-7 grid grid-cols-2 grid-rows-2 gap-4 h-[520px] md:h-[600px]">
                        <div data-aos="zoom-in" data-aos-duration="800"
                            class="relative overflow-hidden rounded-3xl col-span-1 row-span-2 shadow-xl group border border-white/30">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9uNNKgy1RBPAB6d_X4fVrcD9WfwIcDKgCtF-XnVK2MphHsN4guUJY_IvFqNuN5qxjFXxtU2yGl1mKus02z0hcNRhLFHC_DGHV3Aylj13Zz0OsoahC-9qKMPayRKyxp4UjAWyjiPT7WIPGdP8fyGh25gQda0HMPPb2h6SezPmn6pAqTRpo8sZDTEbaXmzpzGePgcOnoHPtBlkinv3q-nqm-9e9lFnqvi5XMgAEEsdVz-lUsKDtdSD9nKGYuC66PLVz1SO3dMW6GMY" alt="Clinic Lounge" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-8 left-8 right-8 text-white">
                                <p class="text-xs md:text-sm font-semibold opacity-80 mb-1">الراحة أولاً</p>
                                <h4 class="text-lg md:text-xl font-bold">بيئة استشفائية هادئة</h4>
                            </div>
                        </div>
                        <div data-aos="zoom-in" data-aos-duration="800" data-aos-delay="150"
                            class="relative overflow-hidden rounded-3xl col-span-1 row-span-1 shadow-xl group border border-white/30">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBWrPdPqUJI5HXFC45oE95CzWjeRKJfS2Wlusf2rZ9LB8sGstPmzuUpqe-cnGjnDEAdI7uDw21Wh93jSRoJG5Lvj2iCOczX6IYC3LZHiKx45dB_vDDqcy6rubfYwyixwB_uxgciPAHayizWvhnU9xihRihFLgiL97VLU6uWL-Mu-3_lC7TqU8VCAHFh59YRVq8vwpQ_v2u4xUDad8Y1PykQg9IosbTPcTNddhhuo_7sINXoX-ie9Tub3s--j-WFoWbieukDG-W80Vc" alt="Advanced Lab" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 right-6 text-white">
                                <h4 class="text-base md:text-lg font-bold">تجهيزات طبية متطورة</h4>
                            </div>
                        </div>
                        <div data-aos="flip-up" data-aos-duration="800" data-aos-delay="300"
                            class="relative overflow-hidden rounded-3xl col-span-1 row-span-1 shadow-xl group bg-primary flex flex-col justify-center p-6 md:p-8 text-white">
                            <div class="text-3xl md:text-4xl font-extrabold mb-2">99%</div>
                            <p class="text-xs md:text-sm opacity-90 leading-relaxed">معدل رضا المرضى عن خدمات الرعاية في العيادة</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Working Hours Section -->
        <section class="py-16 bg-surface-variant/20 border-y border-outline/10" id="hours">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    <div data-aos="fade-right" data-aos-duration="800" class="lg:col-span-4 space-y-4">
                        <span class="text-primary font-bold text-sm tracking-widest">المواعيد المتاحة</span>
                        <h2 class="text-3xl font-extrabold">ساعات العمل الرسمية</h2>
                        <p class="text-on-surface-variant text-sm leading-relaxed">يمكن لفريق الاستقبال تأكيد موعدك أو اقتراح وقت بديل متوافق مع جدول عمل العيادة.</p>
                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm shadow hover:opacity-90 transition-all mt-2">
                                <span class="material-symbols-outlined text-sm">call</span>
                                <span>اتصل للحجز الفوري</span>
                            </a>
                        @endif
                    </div>
                    <div class="lg:col-span-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse(($clinic->working_hours ?? []) as $key => $data)
                            @if(!empty($data['enabled']))
                                <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="{{ $loop->index * 100 }}"
                                    class="glass-card p-5 rounded-2xl border border-white/50 shadow-sm flex flex-col justify-between">
                                    <span class="font-extrabold text-base text-primary mb-2">{{ $days[$key] ?? $key }}</span>
                                    <span class="text-sm text-on-surface font-semibold">
                                        @foreach(($data['periods'] ?? []) as $p)
                                            {{ $p['open'] }} – {{ $p['close'] }}@if(!$loop->last)، @endif
                                        @endforeach
                                    </span>
                                </div>
                            @endif
                        @empty
                            <div data-aos="fade-left" data-aos-duration="800" class="glass-card p-6 rounded-2xl sm:col-span-2 text-center">
                                <span class="text-sm font-bold text-on-surface-variant">يرجى التواصل مع العيادة مباشرة لمعرفة أوقات الدوام الرسمية.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- Patient Reviews Section -->
        <section class="py-stack-lg" id="reviews">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div data-aos="fade-up" data-aos-duration="800" class="text-center mb-16 space-y-3">
                    <h3 class="text-primary font-bold tracking-widest text-sm">آراء المرضى</h3>
                    <h2 class="text-3xl md:text-4xl font-extrabold">تجربة علاجية يشهد بها الجميع</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach([
                        ['text' => 'فريق محترف ومواعيد دقيقة وتجربة مريحة جداً من أول اتصال حتى المتابعة بعد العلاج.', 'author' => 'أحمد محمود'],
                        ['text' => 'العيادة منظمة للغاية والطبيب يشرح الحالة بوضوح تام واهتمام كبير بالتفاصيل.', 'author' => 'سارة كامل'],
                        ['text' => 'الحجز عبر الموقع كان سهلاً والمتابعة مع الاستقبال ممتازة وراقية جداً.', 'author' => 'محمد عبدالله'],
                    ] as $review)
                        <div data-aos="zoom-in-up" data-aos-duration="800" data-aos-delay="{{ $loop->index * 150 }}"
                            class="glass-card p-8 rounded-3xl border border-white/40 shadow-sm hover:shadow-lg transition-shadow flex flex-col justify-between">
                            <div>
                                <div class="text-amber-500 text-lg tracking-widest mb-4">★★★★★</div>
                                <p class="text-on-surface text-base leading-relaxed mb-6 font-medium">"{{ $review['text'] }}"</p>
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-outline/10">
                                <div class="w-11 h-11 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-base">
                                    {{ mb_substr($review['author'], 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="font-bold text-sm text-on-surface">{{ $review['author'] }}</h5>
                                    <span class="text-xs text-on-surface-variant font-medium">مريض سابق · تقييم موثق</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Booking & Contact Section -->
        <section class="py-stack-lg relative overflow-hidden" id="contact">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div class="glass-card rounded-3xl overflow-hidden shadow-2xl border border-white/40 grid lg:grid-cols-12">
                    <div data-aos="fade-left" data-aos-duration="900"
                        class="lg:col-span-5 p-8 sm:p-12 lg:p-16 bg-primary text-white space-y-8 flex flex-col justify-between">
                        <div class="space-y-6">
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight">ابدأ رحلة <br />التعافي اليوم</h2>
                            <p class="text-on-primary-container leading-relaxed opacity-90 text-sm md:text-base">
                                احجز استشارتك مع نخبة من أفضل الأطباء. نقوم بمراجعة طلبك وتأكيد الموعد عبر التواصل المباشر في أسرع وقت.
                            </p>
                            <div class="space-y-4 pt-2">
                                @if($clinic->phone)
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined">call</span>
                                        </div>
                                        <span class="text-base md:text-lg font-semibold dir-ltr">{{ $clinic->phone }}</span>
                                    </div>
                                @endif
                                @if($clinic->address)
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined">location_on</span>
                                        </div>
                                        <span class="text-sm md:text-base font-semibold">{{ $clinic->address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="pt-8 border-t border-white/20">
                            <h4 class="font-bold text-sm mb-2 opacity-90">ملاحظة هامة:</h4>
                            <p class="text-xs text-on-primary-container leading-relaxed">
                                طلب الحجز يصل لفريق الاستقبال بحالة (Pending Confirmation)، وسيتم الاتصال بك لتثبيت الموعد المختار.
                            </p>
                        </div>
                    </div>
                    <div data-aos="fade-right" data-aos-duration="900" class="lg:col-span-7 p-8 sm:p-12 lg:p-16 bg-white/60 backdrop-blur-md">
                        @if(session('booking_success'))
                            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-sm flex items-center gap-3">
                                <span class="material-symbols-outlined">check_circle</span>
                                <span>{{ session('booking_success') }}</span>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 font-bold text-sm space-y-1">
                                @foreach($errors->all() as $err)
                                    <p>• {{ $err }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="relative floating-label-input group">
                                    <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required placeholder=" "
                                        class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary peer transition-all text-on-surface font-semibold text-sm" />
                                    <label for="patient_name"
                                        class="absolute right-4 top-4 text-on-surface-variant pointer-events-none transition-all duration-300 text-sm">الاسم الكامل *</label>
                                </div>
                                <div class="relative floating-label-input group">
                                    <input type="tel" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" required placeholder=" "
                                        class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary peer transition-all text-on-surface font-semibold text-sm" />
                                    <label for="patient_phone"
                                        class="absolute right-4 top-4 text-on-surface-variant pointer-events-none transition-all duration-300 text-sm">رقم الهاتف (للتواصل والتأكيد) *</label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="relative floating-label-input group">
                                    <input type="email" id="patient_email" name="patient_email" value="{{ old('patient_email') }}" placeholder=" "
                                        class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary peer transition-all text-on-surface font-semibold text-sm" />
                                    <label for="patient_email"
                                        class="absolute right-4 top-4 text-on-surface-variant pointer-events-none transition-all duration-300 text-sm">البريد الإلكتروني (اختياري)</label>
                                </div>
                                <div class="relative">
                                    <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                        class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary transition-all text-on-surface font-semibold text-sm" />
                                </div>
                            </div>

                            @if($visitTypes->isNotEmpty())
                                <div class="relative">
                                    <select id="visit_type_select" name="visit_type_id"
                                        class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary transition-all text-on-surface font-semibold text-sm">
                                        <option value="">اختر الخدمة المطلوبة (اختياري)</option>
                                        @foreach($visitTypes as $vt)
                                            <option value="{{ $vt->id }}" @selected(old('visit_type_id') == $vt->id)>
                                                {{ $vt->name }} — {{ number_format($vt->price, 0) }} ج.م
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="relative floating-label-input">
                                <textarea id="notes" name="notes" rows="3" placeholder=" "
                                    class="w-full bg-surface/80 border-outline/20 rounded-xl px-4 py-4 focus:ring-primary focus:border-primary peer transition-all text-on-surface font-semibold text-sm">{{ old('notes') }}</textarea>
                                <label for="notes"
                                    class="absolute right-4 top-4 text-on-surface-variant pointer-events-none transition-all duration-300 text-sm">ملاحظات أو تفاصيل الحالة (اختياري)</label>
                            </div>

                            <button type="submit"
                                class="w-full bg-primary text-white py-5 rounded-2xl font-extrabold text-base md:text-lg shadow-xl hover:bg-primary-container hover:scale-[1.01] transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                                <span>تأكيد طلب حجز الموعد ←</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location & Map Section -->
        @if($clinic->address || ($clinic->latitude && $clinic->longitude))
        <section class="py-12" id="location">
            <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
                <div data-aos="zoom-in" data-aos-duration="800" class="glass-card p-4 sm:p-6 rounded-3xl border border-white/40 shadow-lg">
                    <div class="mb-6 px-2">
                        <h3 class="text-xl font-extrabold text-on-surface">موقع العيادة على الخريطة</h3>
                        @if($clinic->address)
                            <p class="text-sm text-on-surface-variant mt-1">{{ $clinic->address }}</p>
                        @endif
                    </div>
                    <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-outline/10">
                        @if($clinic->latitude && $clinic->longitude)
                            <iframe title="موقع العيادة" class="w-full h-full border-0"
                                    src="https://maps.google.com/maps?q={{ $clinic->latitude }},{{ $clinic->longitude }}&output=embed"
                                    loading="lazy"></iframe>
                        @else
                            <iframe title="موقع العيادة" class="w-full h-full border-0"
                                    src="https://maps.google.com/maps?q={{ urlencode($clinic->address ?? $clinic->name) }}&output=embed"
                                    loading="lazy"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- FAQ Section -->
        <section class="py-16" id="faq">
            <div class="max-w-[1024px] mx-auto px-4 md:px-container-padding-desktop">
                <div data-aos="fade-up" data-aos-duration="800" class="text-center mb-12 space-y-2">
                    <span class="text-primary font-bold text-sm tracking-widest">الأسئلة الشائعة</span>
                    <h2 class="text-3xl font-extrabold">إجابات على استفساراتكم</h2>
                </div>
                <div class="space-y-4">
                    @foreach($faqs as $q => $a)
                        <details data-aos="fade-up" data-aos-duration="700" data-aos-delay="{{ $loop->index * 80 }}"
                            class="glass-card rounded-2xl border border-white/40 shadow-sm group">
                            <summary class="flex items-center justify-between p-6 font-bold text-base md:text-lg cursor-pointer select-none list-none text-on-surface">
                                <span>{{ $q }}</span>
                                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold transition-transform group-open:rotate-45">+</span>
                            </summary>
                            <div class="px-6 pb-6 text-sm md:text-base text-on-surface-variant leading-relaxed border-t border-outline/10 pt-4">
                                {{ $a }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer data-aos="fade-up" data-aos-duration="800" class="bg-surface border-t border-outline/15 py-16 mt-12">
        <div class="max-w-[1440px] mx-auto px-4 md:px-container-padding-desktop">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="space-y-6">
                    <h2 class="font-extrabold text-2xl tracking-tight text-primary">{{ $clinic->name }}</h2>
                    <p class="text-sm text-on-surface-variant leading-relaxed font-medium">
                        {{ $clinic->doctor_title ?? 'مركز طبي رائد' }} يجمع بين الفخامة والخبرة الطبية لتقديم رعاية صحية متكاملة تليق بكم.
                    </p>
                    @if(!empty($clinic->social_links))
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($clinic->social_links as $platform => $url)
                                @if($url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener"
                                        class="w-10 h-10 rounded-xl border border-outline/20 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all shadow-sm"
                                        title="{{ ucfirst($platform) }}">
                                        <span class="text-xs font-bold">{{ mb_substr(ucfirst($platform), 0, 2) }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="space-y-4">
                    <h4 class="font-bold text-base text-on-surface">روابط سريعة</h4>
                    <ul class="space-y-2.5">
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#hero">الرئيسية</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#services">التخصصات والخدمات</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold block" href="#about">عن العيادة والأطباء</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-semibold block" href="#hours">ساعات العمل</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-bold text-base text-on-surface">الخدمات والمواعيد</h4>
                    <ul class="space-y-2.5">
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#contact">حجز موعد جديد</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#reviews">آراء وتجارب المرضى</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#faq">الأسئلة الشائعة</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all text-sm font-semibold block" href="#location">خريطة الموقع</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-bold text-base text-on-surface">تواصل معنا</h4>
                    @if($clinic->phone)
                        <p class="text-sm font-bold text-primary dir-ltr">{{ $clinic->phone }}</p>
                    @endif
                    @if($clinic->address)
                        <p class="text-sm text-on-surface-variant font-medium">{{ $clinic->address }}</p>
                    @endif
                    <p class="text-xs text-on-surface-variant mt-4 leading-relaxed">
                        نحن هنا للإجابة على استفساراتكم ومساعدتكم في الوصول إلى أفضل الرعاية الصحية.
                    </p>
                </div>
            </div>
            <div class="mt-16 pt-8 border-t border-outline/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-semibold text-on-surface-variant">
                <p>© {{ date('Y') }} {{ $clinic->name }}. جميع الحقوق محفوظة.</p>
                <p class="flex items-center gap-1.5">مدعوم بواسطة <a href="{{ route('home') }}" class="text-primary font-extrabold hover:underline inline-flex items-center gap-1"><img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" class="w-5 h-5 object-contain rounded" alt="شعار كلينورا"> Clinora</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-out-cubic',
                    once: true,
                    offset: 60,
                });
            }
        });

        // Hamburger & Mobile Drawer Toggle
        const hamburger = document.getElementById('hamburger');
        const drawer = document.getElementById('nav-drawer');
        const iconMenu = hamburger?.querySelector('.icon-menu');
        const iconClose = hamburger?.querySelector('.icon-close');

        hamburger?.addEventListener('click', () => {
            const isHidden = drawer.classList.contains('hidden');
            if (isHidden) {
                drawer.classList.remove('hidden');
                drawer.classList.add('flex');
                iconMenu?.classList.add('hidden');
                iconClose?.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                drawer.classList.add('hidden');
                drawer.classList.remove('flex');
                iconMenu?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        drawer?.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                drawer?.classList.add('hidden');
                drawer?.classList.remove('flex');
                iconMenu?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });

        // Select Visit Type helper
        function selectVisitType(id) {
            const select = document.getElementById('visit_type_select');
            if (select) {
                select.value = id;
            }
        }

        // GSAP Animations
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);

                // Counter Animation
                const counters = document.querySelectorAll('.counter');
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const updateCount = () => {
                        const current = +counter.innerText.replace(/[^0-9]/g, '');
                        const increment = Math.max(1, Math.ceil(target / 40));
                        if (current < target) {
                            counter.innerText = Math.min(target, current + increment);
                            setTimeout(updateCount, 35);
                        } else {
                            counter.innerText = target > 100 ? target + '+' : target;
                        }
                    };

                    ScrollTrigger.create({
                        trigger: counter,
                        onEnter: updateCount,
                        once: true
                    });
                });
            }
        });

        // Sticky Navbar subtle effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('navbar');
            if (header) {
                if (window.scrollY > 40) {
                    header.classList.add('h-20', 'shadow-md');
                    header.classList.remove('h-24');
                } else {
                    header.classList.add('h-24');
                    header.classList.remove('h-20', 'shadow-md');
                }
            }
        });
    </script>
</body>
</html>
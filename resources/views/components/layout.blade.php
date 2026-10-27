<!DOCTYPE html>
<html class="scroll-smooth" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>كلينورا | منصة إدارة العيادات الحديثة</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}"
        type="image/x-icon">
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-background": "#131b2e",
                        "on-primary": "#ffffff",
                        "on-error": "#ffffff",
                        "secondary-container": "#57dffe",
                        "on-primary-fixed": "#00174b",
                        "on-tertiary-fixed-variant": "#005236",
                        "surface-container-highest": "#dae2fd",
                        "tertiary": "#006242",
                        "on-secondary-fixed-variant": "#004e5c",
                        "on-primary-container": "#eeefff",
                        "primary": "#004ac6",
                        "tertiary-fixed": "#6ffbbe",
                        "surface-container-high": "#e2e7ff",
                        "primary-fixed-dim": "#b4c5ff",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed-dim": "#4edea3",
                        "primary-fixed": "#dbe1ff",
                        "on-tertiary-fixed": "#002113",
                        "surface-dim": "#d2d9f4",
                        "primary-container": "#2563eb",
                        "on-error-container": "#93000a",
                        "surface-bright": "#faf8ff",
                        "surface": "#faf8ff",
                        "surface-tint": "#0053db",
                        "on-surface-variant": "#434655",
                        "on-tertiary": "#ffffff",
                        "outline": "#737686",
                        "surface-container-low": "#f2f3ff",
                        "on-primary-fixed-variant": "#003ea8",
                        "inverse-on-surface": "#eef0ff",
                        "secondary-fixed-dim": "#4cd7f6",
                        "tertiary-container": "#007d55",
                        "error": "#ba1a1a",
                        "secondary": "#00687a",
                        "on-surface": "#131b2e",
                        "on-secondary-container": "#006172",
                        "outline-variant": "#c3c6d7",
                        "inverse-surface": "#283044",
                        "on-tertiary-container": "#bdffdb",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#acedff",
                        "on-secondary-fixed": "#001f26",
                        "surface-container": "#eaedff",
                        "background": "#faf8ff",
                        "inverse-primary": "#b4c5ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-variant": "#dae2fd",
                        "brand": {
                            "50": "#f0f4ff",
                            "100": "#dbe4ff",
                            "200": "#bac8ff",
                            "300": "#91a7ff",
                            "400": "#748ffc",
                            "500": "#4c6ef5",
                            "600": "#3b5bdb",
                            "700": "#364fc7",
                            "800": "#2f44ad",
                            "900": "#283a97",
                            "950": "#192356"
                        }
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "64px",
                        "gutter": "24px",
                        "card-padding": "32px",
                        "section-gap": "80px",
                        "unit": "4px",
                        "margin-mobile": "20px",
                        "container-max": "1440px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Cairo"],
                        "label-sm": ["Tajawal"],
                        "headline-md": ["Cairo"],
                        "headline-lg": ["Cairo"],
                        "label-md": ["Tajawal"],
                        "display-lg": ["Cairo"],
                        "body-lg": ["Tajawal"],
                        "body-md": ["Tajawal"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["24px", {
                            "lineHeight": "1.2",
                            "fontWeight": "600"
                        }],
                        "label-sm": ["12px", {
                            "lineHeight": "1.2",
                            "fontWeight": "600"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "1.3",
                            "fontWeight": "600"
                        }],
                        "headline-lg": ["32px", {
                            "lineHeight": "1.2",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "600"
                        }],
                        "label-md": ["14px", {
                            "lineHeight": "1.4",
                            "letterSpacing": "0.01em",
                            "fontWeight": "500"
                        }],
                        "display-lg": ["48px", {
                            "lineHeight": "1.1",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "1.6",
                            "fontWeight": "400"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "1.5",
                            "fontWeight": "400"
                        }]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.5);
        }

        .soft-bloom {
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.04);
        }

        .active-nav {
            position: relative;
        }

        .active-nav::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            left: 0;
            height: 2px;
            background-color: #004ac6;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .float-delayed-1 {
            animation-delay: 1s;
        }

        .float-delayed-2 {
            animation-delay: 2s;
        }

        .float-delayed-3 {
            animation-delay: 3s;
        }

        .gradient-text {
            background: linear-gradient(135deg, #004ac6 0%, #06B6D4 50%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .ai-glow {
            position: relative;
        }

        .ai-glow::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, #06B6D4, #2563EB);
            border-radius: inherit;
            z-index: -1;
            filter: blur(8px);
            opacity: 0.5;
        }

        /* WhatsApp floating button */
        .whatsapp-wrap {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 60;
        }

        .whatsapp-float {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 8px 24px rgba(18, 140, 126, 0.45);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            position: relative;
            text-decoration: none;
        }

        .whatsapp-float svg {
            width: 30px;
            height: 30px;
            display: block;
        }

        .whatsapp-float:hover {
            transform: scale(1.08);
            box-shadow: 0 10px 28px rgba(18, 140, 126, 0.55);
        }

        .whatsapp-label {
            position: absolute;
            top: 50%;
            right: calc(100% + 12px);
            transform: translateY(-50%) translateX(8px);
            background: #ffffff;
            color: #128C7E;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 16px;
            border-radius: 9999px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        @media (hover: hover) {
            .whatsapp-wrap:hover .whatsapp-label {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.55);
            }

            70% {
                box-shadow: 0 0 0 14px rgba(37, 211, 102, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        .whatsapp-pulse {
            animation: pulse-ring 2.4s infinite;
        }

        /* smooth hover pop for cards not covered by AOS */
        .hover-pop:hover {
            transform: translateY(-6px);
        }

        /* offset anchored sections so the sticky header doesn't cover the target */
        section[id] {
            scroll-margin-top: 96px;
        }

        footer[id] {
            scroll-margin-top: 40px;
        }
    </style>
</head>

<body class="bg-background text-on-background selection:bg-primary-fixed-dim overflow-x-hidden">
    <!-- Section 1: TopNavBar -->
    <header class="docked full-width top-0 sticky z-50 bg-surface/70 backdrop-blur-xl shadow-sm">
        <nav
            class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group" aria-label="كلينورا - الصفحة الرئيسية">
                    <img class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl shadow-sm object-cover"
                        src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="شعار كلينورا">
                    <span class="font-display-lg text-lg sm:text-xl font-black text-primary tracking-tight hidden sm:inline">كلينورا</span>
                </a>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a class="nav-link text-primary font-bold border-b-2 border-primary font-label-md text-label-md transition-colors duration-300"
                    href="{{ url('/#features') }}">المميزات</a>
                <a class="nav-link text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-300"
                    href="{{ url('/#ai') }}">الذكاء الاصطناعي</a>
                <a class="nav-link text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-300"
                    href="{{ url('/#how-it-works') }}">كيف يعمل</a>
                <a class="nav-link text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-300"
                    href="{{ url('/#pricing') }}">الأسعار</a>
                <a class="nav-link text-on-surface-variant font-medium font-label-md text-label-md hover:text-primary transition-colors duration-300"
                    href="{{ url('/#contact') }}">تواصل معنا</a>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('login') }}"
                    class="text-on-surface-variant hover:text-primary font-bold font-label-md text-label-md px-4 py-2 rounded-full transition-colors">تسجيل
                    الدخول</a>
                <a href="{{ route('register') }}"
                    class="bg-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold font-label-md text-label-md hover:bg-primary transition-all shadow-lg shadow-primary/20">ابدأ
                    الآن</a>
            </div>
            <!-- Mobile menu toggle -->
            <button aria-controls="mobile-menu" aria-expanded="false" aria-label="فتح القائمة"
                class="md:hidden flex items-center justify-center w-11 h-11 rounded-xl bg-surface-container-high text-on-surface"
                id="menu-toggle">
                <span class="material-symbols-outlined" id="menu-icon">menu</span>
            </button>
        </nav>
        <!-- Mobile dropdown menu -->
        <div class="md:hidden hidden border-t border-outline-variant bg-surface/95 backdrop-blur-xl" id="mobile-menu">
            <div class="flex flex-col px-margin-mobile py-4 gap-1">
                <a class="mobile-nav-link py-3 px-2 rounded-lg text-on-surface font-bold font-label-md text-label-md hover:bg-surface-container-low transition-colors"
                    href="{{ url('/#features') }}">المميزات</a>
                <a class="mobile-nav-link py-3 px-2 rounded-lg text-on-surface-variant font-medium font-label-md text-label-md hover:bg-surface-container-low transition-colors"
                    href="{{ url('/#ai') }}">الذكاء الاصطناعي</a>
                <a class="mobile-nav-link py-3 px-2 rounded-lg text-on-surface-variant font-medium font-label-md text-label-md hover:bg-surface-container-low transition-colors"
                    href="{{ url('/#how-it-works') }}">كيف يعمل</a>
                <a class="mobile-nav-link py-3 px-2 rounded-lg text-on-surface-variant font-medium font-label-md text-label-md hover:bg-surface-container-low transition-colors"
                    href="{{ url('/#pricing') }}">الأسعار</a>
                <a class="mobile-nav-link py-3 px-2 rounded-lg text-on-surface-variant font-medium font-label-md text-label-md hover:bg-surface-container-low transition-colors"
                    href="{{ url('/#contact') }}">تواصل معنا</a>
                <div class="flex items-center gap-3 pt-3 mt-2 border-t border-outline-variant">
                    <a href="{{ route('login') }}"
                        class="flex-1 text-center text-on-surface-variant font-bold font-label-md text-label-md py-3 rounded-xl border border-outline-variant">تسجيل
                        الدخول</a>
                    <a href="{{ route('register') }}"
                        class="flex-1 text-center bg-primary-container text-on-primary py-3 rounded-xl font-bold font-label-md text-label-md shadow-lg shadow-primary/20">ابدأ
                        الآن</a>
                </div>
            </div>
        </div>
    </header>
    @yield('content')
    <!-- Section 11: Footer -->
    <footer class="full-width bg-surface-container-low border-t border-outline-variant" id="contact">
        <div
            class="flex flex-col md:flex-row justify-between items-start w-full px-4 sm:px-6 md:px-margin-desktop pt-14 pb-10 max-w-container-max mx-auto gap-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between w-full gap-8">
                <div class="max-w-md space-y-3">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img class="w-16 h-16 rounded-2xl shadow-sm object-cover"
                            src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="شعار كلينورا">
                        <span class="font-display-lg text-2xl font-black text-primary">كلينورا</span>
                    </a>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">منصة سحابية متكاملة لإدارة العيادات والمراكز الطبية، تجمع بين الحجز الإلكتروني، الطابور الذكي، السجل الطبي، والذكاء الاصطناعي في مكان واحد.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-8 sm:gap-12">
                    <div>
                        <p class="font-bold text-on-surface mb-3 text-sm">روابط سريعة</p>
                        <ul class="space-y-2.5 text-sm text-on-surface-variant font-medium">
                            <li><a href="{{ route('register') }}" class="hover:text-primary transition-colors">إنشاء حساب عيادة</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-primary transition-colors">تسجيل الدخول</a></li>
                            <li><a href="{{ route('privacy') }}" class="hover:text-primary transition-colors">سياسة الخصوصية</a></li>
                            <li><a href="{{ route('terms') }}" class="hover:text-primary transition-colors">شروط الاستخدام</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface mb-3 text-sm">تواصل معنا</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary text-lg">call</span>
                                <a class="hover:text-primary transition-colors font-semibold" dir="ltr"
                                    href="tel:+201099615358">01099615358</a>
                            </li>
                            <li class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary text-lg">mail</span>
                                <a class="hover:text-primary transition-colors font-semibold"
                                    href="mailto:support@clinora.app">support@clinora.app</a>
                            </li>
                            <li class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary text-lg">location_on</span>
                                <span>القاهرة، مصر</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="max-w-container-max mx-auto px-4 sm:px-6 md:px-margin-desktop py-6 border-t border-outline-variant flex flex-col md:flex-row justify-between items-center gap-4 text-label-sm text-on-surface-variant">
            <p>© <span id="current-year">2026</span> كلينورا. جميع الحقوق محفوظة.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('privacy') }}" class="hover:text-primary transition-colors">الخصوصية</a>
                <span>•</span>
                <a href="{{ route('terms') }}" class="hover:text-primary transition-colors">الشروط والأحكام</a>
                <a aria-label="تواصل عبر واتساب"
                    class="w-9 h-9 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all"
                    href="https://wa.me/201099615358" rel="noopener noreferrer" target="_blank">
                    <svg fill="currentColor" height="16" viewBox="0 0 24 24" width="16">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.001 2C6.478 2 2 6.477 2 12c0 1.887.525 3.647 1.438 5.152L2 22l4.955-1.409A9.945 9.945 0 0012.001 22C17.523 22 22 17.523 22 12S17.523 2 12.001 2z">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <div class="whatsapp-wrap">
        <span class="whatsapp-label">تواصل معنا واتساب</span>
        <a aria-label="تواصل معنا عبر واتساب" class="whatsapp-float whatsapp-pulse" href="https://wa.me/201099615358"
            rel="noopener noreferrer" target="_blank">
            <svg fill="currentColor" height="30" viewBox="0 0 24 24" width="30"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                </path>
                <path
                    d="M12.001 2C6.478 2 2 6.477 2 12c0 1.887.525 3.647 1.438 5.152L2 22l4.955-1.409A9.945 9.945 0 0 0 12.001 22C17.523 22 22 17.523 22 12S17.523 2 12.001 2zm0 18.09a8.06 8.06 0 0 1-4.31-1.243l-.309-.184-3.061.872.87-2.99-.201-.309A8.084 8.084 0 0 1 3.91 12c0-4.464 3.628-8.09 8.091-8.09 4.463 0 8.09 3.626 8.09 8.09 0 4.464-3.627 8.09-8.09 8.09z">
                </path>
            </svg>
        </a>
    </div>

    <!-- AOS Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        const yearEl = document.getElementById('current-year');
        if (yearEl) {
            yearEl.textContent = new Date().getFullYear();
        }

        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                const isOpen = !mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden');
                menuToggle.setAttribute('aria-expanded', String(!isOpen));
                menuIcon.textContent = isOpen ? 'menu' : 'close';
            });
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuIcon.textContent = 'menu';
                });
            });
        }

        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 80
        });

        // Simple scroll spy for active navigation state
        const sections = document.querySelectorAll('section[id], footer[id]');
        const navLinks = document.querySelectorAll('header nav a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('text-primary', 'font-bold', 'border-b-2', 'border-primary');
                link.classList.add('text-on-surface-variant', 'font-medium');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.remove('text-on-surface-variant', 'font-medium');
                    link.classList.add('text-primary', 'font-bold', 'border-b-2', 'border-primary');
                }
            });
        });

        // Smooth scroll for nav links (in addition to CSS scroll-smooth)
        document.querySelectorAll('header nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>

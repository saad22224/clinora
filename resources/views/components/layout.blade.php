<!DOCTYPE html>
<html class="scroll-smooth" lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'كلينورا | منصة إدارة العيادات والمراكز الطبية' }}</title>
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}"
        type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0D9488",
                        "primary-container": "#34548A",
                        "accent-amber": "#F59E0B",
                        "accent-amber-hover": "#D97706",
                        "navy-btn": "#34548A",
                        "navy-btn-hover": "#2B4675",
                        "dark-bg": "#080B14",
                        "dark-nav": "#0B0E17",
                        "dark-card": "#121624",
                        "dark-input": "#181E31",
                        "light-bg": "#F8FAFC",
                        "light-input": "#EEF2FF",
                        "brand": {
                            "50": "#f0fdfa",
                            "100": "#ccfbf1",
                            "200": "#99f6e4",
                            "300": "#5eead4",
                            "400": "#2dd4bf",
                            "500": "#14b8a6",
                            "600": "#0d9488",
                            "700": "#0f766e",
                            "800": "#115e59",
                            "900": "#134e4a",
                            "950": "#042f2e"
                        }
                    },
                    spacing: {
                        "margin-desktop": "64px",
                        "margin-mobile": "20px",
                        "container-max": "1360px"
                    },
                    fontFamily: {
                        "sans": ["Cairo", "Tajawal", "sans-serif"],
                        "display": ["Cairo", "sans-serif"],
                        "body": ["Tajawal", "Cairo", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Cairo', 'Tajawal', sans-serif;
        }

        html.dark {
            color-scheme: dark;
        }

        /* Subtle technical grid background matching the reference design */
        .clinora-grid-bg {
            background-color: #F8FAFC;
            background-image:
                radial-gradient(circle at 82% 14%, rgba(20, 184, 166, 0.08), transparent 36%),
                radial-gradient(circle at 18% 28%, rgba(79, 70, 229, 0.07), transparent 38%),
                linear-gradient(to right, rgba(15, 23, 42, 0.038) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.038) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }

        html.dark .clinora-grid-bg {
            background-color: #080B14;
            background-image:
                radial-gradient(circle at 82% 14%, rgba(20, 184, 166, 0.10), transparent 38%),
                radial-gradient(circle at 18% 28%, rgba(79, 70, 229, 0.10), transparent 40%),
                linear-gradient(to right, rgba(255, 255, 255, 0.032) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.032) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }

        .clinora-card {
            background-color: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.06);
        }

        html.dark .clinora-card {
            background-color: #121624;
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.55);
        }

        .clinora-input {
            background-color: rgba(238, 242, 255, 0.75);
            border: 1px solid #CBD5E1;
            color: #0F172A;
            transition: all 0.2s ease;
        }

        .clinora-input::placeholder {
            color: #64748B;
        }

        .clinora-input:focus {
            background-color: #FFFFFF;
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.16);
            outline: none;
        }

        html.dark .clinora-input {
            background-color: #181E31;
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: #F8FAFC;
        }

        html.dark .clinora-input::placeholder {
            color: #64748B;
        }

        html.dark .clinora-input:focus {
            background-color: #1D243B;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.22);
            outline: none;
        }

        /* Offset anchored sections so the sticky header does not cover them */
        section[id],
        footer[id] {
            scroll-margin-top: 88px;
        }
    </style>
</head>

<body class="clinora-grid-bg text-slate-900 dark:text-slate-100 selection:bg-teal-500/20 overflow-x-hidden min-h-screen flex flex-col transition-colors duration-200">
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 w-full bg-white/90 dark:bg-[#0B0E17]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-white/[0.07] transition-colors duration-200">
        <nav class="flex justify-between items-center w-full px-4 sm:px-6 md:px-12 py-3.5 max-w-[1360px] mx-auto" aria-label="التنقل الرئيسي">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-xl" aria-label="كلينورا - الصفحة الرئيسية">
                    <div class="w-11 h-11 rounded-xl overflow-hidden border border-slate-200/90 dark:border-white/10 bg-white shadow-sm shrink-0">
                        <img class="w-full h-full object-cover"
                            src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="شعار كلينورا">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-none tracking-tight">كلينورا</span>
                        <span class="text-[10px] font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400 mt-1">CLINORA SAAS</span>
                    </div>
                </a>
            </div>

            <!-- Center Desktop Links -->
            <div class="hidden lg:flex items-center gap-7">
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#how-it-works') }}">كيف يعمل</a>
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#features') }}">المميزات</a>
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#about') }}">عن المنصة</a>
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#pricing') }}">الأسعار</a>
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#faq') }}">الأسئلة الشائعة</a>
                <a class="nav-link text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors"
                    href="{{ url('/#contact') }}">تواصل معنا</a>
            </div>

            <!-- Action Buttons & Theme Toggle -->
            <div class="hidden md:flex items-center gap-3">
                <button type="button" id="theme-toggle" data-theme-toggle
                    aria-label="تبديل الوضع الليلي والنهاري"
                    title="تبديل المظهر (ليلي / نهاري)"
                    class="w-10 h-10 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#1F2640] flex items-center justify-center transition-all shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <!-- Moon icon shown in Light Mode -->
                    <svg class="w-4 h-4 block dark:hidden text-slate-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <!-- Sun/Gear icon shown in Dark Mode -->
                    <svg class="w-4 h-4 hidden dark:block text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                <a href="{{ route('login') }}"
                    class="bg-[#34548A] hover:bg-[#2B4675] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    تسجيل الدخول
                </a>
                <a href="{{ route('register') }}"
                    class="bg-[#F59E0B] hover:bg-[#D97706] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-md shadow-amber-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                    إنشاء حساب
                </a>
            </div>

            <!-- Mobile Theme Toggle + Menu Toggle -->
            <div class="flex md:hidden items-center gap-2">
                <button type="button" id="theme-toggle-mobile" data-theme-toggle
                    aria-label="تبديل الوضع الليلي والنهاري"
                    class="w-10 h-10 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-700 dark:text-slate-200 flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-4 h-4 block dark:hidden text-slate-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
                <button aria-controls="mobile-menu" aria-expanded="false" aria-label="فتح القائمة"
                    class="flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-800 dark:text-slate-100"
                    id="menu-toggle" type="button">
                    <span class="material-symbols-outlined text-xl" id="menu-icon">menu</span>
                </button>
            </div>
        </nav>

        <!-- Mobile Dropdown Menu -->
        <div class="md:hidden hidden border-t border-slate-200 dark:border-white/[0.07] bg-white/95 dark:bg-[#0B0E17]/95 backdrop-blur-xl" id="mobile-menu">
            <div class="flex flex-col px-4 py-4 gap-1">
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-800 dark:text-slate-100 font-bold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#how-it-works') }}">كيف يعمل</a>
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#features') }}">المميزات</a>
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#about') }}">عن المنصة</a>
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#pricing') }}">الأسعار</a>
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#faq') }}">الأسئلة الشائعة</a>
                <a class="mobile-nav-link py-2.5 px-3 rounded-xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                    href="{{ url('/#contact') }}">تواصل معنا</a>
                <div class="grid grid-cols-2 gap-3 pt-3 mt-2 border-t border-slate-200 dark:border-white/10">
                    <a href="{{ route('login') }}"
                        class="text-center bg-[#34548A] hover:bg-[#2B4675] text-white font-bold text-sm py-3 rounded-xl transition-colors">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}"
                        class="text-center bg-[#F59E0B] hover:bg-[#D97706] text-white font-bold text-sm py-3 rounded-xl shadow-md shadow-amber-500/20 transition-colors">
                        إنشاء حساب
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="w-full bg-white dark:bg-[#0B0E17] border-t border-slate-200/80 dark:border-white/[0.07] transition-colors duration-200" id="contact">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 md:px-12 pt-14 pb-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                <div class="md:col-span-5 space-y-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 bg-white shadow-sm">
                            <img class="w-full h-full object-cover"
                                src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="شعار كلينورا">
                        </div>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">كلينورا</span>
                    </a>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed max-w-md font-medium">
                        منصة سحابية متكاملة لإدارة العيادات والمراكز الطبية وسلاسل العيادات، تجمع بين الحجز الإلكتروني، الطابور اللحظي، السجل الطبي، الروشتات الإلكترونية، والتقارير المالية في نظام واحد.
                    </p>
                </div>

                <div class="md:col-span-3 space-y-3">
                    <p class="font-extrabold text-slate-900 dark:text-white text-sm">روابط سريعة</p>
                    <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400 font-semibold">
                        <li><a href="{{ route('register') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">إنشاء حساب عيادة</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">تسجيل الدخول</a></li>
                        <li><a href="{{ url('/#features') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">مميزات النظام</a></li>
                        <li><a href="{{ url('/#pricing') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الباقات والأسعار</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">سياسة الخصوصية</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">شروط الاستخدام</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4 space-y-3">
                    <p class="font-extrabold text-slate-900 dark:text-white text-sm">تواصل معنا</p>
                    <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-400 font-semibold">
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                                <span class="material-symbols-outlined text-lg">call</span>
                            </span>
                            <a class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors" dir="ltr"
                                href="tel:+201099615358">01099615358</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                                <span class="material-symbols-outlined text-lg">mail</span>
                            </span>
                            <a class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors" dir="ltr"
                                href="mailto:support@clinora.app">support@clinora.app</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                                <span class="material-symbols-outlined text-lg">location_on</span>
                            </span>
                            <span>القاهرة، مصر</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 md:px-12 py-5 border-t border-slate-200/80 dark:border-white/[0.07] flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-slate-400 font-semibold">
            <p>© <span id="current-year">2026</span> كلينورا. جميع الحقوق محفوظة.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('privacy') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الخصوصية</a>
                <span>•</span>
                <a href="{{ route('terms') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الشروط والأحكام</a>
            </div>
        </div>
    </footer>

    <!-- Floating Scroll-to-Top Button (Matches Reference Image 1) -->
    <button type="button" id="scroll-top-btn" aria-label="العودة إلى الأعلى"
        class="fixed bottom-6 left-6 z-40 w-11 h-11 rounded-full bg-indigo-500 hover:bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 flex items-center justify-center transition-all opacity-0 pointer-events-none translate-y-2">
        <span class="material-symbols-outlined text-xl">arrow_upward</span>
    </button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        (function () {
            function syncThemeButtons() {
                var isDark = document.documentElement.classList.contains('dark');
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
                document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
                    btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                    btn.setAttribute('aria-label', isDark ? 'التبديل إلى الوضع النهاري' : 'التبديل إلى الوضع الليلي');
                });
            }

            window.setTheme = function (mode) {
                var useDark = mode === 'dark';
                document.documentElement.classList.toggle('dark', useDark);
                try {
                    localStorage.setItem('theme', useDark ? 'dark' : 'light');
                } catch (e) {}
                syncThemeButtons();
            };

            window.toggleTheme = function () {
                var isDark = document.documentElement.classList.contains('dark');
                window.setTheme(isDark ? 'light' : 'dark');
            };

            document.addEventListener('DOMContentLoaded', function () {
                syncThemeButtons();

                document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        window.toggleTheme();
                    });
                });

                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
                        try {
                            if (!localStorage.getItem('theme')) {
                                document.documentElement.classList.toggle('dark', e.matches);
                                syncThemeButtons();
                            }
                        } catch (err) {}
                    });
                }

                var yearEl = document.getElementById('current-year');
                if (yearEl) {
                    yearEl.textContent = new Date().getFullYear();
                }

                // Mobile menu toggle
                var menuToggle = document.getElementById('menu-toggle');
                var mobileMenu = document.getElementById('mobile-menu');
                var menuIcon = document.getElementById('menu-icon');
                if (menuToggle && mobileMenu) {
                    menuToggle.addEventListener('click', function () {
                        var isOpen = !mobileMenu.classList.contains('hidden');
                        mobileMenu.classList.toggle('hidden');
                        menuToggle.setAttribute('aria-expanded', String(!isOpen));
                        if (menuIcon) {
                            menuIcon.textContent = isOpen ? 'menu' : 'close';
                        }
                    });
                    document.querySelectorAll('.mobile-nav-link').forEach(function (link) {
                        link.addEventListener('click', function () {
                            mobileMenu.classList.add('hidden');
                            menuToggle.setAttribute('aria-expanded', 'false');
                            if (menuIcon) {
                                menuIcon.textContent = 'menu';
                            }
                        });
                    });
                }

                // Scroll-to-top button
                var scrollTopBtn = document.getElementById('scroll-top-btn');
                if (scrollTopBtn) {
                    window.addEventListener('scroll', function () {
                        if (window.scrollY > 360) {
                            scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
                        } else {
                            scrollTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                        }
                    });
                    scrollTopBtn.addEventListener('click', function () {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }

                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 650,
                        easing: 'ease-out-cubic',
                        once: true,
                        offset: 60
                    });
                }
            });
        })();
    </script>
</body>

</html>

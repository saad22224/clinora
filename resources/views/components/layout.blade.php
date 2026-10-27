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

        .material-symbols-outlined {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1em;
            height: 1em;
            line-height: 1;
            overflow: hidden;
            white-space: nowrap;
            direction: ltr;
            user-select: none;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .material-symbols-outlined svg {
            width: 1em;
            height: 1em;
            display: block;
            flex-shrink: 0;
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

                // Inline SVG fallback/enhancer for Material Symbols so icons never render raw ligature text
                var SVG_ICONS = {
                    mail: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    lock: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                    lock_reset: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
                    lock_Person: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                    visibility: '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                    visibility_off: '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>',
                    login: '<path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>',
                    person: '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                    person_add: '<path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
                    groups: '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    domain: '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                    medical_services: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V9a2 2 0 00-2-2h-3V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                    stethoscope: '<path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                    clinical_notes: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    description: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    folder_shared: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
                    format_list_numbered: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>',
                    queue_play_next: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>',
                    calendar_clock: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                    event_available: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2V7a2 2 0 00-2-2h-1V3m-8 2V3M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z"/>',
                    payments: '<path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    analytics: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                    trending_up: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                    bolt: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    check: '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
                    check_circle: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    verified: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                    verified_user: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                    error: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                    call: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
                    location_on: '<path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    menu: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>',
                    close: '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',
                    arrow_upward: '<path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>',
                    arrow_back: '<path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>',
                    tune: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>',
                    support_agent: '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    send: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    pin: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>',
                    app_registration: '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                    cloud_upload: '<path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>',
                    forum: '<path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>',
                    share: '<path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>'
                };

                function upgradeMaterialIcons() {
                    document.querySelectorAll('.material-symbols-outlined').forEach(function (el) {
                        if (el.children.length > 0) return;
                        var key = (el.textContent || '').trim();
                        if (SVG_ICONS[key]) {
                            el.setAttribute('data-icon', key);
                            el.setAttribute('aria-hidden', 'true');
                            el.innerHTML = '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">' + SVG_ICONS[key] + '</svg>';
                        }
                    });
                }

                upgradeMaterialIcons();

                if (typeof MutationObserver !== 'undefined') {
                    var iconObserver = new MutationObserver(function () {
                        upgradeMaterialIcons();
                    });
                    iconObserver.observe(document.body, { childList: true, subtree: true, characterData: true });
                }
            });
        })();
    </script>
</body>

</html>

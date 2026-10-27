<!DOCTYPE html>
<html dir="rtl" lang="ar" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $clinic->name }}{{ $clinic->doctor_title ? ' | '.$clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->hero_subtitle ?? $clinic->about_us ?? $clinic->name, 160) }}" />
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
    @php
        $primaryColor = $clinic->theme_color ?: '#0D9488';
        $doctorImage = ! empty($clinic->doctor_image)
            ? (str_starts_with($clinic->doctor_image, 'http')
                ? $clinic->doctor_image
                : Storage::url($clinic->doctor_image))
            : null;
        $visitTypes = $clinic->visitTypes ?? collect();
        $displayName = $clinic->name;
        $days = [
            'saturday' => 'السبت',
            'sunday' => 'الأحد',
            'monday' => 'الإثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
        ];
        $enabledHours = collect($clinic->working_hours ?? [])
            ->filter(fn ($data) => is_array($data) && ! empty($data['enabled']))
            ->map(function ($data) {
                $periods = $data['periods'] ?? [];
                if (empty($periods) && ! empty($data['open']) && ! empty($data['close'])) {
                    $periods = [['open' => $data['open'], 'close' => $data['close']]];
                }
                $data['resolved_periods'] = collect($periods)
                    ->filter(fn ($p) => is_array($p) && ! empty($p['open']) && ! empty($p['close']))
                    ->values()
                    ->all();

                return $data;
            })
            ->all();

        $socialLinks = collect($clinic->social_links ?? [])
            ->filter(fn ($url) => is_string($url) && trim($url) !== '')
            ->all();

        $hasAboutSection = ! empty($clinic->doctor_bio) || ! empty($clinic->about_us) || ! empty($clinic->description);
        $hasLocationSection = ! empty($clinic->address) || (! empty($clinic->latitude) && ! empty($clinic->longitude));
    @endphp
    @vite(['resources/css/app.css'])
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "clinic-primary": "{{ $primaryColor }}",
                        "navy-btn": "#34548A",
                        "navy-btn-hover": "#2B4675",
                        "accent-amber": "#F59E0B",
                        "accent-amber-hover": "#D97706",
                        "dark-bg": "#080B14",
                        "dark-nav": "#0B0E17",
                        "dark-card": "#121624",
                        "dark-input": "#181E31",
                        "light-bg": "#F8FAFC",
                        "light-input": "#EEF2FF"
                    },
                    fontFamily: {
                        "sans": ["Cairo", "Tajawal", "sans-serif"],
                        "display": ["Cairo", "sans-serif"]
                    }
                }
            }
        };
    </script>
    <style>
        :root {
            --clinic-primary: {{ $primaryColor }};
            --color-primary: {{ $primaryColor }};
        }

        body {
            font-family: 'Cairo', 'Tajawal', ui-sans-serif, system-ui, sans-serif;
        }

        html.dark {
            color-scheme: dark;
        }

        .clinora-grid-bg {
            background-color: #F8FAFC;
            background-image:
                radial-gradient(circle at 82% 12%, color-mix(in srgb, var(--clinic-primary) 10%, transparent), transparent 36%),
                radial-gradient(circle at 18% 26%, rgba(52, 84, 138, 0.08), transparent 38%),
                linear-gradient(to right, rgba(15, 23, 42, 0.038) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.038) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }

        html.dark .clinora-grid-bg {
            background-color: #080B14;
            background-image:
                radial-gradient(circle at 82% 12%, color-mix(in srgb, var(--clinic-primary) 14%, transparent), transparent 38%),
                radial-gradient(circle at 18% 26%, rgba(79, 70, 229, 0.11), transparent 40%),
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
            border: 1px solid rgba(255, 255, 255, 0.075);
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
            border-color: #34548A;
            box-shadow: 0 0 0 3px rgba(52, 84, 138, 0.18);
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

        section[id],
        footer[id] {
            scroll-margin-top: 88px;
        }
    </style>
</head>
<body class="clinora-grid-bg text-slate-900 dark:text-slate-100 overflow-x-hidden max-w-[100vw] min-h-screen flex flex-col transition-colors duration-200">
    {{-- Sticky Top Navbar --}}
    <header id="navbar"
        class="sticky top-0 inset-x-0 z-50 h-20 border-b border-slate-200/80 dark:border-white/[0.07] bg-white/90 dark:bg-[#0B0E17]/95 backdrop-blur-md transition-all duration-200">
        <div class="mx-auto flex h-full max-w-[1320px] items-center justify-between gap-3 px-4 sm:px-6 md:px-10">
            {{-- Clinic Brand --}}
            <a href="#hero" class="flex min-w-0 items-center gap-3 focus:outline-none">
                @if ($doctorImage)
                    <img src="{{ $doctorImage }}" alt="{{ $displayName }}"
                        class="h-11 w-11 shrink-0 rounded-xl object-cover border border-slate-200 dark:border-white/10 shadow-sm" />
                @else
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-black text-white shadow-sm"
                        style="background-color: var(--clinic-primary);">
                        {{ mb_substr($clinic->name, 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="truncate text-base sm:text-lg font-black leading-tight text-slate-900 dark:text-white" title="{{ $displayName }}">
                        {{ $displayName }}
                    </h1>
                    @if ($clinic->doctor_title)
                        <span class="block truncate text-xs font-bold text-teal-600 dark:text-teal-400 mt-0.5">
                            {{ $clinic->doctor_title }}
                        </span>
                    @endif
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden items-center gap-6 lg:flex" aria-label="روابط العيادة">
                <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#hero">الرئيسية</a>
                @if ($hasAboutSection)
                    <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#about">عن العيادة</a>
                @endif
                <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#how-to-book">خطوات الحجز</a>
                <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#services">الخدمات والأسعار</a>
                <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#hours">المواعيد</a>
                <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#contact">الحجز</a>
                @if ($hasLocationSection)
                    <a class="text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors" href="#location">الموقع</a>
                @endif
            </nav>

            {{-- Action Buttons & Theme Toggle --}}
            <div class="flex shrink-0 items-center gap-2.5">
                <button type="button" id="theme-toggle" data-theme-toggle
                    aria-label="تبديل الوضع الليلي والنهاري"
                    title="تبديل المظهر (ليلي / نهاري)"
                    class="w-10 h-10 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#1F2640] flex items-center justify-center transition-all shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 block dark:hidden text-slate-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                @if ($clinic->phone)
                    <a href="tel:{{ $clinic->phone }}"
                        class="hidden md:inline-flex items-center gap-2 rounded-xl bg-[#34548A] hover:bg-[#2B4675] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span dir="ltr">{{ $clinic->phone }}</span>
                    </a>
                @endif

                <a href="#contact"
                    class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-[#F59E0B] hover:bg-[#D97706] px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-amber-500/20 transition-all">
                    <span>احجز موعداً</span>
                </a>

                <button id="hamburger" type="button" aria-label="القائمة" aria-expanded="false"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-800 dark:text-slate-100 lg:hidden">
                    <span class="icon-menu text-xl leading-none">☰</span>
                    <span class="icon-close hidden text-xl leading-none">✕</span>
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile Navigation Drawer --}}
    <div id="nav-drawer"
        class="fixed inset-x-0 top-20 z-40 hidden max-h-[calc(100vh-5rem)] flex-col space-y-2 overflow-y-auto border-b border-slate-200 dark:border-white/10 bg-white/95 dark:bg-[#0B0E17]/95 backdrop-blur-xl px-5 py-5 shadow-2xl lg:hidden">
        <a href="#hero" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-white/5">الرئيسية</a>
        @if ($hasAboutSection)
            <a href="#about" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">عن الطبيب والعيادة</a>
        @endif
        <a href="#how-to-book" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">خطوات الحجز</a>
        <a href="#services" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">الخدمات والأسعار</a>
        <a href="#hours" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">ساعات العمل</a>
        @if ($hasLocationSection)
            <a href="#location" class="block rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">موقع العيادة</a>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3 border-t border-slate-200 dark:border-white/10">
            @if ($clinic->phone)
                <a href="tel:{{ $clinic->phone }}" class="block rounded-xl bg-[#34548A] py-3 text-center text-sm font-bold text-white dir-ltr">
                    {{ $clinic->phone }}
                </a>
            @endif
            <a href="#contact" class="block rounded-xl bg-[#F59E0B] py-3 text-center text-sm font-extrabold text-white shadow-md shadow-amber-500/20">
                احجز موعدك الآن
            </a>
        </div>
    </div>

    <main class="flex-1">
        {{-- 1. HERO SECTION (Matches Reference Images 2 & 3 Split Hero + Instant Access Card) --}}
        <section id="hero" class="relative overflow-hidden pt-10 pb-16 md:pt-16 md:pb-24">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                    {{-- Right Column: Clinic Title, Subtitle & Instant Booking Card --}}
                    <div class="lg:col-span-6 space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full border border-teal-500/30 bg-teal-500/10 dark:bg-teal-500/15 px-4 py-1.5 text-xs sm:text-sm font-extrabold text-teal-700 dark:text-teal-300">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>{{ $clinic->doctor_title ?: 'حجز إلكتروني مباشر ومتابعة فورية للدور' }}</span>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-3xl sm:text-5xl font-black leading-[1.18] tracking-tight text-slate-900 dark:text-white">
                                @if ($clinic->hero_title)
                                    {!! nl2br(e($clinic->hero_title)) !!}
                                @else
                                    {{ $clinic->name }}
                                @endif
                            </h2>
                            <p class="max-w-xl text-base sm:text-lg font-medium leading-relaxed text-slate-600 dark:text-slate-300">
                                {{ $clinic->hero_subtitle ?? ($clinic->doctor_bio ? Str::limit($clinic->doctor_bio, 190) : ($clinic->about_us ? Str::limit($clinic->about_us, 190) : 'احجز موعدك إلكترونياً في ثوانٍ واحصل فوراً على رقم دورك ورابط متابعة مباشر لحركة الانتظار داخل العيادة.')) }}
                            </p>
                        </div>

                        {{-- "Instant Access" Style Booking Card --}}
                        <div class="clinora-card rounded-3xl p-6 sm:p-7 max-w-xl relative overflow-hidden">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-l from-teal-500 via-indigo-500 to-amber-500"></div>

                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200/80 dark:border-white/[0.07]">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    <span class="font-extrabold text-sm sm:text-base text-indigo-600 dark:text-indigo-400">الحجز الإلكتروني السريع</span>
                                </div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">رقم دور فوري • متابعة مباشرة</span>
                            </div>

                            <div class="space-y-3">
                                <a href="#contact"
                                    class="w-full py-3.5 px-6 rounded-xl bg-[#F59E0B] hover:bg-[#D97706] text-white font-extrabold text-base flex items-center justify-center gap-2.5 shadow-md shadow-amber-500/20 transition-all hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>احجز موعدك الآن</span>
                                </a>

                                @if ($clinic->phone)
                                    <a href="tel:{{ $clinic->phone }}"
                                        class="w-full py-3.5 px-6 rounded-xl bg-[#34548A] hover:bg-[#2B4675] text-white font-extrabold text-base flex items-center justify-center gap-2.5 shadow-sm transition-all hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>اتصال مباشر:</span>
                                        <span class="dir-ltr">{{ $clinic->phone }}</span>
                                    </a>
                                @else
                                    <a href="#services"
                                        class="w-full py-3.5 px-6 rounded-xl bg-[#34548A] hover:bg-[#2B4675] text-white font-extrabold text-base flex items-center justify-center gap-2.5 shadow-sm transition-all hover:-translate-y-0.5">
                                        <span>استعرض الخدمات والأسعار</span>
                                    </a>
                                @endif
                            </div>

                            <ul class="mt-5 pt-4 border-t border-slate-200/80 dark:border-white/[0.07] space-y-2.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300">
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>إصدار رقم الدور ورابط المتابعة فور إتمام الحجز</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>وضوح تام في أسعار الخدمات والكشوفات المتاحة</span>
                                </li>
                                @if ($clinic->address)
                                    <li class="flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        </span>
                                        <span class="truncate">{{ $clinic->address }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Left Column: Doctor Portrait Card + Layered Operational Cards --}}
                    <div class="lg:col-span-6 relative">
                        <div class="relative mx-auto max-w-md lg:max-w-lg">
                            {{-- Top Floating Card: Services Count --}}
                            <div class="clinora-card rounded-2xl p-4 sm:p-5 mb-4 sm:mb-0 sm:absolute sm:-top-5 sm:-right-5 z-20 sm:w-64 shadow-xl">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-extrabold text-slate-500 dark:text-slate-400">الخدمات الطبية المتاحة</span>
                                    <span class="w-8 h-8 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V9a2 2 0 00-2-2h-3V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl font-black text-slate-900 dark:text-white">
                                        {{ $visitTypes->count() > 0 ? $visitTypes->count().' خدمات' : 'حجز مباشر' }}
                                    </span>
                                    <span class="text-xs font-bold text-teal-600 dark:text-teal-400">معلنة الأسعار</span>
                                </div>
                            </div>

                            {{-- Main Doctor / Clinic Card --}}
                            <div class="clinora-card overflow-hidden rounded-[2rem] p-3 sm:p-4">
                                <div class="overflow-hidden rounded-[1.5rem] relative">
                                    @if ($doctorImage)
                                        <img alt="{{ $clinic->name }}" class="aspect-[4/5] w-full object-cover"
                                            src="{{ $doctorImage }}" width="560" height="700" loading="eager" decoding="async" />
                                    @else
                                        <div class="flex aspect-[4/5] w-full flex-col items-center justify-center p-8 text-center text-white relative overflow-hidden"
                                            style="background: linear-gradient(135deg, var(--clinic-primary) 0%, #34548A 100%);">
                                            <div class="w-28 h-28 rounded-3xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-6xl font-black shadow-lg mb-5">
                                                {{ mb_substr($clinic->name, 0, 1) }}
                                            </div>
                                            <p class="text-2xl sm:text-3xl font-black leading-snug">{{ $clinic->name }}</p>
                                            @if ($clinic->doctor_title)
                                                <p class="mt-2 text-sm sm:text-base font-bold text-white/85">{{ $clinic->doctor_title }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom Floating Card: Live Queue Tracking Highlight --}}
                            <div class="clinora-card rounded-2xl p-4 sm:p-5 mt-4 sm:mt-0 sm:absolute sm:-bottom-5 sm:-left-5 z-20 sm:w-72 shadow-xl">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">متابعة لحظية للدور</p>
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">اعرف ترتيبك الحالي من هاتفك</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg">مباشر</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. REAL CLINIC FACTS ROW (Matches Reference Image 1 3-Card Row — 100% Real Data) --}}
        <section class="py-10 md:py-14">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Card 1: Doctor & Specialty --}}
                    <div class="clinora-card rounded-3xl p-6 sm:p-7 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">التخصص والإشراف الطبي</p>
                            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1 truncate">
                                {{ $clinic->doctor_title ?: $clinic->name }}
                            </p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1 truncate">{{ $clinic->name }}</p>
                        </div>
                    </div>

                    {{-- Card 2: Available Visit Types --}}
                    <div class="clinora-card rounded-3xl p-6 sm:p-7 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">الخدمات والكشوفات</p>
                            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1">
                                {{ $visitTypes->count() > 0 ? $visitTypes->count().' خدمات متاحة للحجز' : 'استفسر عبر الهاتف' }}
                            </p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">أسعار معلنة وواضحة قبل التأكيد</p>
                        </div>
                    </div>

                    {{-- Card 3: Working Schedule & Queue --}}
                    <div class="clinora-card rounded-3xl p-6 sm:p-7 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 dark:bg-sky-500/15 border border-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">مواعيد العمل والدور</p>
                            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1">
                                {{ count($enabledHours) > 0 ? count($enabledHours).' أيام عمل أسبوعياً' : 'حجز إلكتروني برقم الدور' }}
                            </p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">تأكيد فوري ورابط متابعة مباشر</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. ABOUT DOCTOR & CLINIC SECTION (Displays doctor_bio and about_us so neither is ever lost) --}}
        @if ($hasAboutSection)
            <section id="about" class="py-12 md:py-16">
                <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                    <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
                        <span class="text-xs sm:text-sm font-extrabold tracking-widest uppercase text-teal-600 dark:text-teal-400">نبذة تعريفية</span>
                        <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white">عن الطبيب والعيادة</h2>
                    </div>

                    <div class="grid grid-cols-1 {{ (!empty($clinic->doctor_bio) && (!empty($clinic->about_us) || !empty($clinic->description))) ? 'lg:grid-cols-2' : '' }} gap-6">
                        @if (! empty($clinic->doctor_bio))
                            <div class="clinora-card rounded-3xl p-7 sm:p-9 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-11 h-11 rounded-2xl bg-teal-500/10 dark:bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">نبذة عن الطبيب</h3>
                                        @if ($clinic->doctor_title)
                                            <p class="text-xs font-bold text-teal-600 dark:text-teal-400">{{ $clinic->doctor_title }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $clinic->doctor_bio }}</p>
                            </div>
                        @endif

                        @if (! empty($clinic->about_us) || ! empty($clinic->description))
                            <div class="clinora-card rounded-3xl p-7 sm:p-9 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-11 h-11 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">من نحن</h3>
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $clinic->name }}</p>
                                    </div>
                                </div>
                                <p class="text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $clinic->about_us ?? $clinic->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- 4. 3-STEP BOOKING PROCESS (Matches Reference Image 1 "Simple 3-Step Process") --}}
        <section id="how-to-book" class="py-12 md:py-16">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white">احجز موعدك في 3 خطوات بسيطة</h2>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium">
                        اختر الخدمة المطلوبة، سجّل بياناتك الأساسية، وتابع رقم دورك مباشرة من هاتفك.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="clinora-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-11 h-11 rounded-2xl bg-[#34548A] text-white font-black text-base flex items-center justify-center shadow-sm">01</span>
                            <span class="text-xs font-extrabold text-teal-600 dark:text-teal-400 bg-teal-500/10 px-3 py-1 rounded-full">الخطوة الأولى</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">اختر نوع الكشف أو الخدمة</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            استعرض قائمة الخدمات الطبية المتاحة مع سعر كل خدمة بوضوح قبل إرسال طلب الحجز.
                        </p>
                    </div>

                    <div class="clinora-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-11 h-11 rounded-2xl bg-[#F59E0B] text-white font-black text-base flex items-center justify-center shadow-sm">02</span>
                            <span class="text-xs font-extrabold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full">الخطوة الثانية</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">أدخل بياناتك وتاريخ الزيارة</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            أدخل اسمك الكامل ورقم هاتفك والتاريخ المفضل للزيارة دون الحاجة لإنشاء حساب أو كلمة مرور.
                        </p>
                    </div>

                    <div class="clinora-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-11 h-11 rounded-2xl bg-teal-600 text-white font-black text-base flex items-center justify-center shadow-sm">03</span>
                            <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full">الخطوة الثالثة</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">استلم رقم الدور وتابع دورك</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            تظهر لك فوراً بطاقة تأكيد الحجز برقم الدور ورابط خاص لمتابعة عدد المنتظرين أمامك لحظة بلحظة.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. SERVICES & VISIT TYPES SECTION --}}
        <section class="py-12 md:py-16" id="services">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="mb-10 space-y-2 text-center max-w-2xl mx-auto">
                    <span class="text-xs sm:text-sm font-extrabold tracking-widest uppercase text-teal-600 dark:text-teal-400">خدماتنا</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white">اختر الخدمة واحجز بسهولة</h2>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium">
                        اضغط على «احجز الآن» أمام الخدمة المطلوبة ليتم تحديدها تلقائياً في نموذج الحجز.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($visitTypes as $visitType)
                        <div class="clinora-card group flex flex-col justify-between rounded-3xl p-6 sm:p-7 transition-all duration-200 hover:-translate-y-1">
                            <div>
                                <div class="mb-5 flex items-center justify-between gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 text-lg font-black text-indigo-600 dark:text-indigo-400">
                                        {{ $loop->iteration }}
                                    </div>
                                    @if (! empty($visitType->duration))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-white/5 px-3 py-1 text-xs font-bold text-slate-600 dark:text-slate-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ $visitType->duration }} دقيقة</span>
                                        </span>
                                    @endif
                                </div>
                                <h3 class="mb-2 text-xl font-black text-slate-900 dark:text-white">{{ $visitType->name }}</h3>
                                @if ($visitType->description)
                                    <p class="mb-6 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $visitType->description }}</p>
                                @else
                                    <p class="mb-6 text-sm leading-relaxed text-slate-500 dark:text-slate-400">حجز مباشر مع إصدار رقم دور إلكتروني في قائمة انتظار العيادة.</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-200/80 dark:border-white/[0.07] pt-4 mt-2">
                                <div>
                                    <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500">قيمة الخدمة</span>
                                    <span class="text-xl font-black text-teal-600 dark:text-teal-400">{{ number_format((float) $visitType->price, 0) }} ج.م</span>
                                </div>
                                <a href="#contact" onclick="selectVisitType({{ $visitType->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#34548A] hover:bg-[#2B4675] px-4 py-2.5 text-xs sm:text-sm font-extrabold text-white shadow-sm transition-all">
                                    <span>احجز الآن</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full clinora-card rounded-3xl p-8 text-center">
                            <p class="text-base font-extrabold text-amber-600 dark:text-amber-400">لا توجد خدمات مضافة حالياً.</p>
                            @if ($clinic->phone)
                                <a href="tel:{{ $clinic->phone }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-[#34548A] px-5 py-2.5 text-sm font-bold text-white dir-ltr">
                                    {{ $clinic->phone }}
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- 6. WORKING HOURS SECTION --}}
        <section class="py-12 md:py-16" id="hours">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="grid items-start gap-8 lg:grid-cols-12">
                    <div class="clinora-card rounded-3xl p-7 sm:p-8 space-y-4 lg:col-span-4">
                        <span class="inline-flex rounded-full bg-teal-500/10 px-3 py-1 text-xs font-extrabold text-teal-600 dark:text-teal-400">المواعيد</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">ساعات العمل</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            مواعيد استقبال المرضى في العيادة خلال أيام الأسبوع. يمكنك الحجز إلكترونياً في أي وقت.
                        </p>
                        @if ($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}"
                                class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#34548A] hover:bg-[#2B4675] px-5 py-3.5 text-sm font-extrabold text-white shadow-sm transition-all">
                                <span>اتصل للحجز:</span>
                                <span class="dir-ltr">{{ $clinic->phone }}</span>
                            </a>
                        @endif
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:col-span-8 lg:grid-cols-3">
                        @forelse ($enabledHours as $key => $data)
                            <div class="clinora-card rounded-2xl p-5 flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-base font-black text-slate-900 dark:text-white">{{ $days[$key] ?? $key }}</span>
                                    <span class="text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full">متاح</span>
                                </div>
                                <div class="space-y-1.5 text-sm font-bold text-slate-700 dark:text-slate-300 dir-ltr text-right">
                                    @forelse ($data['resolved_periods'] ?? [] as $p)
                                        <div class="rounded-xl bg-slate-100/80 dark:bg-white/[0.04] px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 inline-block ml-1">
                                            {{ $p['open'] }} – {{ $p['close'] }}
                                        </div>
                                    @empty
                                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">مفتوح حسب مواعيد العيادة</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="clinora-card rounded-3xl p-8 text-center sm:col-span-2 lg:col-span-3">
                                <span class="text-sm sm:text-base font-bold text-slate-600 dark:text-slate-300">تواصل مع العيادة لمعرفة أوقات العمل.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- 7. ONLINE BOOKING FORM SECTION (Matches Reference Image 4 Clean Card + High-Contrast Inputs) --}}
        <section class="py-12 md:py-16" id="contact">
            <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                <div class="clinora-card grid overflow-hidden rounded-3xl lg:grid-cols-12">
                    {{-- Right Info Panel --}}
                    <div class="flex flex-col justify-between space-y-8 p-8 text-white sm:p-10 lg:col-span-5 lg:p-12"
                        style="background: linear-gradient(155deg, #34548A 0%, #1E293B 100%);">
                        <div class="space-y-5">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-3.5 py-1 text-xs font-extrabold text-amber-300">
                                حجز إلكتروني فوري
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-black leading-tight">
                                احجز موعدك<br>واحصل على رقم الدور
                            </h2>
                            <p class="text-sm sm:text-base leading-relaxed text-slate-200">
                                أرسل طلب الحجز الآن لتستلم فوراً رقم دورك في العيادة مع رابط مباشر لمتابعة حركة الدور من هاتفك.
                            </p>

                            <div class="space-y-3.5 pt-3 border-t border-white/15">
                                @if ($clinic->phone)
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-300">هاتف العيادة</p>
                                            <a href="tel:{{ $clinic->phone }}" class="text-sm sm:text-base font-extrabold text-white hover:underline dir-ltr">{{ $clinic->phone }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if ($clinic->address)
                                    <div class="flex items-start gap-3">
                                        <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-teal-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-300">العنوان</p>
                                            <p class="text-sm font-semibold leading-6 text-white">{{ $clinic->address }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if (! empty($socialLinks))
                                <div class="pt-3 border-t border-white/15">
                                    <p class="text-xs font-bold text-slate-300 mb-2.5">صفحات العيادة الرسمية</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($socialLinks as $platform => $url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition">
                                                <span>{{ match($platform) { 'facebook' => 'فيسبوك', 'instagram' => 'انستجرام', 'twitter' => 'إكس / تويتر', 'linkedin' => 'لينكد إن', default => $platform } }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <p class="border-t border-white/15 pt-5 text-xs leading-relaxed text-slate-300">
                            بعد إرسال الطلب ستنتقل مباشرة إلى صفحة التأكيد التي تحتوي على رقم الدور ورابط المتابعة المباشر.
                        </p>
                    </div>

                    {{-- Left Form Panel --}}
                    <div class="p-7 sm:p-10 lg:col-span-7 lg:p-12">
                        <div class="mb-6">
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">بيانات طلب الحجز</h3>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">الحقول المسبوقة بعلامة (*) مطلوبة لتأكيد الموعد.</p>
                        </div>

                        @if ($errors->any())
                            <div role="alert" class="mb-6 space-y-1.5 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm font-bold text-rose-700 dark:text-rose-300">
                                @foreach ($errors->all() as $err)
                                    <p>• {{ $err }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if ($visitTypes->isEmpty())
                            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-6 text-sm font-bold text-amber-800 dark:text-amber-300">
                                الحجز الإلكتروني غير متاح حالياً.
                                @if ($clinic->phone)
                                    <a href="tel:{{ $clinic->phone }}" class="mt-2 block underline dir-ltr">{{ $clinic->phone }}</a>
                                @endif
                            </div>
                        @else
                            <form id="public-booking-form" method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}#contact" class="space-y-5">
                                @csrf
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="patient_name" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                            الاسم الكامل <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required
                                            placeholder="الاسم الثلاثي للمريض"
                                            class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold" />
                                    </div>

                                    <div>
                                        <label for="patient_phone" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                            رقم الهاتف <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="tel" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" required dir="ltr"
                                            placeholder="010xxxxxxxx"
                                            class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold text-right" />
                                    </div>
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="patient_email" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                            البريد الإلكتروني <span class="text-xs font-normal text-slate-400">(اختياري)</span>
                                        </label>
                                        <input type="email" id="patient_email" name="patient_email" value="{{ old('patient_email') }}" dir="ltr"
                                            placeholder="patient@example.com"
                                            class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold text-right" />
                                    </div>

                                    <div>
                                        <label for="preferred_date" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                            تاريخ الموعد المفضل <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="date" id="preferred_date" name="preferred_date"
                                            value="{{ old('preferred_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                            class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold" />
                                    </div>
                                </div>

                                <div>
                                    <label for="visit_type_select" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        الخدمة المطلوبة <span class="text-rose-500">*</span>
                                    </label>
                                    <select id="visit_type_select" name="visit_type_id" required
                                        class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold">
                                        <option value="">اختر الخدمة المطلوبة *</option>
                                        @foreach ($visitTypes as $vt)
                                            <option value="{{ $vt->id }}" @selected(old('visit_type_id') == $vt->id)>
                                                {{ $vt->name }} — {{ number_format((float) $vt->price, 0) }} ج.م
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-extrabold text-slate-800 dark:text-slate-200 mb-2">
                                        ملاحظات إضافية للعيادة <span class="text-xs font-normal text-slate-400">(اختياري)</span>
                                    </label>
                                    <textarea id="notes" name="notes" rows="3"
                                        placeholder="أي أعراض أو تفاصيل تود إبلاغ الطبيب بها..."
                                        class="clinora-input w-full rounded-xl px-4 py-3.5 text-sm font-semibold">{{ old('notes') }}</textarea>
                                </div>

                                <button type="submit" id="booking-submit-btn"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#F59E0B] hover:bg-[#D97706] py-4 px-6 text-base font-extrabold text-white shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-60 cursor-pointer">
                                    <span data-label>تأكيد الحجز والحصول على رقم الدور</span>
                                    <span data-loading class="hidden">جاري الإرسال…</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- 8. LOCATION & MAP SECTION --}}
        @if ($hasLocationSection)
            <section class="pb-16 pt-4" id="location">
                <div class="mx-auto max-w-[1320px] px-4 sm:px-6 md:px-10">
                    <div class="clinora-card rounded-3xl p-6 sm:p-8">
                        <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <span class="text-xs font-extrabold text-teal-600 dark:text-teal-400">العنوان والخريطة</span>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-0.5">موقع العيادة</h3>
                                @if ($clinic->address)
                                    <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-400">{{ $clinic->address }}</p>
                                @endif
                            </div>
                            @php
                                $mapsUrl = ($clinic->latitude && $clinic->longitude)
                                    ? 'https://www.google.com/maps?q='.$clinic->latitude.','.$clinic->longitude
                                    : 'https://www.google.com/maps?q='.urlencode($clinic->address ?? $clinic->name);
                            @endphp
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#34548A] hover:bg-[#2B4675] px-5 py-2.5 text-sm font-extrabold text-white shadow-sm transition-all">
                                <span>فتح في خرائط Google</span>
                            </a>
                        </div>
                        <div class="h-72 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 sm:h-80">
                            <iframe title="موقع العيادة" class="h-full w-full border-0"
                                src="https://maps.google.com/maps?q={{ $clinic->latitude && $clinic->longitude ? $clinic->latitude.','.$clinic->longitude : urlencode($clinic->address ?? $clinic->name) }}&output=embed"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200/80 dark:border-white/[0.07] bg-white dark:bg-[#0B0E17] py-10 transition-colors duration-200">
        <div class="mx-auto flex max-w-[1320px] flex-col gap-6 px-4 sm:px-6 md:flex-row md:items-center md:justify-between md:px-10">
            <div>
                <p class="text-lg font-black text-slate-900 dark:text-white">{{ $clinic->name }}</p>
                @if ($clinic->doctor_title)
                    <p class="mt-1 text-sm font-semibold text-teal-600 dark:text-teal-400">{{ $clinic->doctor_title }}</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-5 text-sm font-bold text-slate-600 dark:text-slate-400">
                <a href="#hero" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الرئيسية</a>
                @if ($hasAboutSection)
                    <a href="#about" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">عن العيادة</a>
                @endif
                <a href="#services" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الخدمات</a>
                <a href="#hours" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">المواعيد</a>
                <a href="#contact" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">الحجز</a>
            </div>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">© {{ date('Y') }} {{ $clinic->name }}</p>
        </div>
    </footer>

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

            syncThemeButtons();

            document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    window.toggleTheme();
                });
            });
        })();

        const hamburger = document.getElementById('hamburger');
        const drawer = document.getElementById('nav-drawer');
        const iconMenu = hamburger?.querySelector('.icon-menu');
        const iconClose = hamburger?.querySelector('.icon-close');

        hamburger?.addEventListener('click', () => {
            const open = drawer.classList.contains('hidden');
            drawer.classList.toggle('hidden', !open);
            drawer.classList.toggle('flex', open);
            hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
            iconMenu?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
            document.body.style.overflow = open ? 'hidden' : '';
        });

        drawer?.querySelectorAll('a').forEach((a) => {
            a.addEventListener('click', () => {
                drawer.classList.add('hidden');
                drawer.classList.remove('flex');
                hamburger?.setAttribute('aria-expanded', 'false');
                iconMenu?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });

        function selectVisitType(id) {
            const select = document.getElementById('visit_type_select');
            if (select) {
                select.value = id;
                select.focus();
            }
        }

        window.addEventListener('scroll', () => {
            document.getElementById('navbar')?.classList.toggle('shadow-md', window.scrollY > 24);
        }, { passive: true });

        document.getElementById('public-booking-form')?.addEventListener('submit', () => {
            const btn = document.getElementById('booking-submit-btn');
            if (!btn) return;
            btn.disabled = true;
            btn.querySelector('[data-label]')?.classList.add('hidden');
            btn.querySelector('[data-loading]')?.classList.remove('hidden');
        });
    </script>
</body>
</html>

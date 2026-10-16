<!DOCTYPE html>
<html dir="rtl" lang="ar" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $clinic->name }}{{ $clinic->doctor_title ? ' | '.$clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->hero_subtitle ?? $clinic->name, 160) }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    @php
        $primaryColor = $clinic->theme_color ?: '#005c55';
        $doctorImage = ! empty($clinic->doctor_image)
            ? (str_starts_with($clinic->doctor_image, 'http')
                ? $clinic->doctor_image
                : Storage::url($clinic->doctor_image))
            : null;
        $visitTypes = $clinic->visitTypes ?? collect();
        $displayName = $clinic->name;
        $days = [
            'saturday' => 'السبت', 'sunday' => 'الأحد', 'monday' => 'الإثنين',
            'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة',
        ];
        $enabledHours = collect($clinic->working_hours ?? [])
            ->filter(fn ($data) => is_array($data) && ! empty($data['enabled']))
            ->all();
    @endphp
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --clinic-primary: {{ $primaryColor }};
            --color-primary: {{ $primaryColor }};
        }
        body { font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.45);
        }
        .floating-label-input:focus-within label,
        .floating-label-input input:not(:placeholder-shown) + label,
        .floating-label-input textarea:not(:placeholder-shown) + label {
            transform: translateY(-1.5rem) scale(0.85);
            color: var(--clinic-primary);
        }
    </style>
</head>
<body class="bg-background text-on-surface overflow-x-hidden max-w-[100vw]">
    <header id="navbar"
        class="fixed top-0 inset-x-0 z-50 h-20 border-b border-slate-200/70 bg-white/90 backdrop-blur-md transition-shadow">
        <div class="mx-auto flex h-full max-w-6xl items-center justify-between gap-3 px-4 md:px-container-padding-desktop">
            <a href="#hero" class="flex min-w-0 items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary text-lg font-black text-white shadow-sm">
                    {{ mb_substr($clinic->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <h1 class="truncate text-lg font-extrabold leading-tight text-primary" title="{{ $displayName }}">{{ $displayName }}</h1>
                    @if ($clinic->doctor_title)
                        <span class="block truncate text-xs font-semibold text-on-surface-variant">{{ $clinic->doctor_title }}</span>
                    @endif
                </div>
            </a>
            <nav class="hidden items-center gap-5 lg:flex">
                <a class="text-sm font-semibold text-primary" href="#hero">الرئيسية</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary" href="#services">الخدمات</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary" href="#hours">المواعيد</a>
                <a class="text-sm font-semibold text-on-surface-variant hover:text-primary" href="#contact">الحجز</a>
            </nav>
            <div class="flex shrink-0 items-center gap-2">
                <a href="#contact"
                    class="hidden rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-95 sm:inline-flex">
                    احجز موعداً
                </a>
                <button id="hamburger" type="button" aria-label="القائمة"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline/20 bg-surface text-on-surface lg:hidden">
                    <span class="icon-menu text-xl leading-none">☰</span>
                    <span class="icon-close hidden text-xl leading-none">✕</span>
                </button>
            </div>
        </div>
    </header>

    <div id="nav-drawer"
        class="fixed inset-x-0 top-20 z-40 hidden max-h-[calc(100vh-5rem)] flex-col space-y-3 overflow-y-auto border-b border-outline/20 bg-surface px-5 py-5 shadow-xl lg:hidden">
        <a href="#hero" class="block border-b border-outline/10 pb-3 text-base font-bold">الرئيسية</a>
        <a href="#services" class="block border-b border-outline/10 pb-3 text-base font-bold">الخدمات</a>
        <a href="#hours" class="block border-b border-outline/10 pb-3 text-base font-bold">ساعات العمل</a>
        <a href="#contact" class="mt-1 block rounded-xl bg-primary py-3 text-center font-bold text-white">احجز موعدك الآن</a>
    </div>

    <main class="pt-20">
        {{-- Hero --}}
        <section id="hero" class="relative overflow-hidden py-14 md:py-20">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,color-mix(in_srgb,var(--clinic-primary)_14%,transparent),transparent_55%),linear-gradient(180deg,#f7f9fb_0%,#ffffff_100%)]"></div>
            <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-4 md:px-container-padding-desktop lg:grid-cols-2 lg:gap-14">
                <div class="space-y-5">
                    @if ($clinic->doctor_title)
                        <p class="inline-flex rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-bold text-primary">
                            {{ $clinic->doctor_title }}
                        </p>
                    @endif
                    <h2 class="text-3xl font-extrabold leading-[1.2] tracking-tight text-on-surface sm:text-5xl">
                        @if ($clinic->hero_title)
                            {!! nl2br(e($clinic->hero_title)) !!}
                        @else
                            {{ $clinic->name }}
                        @endif
                    </h2>
                    @if ($clinic->hero_subtitle || $clinic->doctor_bio || $clinic->about_us)
                        <p class="max-w-xl text-base font-medium leading-7 text-on-surface-variant">
                            {{ $clinic->hero_subtitle ?? ($clinic->doctor_bio ?? Str::limit($clinic->about_us, 180)) }}
                        </p>
                    @endif
                    <div class="flex flex-wrap gap-3 pt-1">
                        <a href="#contact"
                            class="inline-flex items-center rounded-xl bg-primary px-7 py-3.5 text-base font-bold text-white shadow-md hover:opacity-95">
                            احجز موعدك الآن
                        </a>
                        @if ($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-outline/20 bg-white px-7 py-3.5 text-base font-bold text-on-surface shadow-sm hover:bg-slate-50">
                                <span class="dir-ltr">{{ $clinic->phone }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] bg-primary/10 blur-2xl"></div>
                    <div class="overflow-hidden rounded-[1.75rem] border border-white/70 bg-white shadow-xl ring-1 ring-black/5">
                        @if ($doctorImage)
                            <img alt="{{ $clinic->name }}" class="aspect-[4/5] w-full object-cover"
                                src="{{ $doctorImage }}" width="560" height="700" loading="eager" decoding="async" />
                        @else
                            <div class="flex aspect-[4/5] w-full items-center justify-center bg-gradient-to-br from-primary to-primary-container">
                                <span class="text-7xl font-black text-white/90">{{ mb_substr($clinic->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Services --}}
        <section class="bg-surface-variant/25 py-stack-lg" id="services">
            <div class="mx-auto max-w-6xl px-4 md:px-container-padding-desktop">
                <div class="mb-10 space-y-2 text-center">
                    <h3 class="text-sm font-bold tracking-widest text-primary">خدماتنا</h3>
                    <h2 class="text-3xl font-extrabold md:text-4xl">اختر الخدمة واحجز بسهولة</h2>
                </div>
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($visitTypes as $visitType)
                        <div class="group flex flex-col justify-between rounded-3xl border border-white/50 bg-white/90 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div>
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-lg font-black text-primary">
                                    {{ $loop->iteration }}
                                </div>
                                <h4 class="mb-2 text-xl font-bold text-on-surface">{{ $visitType->name }}</h4>
                                @if ($visitType->description)
                                    <p class="mb-5 text-sm leading-relaxed text-on-surface-variant">{{ $visitType->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between border-t border-outline/10 pt-4">
                                <span class="text-lg font-extrabold text-primary">{{ number_format((float) $visitType->price, 0) }} ج.م</span>
                                <a href="#contact" onclick="selectVisitType({{ $visitType->id }})"
                                    class="text-sm font-bold text-primary hover:underline">احجز الآن</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center text-sm font-bold text-amber-800">
                            لا توجد خدمات مضافة حالياً.
                            @if ($clinic->phone)
                                <a href="tel:{{ $clinic->phone }}" class="mt-2 block underline dir-ltr">{{ $clinic->phone }}</a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Hours --}}
        <section class="border-y border-outline/10 py-14" id="hours">
            <div class="mx-auto max-w-6xl px-4 md:px-container-padding-desktop">
                <div class="grid items-start gap-8 lg:grid-cols-12">
                    <div class="space-y-3 lg:col-span-4">
                        <span class="text-sm font-bold tracking-widest text-primary">المواعيد</span>
                        <h2 class="text-3xl font-extrabold">ساعات العمل</h2>
                        @if ($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}"
                                class="mt-2 inline-flex rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm hover:opacity-95">
                                اتصل للحجز: <span class="ms-2 dir-ltr">{{ $clinic->phone }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:col-span-8 lg:grid-cols-3">
                        @forelse ($enabledHours as $key => $data)
                            <div class="rounded-2xl border border-outline/10 bg-white p-4 shadow-sm">
                                <span class="mb-1 block text-base font-extrabold text-primary">{{ $days[$key] ?? $key }}</span>
                                <span class="text-sm font-semibold text-on-surface dir-ltr">
                                    @foreach ($data['periods'] ?? [] as $p)
                                        {{ $p['open'] }} – {{ $p['close'] }}@if (! $loop->last)، @endif
                                    @endforeach
                                </span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-outline/10 bg-white p-6 text-center sm:col-span-2 lg:col-span-3">
                                <span class="text-sm font-bold text-on-surface-variant">تواصل مع العيادة لمعرفة أوقات العمل.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Booking --}}
        <section class="py-stack-lg" id="contact">
            <div class="mx-auto max-w-6xl px-4 md:px-container-padding-desktop">
                <div class="grid overflow-hidden rounded-3xl border border-outline/10 bg-white shadow-xl lg:grid-cols-12">
                    <div class="flex flex-col justify-between space-y-8 bg-primary p-8 text-white sm:p-10 lg:col-span-5 lg:p-12">
                        <div class="space-y-4">
                            <h2 class="text-3xl font-extrabold leading-tight md:text-4xl">احجز موعدك<br>واحصل على رقم الدور</h2>
                            <p class="text-sm leading-relaxed text-on-primary-container opacity-95 md:text-base">
                                أرسل الطلب وستصلك متابعة مباشرة لحالة دورك في العيادة.
                            </p>
                            <div class="space-y-3 pt-2">
                                @if ($clinic->phone)
                                    <p class="text-base font-semibold dir-ltr">{{ $clinic->phone }}</p>
                                @endif
                                @if ($clinic->address)
                                    <p class="text-sm font-semibold leading-6">{{ $clinic->address }}</p>
                                @endif
                            </div>
                        </div>
                        <p class="border-t border-white/20 pt-6 text-xs leading-relaxed text-on-primary-container">
                            بعد الإرسال ستظهر صفحة التأكيد برقم الدور ورابط المتابعة.
                        </p>
                    </div>
                    <div class="bg-slate-50/80 p-8 sm:p-10 lg:col-span-7 lg:p-12">
                        @if ($errors->any())
                            <div class="mb-5 space-y-1 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800">
                                @foreach ($errors->all() as $err)
                                    <p>• {{ $err }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if ($visitTypes->isEmpty())
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                                الحجز الإلكتروني غير متاح حالياً.
                                @if ($clinic->phone)
                                    <a href="tel:{{ $clinic->phone }}" class="mt-2 block underline dir-ltr">{{ $clinic->phone }}</a>
                                @endif
                            </div>
                        @else
                            <form id="public-booking-form" method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}#contact" class="space-y-5">
                                @csrf
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="relative floating-label-input">
                                        <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required placeholder=" "
                                            class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary" />
                                        <label for="patient_name" class="pointer-events-none absolute right-4 top-4 text-sm text-on-surface-variant transition-all duration-300">الاسم الكامل *</label>
                                    </div>
                                    <div class="relative floating-label-input">
                                        <input type="tel" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" required placeholder=" "
                                            class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary" />
                                        <label for="patient_phone" class="pointer-events-none absolute right-4 top-4 text-sm text-on-surface-variant transition-all duration-300">رقم الهاتف *</label>
                                    </div>
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="relative floating-label-input">
                                        <input type="email" id="patient_email" name="patient_email" value="{{ old('patient_email') }}" placeholder=" "
                                            class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary" />
                                        <label for="patient_email" class="pointer-events-none absolute right-4 top-4 text-sm text-on-surface-variant transition-all duration-300">البريد (اختياري)</label>
                                    </div>
                                    <div>
                                        <input type="date" id="preferred_date" name="preferred_date"
                                            value="{{ old('preferred_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                            class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary" />
                                    </div>
                                </div>
                                <select id="visit_type_select" name="visit_type_id" required
                                    class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary">
                                    <option value="">اختر الخدمة المطلوبة *</option>
                                    @foreach ($visitTypes as $vt)
                                        <option value="{{ $vt->id }}" @selected(old('visit_type_id') == $vt->id)>
                                            {{ $vt->name }} — {{ number_format((float) $vt->price, 0) }} ج.م
                                        </option>
                                    @endforeach
                                </select>
                                <div class="relative floating-label-input">
                                    <textarea id="notes" name="notes" rows="3" placeholder=" "
                                        class="w-full rounded-xl border border-outline/20 bg-white px-4 py-4 text-sm font-semibold text-on-surface focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                                    <label for="notes" class="pointer-events-none absolute right-4 top-4 text-sm text-on-surface-variant transition-all duration-300">ملاحظات (اختياري)</label>
                                </div>
                                <button type="submit" id="booking-submit-btn"
                                    class="flex w-full items-center justify-center rounded-2xl bg-primary py-4 text-base font-extrabold text-white shadow-md hover:bg-primary-container disabled:opacity-60">
                                    <span data-label>تأكيد الحجز والحصول على رقم الدور</span>
                                    <span data-loading class="hidden">جاري الإرسال…</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if ($clinic->address || ($clinic->latitude && $clinic->longitude))
            <section class="pb-16" id="location">
                <div class="mx-auto max-w-6xl px-4 md:px-container-padding-desktop">
                    <div class="rounded-3xl border border-outline/10 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h3 class="text-xl font-extrabold text-on-surface">موقع العيادة</h3>
                                @if ($clinic->address)
                                    <p class="mt-1 text-sm text-on-surface-variant">{{ $clinic->address }}</p>
                                @endif
                            </div>
                            @php
                                $mapsUrl = ($clinic->latitude && $clinic->longitude)
                                    ? 'https://www.google.com/maps?q='.$clinic->latitude.','.$clinic->longitude
                                    : 'https://www.google.com/maps?q='.urlencode($clinic->address ?? $clinic->name);
                            @endphp
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                                class="rounded-xl border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/10">
                                فتح في خرائط Google
                            </a>
                        </div>
                        <div class="h-72 overflow-hidden rounded-2xl border border-outline/10 sm:h-80">
                            <iframe title="موقع العيادة" class="h-full w-full border-0"
                                src="https://maps.google.com/maps?q={{ $clinic->latitude && $clinic->longitude ? $clinic->latitude.','.$clinic->longitude : urlencode($clinic->address ?? $clinic->name) }}&output=embed"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-outline/15 bg-white py-10">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 md:flex-row md:items-center md:justify-between md:px-container-padding-desktop">
            <div>
                <p class="text-lg font-extrabold text-primary">{{ $clinic->name }}</p>
                @if ($clinic->doctor_title)
                    <p class="mt-1 text-sm font-medium text-on-surface-variant">{{ $clinic->doctor_title }}</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-4 text-sm font-semibold text-on-surface-variant">
                <a href="#services" class="hover:text-primary">الخدمات</a>
                <a href="#hours" class="hover:text-primary">المواعيد</a>
                <a href="#contact" class="hover:text-primary">الحجز</a>
            </div>
            <p class="text-xs font-semibold text-on-surface-variant">© {{ date('Y') }} {{ $clinic->name }}</p>
        </div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const drawer = document.getElementById('nav-drawer');
        const iconMenu = hamburger?.querySelector('.icon-menu');
        const iconClose = hamburger?.querySelector('.icon-close');

        hamburger?.addEventListener('click', () => {
            const open = drawer.classList.contains('hidden');
            drawer.classList.toggle('hidden', !open);
            drawer.classList.toggle('flex', open);
            iconMenu?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
            document.body.style.overflow = open ? 'hidden' : '';
        });

        drawer?.querySelectorAll('a').forEach((a) => {
            a.addEventListener('click', () => {
                drawer.classList.add('hidden');
                drawer.classList.remove('flex');
                iconMenu?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });

        function selectVisitType(id) {
            const select = document.getElementById('visit_type_select');
            if (select) select.value = id;
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

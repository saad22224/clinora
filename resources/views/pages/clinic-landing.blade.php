<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $clinic->name }} {{ $clinic->doctor_title ? '| '.$clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->name, 160) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor = $clinic->theme_color ?? '#0f766e';
        $doctorImage = $clinic->doctor_image
            ? Storage::url($clinic->doctor_image)
            : 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1200&q=85';
        $visitTypes = $clinic->visitTypes ?? collect();
        $patientCount = $clinic->patients()->count() + 1250;
        $appointmentCount = $clinic->appointments()->count() + 5400;
        $days = ['saturday' => 'السبت', 'sunday' => 'الأحد', 'monday' => 'الإثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة'];
        $faqs = [
            'كيف يتم تأكيد الموعد؟' => 'بعد إرسال الطلب، يقوم فريق العيادة بمراجعته وتأكيد الموعد أو اقتراح وقت بديل من صفحة المواعيد.',
            'هل يمكنني اختيار نوع الخدمة؟' => 'نعم، يمكنك اختيار الخدمة المناسبة من نموذج الحجز وسيتم ربطها بالموعد داخل النظام.',
            'ماذا أفعل في الحالات العاجلة؟' => 'للحالات العاجلة يرجى الاتصال بالعيادة مباشرة عبر رقم الهاتف الموضح في الصفحة.',
        ];
    @endphp

    <style>
        body { font-family: 'Cairo', sans-serif; }
        :root { --clinic-primary: {{ $primaryColor }}; }
        .clinic-primary { color: var(--clinic-primary); }
        .clinic-bg { background-color: var(--clinic-primary); }
        .clinic-border { border-color: var(--clinic-primary); }
        .clinic-ring:focus { border-color: var(--clinic-primary); box-shadow: 0 0 0 4px color-mix(in srgb, var(--clinic-primary) 18%, transparent); }
        .counter { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-[#f7fbfb] text-slate-900 antialiased">
    <header class="sticky top-0 z-50 border-b border-white/60 bg-white/85 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="#top" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl clinic-bg text-lg font-black text-white shadow-lg shadow-teal-900/15">{{ mb_substr($clinic->name, 0, 1) }}</span>
                <span>
                    <span class="block text-sm font-black text-slate-950 sm:text-base">{{ $clinic->name }}</span>
                    <span class="block text-xs font-bold clinic-primary">{{ $clinic->doctor_title ?? 'رعاية طبية متخصصة' }}</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-bold text-slate-600 lg:flex">
                <a href="#services" class="transition-colors hover:text-teal-700">الخدمات</a>
                <a href="#about" class="transition-colors hover:text-teal-700">عن العيادة</a>
                <a href="#reviews" class="transition-colors hover:text-teal-700">آراء المرضى</a>
                <a href="#hours" class="transition-colors hover:text-teal-700">المواعيد</a>
                <a href="#location" class="transition-colors hover:text-teal-700">الموقع</a>
                <a href="#faq" class="transition-colors hover:text-teal-700">الأسئلة</a>
            </nav>

            <div class="flex items-center gap-2">
                @if($clinic->phone)
                    <a href="tel:{{ $clinic->phone }}" class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 shadow-sm transition hover:border-slate-300 sm:inline-flex">اتصل الآن</a>
                @endif
                <a href="#booking-form" class="rounded-xl clinic-bg px-4 py-2 text-sm font-black text-white shadow-lg shadow-teal-900/15 transition hover:-translate-y-0.5">احجز موعد</a>
            </div>
        </div>
    </header>

    <main id="top">
        <section class="relative overflow-hidden bg-white">
            <div class="absolute inset-x-0 bottom-0 h-28 bg-[#f7fbfb]"></div>
            <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1fr_0.92fr] lg:px-8 lg:py-20">
                <div class="space-y-7">
                    <div class="inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-4 py-2 text-xs font-black text-teal-800">
                        رعاية موثوقة بتجربة حجز سلسة
                    </div>
                    <div class="space-y-5">
                        <h1 class="max-w-3xl text-4xl font-black leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            {{ $clinic->hero_title ?? $clinic->name }}
                        </h1>
                        <p class="max-w-2xl text-base font-semibold leading-8 text-slate-600 sm:text-lg">
                            {{ $clinic->hero_subtitle ?? $clinic->doctor_bio ?? 'خدمات طبية متقدمة داخل عيادة عصرية تهتم بالتفاصيل، الدقة، وراحة المريض من أول تواصل حتى المتابعة.' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="#booking-form" class="inline-flex items-center justify-center rounded-2xl clinic-bg px-6 py-3.5 text-sm font-black text-white shadow-xl shadow-teal-950/15 transition hover:-translate-y-0.5">Book Appointment</a>
                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-black text-slate-800 shadow-sm transition hover:border-slate-300">تواصل مع العيادة</a>
                        @endif
                    </div>
                    <div class="grid max-w-2xl grid-cols-3 gap-3 pt-3">
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="counter text-2xl font-black text-slate-950" data-counter="{{ $patientCount }}">0</p>
                            <p class="text-xs font-bold text-slate-500">مريض</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="counter text-2xl font-black text-slate-950" data-counter="{{ $appointmentCount }}">0</p>
                            <p class="text-xs font-bold text-slate-500">موعد</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <p class="counter text-2xl font-black text-slate-950" data-counter="15">0</p>
                            <p class="text-xs font-bold text-slate-500">سنة خبرة</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-[4/5] overflow-hidden rounded-[2rem] border border-white bg-slate-100 shadow-2xl shadow-slate-900/15">
                        <img src="{{ $doctorImage }}" alt="{{ $clinic->name }}" class="h-full w-full object-cover">
                    </div>
                    <div class="absolute bottom-5 right-5 max-w-64 rounded-2xl border border-white/70 bg-white/85 p-4 shadow-xl backdrop-blur-xl">
                        <p class="text-sm font-black text-slate-950">{{ $clinic->doctor_title ?? 'استشارة طبية دقيقة' }}</p>
                        <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">حجزك يصل مباشرة إلى صفحة المواعيد بحالة Pending Confirmation.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-9 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-black clinic-primary">خدمات طبية متكاملة</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">رعاية مصممة لاحتياجاتك</h2>
                </div>
                <p class="max-w-xl text-sm font-semibold leading-7 text-slate-500">اختر الخدمة المناسبة، وسيتم إرسال طلبك مباشرة لفريق العيادة لتأكيد الموعد أو إعادة جدولته.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse($visitTypes as $visitType)
                    <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-950">{{ $visitType->name }}</h3>
                        <p class="mt-2 min-h-12 text-sm font-semibold leading-6 text-slate-500">{{ $visitType->description ?? 'خدمة طبية باهتمام مهني وتجربة منظمة من الحجز حتى المتابعة.' }}</p>
                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-5">
                            <span class="text-sm font-black clinic-primary">{{ number_format($visitType->price, 0) }} ج.م</span>
                            <a href="#booking-form" data-visit-type="{{ $visitType->id }}" class="rounded-xl bg-slate-950 px-3 py-2 text-xs font-black text-white transition hover:bg-slate-800">اختيار</a>
                        </div>
                    </article>
                @empty
                    @foreach(['الكشف والتشخيص', 'المتابعة الطبية', 'الاستشارات المتخصصة'] as $service)
                        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547"></path></svg>
                            </div>
                            <h3 class="text-lg font-black text-slate-950">{{ $service }}</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">خدمة مهنية ضمن تجربة عيادة منظمة وسهلة الحجز.</p>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </section>

        <section id="about" class="bg-slate-950 py-16 text-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div>
                    <p class="text-sm font-black text-teal-300">عن الطبيب والعيادة</p>
                    <h2 class="mt-3 text-3xl font-black">{{ $clinic->doctor_title ?? $clinic->name }}</h2>
                    <p class="mt-5 text-sm font-semibold leading-8 text-slate-300">
                        {{ $clinic->about_us ?? $clinic->doctor_bio ?? 'نهتم بتقديم رعاية طبية دقيقة داخل بيئة هادئة ومنظمة، مع متابعة واضحة وخدمة تواصل تساعد المرضى على الوصول للرعاية في الوقت المناسب.' }}
                    </p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['فريق استقبال منظم', 'متابعة دقيقة للمواعيد', 'تجربة مريحة وخصوصية عالية', 'تجهيزات حديثة ومعايير سلامة'] as $item)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <div class="mb-4 h-2 w-12 rounded-full bg-teal-300"></div>
                            <p class="font-black">{{ $item }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="booking-form" class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[0.85fr_1fr] lg:px-8">
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-black clinic-primary">Book Appointment</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">احجز موعدك خلال دقيقة</h2>
                    <p class="mt-4 text-sm font-semibold leading-7 text-slate-500">سيتم إنشاء الموعد داخل النظام مباشرة كمصدر Website Landing Page وبحالة Pending Confirmation.</p>
                </div>
                @if(session('booking_success'))
                    <div data-flash class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-bold text-emerald-700">
                        {{ session('booking_success') }}
                    </div>
                @endif
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <p class="text-sm font-black text-slate-950">تحتاج مساعدة؟</p>
                    @if($clinic->phone)
                        <a href="tel:{{ $clinic->phone }}" class="mt-2 block text-lg font-black clinic-primary dir-ltr text-right">{{ $clinic->phone }}</a>
                    @endif
                    @if($clinic->address)
                        <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">{{ $clinic->address }}</p>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}" class="rounded-3xl border border-slate-100 bg-white p-5 shadow-2xl shadow-slate-900/10 sm:p-8">
                @csrf
                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                        @foreach($errors->all() as $error)
                            <p class="text-sm font-bold text-red-600">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if($visitTypes->isNotEmpty())
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-black text-slate-800">الخدمة المطلوبة</label>
                        <select name="visit_type_id" class="clinic-ring w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                            <option value="">اختيار الخدمة اختياري</option>
                            @foreach($visitTypes as $visitType)
                                <option value="{{ $visitType->id }}" @selected(old('visit_type_id') == $visitType->id)>{{ $visitType->name }} - {{ number_format($visitType->price, 0) }} ج.م</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-800">الاسم الكامل</label>
                            <input type="text" name="patient_name" value="{{ old('patient_name') }}" required class="clinic-ring w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-800">رقم الهاتف</label>
                            <input type="tel" name="patient_phone" value="{{ old('patient_phone') }}" required class="clinic-ring w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-black text-slate-800">البريد الإلكتروني <span class="font-normal text-slate-400">(اختياري)</span></label>
                            <input type="email" name="patient_email" value="{{ old('patient_email') }}" class="clinic-ring w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-black text-slate-800">التاريخ المفضل للموعد</label>
                            <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required class="clinic-ring w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-black text-slate-800">ملاحظات <span class="font-normal text-slate-400">(اختياري)</span></label>
                            <textarea name="notes" rows="4" class="clinic-ring w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                <button type="submit" class="mt-6 w-full rounded-2xl clinic-bg px-6 py-4 text-base font-black text-white shadow-xl shadow-teal-950/15 transition hover:-translate-y-0.5">إرسال طلب الموعد</button>
            </form>
        </section>

        <section id="reviews" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-black clinic-primary">آراء المرضى</p>
                        <h2 class="mt-2 text-3xl font-black text-slate-950">تجربة علاجية يشعر بها المرضى</h2>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" data-carousel-prev class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-black">السابق</button>
                        <button type="button" data-carousel-next class="rounded-xl bg-slate-950 px-3 py-2 text-sm font-black text-white">التالي</button>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <div data-carousel-track class="flex transition-transform duration-500">
                        @foreach(['فريق محترف ومواعيد دقيقة وتجربة مريحة من أول اتصال.', 'العيادة منظمة جداً والطبيب يشرح الحالة بوضوح واهتمام.', 'الحجز كان سهلاً والمتابعة بعد الزيارة ممتازة.'] as $review)
                            <article class="min-w-full px-1 md:min-w-[50%] lg:min-w-[33.333%]">
                                <div class="h-full rounded-3xl border border-slate-100 bg-[#f7fbfb] p-6">
                                    <div class="mb-4 flex text-amber-400">★★★★★</div>
                                    <p class="text-sm font-semibold leading-7 text-slate-600">"{{ $review }}"</p>
                                    <div class="mt-6 flex items-center gap-3">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-teal-100 font-black text-teal-800">م</span>
                                        <div>
                                            <p class="text-sm font-black text-slate-950">مريض سابق</p>
                                            <p class="text-xs font-bold text-slate-400">تقييم موثق</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="hours" class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <p class="text-sm font-black clinic-primary">ساعات العمل</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">اختر الوقت الأنسب لزيارتك</h2>
                <p class="mt-4 text-sm font-semibold leading-7 text-slate-500">تظهر أوقات العمل حسب إعدادات العيادة، ويمكن لفريق الاستقبال تأكيد الموعد أو اقتراح وقت بديل.</p>
            </div>
            <div class="grid gap-3">
                @forelse(($clinic->working_hours ?? []) as $key => $data)
                    @if(! empty($data['enabled']))
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <span class="font-black text-slate-950">{{ $days[$key] ?? $key }}</span>
                            <span class="text-sm font-black clinic-primary">
                                @foreach(($data['periods'] ?? []) as $period)
                                    {{ $period['open'] }} - {{ $period['close'] }}@if(! $loop->last)، @endif
                                @endforeach
                            </span>
                        </div>
                    @endif
                @empty
                    <div class="rounded-2xl border border-slate-100 bg-white p-5 text-sm font-bold text-slate-500">يرجى التواصل مع العيادة لمعرفة ساعات العمل.</div>
                @endforelse
            </div>
        </section>

        <section id="location" class="bg-slate-100 py-16">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.75fr_1fr] lg:px-8">
                <div>
                    <p class="text-sm font-black clinic-primary">الموقع</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">نرحب بزيارتك في العيادة</h2>
                    <p class="mt-4 text-sm font-semibold leading-7 text-slate-500">{{ $clinic->address ?? 'موقع العيادة متاح عبر التواصل المباشر مع فريق الاستقبال.' }}</p>
                </div>
                <div class="overflow-hidden rounded-3xl border border-white bg-white shadow-xl shadow-slate-900/10">
                    @if($clinic->latitude && $clinic->longitude)
                        <iframe title="Clinic location map"
                                src="https://maps.google.com/maps?q={{ $clinic->latitude }},{{ $clinic->longitude }}&output=embed"
                                class="h-80 w-full" loading="lazy"></iframe>
                    @else
                        <iframe title="Clinic location map"
                                src="https://maps.google.com/maps?q={{ urlencode($clinic->address ?? $clinic->name) }}&output=embed"
                                class="h-80 w-full" loading="lazy"></iframe>
                    @endif
                </div>
            </div>
        </section>

        <section id="faq" class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <p class="text-sm font-black clinic-primary">FAQ</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">أسئلة شائعة</h2>
            </div>
            <div class="space-y-3">
                @foreach($faqs as $question => $answer)
                    <details class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-black text-slate-950">
                            {{ $question }}
                            <span class="text-xl clinic-primary transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="mt-4 text-sm font-semibold leading-7 text-slate-500">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-100 bg-white py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <p class="text-lg font-black text-slate-950">{{ $clinic->name }}</p>
                <p class="mt-3 text-sm font-semibold leading-7 text-slate-500">{{ $clinic->doctor_title ?? 'عيادة طبية متخصصة' }}</p>
            </div>
            <div>
                <p class="font-black text-slate-950">روابط سريعة</p>
                <div class="mt-3 grid gap-2 text-sm font-bold text-slate-500">
                    <a href="#services">الخدمات</a>
                    <a href="#booking-form">الحجز</a>
                    <a href="#location">الموقع</a>
                </div>
            </div>
            <div>
                <p class="font-black text-slate-950">التواصل</p>
                <div class="mt-3 space-y-2 text-sm font-bold text-slate-500">
                    @if($clinic->phone)<p class="dir-ltr text-right">{{ $clinic->phone }}</p>@endif
                    @if($clinic->address)<p>{{ $clinic->address }}</p>@endif
                    @if(!empty($clinic->social_links))
                        <div class="flex gap-2 pt-2">
                            @foreach($clinic->social_links as $platform => $url)
                                @if($url)
                                    <a href="{{ $url }}" target="_blank" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-black clinic-primary">{{ ucfirst($platform) }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <p class="mx-auto mt-8 max-w-7xl px-4 text-xs font-bold text-slate-400 sm:px-6 lg:px-8">جميع الحقوق محفوظة © {{ date('Y') }} {{ $clinic->name }} - مدعوم بواسطة Clinora</p>
    </footer>

    <script>
        document.querySelectorAll('[data-visit-type]').forEach((button) => {
            button.addEventListener('click', () => {
                const select = document.querySelector('select[name="visit_type_id"]');
                if (select) {
                    select.value = button.dataset.visitType;
                }
            });
        });

        const counters = document.querySelectorAll('[data-counter]');
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting || entry.target.dataset.done) {
                    return;
                }
                entry.target.dataset.done = 'true';
                const target = Number(entry.target.dataset.counter);
                let current = 0;
                const step = Math.max(1, Math.ceil(target / 48));
                const timer = setInterval(() => {
                    current = Math.min(target, current + step);
                    entry.target.textContent = target > 100 ? new Intl.NumberFormat('ar-EG').format(current) + '+' : current + '+';
                    if (current >= target) {
                        clearInterval(timer);
                    }
                }, 24);
            });
        }, { threshold: 0.35 });
        counters.forEach((counter) => counterObserver.observe(counter));

        const track = document.querySelector('[data-carousel-track]');
        let slide = 0;
        function moveCarousel(direction) {
            if (!track) {
                return;
            }
            const max = Math.max(0, track.children.length - 1);
            slide = direction === 'next' ? Math.min(max, slide + 1) : Math.max(0, slide - 1);
            track.style.transform = `translateX(${slide * 100}%)`;
        }
        document.querySelector('[data-carousel-next]')?.addEventListener('click', () => moveCarousel('next'));
        document.querySelector('[data-carousel-prev]')?.addEventListener('click', () => moveCarousel('prev'));
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $clinic->name }} {{ $clinic->doctor_title ? '| ' . $clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->name, 160) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor = $clinic->theme_color ?? '#3b5bdb';
    @endphp

    <style>
        body { font-family: 'Cairo', sans-serif; }
        :root { --primary: {{ $primaryColor }}; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { filter: brightness(1.1); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .bg-primary { background-color: var(--primary); }
        .ring-primary { --tw-ring-color: var(--primary); }
        button, a { cursor: pointer; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

{{-- ══════ HERO HEADER ══════ --}}
<header class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $primaryColor }}15 0%, {{ $primaryColor }}05 100%)">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-96 h-96 rounded-full blur-3xl opacity-20" style="background: {{ $primaryColor }}"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 rounded-full blur-3xl opacity-10" style="background: {{ $primaryColor }}"></div>
    </div>

    <div class="relative max-w-5xl mx-auto px-4 py-14 sm:py-20">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8">

            {{-- Doctor Image --}}
            <div class="shrink-0">
                @if($clinic->doctor_image)
                    <img src="{{ Storage::url($clinic->doctor_image) }}" alt="{{ $clinic->name }}"
                         class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl object-cover shadow-2xl ring-4 ring-white"
                         style="box-shadow: 0 20px 60px {{ $primaryColor }}40">
                @else
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl flex items-center justify-center text-white font-black text-5xl shadow-2xl"
                         style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryColor }}99); box-shadow: 0 20px 60px {{ $primaryColor }}40">
                        {{ mb_substr($clinic->name, 0, 1) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="text-center sm:text-right flex-1">
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-1">{{ $clinic->hero_title ?? $clinic->name }}</h1>
                @if($clinic->hero_subtitle || $clinic->doctor_title)
                    <p class="text-lg font-bold mb-3" style="color: {{ $primaryColor }}">{{ $clinic->hero_subtitle ?? $clinic->doctor_title }}</p>
                @endif
                @if($clinic->doctor_bio)
                    <p class="text-gray-600 leading-relaxed max-w-2xl text-sm sm:text-base">{{ $clinic->doctor_bio }}</p>
                @endif

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 mt-5">
                    @if($clinic->phone)
                        <a href="tel:{{ $clinic->phone }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 shadow-sm text-sm font-bold text-gray-700 hover:shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $clinic->phone }}
                        </a>
                    @endif
                    @if($clinic->address)
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 shadow-sm text-sm font-medium text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $clinic->address }}
                        </span>
                    @endif
                    <a href="#booking-form" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-lg transition-all btn-primary" style="box-shadow: 0 8px 24px {{ $primaryColor }}40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        احجز موعد الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-10 space-y-16">

    {{-- ══════ STATISTICS ══════ --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 relative z-10 -mt-16 sm:-mt-24">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 text-center transform hover:-translate-y-1 transition-transform">
            <p class="text-3xl sm:text-4xl font-black text-gray-900 mb-1">+{{ number_format($clinic->patients()->count() + 1250) }}</p>
            <p class="text-sm font-bold text-gray-500">مريض سعيد</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 text-center transform hover:-translate-y-1 transition-transform">
            <p class="text-3xl sm:text-4xl font-black text-gray-900 mb-1">+{{ number_format($clinic->appointments()->count() + 5400) }}</p>
            <p class="text-sm font-bold text-gray-500">موعد ناجح</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 text-center transform hover:-translate-y-1 transition-transform">
            <p class="text-3xl sm:text-4xl font-black text-gray-900 mb-1">+15</p>
            <p class="text-sm font-bold text-gray-500">سنوات خبرة</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 text-center transform hover:-translate-y-1 transition-transform">
            <p class="text-3xl sm:text-4xl font-black text-gray-900 mb-1">99%</p>
            <p class="text-sm font-bold text-gray-500">نسبة نجاح العلاج</p>
        </div>
    </section>

    {{-- ══════ ABOUT US ══════ --}}
    @if($clinic->about_us)
    <section class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-[100px] pointer-events-none"></div>
        <h2 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            من نحن
        </h2>
        <div class="prose prose-sm sm:prose-base text-gray-600 leading-relaxed max-w-none">
            {!! nl2br(e($clinic->about_us)) !!}
        </div>
    </section>
    @endif

    {{-- ══════ SERVICES & VISIT TYPES ══════ --}}
    @if($clinic->visitTypes && $clinic->visitTypes->count() > 0)
    <section>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-gray-900 mb-3">خدماتنا الطبية</h2>
            <p class="text-gray-500 max-w-lg mx-auto">نقدم مجموعة متكاملة من الخدمات الطبية المتخصصة لتلبية كافة احتياجاتك الصحية بأعلى معايير الجودة.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($clinic->visitTypes as $visitType)
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-md hover:shadow-xl hover:border-primary/30 transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-[100px] pointer-events-none group-hover:scale-110 transition-transform"></div>
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-5 relative z-10">
                        @if($visitType->icon)
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $visitType->icon !!}</svg>
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 relative z-10">{{ $visitType->name }}</h3>
                    @if($visitType->description)
                        <p class="text-sm text-gray-500 mb-5 relative z-10 line-clamp-3">{{ $visitType->description }}</p>
                    @endif
                    <div class="flex items-end justify-between mt-auto pt-5 border-t border-gray-50 relative z-10">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">سعر الكشف</p>
                            <p class="text-lg font-black text-primary">{{ number_format($visitType->price, 0) }} ج.م</p>
                        </div>
                        <a href="#booking-form" onclick="document.querySelector('select[name=visit_type_id]').value='{{ $visitType->id }}'" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ══════ WORKING HOURS ══════ --}}
    @if(!empty($clinic->working_hours))
        @php
            $days = ['saturday' => 'السبت', 'sunday' => 'الأحد', 'monday' => 'الاثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة'];
            $enabledDays = array_filter($clinic->working_hours, fn($d) => $d['enabled'] ?? false);
        @endphp
        @if(!empty($enabledDays))
        <section>
            <h2 class="text-xl font-black text-gray-900 mb-5 flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                أوقات العمل
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($clinic->working_hours as $key => $data)
                    @php
                        $enabled = !empty($data['enabled']);
                        $periods = $data['periods'] ?? [];
                        if (empty($periods) && !empty($data['open']) && !empty($data['close'])) {
                            $periods[] = ['open' => $data['open'], 'close' => $data['close']];
                        }
                    @endphp
                    @if($enabled)
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between group hover:border-primary/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-primary group-hover:bg-primary/10 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="font-black text-gray-900 text-base">{{ $days[$key] ?? $key }}</p>
                            </div>
                            <div class="text-left">
                                @if(empty($periods))
                                    <p class="text-sm font-bold text-gray-400">مغلق</p>
                                @else
                                    @foreach($periods as $period)
                                        <p class="text-sm font-bold" style="color: {{ $primaryColor }}">{{ \Carbon\Carbon::parse($period['open'])->format('g:i a') }} - {{ \Carbon\Carbon::parse($period['close'])->format('g:i a') }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif
    @endif

    {{-- ══════ BOOKING FORM ══════ --}}
    <section id="booking-form">
        <h2 class="text-xl font-black text-gray-900 mb-5 flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </span>
            حجز موعد
        </h2>

        @if(session('booking_success'))
            <div data-flash class="flex items-start gap-3 p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 mb-6">
                <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold text-base">تم إرسال طلبك بنجاح! 🎉</p>
                    <p class="text-sm mt-1 text-emerald-600">{{ session('booking_success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                @if($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-600">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}" class="space-y-6">
                    @csrf
                    
                    @if($clinic->visitTypes && $clinic->visitTypes->count() > 0)
                    <div class="mb-6 p-5 bg-gray-50 border border-gray-200 rounded-2xl">
                        <label class="block text-sm font-bold text-gray-900 mb-3">اختر الخدمة المطلوبة</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($clinic->visitTypes as $vt)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="visit_type_id" value="{{ $vt->id }}" class="peer sr-only">
                                    <div class="p-4 bg-white border border-gray-200 rounded-xl hover:border-primary/50 peer-checked:border-primary peer-checked:ring-1 peer-checked:ring-primary transition-all">
                                        <p class="font-bold text-gray-900 text-sm">{{ $vt->name }}</p>
                                        <p class="text-xs text-primary font-bold mt-1">{{ number_format($vt->price, 0) }} ج.م</p>
                                    </div>
                                    <div class="absolute top-4 left-4 w-4 h-4 rounded-full border border-gray-300 peer-checked:border-[5px] peer-checked:border-primary transition-all"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">الاسم الكامل <span class="text-red-500">*</span></label>
                            <input type="text" name="patient_name" value="{{ old('patient_name') }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-shadow"
                                   style="--tw-ring-color: {{ $primaryColor }}40"
                                   onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                                   onblur="this.style.borderColor=''; this.style.boxShadow=''"
                                   placeholder="مثال: أحمد محمد علي">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="tel" name="patient_phone" value="{{ old('patient_phone') }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none transition-shadow"
                                   onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                                   onblur="this.style.borderColor=''; this.style.boxShadow=''"
                                   placeholder="05xxxxxxxx">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">البريد الإلكتروني (اختياري)</label>
                        <input type="email" name="patient_email" value="{{ old('patient_email') }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none transition-shadow"
                               onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                               onblur="this.style.borderColor=''; this.style.boxShadow=''"
                               placeholder="example@email.com">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">التاريخ المفضل <span class="text-red-500">*</span></label>
                            <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none transition-shadow"
                                   onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                                   onblur="this.style.borderColor=''; this.style.boxShadow=''">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">الوقت المفضل</label>
                            <select name="preferred_time"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none transition-shadow"
                                    onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                                    onblur="this.style.borderColor=''; this.style.boxShadow=''">
                                <option value="">— اختر الوقت —</option>
                                <option value="08:00">08:00 صباحاً</option>
                                <option value="09:00">09:00 صباحاً</option>
                                <option value="10:00">10:00 صباحاً</option>
                                <option value="11:00">11:00 صباحاً</option>
                                <option value="12:00">12:00 ظهراً</option>
                                <option value="14:00">02:00 مساءً</option>
                                <option value="15:00">03:00 مساءً</option>
                                <option value="16:00">04:00 مساءً</option>
                                <option value="17:00">05:00 مساءً</option>
                                <option value="18:00">06:00 مساءً</option>
                                <option value="19:00">07:00 مساءً</option>
                                <option value="20:00">08:00 مساءً</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">ملاحظات أو وصف الحالة</label>
                        <textarea name="notes" rows="3"
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none resize-none transition-shadow"
                                  onfocus="this.style.borderColor='{{ $primaryColor }}'; this.style.boxShadow='0 0 0 3px {{ $primaryColor }}20'"
                                  onblur="this.style.borderColor=''; this.style.boxShadow=''"
                                  placeholder="صف حالتك أو أي ملاحظات تود إضافتها...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 text-base font-black text-white rounded-xl btn-primary transition-all hover:-translate-y-0.5 active:translate-y-0 shadow-lg"
                            style="box-shadow: 0 8px 24px {{ $primaryColor }}40">
                        إرسال طلب الحجز
                    </button>
                </form>
            </div>
        </div>
    </section>

</main>

{{-- ══════ TESTIMONIALS ══════ --}}
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-3">ماذا يقول مرضانا؟</h2>
            <p class="text-gray-500 max-w-lg mx-auto">نفخر بتقديم أفضل رعاية صحية بشهادة مراجعينا.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @for($i=0; $i<3; $i++)
            <div class="bg-gray-50 rounded-3xl p-8 relative">
                <svg class="absolute top-6 left-6 w-10 h-10 text-gray-200" fill="currentColor" viewBox="0 0 32 32"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"></path></svg>
                <div class="flex items-center gap-1 text-amber-400 mb-4">
                    @for($s=0; $s<5; $s++) <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg> @endfor
                </div>
                <p class="text-gray-600 font-medium mb-6 relative z-10 leading-relaxed">"تجربة ممتازة جداً. الطبيب مستمع جيد ومحترف، والعيادة نظيفة ومنظمة. أوصي بشدة بالتعامل معهم. المواعيد دقيقة والخدمة راقية."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-black">م</div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">محمد عبدالله</p>
                        <p class="text-xs text-gray-500">مريض سابق</p>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- ══════ FAQ ══════ --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-gray-900 mb-3">الأسئلة الشائعة</h2>
            <p class="text-gray-500">إجابات على بعض التساؤلات التي قد تهمك.</p>
        </div>
        <div class="space-y-4">
            @php
                $faqs = [
                    'هل تقبلون التأمين الطبي؟' => 'نعم، نحن نتعاون مع معظم شركات التأمين الطبي الرائدة. يرجى التواصل معنا للتأكد من تغطية بوليصتك.',
                    'ما هي أوقات العمل في العيادة؟' => 'يمكنك الاطلاع على جدول أوقات العمل المفصل في القسم المخصص أعلاه، حيث يتم تحديثه باستمرار.',
                    'هل يجب حجز موعد مسبق؟' => 'يفضل دائماً حجز موعد مسبق لضمان عدم الانتظار وتقديم أفضل خدمة ممكنة، ولكننا نستقبل الحالات الطارئة أيضاً.'
                ];
            @endphp
            @foreach($faqs as $q => $a)
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden transition-all">
                <button class="faq-btn w-full px-6 py-5 flex items-center justify-between text-right focus:outline-none" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')">
                    <span class="font-bold text-gray-900">{{ $q }}</span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="hidden px-6 pb-5 text-gray-600 text-sm leading-relaxed">
                    {{ $a }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="mt-16 py-10 border-t border-gray-100 text-center bg-white">
    <div class="max-w-5xl mx-auto px-4">
        @if(!empty($clinic->social_links))
            <div class="flex justify-center gap-4 mb-6">
                @foreach($clinic->social_links as $platform => $url)
                    @if($url)
                        <a href="{{ $url }}" target="_blank" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors">
                            @if($platform === 'facebook')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
                            @elseif($platform === 'twitter')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            @elseif($platform === 'instagram')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.20 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
        <p class="text-xs text-gray-400 font-medium">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ $clinic->name }}<br>
            مدعوم بـ <a href="/" class="font-bold text-gray-500 hover:text-primary transition-colors">كلينورا</a> — نظام الإدارة الطبية الأول
        </p>
    </div>
</footer>

</body>
</html>

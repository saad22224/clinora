<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $clinic->name }}{{ $clinic->doctor_title ? ' | '.$clinic->doctor_title : '' }}</title>
    <meta name="description" content="{{ Str::limit($clinic->doctor_bio ?? $clinic->name, 160) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor   = $clinic->theme_color ?? '#0a5c52';
        $accentColor    = '#c9a84c';
        $doctorImage    = $clinic->doctor_image
            ? Storage::url($clinic->doctor_image)
            : 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1200&q=85';
        $visitTypes     = $clinic->visitTypes ?? collect();
        $patientCount   = $clinic->patients()->count() + 1250;
        $apptCount      = $clinic->appointments()->count() + 5400;
        $days           = ['saturday'=>'السبت','sunday'=>'الأحد','monday'=>'الإثنين','tuesday'=>'الثلاثاء','wednesday'=>'الأربعاء','thursday'=>'الخميس','friday'=>'الجمعة'];
        $faqs = [
            'كيف يتم تأكيد الموعد؟'        => 'بعد إرسال الطلب يراجعه فريق العيادة ويؤكد الموعد أو يقترح وقتاً بديلاً من صفحة المواعيد.',
            'هل يمكنني اختيار نوع الخدمة؟' => 'نعم، اختر الخدمة من نموذج الحجز وستُربط بالموعد داخل النظام تلقائياً.',
            'ماذا أفعل في الحالات العاجلة؟' => 'للحالات العاجلة اتصل بالعيادة مباشرةً عبر رقم الهاتف الموضح في الصفحة.',
        ];
        $clinicInitial  = mb_substr($clinic->name, 0, 1);
    @endphp

    <style>
        /* ─── tokens ─── */
        :root {
            --p:  {{ $primaryColor }};
            --a:  {{ $accentColor }};
            --bg: #f6faf9;
            --surface: #ffffff;
            --txt: #0e1a18;
            --muted: #5a7470;
            --border: #dceae8;
            --radius-card: 20px;
            --radius-btn: 12px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg);
            color: var(--txt);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ─── utilities ─── */
        .cp   { color: var(--p); }
        .ca   { color: var(--a); }
        .bgp  { background: var(--p); }
        .bga  { background: var(--a); }
        .container { width: min(1280px, 100%); margin-inline: auto; padding-inline: clamp(1.25rem, 5vw, 3rem); }

        /* ─── scrollbar ─── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--p); border-radius: 99px; }

        /* ========================================
           NAVBAR
        ======================================== */
        #navbar {
            position: fixed;
            inset-inline: 0;
            top: 0;
            z-index: 100;
            padding: 0;
            transition: background .35s, box-shadow .35s;
        }
        #navbar.scrolled {
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(20px);
            box-shadow: 0 1px 0 var(--border);
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-block: 1rem;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: .7rem;
            text-decoration: none;
            flex-shrink: 0;
        }
        .nav-logo-icon {
            width: 40px; height: 40px;
            background: var(--p);
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            font-weight: 900;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 16px color-mix(in srgb, var(--p) 35%, transparent);
        }
        .nav-logo-name { font-size: .92rem; font-weight: 900; color: var(--txt); line-height: 1.2; }
        .nav-logo-sub  { font-size: .7rem; font-weight: 700; color: var(--p); }

        /* desktop links — hidden on mobile */
        .nav-links {
            display: none;
            gap: 1.8rem;
            list-style: none;
            flex: 1;
            justify-content: center;
        }
        @media(min-width:1024px){ .nav-links { display: flex; } }
        .nav-links a {
            font-size: .82rem;
            font-weight: 700;
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
            white-space: nowrap;
        }
        .nav-links a:hover { color: var(--p); }

        .nav-actions { display: flex; align-items: center; gap: .6rem; }

        /* hamburger button — visible only on mobile */
        .nav-hamburger {
            display: grid;
            place-items: center;
            width: 40px; height: 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color .2s;
        }
        @media(min-width:1024px){ .nav-hamburger { display: none; } }
        .nav-hamburger:hover { border-color: var(--p); }
        .nav-hamburger svg { transition: transform .3s; }
        .nav-hamburger.open svg.icon-menu { display: none; }
        .nav-hamburger svg.icon-close { display: none; }
        .nav-hamburger.open svg.icon-close { display: block; }

        /* mobile drawer */
        .nav-drawer {
            display: none;
            position: fixed;
            inset: 0;
            top: 64px;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(20px);
            z-index: 99;
            padding: 2rem clamp(1.25rem,5vw,3rem);
            flex-direction: column;
            gap: 0;
            border-top: 1px solid var(--border);
            transform: translateY(-8px);
            opacity: 0;
            transition: opacity .25s, transform .25s;
        }
        .nav-drawer.open {
            display: flex;
            transform: translateY(0);
            opacity: 1;
        }
        .nav-drawer a {
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--txt);
            text-decoration: none;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            transition: color .2s;
        }
        .nav-drawer a:hover { color: var(--p); }
        .nav-drawer .drawer-book {
            margin-top: 1.5rem;
            display: block;
            text-align: center;
            background: var(--p);
            color: #fff;
            border-radius: 14px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 900;
            border-bottom: none;
            box-shadow: 0 6px 22px color-mix(in srgb, var(--p) 30%, transparent);
        }

        .btn-ghost {
            display: none;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .6rem 1.3rem;
            background: var(--p);
            color: #fff;
            border: none;
            border-radius: var(--radius-btn);
            font-family: inherit;
            font-size: .84rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 6px 22px color-mix(in srgb, var(--p) 30%, transparent);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px color-mix(in srgb, var(--p) 38%, transparent); }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .7rem 1.6rem;
            border: 2px solid var(--border);
            border-radius: var(--radius-btn);
            font-family: inherit;
            font-size: .9rem;
            font-weight: 800;
            color: var(--txt);
            background: var(--surface);
            text-decoration: none;
            cursor: pointer;
            transition: border-color .2s, transform .2s;
        }
        .btn-outline:hover { border-color: var(--p); color: var(--p); transform: translateY(-2px); }

        /* ========================================
           HERO
        ======================================== */
        #hero {
            min-height: 100svh;
            display: grid;
            grid-template-columns: 1fr;
            overflow: hidden;
            position: relative;
            background: var(--surface);
        }
        @media(min-width:1024px){
            #hero { grid-template-columns: 1fr 1fr; }
        }

        /* left text pane */
        .hero-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 8rem clamp(1.5rem,5vw,5rem) 4rem;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem .9rem;
            background: color-mix(in srgb, var(--p) 10%, transparent);
            border: 1px solid color-mix(in srgb, var(--p) 22%, transparent);
            border-radius: 99px;
            font-size: .75rem;
            font-weight: 800;
            color: var(--p);
            width: fit-content;
            margin-bottom: 1.8rem;
        }
        .hero-badge::before {
            content: '';
            width: 7px; height: 7px;
            background: var(--a);
            border-radius: 50%;
            display: inline-block;
        }

        .hero-title {
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 900;
            line-height: 1.15;
            color: var(--txt);
            margin-bottom: 1.4rem;
        }
        .hero-title em {
            font-style: normal;
            color: var(--p);
            position: relative;
        }
        .hero-title em::after {
            content: '';
            position: absolute;
            bottom: 4px;
            right: 0;
            left: 0;
            height: 3px;
            background: var(--a);
            border-radius: 99px;
            transform-origin: right;
        }

        .hero-sub {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.9;
            color: var(--muted);
            max-width: 42ch;
            margin-bottom: 2.4rem;
        }

        .hero-btns {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem;
            margin-bottom: 3rem;
        }
        .hero-btns .btn-primary { padding: .85rem 2rem; font-size: .95rem; }

        /* stats strip */
        .hero-stats {
            display: flex;
            gap: 1.5rem;
        }
        .stat-item { display: flex; flex-direction: column; gap: .15rem; }
        .stat-num {
            font-size: 1.7rem;
            font-weight: 900;
            color: var(--txt);
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }
        .stat-label { font-size: .72rem; font-weight: 700; color: var(--muted); }
        .stat-divider {
            width: 1px;
            background: var(--border);
            align-self: stretch;
        }

        /* right image pane */
        .hero-image-pane {
            position: relative;
            background: color-mix(in srgb, var(--p) 8%, #fff);
            min-height: 340px;
        }
        @media(min-width:1024px){
            .hero-image-pane {
                min-height: unset;
                /* full height handled by grid */
            }
        }

        /* big watermark letter */
        .hero-watermark {
            position: absolute;
            font-size: 40vw;
            font-weight: 900;
            color: color-mix(in srgb, var(--p) 6%, transparent);
            line-height: 1;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
        }
        @media(min-width:1024px){ .hero-watermark { font-size: 16vw; } }

        .hero-img-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }
        .hero-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            display: block;
        }
        /* gradient fade into background on desktop — seamless blend */
        @media(min-width:1024px){
            .hero-img-wrap::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    to right,
                    rgba(255,255,255,.55) 0%,
                    transparent 30%
                );
                pointer-events: none;
            }
        }
        /* bottom fade on mobile so it blends into next section */
        @media(max-width:1023px){
            .hero-img-wrap::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    to bottom,
                    transparent 55%,
                    var(--bg) 100%
                );
                pointer-events: none;
            }
        }

        /* floating card */
        .hero-card {
            position: absolute;
            bottom: 2.5rem;
            right: 2rem;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,.7);
            border-radius: var(--radius-card);
            padding: 1.1rem 1.4rem;
            box-shadow: 0 20px 50px rgba(0,0,0,.12);
            max-width: 230px;
            z-index: 3;
        }
        .hero-card-dot {
            width: 9px; height: 9px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            margin-left: .45rem;
            box-shadow: 0 0 0 3px rgba(34,197,94,.25);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0%,100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,.1); }
        }
        .hero-card-title  { font-size: .82rem; font-weight: 900; color: var(--txt); display: flex; align-items: center; }
        .hero-card-body   { font-size: .72rem; font-weight: 600; color: var(--muted); margin-top: .4rem; line-height: 1.6; }

        /* scroll indicator */
        .scroll-hint {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
            font-size: .68rem;
            font-weight: 700;
            color: var(--muted);
            z-index: 5;
        }
        @media(min-width:1024px){ .scroll-hint { display: none; } }
        .scroll-hint-line {
            width: 1px; height: 40px;
            background: linear-gradient(to bottom, var(--p), transparent);
            animation: scroll-line 1.8s ease-in-out infinite;
        }
        @keyframes scroll-line {
            0%,100% { opacity: 1; transform: scaleY(1); transform-origin: top; }
            50%      { opacity: .4; transform: scaleY(.5); }
        }

        /* ========================================
           SECTION HEADER
        ======================================== */
        .section-eyebrow {
            font-size: .78rem;
            font-weight: 800;
            color: var(--p);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: .6rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .section-eyebrow::before {
            content: '';
            width: 20px; height: 2px;
            background: var(--a);
            border-radius: 99px;
            display: inline-block;
        }
        .section-title {
            font-size: clamp(1.8rem,3.5vw,2.6rem);
            font-weight: 900;
            line-height: 1.2;
            color: var(--txt);
        }
        .section-sub {
            font-size: .95rem;
            font-weight: 600;
            line-height: 1.85;
            color: var(--muted);
            max-width: 52ch;
            margin-top: .9rem;
        }

        /* ========================================
           SERVICES
        ======================================== */
        #services { padding-block: 6rem; }
        .services-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .services-grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }

        .service-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            padding: 1.8rem;
            transition: transform .3s, box-shadow .3s, border-color .3s;
            position: relative;
            overflow: hidden;
        }
        .service-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, color-mix(in srgb, var(--p) 6%, transparent), transparent 60%);
            opacity: 0;
            transition: opacity .3s;
        }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 20px 50px rgba(10,92,82,.1); border-color: color-mix(in srgb, var(--p) 30%, transparent); }
        .service-card:hover::before { opacity: 1; }

        .service-icon {
            width: 52px; height: 52px;
            background: color-mix(in srgb, var(--p) 10%, transparent);
            border-radius: 14px;
            display: grid;
            place-items: center;
            margin-bottom: 1.3rem;
            color: var(--p);
            transition: background .3s, transform .3s;
        }
        .service-card:hover .service-icon { background: var(--p); color: #fff; transform: scale(1.08) rotate(-3deg); }

        .service-name  { font-size: 1.05rem; font-weight: 900; color: var(--txt); margin-bottom: .5rem; }
        .service-desc  { font-size: .84rem; font-weight: 600; line-height: 1.8; color: var(--muted); min-height: 3.6em; }

        .service-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.4rem;
            padding-top: 1.1rem;
            border-top: 1px solid var(--border);
        }
        .service-price { font-size: 1rem; font-weight: 900; color: var(--p); }
        .service-btn {
            padding: .5rem 1rem;
            background: var(--txt);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-family: inherit;
            font-size: .78rem;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s;
        }
        .service-btn:hover { background: var(--p); }

        /* ========================================
           ABOUT
        ======================================== */
        #about {
            background: var(--txt);
            padding-block: 6rem;
            position: relative;
            overflow: hidden;
        }
        #about::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, color-mix(in srgb, var(--p) 30%, transparent) 0%, transparent 70%);
            top: -200px;
            left: -200px;
            pointer-events: none;
        }
        .about-grid { display: grid; gap: 3.5rem; }
        @media(min-width:1024px){ .about-grid { grid-template-columns: 1fr 1fr; align-items: center; } }

        .about-eyebrow { color: color-mix(in srgb, var(--p) 70%, #5fffee); }
        .about-eyebrow::before { background: var(--a); }
        .about-title { color: #fff; }
        .about-body { font-size: .93rem; font-weight: 600; line-height: 1.95; color: #94b4b0; margin-top: 1.1rem; }

        .about-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .feat-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            padding: 1.4rem;
            transition: background .3s, border-color .3s;
        }
        .feat-card:hover { background: rgba(255,255,255,.07); border-color: rgba(255,255,255,.15); }
        .feat-bar { width: 30px; height: 3px; background: var(--a); border-radius: 99px; margin-bottom: 1rem; }
        .feat-text { font-size: .88rem; font-weight: 800; color: #fff; }

        /* ========================================
           BOOKING SECTION
        ======================================== */
        #booking-form { padding-block: 6rem; }
        .booking-grid { display: grid; gap: 3rem; }
        @media(min-width:1024px){ .booking-grid { grid-template-columns: 1fr 1.4fr; align-items: start; } }

        .booking-sidebar { display: flex; flex-direction: column; gap: 1.2rem; }

        .contact-box {
            background: var(--p);
            border-radius: var(--radius-card);
            padding: 2rem;
            color: #fff;
        }
        .contact-box-label { font-size: .8rem; font-weight: 700; opacity: .75; margin-bottom: .4rem; }
        .contact-box-phone { font-size: 1.3rem; font-weight: 900; direction: ltr; text-align: right; text-decoration: none; color: #fff; display: block; }
        .contact-box-addr  { font-size: .82rem; font-weight: 600; opacity: .8; margin-top: .6rem; line-height: 1.6; }

        .info-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            padding: 1.5rem;
        }
        .info-box-title { font-size: .88rem; font-weight: 900; color: var(--txt); margin-bottom: .6rem; }
        .info-box-body  { font-size: .8rem; font-weight: 600; line-height: 1.75; color: var(--muted); }

        /* form */
        .booking-form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            box-shadow: 0 25px 60px rgba(10,92,82,.07);
        }
        .form-row { display: grid; gap: 1rem; }
        @media(min-width:640px){ .form-row { grid-template-columns: 1fr 1fr; } }

        .form-group { display: flex; flex-direction: column; gap: .5rem; }
        .form-group.full { grid-column: 1 / -1; }

        .form-label { font-size: .82rem; font-weight: 800; color: var(--txt); }
        .form-label span { font-weight: 600; color: var(--muted); }

        .form-input, .form-select, .form-textarea {
            font-family: inherit;
            font-size: .88rem;
            font-weight: 600;
            color: var(--txt);
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: .85rem 1rem;
            outline: none;
            width: 100%;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--p);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--p) 15%, transparent);
            background: #fff;
        }
        .form-textarea { resize: vertical; min-height: 110px; }

        .form-submit {
            width: 100%;
            margin-top: 1.4rem;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 900;
            background: var(--p);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 8px 28px color-mix(in srgb, var(--p) 35%, transparent);
            transition: transform .2s, box-shadow .2s;
        }
        .form-submit:hover { transform: translateY(-2px); box-shadow: 0 14px 38px color-mix(in srgb, var(--p) 42%, transparent); }

        .flash-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            font-size: .85rem;
            font-weight: 700;
            color: #166534;
            margin-bottom: 1.2rem;
        }
        .flash-errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.2rem;
        }
        .flash-errors p { font-size: .82rem; font-weight: 700; color: #dc2626; line-height: 1.8; }

        /* ========================================
           REVIEWS
        ======================================== */
        #reviews { background: var(--surface); padding-block: 6rem; }
        .reviews-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }
        .carousel-btns { display: flex; gap: .6rem; }
        .carousel-btn {
            width: 44px; height: 44px;
            border-radius: 12px;
            border: 1.5px solid var(--border);
            background: transparent;
            cursor: pointer;
            display: grid;
            place-items: center;
            color: var(--txt);
            font-size: 1rem;
            transition: background .2s, border-color .2s, color .2s;
        }
        .carousel-btn:hover, .carousel-btn.active { background: var(--p); border-color: var(--p); color: #fff; }

        .carousel-overflow { overflow: hidden; }
        .carousel-track {
            display: flex;
            transition: transform .55s cubic-bezier(.4,0,.2,1);
        }
        .review-slide { min-width: 100%; padding: .25rem; }
        @media(min-width:768px){ .review-slide { min-width: 50%; } }
        @media(min-width:1200px){ .review-slide { min-width: 33.333%; } }

        .review-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            padding: 1.8rem;
            height: 100%;
        }
        .review-stars { color: var(--a); font-size: 1rem; letter-spacing: .1em; margin-bottom: 1rem; }
        .review-body { font-size: .88rem; font-weight: 600; line-height: 1.85; color: var(--muted); }
        .review-author {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--border);
        }
        .review-avatar {
            width: 44px; height: 44px;
            background: color-mix(in srgb, var(--p) 12%, transparent);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 900;
            font-size: .9rem;
            color: var(--p);
            flex-shrink: 0;
        }
        .review-name { font-size: .85rem; font-weight: 900; color: var(--txt); }
        .review-tag  { font-size: .72rem; font-weight: 700; color: var(--muted); }

        /* ========================================
           HOURS
        ======================================== */
        #hours { padding-block: 6rem; }
        .hours-grid { display: grid; gap: 3rem; }
        @media(min-width:1024px){ .hours-grid { grid-template-columns: 1fr 1fr; align-items: start; } }

        .hours-list { display: flex; flex-direction: column; gap: .75rem; margin-top: 2rem; }
        .hours-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.3rem;
            transition: border-color .2s;
        }
        .hours-item:hover { border-color: var(--p); }
        .hours-day  { font-size: .9rem; font-weight: 900; color: var(--txt); }
        .hours-time { font-size: .85rem; font-weight: 800; color: var(--p); }

        /* ========================================
           LOCATION
        ======================================== */
        #location { background: #eef5f4; padding-block: 6rem; }
        .location-grid { display: grid; gap: 2.5rem; }
        @media(min-width:1024px){ .location-grid { grid-template-columns: 1fr 1.5fr; align-items: start; } }

        .map-wrap {
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(0,0,0,.07);
        }
        .map-wrap iframe { display: block; width: 100%; height: 360px; border: 0; }

        /* ========================================
           FAQ
        ======================================== */
        #faq { padding-block: 6rem; }
        .faq-inner { max-width: 780px; margin-inline: auto; }
        .faq-list { display: flex; flex-direction: column; gap: .75rem; margin-top: 2.5rem; }

        details.faq-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: border-color .2s;
        }
        details.faq-item[open], details.faq-item:hover { border-color: var(--p); }

        details.faq-item summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.5rem;
            font-size: .92rem;
            font-weight: 900;
            color: var(--txt);
            cursor: pointer;
            list-style: none;
            user-select: none;
        }
        details.faq-item summary::-webkit-details-marker { display: none; }
        .faq-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--p) 10%, transparent);
            color: var(--p);
            display: grid;
            place-items: center;
            font-size: 1.1rem;
            font-weight: 400;
            flex-shrink: 0;
            transition: background .2s, transform .3s;
        }
        details.faq-item[open] .faq-icon { background: var(--p); color: #fff; transform: rotate(45deg); }

        .faq-body {
            padding: 0 1.5rem 1.4rem;
            font-size: .86rem;
            font-weight: 600;
            line-height: 1.9;
            color: var(--muted);
        }

        /* ========================================
           FOOTER
        ======================================== */
        footer {
            background: var(--txt);
            padding-block: 4rem 2rem;
        }
        .footer-grid {
            display: grid;
            gap: 2.5rem;
        }
        @media(min-width:768px){ .footer-grid { grid-template-columns: 1.5fr 1fr 1fr; } }

        .footer-brand { }
        .footer-name { font-size: 1.1rem; font-weight: 900; color: #fff; }
        .footer-tagline { font-size: .82rem; font-weight: 600; color: #94b4b0; margin-top: .5rem; line-height: 1.7; }

        .footer-col-title { font-size: .82rem; font-weight: 900; color: #fff; margin-bottom: .9rem; letter-spacing: .04em; text-transform: uppercase; }
        .footer-links { display: flex; flex-direction: column; gap: .5rem; }
        .footer-links a { font-size: .84rem; font-weight: 600; color: #94b4b0; text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: #fff; }

        .footer-contact p { font-size: .84rem; font-weight: 600; color: #94b4b0; line-height: 1.8; }
        .footer-contact .phone { direction: ltr; text-align: right; font-weight: 800; color: #fff; font-size: .9rem; }

        .social-row { display: flex; gap: .6rem; margin-top: .9rem; flex-wrap: wrap; }
        .social-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.05);
            display: grid;
            place-items: center;
            color: #94b4b0;
            text-decoration: none;
            transition: border-color .2s, color .2s, background .2s, transform .2s;
            flex-shrink: 0;
        }
        .social-btn:hover { border-color: var(--a); color: var(--a); background: rgba(255,255,255,.08); transform: translateY(-2px); }
        .social-btn svg { width: 18px; height: 18px; fill: currentColor; display: block; }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,.06);
            font-size: .76rem;
            font-weight: 600;
            color: rgba(255,255,255,.3);
            text-align: center;
        }
        .footer-bottom span { color: var(--a); }

        /* ========================================
           GSAP INITIAL STATES (set via JS)
        ======================================== */
        @media(prefers-reduced-motion: reduce) {
            * { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

    {{-- ════════ NAVBAR ════════ --}}
    <header id="navbar">
        <div class="container">
            <nav class="nav-inner">
                <a href="#hero" class="nav-logo">
                    <div class="nav-logo-icon">{{ $clinicInitial }}</div>
                    <div>
                        <div class="nav-logo-name">{{ $clinic->name }}</div>
                        <div class="nav-logo-sub">{{ $clinic->doctor_title ?? 'رعاية طبية متخصصة' }}</div>
                    </div>
                </a>

                {{-- desktop links --}}
                <ul class="nav-links">
                    <li><a href="#services">الخدمات</a></li>
                    <li><a href="#about">عن العيادة</a></li>
                    <li><a href="#hours">المواعيد</a></li>
                    <li><a href="#location">الموقع</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="#booking-form" class="btn-primary">احجز موعدك</a>
                    <button class="nav-hamburger" id="hamburger" aria-label="القائمة" aria-expanded="false">
                        <svg class="icon-menu" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    {{-- mobile drawer --}}
    <div class="nav-drawer" id="nav-drawer">
        <a href="#services"     class="drawer-link">الخدمات</a>
        <a href="#about"        class="drawer-link">عن العيادة</a>
        <a href="#reviews"      class="drawer-link">آراء المرضى</a>
        <a href="#hours"        class="drawer-link">ساعات العمل</a>
        <a href="#location"     class="drawer-link">الموقع</a>
        <a href="#faq"          class="drawer-link">الأسئلة الشائعة</a>
        <a href="#booking-form" class="drawer-book">احجز موعدك الآن ←</a>
    </div>

    <main>

        {{-- ════════ HERO ════════ --}}
        <section id="hero">
            <div class="hero-text">
                <div class="hero-badge" id="hero-badge">حجز مباشر · تأكيد فوري</div>

                <h1 class="hero-title" id="hero-title">
                    {!! nl2br(e($clinic->hero_title ?? $clinic->name)) !!}
                    @if($clinic->doctor_title)
                        <br><em>{{ $clinic->doctor_title }}</em>
                    @endif
                </h1>

                <p class="hero-sub" id="hero-sub">
                    {{ $clinic->hero_subtitle ?? $clinic->doctor_bio ?? 'خدمات طبية متقدمة داخل بيئة عصرية تهتم بالدقة وراحة المريض، من أول تواصل حتى المتابعة.' }}
                </p>

                <div class="hero-btns" id="hero-btns">
                    <a href="#booking-form" class="btn-primary">
                        احجز موعدك الآن
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    @if($clinic->phone)
                        <a href="tel:{{ $clinic->phone }}" class="btn-outline">تواصل مع العيادة</a>
                    @endif
                </div>

                <div class="hero-stats" id="hero-stats">
                    <div class="stat-item">
                        <span class="stat-num" data-counter="{{ $patientCount }}">0</span>
                        <span class="stat-label">مريض</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-num" data-counter="{{ $apptCount }}">0</span>
                        <span class="stat-label">موعد محجوز</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-num" data-counter="15">0</span>
                        <span class="stat-label">سنة خبرة</span>
                    </div>
                </div>
            </div>

            <div class="hero-image-pane" id="hero-image">
                <div class="hero-watermark" aria-hidden="true">{{ $clinicInitial }}</div>
                <div class="hero-img-wrap">
                    <img src="{{ $doctorImage }}" alt="{{ $clinic->name }}" loading="eager">
                </div>
                <div class="hero-card" id="hero-card">
                    <div class="hero-card-title">
                        <span class="hero-card-dot"></span>
                        نظام الحجز يعمل
                    </div>
                    <div class="hero-card-body">حجزك يصل مباشرة للعيادة بحالة Pending Confirmation.</div>
                </div>
            </div>

            <div class="scroll-hint">
                <span>اكتشف</span>
                <div class="scroll-hint-line"></div>
            </div>
        </section>

        {{-- ════════ SERVICES ════════ --}}
        <section id="services">
            <div class="container">
                <div class="services-head">
                    <div>
                        <div class="section-eyebrow">خدمات طبية متكاملة</div>
                        <h2 class="section-title">رعاية مصممة لاحتياجاتك</h2>
                    </div>
                    <p class="section-sub" style="max-width:38ch">اختر الخدمة واحجز موعدك، سيصلك تأكيد من فريق العيادة.</p>
                </div>

                <div class="services-grid">
                    @forelse($visitTypes as $visitType)
                        <article class="service-card gs-card">
                            <div class="service-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="service-name">{{ $visitType->name }}</h3>
                            <p class="service-desc">{{ $visitType->description ?? 'خدمة طبية باهتمام مهني وتجربة منظمة من الحجز حتى المتابعة.' }}</p>
                            <div class="service-footer">
                                <span class="service-price">{{ number_format($visitType->price, 0) }} ج.م</span>
                                <a href="#booking-form" data-visit-type="{{ $visitType->id }}" class="service-btn">اختيار الخدمة</a>
                            </div>
                        </article>
                    @empty
                        @foreach(['الكشف والتشخيص', 'المتابعة الطبية', 'الاستشارات المتخصصة'] as $service)
                            <article class="service-card gs-card">
                                <div class="service-icon">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <h3 class="service-name">{{ $service }}</h3>
                                <p class="service-desc">خدمة مهنية ضمن تجربة عيادة منظمة وسهلة الحجز.</p>
                            </article>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ════════ ABOUT ════════ --}}
        <section id="about">
            <div class="container">
                <div class="about-grid">
                    <div>
                        <div class="section-eyebrow about-eyebrow">عن الطبيب والعيادة</div>
                        <h2 class="section-title about-title">{{ $clinic->doctor_title ?? $clinic->name }}</h2>
                        <p class="about-body">{{ $clinic->about_us ?? $clinic->doctor_bio ?? 'نهتم بتقديم رعاية طبية دقيقة داخل بيئة هادئة ومنظمة، مع متابعة واضحة وخدمة تواصل تساعد المرضى على الوصول للرعاية في الوقت المناسب.' }}</p>
                    </div>
                    <div class="about-features">
                        @foreach(['فريق استقبال منظم', 'متابعة دقيقة للمواعيد', 'تجربة مريحة وخصوصية عالية', 'تجهيزات حديثة'] as $feat)
                            <div class="feat-card gs-card">
                                <div class="feat-bar"></div>
                                <div class="feat-text">{{ $feat }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════ BOOKING ════════ --}}
        <section id="booking-form">
            <div class="container">
                <div style="text-align:center; margin-bottom:3rem;">
                    <div class="section-eyebrow" style="justify-content:center;">احجز موعدك</div>
                    <h2 class="section-title">خطوة واحدة تفصلك عن موعدك</h2>
                </div>

                <div class="booking-grid">
                    <div class="booking-sidebar">
                        @if($clinic->phone)
                            <div class="contact-box">
                                <div class="contact-box-label">للتواصل المباشر</div>
                                <a href="tel:{{ $clinic->phone }}" class="contact-box-phone">{{ $clinic->phone }}</a>
                                @if($clinic->address)
                                    <div class="contact-box-addr">{{ $clinic->address }}</div>
                                @endif
                            </div>
                        @endif
                        <div class="info-box">
                            <div class="info-box-title">كيف يعمل الحجز؟</div>
                            <div class="info-box-body">أرسل طلبك عبر النموذج، سيصل لفريق العيادة مباشرةً كـ Pending Confirmation ويتواصلون معك للتأكيد.</div>
                        </div>
                    </div>

                    <div class="booking-form-card">
                        @if(session('booking_success'))
                            <div class="flash-success">{{ session('booking_success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="flash-errors">
                                @foreach($errors->all() as $err)
                                    <p>• {{ $err }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('clinic.public.book', $clinic->slug) }}">
                            @csrf
                            @if($visitTypes->isNotEmpty())
                                <div class="form-group full" style="margin-bottom:1.2rem;">
                                    <label class="form-label">الخدمة المطلوبة <span>(اختياري)</span></label>
                                    <select name="visit_type_id" class="form-select">
                                        <option value="">اختر الخدمة…</option>
                                        @foreach($visitTypes as $vt)
                                            <option value="{{ $vt->id }}" @selected(old('visit_type_id') == $vt->id)>{{ $vt->name }} — {{ number_format($vt->price, 0) }} ج.م</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">الاسم الكامل</label>
                                    <input type="text" name="patient_name" value="{{ old('patient_name') }}" required placeholder="أدخل اسمك" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="tel" name="patient_phone" value="{{ old('patient_phone') }}" required placeholder="01xxxxxxxxx" class="form-input">
                                </div>
                                <div class="form-group full">
                                    <label class="form-label">البريد الإلكتروني <span>(اختياري)</span></label>
                                    <input type="email" name="patient_email" value="{{ old('patient_email') }}" placeholder="you@example.com" class="form-input">
                                </div>
                                <div class="form-group full">
                                    <label class="form-label">التاريخ المفضل</label>
                                    <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required class="form-input">
                                </div>
                                <div class="form-group full">
                                    <label class="form-label">ملاحظات <span>(اختياري)</span></label>
                                    <textarea name="notes" class="form-textarea" placeholder="أي تفاصيل إضافية…">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="form-submit">إرسال طلب الموعد ←</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════ REVIEWS ════════ --}}
        <section id="reviews">
            <div class="container">
                <div class="reviews-head">
                    <div>
                        <div class="section-eyebrow">آراء المرضى</div>
                        <h2 class="section-title">تجربة علاجية يشعر بها الجميع</h2>
                    </div>
                    <div class="carousel-btns">
                        <button class="carousel-btn" data-carousel-prev aria-label="السابق">&#8592;</button>
                        <button class="carousel-btn active" data-carousel-next aria-label="التالي">&#8594;</button>
                    </div>
                </div>

                <div class="carousel-overflow">
                    <div data-carousel-track class="carousel-track">
                        @foreach([
                            ['text' => 'فريق محترف ومواعيد دقيقة وتجربة مريحة من أول اتصال.', 'author' => 'أحمد م.'],
                            ['text' => 'العيادة منظمة جداً والطبيب يشرح الحالة بوضوح واهتمام.', 'author' => 'سارة ك.'],
                            ['text' => 'الحجز كان سهلاً والمتابعة بعد الزيارة ممتازة.', 'author' => 'محمد ع.'],
                        ] as $review)
                            <div class="review-slide">
                                <div class="review-card">
                                    <div class="review-stars">★★★★★</div>
                                    <p class="review-body">{{ $review['text'] }}</p>
                                    <div class="review-author">
                                        <div class="review-avatar">م</div>
                                        <div>
                                            <div class="review-name">{{ $review['author'] }}</div>
                                            <div class="review-tag">مريض سابق · تقييم موثق</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════ HOURS ════════ --}}
        <section id="hours">
            <div class="container">
                <div class="hours-grid">
                    <div>
                        <div class="section-eyebrow">ساعات العمل</div>
                        <h2 class="section-title">اختر الوقت الأنسب لزيارتك</h2>
                        <p class="section-sub">يمكن لفريق الاستقبال تأكيد موعدك أو اقتراح وقت بديل حسب الجدول.</p>
                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}" class="btn-primary" style="margin-top:1.8rem; width:fit-content;">اتصل للحجز الفوري</a>
                        @endif
                    </div>
                    <div class="hours-list">
                        @forelse(($clinic->working_hours ?? []) as $key => $data)
                            @if(! empty($data['enabled']))
                                <div class="hours-item gs-card">
                                    <span class="hours-day">{{ $days[$key] ?? $key }}</span>
                                    <span class="hours-time">
                                        @foreach(($data['periods'] ?? []) as $p)
                                            {{ $p['open'] }} – {{ $p['close'] }}@if(!$loop->last)، @endif
                                        @endforeach
                                    </span>
                                </div>
                            @endif
                        @empty
                            <div class="hours-item">
                                <span class="hours-day" style="color:var(--muted); font-weight:700;">يرجى التواصل مع العيادة لمعرفة ساعات العمل.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════ LOCATION ════════ --}}
        <section id="location">
            <div class="container">
                <div class="location-grid">
                    <div>
                        <div class="section-eyebrow">الموقع</div>
                        <h2 class="section-title">نرحب بزيارتك في العيادة</h2>
                        @if($clinic->address)
                            <p class="section-sub">{{ $clinic->address }}</p>
                        @else
                            <p class="section-sub">موقع العيادة متاح عبر التواصل المباشر مع فريق الاستقبال.</p>
                        @endif
                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}" class="btn-outline" style="margin-top:1.6rem; width:fit-content;">اتصل للحصول على الاتجاهات</a>
                        @endif
                    </div>
                    <div class="map-wrap">
                        @if($clinic->latitude && $clinic->longitude)
                            <iframe title="موقع العيادة"
                                    src="https://maps.google.com/maps?q={{ $clinic->latitude }},{{ $clinic->longitude }}&output=embed"
                                    loading="lazy"></iframe>
                        @else
                            <iframe title="موقع العيادة"
                                    src="https://maps.google.com/maps?q={{ urlencode($clinic->address ?? $clinic->name) }}&output=embed"
                                    loading="lazy"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ════════ FAQ ════════ --}}
        <section id="faq">
            <div class="container">
                <div class="faq-inner">
                    <div style="text-align:center;">
                        <div class="section-eyebrow" style="justify-content:center;">الأسئلة الشائعة</div>
                        <h2 class="section-title">كل ما تحتاج معرفته</h2>
                    </div>
                    <div class="faq-list">
                        @foreach($faqs as $q => $a)
                            <details class="faq-item gs-card">
                                <summary>
                                    {{ $q }}
                                    <span class="faq-icon" aria-hidden="true">+</span>
                                </summary>
                                <div class="faq-body">{{ $a }}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- ════════ FOOTER ════════ --}}
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-name">{{ $clinic->name }}</div>
                    <div class="footer-tagline">{{ $clinic->doctor_title ?? 'عيادة طبية متخصصة' }}<br>رعاية طبية بمعايير عالية وتجربة سلسة.</div>
                </div>
                <div>
                    <div class="footer-col-title">روابط سريعة</div>
                    <div class="footer-links">
                        <a href="#services">الخدمات</a>
                        <a href="#about">عن العيادة</a>
                        <a href="#booking-form">احجز موعد</a>
                        <a href="#hours">ساعات العمل</a>
                        <a href="#location">الموقع</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-title">تواصل معنا</div>
                    <div class="footer-contact">
                        @if($clinic->phone)<p class="phone">{{ $clinic->phone }}</p>@endif
                        @if($clinic->address)<p style="margin-top:.4rem;">{{ $clinic->address }}</p>@endif
                        @if(!empty($clinic->social_links))
                            <div class="social-row">
                                @php
                                $socialIcons = [
                                    'facebook'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>',
                                    'instagram' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                                    'twitter'   => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                                    'x'         => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                                    'whatsapp'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
                                    'youtube'   => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                                    'tiktok'    => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
                                    'snapchat'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.45-.884-.675-1.332-.81-.136-.045-.256-.09-.346-.134-1.018-.39-1.318-.81-1.303-1.228.016-.434.313-.763.749-.9.164-.06.344-.09.538-.09.16 0 .359.03.523.112.375.165.734.271 1.049.271.217 0 .364-.051.448-.104l-.031-.51c-.106-1.629-.225-3.664.301-4.842C7.876 1.07 11.218.793 12.206.793z"/></svg>',
                                    'linkedin'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                                    'telegram'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
                                ];
                                @endphp
                                @foreach($clinic->social_links as $platform => $url)
                                    @if($url && isset($socialIcons[strtolower($platform)]))
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="social-btn" aria-label="{{ ucfirst($platform) }}" title="{{ ucfirst($platform) }}">
                                            {!! $socialIcons[strtolower($platform)] !!}
                                        </a>
                                    @elseif($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="social-btn" aria-label="{{ ucfirst($platform) }}" title="{{ ucfirst($platform) }}" style="width:auto;padding:0 .7rem;font-size:.72rem;font-weight:800;">
                                            {{ ucfirst($platform) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                جميع الحقوق محفوظة © {{ date('Y') }} {{ $clinic->name }} · مدعوم بواسطة <span>Clinora</span>
            </div>
        </div>
    </footer>

    {{-- ════════ GSAP ════════ --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
    (() => {
        gsap.registerPlugin(ScrollTrigger);

        /* ── Hamburger / mobile drawer ── */
        const hamburger = document.getElementById('hamburger');
        const drawer    = document.getElementById('nav-drawer');
        hamburger?.addEventListener('click', () => {
            const isOpen = hamburger.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', isOpen);
            if (isOpen) {
                drawer.classList.add('open');
                document.body.style.overflow = 'hidden';
            } else {
                drawer.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
        // close drawer on link click
        drawer?.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                hamburger.classList.remove('open');
                drawer.classList.remove('open');
                document.body.style.overflow = '';
            });
        });

        /* ── Navbar scroll class ── */
        const nav = document.getElementById('navbar');
        ScrollTrigger.create({
            start: 'top -60',
            onUpdate: self => nav.classList.toggle('scrolled', self.progress > 0)
        });

        /* ── Hero entrance ── */
        const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        heroTl
            .from('#hero-badge',  { y: 20, opacity: 0, duration: .7 })
            .from('#hero-title',  { y: 32, opacity: 0, duration: .75 }, '-=.4')
            .from('#hero-sub',    { y: 24, opacity: 0, duration: .65 }, '-=.45')
            .from('#hero-btns',   { y: 20, opacity: 0, duration: .6  }, '-=.4')
            .from('#hero-stats .stat-item', { y: 16, opacity: 0, stagger: .12, duration: .55 }, '-=.35')
            .from('#hero-image',  { x: 60, opacity: 0, duration: 1.1, ease: 'power2.out' }, .2)
            .from('#hero-card',   { y: 30, opacity: 0, duration: .7,  ease: 'back.out(1.4)' }, '-=.3');

        /* hero title underline */
        gsap.from('#hero-title em::after', {
            scaleX: 0, transformOrigin: 'right', duration: .9, ease: 'power3.out', delay: 1.2
        });

        /* ── Counters ── */
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = Number(el.dataset.counter);
            ScrollTrigger.create({
                trigger: el,
                start: 'top 85%',
                once: true,
                onEnter: () => {
                    gsap.to({ val: 0 }, {
                        val: target,
                        duration: 1.8,
                        ease: 'power2.out',
                        onUpdate() {
                            const v = Math.round(this.targets()[0].val);
                            el.textContent = (target > 100
                                ? new Intl.NumberFormat('ar-EG').format(v) + '+'
                                : v + '+');
                        }
                    });
                }
            });
        });

        /* ── Generic scroll-reveal ── */
        function revealCards(selector, stagger = .1, yOffset = 40) {
            const els = gsap.utils.toArray(selector);
            if (!els.length) return;
            gsap.from(els, {
                scrollTrigger: { trigger: els[0], start: 'top 82%' },
                y: yOffset, opacity: 0, duration: .7, stagger, ease: 'power2.out'
            });
        }

        /* sections */
        document.querySelectorAll('section').forEach(sec => {
            const head = sec.querySelector('.section-eyebrow, .about-eyebrow');
            if (head) gsap.from(head, {
                scrollTrigger: { trigger: head, start: 'top 88%' },
                x: -20, opacity: 0, duration: .6, ease: 'power2.out'
            });
            const title = sec.querySelector('.section-title, .about-title');
            if (title) gsap.from(title, {
                scrollTrigger: { trigger: title, start: 'top 88%' },
                y: 18, opacity: 0, duration: .65, ease: 'power2.out', delay: .1
            });
        });

        revealCards('.service-card');
        revealCards('.feat-card', .12, 30);
        revealCards('.hours-item', .08, 25);
        revealCards('.faq-item', .09, 22);

        /* about section special */
        gsap.from('.about-body', {
            scrollTrigger: { trigger: '.about-body', start: 'top 85%' },
            y: 22, opacity: 0, duration: .7, ease: 'power2.out', delay: .15
        });
        gsap.from('.contact-box', {
            scrollTrigger: { trigger: '.contact-box', start: 'top 85%' },
            x: -30, opacity: 0, duration: .7, ease: 'power2.out'
        });
        gsap.from('.info-box', {
            scrollTrigger: { trigger: '.info-box', start: 'top 85%' },
            x: -30, opacity: 0, duration: .7, ease: 'power2.out', delay: .12
        });
        gsap.from('.booking-form-card', {
            scrollTrigger: { trigger: '.booking-form-card', start: 'top 85%' },
            y: 40, opacity: 0, duration: .8, ease: 'power2.out'
        });
        gsap.from('.map-wrap', {
            scrollTrigger: { trigger: '.map-wrap', start: 'top 85%' },
            scale: .97, opacity: 0, duration: .8, ease: 'power2.out'
        });

        /* review slides */
        gsap.from('.review-card', {
            scrollTrigger: { trigger: '.carousel-overflow', start: 'top 82%' },
            y: 35, opacity: 0, stagger: .12, duration: .65, ease: 'power2.out'
        });

        /* footer */
        gsap.from('.footer-grid > *', {
            scrollTrigger: { trigger: 'footer', start: 'top 90%' },
            y: 25, opacity: 0, stagger: .1, duration: .6, ease: 'power2.out'
        });

        /* ── Carousel ── */
        const track = document.querySelector('[data-carousel-track]');
        let slide = 0;
        function moveCarousel(dir) {
            if (!track) return;
            const slides  = track.querySelectorAll('.review-slide');
            const perView = window.innerWidth >= 1200 ? 3 : window.innerWidth >= 768 ? 2 : 1;
            const max     = Math.max(0, slides.length - perView);
            slide = dir === 'next' ? Math.min(max, slide + 1) : Math.max(0, slide - 1);
            track.style.transform = `translateX(${slide * (100 / perView)}%)`;
        }
        document.querySelector('[data-carousel-next]')?.addEventListener('click', () => moveCarousel('next'));
        document.querySelector('[data-carousel-prev]')?.addEventListener('click', () => moveCarousel('prev'));

        /* ── Service card visit type selector ── */
        document.querySelectorAll('[data-visit-type]').forEach(btn => {
            btn.addEventListener('click', () => {
                const sel = document.querySelector('select[name="visit_type_id"]');
                if (sel) sel.value = btn.dataset.visitType;
            });
        });

        /* ── Smooth anchor links with offset ── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (!target) return;
                e.preventDefault();
                window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
            });
        });

    })();
    </script>
</body>
</html>
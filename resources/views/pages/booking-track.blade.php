<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>متابعة الدور | {{ $clinic->name }}</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        "sans": ["Cairo", "sans-serif"]
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
        html.dark { color-scheme: dark; }
        .clinora-grid-bg {
            background-color: #F8FAFC;
            background-image:
                radial-gradient(circle at 82% 14%, rgba(20, 184, 166, 0.08), transparent 36%),
                radial-gradient(circle at 18% 28%, rgba(52, 84, 138, 0.08), transparent 38%),
                linear-gradient(to right, rgba(15, 23, 42, 0.038) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.038) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }
        html.dark .clinora-grid-bg {
            background-color: #080B14;
            background-image:
                radial-gradient(circle at 82% 14%, rgba(20, 184, 166, 0.12), transparent 38%),
                radial-gradient(circle at 18% 28%, rgba(79, 70, 229, 0.11), transparent 40%),
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
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.55);
        }
    </style>
</head>
<body class="min-h-screen clinora-grid-bg text-slate-900 dark:text-slate-100 transition-colors duration-200">
    <div class="max-w-lg mx-auto px-4 py-8 sm:py-12" data-queue-track data-poll-url="{{ $pollUrl }}">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <a href="{{ route('clinic.public.show', $clinic->slug) }}" class="text-xs font-extrabold text-teal-600 dark:text-teal-400 hover:underline">{{ $clinic->name }}</a>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-0.5">متابعة الدور المباشرة</h1>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ $confirmUrl }}" class="rounded-xl bg-[#34548A] hover:bg-[#2B4675] px-3.5 py-2 text-xs font-extrabold text-white transition">تفاصيل الحجز</a>
                <button type="button" id="theme-toggle" data-theme-toggle
                    aria-label="تبديل الوضع الليلي والنهاري"
                    class="w-9 h-9 rounded-full border border-slate-200 dark:border-white/10 bg-white dark:bg-[#161B2E] text-slate-700 dark:text-slate-200 flex items-center justify-center shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 block dark:hidden" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="clinora-card rounded-3xl p-6 sm:p-8 space-y-6 relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-l from-teal-500 via-indigo-500 to-amber-500"></div>

            <div class="text-center pt-2">
                <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 mb-1.5">رقم دورك</p>
                <p class="text-5xl sm:text-6xl font-black text-teal-600 dark:text-teal-400" data-track="queue_number">{{ $payload['queue_number'] ?? '—' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3.5 text-center">
                <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4">
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">حالة الدور</p>
                    <p class="text-sm sm:text-base font-black text-slate-900 dark:text-white" data-track="queue_status_label">{{ $payload['queue_status_label'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4">
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">ترتيبك الحالي</p>
                    <p class="text-sm sm:text-base font-black text-slate-900 dark:text-white" data-track="estimated_position">{{ $payload['estimated_position'] ?? '—' }}</p>
                </div>
            </div>

            <div class="rounded-2xl bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/25 p-5">
                <p class="text-sm font-extrabold text-teal-900 dark:text-teal-200 leading-relaxed" data-track="message">{{ $payload['message'] }}</p>
                <p class="text-xs font-bold text-teal-700 dark:text-teal-300 mt-2" data-track="position_ahead_text">
                    @if($payload['position_ahead'] !== null)
                        أمامك: {{ $payload['position_ahead'] }}
                    @endif
                </p>
            </div>

            <div class="border-t border-slate-200/80 dark:border-white/[0.07] pt-4 text-xs font-semibold text-slate-500 dark:text-slate-400 space-y-1.5">
                <p>التاريخ: <span class="font-bold text-slate-700 dark:text-slate-200" data-track="appointment_date_label">{{ $payload['appointment_date_label'] }}</span></p>
                <p>حالة الحجز: <span class="font-bold text-slate-700 dark:text-slate-200" data-track="booking_status_label">{{ $payload['booking_status_label'] }}</span></p>
                <p class="pt-1 text-slate-400 dark:text-slate-500">يتم تحديث الصفحة تلقائيًا كل حوالي 18 ثانية أثناء فتحها.</p>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
                try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
            });
        });

        (() => {
            const root = document.querySelector('[data-queue-track]');
            if (!root) return;
            const pollUrl = root.getAttribute('data-poll-url');
            let timer = null;
            let inFlight = false;

            const setText = (key, value) => {
                const el = root.querySelector(`[data-track="${key}"]`);
                if (el) el.textContent = value ?? '—';
            };

            const apply = (data) => {
                setText('queue_number', data.queue_number);
                setText('queue_status_label', data.queue_status_label);
                setText('estimated_position', data.estimated_position);
                setText('message', data.message);
                setText('appointment_date_label', data.appointment_date_label);
                setText('booking_status_label', data.booking_status_label);
                const ahead = root.querySelector('[data-track="position_ahead_text"]');
                if (ahead) {
                    ahead.textContent = data.position_ahead == null ? '' : `أمامك: ${data.position_ahead}`;
                }
                if (!data.is_trackable || ['completed', 'skipped'].includes(data.queue_status) || ['cancelled', 'rejected'].includes(data.booking_status)) {
                    stop();
                }
            };

            const poll = async () => {
                if (inFlight || document.hidden) return;
                inFlight = true;
                try {
                    const res = await fetch(pollUrl, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) apply(await res.json());
                } catch (e) {}
                inFlight = false;
            };

            const start = () => {
                stop();
                timer = setInterval(poll, 18000);
            };
            const stop = () => {
                if (timer) clearInterval(timer);
                timer = null;
            };

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) stop();
                else { poll(); start(); }
            });

            start();
        })();
    </script>
</body>
</html>

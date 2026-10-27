<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تأكيد الحجز | {{ $clinic->name }}</title>
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
    <div class="max-w-lg mx-auto px-4 py-8 sm:py-12">
        {{-- Top Bar --}}
        <div class="mb-5 flex items-center justify-between">
            <a href="{{ route('clinic.public.show', $clinic->slug) }}"
               class="inline-flex items-center gap-2 text-xs sm:text-sm font-extrabold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                <span>→</span>
                <span>{{ $clinic->name }}</span>
            </a>
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

        <div class="clinora-card rounded-3xl overflow-hidden">
            <div class="px-6 py-8 text-center text-white" style="background: linear-gradient(135deg, #34548A 0%, #0D9488 100%);">
                <div class="mx-auto mb-4 w-16 h-16 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-3xl font-black">✓</div>
                <h1 class="text-2xl font-black">تم تأكيد طلب الحجز</h1>
                <p class="mt-1.5 text-slate-100 text-sm font-semibold">{{ $clinic->name }}</p>
            </div>

            <div class="p-6 sm:p-8 space-y-5">
                <div class="grid grid-cols-2 gap-3.5">
                    <div class="rounded-2xl bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/25 p-5 text-center col-span-2">
                        <p class="text-xs font-extrabold text-teal-700 dark:text-teal-300 mb-1">رقم الدور</p>
                        <p class="text-4xl sm:text-5xl font-black text-teal-800 dark:text-teal-200">{{ $payload['queue_number'] ?? '—' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">التاريخ</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $payload['appointment_date_label'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">حالة الدور</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $payload['queue_status_label'] }}</p>
                    </div>
                    @if($payload['doctor_name'])
                        <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4 col-span-2">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">الطبيب</p>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $payload['doctor_name'] }}</p>
                        </div>
                    @endif
                    @if($payload['visit_type'])
                        <div class="rounded-2xl bg-slate-100/80 dark:bg-[#181E31] border border-slate-200/70 dark:border-white/[0.06] p-4 col-span-2">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">الخدمة</p>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $payload['visit_type'] }}</p>
                        </div>
                    @endif
                </div>

                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 leading-relaxed">{{ $payload['message'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    رقم الدور ثابت لحجزك. قد يتغيّر ترتيبك الحالي إذا اكتمل أو تُخطي مرضى أمامك — تابع صفحة التتبع لمعرفة عدد المنتظرين أمامك.
                </p>

                <div class="space-y-2.5 pt-2">
                    <a href="{{ $trackingUrl }}"
                       class="block w-full py-3.5 rounded-xl bg-[#F59E0B] hover:bg-[#D97706] text-white font-extrabold text-sm text-center shadow-md shadow-amber-500/20 transition">
                        فتح صفحة متابعة الدور المباشرة
                    </a>
                    <button type="button" id="copy-link-btn"
                            data-copy-url="{{ $trackingUrl }}"
                            class="w-full py-3.5 rounded-xl bg-[#34548A] hover:bg-[#2B4675] text-white font-extrabold text-sm transition cursor-pointer">
                        نسخ رابط المتابعة
                    </button>
                    <p id="copy-feedback" class="hidden text-center text-xs font-extrabold text-emerald-600 dark:text-emerald-400 pt-1">تم نسخ الرابط</p>
                </div>
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

        document.getElementById('copy-link-btn')?.addEventListener('click', async (e) => {
            const url = e.currentTarget.getAttribute('data-copy-url');
            const feedback = document.getElementById('copy-feedback');
            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(url);
                } else {
                    const input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }
                feedback?.classList.remove('hidden');
                setTimeout(() => feedback?.classList.add('hidden'), 2500);
            } catch (err) {
                if (feedback) {
                    feedback.textContent = 'تعذّر النسخ — انسخ الرابط يدويًا من شريط العنوان في صفحة التتبع';
                    feedback.classList.remove('hidden', 'text-emerald-600');
                    feedback.classList.add('text-rose-600');
                }
            }
        });
    </script>
</body>
</html>

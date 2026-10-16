<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>متابعة الدور | {{ $clinic->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 text-gray-900">
    <div class="max-w-lg mx-auto px-4 py-8" data-queue-track data-poll-url="{{ $pollUrl }}">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400">{{ $clinic->name }}</p>
                <h1 class="text-xl font-black text-slate-900">متابعة الدور</h1>
            </div>
            <a href="{{ $confirmUrl }}" class="text-xs font-bold text-teal-700 hover:underline">تفاصيل الحجز</a>
        </div>

        <div class="rounded-3xl bg-white border border-slate-100 shadow-sm p-6 space-y-5">
            <div class="text-center">
                <p class="text-xs font-bold text-slate-400 mb-1">رقم دورك</p>
                <p class="text-5xl font-black text-teal-700" data-track="queue_number">{{ $payload['queue_number'] ?? '—' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-[11px] font-bold text-slate-400 mb-1">حالة الدور</p>
                    <p class="text-sm font-black text-slate-800" data-track="queue_status_label">{{ $payload['queue_status_label'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-[11px] font-bold text-slate-400 mb-1">ترتيبك الحالي</p>
                    <p class="text-sm font-black text-slate-800" data-track="estimated_position">{{ $payload['estimated_position'] ?? '—' }}</p>
                </div>
            </div>

            <div class="rounded-2xl bg-teal-50 border border-teal-100 p-4">
                <p class="text-sm font-bold text-teal-900 leading-relaxed" data-track="message">{{ $payload['message'] }}</p>
                <p class="text-xs text-teal-700/80 mt-2" data-track="position_ahead_text">
                    @if($payload['position_ahead'] !== null)
                        أمامك: {{ $payload['position_ahead'] }}
                    @endif
                </p>
            </div>

            <div class="text-xs text-slate-400 space-y-1">
                <p>التاريخ: <span data-track="appointment_date_label">{{ $payload['appointment_date_label'] }}</span></p>
                <p>حالة الحجز: <span data-track="booking_status_label">{{ $payload['booking_status_label'] }}</span></p>
                <p class="pt-2">يتم تحديث الصفحة تلقائيًا كل حوالي 18 ثانية أثناء فتحها.</p>
            </div>
        </div>
    </div>

    <script>
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

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تأكيد الحجز | {{ $clinic->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-b from-teal-50 to-white text-gray-900">
    <div class="max-w-lg mx-auto px-4 py-10">
        <div class="rounded-3xl bg-white shadow-xl border border-teal-100 overflow-hidden">
            <div class="bg-teal-700 text-white px-6 py-8 text-center">
                <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-white/15 flex items-center justify-center text-3xl">✓</div>
                <h1 class="text-2xl font-black">تم تأكيد طلب الحجز</h1>
                <p class="mt-2 text-teal-100 text-sm">{{ $clinic->name }}</p>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-teal-50 p-4 text-center col-span-2">
                        <p class="text-xs font-bold text-teal-700 mb-1">رقم الدور</p>
                        <p class="text-4xl font-black text-teal-800">{{ $payload['queue_number'] ?? '—' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500 mb-1">التاريخ</p>
                        <p class="text-sm font-bold text-slate-800">{{ $payload['appointment_date_label'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500 mb-1">حالة الدور</p>
                        <p class="text-sm font-bold text-slate-800">{{ $payload['queue_status_label'] }}</p>
                    </div>
                    @if($payload['doctor_name'])
                        <div class="rounded-2xl bg-slate-50 p-4 col-span-2">
                            <p class="text-xs font-bold text-slate-500 mb-1">الطبيب</p>
                            <p class="text-sm font-bold text-slate-800">{{ $payload['doctor_name'] }}</p>
                        </div>
                    @endif
                    @if($payload['visit_type'])
                        <div class="rounded-2xl bg-slate-50 p-4 col-span-2">
                            <p class="text-xs font-bold text-slate-500 mb-1">الخدمة</p>
                            <p class="text-sm font-bold text-slate-800">{{ $payload['visit_type'] }}</p>
                        </div>
                    @endif
                </div>

                <p class="text-sm text-slate-600 leading-relaxed">{{ $payload['message'] }}</p>
                <p class="text-xs text-slate-400 leading-relaxed">
                    رقم الدور ثابت لحجزك. قد يتغيّر ترتيبك الحالي إذا اكتمل أو تُخطي مرضى أمامك — تابع صفحة التتبع لمعرفة عدد المنتظرين أمامك.
                </p>

                <div class="space-y-2">
                    <button type="button" id="copy-link-btn"
                            data-copy-url="{{ $trackingUrl }}"
                            class="w-full py-3 rounded-2xl bg-teal-700 hover:bg-teal-600 text-white font-bold text-sm transition">
                        نسخ رابط المتابعة
                    </button>
                    <a href="{{ $trackingUrl }}"
                       class="block w-full py-3 rounded-2xl border border-teal-200 text-teal-800 font-bold text-sm text-center hover:bg-teal-50 transition">
                        فتح صفحة متابعة الدور
                    </a>
                    <p id="copy-feedback" class="hidden text-center text-xs font-bold text-emerald-600">تم نسخ الرابط</p>
                </div>
            </div>
        </div>
    </div>

    <script>
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

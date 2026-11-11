<x-layouts.admin>
    <x-slot:title>مفتاح توقيع تراخيص Offline</x-slot:title>

    <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm p-6 space-y-4 max-w-3xl">
        <h3 class="font-black text-lg">حالة المفتاح</h3>
        <p class="text-sm {{ $keyStatus['configured'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-800 dark:text-amber-200' }} font-bold">{{ $keyStatus['message'] }}</p>

        <dl class="space-y-2 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-slate-500">key_id</dt><dd class="font-mono" dir="ltr">{{ $keyStatus['key_id'] ?: '—' }}</dd></div>
            <div>
                <dt class="text-slate-500 mb-1">المفتاح العام (آمن للعرض)</dt>
                <dd class="font-mono text-xs break-all bg-slate-50 dark:bg-white/5 rounded-xl p-3" dir="ltr">{{ $keyStatus['public_key'] ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">وضع الإنتاج</dt><dd class="font-bold">{{ $keyStatus['is_production'] ? 'نعم' : 'لا' }}</dd></div>
        </dl>

        <div class="rounded-xl border border-slate-200 dark:border-white/10 p-4 text-sm space-y-2 bg-slate-50 dark:bg-white/[0.03]">
            <p class="font-black">إجراء آمن لإنشاء مفتاح إنتاج</p>
            <ol class="list-decimal me-5 space-y-1 text-slate-600 dark:text-slate-300">
                <li>على خادم الشركة (ليس جهاز التطوير العام): شغّل <code class="font-mono text-xs" dir="ltr">php artisan clinora:offline-license-keygen --key-id=lic-2026-01 --out=/secure/path/lic-2026-01.seed</code></li>
                <li>ضع مسار الملف و`key_id` في `.env` عبر `CLINORA_OFFLINE_LICENSE_*`.</li>
                <li>انسخ المفتاح العام فقط إلى Clinora Offline (`config/clinora.php` و`LicensePublicKeys.Trusted`) ثم ابنِ مثبّت Offline جديد.</li>
                <li>لا ترفع ملف `.seed` إلى Git أو التخزين العام.</li>
            </ol>
        </div>

        <a href="{{ route('admin.offline-licenses.index') }}" class="inline-flex px-4 py-2 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10">رجوع</a>
    </div>
</x-layouts.admin>

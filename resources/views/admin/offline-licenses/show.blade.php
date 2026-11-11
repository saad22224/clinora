<x-layouts.admin>
    <x-slot:title>تفاصيل الترخيص {{ $license->license_id }}</x-slot:title>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/30 text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.offline-licenses.download', $license) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500">تنزيل JSON</a>
        <a href="{{ route('admin.offline-licenses.renew', $license) }}" class="px-4 py-2 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10">تجديد / إصدار لاحق</a>
        <a href="{{ route('admin.offline-licenses.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10">رجوع</a>
    </div>

    <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-black text-lg" dir="ltr">{{ $license->license_id }}</h3>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-slate-100 dark:bg-white/10">{{ $license->statusLabel() }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">العيادة</dt><dd class="font-bold">{{ $license->clinic_name }}</dd></div>
            <div><dt class="text-slate-500">العميل</dt><dd class="font-bold">{{ $license->customer_name ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">key_id</dt><dd class="font-mono" dir="ltr">{{ $license->key_id }}</dd></div>
            <div><dt class="text-slate-500">transfer_seq</dt><dd class="font-bold">{{ $license->transfer_seq }}</dd></div>
            <div><dt class="text-slate-500">يبدأ</dt><dd class="font-bold">{{ $license->starts_at->toDateString() }}</dd></div>
            <div><dt class="text-slate-500">ينتهي</dt><dd class="font-bold">{{ $license->expires_at->toDateString() }}</dd></div>
            <div><dt class="text-slate-500">الأجهزة</dt><dd class="font-bold">{{ $license->max_devices }}</dd></div>
            <div><dt class="text-slate-500">أصدره</dt><dd class="font-bold">{{ $license->issuer?->name ?: '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">ملخص البصمة</dt><dd class="font-mono text-xs" dir="ltr">{{ $license->fingerprintSummary() }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">SHA-256 للملف</dt><dd class="font-mono text-xs break-all" dir="ltr">{{ $license->envelope_sha256 }}</dd></div>
            @if($license->renewedFrom)
                <div class="sm:col-span-2"><dt class="text-slate-500">مجدَّد من</dt><dd class="font-mono text-xs" dir="ltr"><a class="text-indigo-600" href="{{ route('admin.offline-licenses.show', $license->renewedFrom) }}">{{ $license->renewedFrom->license_id }}</a></dd></div>
            @endif
        </dl>
        <p class="text-xs text-slate-500">لا يمكن تعديل ملف موقّع. أي تغيير يتطلب إصدارًا جديدًا.</p>
    </div>
</x-layouts.admin>

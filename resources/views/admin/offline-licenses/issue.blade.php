<x-layouts.admin>
    <x-slot:title>{{ $renewing ? 'تجديد ترخيص offline' : 'تأكيد إصدار الترخيص' }}</x-slot:title>

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/30 text-sm font-semibold text-rose-700 dark:text-rose-300">{{ session('error') }}</div>
    @endif

    @unless($keyStatus['configured'] && ($keyStatus['is_production'] || ! config('clinora.offline_license.production')))
        <div class="px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 text-sm text-amber-900 dark:text-amber-200">{{ $keyStatus['message'] }}</div>
    @endunless

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm p-6 space-y-3">
            <h3 class="font-black text-lg">بيانات من رمز الطلب</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">إصدار الطلب</dt><dd class="font-bold" dir="ltr">v{{ $requestData['v'] }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">عيادة في الطلب</dt><dd class="font-bold">{{ $requestData['clinic'] ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">ترخيص سابق</dt><dd class="font-mono text-xs" dir="ltr">{{ $requestData['license_id'] ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">إصدار Offline</dt><dd class="font-bold" dir="ltr">{{ $requestData['version'] ?: '—' }}</dd></div>
                <div>
                    <dt class="text-slate-500 mb-1">ملخص البصمة</dt>
                    <dd class="font-mono text-[11px] break-all bg-slate-50 dark:bg-white/5 rounded-xl p-3" dir="ltr">
                        @foreach($requestData['fingerprint'] as $i => $part)
                            [{{ $i }}] {{ $part ?? 'null' }}<br>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 mb-1">مفتاح التثبيت (للنقل لاحقًا)</dt>
                    <dd class="font-mono text-[11px] break-all bg-slate-50 dark:bg-white/5 rounded-xl p-3" dir="ltr">{{ $requestData['install_public_key'] }}</dd>
                </div>
            </dl>
            <p class="text-xs text-slate-500">لا يوجد إلغاء عن بُعد لتراخيص Offline. الإلغاء في هذه اللوحة لن يوقف جهاز العميل.</p>
        </div>

        <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm p-6">
            <h3 class="font-black text-lg mb-4">{{ $renewing ? 'بيانات التجديد' : 'بيانات الترخيص' }}</h3>
            <form method="POST" action="{{ $renewing ? route('admin.offline-licenses.renew.store') : route('admin.offline-licenses.store') }}" class="space-y-3">
                @csrf
                @if($renewing)
                    <div>
                        <label class="text-xs font-bold text-slate-500">رمز طلب جديد (اختياري — جهاز آخر)</label>
                        <textarea name="request_code" rows="3" dir="ltr" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 font-mono text-xs" placeholder="اتركه فارغًا للإبقاء على بصمة الترخيص السابق"></textarea>
                    </div>
                @endif
                <div>
                    <label class="text-xs font-bold text-slate-500">اسم العيادة</label>
                    <input name="clinic_name" value="{{ old('clinic_name', $defaults['clinic_name'] ?? '') }}" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">اسم العميل (للسجل فقط — لا يدخل الملف الموقّع)</label>
                    <input name="customer_name" value="{{ old('customer_name', $defaults['customer_name'] ?? '') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">رقم الترخيص</label>
                    <input name="license_id" dir="ltr" value="{{ old('license_id', $defaults['license_id'] ?? '') }}" placeholder="يُولَّد تلقائيًا إن تُرك فارغًا" @if($renewing) required @endif class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm font-mono">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500">يبدأ</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at', $defaults['starts_at']) }}" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">ينتهي</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at', $defaults['expires_at']) }}" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500">أيام السماح</label>
                        <input type="number" name="grace_days" value="{{ old('grace_days', $defaults['grace_days']) }}" min="0" max="365" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">أقصى أجهزة</label>
                        <input type="number" name="max_devices" value="{{ old('max_devices', $defaults['max_devices']) }}" min="0" max="1000" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500">min_match</label>
                        <input type="number" name="min_match" value="{{ old('min_match', $defaults['min_match']) }}" min="1" max="5" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">transfer_seq</label>
                        <input type="number" name="transfer_seq" value="{{ old('transfer_seq', $defaults['transfer_seq'] ?? 0) }}" min="0" required class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">أقصى أطباء (اختياري)</label>
                    <input type="number" name="max_doctors" value="{{ old('max_doctors', $defaults['max_doctors'] ?? '') }}" min="0" max="1000" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 text-sm">
                </div>
                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" name="confirm" value="1" required class="mt-1">
                    <span>أؤكّد مراجعة البيانات وأن الملف سيُوقَّع نهائيًا ولن يُعدَّل بعد الإصدار.</span>
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500">{{ $renewing ? 'توقيع التجديد' : 'توقيع وإصدار' }}</button>
                    <a href="{{ route('admin.offline-licenses.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin>
    <x-slot:title>إصدار ترخيص Offline</x-slot:title>

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/30 text-sm font-semibold text-rose-700 dark:text-rose-300">{{ session('error') }}</div>
    @endif

    @unless($keyStatus['configured'])
        <div class="px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 text-sm text-amber-900 dark:text-amber-200">
            {{ $keyStatus['message'] }}
            <a href="{{ route('admin.offline-licenses.keys') }}" class="font-bold underline ms-1">إعداد المفتاح</a>
        </div>
    @endunless

    <div class="bg-white dark:bg-[#0F0F1A] border border-slate-100 dark:border-white/[0.06] rounded-2xl shadow-sm p-6 space-y-4 max-w-3xl">
        <div>
            <h3 class="font-black text-lg text-slate-900 dark:text-white">1. الصق رمز طلب التفعيل</h3>
            <p class="text-sm text-slate-500 mt-1">رمز `CLNREQ1-...` من صفحة الترخيص في Clinora Offline. لن يُقبل رمز تالف أو ناقص.</p>
        </div>

        <form method="POST" action="{{ route('admin.offline-licenses.preview') }}" class="space-y-4">
            @csrf
            <textarea name="request_code" rows="8" dir="ltr" required placeholder="CLNREQ1-...." class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-3 py-2 font-mono text-xs">{{ old('request_code') }}</textarea>
            @error('request_code')<p class="text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500">فك الرمز والمتابعة</button>
                <a href="{{ route('admin.offline-licenses.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 dark:border-white/10">رجوع</a>
            </div>
        </form>
    </div>
</x-layouts.admin>

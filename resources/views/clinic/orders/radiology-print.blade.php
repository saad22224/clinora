<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ $patient->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: Cairo, sans-serif; background: #f1f5f9; }
        .sheet { width: 210mm; min-height: 297mm; margin: 20px auto; background: #fff; padding: 2rem; }
        @media print { body { background: #fff; } .sheet { margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="fixed top-4 left-4 z-50 no-print flex gap-2">
        <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-xl">طباعة الطلب</button>
        <button onclick="window.close()" class="px-5 py-2 bg-gray-100 font-bold rounded-xl border">إغلاق</button>
    </div>
    <div class="sheet shadow rounded-xl">
        <header class="border-b-2 border-gray-900 pb-4 mb-6 flex justify-between">
            <div>
                <h1 class="text-2xl font-black">{{ $clinic->name }}</h1>
                <p class="font-bold text-gray-700">{{ $clinic->doctor_title }} {{ $order->orderedBy?->name }}</p>
                @if($clinic->phone)<p class="text-sm text-gray-500 dir-ltr">{{ $clinic->phone }}</p>@endif
            </div>
            <div class="text-left">
                <p class="text-lg font-black">{{ $title }}</p>
                <p class="text-sm">{{ $order->ordered_at->format('Y/m/d H:i') }}</p>
            </div>
        </header>
        <div class="mb-6 grid grid-cols-2 gap-3 text-sm">
            <p><span class="font-bold text-gray-500">المريض:</span> {{ $patient->name }}</p>
            <p><span class="font-bold text-gray-500">العمر:</span> {{ $patient->displayAge() !== null ? $patient->displayAge().' سنة' : '—' }}</p>
            <p><span class="font-bold text-gray-500">الزيارة:</span> {{ $visit->visited_at->format('Y/m/d') }}</p>
            <p><span class="font-bold text-gray-500">الهاتف:</span> <span class="dir-ltr">{{ $patient->phone }}</span></p>
        </div>
        <h2 class="font-black mb-3">الفحوصات المطلوبة</h2>
        <ol class="list-decimal me-5 space-y-2">
            @foreach($order->items as $item)
                <li class="font-bold">{{ $item->name_snapshot }}</li>
            @endforeach
        </ol>
        @if($order->notes)
            <div class="mt-6 pt-4 border-t border-dashed">
                <p class="text-xs font-bold text-gray-400 mb-1">تعليمات</p>
                <p class="whitespace-pre-wrap">{{ $order->notes }}</p>
            </div>
        @endif
        <footer class="mt-16 flex justify-between items-end">
            <p class="text-sm text-gray-500">Clinora</p>
            <div class="w-40 text-center text-sm font-bold border-t border-dashed pt-2">توقيع الطبيب</div>
        </footer>
    </div>
</body>
</html>

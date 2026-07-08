<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طباعة الوصفة الطبية - {{ $prescription->patient->name ?? 'مريض' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
        }
        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        .prescription-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 10rem;
            color: rgba(0, 0, 0, 0.02);
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
        .content-layer {
            position: relative;
            z-index: 10;
        }
        .rx-symbol {
            font-size: 3.5rem;
            line-height: 1;
            font-family: serif;
            font-weight: 900;
            color: #000;
        }
        .rx-text {
            font-family: monospace, monospace;
            white-space: pre-wrap;
            line-height: 2.2;
            font-size: 1.1rem;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-container {
                width: 100%;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="text-gray-900">

    <div class="fixed top-4 left-4 z-50 no-print flex gap-2">
        <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            طباعة الروشتة
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl border border-gray-200 hover:bg-gray-200 transition-colors">
            إغلاق
        </button>
    </div>

    <div class="print-container">
        <!-- Watermark -->
        <div class="prescription-watermark">{{ $clinic->name }}</div>

        <div class="content-layer p-8 md:p-12 flex flex-col h-full min-h-[297mm]">
            <!-- Header -->
            <header class="flex justify-between items-start border-b-2 border-gray-900 pb-6 mb-6">
                <!-- Clinic & Doctor Info -->
                <div class="text-right">
                    <h1 class="text-3xl font-black text-gray-900 mb-1">{{ $clinic->doctor_title ?? 'د.' }} {{ $clinic->users->first()?->name ?? 'طبيب العيادة' }}</h1>
                    <p class="text-lg font-bold text-gray-700">{{ $clinic->name }}</p>
                    <div class="mt-2 text-sm text-gray-500 font-medium space-y-1">
                        @if($clinic->address)
                            <p class="flex items-center gap-1.5 justify-end">
                                <span>{{ $clinic->address }}</span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </p>
                        @endif
                        @if($clinic->phone)
                            <p class="flex items-center gap-1.5 justify-end dir-ltr">
                                <span>{{ $clinic->phone }}</span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Logo or Additional Info -->
                <div class="text-left">
                    @if($clinic->logo)
                        <img src="{{ Storage::url($clinic->logo) }}" alt="Logo" class="max-h-24 object-contain">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 border-2 border-gray-900 flex items-center justify-center font-black text-gray-900 text-3xl">
                            {{ mb_substr($clinic->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </header>

            <!-- Patient Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-8 flex flex-wrap gap-x-8 gap-y-4 justify-between items-center">
                <div>
                    <span class="text-sm font-bold text-gray-500">اسم المريض:</span>
                    <strong class="text-lg text-gray-900 mr-2">{{ $prescription->patient->name ?? '—' }}</strong>
                </div>
                
                @if($prescription->patient?->dob)
                <div>
                    <span class="text-sm font-bold text-gray-500">العمر:</span>
                    <strong class="text-lg text-gray-900 mr-2">{{ $prescription->patient?->dob->age }} سنة</strong>
                </div>
                @else
                <div>
                    <span class="text-sm font-bold text-gray-500">العمر:</span>
                    <strong class="text-lg text-gray-900 mr-2">{{ $prescription->patient?->age }} سنة</strong>
                </div>

                @endif
                
                <div>
                    <span class="text-sm font-bold text-gray-500">التاريخ:</span>
                    <strong class="text-lg text-gray-900 mr-2">{{ $prescription->created_at->format('Y/m/d') }}</strong>
                </div>
            </div>

            <!-- Rx Body -->
            <div class="flex-1">
                <div class="rx-symbol mb-6">Rx</div>
                
                <div class="pl-8 pr-4">
                    @if($prescription->items->isNotEmpty())
                        <div class="space-y-6 mb-8" dir="ltr">
                            @foreach($prescription->items as $index => $item)
                                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-baseline gap-3">
                                        <span class="font-bold text-lg text-gray-500">{{ $index + 1 }}.</span>
                                        <div class="flex-1">
                                            <h3 class="text-xl font-black text-gray-900 tracking-wide">{{ $item->medicine_name }}</h3>
                                            @if($item->dosage || $item->instructions)
                                                <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-base font-bold text-gray-700 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 inline-block">
                                                    @if($item->dosage)
                                                        <span class="text-indigo-900">{{ $item->dosage }}</span>
                                                    @endif
                                                    @if($item->instructions)
                                                        <span class="text-gray-600 font-medium">— {{ $item->instructions }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($prescription->notes)
                            <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-300">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2" dir="ltr">General Notes / Recommendations:</h4>
                                <div class="rx-text text-left" dir="ltr">
{!! nl2br(e($prescription->notes)) !!}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="rx-text text-left" dir="ltr">
{!! nl2br(e($prescription->notes)) !!}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-12 pt-6 border-t-2 border-gray-900 flex justify-between items-end">
                <div class="text-sm font-bold text-gray-500">
                    <p>ألف سلامة، مع تمنياتنا بالشفاء العاجل.</p>
                </div>
                <div class="text-center w-48">
                    <div class="border-b-2 border-gray-400 border-dashed mb-2 h-10"></div>
                    <p class="text-sm font-bold text-gray-700">توقيع الطبيب</p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Auto-print when loaded (uncomment if you want to print immediately)
        window.addEventListener('load', () => {
            // setTimeout(() => window.print(), 500);
        });
    </script>
</body>
</html>

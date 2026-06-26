<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'لوحة العيادة' }} | كلينورا</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Cairo', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-gray-900 dark:bg-[#0A0A0F] dark:text-gray-100 flex min-h-screen overflow-hidden">

    {{-- ══════ SIDEBAR ══════ --}}
    <aside id="sidebar" class="w-[268px] bg-white dark:bg-[#0F0F14] border-l border-gray-100 dark:border-white/[0.06] flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0 shadow-sm">

        {{-- Brand --}}
        <div class="h-[68px] flex items-center justify-between px-5 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-lg shadow-brand-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white leading-none">{{ Auth::user()->clinic->name ?? 'العيادة' }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">لوحة تحكم العيادة</p>
                </div>
            </div>
            {{-- View Public Page Button --}}
            @if(Auth::user()->clinic?->slug)
            <a href="{{ route('clinic.public.show', Auth::user()->clinic->slug) }}" target="_blank"
               title="عرض الصفحة العامة"
               class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 transition-all" >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            <x-clinic.nav-item route="clinic.dashboard" icon="home" label="الرئيسية" />

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest">العمليات الطبية</p>
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_patients'))
                <x-clinic.nav-item route="clinic.patients.index" icon="users" label="المرضى" />
            @endif
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_appointments'))
                <x-clinic.nav-item route="clinic.appointments.index" icon="calendar" label="المواعيد" />
            @endif
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_prescriptions'))
                <x-clinic.nav-item route="clinic.prescriptions.index" icon="document-text" label="الوصفات الطبية" />
            @endif
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_records'))
                <x-clinic.nav-item route="clinic.records.index" icon="folder" label="السجلات الطبية" />
            @endif

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest">الإدارة والمالية</p>
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_billing'))
                <x-clinic.nav-item route="clinic.billing.index" icon="credit-card" label="الفواتير" />
            @endif
            @if(Auth::user()->role !== 'secretary')
                <x-clinic.nav-item route="clinic.reports.index" icon="chart-bar" label="التقارير" />
                <x-clinic.nav-item route="clinic.staff.index" icon="user-group" label="فريق العمل" />
            @endif

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest">النظام</p>
            @if(Auth::user()->role !== 'secretary' || Auth::user()->can('manage_settings'))
                <x-clinic.nav-item route="clinic.visit-types.index" icon="cube" label="الخدمات وأنواع الزيارات" />
                <x-clinic.nav-item route="clinic.settings.index" icon="cog" label="إعدادات العيادة" />
            @endif
        </nav>

        {{-- User Footer --}}
        <div class="p-3 border-t border-gray-100 dark:border-white/[0.06] shrink-0">
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b5bdb&color=fff&size=80" alt="Avatar" class="w-9 h-9 rounded-full shrink-0 ring-2 ring-gray-100 dark:ring-white/10">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="تسجيل الخروج" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ══════ MAIN CONTENT ══════ --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        {{-- Topbar --}}
        <header class="h-[68px] bg-white/90 dark:bg-[#0F0F14]/90 backdrop-blur-xl border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between px-4 sm:px-6 z-10 shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h1 class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $title ?? 'لوحة التحكم' }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l، d F Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- View public page quick button --}}
                @if(Auth::user()->clinic?->slug)
                <a href="{{ route('clinic.public.show', Auth::user()->clinic->slug) }}" target="_blank"
                   class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-xl border border-brand-200 text-brand-600 hover:bg-brand-50 dark:border-brand-700/40 dark:text-brand-400 dark:hover:bg-brand-900/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    عرض الصفحة العامة
                </a>
                @endif

                {{-- Notifications placeholder --}}
                <button class="relative p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-2 right-2 block h-1.5 w-1.5 rounded-full bg-red-500 ring-1 ring-white dark:ring-[#0A0A0F]"></span>
                </button>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>

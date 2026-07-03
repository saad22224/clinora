<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'الإدارة العليا' }} | كلينورا</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Cairo', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body class="font-sans antialiased bg-[#05050A] text-slate-100 flex min-h-screen overflow-hidden">

    {{-- ══════ ADMIN SIDEBAR ══════ --}}
    <aside class="w-[260px] bg-[#0A0A12] border-l border-white/[0.06] flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0">

        {{-- Brand --}}
        <div class="h-[68px] flex items-center px-5 border-b border-white/[0.06] shrink-0 gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h2 class="font-black text-sm text-white leading-none">كلينورا</h2>
                <p class="text-[10px] text-slate-500 mt-0.5">Super Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            <x-admin.nav-item route="admin.dashboard" icon="home" label="الرئيسية" />

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-600 uppercase tracking-widest">إدارة المنصة</p>
            <x-admin.nav-item route="admin.clinics.index" icon="office-building" label="العيادات" />
            <x-admin.nav-item route="admin.marketers.index" icon="users" label="المسوقون والإحالات" badge="جديد" />
            <x-admin.nav-item route="admin.subscriptions.index" icon="credit-card" label="الباقات والاشتراكات" />
            <x-admin.nav-item route="admin.revenue.index" icon="chart-bar" label="الإيرادات" />

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-600 uppercase tracking-widest">الدعم والمستخدمون</p>
            <x-admin.nav-item route="admin.tickets.index" icon="support" label="تذاكر الدعم" />
            <x-admin.nav-item route="admin.users.index" icon="users" label="المستخدمون" />
            <x-admin.nav-item route="admin.roles.index" icon="shield-check" label="الصلاحيات" />

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-600 uppercase tracking-widest">إعدادات النظام</p>
            <x-admin.nav-item route="admin.whatsapp.index" icon="whatsapp" label="اتصال الواتساب (سريع)" badge="Live" />
            <x-admin.nav-item route="admin.settings.index" icon="cog" label="إعدادات المنصة" />
            <x-admin.nav-item route="admin.logs.index" icon="clipboard-list" label="سجلات النظام" />
        </nav>

        {{-- User Footer --}}
        <div class="p-3 border-t border-white/[0.06] shrink-0">
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-white/5 transition-colors group">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6366f1&color=fff&size=80" alt="Avatar" class="w-9 h-9 rounded-full shrink-0 ring-2 ring-white/10">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 truncate">Super Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="تسجيل الخروج" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ══════ MAIN CONTENT ══════ --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        {{-- Topbar --}}
        <header class="h-[68px] bg-[#0A0A12]/80 backdrop-blur-xl border-b border-white/[0.06] flex items-center justify-between px-4 sm:px-6 z-10 shrink-0">
            <div>
                <h1 class="text-lg font-black text-white leading-none">{{ $title ?? 'الإدارة العليا' }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ now()->translatedFormat('l، d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-xs font-bold text-indigo-300">النظام يعمل بشكل طبيعي</span>
                </div>
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

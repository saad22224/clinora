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
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</head>
<body class="font-sans antialiased bg-[#F8FAFC] text-slate-900 dark:bg-[#09090b] dark:text-slate-100 flex min-h-screen overflow-hidden selection:bg-indigo-500 selection:text-white">
    
    <!-- Admin Sidebar (Stripe/Notion style - Slate/Indigo theme) -->
    <aside class="w-[280px] bg-slate-900 dark:bg-[#09090b] border-l border-slate-800 dark:border-white/5 flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0 text-slate-300">
        <!-- Admin Branding -->
        <div class="h-[72px] flex items-center px-6 border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-sm text-white">كلينورا للإدارة العليا</h2>
                    <p class="text-xs text-slate-400">Super Admin</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <x-admin.nav-item route="admin.dashboard" icon="home" label="الرئيسية" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">إدارة المنصة</p>
            </div>
            <x-admin.nav-item route="admin.clinics.index" icon="office-building" label="العيادات" />
            <x-admin.nav-item route="admin.subscriptions.index" icon="credit-card" label="الاشتراكات والباقات" />
            <x-admin.nav-item route="admin.revenue.index" icon="chart-bar" label="الإيرادات" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">الدعم والمستخدمين</p>
            </div>
            <x-admin.nav-item route="admin.tickets.index" icon="support" label="تذاكر الدعم" />
            <x-admin.nav-item route="admin.users.index" icon="users" label="المستخدمين" />
            <x-admin.nav-item route="admin.roles.index" icon="shield-check" label="الصلاحيات والأدوار" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">إعدادات النظام</p>
            </div>
            <x-admin.nav-item route="admin.settings.index" icon="cog" label="إعدادات المنصة" />
            <x-admin.nav-item route="admin.logs.index" icon="clipboard-list" label="سجلات النظام (Logs)" />
        </nav>
        
        <!-- User Profile Footer -->
        <div class="p-4 border-t border-slate-800 shrink-0">
            <button class="w-full flex items-center gap-3 p-2 hover:bg-slate-800 rounded-xl transition-colors">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6366f1&color=fff" alt="Avatar" class="w-9 h-9 rounded-full ring-2 ring-slate-800">
                <div class="text-right flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">Super Admin</p>
                </div>
            </button>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        
        <!-- Topbar -->
        <header class="h-[72px] bg-white/80 dark:bg-[#09090b]/80 backdrop-blur-xl border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0">
            <!-- Mobile Menu Toggle -->
            <button class="md:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Title -->
            <div class="flex-1">
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title ?? 'الإدارة' }}</h1>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>

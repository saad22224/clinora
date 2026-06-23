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
        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #4b5563; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50/50 text-gray-900 dark:bg-[#0A0A0A] dark:text-gray-100 flex min-h-screen overflow-hidden selection:bg-brand-500 selection:text-white">
    
    <!-- Clinic Sidebar (Linear/Vercel style) -->
    <aside class="w-[280px] bg-white dark:bg-[#111111] border-l border-gray-200 dark:border-white/5 flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0">
        <!-- Clinic Branding -->
        <div class="h-[72px] flex items-center px-6 border-b border-gray-200 dark:border-white/5 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold shadow-lg shadow-brand-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-sm text-gray-900 dark:text-white truncate max-w-[150px]">{{ Auth::user()->clinic->name ?? 'العيادة' }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">لوحة تحكم العيادة</p>
                </div>
            </div>
            <!-- Quick actions dropdown could go here -->
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <x-clinic.nav-item route="clinic.dashboard" icon="home" label="الرئيسية" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">العمليات الطبية</p>
            </div>
            <x-clinic.nav-item route="clinic.patients.index" icon="users" label="المرضى" badge="3" />
            <x-clinic.nav-item route="clinic.appointments.index" icon="calendar" label="المواعيد" />
            <x-clinic.nav-item route="clinic.prescriptions.index" icon="document-text" label="الوصفات الطبية" />
            <x-clinic.nav-item route="clinic.records.index" icon="folder" label="السجلات الطبية" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">الإدارة والمالية</p>
            </div>
            <x-clinic.nav-item route="clinic.billing.index" icon="credit-card" label="الفواتير" />
            <x-clinic.nav-item route="clinic.reports.index" icon="chart-bar" label="التقارير" />
            <x-clinic.nav-item route="clinic.staff.index" icon="user-group" label="فريق العمل" />
            
            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">النظام</p>
            </div>
            <x-clinic.nav-item route="clinic.settings.index" icon="cog" label="الإعدادات" />
        </nav>
        
        <!-- User Profile Footer -->
        <div class="p-4 border-t border-gray-200 dark:border-white/5 shrink-0">
            <button class="w-full flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-xl transition-colors group">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 dark:border-white/10 shadow-sm">
                    <div class="text-right flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
            </button>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        
        <!-- Topbar -->
        <header class="h-[72px] bg-white/80 dark:bg-[#0A0A0A]/80 backdrop-blur-xl border-b border-gray-200 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0">
            <!-- Mobile Menu Toggle -->
            <button class="md:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Global Search (Linear style) -->
            <div class="flex-1 max-w-xl hidden md:flex items-center">
                <div class="relative w-full group">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" class="block w-full py-2 pr-10 pl-3 bg-gray-100/50 dark:bg-white/5 border-transparent rounded-xl text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:border-brand-500 focus:bg-white dark:focus:bg-[#111] focus:ring-4 focus:ring-brand-500/10 transition-all dark:text-white" placeholder="ابحث عن مريض، موعد، أو فاتورة... (Cmd+K)">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <button class="relative p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-2 right-2.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-[#0A0A0A]"></span>
                </button>
            </div>
        </header>

        <!-- Main Content Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>

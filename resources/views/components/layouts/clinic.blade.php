<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'لوحة العيادة' }} | كلينورا</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { font-family: 'Cairo', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-gray-900 dark:bg-[#0A0A0F] dark:text-gray-100 flex min-h-screen overflow-hidden">

    {{-- ══════ SIDEBAR BACKDROP ON MOBILE ══════ --}}
    <div id="sidebar-backdrop" class="hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden transition-opacity"></div>

    {{-- ══════ SIDEBAR ══════ --}}
    <aside id="sidebar" class="w-[268px] bg-white dark:bg-[#0F0F14] border-l border-gray-100 dark:border-white/[0.06] flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0 shadow-sm transition-all">

        {{-- Brand --}}
        <div class="h-[68px] flex items-center justify-between px-5 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl overflow-hidden shadow-md shrink-0 border border-gray-100 dark:border-white/10 bg-white">
                    <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="Clinora Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white leading-none">{{ optional(app()->bound('tenant') ? app('tenant') : Auth::user()->clinic)->name ?? 'العيادة' }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">لوحة تحكم العيادة</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                {{-- View Public Page Button --}}
                @if(Auth::user()->clinic?->slug)
                <a href="{{ route('clinic.public.show', Auth::user()->clinic->slug) }}" target="_blank"
                   title="عرض الصفحة العامة"
                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 transition-all" >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
                @endif
                {{-- Close Sidebar Button on Mobile --}}
                <button id="sidebar-close" type="button" class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            @php
                $orgForNav = Auth::user()->primaryOrganization();
            @endphp
            @if($orgForNav)
                <a href="{{ route('organization.dashboard', $orgForNav) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-brand-700 bg-brand-50 dark:bg-brand-900/20 dark:text-brand-300 mb-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    لوحة سلسلة العيادات
                </a>
            @endif

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
                <x-clinic.nav-item route="clinic.billing.index" icon="credit-card" label="المالية" />
                <x-clinic.nav-item route="clinic.reports.index" icon="chart-bar" label="التقارير" />
            @endif
            @if(Auth::user()->role !== 'secretary')
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
        @if(session('superadmin_impersonator_id'))
            <div class="bg-amber-500 text-slate-950 px-4 py-2.5 text-xs font-black flex items-center justify-between gap-4 shadow-md z-[1000] shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>أنت تتصفح حالياً لوحة تحكم عيادة: <strong class="underline">{{ optional(app()->bound('tenant') ? app('tenant') : Auth::user()->clinic)->name }}</strong> بصلاحية السوبر أدمن</span>
                </div>
                <a href="{{ route('impersonate.leave') }}" class="px-3.5 py-1.5 bg-slate-950 text-amber-400 hover:bg-slate-900 rounded-xl text-xs font-bold transition-all shadow-sm shrink-0 flex items-center gap-1.5">
                    <span>⬅️ العودة لسوبر أدمن</span>
                </a>
            </div>
        @elseif(session('active_organization_id') && session('active_clinic_id'))
            <div class="bg-brand-600 text-white px-4 py-2.5 text-xs font-black flex items-center justify-between gap-4 shadow-md z-[1000] shrink-0">
                <span>أنت تدير عيادة ضمن سلسلة العيادات</span>
                <form method="POST" action="{{ route('organization.clinics.leave', session('active_organization_id')) }}">
                    @csrf
                    <button class="px-3.5 py-1.5 bg-white text-brand-700 rounded-xl text-xs font-bold">العودة للوحة السلسلة</button>
                </form>
            </div>
        @endif

        {{-- Topbar --}}
        @php
            $unreadNotifications = Auth::user()?->clinic ? \App\Models\ClinicNotification::where('clinic_id', Auth::user()->clinic->id)->where('is_read', false)->latest()->get() : collect();
            $unreadCount = $unreadNotifications->count();
        @endphp
        <header class="h-[68px] bg-white/90 dark:bg-[#0F0F14]/90 backdrop-blur-xl border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between px-4 sm:px-6 z-[999] shrink-0 relative">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="md:hidden w-8 h-8 rounded-lg overflow-hidden shadow shrink-0 border border-gray-100 dark:border-white/10 bg-white">
                    <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="Clinora Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $title ?? 'لوحة التحكم' }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l، d F Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Theme Toggle Button --}}
                <button onclick="toggleTheme()" type="button" title="تغيير المظهر (ليلي / نهاري)" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5 block dark:hidden text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                {{-- View public page quick button --}}
                @if(Auth::user()->clinic?->slug)
                <a href="{{ route('clinic.public.show', Auth::user()->clinic->slug) }}" target="_blank"
                   class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-xl border border-brand-200 text-brand-600 hover:bg-brand-50 dark:border-brand-700/40 dark:text-brand-400 dark:hover:bg-brand-900/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    عرض الصفحة العامة
                </a>
                @endif

                {{-- Notifications Dropdown (Vanilla JS) --}}
                <div class="relative" id="notif-container">
                    <button id="notif-toggle-btn" type="button" title="الإشعارات والتنبيهات" class="relative p-2.5 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-500 text-[8px] font-black text-white ring-2 ring-white dark:ring-[#0F0F14] animate-pulse">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="notif-dropdown-menu" 
                         class="hidden absolute left-0 mt-2 w-80 sm:w-96 bg-white dark:bg-[#141420] rounded-2xl shadow-2xl border border-gray-100 dark:border-white/[0.08] z-[9999] overflow-hidden transition-all">
                        
                        <div class="p-3.5 bg-gray-50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-gray-900 dark:text-white">إشعارات المنصة</span>
                                @if($unreadCount > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-red-500/10 text-red-500 dark:text-red-400 font-bold text-[10px]">{{ $unreadCount }} جديد</span>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <form method="POST" action="{{ route('clinic.notifications.mark-all-read') }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">
                                        تحديد الكل كمقروء ✔️
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-white/[0.04]">
                            @if($unreadCount === 0)
                                <div class="p-8 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">لا توجد إشعارات غير مقروءة</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">أنت على علم بكل التحديثات والتنبيهات.</p>
                                </div>
                            @else
                                @foreach($unreadNotifications as $notif)
                                    <div class="p-3.5 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors flex gap-3 items-start relative group">
                                        <div class="shrink-0 mt-0.5">
                                            @switch($notif->type)
                                                @case('success')
                                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    @break
                                                @case('warning')
                                                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    </div>
                                                    @break
                                                @case('danger')
                                                    <div class="w-8 h-8 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 dark:text-red-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                    @break
                                                @default
                                                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 dark:text-blue-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                            @endswitch
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $notif->title }}</h4>
                                                <span class="text-[10px] text-gray-400 shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed break-words">{{ $notif->message }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('clinic.notifications.read', $notif->id) }}" class="shrink-0 self-center">
                                            @csrf
                                            <button type="submit" title="تحديد كمقروء وإخفاء" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="p-2.5 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/[0.06] text-center">
                            <a href="{{ route('clinic.notifications.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors block py-1">
                                عرض صفحة جميع التنبيهات ←
                            </a>
                        </div>
                    </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifBtn = document.getElementById('notif-toggle-btn');
            const notifMenu = document.getElementById('notif-dropdown-menu');

            if (notifBtn && notifMenu) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
                        notifMenu.classList.add('hidden');
                    }
                });
            }

            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');

            function openSidebar() {
                if (!sidebar) return;
                sidebar.classList.remove('hidden');
                sidebar.classList.add('fixed', 'inset-y-0', 'right-0', 'z-50', 'shadow-2xl', 'flex');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                if (!sidebar) return;
                sidebar.classList.add('hidden');
                sidebar.classList.remove('fixed', 'inset-y-0', 'right-0', 'z-50', 'shadow-2xl');
                if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
            if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
            if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);
        });
    </script>
</body>
</html>

@props(['organization', 'title' => 'لوحة السلسلة'])
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | كلينورا</title>
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

    <div id="sidebar-backdrop" class="hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden transition-opacity"></div>

    <aside id="sidebar" class="w-[268px] bg-white dark:bg-[#0F0F14] border-l border-gray-100 dark:border-white/[0.06] flex flex-col shrink-0 z-20 hidden md:flex h-screen sticky top-0 shadow-sm transition-all">
        <div class="h-[68px] flex items-center justify-between px-5 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl overflow-hidden shadow-md shrink-0 border border-gray-100 dark:border-white/10 bg-white">
                    <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="Clinora Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h2 class="font-black text-sm text-gray-900 dark:text-white leading-none truncate max-w-[140px]">{{ $organization->name }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">لوحة سلسلة العيادات</p>
                </div>
            </div>
            <button id="sidebar-close" type="button" class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            @php
                $dashboardActive = request()->routeIs('organization.dashboard');
                $reportsActive = request()->routeIs('organization.reports.*');
                $sidebarClinics = $organization->clinics()->orderBy('name')->get(['id', 'name', 'is_active']);
                $orgMembership = request()->attributes->get('organization_membership')
                    ?? $organization->memberships()->where('user_id', Auth::id())->first();
            @endphp

            <a href="{{ route('organization.dashboard', $organization) }}" @class([
                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group',
                'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-bold' => $dashboardActive,
                'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' => ! $dashboardActive,
            ])>
                <svg class="w-5 h-5 {{ $dashboardActive ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>لوحة التحكم</span>
            </a>

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest">الإدارة والمالية</p>

            <a href="{{ route('organization.reports.index', $organization) }}" @class([
                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group',
                'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-bold' => $reportsActive,
                'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' => ! $reportsActive,
            ])>
                <svg class="w-5 h-5 {{ $reportsActive ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>تقارير السلسلة</span>
            </a>

            <p class="px-3 pt-5 pb-2 text-[10px] font-black text-gray-400 dark:text-gray-600 uppercase tracking-widest">عيادات السلسلة</p>

            @forelse($sidebarClinics as $sidebarClinic)
                @php
                    $clinicActive = request()->routeIs('organization.clinics.show')
                        && (int) optional(request()->route('clinic'))->id === (int) $sidebarClinic->id;
                @endphp
                <a href="{{ route('organization.clinics.show', [$organization, $sidebarClinic]) }}" @class([
                    'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group',
                    'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 font-bold' => $clinicActive,
                    'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' => ! $clinicActive,
                ]) title="عرض تفاصيل وتقارير العيادة">
                    <span @class([
                        'w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black shrink-0',
                        'bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300' => $clinicActive,
                        'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400' => ! $clinicActive,
                    ])>
                        {{ mb_substr($sidebarClinic->name, 0, 1) }}
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="block truncate font-bold">{{ $sidebarClinic->name }}</span>
                        <span class="block text-[10px] {{ $sidebarClinic->is_active ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ $sidebarClinic->is_active ? 'تفاصيل وتقارير' : 'معطلة' }}
                        </span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 {{ $clinicActive ? 'text-brand-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا توجد عيادات بعد.</p>
            @endforelse
        </nav>

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

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <header class="h-[68px] bg-white/90 dark:bg-[#0F0F14]/90 backdrop-blur-xl border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between px-4 sm:px-6 z-[999] shrink-0 relative">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="md:hidden w-8 h-8 rounded-lg overflow-hidden shadow shrink-0 border border-gray-100 dark:border-white/10 bg-white">
                    <img src="{{ asset('assets/Gemini_Generated_Image_7rtamw7rtamw7rta.png') }}" alt="Clinora Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $title }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l، d F Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="toggleTheme()" type="button" title="تغيير المظهر (ليلي / نهاري)" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5 block dark:hidden text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                @if(session('success'))
                    <div data-flash class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-600 dark:text-red-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="font-semibold">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'كلينورا - النظام السحابي الرائد لإدارة العيادات الطبية' }}">
    
    <title>{{ $title ?? 'كلينورا' }} | نظام إدارة العيادات</title>

    <!-- SEO & Open Graph -->
    <meta property="og:title" content="{{ $title ?? 'كلينورا' }} | نظام إدارة العيادات">
    <meta property="og:description" content="{{ $description ?? 'كلينورا - النظام السحابي الرائد لإدارة العيادات الطبية' }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <header class="sticky top-0 z-50 glass dark:glass-dark border-b border-gray-200 dark:border-gray-800 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-2xl tracking-tight">كلينورا</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-8 space-x-reverse">
                    <a href="{{ route('home') }}" class="text-base font-medium text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">الرئيسية</a>
                    <a href="{{ route('pricing') }}" class="text-base font-medium text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">الأسعار</a>
                    <a href="{{ route('about') }}" class="text-base font-medium text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">من نحن</a>
                    <a href="{{ route('contact') }}" class="text-base font-medium text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">اتصل بنا</a>
                </nav>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-brand-400 transition-colors">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all bg-brand-600 rounded-xl hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                        ابدأ مجاناً
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8">
                <div class="space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-2xl tracking-tight">كلينورا</span>
                    </a>
                    <p class="text-gray-500 dark:text-gray-400 text-base leading-relaxed">نُمكّن العيادات من تقديم رعاية صحية أفضل من خلال حلول تقنية سحابية حديثة، آمنة، وقابلة للتوسع.</p>
                </div>
                
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">المنتج</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}#features" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">المميزات</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">الأسعار</a></li>
                        <li><a href="#" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">الأمان والخصوصية</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">الشركة</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('about') }}" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">من نحن</a></li>
                        <li><a href="{{ route('contact') }}" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">اتصل بنا</a></li>
                        <li><a href="#" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">المدونة</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">الشؤون القانونية</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('privacy') }}" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">سياسة الخصوصية</a></li>
                        <li><a href="{{ route('terms') }}" class="text-base text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">شروط الاستخدام</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800 text-center md:flex md:justify-between md:items-center">
                <p class="text-base text-gray-400">&copy; {{ date('Y') }} كلينورا. جميع الحقوق محفوظة.</p>
                <div class="flex justify-center space-x-6 space-x-reverse mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">تويتر (X)</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

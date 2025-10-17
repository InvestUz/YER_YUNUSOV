<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toshkent Invest - ERP Tizimi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        /* Global Font Enforcement */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            color: #374151 !important;
        }

        /* Headings */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            line-height: 1.3 !important;
            color: #1f2937 !important;
        }

        h1 {
            font-size: 24px !important;
        }

        h2 {
            font-size: 20px !important;
        }

        h3 {
            font-size: 18px !important;
        }

        h4 {
            font-size: 16px !important;
        }

        h5 {
            font-size: 14px !important;
        }

        h6 {
            font-size: 12px !important;
        }

        /* Paragraphs */
        p {
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #4b5563 !important;
        }

        /* Links */
        a {
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            text-decoration: none !important;
        }

        /* Buttons */
        button, .btn, input[type="submit"], input[type="button"] {
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
        }

        /* Inputs */
        input, textarea, select {
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            color: #374151 !important;
        }

        /* Tables */
        table, th, td {
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
        }

        th {
            font-weight: 600 !important;
            color: #1f2937 !important;
        }

        td {
            color: #4b5563 !important;
        }

        /* Labels */
        label {
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #374151 !important;
        }

        /* Small text */
        small, .text-xs, .text-sm {
            font-family: 'Inter', sans-serif !important;
        }

        /* Ensure spans inherit */
        span {
            font-family: 'Inter', sans-serif !important;
        }

        /* Divs */
        div {
            font-family: 'Inter', sans-serif !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
         
            <div>
                <div class="w-40 d-flex m-auto"><img src="https://toshkentinvest.uz/assets/frontend/tild6238-3031-4265-a564-343037346231/tic_logo_blue.png" alt=""></div>
                <p class="text-xs text-gray-500">Yerlarni monitoring qilish tizimi</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1">
            <!-- Monitoring -->
            <a href="{{ route('monitoring.report1') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('monitoring.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span>Мониторинг</span>
            </a>

            <!-- Lots -->
            <a href="{{ route('lots.index') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('lots.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <span>Лотлар</span>
            </a>

            <!-- Dashboard / Infographics -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span>Инфографика</span>
            </a>
        </nav>

        <!-- User Info -->
        @auth
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                        <span class="text-sm font-semibold text-blue-600">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Чиқиш</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
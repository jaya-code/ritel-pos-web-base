<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Cashier</title>
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        /* Dark Mode Automatic Overrides */
        .dark body,
        .dark .bg-slate-100 {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
        }

        .dark .bg-white {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }

        .dark .bg-slate-50,
        .dark .bg-white\/80 {
            background-color: #0f172a !important;
        }

        .dark .bg-indigo-50 {
            background-color: #312e81 !important;
            color: #a5b4fc !important;
        }

        .dark .text-slate-800,
        .dark .text-slate-900 {
            color: #f8fafc !important;
        }

        .dark .text-slate-600,
        .dark .text-slate-700 {
            color: #cbd5e1 !important;
        }

        .dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        .dark .border-slate-200,
        .dark .border-slate-100,
        .dark .border-gray-50 {
            border-color: #334155 !important;
        }

        .dark .text-indigo-700,
        .dark .text-indigo-600 {
            color: #a5b4fc !important;
        }

        .dark .shadow-sm,
        .dark .shadow-md,
        .dark .shadow-lg,
        .dark .shadow-xl {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.5) !important;
        }

        .dark input,
        .dark textarea,
        .dark select {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }

        .dark input:focus,
        .dark textarea:focus {
            background-color: #0f172a !important;
        }

        /* Custom scrollbar for cart if needed */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen h-[100dvh] overflow-hidden font-sans antialiased text-slate-600">
    <div class="w-full h-full flex flex-col relative bg-white shadow-2xl ring-1 ring-slate-900/5 overflow-hidden">
        <!-- Sidebar Backdrop -->
        <div id="sidebar-backdrop" onclick="toggleSidebar()"
            class="fixed inset-0 bg-slate-900/50 z-40 hidden transition-opacity opacity-0"></div>

        <!-- Sidebar Drawer -->
        <div id="sidebar-drawer"
            class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800">{{ Auth::user()->store->name ?? 'Menu' }}</h2>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-grow p-4 space-y-2">
                <a href="{{ route('pos.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('pos.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 36v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Kasir (POS)
                </a>
                <a href="{{ route('pos.history') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('pos.history') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Transaksi
                </a>
                <a href="{{ route('pos.stock') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('pos.stock') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Input Stok
                </a>
                <a href="{{ route('pos.opname') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('pos.opname') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Stock Opname
                </a>
                <a href="{{ route('kasir.settings.printer') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('kasir.settings.printer') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Printer Settings
                </a>
                <a href="{{ route('closing.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('closing.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Tutup Kasir
                </a>

                <button onclick="installPWA()"
                    class="pwa-install-btn hidden items-center gap-3 px-4 py-3 rounded-xl text-indigo-600 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 font-semibold w-full text-left transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Install Aplikasi
                </button>
                @if (Auth::user()->role === 'admin')
                    <a href="{{ route('dashboard.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard Admin
                    </a>
                @endif
            </nav>
            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Header -->
        <div
            class="flex-none flex justify-between items-center p-4 bg-white/80 backdrop-blur-md border-b border-slate-200 z-30 sticky top-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="p-1 -ml-1 text-slate-600 hover:text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 tracking-tight">
                        {{ Auth::user()->store->name ?? 'R-POS Cashier' }}</h1>
                    <p class="text-xs text-slate-500 font-medium">{{ Carbon\Carbon::now()->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" id="dark-mode-toggle"
                    class="p-2 rounded-full text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <!-- User Profile or other info can go here -->
            </div>
        </div>

        @if (session('success'))
            <div class="flex-none bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative m-4 mb-0"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex-none bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4 mb-0"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex-none bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4 mb-0"
                role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">Something went wrong.</span>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="flex-grow flex flex-col overflow-hidden relative">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('SW registered!', reg);
                }).catch(err => console.log('SW registration failed', err));
            });
        }

        let pwaDeferredPrompt;

        // Listen for the beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            pwaDeferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            const installBtns = document.querySelectorAll('.pwa-install-btn');
            installBtns.forEach(btn => {
                btn.classList.remove('hidden');
                btn.classList.add('flex');
            });
        });

        function installPWA() {
            if (pwaDeferredPrompt) {
                // Show the prompt
                pwaDeferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                pwaDeferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        const installBtns = document.querySelectorAll('.pwa-install-btn');
                        installBtns.forEach(btn => {
                            btn.classList.add('hidden');
                            btn.classList.remove('flex');
                        });
                    }
                    pwaDeferredPrompt = null;
                });
            }
        }

        function toggleSidebar() {
            const drawer = document.getElementById('sidebar-drawer');
            const backdrop = document.getElementById('sidebar-backdrop');

            if (drawer.classList.contains('-translate-x-full')) {
                // Open
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            } else {
                // Close
                drawer.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateThemeToggleIcon();
        }

        function updateThemeToggleIcon() {
            var darkIcon = document.getElementById('theme-toggle-dark-icon');
            var lightIcon = document.getElementById('theme-toggle-light-icon');
            if (document.documentElement.classList.contains('dark')) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeToggleIcon);
    </script>
    @yield('scripts')
</body>

</html>

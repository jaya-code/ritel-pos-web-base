<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - R-POS</title>
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- jQuery (Required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .feather {
            width: 18px;
            height: 18px;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Select2 Custom Styling to match Tailwind */
        .select2-container .select2-selection--single {
            height: 42px;
            border-color: #d1d5db;
            /* gray-300 */
            border-radius: 0.5rem;
            /* rounded-lg */
            padding: 6px 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            /* gray-700 */
            line-height: 28px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-dropdown {
            border-color: #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .select2-search__field {
            border-radius: 0.375rem;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
            class="fixed inset-y-0 left-0 z-30 w-64 transition duration-300 transform bg-gray-900 border-r border-gray-800 md:translate-x-0 md:static md:inset-0 flex flex-col">
            <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30">
                        R
                    </div>
                    <span class="text-xl font-bold text-white tracking-wide">
                        {{ Auth::user()->store ? Auth::user()->store->name : 'POS Admin' }}
                    </span>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="{{ route('dashboard.index') }}"
                    class="flex items-center px-4 py-3 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('dashboard*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : '' }}">
                    <i data-feather="home"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard*') ? 'text-white' : 'group-hover:text-white transition-colors' }}"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <div class="pt-6 pb-2 text-xs font-bold text-gray-500 uppercase tracking-wider pl-4">Master Data</div>

                <a href="{{ route('products.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('products*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="box"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('products*') ? 'text-indigo-400' : 'group-hover:text-indigo-400 transition-colors' }}"></i>
                    <span class="font-medium">Products</span>
                </a>
                <a href="{{ route('categories.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('categories*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="grid"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('categories*') ? 'text-indigo-400' : 'group-hover:text-indigo-400 transition-colors' }}"></i>
                    <span class="font-medium">Categories</span>
                </a>
                <a href="{{ route('suppliers.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('suppliers*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="truck"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('suppliers*') ? 'text-indigo-400' : 'group-hover:text-indigo-400 transition-colors' }}"></i>
                    <span class="font-medium">Suppliers</span>
                </a>

                <div class="pt-6 pb-2 text-xs font-bold text-gray-500 uppercase tracking-wider pl-4">Transactions</div>

                <a href="{{ route('pembelian.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('pembelian*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="shopping-cart"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('pembelian*') ? 'text-emerald-400' : 'group-hover:text-emerald-400 transition-colors' }}"></i>
                    <span class="font-medium">Purchasing</span>
                </a>
                <a href="{{ route('penjualan.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('penjualan*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="shopping-bag"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('penjualan*') ? 'text-emerald-400' : 'group-hover:text-emerald-400 transition-colors' }}"></i>
                    <span class="font-medium">Sales</span>
                </a>
                <a href="{{ route('pos.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('pos*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="monitor"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('pos*') ? 'text-emerald-400' : 'group-hover:text-emerald-400 transition-colors' }}"></i>
                    <span class="font-medium">POS (Cashier)</span>
                </a>
                @if (auth()->user()->role === 'kasir')
                    <a href="{{ route('closing.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('closing*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="lock"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('closing*') ? 'text-emerald-400' : 'group-hover:text-emerald-400 transition-colors' }}"></i>
                        <span class="font-medium">Tutup Kasir</span>
                    </a>
                @endif

                <div class="pt-6 pb-2 text-xs font-bold text-gray-500 uppercase tracking-wider pl-4">Administration
                </div>

                <a href="{{ route('users.index') }}"
                    class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('users*') ? 'bg-gray-800 text-white' : '' }}">
                    <i data-feather="users"
                        class="w-5 h-5 mr-3 {{ request()->routeIs('users*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                    <span class="font-medium">Users</span>
                </a>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('subscription-plans.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('subscription-plans*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="award"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('subscription-plans*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Paket Langganan</span>
                    </a>
                    <a href="{{ route('stores.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('stores*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="map-pin"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('stores*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Stores</span>
                    </a>
                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.withdrawals*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="dollar-sign"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('admin.withdrawals*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Penarikan QRIS</span>
                    </a>
                @endif
                @if (auth()->user()->role === 'owner')
                    <a href="{{ route('settings.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('settings*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="settings"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('settings*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Settings</span>
                    </a>
                    <a href="{{ route('payment-settings.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('payment-settings*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="credit-card"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('payment-settings*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Payment Settings</span>
                    </a>
                    <a href="{{ route('subscription.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('subscription*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="shield"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('subscription*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Berlangganan</span>
                    </a>
                    <a href="{{ route('promos.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('promos*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="tag"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('promos*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Promotions</span>
                    </a>
                    <a href="{{ route('withdrawals.index') }}"
                        class="flex items-center px-4 py-2.5 text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200 group {{ request()->routeIs('withdrawals*') ? 'bg-gray-800 text-white' : '' }}">
                        <i data-feather="dollar-sign"
                            class="w-5 h-5 mr-3 {{ request()->routeIs('withdrawals*') ? 'text-purple-400' : 'group-hover:text-purple-400 transition-colors' }}"></i>
                        <span class="font-medium">Tarik Saldo</span>
                    </a>
                @endif
            </nav>

            <!-- User Profile Bottom -->
            <div class="w-full p-4 border-t border-gray-800 bg-gray-900">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between h-16 bg-white border-b border-gray-200 px-6 shadow-sm">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>

                <!-- Search or Breadcrumbs area -->
                <div class="hidden md:flex items-center text-gray-500 text-sm">
                    <span class="mr-2">Welcome back,</span>
                    <span class="font-bold text-gray-800">{{ Auth::user()->name }}</span>
                </div>

                <div class="flex items-center ml-auto gap-4">
                    @if (auth()->user()->role === 'admin')
                        <div class="relative hidden sm:block">
                            <form action="{{ route('admin.switch_store') }}" method="POST"
                                class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 rounded-lg px-2 py-1">
                                @csrf
                                <label for="store_dropdown"
                                    class="text-xs font-bold text-indigo-700 isolate flex items-center gap-1">
                                    <i data-feather="map-pin" class="w-3 h-3"></i> Filter
                                </label>
                                <select name="store_id" id="store_dropdown" onchange="this.form.submit()"
                                    class="text-sm bg-transparent border-none text-indigo-900 font-medium focus:ring-0 py-0 pl-1 pr-6 cursor-pointer outline-none">
                                    <option value="all" {{ !session('admin_active_store_id') ? 'selected' : '' }}>
                                        All Stores</option>
                                    @foreach (\App\Models\Store::all() as $global_store)
                                        <option value="{{ $global_store->id }}"
                                            {{ session('admin_active_store_id') == $global_store->id ? 'selected' : '' }}>
                                            {{ $global_store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                    @endif

                    <div class="relative">
                        <button class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors">
                            <i data-feather="bell" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800 font-medium transition-colors">
                            <i data-feather="log-out" class="w-4 h-4"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Body -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm"
                        role="alert">
                        <i data-feather="check-circle" class="w-5 h-5 text-emerald-500"></i>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm"
                        role="alert">
                        <i data-feather="alert-circle" class="w-5 h-5 text-red-500"></i>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
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

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            pwaDeferredPrompt = e;
            const installBtns = document.querySelectorAll('.pwa-install-btn');
            installBtns.forEach(btn => {
                btn.classList.remove('hidden');
                btn.classList.add('flex');
            });
        });

        function installPWA() {
            if (pwaDeferredPrompt) {
                pwaDeferredPrompt.prompt();
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

        document.addEventListener('alpine:init', () => {
            // Alpine initialized
        });
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();

            // Initialize Select2 Globally
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
    @yield('scripts')
</body>

</html>

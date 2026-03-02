@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Welcome back,
                    {{ Auth::user()->name ?? 'Admin' }}! 👋</h1>
                <p class="text-gray-500 mt-1">Here's what's happening with your store today.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="installPWA()"
                    class="pwa-install-btn hidden bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all duration-200 font-medium items-center gap-2">
                    <i data-feather="download"></i>
                    Install App
                </button>
                <a href="{{ route('pos.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 hover:shadow-blue-300 transition-all duration-200 font-medium flex items-center gap-2">
                    <i data-feather="monitor"></i>
                    Open POS
                </a>
            </div>
        </div>



        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Sales Today -->
            <div
                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-4">
                        <i data-feather="dollar-sign"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Total Sales Today</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($salesToday, 0, ',', '.') }}</h3>
                    <div
                        class="flex items-center gap-1 text-xs font-medium text-green-600 mt-3 bg-green-50 px-2 py-1 rounded w-fit">
                        <i data-feather="trending-up" class="w-3 h-3"></i>
                        <span>{{ $transactionsToday }} Transactions</span>
                    </div>
                </div>
            </div>

            <!-- Sales Month -->
            <div
                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                        <i data-feather="calendar"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Revenue This Month</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($salesMonth, 0, ',', '.') }}</h3>
                    <div
                        class="flex items-center gap-1 text-xs font-medium text-purple-600 mt-3 bg-purple-50 px-2 py-1 rounded w-fit">
                        <span>{{ $transactionsMonth }} Total Orders</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div
                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                </div>
                <div class="relative">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 mb-4">
                        <i data-feather="alert-triangle"></i>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Low Stock Alerts</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $lowStockProducts->count() }}</h3>
                    <div
                        class="flex items-center gap-1 text-xs font-medium text-red-600 mt-3 bg-red-50 px-2 py-1 rounded w-fit">
                        <span>Items need attention</span>
                    </div>
                </div>
            </div>

            @if (auth()->user()->role === 'owner')
                <!-- QRIS Balance -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div
                        class="absolute right-0 top-0 w-24 h-24 bg-indigo-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                    </div>
                @else
                    <!-- Products (Placeholder) -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div
                            class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                        </div>
                        <div class="relative">
                            <div
                                class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 mb-4">
                                <i data-feather="package"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Inventory System</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">Active</h3>
                            <div
                                class="flex items-center gap-1 text-xs font-medium text-orange-600 mt-3 bg-orange-50 px-2 py-1 rounded w-fit">
                                <a href="{{ route('products.index') }}" class="hover:underline">Manage Products &rarr;</a>
                            </div>
                        </div>
                    </div>
            @endif
        </div>

        <!-- Dashboard Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Sales Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Sales Trend (Last 7 Days)</h2>
                </div>
                <div class="flex-1 w-full h-72">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Top Products Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Top 5 Products</h2>
                </div>
                <div class="flex-1 w-full h-72 flex justify-center">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Recent Transactions -->
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Recent Transactions</h2>
                        <p class="text-sm text-gray-400">Latest 5 sales processed</p>
                    </div>
                    <a href="{{ route('penjualan.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                        View All
                    </a>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/50 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Invoice</th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">Total</th>
                                <th class="px-6 py-4 font-semibold">Method</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentTransactions as $trx)
                                <tr class="hover:bg-gray-50/80 transition-colors group">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $trx->invoice }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $trx->created_at->format('d M, H:i') }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">Rp
                                        {{ number_format($trx->total, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                        {{ $trx->metode_pembayaran == 'Tunai' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-blue-50 text-blue-700 border-blue-100' }}">
                                            {{ $trx->metode_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('penjualan.show', $trx->penjualan_id) }}"
                                            class="text-gray-400 hover:text-blue-600 p-2 hover:bg-blue-50 rounded-lg inline-block transition-all">
                                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gray-50 rounded-full p-4 mb-3">
                                                <i data-feather="inbox" class="w-6 h-6 text-gray-300"></i>
                                            </div>
                                            <p>No transactions found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">Low Stock Alert</h2>
                    <p class="text-sm text-gray-400">Products nearing depletion</p>
                </div>
                <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[400px]">
                    @forelse($lowStockProducts as $product)
                        <div
                            class="flex items-center p-3 rounded-xl hover:bg-red-50/50 transition-colors border border-transparent hover:border-red-100 group">
                            <div
                                class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                                <i data-feather="alert-circle" class="w-5 h-5"></i>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h4
                                    class="text-sm font-semibold text-gray-900 truncate group-hover:text-red-700 transition-colors">
                                    {{ $product->product_name }}</h4>
                                <p class="text-xs text-gray-500">{{ $product->barcode }}</p>
                            </div>
                            <div class="text-right ml-2">
                                <span class="block text-sm font-bold text-red-600">{{ $product->stock + 0 }}</span>
                                <span class="text-xs text-gray-400">Min: {{ $product->stock_min + 0 }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 text-gray-400">
                            <div class="bg-green-50 rounded-full p-4 mb-3">
                                <i data-feather="check" class="w-6 h-6 text-green-500"></i>
                            </div>
                            <p class="font-medium text-gray-600">All good!</p>
                            <p class="text-xs mt-1">No products are low on stock.</p>
                        </div>
                    @endforelse
                </div>
                @if ($lowStockProducts->count() > 0)
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                        <a href="{{ route('products.index') }}"
                            class="block w-full text-center text-sm font-medium text-blue-600 hover:text-blue-700 py-2">
                            Manage Inventory
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if (Auth::user()->role === 'admin')
            <!-- Login Logs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Log Aktivitas Login</h2>
                        <p class="text-sm text-gray-400">10 riwayat login terakhir ke sistem</p>
                    </div>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/50 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-6 py-4 font-semibold">Waktu Login</th>
                                <th class="px-6 py-4 font-semibold">IP Address</th>
                                <th class="px-6 py-4 font-semibold">Browser / Device</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentLogins as $log)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                                                {{ substr($log->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown' }}
                                                </p>
                                                <p class="text-xs text-gray-500 capitalize">{{ $log->user->role ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $log->login_at ? $log->login_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        <span class="truncate block max-w-xs"
                                            title="{{ $log->user_agent }}">{{ $log->user_agent ? \Illuminate\Support\Str::limit($log->user_agent, 40) : '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <p>Belum ada log login yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Line Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: @json($chartData),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });

            // Top Products Doughnut Chart
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            const topProductsChart = new Chart(topProductsCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($topProductsLabels),
                    datasets: [{
                        data: @json($topProductsData),
                        backgroundColor: [
                            '#4f46e5', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
@endsection

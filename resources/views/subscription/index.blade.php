@extends('layouts.admin')

@section('title', 'Berlangganan Aplikasi')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Status Berlangganan</h1>
        <p class="text-gray-500 mt-1">Kelola paket langganan dan pantau masa aktif aplikasi Anda.</p>
    </div>

    <!-- Status Card -->
    <div
        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-full flex items-center justify-center {{ $subscriptionDaysLeft > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                <i data-feather="shield" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    @if ($subscriptionDaysLeft > 0)
                        Aktif
                    @elseif($subscriptionDaysLeft === 0)
                        Hari Terakhir
                    @else
                        Kedaluwarsa
                    @endif
                </h2>
                <p class="text-gray-500 text-sm">
                    @if (auth()->user()->store && auth()->user()->store->subscription_until)
                        Berlaku hingga: <span
                            class="font-medium text-gray-800">{{ \Carbon\Carbon::parse(auth()->user()->store->subscription_until)->format('d M Y') }}</span>
                    @else
                        Belum pernah berlangganan.
                    @endif
                </p>
            </div>
        </div>
        <div class="text-right">
            @if ($subscriptionDaysLeft > 0)
                <span class="text-3xl font-bold text-green-600">{{ $subscriptionDaysLeft }}</span>
                <span class="text-gray-500 block text-sm font-medium">Hari Tersisa</span>
            @else
                <span
                    class="text-red-600 font-bold block bg-red-50 px-4 py-2 rounded-lg border border-red-200 mt-2 flex items-center gap-2">
                    <i data-feather="alert-octagon" class="w-4 h-4"></i> Akses Kasir Terhenti
                </span>
            @endif
        </div>
    </div>

    <!-- Packages -->
    <h2 class="text-xl font-bold text-gray-800 mb-4">Pilih Paket Langganan</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        @forelse($plans as $plan)
            <div
                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden flex flex-col">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">Masa Aktif: {{ $plan->duration_days }} Hari</p>
                </div>
                <div class="mb-6 flex-grow">
                    <span class="text-3xl font-extrabold text-indigo-600">Rp
                        {{ number_format($plan->price, 0, ',', '.') }}</span>
                </div>
                <button onclick="checkout({{ $plan->id }})"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-sm shadow-indigo-200 flex justify-center items-center gap-2">
                    <i data-feather="credit-card" class="w-4 h-4"></i> Beli Paket
                </button>
            </div>
        @empty
            <div
                class="col-span-1 md:col-span-3 text-center py-10 bg-gray-50 rounded-2xl border border-gray-200 border-dashed text-gray-500">
                Saat ini tidak ada paket langganan yang tersedia.
            </div>
        @endforelse
    </div>

    <!-- Transactions History -->
    <h2 class="text-xl font-bold text-gray-800 mb-4">Riwayat Transaksi</h2>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-sans">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold w-12">No</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Paket</th>
                        <th class="px-6 py-4 font-semibold">Nominal</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($transactions as $index => $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $trx->plan->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($trx->status === 'success')
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Berhasil</span>
                                @elseif ($trx->status === 'pending')
                                    <span
                                        class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Menunggu
                                        Pembayaran</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Gagal /
                                        Kadaluarsa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($trx->status === 'pending' && $trx->snap_token)
                                    <button onclick="payWithSnap('{{ $trx->snap_token }}')"
                                        class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded hover:text-indigo-800 hover:bg-indigo-100 font-semibold text-sm transition-colors border border-indigo-200">
                                        Lanjutkan Pembayarant
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">
                                Belum ada riwayat transaksi langganan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Midtrans Snap.js -->
    <script
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        function checkout(planId) {
            fetch('{{ route('subscription.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        plan_id: planId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        payWithSnap(data.snap_token);
                    } else {
                        alert('Gagal mendapatkan token pembayaran.');
                        console.error(data);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan pada server saat memproses transaksi langganan.');
                    console.error('Error in request:', error);
                });
        }

        function payWithSnap(snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    alert("Pembayaran telah berhasil kami proses!");
                    window.location.reload();
                },
                onPending: function(result) {
                    alert("Silahkan selesaikan pembayaran langganan Anda.");
                },
                onError: function(result) {
                    alert("Maaf, proses pembayaran mengalami kegagalan.");
                },
                onClose: function() {
                    alert('Anda menutup dialog pembayaran.');
                }
            });
        }
    </script>
@endsection

@extends('layouts.admin')

@section('title', 'History Penjualan')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">History Penjualan</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Invoice</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Cashier</th>
                        <th class="px-6 py-4 font-semibold">Total</th>
                        <th class="px-6 py-4 font-semibold">Pay</th>
                        <th class="px-6 py-4 font-semibold">Method</th>
                        <th class="px-6 py-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $trx->invoice }}
                                <div class="text-xs text-gray-400 font-mono">{{ $trx->penjualan_id }}</div>
                            </td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($trx->tgl_penjualan)->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">{{ $trx->user->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">Rp {{ number_format($trx->jumlah_uang, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium 
                                {{ $trx->metode_pembayaran == 'Tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('penjualan.show', $trx->penjualan_id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-xs font-medium border border-blue-200">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="inbox" class="w-12 h-12 mb-3 opacity-50"></i>
                                    <p>No transactions found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection

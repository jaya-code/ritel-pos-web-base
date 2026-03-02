@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('penjualan.index') }}" class="text-gray-500 hover:text-gray-700">
            <i data-feather="arrow-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi</h1>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">INVOICE</h2>
                <p class="text-sm text-gray-500 mt-1">#{{ $transaction->invoice }}</p>
            </div>
            <div class="text-right">
                <span
                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase tracking-wide">PAID</span>
                <p class="text-sm text-gray-500 mt-2">
                    {{ \Carbon\Carbon::parse($transaction->tgl_penjualan)->format('d F Y, H:i') }}</p>
            </div>
        </div>

        <!-- Info -->
        <div class="px-8 py-6 grid grid-cols-2 gap-8">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Cashier</p>
                <p class="font-medium text-gray-800">{{ $transaction->user->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Payment Method</p>
                <p class="font-medium text-gray-800">{{ $transaction->metode_pembayaran }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="px-8 py-2">
            <table class="w-full text-left text-sm">
                <thead class="text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-3 font-semibold">Item</th>
                        <th class="py-3 font-semibold text-center">Qty</th>
                        <th class="py-3 font-semibold text-right">Price</th>
                        <th class="py-3 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($transaction->details as $item)
                        <tr>
                            <td class="py-4">
                                <p class="font-medium text-gray-800">{{ $item->product->product_name ?? 'Item Removed' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $item->product->barcode ?? '-' }}</p>
                            </td>
                            <td class="py-4 text-center text-gray-600">{{ $item->qty_jual }}</td>
                            <td class="py-4 text-right text-gray-600">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="py-4 text-right font-medium text-gray-800">Rp
                                {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
            <div class="w-full max-w-xs ml-auto space-y-3">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
                </div>
                <!-- Add Discount here if available -->
                <div class="flex justify-between text-base font-bold text-gray-900 pt-3 border-t border-gray-200">
                    <span>Total</span>
                    <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 pt-1">
                    <span>Tunai (Paid)</span>
                    <span>Rp {{ number_format($transaction->jumlah_uang, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-green-600 font-medium">
                    <span>Kembali (Change)</span>
                    <span>Rp {{ number_format($transaction->uang_kembali, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-100 text-center">
            <button onclick="window.print()"
                class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center justify-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i> Print Invoice
            </button>
        </div>
    </div>
@endsection

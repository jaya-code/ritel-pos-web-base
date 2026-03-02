@extends('layouts.admin')

@section('title', 'Purchase Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Invoice: {{ $pembelian->nofak_beli }}</h1>
                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        {{ $pembelian->status_pembelian }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Date: {{ $pembelian->tgl_beli }}</p>
                    <p class="text-sm text-gray-500">Supplier: <span
                            class="font-bold text-gray-700">{{ $pembelian->supplier->name ?? 'Unknown' }}</span></p>
                    <p class="text-sm text-gray-500">Processed by: {{ $pembelian->user->name ?? 'Unknown' }}</p>
                </div>
            </div>

            <div class="p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Items Purchased</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600">
                                <th class="px-4 py-3 rounded-l-lg">Product</th>
                                <th class="px-4 py-3 text-right">Cost Price</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right rounded-r-lg">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pembelian->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $detail->product->product_name ?? 'Item Deleted' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">Rp
                                        {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">{{ $detail->qty_beli + 0 }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-800">Rp
                                        {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <div class="w-64 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">Total</span>
                            <span class="font-semibold">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 text-lg font-bold text-blue-600">
                            <span>Grand Total</span>
                            <span>Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <a href="{{ route('pembelian.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
                    &larr; Back to History
                </a>
                <button onclick="window.print()"
                    class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
@endsection

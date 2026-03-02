@extends('layouts.admin')

@section('title', 'Incoming Goods (Pembelian)')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Purchase History</h2>
            <a href="{{ route('pembelian.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i data-feather="plus"></i>
                New Purchase
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Invoice</th>
                        <th class="px-6 py-4 font-semibold">Supplier</th>
                        <th class="px-6 py-4 font-semibold">Grand Total</th>
                        <th class="px-6 py-4 font-semibold">User</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembelian as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">{{ $item->tgl_beli }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nofak_beli }}</td>
                            <td class="px-6 py-4">{{ $item->supplier->name ?? '-' }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $item->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('pembelian.edit', $item->pembelian_id) }}"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <a href="{{ route('pembelian.show', $item->pembelian_id) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No purchases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pembelian->links() }}
        </div>
    </div>
@endsection

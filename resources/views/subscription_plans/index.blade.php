@extends('layouts.admin')

@section('title', 'Kelola Paket Langganan')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Paket Langganan</h1>
        <a href="{{ route('subscription-plans.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm shadow-indigo-200">
            <i data-feather="plus" class="w-4 h-4 inline-block mr-1"></i> Tambah Paket
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-sans">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold w-12">No</th>
                        <th class="px-6 py-4 font-semibold">Nama Paket</th>
                        <th class="px-6 py-4 font-semibold">Harga</th>
                        <th class="px-6 py-4 font-semibold">Durasi (Hari)</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($plans as $index => $plan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $plan->name }}</td>
                            <td class="px-6 py-4 text-gray-600">Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $plan->duration_days }} Hari</td>
                            <td class="px-6 py-4 text-center">
                                @if ($plan->is_active)
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('subscription-plans.edit', $plan) }}"
                                        class="p-1.5 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('subscription-plans.destroy', $plan) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition-colors">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">
                                Belum ada paket langganan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

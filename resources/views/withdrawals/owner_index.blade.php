@extends('layouts.admin')

@section('content')
    <div class="container mx-auto max-w-7xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tarik Saldo QRIS Dinamis</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 text-white shadow-lg flex flex-col justify-center">
                <h3 class="text-indigo-100 font-medium mb-1 flex items-center gap-2"><i data-feather="wallet"
                        class="w-4 h-4"></i> Saldo Tersedia</h3>
                <div class="text-3xl font-bold tracking-tight">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                <p class="text-xs text-indigo-200 mt-2">*Sudah dipotong admin fee ({{ $store->qris_fee ?? 0 }}%)</p>
            </div>

            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2"><i data-feather="send"
                        class="w-5 h-5 text-indigo-500"></i> Pengajuan Penarikan Baru</h3>
                @if ($withdrawals->where('status', 'pending')->count() > 0)
                    <div class="text-amber-700 bg-amber-50 rounded-xl p-4 flex items-start gap-3 border border-amber-200">
                        <i data-feather="clock" class="mt-0.5"></i>
                        <div>
                            <p class="font-bold">Pengajuan Sedang Diproses</p>
                            <p class="text-sm mt-1">Anda memiliki pengajuan penarikan yang sedang menunggu konfirmasi Admin.
                                Tunggu hingga selesai sebelum mengajukan yang baru.</p>
                        </div>
                    </div>
                @elseif($balance <= 0)
                    <div class="text-gray-500 bg-gray-50 rounded-xl p-4 flex items-center gap-3 border border-gray-200">
                        <i data-feather="info"></i>
                        Belum ada saldo QRIS yang bisa ditarik.
                    </div>
                @else
                    <form action="{{ route('withdrawals.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nominal
                                    (Rp)</label>
                                <input type="number" name="amount" min="10000" max="{{ $balance }}"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    required placeholder="Min. 10.000">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama
                                    Bank (mis. BCA)</label>
                                <input type="text" name="bank_name"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor
                                    Rekening</label>
                                <input type="text" name="account_number"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Atas
                                    Nama</label>
                                <input type="text" name="account_name"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    required>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium flex items-center gap-2 transition-all">
                                Ajukan Penarikan <i data-feather="arrow-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Riwayat Penarikan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-200">Tanggal</th>
                            <th class="p-4 font-medium border-b border-gray-200">Jumlah</th>
                            <th class="p-4 font-medium border-b border-gray-200">Rekening Tujuan</th>
                            <th class="p-4 font-medium border-b border-gray-200">Status</th>
                            <th class="p-4 font-medium border-b border-gray-200 text-right">Aksi / Info</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($withdrawals as $w)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 whitespace-nowrap">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4 font-bold text-gray-900 border-l border-r border-gray-50 text-center">Rp
                                    {{ number_format($w->amount, 0, ',', '.') }}</td>
                                <td class="p-4">
                                    <div class="font-medium text-gray-900">{{ $w->bank_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $w->account_number }} - {{ $w->account_name }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if ($w->status === 'pending')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                                    @elseif($w->status === 'approved')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Approved</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">Rejected</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if ($w->status === 'approved')
                                        @if ($w->receipt_path)
                                            <a href="{{ asset($w->receipt_path) }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium inline-flex items-center gap-1">
                                                <i data-feather="file-text" class="w-4 h-4"></i> Bukti Transfer
                                            </a>
                                        @endif
                                        @if ($w->admin_note)
                                            <div class="text-xs text-gray-500 mt-1" title="{{ $w->admin_note }}"><i
                                                    data-feather="message-circle" class="w-3 h-3 inline"></i> Note</div>
                                        @endif
                                    @elseif($w->status === 'rejected')
                                        <div class="text-xs text-red-600 max-w-xs truncate ml-auto"
                                            title="{{ $w->admin_note }}">Alasan: {{ $w->admin_note }}</div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">Belum ada riwayat penarikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

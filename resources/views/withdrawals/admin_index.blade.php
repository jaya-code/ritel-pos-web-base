@extends('layouts.admin')

@section('content')
    <div class="container mx-auto max-w-7xl" x-data="{
        approveModalOpen: false,
        rejectModalOpen: false,
        selectedId: null,
    
        openApproveModal(id) {
            this.selectedId = id;
            this.approveModalOpen = true;
        },
    
        openRejectModal(id) {
            this.selectedId = id;
            this.rejectModalOpen = true;
        }
    }">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Penarikan QRIS Toko</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-200">Toko</th>
                            <th class="p-4 font-medium border-b border-gray-200">Tanggal</th>
                            <th class="p-4 font-medium border-b border-gray-200 text-right">Jumlah</th>
                            <th class="p-4 font-medium border-b border-gray-200 border-l">Tujuan Transfer</th>
                            <th class="p-4 font-medium border-b border-gray-200 text-center">Status</th>
                            <th class="p-4 font-medium border-b border-gray-200 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($withdrawals as $w)
                            <tr
                                class="hover:bg-gray-50 transition-colors {{ $w->status === 'pending' ? 'bg-indigo-50/30' : '' }}">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $w->store->name ?? 'Unknown Store' }}</div>
                                    <div class="text-xs text-gray-500">Fee: {{ $w->store->qris_fee ?? 0 }}%</div>
                                </td>
                                <td class="p-4 text-xs whitespace-nowrap">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4 font-bold text-gray-900 border-l border-gray-50 text-right">Rp
                                    {{ number_format($w->amount, 0, ',', '.') }}</td>
                                <td class="p-4 border-l border-gray-50">
                                    <div class="font-medium text-gray-900">{{ $w->bank_name }}</div>
                                    <div class="text-xs text-gray-800">{{ $w->account_number }}</div>
                                    <div class="text-xs text-gray-500 uppercase">{{ $w->account_name }}</div>
                                </td>
                                <td class="p-4 text-center">
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
                                <td class="p-4 text-center">
                                    @if ($w->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openApproveModal({{ $w->id }})"
                                                class="bg-emerald-100 text-emerald-700 p-2 rounded-lg hover:bg-emerald-200 transition-colors"
                                                title="Setujui">
                                                <i data-feather="check" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="openRejectModal({{ $w->id }})"
                                                class="bg-red-100 text-red-700 p-2 rounded-lg hover:bg-red-200 transition-colors"
                                                title="Tolak">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">
                                            @if ($w->status === 'approved' && $w->receipt_path)
                                                <a href="{{ asset($w->receipt_path) }}" target="_blank"
                                                    class="text-indigo-600 hover:underline">Lihat Bukti</a>
                                            @else
                                                Terselesaikan
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400">Belum ada pengajuan penarikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approve Modal -->
        <div x-show="approveModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="approveModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true" @click="approveModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="approveModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="`/admin/withdrawals/${selectedId}/approve`" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i data-feather="check" class="h-6 w-6 text-emerald-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Setujui Penarikan
                                    </h3>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p class="mb-4">Apakah Anda yakin ingin menyetujui penarikan ini? Status akan
                                            berubah menjadi Approved.</p>

                                        <label
                                            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Upload
                                            Bukti Transfer (Opsional)</label>
                                        <input type="file" name="receipt"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mb-4"
                                            accept="image/*">

                                        <label
                                            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Catatan
                                            Admin (Opsional)</label>
                                        <textarea name="admin_note"
                                            class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm" rows="3"
                                            placeholder="Misal: Sudah ditransfer via BCA..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Setujui
                                Transfer</button>
                            <button type="button" @click="approveModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="rejectModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true" @click="rejectModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="rejectModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="`/admin/withdrawals/${selectedId}/reject`" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i data-feather="alert-triangle" class="h-6 w-6 text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Tolak Penarikan
                                    </h3>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p class="mb-4">Apakah Anda yakin ingin menolak penarikan ini? Status akan
                                            berubah menjadi Rejected dan saldo akan dikembalikan ke toko.</p>

                                        <label
                                            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Alasan
                                            Penolakan (Wajib)</label>
                                        <textarea name="admin_note" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 text-sm"
                                            rows="3" required placeholder="Misal: Nomor rekening tidak valid..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Ya,
                                Tolak</button>
                            <button type="button" @click="rejectModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

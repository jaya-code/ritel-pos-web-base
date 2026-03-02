@extends('layouts.cashier')

@section('content')
    <div class="flex flex-col h-full bg-slate-50 relative overflow-hidden">
        <div class="max-w-4xl mx-auto py-8 flex-grow overflow-y-auto w-full px-4 sm:px-0 pb-24">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Tutup Kasir (Closing)</h1>
                    <p class="mt-2 text-sm text-gray-500">Rekap transaksi shift kasir {{ auth()->user()->name }} sejak
                        penutupan
                        terakhir.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200">
                    <div class="flex">
                        <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
                    <div class="flex">
                        <i data-feather="alert-circle" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Input Kasir</h3>
                    <p class="text-sm text-gray-500">Masukkan jumlah uang fisik tunai yang ada di laci kasir saat ini.</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('closing.store') }}" method="POST" id="closingForm"
                        onsubmit="return confirm('Apakah Anda yakin ingin melakukan submit tutup kasir? Pastikan jumlah uang sudah benar.')">
                        @csrf
                        <div class="grid grid-cols-1 gap-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Uang Tunai Fisik (Actual
                                    Cash)</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" id="actual_cash_display" name="actual_cash_display"
                                        class="block w-full rounded-xl border-gray-300 pl-12 pr-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 text-lg font-bold shadow-sm transition-colors"
                                        placeholder="0">
                                    <input type="hidden" id="actual_cash" name="actual_cash" value="0">
                                </div>
                                @error('actual_cash')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="notes" rows="3"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                placeholder="Tulis catatan jika ada selisih uang atau masalah lainnya..."></textarea>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('dashboard.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                                Simpan & Tutup Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const displayInput = document.getElementById('actual_cash_display');
                const hiddenInput = document.getElementById('actual_cash');

                function formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                }

                displayInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '');
                    let numericValue = parseInt(value || '0', 10);

                    this.value = numericValue === 0 ? '' : formatNumber(numericValue);
                    hiddenInput.value = numericValue;
                });
            });
        </script>
    @endpush

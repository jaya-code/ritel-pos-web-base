@extends('layouts.cashier')

@section('content')
    <div class="flex flex-col h-full bg-slate-50 relative overflow-hidden">
        <!-- Toolbar -->
        <div class="flex-none p-4 bg-white border-b border-slate-200 shadow-sm z-10">
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <a href="{{ route('pos.history') }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors whitespace-nowrap {{ !$status ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    All
                </a>
                <a href="{{ route('pos.history', ['status' => 'pending']) }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors whitespace-nowrap {{ $status === 'pending' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    Pending
                </a>
                <a href="{{ route('pos.history', ['status' => 'paid']) }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors whitespace-nowrap {{ $status === 'paid' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    Paid
                </a>
                <a href="{{ route('pos.history', ['status' => 'cancelled']) }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors whitespace-nowrap {{ $status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    Cancelled
                </a>
            </div>
        </div>

        <!-- List -->
        <div class="flex-grow overflow-y-auto p-4 pb-24 space-y-4 max-w-3xl mx-auto w-full">
            @forelse ($transactions as $transaction)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 relative">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-slate-800">{{ $transaction->invoice }}</h3>
                            <p class="text-xs text-slate-500">
                                {{ $transaction->tgl_penjualan->setTimezone('Asia/Makassar')->format('d M Y, H:i') }}</p>
                        </div>
                        <span
                            class="px-2 py-1 rounded text-xs font-bold uppercase
                            @if ($transaction->status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($transaction->status === 'pending') bg-amber-100 text-amber-700
                            @elseif($transaction->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-700 @endif">
                            {{ $transaction->status }}
                        </span>
                    </div>

                    <div class="space-y-1 mb-3">
                        @foreach ($transaction->details->take(3) as $detail)
                            <div class="flex justify-between text-sm">
                                <span
                                    class="text-slate-600 line-clamp-1">{{ $detail->product->product_name ?? 'Unknown item' }}</span>
                                <span class="text-slate-500 whitespace-nowrap">x{{ $detail->qty_jual }}</span>
                            </div>
                        @endforeach
                        @if ($transaction->details->count() > 3)
                            <p class="text-xs text-slate-400 italic">+ {{ $transaction->details->count() - 3 }} more items
                            </p>
                        @endif
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-slate-50">
                        <div class="text-lg font-bold text-slate-800">
                            Rp {{ number_format($transaction->total, 0, ',', '.') }}
                        </div>

                        @if ($transaction->status === 'pending')
                            <form action="{{ route('pos.cancel', $transaction->penjualan_id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to cancel this transaction? Stock will be restored.');">
                                @csrf
                                <button type="submit"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            </form>
                        @endif
                        <button onclick="printReceipt('{{ $transaction->penjualan_id }}')"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors flex items-center gap-1">
                            <i data-feather="printer" class="w-3 h-3"></i> Cetak
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-64 text-slate-400 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 opacity-20" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <p class="text-sm font-medium">No transactions found</p>
                </div>
            @endforelse

            <div class="pt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Print Logic -->
    <script>
        const printMethod = '{{ Auth::user()->print_method ?? 'web' }}';

        function printReceipt(receiptId) {
            if (!receiptId) return;

            const receiptUrl = '{{ url('/pos/receipt') }}/' + receiptId + '/json';

            if (printMethod === 'mate_bluetooth' || printMethod === 'android_mate') {
                window.location.href = 'my.bluetoothprint.scheme://' + receiptUrl;
            } else if (printMethod === 'mate_bluetooth_ios' || printMethod === 'ios_bprint') {
                let iosUrl = 'bprint://' + receiptUrl;
                console.log('Redirecting to iOS BPrint:', iosUrl);
                window.location.href = iosUrl;
            } else {
                const fallbackUrl = '{{ url('/pos/receipt') }}/' + receiptId + '/print';
                window.open(fallbackUrl, '_blank');
            }
        }
    </script>
@endsection

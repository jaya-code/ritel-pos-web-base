@extends('layouts.cashier')

@section('content')
    @if (!$activeShift)
        <!-- Shift Modal Overlay -->
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="bg-indigo-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mulai Shift Kasir
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-slate-600 mb-6 text-sm leading-relaxed">
                        Silakan masukkan jumlah <strong>Modal Awal / Uang Kembalian</strong> yang ada di laci kasir saat ini
                        sebelum Anda mulai melakukan transaksi Point of Sale.
                    </p>
                    <form action="{{ route('closing.open') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label for="opening_cash_display" class="block text-sm font-semibold text-slate-700 mb-2">Modal
                                Awal Laci (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-medium pb-0.5">Rp</span>
                                </div>
                                <input type="text" id="opening_cash_display"
                                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-lg text-slate-800"
                                    placeholder="0" required autofocus>
                                <input type="hidden" name="opening_cash" id="opening_cash" value="0">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Buka Shift &amp; Mulai Transaksi</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const displayInput = document.getElementById('opening_cash_display');
                const hiddenInput = document.getElementById('opening_cash');
                if (displayInput) {
                    displayInput.addEventListener('input', function(e) {
                        let value = this.value.replace(/\D/g, '');
                        let numericValue = parseInt(value || '0', 10);
                        this.value = numericValue === 0 ? '' : new Intl.NumberFormat('id-ID').format(
                            numericValue);
                        hiddenInput.value = numericValue;
                    });
                }
            });
        </script>
    @endif

    <!-- Main Split-Pane Container -->
    <div class="flex flex-col lg:flex-row h-full w-full overflow-hidden">

        <!-- Left Pane: Scanner & Search -->
        <div
            class="w-full h-auto max-h-[55vh] lg:max-h-none lg:h-auto lg:w-7/12 xl:w-8/12 flex flex-col border-b lg:border-b-0 lg:border-r border-slate-200 bg-white shadow-[0_4px_15px_-3px_rgba(0,0,0,0.05)] z-20">
            <!-- Scanner Section -->
            <div class="flex-none p-4 pb-0 lg:pb-4 bg-white z-10">
                <!-- Scan Toggle -->
                <div class="mb-3">
                    <button type="button" id="start-scan-btn"
                        class="w-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 border border-indigo-200 py-2.5 rounded-xl font-semibold flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Scan Barcode
                    </button>
                    <div id="scanner-container"
                        class="hidden mt-3 mx-auto w-full max-w-sm lg:max-w-md relative rounded-xl overflow-hidden shadow-lg ring-1 ring-black/5 bg-black">
                        <div id="reader" class="w-full"></div>
                        <button type="button" id="stop-scan-btn"
                            class="absolute top-3 right-3 bg-red-500/80 hover:bg-red-600 text-white rounded-full p-2 backdrop-blur-sm transition-colors z-20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex gap-2">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="barcode-input"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder-slate-400 text-sm font-medium"
                            placeholder="Search product or enter barcode..." autofocus>
                    </div>
                    <button type="button" id="add-btn"
                        class="bg-slate-800 text-white dark:bg-indigo-600 dark:hover:bg-indigo-700 px-5 rounded-xl font-medium hover:bg-slate-900 transition-colors active:scale-95">
                        Add
                    </button>
                </div>
            </div>

            <!-- Promotions Viewer Desktop -->
            <div class="hidden lg:flex flex-col flex-grow overflow-y-auto bg-slate-50/50 border-t border-slate-100">
                <div class="p-4">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg>
                        Active Promotions
                    </h3>
                    @if (count($promos) > 0)
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                            @foreach ($promos as $promo)
                                <div
                                    class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col">
                                    <div
                                        class="absolute top-0 left-0 w-1 h-full {{ $promo->type == 'bundle' ? 'bg-amber-400' : ($promo->type == 'buy_x_get_y' ? 'bg-emerald-400' : 'bg-blue-400') }}">
                                    </div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $promo->name }}</h4>
                                        <span
                                            class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase tracking-wider whitespace-nowrap ml-2">
                                            {{ str_replace('_', ' ', $promo->type) }}
                                        </span>
                                    </div>
                                    @if ($promo->description)
                                        <p class="text-xs text-slate-500 mb-3">{{ $promo->description }}</p>
                                    @endif

                                    <div
                                        class="mt-auto pt-3 border-t border-slate-50 text-[11px] font-medium text-slate-700">
                                        @if ($promo->type == 'buy_x_get_y')
                                            Beli <strong>{{ $promo->buy_qty }}</strong> x
                                            {{ $promo->product->product_name ?? 'Produk' }} <br>
                                            <span class="text-emerald-600 mt-1 inline-block">Gratis
                                                <strong>{{ $promo->get_qty }}</strong> x
                                                {{ $promo->rewardProduct->product_name ?? ($promo->product->product_name ?? 'Produk') }}!</span>
                                        @elseif($promo->type == 'bundle')
                                            Beli <strong>{{ $promo->buy_qty }}</strong> x
                                            {{ $promo->product->product_name ?? 'Produk' }} <br>
                                            <span class="text-amber-600 mt-1 inline-block">Harga Spesial: <strong>Rp
                                                    {{ number_format($promo->bundle_price, 0, ',', '.') }}</strong> per
                                                Item!</span>
                                        @elseif($promo->type == 'simple_discount')
                                            Diskon {{ $promo->product->product_name ?? 'Produk' }} <br>
                                            <span class="text-blue-600 mt-1 inline-block">
                                                Potongan
                                                <strong>{{ $promo->discount_type == 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-slate-200 border-dashed p-8 text-center bg-slate-50/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto text-slate-300 mb-3"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <p class="text-sm text-slate-500 font-medium">Belum ada promosi aktif.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Pane: Cart & Checkout -->
        <div class="w-full lg:w-5/12 xl:w-4/12 flex flex-col bg-slate-50 relative flex-grow overflow-hidden">
            <!-- Cart Control Bar -->
            <div class="flex-none px-4 py-2 bg-white border-b border-gray-50 flex justify-between items-center z-10">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Current Order</h2>
                <div class="flex items-center gap-4">
                    <!-- Member Selection -->
                    <div class="relative">
                        <input type="hidden" id="selected-member-id" value="">
                        <button onclick="openMemberSearchModal()" id="member-select-btn"
                            class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg border border-indigo-200 text-xs font-bold hover:bg-indigo-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span id="member-name-display">Select Member</span>
                        </button>
                        <button id="remove-member-btn" onclick="removeMember()"
                            class="hidden absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <button onclick="saveHeldCart()"
                        class="group text-[10px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-600 hover:text-amber-700 px-3 py-1.5 rounded-lg transition-colors border border-amber-200">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            HOLD
                        </span>
                    </button>
                    <button onclick="openHeldCartsModal()"
                        class="group text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-700 px-3 py-1.5 rounded-lg transition-colors relative border border-slate-200">
                        RECALL
                        <span id="held-count-badge"
                            class="hidden absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] px-1.5 min-w-[16px] h-4 rounded-full flex items-center justify-center shadow-sm">0</span>
                    </button>
                </div>
            </div>

            <!-- Cart List -->
            <div id="cart-container" class="flex-grow overflow-y-auto px-4 py-4 space-y-3 bg-slate-50">
                <!-- Cart Items Injected Here -->
                <div class="h-full flex flex-col items-center justify-center text-slate-400" id="empty-cart-msg">
                    <div class="w-16 h-16 bg-slate-200/50 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-40" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium">Cart is empty</p>
                    <p class="text-xs opacity-75">Scan product to begin</p>
                </div>
            </div>

            <!-- Footer Total & Checkout -->
            <div class="flex-none bg-white border-t border-slate-200 p-4 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] z-40">
                <div class="flex justify-between items-end mb-4">
                    <div class="w-full flex justify-between items-end gap-4">
                        <div class="flex-grow">
                            <label for="discount-input"
                                class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Discount
                                (Rp)</label>
                            <input type="number" id="discount-display-input"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                                placeholder="0" min="0" oninput="updateTotal()">
                        </div>
                        <div class="text-right min-w-[150px]">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Amount</span>
                            <div class="text-3xl font-bold text-slate-800 tracking-tight leading-none mt-1"
                                id="grand-total">Rp 0
                            </div>
                        </div>
                    </div>
                </div>
                <button id="checkout-init-btn" disabled
                    class="w-full bg-indigo-600 text-white py-3.5 rounded-xl font-bold text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transition-all flex items-center justify-center gap-2">
                    <span>Process Payment</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden form for submission -->
    <form id="transaction-form" action="{{ route('pos.store') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="cart_data" id="cart-data-input">
        <input type="hidden" name="amount_paid" id="amount-paid-input">
        <input type="hidden" name="payment_method" id="payment-method-input">
        <!-- is_member is now determined by member_id existence or can still be a flag if needed -->
        <input type="hidden" name="member_id" id="member-id-input">
        <input type="hidden" name="discount" id="discount-input" value="0">
    </form>

    <!-- Member Search Modal -->
    <div id="memberSearchModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeMemberSearchModal()">
        </div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md w-full border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold leading-6 text-slate-800">Select Member</h3>
                            <button onclick="closeMemberSearchModal()"
                                class="text-slate-400 hover:text-slate-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="relative mb-4">
                            <input type="text" id="member-search-input"
                                class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder-slate-400 text-sm font-medium"
                                placeholder="Search by Name or Phone...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div id="member-search-results" class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            <!-- Results injected here -->
                            <p class="text-center text-slate-400 text-sm py-4">Type to search members...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="productSelectionModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-bold leading-6 text-slate-800 mb-4" id="modal-title">Select
                                    Product</h3>
                                <div class="mt-2 text-sm text-slate-500 text-left">
                                    <div id="product-selection-list"
                                        class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                                        <!-- Items injected here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                        <button type="button" onclick="closeModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all active:scale-95">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closePaymentModal()">
        </div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md w-full border border-slate-100">

                    <!-- Modal Header -->
                    <div class="bg-indigo-600 px-4 py-4 sm:px-6">
                        <h3 class="text-lg font-bold leading-6 text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-100" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            Confirm Payment
                        </h3>
                        <p class="text-indigo-100 text-xs mt-1">Complete the transaction below</p>
                    </div>

                    <div class="px-4 py-5 sm:p-6 space-y-5">
                        <!-- Total Display -->
                        <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                            <span class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total
                                Amount</span>
                            <div class="text-3xl font-bold text-slate-800 tracking-tight" id="payment-modal-total">Rp 0
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Payment Method</label>
                                <input type="hidden" id="payment-method" value="Tunai">
                                <div class="grid grid-cols-2 gap-3" id="payment-method-grid">
                                    @php
                                        $enabledMethods = $paymentConfig['enabled_methods'] ?? ['Tunai'];
                                    @endphp

                                    @if (in_array('Tunai', $enabledMethods))
                                        <button type="button"
                                            class="payment-option-card flex flex-col items-center justify-center p-3 rounded-xl border transition-all relative overflow-hidden group active:scale-95 bg-white border-slate-200 text-slate-600 hover:bg-slate-50"
                                            data-value="Tunai">
                                            <div class="mb-1 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold">Tunai</span>
                                            <div class="active-indicator hidden absolute top-2 right-2 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif

                                    @if (in_array('Qris Statis', $enabledMethods))
                                        <button type="button"
                                            class="payment-option-card flex flex-col items-center justify-center p-3 rounded-xl border transition-all relative overflow-hidden group active:scale-95 bg-white border-slate-200 text-slate-600 hover:bg-slate-50"
                                            data-value="Qris Statis">
                                            <div class="mb-1 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold">Qris Statis</span>
                                            <div class="active-indicator hidden absolute top-2 right-2 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif

                                    @if (in_array('Qris Dinamis', $enabledMethods))
                                        <button type="button"
                                            class="payment-option-card flex flex-col items-center justify-center p-3 rounded-xl border transition-all relative overflow-hidden group active:scale-95 bg-white border-slate-200 text-slate-600 hover:bg-slate-50"
                                            data-value="Qris Dinamis">
                                            <div class="mb-1 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold">Qris Dinamis</span>
                                            <div class="active-indicator hidden absolute top-2 right-2 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif

                                    @if (in_array('Debit', $enabledMethods))
                                        <button type="button"
                                            class="payment-option-card flex flex-col items-center justify-center p-3 rounded-xl border transition-all relative overflow-hidden group active:scale-95 bg-white border-slate-200 text-slate-600 hover:bg-slate-50"
                                            data-value="Debit">
                                            <div class="mb-1 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold">Debit</span>
                                            <div class="active-indicator hidden absolute top-2 right-2 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif
                                </div>
                                <div id="qris-static-container" class="mt-4 hidden text-center">
                                    <p class="text-sm font-semibold text-slate-600 mb-2">Scan QRIS to Pay</p>
                                    <img id="qris-static-image" src="" alt="QRIS Static"
                                        class="mx-auto rounded-xl shadow-md max-h-48 border border-white">
                                </div>
                                <div id="qris-dynamic-info"
                                    class="mt-4 hidden text-center bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                    <p class="text-sm text-indigo-700">Admin Fee: <span id="qris-fee-display"
                                            class="font-bold"></span></p>
                                    <p class="text-xs text-indigo-500 mt-1">Total to Pay: <span id="qris-total-display"
                                            class="font-bold text-lg"></span></p>
                                </div>
                            </div>

                            <div id="amount-paid-container" class="hidden">
                                <label for="amount-paid" class="block text-sm font-semibold text-slate-700 mb-1.5">Amount
                                    Paid (Bayar)</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">Rp</span>
                                    <input type="number" id="amount-paid"
                                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800 placeholder-slate-300"
                                        placeholder="0">
                                </div>
                                <div id="cash-suggestions" class="flex flex-wrap gap-2 mt-2 hidden"></div>
                            </div>

                            <div id="change-container" class="hidden">
                                <div
                                    class="flex justify-between items-center bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
                                    <span class="text-sm font-semibold text-slate-600">Change (Kembali)</span>
                                    <div class="text-xl font-bold text-emerald-600" id="payment-change">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div
                        class="bg-slate-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="button" id="confirm-payment-btn"
                            class="w-full inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl transition-all sm:ml-3 sm:w-auto active:scale-95">
                            Pay Now
                        </button>
                        <button type="button" onclick="closePaymentModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all sm:mt-0 sm:w-auto active:scale-95">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Held Carts Modal -->
    <div id="heldCartsModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeHeldCartsModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Held Transactions</h3>
                            <div id="held-carts-list" class="space-y-2 max-h-60 overflow-y-auto">
                                <!-- Held carts injected here -->
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="closeHeldCartsModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @php
        $isProduction = config('services.midtrans.is_production');
        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $snapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        const POS_CART_KEY = 'pos_cart_v1';
        const POS_HELD_KEY = 'pos_held_v1';
        let cart = [];
        let heldCarts = [];
        let html5QrCode;
        let isScanning = false;
        let currentTotal = 0;

        // Injected vars
        const qrisFeePercent = {{ $qrisFee }};
        const qrisStaticImage =
            "{{ isset($paymentConfig['qris_static_image']) ? asset('storage/' . $paymentConfig['qris_static_image']) : '' }}";

        // Helper Functions
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function addToCart(product) {
            const isBaseUnit = product.is_base_unit !== false;
            const unitId = isBaseUnit ? null : product.selected_unit.id;
            const multiplier = isBaseUnit ? 1 : product.selected_unit.quantity;

            const existingItem = cart.find(item => item.id === product.id && item.unit_id === unitId);

            // Allow negative stock (removed product.stock check here)

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: product.id,
                    unit_id: unitId,
                    is_base_unit: isBaseUnit,
                    name: isBaseUnit ? product.product_name :
                        `${product.product_name} (${product.selected_unit.unit_name})`,
                    price: isBaseUnit ? product.selling_price : product.selected_unit.price,
                    member_price: isBaseUnit ? (product.member_price || 0) : (product.selected_unit.member_price ||
                        0),
                    quantity: 1,
                    stock: product.stock,
                    multiplier: multiplier
                });
            }
            saveCart();
            renderCart();
            $('#barcode-input').val('').focus();
            showToast(`${product.product_name} berhasil ditambahkan!`, 'success');
        }

        function saveCart() {
            localStorage.setItem(POS_CART_KEY, JSON.stringify(cart));
        }
        const promos = @json($promos);

        // Calculate Best Promo for Cart
        function applyPromos() {
            // Reset discounts on items
            window.unclaimedPromos = [];
            cart.forEach(item => {
                item.discount = 0;
                item.promo_name = null;
                item.is_free = false;
            });

            let totalGlobalDiscount = 0;

            // Group items by product_id for easy checking
            let productQuantities = {};
            cart.forEach(item => {
                if (!productQuantities[item.id]) productQuantities[item.id] = 0;
                productQuantities[item.id] += item.quantity;
            });

            // 1. Apply Bundle & BOGO Promos first (Priority)
            promos.forEach(promo => {
                if (promo.type === 'bundle') {
                    // Bundle: Buy X items for $Y
                    // Only if target product matches
                    if (productQuantities[promo.product_id] >= promo.buy_qty) {
                        let count = productQuantities[promo.product_id];
                        let bundles = Math.floor(count / promo.buy_qty);
                        let remainder = count % promo.buy_qty;

                        // We need to find the cart item corresponding to this product to adjust price representation
                        // Note: Cart items might be split or single. Assuming single row per product ID for simplicity in logic,
                        // but if multiple rows exist (not typical in this POS), we'd need to iterate.
                        // In this POS, cart.find is used.

                        let item = cart.find(i => i.id == promo.product_id);
                        if (item) {
                            // Calculate total normal price for these bundled items
                            let normalPrice = item.price * (bundles * promo.buy_qty);
                            let bundlePrice = promo.bundle_price * bundles;
                            let discount = normalPrice - bundlePrice;

                            // Distribute discount across the item row
                            item.discount += discount;
                            item.promo_name = promo.name;
                        }
                    }
                } else if (promo.type === 'buy_x_get_y') {
                    let targetQty = productQuantities[promo.product_id] || 0;
                    let buyQty = parseInt(promo.buy_qty) || 1;
                    let getQty = parseInt(promo.get_qty) || 1;

                    let isSameProduct = !promo.reward_product_id || (promo.reward_product_id == promo.product_id);

                    if (isSameProduct) {
                        let bundleSize = buyQty + getQty;
                        let possibleRewards = Math.floor(targetQty / bundleSize) * getQty;
                        let remainder = targetQty % bundleSize;

                        if (remainder >= buyQty) {
                            let productName = promo.product ? promo.product.product_name : 'Produk';
                            window.unclaimedPromos.push(
                                `Tambahkan ${getQty}x ${productName} lagi untuk dapat GRATIS! (Promo: ${promo.name})`
                            );
                        }

                        let rewardItem = cart.find(i => i.id == promo.product_id);
                        if (rewardItem && possibleRewards > 0) {
                            let freeQty = Math.min(rewardItem.quantity, possibleRewards);
                            let discount = freeQty * rewardItem.price;
                            rewardItem.discount += discount;
                            rewardItem.promo_name = promo.name;
                            if (freeQty === rewardItem.quantity) rewardItem.is_free = true;
                        }
                    } else {
                        if (targetQty >= buyQty) {
                            let possibleRewards = Math.floor(targetQty / buyQty) * getQty;
                            let rewardItem = cart.find(i => i.id == promo.reward_product_id);
                            let claimed = 0;

                            if (rewardItem) {
                                let freeQty = Math.min(rewardItem.quantity, possibleRewards);
                                claimed = freeQty;
                                let discount = freeQty * rewardItem.price;
                                rewardItem.discount += discount;
                                rewardItem.promo_name = promo.name;
                                if (freeQty === rewardItem.quantity) rewardItem.is_free = true;
                            }

                            let unclaimed = possibleRewards - claimed;
                            if (unclaimed > 0) {
                                let rewardName = promo.reward_product ? promo.reward_product.product_name :
                                    'Produk Gratis';
                                window.unclaimedPromos.push(
                                    `Belum diklaim: ${unclaimed}x ${rewardName} (Promo: ${promo.name})`);
                            }
                        }
                    }
                }
            });

            // 2. Apply Simple Discounts (Percentage/Fixed)
            promos.forEach(promo => {
                if (promo.type === 'simple_discount') {
                    let item = cart.find(i => i.id == promo.product_id);
                    if (item) {
                        // Avoid double dipping if Bundle applied? Or stack?
                        // Let's assume stackable for now or simple priority.
                        // If bundle applied, maybe skipping simple discount is safer?
                        // For now, let's apply on remaining value if needed, or just apply.
                        // Safe approach: Simple discount applies to unit price.

                        let discountAmount = 0;
                        if (promo.discount_type === 'percentage') {
                            discountAmount = item.price * (promo.discount_value / 100);
                        } else {
                            discountAmount = promo.discount_value;
                        }

                        // Apply to total qty
                        let totalDiscount = discountAmount * item.quantity;
                        item.discount += totalDiscount;
                        item.promo_name = item.promo_name ? item.promo_name + ', ' + promo.name : promo.name;
                    }
                }
            });
        }

        // Render Cart Items
        function renderCart() {
            applyPromos(); // Calculate discounts before rendering

            const container = $('#cart-container');
            container.empty();

            if (window.unclaimedPromos && window.unclaimedPromos.length > 0) {
                window.unclaimedPromos.forEach(msg => {
                    container.append(`
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-2 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>${msg}</span>
                        </div>
                    `);
                });
            }

            if (cart.length === 0) {
                container.html(`
                    <div class="h-full flex flex-col items-center justify-center text-slate-400">
                        <div class="w-16 h-16 bg-slate-200/50 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Cart is empty</p>
                        <p class="text-xs opacity-75">Scan product to begin</p>
                    </div>
                `);
                $('#checkout-init-btn').prop('disabled', true);
                $('#total-display').text(formatRupiah(0));
                $('#grand-total').text(formatRupiah(0));
                currentTotal = 0;
                return;
            }

            let subtotal = 0;
            let totalDiscount = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                totalDiscount += item.discount || 0;

                const hasDiscount = item.discount > 0;
                const finalPrice = itemTotal - (item.discount || 0);

                const html = `
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 relative group">
                        <div class="flex justify-between items-start mb-2">
                             <div>
                                <h3 class="font-bold text-slate-700 text-sm leading-tight">${item.name}</h3>
                                <div class="text-xs text-slate-400 mt-0.5">${item.barcode || '-'}</div>
                                ${hasDiscount ? `<div class="text-[10px] text-green-600 font-bold mt-1 item-promo-tag items-center flex gap-1"><i data-feather="tag" class="w-3 h-3"></i> ${item.promo_name} (-${formatRupiah(item.discount)})</div>` : ''}
                             </div>
                             <div class="text-right">
                                <div class="font-bold text-slate-800">${formatRupiah(finalPrice)}</div>
                                ${hasDiscount ? `<div class="text-xs text-slate-400 line-through">${formatRupiah(itemTotal)}</div>` : ''}
                             </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center bg-slate-100 rounded-lg p-0.5">
                                <button onclick="updateQty(${index}, -1)" class="w-7 h-7 flex items-center justify-center bg-white rounded-md shadow-sm text-slate-600 hover:text-indigo-600 active:scale-95 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <input type="number" 
                                    value="${item.quantity}" 
                                    onchange="setQty(${index}, this.value)"
                                    class="w-10 text-center bg-transparent border-none text-sm font-bold text-slate-700 outline-none p-0 focus:ring-0">
                                <button onclick="updateQty(${index}, 1)" class="w-7 h-7 flex items-center justify-center bg-white rounded-md shadow-sm text-slate-600 hover:text-indigo-600 active:scale-95 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <button onclick="removeFromCart(${index})" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                container.append(html);
            });

            // Re-init feather icons
            if (typeof feather !== 'undefined') feather.replace();

            updateTotal();
            $('#checkout-init-btn').prop('disabled', false);
        }

        function updateQty(index, change) {
            const item = cart[index];

            if (item.quantity + change > 0) {
                // Allow negative stock (removed item.stock check here)
                item.quantity += change;
            } else {
                if (!confirm('Hapus item dari keranjang?')) return;
                cart.splice(index, 1);
            }
            saveCart();
            renderCart();
        }

        function setQty(index, value) {
            const qty = parseInt(value);
            if (isNaN(qty) || qty <= 0) {
                removeFromCart(index);
                return;
            }
            const item = cart[index];
            // Allow negative stock (removed item.stock check here)
            item.quantity = qty;
            saveCart();
            renderCart();
        }

        function removeFromCart(index) {
            if (!confirm('Hapus item dari keranjang?')) return;
            cart.splice(index, 1);
            saveCart();
            renderCart();
        }

        function updateTotal() {
            let subtotal = 0;
            let promoDiscount = 0;

            cart.forEach(item => {
                subtotal += item.price * item.quantity;
                promoDiscount += item.discount || 0;
            });

            const manualDiscount = parseFloat($('#discount-display-input').val()) || 0;
            const total = Math.max(0, subtotal - promoDiscount - manualDiscount);

            currentTotal = total;

            $('#grand-total').text(formatRupiah(total));

            if (manualDiscount > 0 || promoDiscount > 0) {
                // Maybe show breakdown if needed, but for now just total
            }
            $('#payment-modal-total').text(formatRupiah(total));

            const btn = $('#checkout-init-btn');
            if (total > 0 || (cart.length > 0 && total >
                    0)) { // Allow checkout if cart has items, even if free after discount (though unlikely)
                btn.prop('disabled', false);
            } else {
                btn.prop('disabled', true);
            }
        }

        function openMemberSearchModal() {
            $('#memberSearchModal').removeClass('hidden');
            $('#member-search-input').focus();
        }

        function closeMemberSearchModal() {
            $('#memberSearchModal').addClass('hidden');
        }

        function selectMember(member) {
            $('#selected-member-id').val(member.id);
            $('#member-name-display').text(member.name);
            $('#member-select-btn').removeClass('bg-indigo-50 text-indigo-700').addClass(
                'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700');
            $('#remove-member-btn').removeClass('hidden');
            closeMemberSearchModal();
            renderCart();
            showToast('Member selected: ' + member.name, 'success');
        }

        function removeMember() {
            $('#selected-member-id').val('');
            $('#member-name-display').text('Select Member');
            $('#member-select-btn').removeClass('bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700').addClass(
                'bg-indigo-50 text-indigo-700 hover:bg-indigo-100');
            $('#remove-member-btn').addClass('hidden');
            renderCart();
        }

        // Member Search Input Logic
        let searchTimeout;
        $('#member-search-input').on('input', function() {
            const query = $(this).val();
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                $('#member-search-results').html(
                    '<p class="text-center text-slate-400 text-sm py-4">Type to search members...</p>');
                return;
            }

            searchTimeout = setTimeout(() => {
                $.ajax({
                    url: '{{ route('pos.search.member') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        query: query
                    },
                    success: function(members) {
                        const container = $('#member-search-results');
                        container.empty();

                        if (members.length === 0) {
                            container.html(
                                '<p class="text-center text-slate-400 text-sm py-4">No members found</p>'
                            );
                            return;
                        }

                        members.forEach(member => {
                            container.append(`
                                <div class="p-3 hover:bg-slate-50 cursor-pointer flex justify-between items-center transition-colors" onclick='selectMember(${JSON.stringify(member)})'>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">${member.name}</div>
                                        <div class="text-xs text-slate-500">${member.phone || '-'}</div>
                                    </div>
                                    <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">
                                        Poin: ${member.point}
                                    </div>
                                </div>
                            `);
                        });
                    },
                    error: function() {
                        $('#member-search-results').html(
                            '<p class="text-center text-red-400 text-sm py-4">Error searching</p>'
                        );
                    }
                });
            }, 300);
        });



        function toggleMemberPrice() {
            renderCart();
        }

        function searchProduct(query) {
            $.ajax({
                url: '{{ route('pos.search') }}',
                type: 'POST',
                data: {
                    barcode: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (response.products.length === 1) {
                            addToCart(response.products[0]);
                        } else {
                            // Show selection modal
                            showProductSelection(response.products);
                        }
                    } else {
                        alert('Product not found!');
                    }
                },
                error: function() {
                    alert('Error searching product');
                }
            });
        }

        function showProductSelection(products) {
            const list = $('#product-selection-list');
            list.empty();
            products.forEach(p => {
                const displayName = p.is_base_unit ? p.product_name :
                    `${p.product_name} (${p.selected_unit.unit_name})`;
                const displayPrice = p.is_base_unit ? p.selling_price : p.selected_unit.price;
                const barcode = p.is_base_unit ? (p.barcode || '-') : (p.selected_unit.barcode || p.barcode || '-');

                list.append(`
                    <div class="p-3 hover:bg-slate-50 cursor-pointer flex justify-between items-center border-b border-slate-100 last:border-0" onclick='selectProduct(${JSON.stringify(p)})'>
                         <div>
                            <div class="font-bold text-slate-800">${displayName}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Barcode: <span class="text-slate-700">${barcode}</span></div>
                         </div>
                         <div class="font-bold text-indigo-600">${formatRupiah(displayPrice)}</div>
                    </div>
                `);
            });
            $('#productSelectionModal').removeClass('hidden');
        }

        function selectProduct(product) {
            addToCart(product);
            closeModal();
        }

        function closeModal() {
            $('#productSelectionModal').addClass('hidden');
        }

        function closePaymentModal() {
            $('#paymentModal').addClass('hidden');
        }

        $(document).ready(function() {
            @if (session('success'))
                localStorage.removeItem(POS_CART_KEY);
            @endif

            // Restore cart
            const savedCart = localStorage.getItem(POS_CART_KEY);
            if (savedCart) {
                try {
                    cart = JSON.parse(savedCart);
                    renderCart();
                } catch (e) {
                    console.error(e);
                }
            }

            // Restore held carts
            loadHeldCarts();

            $('#add-btn').click(function() {
                const query = $('#barcode-input').val();
                if (!query) return;
                searchProduct(query);
            });

            $('#barcode-input').keypress(function(e) {
                if (e.which === 13) {
                    $('#add-btn').click();
                }
            });

            $('#checkout-init-btn').click(function() {
                if (cart.length === 0) return;

                // Reset Payment Modal State
                $('#payment-method').val(''); // Clear method
                $('#amount-paid').val('');
                $('#payment-change').text(formatRupiah(0));

                // Reset Cards Visuals
                $('.payment-option-card').removeClass(
                        'ring-2 ring-indigo-500 bg-indigo-50 border-indigo-200 text-indigo-700')
                    .addClass('bg-white border-slate-200 text-slate-600 hover:bg-slate-50');
                $('.payment-option-card .active-indicator').addClass('hidden');
                $('.payment-option-card .text-slate-400').removeClass('text-indigo-500');

                // Hide Inputs
                $('#amount-paid-container').addClass('hidden');
                $('#change-container').addClass('hidden');
                $('#qris-static-container').addClass('hidden');
                $('#qris-dynamic-info').addClass('hidden');

                // Button State
                $('#confirm-payment-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');

                $('#paymentModal').removeClass('hidden');
            });


            // Scanner Logic
            $('#start-scan-btn').click(function() {
                $('#scanner-container').removeClass('hidden');

                // Check if Html5Qrcode is defined
                if (typeof Html5Qrcode === "undefined") {
                    alert("Scanner library not loaded. Please refresh page or check internet connection.");
                    return;
                }

                html5QrCode = new Html5Qrcode("reader");
                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                };

                html5QrCode.start({
                        facingMode: "environment"
                    }, config, onScanSuccess)
                    .catch(err => {
                        console.error("Error starting scanner", err);
                        alert("Error starting camera: " + err);
                        $('#scanner-container').addClass('hidden');
                    });

                isScanning = true;
                $(this).addClass('hidden');
            });

            $('#stop-scan-btn').click(function() {
                stopScanner();
            });

            function stopScanner() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        $('#scanner-container').addClass('hidden');
                        $('#start-scan-btn').removeClass('hidden');
                        isScanning = false;
                    }).catch(err => {
                        console.error("Failed to stop scanner", err);
                    });
                }
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Play beep sound
                playBeep();

                $('#barcode-input').val(decodedText);
                stopScanner();

                // Trigger add to cart
                $('#add-btn').click();
            }

            $('#confirm-payment-btn').click(function() {
                const method = $('#payment-method').val();
                let paid = parseFloat($('#amount-paid').val()) || 0;
                const btn = $(this);

                // For non-cash, enforce exact amount
                if (method !== 'Tunai') {
                    paid = currentTotal;
                    // Make sure we use the total including fee if Qris Dinamis
                    if (method === 'Qris Dinamis') {
                        const fee = currentTotal * (qrisFeePercent / 100);
                        paid = Math.round(currentTotal + fee);
                    }
                }

                if (paid < currentTotal) {
                    alert('Pembayaran kurang!');
                    return;
                }

                if (method === 'Qris Dinamis') {
                    // Disable button
                    btn.prop('disabled', true).text('Processing...');

                    $.ajax({
                        url: '{{ route('pos.token') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cart_data: JSON.stringify(cart),
                            member_id: $('#selected-member-id').val(),
                            is_member: !!$('#selected-member-id')
                                .val(), // Pass as boolean too if needed, depending on controller
                            discount: parseInt($('#discount-display-input').val()) || 0
                        },
                        success: function(response) {
                            if (response.token) {
                                window.snap.pay(response.token, {
                                    onSuccess: function(result) {
                                        // Submit form
                                        $('#cart-data-input').val(JSON.stringify(
                                            cart));
                                        $('#amount-paid-input').val(paid);
                                        $('#payment-method-input').val(method);
                                        $('#member-id-input').val($(
                                            '#selected-member-id').val());
                                        $('#discount-input').val(parseInt($(
                                                '#discount-display-input')
                                            .val()) || 0);
                                        $('#transaction-form').submit();
                                    },
                                    onPending: function(result) {
                                        alert(
                                            'Payment pending. Check Midtrans dashboard.'
                                        );
                                        btn.prop('disabled', false).text('Pay Now');
                                    },
                                    onError: function(result) {
                                        alert('Payment failed!');
                                        btn.prop('disabled', false).text('Pay Now');
                                    },
                                    onClose: function() {
                                        btn.prop('disabled', false).text('Pay Now');
                                    }
                                });
                            } else {
                                alert('Failed to get payment token.');
                                btn.prop('disabled', false).text('Pay Now');
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Error processing payment.';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                msg = xhr.responseJSON.error;
                            }
                            alert(msg);
                            btn.prop('disabled', false).text('Pay Now');
                        }
                    });
                    return;
                }

                $('#cart-data-input').val(JSON.stringify(cart));
                $('#amount-paid-input').val(paid);
                $('#payment-method-input').val(method);
                $('#member-id-input').val($('#selected-member-id').val());
                $('#discount-input').val(parseInt($('#discount-display-input').val()) || 0);
                $('#transaction-form').submit();
            });

            // ... (rest of logic) ...


            // Payment Method Change Handler
            $('#payment-method').change(function() {
                const method = $(this).val();

                // Reset fields
                $('#amount-paid').val('');
                $('#payment-change').text(formatRupiah(0));

                // Reset extras
                $('#qris-static-container').addClass('hidden');
                $('#qris-dynamic-info').addClass('hidden');
                $('#change-container').addClass('hidden');
                $('#cash-suggestions').addClass('hidden');
                $('#amount-paid-container').addClass('hidden');

                if (!method) return;

                // Show amount container for all valid methods
                $('#amount-paid-container').removeClass('hidden');

                if (method === 'Tunai') {
                    $('#amount-paid').prop('readonly', false).focus();
                    $('#change-container').removeClass('hidden');
                    $('#cash-suggestions').removeClass('hidden');
                    updateCashSuggestions();

                    $('#confirm-payment-btn').prop('disabled', true).addClass(
                        'opacity-50 cursor-not-allowed');
                } else if (method === 'Qris Dinamis') {
                    // For Qris, amount is exact + fee
                    $('#change-container').addClass('hidden');
                    $('#cash-suggestions').addClass('hidden');

                    const fee = currentTotal * (qrisFeePercent / 100);
                    const totalWithFee = Math.round(currentTotal + fee);
                    $('#amount-paid').val(totalWithFee).prop('readonly', true);

                    $('#confirm-payment-btn').prop('disabled', false).removeClass(
                        'opacity-50 cursor-not-allowed');
                } else if (method === 'Qris Statis') {
                    $('#change-container').addClass('hidden');
                    $('#cash-suggestions').addClass('hidden');

                    if (qrisStaticImage) {
                        $('#qris-static-image').attr('src', qrisStaticImage);
                        $('#qris-static-container').removeClass('hidden');
                    } else {
                        $('#qris-static-container').removeClass('hidden');
                    }

                    $('#amount-paid').val(currentTotal).prop('readonly', true);
                    $('#confirm-payment-btn').prop('disabled', false).removeClass(
                        'opacity-50 cursor-not-allowed');
                } else {
                    // Other non-cash
                    $('#change-container').addClass('hidden');
                    $('#cash-suggestions').addClass('hidden');
                    $('#amount-paid').val(currentTotal).prop('readonly', true);

                    $('#confirm-payment-btn').prop('disabled', false).removeClass(
                        'opacity-50 cursor-not-allowed');
                }
            });

            // Cash Suggestions Logic
            function updateCashSuggestions() {
                const container = $('#cash-suggestions');
                container.empty();

                if ($('#payment-method').val() !== 'Tunai') return;

                const suggestions = [currentTotal];

                // Next 5000
                if (currentTotal % 5000 !== 0) suggestions.push(Math.ceil(currentTotal / 5000) * 5000);
                // Next 10000
                if (currentTotal % 10000 !== 0) suggestions.push(Math.ceil(currentTotal / 10000) * 10000);
                // Next 20000
                if (currentTotal % 20000 !== 0) suggestions.push(Math.ceil(currentTotal / 20000) * 20000);
                // Next 50000
                if (currentTotal % 50000 !== 0) suggestions.push(Math.ceil(currentTotal / 50000) * 50000);
                // Next 100000
                if (currentTotal % 100000 !== 0) suggestions.push(Math.ceil(currentTotal / 100000) * 100000);

                // Unique and sort
                const uniqueSuggestions = [...new Set(suggestions)].sort((a, b) => a - b);

                uniqueSuggestions.forEach(amount => {
                    const btn = $(`
                        <button type="button" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors border border-indigo-200">
                            ${formatRupiah(amount)}
                        </button>
                    `);
                    btn.click(function() {
                        $('#amount-paid').val(amount).trigger('input');
                    });
                    container.append(btn);
                });
            }

            // Amount Paid Input
            $('#amount-paid').on('input', function() {
                const paid = parseFloat($(this).val()) || 0;
                const change = paid - currentTotal;

                if (change >= 0) {
                    $('#payment-change').text(formatRupiah(change)).removeClass('text-red-500').addClass(
                        'text-emerald-600');
                    $('#confirm-payment-btn').prop('disabled', false).removeClass(
                        'opacity-50 cursor-not-allowed');
                } else {
                    $('#payment-change').text(formatRupiah(change)).removeClass('text-emerald-600')
                        .addClass('text-red-500');
                    $('#confirm-payment-btn').prop('disabled', true).addClass(
                        'opacity-50 cursor-not-allowed');
                }
            });


            // Handle Payment Method Card Click
            $('.payment-option-card').click(function() {
                const value = $(this).data('value');

                // Update hidden input and trigger change
                $('#payment-method').val(value).trigger('change');

                // Update Visuals
                $('.payment-option-card').removeClass(
                    'ring-2 ring-indigo-500 bg-indigo-50 border-indigo-200 text-indigo-700').addClass(
                    'bg-white border-slate-200 text-slate-600 hover:bg-slate-50');
                $('.payment-option-card .active-indicator').addClass('hidden');
                $('.payment-option-card .text-slate-400').removeClass('text-indigo-500');

                // Activate Clicked
                $(this).removeClass('bg-white border-slate-200 text-slate-600 hover:bg-slate-50').addClass(
                    'ring-2 ring-indigo-500 bg-indigo-50 border-indigo-200 text-indigo-700');
                $(this).find('.active-indicator').removeClass('hidden');
                $(this).find('.text-slate-400').addClass('text-indigo-500');
            }); // End .payment-option-card click
        }); // End $(document).ready

        // Held Cart Functions
        function loadHeldCarts() {
            const saved = localStorage.getItem(POS_HELD_KEY);
            if (saved) {
                try {
                    heldCarts = JSON.parse(saved);
                } catch (e) {
                    heldCarts = [];
                }
            }
            updateHeldCount();
        }

        function saveHeldCart() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }

            heldCarts.push({
                time: new Date().toLocaleString(),
                items: cart,
                total: currentTotal
            });
            localStorage.setItem(POS_HELD_KEY, JSON.stringify(heldCarts));

            // Clear current cart
            cart = [];
            saveCart();
            renderCart();
            updateHeldCount();
            alert('Cart saved successfully!');
        }

        function updateHeldCount() {
            const count = heldCarts.length;
            const badge = $('#held-count-badge');
            badge.text(count);
            if (count > 0) {
                badge.removeClass('hidden');
            } else {
                badge.addClass('hidden');
            }
        }

        function openHeldCartsModal() {
            renderHeldCartsList();
            $('#heldCartsModal').removeClass('hidden');
        }

        function closeHeldCartsModal() {
            $('#heldCartsModal').addClass('hidden');
        }

        function renderHeldCartsList() {
            const container = $('#held-carts-list');
            container.empty();

            if (heldCarts.length === 0) {
                container.html('<p class="text-center text-gray-500 py-4">No held transactions.</p>');
                return;
            }

            heldCarts.forEach((hc, index) => {
                const html = `
                    <div class="bg-white border border-slate-200 rounded-xl p-3 flex justify-between items-center hover:bg-slate-50 transition-colors shadow-sm">
                        <div>
                            <div class="font-semibold text-slate-700 text-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ${hc.time}
                            </div>
                            <div class="text-xs text-slate-500 mt-1 pl-6">
                                <span class="font-medium text-slate-800">${hc.items.length} Items</span> • <span class="font-bold text-indigo-600">${formatRupiah(hc.total)}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                             <button onclick="restoreHeldCart(${index})" class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">Restore</button>
                             <button onclick="removeHeldCart(${index})" class="text-xs font-semibold bg-red-50 text-red-500 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">Delete</button>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        function restoreHeldCart(index) {
            if (cart.length > 0) {
                if (!confirm('Current cart is not empty. Overwrite?')) return;
            }

            const hc = heldCarts[index];
            cart = hc.items;

            // Remove from held
            heldCarts.splice(index, 1);
            localStorage.setItem(POS_HELD_KEY, JSON.stringify(heldCarts));

            saveCart();
            renderCart();
            updateHeldCount();
            closeHeldCartsModal();
        }

        function removeHeldCart(index) {
            if (!confirm('Delete this held transaction?')) return;
            heldCarts.splice(index, 1);
            localStorage.setItem(POS_HELD_KEY, JSON.stringify(heldCarts));
            renderHeldCartsList();
            updateHeldCount();
        }

        // Beep Sound
        function playBeep() {
            try {
                const ctx = new(window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.type = "sine";
                osc.frequency.value = 1500; // Frequency in Hz
                gain.gain.value = 0.1; // Volume

                osc.start();
                setTimeout(() => {
                    osc.stop();
                }, 100); // Duration in ms
            } catch (e) {
                console.error("Audio API not supported");
            }
        }

        function showToast(message, type = 'success') {
            const colorClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const toast = $(`
                <div class="fixed top-4 right-4 ${colorClass} text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-[-20px] opacity-0 z-50 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold">${message}</span>
                </div>
            `);

            $('body').append(toast);

            // Animate in
            setTimeout(() => {
                toast.removeClass('translate-y-[-20px] opacity-0');
            }, 10);

            // Remove after 3s
            setTimeout(() => {
                toast.addClass('translate-y-[-20px] opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
    <!-- Persistent Transaction Success Modal -->
    <div id="success-modal"
        class="fixed inset-0 bg-slate-900/60 z-[100] hidden flex-col justify-center items-center px-4 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300 border border-slate-100 dark:border-slate-700"
            id="success-modal-content">
            <div class="p-6 text-center">
                <div
                    class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Transaksi Berhasil!</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Pembayaran diterima dan pesanan selesai dicatat.
                </p>

                <div class="space-y-3">
                    <button onclick="printLastReceipt()"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Struk
                    </button>
                    <button onclick="closeSuccessModal()"
                        class="w-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-3 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Selesai (Tutup)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const printMethod = '{{ Auth::user()->print_method ?? 'web' }}';

        function showSuccessModal(receiptId) {
            localStorage.setItem('pending_receipt_id', receiptId);
            $('#success-modal').removeClass('hidden').addClass('flex');
            setTimeout(() => {
                $('#success-modal').removeClass('opacity-0');
                $('#success-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
            }, 10);
        }

        function closeSuccessModal() {
            localStorage.removeItem('pending_receipt_id');
            $('#success-modal').addClass('opacity-0');
            $('#success-modal-content').addClass('scale-95 opacity-0').removeClass('scale-100 opacity-100');
            setTimeout(() => {
                $('#success-modal').removeClass('flex').addClass('hidden');
            }, 300);
        }

        function printLastReceipt() {
            const receiptId = localStorage.getItem('pending_receipt_id');
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

        $(document).ready(function() {
            @if (session('success') && session('receipt_id'))
                showSuccessModal('{{ session('receipt_id') }}');
            @else
                const savedReceipt = localStorage.getItem('pending_receipt_id');
                if (savedReceipt) {
                    showSuccessModal(savedReceipt);
                }
            @endif
        });
    </script>

    @if (session('success') && session('closing_receipt_id'))
        <script>
            // Auto Trigger Bluetooth Print App based on Setting for Shift Closing
            $(document).ready(function() {
                const printMethod = '{{ Auth::user()->print_method ?? 'web' }}';
                const receiptId = '{{ session('closing_receipt_id') }}';
                const receiptUrl = '{{ url('/closing/receipt') }}/' + receiptId + '/json';

                if (printMethod === 'mate_bluetooth' || printMethod === 'android_mate') {
                    window.location.href = 'my.bluetoothprint.scheme://' + receiptUrl;
                } else if (printMethod === 'mate_bluetooth_ios' || printMethod === 'ios_bprint') {
                    let iosUrl = receiptUrl;
                    if (iosUrl.startsWith('https://')) {
                        iosUrl = 'bprints://' + iosUrl.substring(8);
                    } else if (iosUrl.startsWith('http://')) {
                        iosUrl = 'bprint://' + iosUrl.substring(7);
                    } else {
                        iosUrl = 'bprint://' + iosUrl;
                    }
                    console.log('Redirecting to iOS BPrint:', iosUrl);
                    window.location.href = iosUrl;
                } else {
                    console.log('Web print selected or standard thermal printer auto-print required.');
                    // Native browser print could be triggered here if there was a web view
                }
            });
        </script>
    @endif
@endsection

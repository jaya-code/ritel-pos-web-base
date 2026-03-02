@extends('layouts.cashier')

@section('content')
    <div class="flex flex-col h-full bg-slate-50 relative overflow-hidden">
        <!-- Header / Toolbar -->
        <div class="flex-none p-4 bg-white border-b border-slate-200 shadow-sm z-10">
            <h2 class="text-lg font-bold text-slate-800">Input Stok Masuk</h2>
        </div>

        <!-- Form Content -->
        <div class="flex-grow overflow-y-auto p-4 pb-24">
            <form action="{{ route('pos.stock.store') }}" method="POST" id="stock-form" class="max-w-3xl mx-auto w-full">
                @csrf

                <!-- Supplier Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                    <select name="supplier_id"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors"
                        required>
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Entry Section -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-4">
                    <h3 class="font-bold text-slate-700 mb-3">Tambah Produk</h3>

                    <!-- Scan/Search Control -->
                    <div class="mb-3">
                        <div class="flex gap-2 mb-2">
                            <button type="button" id="start-scan-btn"
                                class="flex-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 py-2 rounded-lg font-semibold flex items-center justify-center gap-2 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                Scan Barcode
                            </button>
                        </div>

                        <!-- Scanner Container -->
                        <div id="scanner-container"
                            class="hidden mb-3 mx-auto max-w-sm relative rounded-lg overflow-hidden shadow-lg ring-1 ring-black/5 bg-black">
                            <div id="reader" class="w-full"></div>
                            <button type="button" id="stop-scan-btn"
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 z-20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="relative flex gap-2">
                            <div class="relative flex-grow">
                                <input type="text" id="barcode-input"
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm"
                                    placeholder="Cari nama produk atau scan barcode...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <!-- Loading Indicator -->
                                <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                    <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" id="manual-search-btn"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700 transition-colors">
                                Cari
                            </button>
                        </div>
                    </div>

                    <!-- Selected Product Info (Hidden initially) -->
                    <div id="selected-product-info" class="hidden bg-slate-50 p-3 rounded-lg border border-slate-200 mb-3">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-xs text-slate-500">Produk Terpilih:</span>
                                <div class="font-bold text-slate-800" id="selected-product-name">Product Name</div>
                            </div>
                            <button type="button" id="clear-selection-btn"
                                class="text-xs text-red-500 hover:text-red-700">Batal</button>
                        </div>
                        <input type="hidden" id="selected-product-id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Jumlah</label>
                                <input type="number" id="qty-input" class="w-full rounded-lg border-slate-200 text-sm"
                                    placeholder="1" min="1" value="1">
                            </div>
                            <div id="unit-selection-container" class="hidden">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Satuan</label>
                                <select id="unit-select"
                                    class="w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                                    <option value="">Default (Pcs)</option>
                                    <!-- Options injected via JS -->
                                </select>
                            </div>
                        </div>

                        <button type="button" id="add-item-btn"
                            class="w-full mt-3 bg-indigo-600 text-white font-bold py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            + Tambah ke Daftar
                        </button>
                    </div>
                </div>

                <!-- List of Items to Add -->
                <div class="space-y-3 mb-20" id="items-container">
                    <!-- Items will be injected here -->
                    <div id="empty-state" class="text-center py-8 text-slate-400 text-sm">
                        Belum ada produk yang ditambahkan
                    </div>
                </div>

                <!-- Floating Submit Button -->
                <div
                    class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 shadow-lg max-w-md mx-auto">
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                        Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Selection Modal -->
    <div id="productSelectionModal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden flex flex-col max-h-[80vh]">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Pilih Produk</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto p-2" id="product-selection-list">
                <!-- Matches injected here -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let html5QrcodeScanner = null;
        let itemIndex = 0;

        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        $(document).ready(function() {
            // Scanner Logic
            $('#start-scan-btn').click(function() {
                $('#scanner-container').removeClass('hidden');

                if (html5QrcodeScanner === null) {
                    html5QrcodeScanner = new Html5Qrcode("reader");
                }

                html5QrcodeScanner.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    (decodedText, decodedResult) => {
                        // Handle on success
                        stopScanner();
                        $('#barcode-input').val(decodedText);
                        searchProduct(decodedText);
                    },
                    (errorMessage) => {
                        // parse error, ignore
                    }
                ).catch((err) => {
                    console.log("Error starting scanner: " + err);
                    alert("Tidak dapat mengakses kamera");
                });
            });

            $('#stop-scan-btn').click(function() {
                stopScanner();
            });

            function stopScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        $('#scanner-container').addClass('hidden');
                    }).catch((err) => {
                        console.log("Error stopping scanner");
                    });
                } else {
                    $('#scanner-container').addClass('hidden');
                }
            }

            // Search Logic

            // Trigger search on "Cari" button click
            $('#manual-search-btn').click(function() {
                searchProduct($('#barcode-input').val());
            });

            // Trigger search on Enter key
            $('#barcode-input').on('keyup', function(e) {
                if (e.which === 13) { // Enter key
                    searchProduct($(this).val());
                }
            });

            function searchProduct(query) {
                if (!query) return;

                $('#search-loading').removeClass('hidden');

                $.ajax({
                    url: '{{ route('pos.search') }}',
                    type: 'POST',
                    data: {
                        barcode: query,
                        type: 'stock',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#search-loading').addClass('hidden');
                        if (response.success) {
                            if (response.products.length === 1) {
                                selectProduct(response.products[0]);
                            } else {
                                showProductSelection(response.products);
                            }
                        } else {
                            alert('Produk tidak ditemukan');
                        }
                    },
                    error: function() {
                        $('#search-loading').addClass('hidden');
                        alert('Error searching product');
                    }
                });
            }

            // Product Selection
            window.selectProduct = function(product) {
                $('#selected-product-id').val(product.id);
                $('#selected-product-name').text(product.product_name);
                // Cost input removed
                $('#qty-input').val(1);

                // Handle Units
                $('#unit-select').html('<option value="">Default (Pcs)</option>');
                if (product.units && product.units.length > 0) {
                    $('#unit-selection-container').removeClass('hidden');
                    product.units.forEach(unit => {
                        $('#unit-select').append(
                            `<option value="${unit.id}" data-quantity="${unit.quantity}">${unit.unit_name} (x${unit.quantity})</option>`
                        );
                    });
                } else {
                    $('#unit-selection-container').addClass('hidden');
                }

                $('#selected-product-info').removeClass('hidden');
                $('#barcode-input').val(''); // Clear search
                $('#qty-input').focus();

                closeModal();
            }

            window.showProductSelection = function(products) {
                const list = $('#product-selection-list');
                list.empty();
                products.forEach(p => {
                    list.append(`
                        <div class="p-3 hover:bg-slate-50 cursor-pointer flex justify-between items-center border-b border-slate-50 last:border-0" onclick='selectProduct(${JSON.stringify(p)})'>
                             <div>
                                <div class="font-bold text-slate-800">${p.product_name}</div>
                                <div class="text-xs text-slate-500">Stok: ${p.stock}</div>
                             </div>
                        </div>
                    `);
                });
                $('#productSelectionModal').removeClass('hidden');
            }

            window.closeModal = function() {
                $('#productSelectionModal').addClass('hidden');
            }

            $('#clear-selection-btn').click(function() {
                $('#selected-product-info').addClass('hidden');
                $('#selected-product-id').val('');
                $('#qty-input').val('');
                $('#unit-select').val('');
            });

            // Add to List Logic
            $('#add-item-btn').click(function() {
                const productId = $('#selected-product-id').val();
                const productName = $('#selected-product-name').text();
                const cost = 0; // Default to 0 as requested
                const qty = $('#qty-input').val();

                if (!productId || !qty || qty < 1) {
                    alert('Mohon lengkapi jumlah barang');
                    return;
                }

                $('#empty-state').hide();

                const selectedUnitId = $('#unit-select').val();
                let unitText = '';
                if (selectedUnitId) {
                    unitText = ` (${$('#unit-select option:selected').text()})`;
                }

                const itemHtml = `
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex justify-between items-center item-row">
                        <div>
                            <div class="font-bold text-slate-800 text-sm">${productName}${unitText}</div>
                            <div class="text-xs text-slate-500">
                                Qty: ${qty}
                            </div>
                            <input type="hidden" name="details[${itemIndex}][product_id]" value="${productId}">
                            <input type="hidden" name="details[${itemIndex}][product_unit_id]" value="${selectedUnitId}">
                            <input type="hidden" name="details[${itemIndex}][harga_beli]" value="${cost}">
                            <input type="hidden" name="details[${itemIndex}][qty_beli]" value="${qty}">
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 p-2 remove-item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `;

                $('#items-container').append(itemHtml);
                itemIndex++;

                // Reset Selection
                $('#clear-selection-btn').click();
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                if ($('.item-row').length === 0) {
                    $('#empty-state').show();
                }
            });
        });
    </script>
@endsection

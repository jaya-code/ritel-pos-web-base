@extends('layouts.cashier')

@section('content')
    <div class="flex flex-col h-full bg-slate-50 relative overflow-hidden">
        <!-- Header / Toolbar -->
        <div class="flex-none p-4 bg-white border-b border-slate-200 shadow-sm z-10">
            <h2 class="text-lg font-bold text-slate-800">Stock Opname</h2>
            <p class="text-xs text-slate-500">Sesuaikan stok fisik dengan stok sistem</p>
        </div>

        <!-- Form Content -->
        <div class="flex-grow overflow-y-auto p-4 pb-24">
            <form action="{{ route('pos.opname.store') }}" method="POST" id="opname-form" class="max-w-3xl mx-auto w-full">
                @csrf

                <!-- Product Entry Section -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-4">
                    <h3 class="font-bold text-slate-700 mb-3">Cari Produk</h3>

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
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs text-slate-500 uppercase tracking-wider font-bold">Produk
                                    Terpilih:</span>
                                <div class="font-bold text-slate-800 text-lg mt-1" id="selected-product-name">Product Name
                                </div>
                            </div>
                            <button type="button" id="clear-selection-btn"
                                class="p-1 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" id="selected-product-id">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white p-3 rounded border border-slate-200 text-center">
                                <label class="block text-xs font-bold text-slate-500 mb-1">Stok Sistem</label>
                                <div class="text-xl font-bold text-slate-800" id="system-stock-display">0</div>
                                <input type="hidden" id="system-stock-input">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Stok Fisik Aktual</label>
                                <input type="number" id="actual-stock-input"
                                    class="w-full text-center text-xl font-bold rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0" min="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Keterangan / Catatan
                                (Opsional)</label>
                            <input type="text" id="note-input"
                                class="w-full rounded text-sm border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Contoh: Barang rusak, salah hitung sebelumnya...">
                        </div>

                        <button type="button" id="add-item-btn"
                            class="w-full mt-4 bg-indigo-600 text-white font-bold py-2.5 rounded-lg hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200">
                            + Tambahkan ke Daftar Opname
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-3 px-1">
                    <h3 class="font-bold text-slate-700 text-sm">Daftar Penyesuaian</h3>
                    <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded-full" id="item-count">0
                        item</span>
                </div>

                <!-- List of Items to Add -->
                <div class="space-y-3 mb-20" id="items-container">
                    <!-- Items will be injected here -->
                    <div id="empty-state"
                        class="bg-white rounded-xl border border-slate-200 border-dashed p-8 text-center text-slate-400 text-sm flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300 mb-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Belum ada produk yang disesuaikan
                    </div>
                </div>

                <!-- Floating Submit Button -->
                <div
                    class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 shadow-[0_-4px_15px_-5px_rgba(0,0,0,0.1)] max-w-md mx-auto z-30">
                    <button type="button" id="submit-opname-btn" disabled
                        class="w-full bg-slate-300 text-slate-500 font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                        Simpan Opname
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Selection Modal -->
    <div id="productSelectionModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden flex flex-col max-h-[80vh] shadow-2xl">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
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
        @if (session('success'))
            localStorage.removeItem('opnameItems');
        @endif

        let html5QrcodeScanner = null;
        let opnameItems = JSON.parse(localStorage.getItem('opnameItems') || '[]');

        function saveOpnameItems() {
            localStorage.setItem('opnameItems', JSON.stringify(opnameItems));
        }

        function updateSubmitState() {
            $('#item-count').text(`${opnameItems.length} item`);

            const btn = $('#submit-opname-btn');
            if (opnameItems.length > 0) {
                btn.prop('disabled', false)
                    .removeClass('bg-slate-300 text-slate-500')
                    .addClass(
                        'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 shadow-[0_5px_15px_-5px_rgba(79,70,229,0.4)] active:scale-95'
                    );
            } else {
                btn.prop('disabled', true)
                    .removeClass(
                        'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 shadow-[0_5px_15px_-5px_rgba(79,70,229,0.4)] active:scale-95'
                    )
                    .addClass('bg-slate-300 text-slate-500');
            }
        }

        function renderOpnameItems() {
            $('.item-row').remove();

            if (opnameItems.length === 0) {
                $('#empty-state').show();
            } else {
                $('#empty-state').hide();
                opnameItems.forEach((item, index) => {
                    let diffBadge =
                        '<span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs font-bold">Pas (0)</span>';
                    if (item.diff > 0) {
                        diffBadge =
                            '<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-bold">+' +
                            item.diff + '</span>';
                    } else if (item.diff < 0) {
                        diffBadge = '<span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">' +
                            item.diff + '</span>';
                    }

                    const noteText = item.note ? item.note : 'Tidak ada catatan';

                    const itemHtml = `
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm item-row flex flex-col relative items-start">
                            <div class="w-full flex justify-between items-start">
                                <div class="font-bold text-slate-800 text-sm w-4/5">${item.productName}</div>
                                <button type="button" class="text-red-400 hover:text-red-600 transition-colors remove-item -mt-1 -mr-1 p-1" data-id="${item.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-3 w-full border-t border-slate-50 pt-3">
                                <div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sistem</div>
                                    <div class="font-bold text-slate-700">${item.systemStock}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Fisik (Aktual)</div>
                                    <div class="font-bold text-slate-900 border-b border-indigo-200 border-dashed inline-block">${item.actualStock}</div>
                                </div>
                            </div>

                            <div class="mt-2 text-xs flex items-center justify-between w-full">
                                <div class="text-slate-500 italic truncate w-2/3">${noteText}</div>
                                <div>Selisih: ${diffBadge}</div>
                            </div>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="details[${index}][product_id]" value="${item.productId}">
                            <input type="hidden" name="details[${index}][system_stock]" value="${item.systemStock}">
                            <input type="hidden" name="details[${index}][actual_stock]" value="${item.actualStock}">
                            <input type="hidden" name="details[${index}][note]" value="${item.note}">
                        </div>
                    `;
                    $('#items-container').append(itemHtml);
                });
            }
            updateSubmitState();
        }

        $(document).ready(function() {
            // Initial render
            renderOpnameItems();

            // Setup Scanner logic identically to pos.stock
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
                        stopScanner();
                        $('#barcode-input').val(decodedText);
                        searchProduct(decodedText);
                        playBeep(); // optional nice touch
                    },
                    (errorMessage) => {}
                ).catch((err) => {
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
                    });
                } else {
                    $('#scanner-container').addClass('hidden');
                }
            }

            function playBeep() {
                try {
                    const ctx = new(window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    osc.connect(ctx.destination);
                    osc.type = "sine";
                    osc.frequency.value = 1500;
                    osc.start();
                    setTimeout(() => osc.stop(), 100);
                } catch (e) {}
            }

            // Search Logic
            $('#manual-search-btn').click(function() {
                searchProduct($('#barcode-input').val());
            });

            $('#barcode-input').on('keyup', function(e) {
                if (e.which === 13) {
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

            // Product Selection Handling
            window.selectProduct = function(product) {
                $('#selected-product-id').val(product.id);
                $('#selected-product-name').text(product.product_name);

                // Opname Specifics
                $('#system-stock-display').text(product.stock);
                $('#system-stock-input').val(product.stock);

                // Clear inputs
                $('#actual-stock-input').val('');
                $('#note-input').val('');

                $('#selected-product-info').removeClass('hidden');
                $('#barcode-input').val('');
                $('#actual-stock-input').focus();

                closeModal();
            }

            window.showProductSelection = function(products) {
                const list = $('#product-selection-list');
                list.empty();
                products.forEach(p => {
                    list.append(`
                        <div class="px-3 py-3 hover:bg-slate-100 cursor-pointer flex justify-between items-center border-b border-slate-100 last:border-0 rounded-lg transition-colors" onclick='selectProduct(${JSON.stringify(p)})'>
                             <div>
                                <div class="font-bold text-slate-800">${p.product_name}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Stok Sistem: <span class="font-bold text-indigo-600">${p.stock}</span></div>
                             </div>
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
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
            });

            // Add Opname Item to List
            $('#add-item-btn').click(function() {
                const productId = $('#selected-product-id').val();
                const productName = $('#selected-product-name').text();
                const systemStock = parseInt($('#system-stock-input').val());
                const actualStockVal = $('#actual-stock-input').val();
                const note = $('#note-input').val();

                if (!productId || actualStockVal === '') {
                    alert('Mohon isi stok fisik aktual');
                    $('#actual-stock-input').focus();
                    return;
                }

                const actualStock = parseInt(actualStockVal);
                const diff = actualStock - systemStock;

                // Push to array and save
                opnameItems.push({
                    id: Date.now(), // timestamp as unique id
                    productId: productId,
                    productName: productName,
                    systemStock: systemStock,
                    actualStock: actualStock,
                    note: note,
                    diff: diff
                });

                saveOpnameItems();
                renderOpnameItems();

                // Clean up selection
                $('#clear-selection-btn').click();
                showToast('Ditambahkan ke daftar!');
            });

            $(document).on('click', '.remove-item', function() {
                const id = $(this).data('id');
                opnameItems = opnameItems.filter(item => item.id !== id);
                saveOpnameItems();
                renderOpnameItems();
            });

            function updateSubmitState() {
                $('#item-count').text(`${opnameItems.length} item`);

                const btn = $('#submit-opname-btn');
                if (opnameItems.length > 0) {
                    btn.prop('disabled', false)
                        .removeClass('bg-slate-300 text-slate-500')
                        .addClass(
                            'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 shadow-[0_5px_15px_-5px_rgba(79,70,229,0.4)] active:scale-95'
                        );
                } else {
                    btn.prop('disabled', true)
                        .removeClass(
                            'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 shadow-[0_5px_15px_-5px_rgba(79,70,229,0.4)] active:scale-95'
                        )
                        .addClass('bg-slate-300 text-slate-500');
                }
            }

            $('#submit-opname-btn').click(function() {
                if (confirm(
                        'Konfirmasi simpan hasil Stock Opname? Stok sistem akan diperbarui secara permanen.'
                    )) {
                    $('#opname-form').submit();
                }
            });

            function showToast(message) {
                const toast = $(`
                    <div class="fixed top-4 right-4 bg-slate-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-lg transform transition-all duration-300 translate-y-[-20px] opacity-0 z-50">
                        ${message}
                    </div>
                `);
                $('body').append(toast);
                setTimeout(() => toast.removeClass('translate-y-[-20px] opacity-0'), 10);
                setTimeout(() => {
                    toast.addClass('translate-y-[-20px] opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            }
        });
    </script>
@endsection

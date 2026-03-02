@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back to List
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Barcode -->
                <div class="mb-4">
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <div class="flex gap-2">
                        <input type="text" name="barcode" id="barcode"
                            class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('barcode') border-red-500 @enderror"
                            value="{{ old('barcode') }}" required>
                        <button type="button" id="start-scan-btn"
                            class="bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-4 rounded-lg font-medium flex items-center justify-center gap-2 transition-colors">
                            <i data-feather="maximize" class="w-5 h-5"></i> Scan
                        </button>
                    </div>
                    @error('barcode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Scanner Container -->
                    <div id="scanner-container"
                        class="hidden mt-3 mx-auto w-full relative rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-black">
                        <div id="reader" class="w-full"></div>
                        <button type="button" id="stop-scan-btn"
                            class="absolute top-2 right-2 bg-red-500 text-white hover:bg-red-600 rounded-full p-1.5 z-20 transition-colors">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- SKU -->
                <div class="mb-4">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" id="sku"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('sku') border-red-500 @enderror"
                        value="{{ old('sku') }}">
                    @error('sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Name -->
                <div class="mb-4">
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="product_name" id="product_name"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('product_name') border-red-500 @enderror"
                        value="{{ old('product_name') }}" required>
                    @error('product_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id"
                        class="select2 w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div class="mb-4">
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="supplier_id" id="supplier_id"
                        class="select2 w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rack Code -->
                <div class="mb-4">
                    <label for="kode_rak" class="block text-sm font-medium text-gray-700 mb-1">Rack Code</label>
                    <input type="text" name="kode_rak" id="kode_rak"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('kode_rak') }}">
                </div>

                <!-- Return Period -->
                <div class="mb-4">
                    <label for="periode_return" class="block text-sm font-medium text-gray-700 mb-1">Return Period
                        (Days)</label>
                    <input type="number" name="periode_return" id="periode_return"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('periode_return') }}" placeholder="e.g. 30">
                </div>

                <!-- Unit -->
                <div class="mb-4">
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="satuan" id="satuan"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('satuan', 'pcs') }}" required>
                </div>

                <!-- Isi (Contents) -->
                <div class="mb-4">
                    <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi / Content Qty</label>
                    <input type="number" name="isi" id="isi" step="any"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('isi', 1) }}">
                </div>

                <!-- Price Info -->
                <div class="mb-4">
                    <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                    <input type="number" name="cost_price" id="cost_price"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('cost_price') }}" required>
                </div>

                <div class="mb-4">
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                    <input type="number" name="selling_price" id="selling_price"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('selling_price') }}" required>
                </div>

                <div class="mb-4">
                    <label for="member_price" class="block text-sm font-medium text-gray-700 mb-1">Member Price</label>
                    <input type="number" name="member_price" id="member_price"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('member_price') }}">
                </div>

                <!-- Stock -->
                <div class="mb-4">
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Initial Stock</label>
                    <input type="number" name="stock" id="stock"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('stock', 0) }}" required>
                </div>

                <div class="mb-4">
                    <label for="stock_min" class="block text-sm font-medium text-gray-700 mb-1">Min. Stock Alert</label>
                    <input type="number" name="stock_min" id="stock_min"
                        class="w-full rounded-lg border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                        value="{{ old('stock_min', 1) }}" required>
                </div>
            </div>

            <!-- Dynamic Units Section -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Unit Conversions (Optional)</h2>
                        <p class="text-sm text-gray-500">Add larger units like Pack, Box, or Lusin that multiply the base
                            stock.</p>
                    </div>
                    <button type="button" id="add-unit-btn"
                        class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                        <i data-feather="plus" class="w-4 h-4"></i> Add Unit
                    </button>
                </div>

                <div id="units-container" class="space-y-4">
                    <!-- Unit rows will be appended here -->
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 border-t pt-6">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Product
                </button>
            </div>
        </form>
    </div>

    <!-- Required matching library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <!-- JS for Dynamic Units & Scanner -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('units-container');
            const addBtn = document.getElementById('add-unit-btn');
            let unitIndex = 0;

            function addUnitRow() {
                const row = document.createElement('div');
                row.className =
                    'grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200 relative group items-end';
                row.innerHTML = `
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit Name</label>
                        <input type="text" name="units[${unitIndex}][unit_name]" placeholder="e.g. Box" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qty (Multiplier)</label>
                        <input type="number" name="units[${unitIndex}][quantity]" placeholder="e.g. 12" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Barcode</label>
                        <input type="text" name="units[${unitIndex}][barcode]" placeholder="Optional" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Selling Price</label>
                        <input type="number" name="units[${unitIndex}][price]" placeholder="e.g. 50000" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Member Price</label>
                        <input type="number" name="units[${unitIndex}][member_price]" placeholder="Optional" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="col-span-12 md:col-span-1 text-right">
                        <button type="button" class="text-red-500 hover:text-red-700 p-2 remove-unit-btn" title="Remove Unit">
                            <i data-feather="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);

                row.querySelector('.remove-unit-btn').addEventListener('click', function() {
                    row.remove();
                });

                if (typeof feather !== 'undefined') feather.replace();
                unitIndex++;
            }

            addBtn.addEventListener('click', addUnitRow);

            // Barcode Scanner Logic
            let html5QrcodeScanner = null;
            const startScanBtn = document.getElementById('start-scan-btn');
            const stopScanBtn = document.getElementById('stop-scan-btn');
            const scannerContainer = document.getElementById('scanner-container');
            const barcodeInput = document.getElementById('barcode');

            startScanBtn.addEventListener('click', function() {
                scannerContainer.classList.remove('hidden');

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
                        barcodeInput.value = decodedText;
                        playBeep();
                        barcodeInput.focus();
                    },
                    (errorMessage) => {
                        // parse error, ignore
                    }
                ).catch((err) => {
                    console.error("Error starting scanner: ", err);
                    alert(
                        "Tidak dapat mengakses kamera. Pastikan browser memiliki izin akses kamera.");
                    scannerContainer.classList.add('hidden');
                });
            });

            stopScanBtn.addEventListener('click', function() {
                stopScanner();
            });

            function stopScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        scannerContainer.classList.add('hidden');
                    }).catch((err) => {
                        console.error("Error stopping scanner", err);
                    });
                } else {
                    scannerContainer.classList.add('hidden');
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
        });
    </script>
@endsection

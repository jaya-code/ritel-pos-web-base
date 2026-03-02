@extends('layouts.admin')

@section('title', 'Edit Purchase')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Purchase: {{ $pembelian->nofak_beli }}</h1>
            <a href="{{ route('pembelian.index') }}"
                class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('pembelian.update', $pembelian->pembelian_id) }}" method="POST"
                x-data='purchaseForm(@json($products), @json($pembelian->details))'>
                @csrf
                @method('PUT')

                <!-- Main Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Supplier -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" class="select2 w-full" required>
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ $pembelian->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                        <input type="date" name="tgl_beli" value="{{ $pembelian->tgl_beli->format('Y-m-d') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                            required>
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                        <select name="jenis_pembelian" x-model="jenis_pembelian"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors">
                            <option value="Non Kredit">Non Kredit (Cash)</option>
                            <option value="Kredit">Kredit</option>
                            <option value="Titipan">Titipan</option>
                        </select>
                    </div>

                    <!-- Due Date (Conditional) -->
                    <div x-show="jenis_pembelian === 'Kredit'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="tgl_jatuh_tempo"
                            value="{{ $pembelian->tgl_jatuh_tempo ? $pembelian->tgl_jatuh_tempo->format('Y-m-d') : '' }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status_pembelian"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors">
                            <option value="Belum Lunas"
                                {{ $pembelian->status_pembelian == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="Lunas" {{ $pembelian->status_pembelian == 'Lunas' ? 'selected' : '' }}>Lunas
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-semibold text-gray-800 text-lg">Items</h3>
                        <button type="button" @click="addItem()"
                            class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1">
                            <i data-feather="plus" class="w-4 h-4"></i> Add Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div
                                class="flex flex-col md:flex-row gap-4 items-start md:items-end bg-gray-50 p-4 rounded-xl border border-gray-200 relative group animate-fade-in-down">

                                <!-- Product Select -->
                                <div class="flex-1 w-full md:w-auto">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Product</label>
                                    <select :name="`details[${index}][product_id]`" class="w-full product-select"
                                        x-init="let comp = $data;
                                        setTimeout(() => {
                                            $($el).val(item.product_id).trigger('change');
                                            $($el).select2({ width: '100%', placeholder: 'Select Product' });
                                            $($el).on('change', function(e) {
                                                item.product_id = e.target.value;
                                                comp.updateAvailableUnits(index);
                                        
                                                if (!item.is_existing) {
                                                    comp.updateCost(index);
                                                    item.product_unit_id = ''; // reset only for new changes
                                                }
                                                comp.updateAvailableProducts();
                                                item.is_existing = false; // Reset flag after first load
                                            });
                                            comp.updateAvailableProducts();
                                        }, 100);">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }} (Stock:
                                                {{ $product->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Unit Select -->
                                <div class="w-full md:w-32"
                                    x-show="item.available_units && item.available_units.length > 0">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Unit</label>
                                    <select :name="`details[${index}][product_unit_id]`" x-model="item.product_unit_id"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                        @change="updateCost(index)">
                                        <option value="">Default (Pcs)</option>
                                        <template x-for="unit in item.available_units" :key="unit.id">
                                            <option :value="unit.id" x-text="`${unit.unit_name} (x${unit.quantity})`">
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Cost Price -->
                                <div class="w-full md:w-32">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Cost
                                        (Rp)</label>
                                    <input type="number" :name="`details[${index}][harga_beli]`" x-model="item.harga_beli"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                        placeholder="0">
                                </div>

                                <!-- Qty -->
                                <div class="w-full md:w-24">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Qty</label>
                                    <input type="number" :name="`details[${index}][qty_beli]`" x-model="item.qty_beli"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                        placeholder="1">
                                </div>

                                <!-- Subtotal -->
                                <div class="w-full md:w-40 bg-white p-2 rounded-lg border border-gray-200 text-right">
                                    <label
                                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1 text-left">Subtotal</label>
                                    <span class="text-sm font-bold text-gray-800"
                                        x-text="formatRupiah(item.qty_beli * item.harga_beli)"></span>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="absolute top-2 right-2 md:static md:mb-0.5 text-red-400 hover:text-red-600 transition-colors p-2 rounded-full hover:bg-red-50">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer / Totals -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex flex-col md:flex-row justify-end items-start md:items-center gap-6">
                        <div class="w-full md:w-80 space-y-3">
                            <!-- Discount -->
                            <div class="flex justify-between items-center">
                                <label class="text-sm text-gray-600">Discount Global (Rp)</label>
                                <input type="number" name="diskon" x-model="diskon"
                                    class="w-32 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm text-right"
                                    placeholder="0">
                            </div>

                            <!-- Tax -->
                            <div class="flex justify-between items-center">
                                <label class="text-sm text-gray-600">Tax / PPN (Rp)</label>
                                <input type="number" name="ppn" x-model="ppn"
                                    class="w-32 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm text-right"
                                    placeholder="0">
                            </div>

                            <div class="border-t border-gray-200 my-2"></div>

                            <!-- Grand Total -->
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">Grand Total</span>
                                <span class="text-xl font-bold text-blue-600"
                                    x-text="formatRupiah(calculateGrandTotal())"></span>
                                <input type="hidden" name="grand_total" :value="calculateGrandTotal()">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <div class="mr-3">
                            <a href="{{ route('pembelian.index') }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-8 rounded-lg shadow hover:shadow-md transition-all">Cancel</a>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Update Purchase
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@section('scripts')
    <script>
        function purchaseForm(productsData = [], existingItems = []) {
            return {
                products: productsData,
                jenis_pembelian: '{{ $pembelian->jenis_pembelian }}',
                diskon: 0, // Assumption, as it's not in DB schema shown in create but let's keep logic
                ppn: 0,
                items: existingItems.length ? existingItems.map(item => {
                    const product = productsData.find(p => p.id == item.product_id);
                    return {
                        product_id: item.product_id,
                        product_unit_id: item.product_unit_id || '',
                        available_units: product ? product.units : [],
                        qty_beli: item.qty_beli,
                        harga_beli: item.harga_beli,
                        is_existing: true
                    };
                }) : [{
                    product_id: '',
                    product_unit_id: '',
                    available_units: [],
                    qty_beli: 1,
                    harga_beli: 0,
                    is_existing: false
                }],
                addItem() {
                    this.items.push({
                        product_id: '',
                        product_unit_id: '',
                        available_units: [],
                        qty_beli: 1,
                        harga_beli: 0,
                        is_existing: false
                    });
                    setTimeout(() => {
                        feather.replace();
                        this.updateAvailableProducts();
                    }, 100);
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    setTimeout(() => {
                        this.updateAvailableProducts();
                    }, 100);
                },
                updateAvailableUnits(index) {
                    const productId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == productId);
                    if (product && product.units) {
                        this.items[index].available_units = product.units;
                    } else {
                        this.items[index].available_units = [];
                    }
                },
                updateCost(index) {
                    const productId = this.items[index].product_id;
                    const unitId = this.items[index].product_unit_id;
                    const product = this.products.find(p => p.id == productId);

                    if (product) {
                        let multiplier = 1;
                        if (unitId) {
                            const unit = this.items[index].available_units.find(u => u.id == unitId);
                            if (unit) {
                                multiplier = unit.quantity;
                            }
                        }
                        this.items[index].harga_beli = product.cost_price * multiplier;
                    } else {
                        this.items[index].harga_beli = 0;
                    }
                },
                calculateSubtotal() {
                    return this.items.reduce((total, item) => {
                        return total + (item.qty_beli * item.harga_beli);
                    }, 0);
                },
                calculateGrandTotal() {
                    let subtotal = this.calculateSubtotal();
                    let disc = parseFloat(this.diskon) || 0;
                    let tax = parseFloat(this.ppn) || 0;
                    return subtotal - disc + tax;
                },
                formatRupiah(amount) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
                },
                updateAvailableProducts() {
                    const selectedIds = this.items.map(item => String(item.product_id)).filter(id => id !== '');
                    $('.product-select').each(function() {
                        const currentValue = String($(this).val() || '');
                        $(this).find('option').each(function() {
                            const optionValue = String($(this).val() || '');
                            if (optionValue !== '' && optionValue !== currentValue && selectedIds.includes(
                                    optionValue)) {
                                $(this).prop('disabled', true);
                            } else {
                                $(this).prop('disabled', false);
                            }
                        });
                        // Refresh select2 UI to reflect disabled states without triggering 'change'
                        $(this).select2({
                            width: '100%',
                            placeholder: 'Select Product'
                        });
                    });
                }
            }
        }
    </script>
@endsection
@endsection

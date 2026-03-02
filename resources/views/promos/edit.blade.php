@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Promotion</h2>

        <div class="bg-white shadow-md rounded-xl p-6" x-data="{
            type: '{{ $promo->type }}',
            discountType: '{{ $promo->discount_type ?? 'percentage' }}'
        }">
            <form action="{{ route('promos.update', $promo) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Name</label>
                        <input type="text" name="name" value="{{ $promo->name }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Type</label>
                        <select name="type" x-model="type"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="simple_discount">Simple Discount (Fixed/%)</option>
                            <option value="buy_x_get_y">Buy X Get Y (Free Item)</option>
                            <option value="bundle">Bundling (Bulk Price)</option>
                        </select>
                    </div>
                </div>

                <!-- Target Product -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Product</label>
                    <select name="product_id" class="select2 w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">-- Apply to All Products (if supported) or Select One --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ $promo->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }} - Rp {{ number_format($product->selling_price, 0) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dynamic Fields -->

                <!-- Simple Discount Fields -->
                <div x-show="type === 'simple_discount'" class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                    <h3 class="font-semibold text-blue-800 mb-4">Discount Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                            <select name="discount_type" x-model="discountType"
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span x-text="discountType === 'fixed' ? 'Rp' : '%'"
                                        class="text-gray-500 sm:text-sm"></span>
                                </div>
                                <input type="number" name="discount_value" value="{{ $promo->discount_value }}"
                                    class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buy X Get Y Fields -->
                <div x-show="type === 'buy_x_get_y'" class="bg-purple-50 p-4 rounded-lg mb-6 border border-purple-100"
                    style="display: none;">
                    <h3 class="font-semibold text-purple-800 mb-4">Buy X Get Y Rules</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buy Quantity (X)</label>
                            <input type="number" name="buy_qty" value="{{ $promo->buy_qty }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm" :disabled="type !== 'buy_x_get_y'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Get Quantity (Y)</label>
                            <input type="number" name="get_qty" value="{{ $promo->get_qty }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reward Product (Optional)</label>
                            <select name="reward_product_id" class="select2 w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">-- Same as Target Product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $promo->reward_product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bundle Fields -->
                <div x-show="type === 'bundle'" class="bg-green-50 p-4 rounded-lg mb-6 border border-green-100"
                    style="display: none;">
                    <h3 class="font-semibold text-green-800 mb-4">Bundle Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buy Quantity</label>
                            <input type="number" name="buy_qty" value="{{ $promo->buy_qty }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm" :disabled="type !== 'bundle'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bundle Price (Total)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="bundle_price" value="{{ $promo->bundle_price }}"
                                    class="pl-10 w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date"
                            value="{{ $promo->start_date ? $promo->start_date->format('Y-m-d') : '' }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date"
                            value="{{ $promo->end_date ? $promo->end_date->format('Y-m-d') : '' }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $promo->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('promos.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">Cancel</a>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Update
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>
@endsection

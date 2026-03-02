@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <a href="{{ route('products.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            Add New Product
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-700">Product List</h3>
            <form action="{{ route('products.index') }}" method="GET" class="flex gap-2 w-full md:w-1/3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, barcode, or SKU..."
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                @if (request('search'))
                    <a href="{{ route('products.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center justify-center">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i data-feather="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">Barcode</th>
                        <th class="py-3 px-6">SKU</th>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Category</th>
                        <th class="py-3 px-6">Supplier</th>
                        <th class="py-3 px-6">Stock</th>
                        <th class="py-3 px-6">Price (Sell)</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($products as $product)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 whitespace-nowrap font-medium">{{ $product->barcode }}</td>
                            <td class="py-3 px-6">{{ $product->sku ?? '-' }}</td>
                            <td class="py-3 px-6">{{ $product->product_name }}</td>
                            <td class="py-3 px-6">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="py-3 px-6">{{ $product->supplier->name ?? 'N/A' }}</td>
                            <td class="py-3 px-6">
                                <span
                                    class="{{ $product->stock <= $product->stock_min ? 'text-red-500 font-bold' : 'text-green-600' }}">
                                    {{ $product->stock }}
                                </span>
                                <span class="text-xs text-gray-400">({{ $product->satuan }})</span>
                            </td>
                            <td class="py-3 px-6 font-medium">Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-4 transform hover:text-red-500 hover:scale-110">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($products->isEmpty())
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">No products found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection

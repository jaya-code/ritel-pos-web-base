@extends('layouts.admin')

@section('title', 'Edit Store')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Store: {{ $store->name }}</h1>
            <a href="{{ route('stores.index') }}"
                class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('stores.update', $store->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                    <input type="text" name="name" value="{{ old('name', $store->name) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                    <input type="text"
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 shadow-sm cursor-not-allowed"
                        value="{{ $store->owner->name ?? 'No Owner' }} ({{ $store->owner->email ?? '-' }})" disabled>
                    <p class="text-gray-500 text-xs mt-1">Owner details cannot be changed here.</p>
                </div>

                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $store->phone) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">QRIS Dynamic Fee (%)</label>
                        <input type="number" step="0.01" name="qris_fee"
                            value="{{ old('qris_fee', $store->qris_fee ?? 0) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('qris_fee') border-red-500 @enderror">
                        <p class="text-blue-600 text-xs mt-1">Set the admin fee percentage for dynamic QRIS transactions.
                        </p>
                        @error('qris_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai (Subscription Valid
                        Until)</label>
                    <input type="date" name="subscription_until"
                        value="{{ old('subscription_until', $store->subscription_until ? $store->subscription_until->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('subscription_until') border-red-500 @enderror">
                    @error('subscription_until')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('address') border-red-500 @enderror"
                        required>{{ old('address', $store->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-6 border-t pt-4 gap-3">
                    <a href="{{ route('stores.index') }}"
                        class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Store
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

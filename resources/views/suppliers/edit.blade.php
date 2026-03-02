@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Supplier</h1>
            <a href="{{ route('suppliers.index') }}"
                class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                        <input type="text" name="name" value="{{ $supplier->name }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Info</label>
                        <input type="text" name="contact_info" value="{{ $supplier->contact_info }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors">{{ $supplier->address }}</textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Supplier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Store Configuration')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Store Configuration</h2>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-semibold text-gray-700">General Information</h3>
                <p class="text-sm text-gray-500 mt-1">Update your store identity and contact details.</p>
            </div>

            <div class="p-6">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Inputs -->
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Store
                                    Name</label>
                                <input type="text" name="name" id="name" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-200"
                                    value="{{ old('name', $store->name ?? '') }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone
                                    Number</label>
                                <input type="text" name="phone" id="phone"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-200"
                                    value="{{ old('phone', $store->phone ?? '') }}">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" id="address" rows="3" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-200">{{ old('address', $store->address ?? '') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column: Logo Upload -->
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Store Logo</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors"
                                    id="drop-zone">
                                    <div class="space-y-1 text-center">
                                        <!-- Preview Container -->
                                        <div class="mx-auto h-24 w-24 text-gray-400 relative mb-3">
                                            @if (isset($store->logo) && $store->logo)
                                                <img src="{{ asset('storage/' . $store->logo) }}" alt="Store Logo"
                                                    class="h-24 w-24 object-cover rounded-full shadow-sm" id="logo-preview">
                                            @else
                                                <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto"
                                                    id="logo-placeholder">
                                                    <i data-feather="image" class="h-10 w-10 text-gray-400"></i>
                                                </div>
                                                <img src="" alt="Preview"
                                                    class="h-24 w-24 object-cover rounded-full shadow-sm hidden"
                                                    id="logo-preview">
                                            @endif
                                        </div>

                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="logo"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                                <input id="logo" name="logo" type="file" class="sr-only"
                                                    accept="image/*" onchange="previewImage(event)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                </div>
                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i data-feather="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100">
                        <button type="reset"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-200 transition-all">Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');

                output.src = reader.result;
                output.classList.remove('hidden');

                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection

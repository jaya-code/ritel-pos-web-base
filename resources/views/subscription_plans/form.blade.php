@extends('layouts.admin')

@section('title', isset($plan) ? 'Edit Paket Langganan' : 'Tambah Paket Langganan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($plan) ? 'Edit' : 'Tambah' }} Paket Langganan</h1>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ isset($plan) ? route('subscription-plans.update', $plan) : route('subscription-plans.store') }}"
            method="POST" class="p-6">
            @csrf
            @if (isset($plan))
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" required
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    @error('name')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', isset($plan) ? (int) $plan->price : '') }}"
                        required min="0" step="1"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    @error('price')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari)</label>
                    <input type="number" name="duration_days"
                        value="{{ old('duration_days', $plan->duration_days ?? 30) }}" required min="1"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <p class="text-xs text-gray-500 mt-1">Contoh: 30 untuk 1 bulan, 365 untuk 1 tahun.</p>
                    @error('duration_days')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center mt-4">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <label class="ml-2 text-sm text-gray-700">Aktifkan Paket Ini</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-6 border-t border-gray-50">
                <a href="{{ route('subscription-plans.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                    {{ isset($plan) ? 'Simpan Perubahan' : 'Simpan Paket' }}
                </button>
            </div>
        </form>
    </div>
@endsection

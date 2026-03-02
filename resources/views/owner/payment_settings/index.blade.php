@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4 text-slate-700 font-bold">Payment Settings</h2>

                <div class="card shadow-sm border-0 rounded-xl">
                    <div class="card-body p-6">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('payment-settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <h5 class="text-slate-600 font-semibold mb-3">Active Payment Methods</h5>
                            <p class="text-xs text-slate-500 mb-4">Select the payment methods you want to accept in the POS
                                system.</p>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                                @php
                                    $config = $store->payment_config ?? [];
                                    $methods = $config['enabled_methods'] ?? [];
                                @endphp

                                <!-- Tunai -->
                                <div>
                                    <label class="payment-method-card cursor-pointer block relative h-full">
                                        <input type="checkbox" name="payment_methods[]" value="Tunai" class="peer sr-only"
                                            {{ in_array('Tunai', $methods) ? 'checked' : '' }}>
                                        <div
                                            class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center text-center h-full">
                                            <div class="mb-2 text-slate-400 peer-checked:text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="font-bold text-sm text-slate-700 peer-checked:text-indigo-700">Tunai</span>
                                        </div>
                                        <div
                                            class="absolute top-2 right-2 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </label>
                                </div>

                                <!-- Qris Statis -->
                                <div>
                                    <label class="payment-method-card cursor-pointer block relative h-full">
                                        <input type="checkbox" name="payment_methods[]" value="Qris Statis"
                                            class="peer sr-only" {{ in_array('Qris Statis', $methods) ? 'checked' : '' }}>
                                        <div
                                            class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center text-center h-full">
                                            <div class="mb-2 text-slate-400 peer-checked:text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-sm text-slate-700 peer-checked:text-indigo-700">Qris
                                                Statis</span>
                                        </div>
                                        <div
                                            class="absolute top-2 right-2 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </label>
                                </div>

                                <!-- Qris Dinamis -->
                                <div>
                                    <label class="payment-method-card cursor-pointer block relative h-full">
                                        <input type="checkbox" name="payment_methods[]" value="Qris Dinamis"
                                            class="peer sr-only" {{ in_array('Qris Dinamis', $methods) ? 'checked' : '' }}>
                                        <div
                                            class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center text-center h-full">
                                            <div class="mb-2 text-slate-400 peer-checked:text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-sm text-slate-700 peer-checked:text-indigo-700">Qris
                                                Dinamis</span>
                                            <span class="text-[10px] text-slate-400 mt-1">Fee:
                                                {{ number_format($store->qris_fee, 2) }}%</span>
                                        </div>
                                        <div
                                            class="absolute top-2 right-2 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </label>
                                </div>

                                <!-- Debit -->
                                <div>
                                    <label class="payment-method-card cursor-pointer block relative h-full">
                                        <input type="checkbox" name="payment_methods[]" value="Debit" class="peer sr-only"
                                            {{ in_array('Debit', $methods) ? 'checked' : '' }}>
                                        <div
                                            class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center text-center h-full">
                                            <div class="mb-2 text-slate-400 peer-checked:text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="font-bold text-sm text-slate-700 peer-checked:text-indigo-700">Debit</span>
                                        </div>
                                        <div
                                            class="absolute top-2 right-2 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Static QRIS Upload -->
                            <div class="mb-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="text-slate-600 font-semibold mb-3">QRIS Static Configuration</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label text-sm fw-bold">Upload QRIS Image</label>
                                        <input type="file" name="qris_static_image" class="form-control"
                                            accept="image/*">
                                        <div class="form-text text-xs">Upload your static QRIS code image (JPG, PNG). This
                                            will be displayed on the POS when 'Qris Statis' is selected.</div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        @if (isset($config['qris_static_image']) && $config['qris_static_image'])
                                            <div class="mt-2">
                                                <p class="text-xs mb-2 font-semibold text-slate-500">Current Image:</p>
                                                <img src="{{ asset('storage/' . $config['qris_static_image']) }}"
                                                    alt="QRIS Static"
                                                    class="img-fluid rounded shadow-sm max-h-40 border border-white">
                                            </div>
                                        @else
                                            <div
                                                class="mt-2 flex items-center justify-center h-32 border-2 border-dashed border-slate-300 rounded-lg bg-white">
                                                <p class="text-xs text-slate-400">No image uploaded</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic QRIS Info -->
                            <div class="mb-5 bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                                <h5 class="text-indigo-800 font-semibold mb-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    About QRIS Dynamic
                                </h5>
                                <p class="text-sm text-indigo-700 mb-0">
                                    Dynamic QRIS is integrated automatically via Midtrans.
                                    <br>
                                    The current admin fee for Dynamic QRIS transactions is <span
                                        class="font-bold border-b border-indigo-300">{{ number_format($store->qris_fee, 2) }}%</span>
                                    per transaction.
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

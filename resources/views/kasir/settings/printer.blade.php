@extends('layouts.cashier')

@section('content')
    <div class="flex flex-col h-full bg-slate-50 relative overflow-hidden">
        <!-- Header -->
        <div class="bg-white px-6 py-4 border-b border-slate-200 shadow-sm flex-none z-10">
            <h2 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Printer Settings
            </h2>
            <p class="text-sm text-slate-500 mt-1">Configure your preferred receipt printing method.</p>
        </div>

        <!-- Content -->
        <div class="flex-grow overflow-y-auto p-4 sm:p-6 pb-24">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden max-w-2xl mx-auto">
                <div class="p-4 sm:p-6">
                    <form action="{{ route('kasir.settings.printer.save') }}" method="POST">
                        @csrf

                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Select
                                Printing Method</label>

                            <!-- Web Print -->
                            <div class="relative flex items-start p-4 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $user->print_method == 'web' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700' }}"
                                onclick="document.getElementById('method-web').click()">
                                <div class="flex items-center h-6">
                                    <input id="method-web" name="print_method" type="radio" value="web"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-600"
                                        {{ $user->print_method == 'web' ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-white">Standard Web
                                        Print <span
                                            class="text-xs font-normal text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 ml-2 rounded-full border border-slate-200 dark:border-slate-600">Default</span></span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Uses the classic
                                        browser
                                        `window.print()` dialog. Good for desktops or basic setups.</span>
                                </div>
                            </div>

                            <!-- Android Mate Bluetooth -->
                            <div class="relative flex items-start p-4 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $user->print_method == 'mate_bluetooth' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700' }}"
                                onclick="document.getElementById('method-android').click()">
                                <div class="flex items-center h-6">
                                    <input id="method-android" name="print_method" type="radio" value="mate_bluetooth"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-600"
                                        {{ $user->print_method == 'mate_bluetooth' ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-white">Android App
                                        (Bluetooth
                                        Print)</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Automatically
                                        redirects to the
                                        "Bluetooth Print" app by MateTech for direct thermal printing. Requires the app to
                                        be installed from the Play Store.</span>
                                </div>
                            </div>

                            <!-- iOS Bprint -->
                            <div class="relative flex items-start p-4 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $user->print_method == 'mate_bluetooth_ios' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700' }}"
                                onclick="document.getElementById('method-ios').click()">
                                <div class="flex items-center h-6">
                                    <input id="method-ios" name="print_method" type="radio" value="mate_bluetooth_ios"
                                        class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-600"
                                        {{ $user->print_method == 'mate_bluetooth_ios' ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-white">iOS App
                                        (Bluetooth
                                        Print)</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Automatically
                                        redirects to the
                                        "Bluetooth Print" app for iPhone/iPad users for direct thermal printing. Requires
                                        the app from the App Store.</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-indigo-50 dark:bg-slate-900 border-t border-indigo-100 dark:border-slate-700 p-4">
                    <h4
                        class="text-xs font-bold text-indigo-800 dark:text-indigo-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        How to Setup Mobile App Printing
                    </h4>
                    <ul class="text-xs text-indigo-700/80 dark:text-indigo-300/80 space-y-1 list-disc list-inside">
                        <li>Download "Bluetooth Print" on <a
                                href="https://play.google.com/store/apps/details?id=mate.bluetoothprint" target="_blank"
                                class="underline hover:text-indigo-900 dark:hover:text-indigo-400">Android</a> or <a
                                href="https://apps.apple.com/us/app/id1599863946" target="_blank"
                                class="underline hover:text-indigo-900 dark:hover:text-indigo-400">iOS</a>.</li>
                        <li>Connect your Thermal Printer to your device via Bluetooth.</li>
                        <li>Open the app, select your printer, and <strong class="dark:text-indigo-200">Enable Browser Print
                                function</strong>.</li>
                        <li>Leave the app running in the background while using this POS.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple visual update for radio buttons selection
        document.querySelectorAll('input[type=radio][name=print_method]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Remove active classes
                document.querySelectorAll('input[type=radio][name=print_method]').forEach(function(r) {
                    const parent = r.closest('div.relative');
                    parent.classList.remove('border-indigo-500', 'bg-indigo-50/50');
                    parent.classList.add('border-slate-200');
                });
                // Add to selected
                if (this.checked) {
                    const parent = this.closest('div.relative');
                    parent.classList.remove('border-slate-200');
                    parent.classList.add('border-indigo-500', 'bg-indigo-50/50');
                }
            });
        });
    </script>
@endsection

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Habis - R-POS</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body
    class="bg-gray-50 dark:bg-slate-900 min-h-screen flex items-center justify-center transition-colors duration-200 p-4">

    <div
        class="text-center max-w-md w-full bg-white dark:bg-slate-800 rounded-3xl shadow-xl p-8 border border-transparent dark:border-slate-700">
        <div
            class="w-24 h-24 bg-amber-100 dark:bg-amber-900/30 text-amber-500 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="text-9xl font-extrabold text-gray-200 dark:text-slate-700 tracking-widest leading-none mb-2">419</h1>
        <div
            class="bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wider inline-block -mt-6 relative z-10 shadow-md">
            Sesi Kadaluarsa
        </div>

        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-8 mb-3">Halaman Sudah Kadaluarsa</h2>

        <p class="text-gray-500 dark:text-slate-400 mb-8 leading-relaxed">
            Maaf, sesi Anda telah habis karena tidak ada aktivitas dalam waktu lama, atau halaman ini dibiarkan terbuka
            terlalu lama. Silakan muat ulang atau kembali ke halaman utama.
        </p>

        <div class="flex flex-col gap-3">
            <button onclick="window.location.reload()"
                class="w-full bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 border border-gray-300 dark:border-slate-600 py-3 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Muat Ulang Halaman
            </button>
            <a href="{{ url('/') }}"
                class="w-full bg-indigo-600 text-white py-3 rounded-xl font-semibold shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 hover:shadow-indigo-600/50 transition-all flex items-center justify-center gap-2 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - R-POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden text-center">
        <div class="p-8">
            <div class="mb-6 flex justify-center">
                <div class="h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-2">Verify your email</h1>
            <p class="text-slate-500 mb-8">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you?
            </p>

            @if (session('message') == 'Verification link sent!')
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-medium">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-lg shadow-indigo-200">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-slate-400 hover:text-slate-600 text-sm font-medium transition-colors">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

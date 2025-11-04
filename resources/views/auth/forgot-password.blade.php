<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | AgriEcom</title>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-lime-50 to-green-100">
    <div class="flex flex-col items-center justify-center w-full max-w-md bg-white rounded-3xl shadow-2xl p-10 mt-10">
        <div class="flex flex-col items-center w-full">
            <h2 class="text-2xl font-extrabold text-green-800 mb-2 tracking-wide">Forgot your password?</h2>
            <p class="text-green-700 text-base text-center mb-6">Enter your email address and we'll send you a password reset link.</p>
            <form method="POST" action="{{ route('password.email') }}" class="w-full flex flex-col gap-4">
                @csrf
                <div>
                    <label for="email" class="block text-green-800 font-semibold mb-1">Email</label>
                    <input id="email" type="email" name="email" required autofocus class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 bg-lime-50" placeholder="Enter your email address">
                </div>
                @if ($errors->any())
                    <div class="mb-2 text-red-600 text-sm font-semibold text-center">
                        {{ $errors->first() }}
                    </div>
                @endif
                <button type="submit" class="w-full mt-2 bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-xl shadow transition">Send Password Reset Link</button>
            </form>
            <div class="mt-6 text-center w-full">
                <a href="{{ route('login') }}" class="text-green-700 hover:underline font-semibold">&larr; Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>

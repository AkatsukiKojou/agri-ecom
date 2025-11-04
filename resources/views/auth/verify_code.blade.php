<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriEcom | Email Verification</title>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-lime-50 to-green-100">
    <div class="bg-white rounded-3xl shadow-2xl flex flex-col md:flex-row w-full max-w-3xl overflow-hidden">
        <!-- Left Side: System Info -->
        <div class="md:w-1/2 flex flex-col items-center justify-center bg-gradient-to-br from-green-700 via-lime-400 to-green-600 p-10 text-white">
            <img src="{{ asset('agri-icon.png') }}" alt="AgriEcom Logo" class="w-20 h-20 mb-6 rounded-full shadow-lg bg-white">
            <h2 class="text-3xl font-extrabold mb-2 tracking-wide">AgriEcom</h2>
            <p class="text-lg font-semibold mb-4 text-lime-100 text-center">Your Gateway to Modern Agriculture</p>
            <p class="text-base text-lime-50 text-center">AgriEcom connects farmers, service providers, and buyers in one trusted platform. Discover, book, and buy with ease!</p>
        </div>
        <!-- Right Side: Verification Form -->
        <div class="md:w-1/2 flex flex-col justify-center p-10">
            <h2 class="text-2xl font-extrabold text-green-800 mb-4 text-center">Email Verification</h2>
            <p class="text-green-700 text-center mb-2">Enter the 6-digit code sent to your email.</p>
            <div class="text-green-900 text-center mb-4">
                <span class="font-semibold">Verifying:</span>
                <span class="bg-lime-100 text-green-800 rounded px-3 py-1 font-bold">{{ old('email', request('email')) }}</span>
            </div>
            <form method="POST" action="{{ route('verify.code') }}" class="w-full max-w-sm mx-auto">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', request('email')) }}">
                <div class="mb-4">
                    <label for="verification_code" class="block text-green-800 font-semibold mb-1">Verification Code</label>
                    <input type="text" id="verification_code" name="verification_code" maxlength="6" required pattern="[0-9]{6}" class="mt-1 block w-full border border-gray-300 rounded-md p-2 text-center text-lg tracking-widest" placeholder="Enter code">
                    @error('verification_code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if(session('error'))
                    <div class="mb-2 text-red-600 text-sm font-semibold text-center">{{ session('error') }}</div>
                @endif
                <button type="submit" class="w-full mt-6 bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl shadow transition text-lg flex items-center justify-center gap-2">
                    <i class="bi bi-shield-check"></i> Verify
                </button>
            </form>
        </div>
    </div>
</body>
</html>
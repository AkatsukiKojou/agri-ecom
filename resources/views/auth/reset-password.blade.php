<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | AgriEcom</title>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-lime-50 to-green-100">
    <div class="flex flex-col items-center justify-center w-full max-w-md bg-white rounded-3xl shadow-2xl p-10 mt-10">
        <div class="flex flex-col items-center w-full">
            <h2 class="text-2xl font-extrabold text-green-800 mb-2 tracking-wide">Reset your password</h2>
            <p class="text-green-700 text-base text-center mb-6">Enter your email and set a new password for your account.</p>
            <div id="resetFormContainer">
                <form id="resetForm" method="POST" action="{{ route('password.store') }}" class="w-full flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div>
                        <label for="email" class="block text-green-800 font-semibold mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 bg-lime-50">
                        @if ($errors->has('email'))
                            <div class="text-red-600 text-sm font-semibold mt-1">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="password" class="block text-green-800 font-semibold mb-1">New Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 bg-lime-50">
                        @if ($errors->has('password'))
                            <div class="text-red-600 text-sm font-semibold mt-1">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-green-800 font-semibold mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 bg-lime-50">
                        @if ($errors->has('password_confirmation'))
                            <div class="text-red-600 text-sm font-semibold mt-1">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>
                    <button type="submit" class="w-full mt-2 bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-xl shadow transition">Reset Password</button>
                </form>
            </div>
            <div id="successAnimation" class="hidden flex-col items-center justify-center w-full mt-8">
                <span class="inline-block bg-green-700 rounded-full p-6 mb-4 animate-bounce">
                    <i class="bi bi-check-lg text-white text-4xl"></i>
                </span>
                <h3 class="text-xl font-bold text-green-800 mb-2">Password Changed Successfully!</h3>
                <p class="text-green-700 text-center mb-2">You will be redirected to login in a moment...</p>
            </div>
            <div class="mt-6 text-center w-full">
                <a href="{{ route('login') }}" class="text-green-700 hover:underline font-semibold">&larr; Back to Login</a>
            </div>
        </div>
    </div>
</body>
<script>
    // Only run this if there are no validation errors and the form was just submitted
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Let the form submit, but after redirect, show animation if redirected back with status
                sessionStorage.setItem('showResetSuccess', '1');
            });
        }
        // If redirected after successful reset, show animation
        if (sessionStorage.getItem('showResetSuccess') === '1' && !document.querySelector('.text-red-600')) {
            const formContainer = document.getElementById('resetFormContainer');
            const successAnim = document.getElementById('successAnimation');
            if (formContainer && successAnim) {
                formContainer.style.display = 'none';
                successAnim.classList.remove('hidden');
                setTimeout(function() {
                    sessionStorage.removeItem('showResetSuccess');
                    window.location.href = "{{ route('login') }}";
                }, 5000);
            }
        }
    });
</script>
</html>

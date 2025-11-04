    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Registering | AgriEcom</title>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .thankyou-anim {
            animation: popIn 1s cubic-bezier(.68,-0.55,.27,1.55) forwards;
        }
        @keyframes popIn {
            0% { transform: scale(0.7); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-lime-50 to-green-100">
    <div class="flex flex-col items-center justify-center w-full max-w-md bg-white rounded-3xl shadow-2xl p-10">
        <div class="thankyou-anim flex flex-col items-center">
            <img src="{{ asset('agri-icon.png') }}" alt="AgriEcom Logo" class="w-20 h-20 mb-6 rounded-full shadow-lg bg-white animate-bounce">
                <h2 class="text-3xl font-extrabold text-green-800 mb-2 tracking-wide">Thank You for Registering!</h2>
                <p class="text-lg font-semibold mb-4 text-green-700 text-center">Your registration is complete. You may now log in to start using AgriEcom.</p>
                <div class="flex items-center justify-center mb-4">
                    <i class="bi bi-check-circle-fill text-green-500 text-6xl animate-bounce"></i>
                </div>
                <a href="{{ route('login') }}" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-full font-bold shadow hover:bg-green-700 transition">Log In</a>
        </div>
    </div>
        <!-- No auto-redirect, user must click login -->
</body>
</html>

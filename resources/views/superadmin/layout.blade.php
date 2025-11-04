{{-- filepath: resources/views/superadmin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 via-lime-50 to-green-100 min-h-screen flex flex-col text-green-900 font-sans">

    <!-- Navbar -->
    <nav class="w-full backdrop-blur-lg bg-white/60 shadow-lg border-b border-green-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <i class="bi bi-shield-lock-fill text-green-700 text-3xl"></i>
            <span class="text-2xl font-extrabold text-green-900">Admin Dashboard</span>
        </div>
        <form method="GET" action="{{ route('superadmin.logout') }}">
            <button type="submit" class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg font-semibold shadow transition">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>

    <div class="flex min-h-[80vh] w-full flex-1">
        <!-- Sidebar -->
        <aside class="w-64 bg-white/90 shadow-xl rounded-2xl m-4 flex flex-col py-8 px-4">
            <div class="flex flex-col items-center mb-8">
                <span class="inline-block bg-green-700 rounded-full p-4 mb-2 shadow">
                    <i class="bi bi-shield-lock-fill text-lime-100 text-3xl"></i>
                </span>
                <h2 class="text-xl font-extrabold text-green-900 mb-1">Admin</h2>
                <span class="text-green-700 text-xs">Platform Control Panel</span>
            </div>
            <nav class="flex flex-col gap-2">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
                <a href="{{ route('superadmin.users') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-people-fill"></i> Manage Users
                </a>
                <a href="{{ route('manageadmins.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-person-badge-fill"></i> Manage LSA
                </a>
                <a href="{{ route('superadmin.products') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-basket-fill"></i> Manage Products
                </a>
                <a href="{{ route('superadmin.services') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-gear-wide-connected"></i> Manage Training Services
                </a>
                <a href="{{ route('superadmin.reports') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-bar-chart-line-fill"></i>Reports & Analytics
                </a>
                <a href="{{ route('superadmin.activitylog') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 text-green-900 font-semibold transition">
                    <i class="bi bi-clock-history"></i> Activity Log
                </a>
              
            </nav>
        </aside>

        <!-- Main Content -->
            @yield('content')
     
    </div>

    <footer class="bg-gradient-to-r from-green-900 via-green-700 to-lime-600 text-lime-100 text-sm text-center py-4 mt-8 shadow-inner">
        <div class="flex flex-col md:flex-row items-center justify-center gap-2">
            <span>&copy; {{ date('Y') }} <span class="font-semibold">AgriEcom</span>. All rights reserved.</span>
            <span class="hidden md:inline-block">|</span>
            <span class="italic">Empowering Agriculture, Empowering You.</span>
        </div>
    </footer>
</body>
</html>
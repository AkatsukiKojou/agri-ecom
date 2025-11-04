@extends('superadmin.layout')

@section('title', 'Super Admin Dashboard')

@section('content')
<main class="flex-1 flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-6xl">
        <h2 class="text-4xl font-extrabold text-green-900 mb-2 text-center drop-shadow">Welcome, Super Admin!</h2>
        <p class="text-green-700 text-center mb-10 text-lg">You have full access to manage the platform.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <a href="{{ route('superadmin.users') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-people-fill text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Manage Users</span>
                <span class="text-green-600 text-sm">View, add, or remove users</span>
            </a>
            <!-- Card 2 -->
            <a href="{{ route('manageadmins.index') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-person-badge-fill text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Manage LSA</span>
                <span class="text-green-600 text-sm">LSA accounts & permissions</span>
            </a>
            <!-- Card 3 -->
            <a href="{{ route('superadmin.products') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-basket-fill text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Manage Products</span>
                <span class="text-green-600 text-sm">Product listings & inventory</span>
            </a>
            <!-- Card 4 -->
            <a href="{{ route('superadmin.services') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-gear-wide-connected text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Manage Training Services</span>
                <span class="text-green-600 text-sm">Training Service offerings</span>
            </a>
            <!-- Card 5 -->
            <a href="{{ route('superadmin.reports') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-bar-chart-line-fill text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Report & Analytics</span>
                <span class="text-green-600 text-sm">Platform stats & insights</span>
            </a>
            <!-- Card 6 -->
            <a href="{{ route('superadmin.activitylog') }}" class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-xl hover:shadow-2xl border border-green-100 hover:border-green-300 transition group">
                <span class="bg-green-100 p-4 rounded-full mb-4 shadow">
                    <i class="bi bi-clock-history text-4xl text-green-700 group-hover:text-green-900"></i>
                </span>
                <span class="font-bold text-green-900 text-lg mb-1">Activity Log</span>
                <span class="text-green-600 text-sm">Track all platform actions</span>
            </a>
        
        </div>
    </div>
</main>
@endsection
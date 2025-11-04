{{-- resources/views/superadmin/users/show.blade.php --}}
@extends('superadmin.layout')
@section('title', 'User Details')
@section('content')
<div class="w-full mx-auto py-12 px-4">
    <div class="bg-gradient-to-r from-green-100 to-green-50 rounded-2xl shadow-xl p-8 relative">
        <div class="absolute -top-8 left-1/2 -translate-x-1/2">
            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('agri-profile.png') }}" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg" alt="Profile">
        </div>
        <div class="pt-28 pb-4 text-center">
            <h2 class="text-3xl font-bold text-green-900 mb-1 flex items-center justify-center gap-2"><i class="bi bi-person-circle"></i> {{ $user->name }}</h2>
            <div class="text-green-700 text-lg mb-2">{{ $user->email }}</div>
            <span class="inline-block bg-green-200 text-green-800 text-xs px-3 py-1 rounded-full font-semibold">{{ ucfirst($user->role) }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div>
                <div class="font-semibold text-green-800 mb-1">Phone</div>
                <div class="text-green-700 text-sm">{{ $user->phone ?? '-' }}</div>
            </div>
            <div>
                <div class="font-semibold text-green-800 mb-1">Gender</div>
                <div class="text-green-700 text-sm">{{ $user->gender ?? '-' }}</div>
            </div>
            <div>
                <div class="font-semibold text-green-800 mb-1">Birthday</div>
                <div class="text-green-700 text-sm">{{ $user->date_of_birth ?? '-' }}</div>
            </div>
            <div>
                <div class="font-semibold text-green-800 mb-1">Status</div>
                <div class="text-green-700 text-sm">{{ $user->status ?? 'Active' }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="font-semibold text-green-800 mb-1">Address</div>
                <div class="text-green-700 text-sm">
                    {{ $user->region ?? ($user->profile->region ?? '') }},
                    {{ $user->province ?? ($user->profile->province ?? '') }},
                    {{ $user->city ?? ($user->profile->city ?? '') }}<br>
                    {{ $user->barangay ?? ($user->profile->barangay ?? '') }} {{ $user->address ?? '' }}
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="font-semibold text-green-800 mb-1">Date Registered</div>
                <div class="text-green-700 text-sm">{{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}</div>
            </div>
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('superadmin.users') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition font-semibold">Back to Users</a>
        </div>
    </div>
</div>
@endsection

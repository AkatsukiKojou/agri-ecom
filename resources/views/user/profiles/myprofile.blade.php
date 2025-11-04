@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-3xl font-bold mb-6">My Profile</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-10">
        @csrf

        <!-- Profile Overview -->
        <div>
            <h3 class="text-xl font-semibold border-b pb-2 mb-4">Overview</h3>
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $user->profile_image_url }}" alt="Profile Image" class="w-20 h-20 rounded-full object-cover border">
                <input type="file" name="profile_image" class="block">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label class="block font-semibold">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border p-2 rounded" required>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div>
            <h3 class="text-xl font-semibold border-b pb-2 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-semibold">Alternate Email</label>
                    <input type="email" name="alternate_email" value="{{ old('alternate_email', $user->alternate_email) }}" class="w-full border p-2 rounded">
                </div>
            </div>
        </div>

        <!-- Places Lived -->
        <div>
            <h3 class="text-xl font-semibold border-b pb-2 mb-4">Places Lived</h3>
            <div>
                <label class="block font-semibold">Current City</label>
                <input type="text" name="current_city" value="{{ old('current_city', $user->current_city) }}" class="w-full border p-2 rounded">
            </div>
            <div class="mt-4">
                <label class="block font-semibold">Hometown</label>
                <input type="text" name="hometown" value="{{ old('hometown', $user->hometown) }}" class="w-full border p-2 rounded">
            </div>
        </div>

        <!-- Password Section -->
        <div>
            <h3 class="text-xl font-semibold border-b pb-2 mb-4">Update Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">New Password</label>
                    <input type="password" name="password" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full border p-2 rounded">
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection

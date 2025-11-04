@extends('user.layout')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-6 text-green-800">Edit Profile</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Name --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="name" class="block font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="w-full mt-1 p-2 border border-gray-300 rounded">
        </div>

        {{-- Username --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="username" class="block font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', auth()->user()->username) }}" class="w-full mt-1 p-2 border border-gray-300 rounded">
        </div>

        {{-- Email --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="email" class="block font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="w-full mt-1 p-2 border border-gray-300 rounded">
        </div>

        {{-- Phone --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="phone" class="block font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" class="w-full mt-1 p-2 border border-gray-300 rounded">
        </div>

        {{-- Address --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="address" class="block font-medium text-gray-700">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}" class="w-full mt-1 p-2 border border-gray-300 rounded">
        </div>

        {{-- Profile Photo --}}
        <div class="bg-gray-50 p-4 rounded border">
            <label for="photo" class="block font-medium text-gray-700 mb-1">Profile Photo</label>
            @if(auth()->user()->photo)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border">
                </div>
            @endif
            <input type="file" name="photo" id="photo" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <div class="text-right">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded shadow">Update Profile</button>
        </div>

    </form>
</div>
@endsection

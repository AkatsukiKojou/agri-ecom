

@extends('admin.layout')

@section('content')
<x-app-layout>
    <div class="max-w-xl mx-auto p-6 bg-white shadow rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Admin Settings</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Address</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Current Profile Photo</label>
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover mb-2">
                @else
                    <p class="text-gray-500 mb-2">No profile photo uploaded.</p>
                @endif

                <label class="block text-sm font-medium">Upload New Photo</label>
                <input type="file" name="photo" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">New Password</label>
                <input type="password" name="password" class="w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2">
            </div>

            <button class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Update</button>
        </form>
    </div>
</x-app-layout>
@endsection

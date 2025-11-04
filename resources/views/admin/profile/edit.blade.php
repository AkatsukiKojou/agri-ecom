@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-xl shadow-md border border-green-300">
    <h2 class="text-2xl font-semibold text-green-700 mb-6">Edit Profile</h2>

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-gray-700 font-medium">Farm Name</label>
            <input type="text" name="farm_name" value="{{ old('farm_name', $profile->farm_name) }}" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Farm Owner</label>
            <input type="text" name="farm_owner" value="{{ old('farm_owner', $profile->farm_owner) }}" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Location</label>
            <input type="text" name="location" value="{{ old('location', $profile->location) }}" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Description</label>
            <textarea name="description" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1">{{ old('description', $profile->description) }}</textarea>
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Phone Number</label>
            <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number) }}" class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $profile->email) }}" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Facebook</label>
            <input type="text" name="Facebook" value="{{ old('Facebook', $profile->Facebook) }}" class="w-full border border-gray-300 rounded px-4 py-2 mt-1">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Profile Photo</label>
            <input type="file" name="profile_photo" class="w-full mt-1">
            @if($profile->profile_photo)
                <img src="{{ asset('storage/' . $profile->profile_photo) }}" class="w-24 h-24 mt-2 object-cover rounded">
            @endif
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Certificate</label>
            <input type="file" name="certificate" class="w-full mt-1">
            @if($profile->certificate)
                <a href="{{ asset('storage/' . $profile->certificate) }}" target="_blank" class="text-green-600 underline">View Current</a>
            @endif
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Farm Photo</label>
            <input type="file" name="farm_photo" class="w-full mt-1">
            @if($profile->farm_photo)
                <img src="{{ asset('storage/' . $profile->farm_photo) }}" class="w-32 h-24 mt-2 object-cover rounded">
            @endif
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Documentary Requirements</label>
            <input type="file" name="documentary" class="w-full mt-1">
            @if($profile->documentary)
                <a href="{{ asset('storage/' . $profile->documentary) }}" target="_blank" class="text-green-600 underline">View File</a>
            @endif
        </div>

        <div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Update Profile</button>
        </div>
    </form>
</div>
@endsection

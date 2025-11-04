@extends('user.layout')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profile</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4 bg-white p-6 shadow rounded">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full mt-1 border rounded px-3 py-2 text-sm" required>
            @error('name')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                class="w-full mt-1 border rounded px-3 py-2 text-sm" required>
            @error('phone')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="address" rows="3"
                class="w-full mt-1 border rounded px-3 py-2 text-sm" required>{{ old('address', $user->address) }}</textarea>
            @error('address')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit"
            class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded">
            Save Changes
        </button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">My Shipping Addresses</h2>

    {{-- Add New Address Form --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Add New Address</h3>
        <form method="POST" action="{{ route('user.shipping.store') }}" class="space-y-4">
            @csrf
            <input name="name" type="text" placeholder="Full Name" required class="w-full border p-2 rounded">
            <input name="phone" type="text" placeholder="Phone Number" required class="w-full border p-2 rounded">
            <textarea name="address" rows="3" placeholder="Complete Address" required class="w-full border p-2 rounded"></textarea>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Add Address</button>
        </form>
    </div>

    {{-- List of Saved Addresses --}}
    @foreach ($shippingAddresses as $address)
    <div class="bg-white p-6 rounded-lg shadow mb-4">
        <div class="flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $address->name }}</p>
                <p class="text-sm">{{ $address->phone }}</p>
                <p class="text-sm text-gray-600">{{ $address->address }}</p>
                @if ($address->is_default)
                    <span class="text-green-600 text-xs font-semibold">Default Address</span>
                @endif
            </div>
            <div class="flex space-x-2">
                {{-- Set Default --}}
                @if (! $address->is_default)
                <form method="POST" action="{{ route('user.shipping.set-default', $address->id) }}">
                    @csrf
                    @method('PUT')
                    <button class="text-sm text-blue-600 hover:underline">Set as Default</button>
                </form>
                @endif

                {{-- Edit Address Button --}}
                <button @click="openEdit{{ $address->id }} = true" class="text-sm text-yellow-600 hover:underline">Edit</button>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-data="{ openEdit{{ $address->id }}: false }">
        <div x-show="openEdit{{ $address->id }}" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition>
            <div @click.away="openEdit{{ $address->id }} = false" class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Edit Address</h3>
                <form method="POST" action="{{ route('user.shipping.update', $address->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input name="name" type="text" value="{{ $address->name }}" required class="w-full border p-2 rounded">
                    <input name="phone" type="text" value="{{ $address->phone }}" required class="w-full border p-2 rounded">
                    <textarea name="address" rows="3" required class="w-full border p-2 rounded">{{ $address->address }}</textarea>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openEdit{{ $address->id }} = false" class="bg-gray-200 px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

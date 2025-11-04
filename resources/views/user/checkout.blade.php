@extends('user.layout')

@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold text-green-800 mb-4">Checkout</h2>

    <div class="flex items-center space-x-4 mb-6">
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded">
        <div>
            <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
            <p class="text-gray-600">Quantity: {{ $checkout['quantity'] }}</p>
            <p class="text-green-700 font-bold">Price: ₱{{ number_format($product->price, 2) }}</p>
        </div>
    </div>

    <div class="mb-4 text-right">
        <p class="text-lg font-semibold">Total: ₱{{ number_format($total, 2) }}</p>
    </div>

    <form action="{{ route('checkout.place') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
            Place Order
        </button>
    </form>
</div>
@endsection

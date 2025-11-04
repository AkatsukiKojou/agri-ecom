{{-- @extends('user.layout')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4 text-green-700">Order #{{ $order->id }} Details</h2>

    @foreach ($order->items as $item)
        <div class="border-b py-2">
            <p><strong>{{ $item->product_name }}</strong></p>
            <p>₱{{ number_format($item->price, 2) }} x {{ $item->quantity }} = ₱{{ number_format($item->total, 2) }}</p>
        </div>
    @endforeach

    <div class="mt-4 text-right font-semibold text-gray-700">
        Total: ₱{{ number_format($order->total, 2) }}
    </div>
</div>
@endsection --}}
@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Order #{{ $order->id }}</h1>

    <!-- Customer Info -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2 text-gray-700">Customer Info</h2>
        <p><strong>Name:</strong> {{ $order->name }}</p>
        <p><strong>Address:</strong> {{ $order->address }}</p>
        <p><strong>Location:</strong> {{ $order->location }}</p>
        <p><strong>Contact:</strong> {{ $order->contact }}</p>
    </div>

    <!-- Shipping & Payment -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2 text-gray-700">Shipping & Payment</h2>
        <p><strong>Shipping:</strong> {{ ucfirst($order->shipping) }}</p>
        <p><strong>Payment:</strong> {{ strtoupper($order->payment) }}</p>
        <p><strong>Status:</strong> 
            <span class="px-2 py-1 rounded text-white {{ 
                $order->status === 'pending' ? 'bg-yellow-500' : 
                ($order->status === 'completed' ? 'bg-green-600' : 'bg-red-500') 
            }}">
                {{ $order->status_label }}
            </span>
        </p>
    </div>

    <!-- Ordered Items -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Order Items</h2>
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Quantity</th>
                    <th class="px-4 py-2 text-left">Unit Price</th>
                    <th class="px-4 py-2 text-left">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $item->product_name }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">₱{{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-2">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Order Total -->
    <div class="bg-white rounded-lg shadow p-4 text-right">
        <p class="text-xl font-bold text-gray-800">Total: ₱{{ number_format($order->total_price, 2) }}</p>
    </div>
</div>
@endsection

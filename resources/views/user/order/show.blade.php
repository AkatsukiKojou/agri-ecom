@extends('user.layout')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white rounded-lg shadow p-8">
    <h2 class="text-2xl font-bold mb-2 text-green-700">Order #{{ $order->id }} Details</h2>
    <p class="text-xs text-gray-500 mb-4">Placed on {{ $order->created_at->format('F d, Y h:i A') }}</p>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p><strong>Status:</strong>
                <span class="px-2 py-1 rounded
                    @if($order->status == 'shipped') bg-blue-100 text-blue-600
                    @elseif($order->status == 'completed') bg-green-100 text-green-600
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-600
                    @else bg-yellow-100 text-yellow-600
                    @endif">
                    {{ $order->status_label }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'N/A') }}</p>
        </div>
        <div>
            <p><strong>Recipient:</strong> {{ $order->name }}</p>
            <p><strong>Phone:</strong> {{ $order->phone }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
        </div>
    </div>

    <h3 class="text-lg font-semibold mb-3 text-green-800">Products</h3>
    <div class="divide-y">
        @foreach ($order->items as $item)
            <div class="flex items-center py-4">
                @if($item->product && $item->product->image)
                    <img src="{{ asset('storage/' . $item->product->image) }}"
                         alt="{{ $item->product_name }}"
                         class="w-14 h-14 object-cover rounded border mr-4">
                @endif
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                    <p class="text-sm text-gray-700">₱{{ number_format($item->price, 2) }} x {{ $item->quantity }}</p>
                </div>
                <div class="font-semibold text-green-700">
                    ₱{{ number_format($item->price * $item->quantity, 2) }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-right text-xl font-bold text-green-700">
        @if(strtolower($order->payment_method) === 'cod')
            Total: ₱{{ number_format($order->total_price + ($order->shipping_fee ?? 0), 2) }}
            <span class="block text-xs text-gray-500 font-normal">
                (₱{{ number_format($order->total_price, 2) }} + Shipping: ₱{{ number_format($order->shipping_fee ?? 0, 2) }})
            </span>
        @else
            Total: ₱{{ number_format($order->total_price, 2) }}
        @endif
    </div>
</div>
@endsection

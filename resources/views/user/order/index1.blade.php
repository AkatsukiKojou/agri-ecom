@extends('user.layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Orders</h1>

    @if($orders->isEmpty())
        <div class="text-gray-600">You haven't placed any orders yet.</div>
    @else
        @foreach($orders as $order)
            <div class="bg-white shadow rounded mb-6 p-6">
                <div class="mb-2">
                    <h2 class="text-lg font-semibold text-gray-800">Order #{{ $order->id }}</h2>
                    <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y') }}</p>
                </div>

                <div class="mb-2 text-sm text-gray-700">
                    <p><strong>Status:</strong> {{ $order->status_label }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                </div>

                <table class="w-full table-auto mt-4 text-sm">
                    <thead class="bg-orange-100">
                        <tr>
                            <th class="p-2 text-left">Product</th>
                            <th class="p-2 text-left">Quantity</th>
                            <th class="p-2 text-left">Unit Price</th>
                            <th class="p-2 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $item->product_name }}</td>
                                <td class="p-2">{{ $item->quantity }}</td>
                                <td class="p-2">₱{{ number_format($item->price, 2) }}</td>
                                <td class="p-2">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>
@endsection

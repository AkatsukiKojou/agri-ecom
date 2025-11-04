@extends('user.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-2xl font-bold text-green-700 mb-4">Order Summary</h2>

    <div class="space-y-4">
        @foreach($items as $item)
            <div class="flex justify-between items-center border-b pb-2">
                <div>
                    <p class="font-medium">{{ $item['name'] }}</p>
                    <p class="text-sm text-gray-600">₱{{ number_format($item['price'], 2) }} × {{ $item['quantity'] }}</p>
                </div>
                <div class="font-semibold text-gray-800">
                    ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-right mt-6 text-xl font-bold text-green-800">
        Total: ₱{{ number_format($total, 2) }}
    </div>
</div>
@endsection

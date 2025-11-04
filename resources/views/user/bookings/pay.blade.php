@extends('user.layout')

@section('title', 'Pay for Booking')

@section('content')
<div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-8 mt-10">
    <h1 class="text-2xl font-bold text-center text-green-800 mb-6">Pay for Booking</h1>
    <div class="mb-4 flex justify-between">
        <span class="text-gray-600">Reference #:</span>
        <span class="font-mono font-bold">BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="mb-2 flex justify-between">
        <span class="text-gray-600">Service:</span>
            <span class="font-semibold">{{ $booking->service->service_name ?? 'N/A' }}</span>
    </div>
    <div class="mb-2 flex justify-between">
        <span class="text-gray-600">Total Price:</span>
        <span class="font-bold text-green-700">₱{{ number_format($booking->total_price, 2) }}</span>
    </div>
    <div class="mb-2 flex justify-between">
        <span class="text-gray-600">Payment Method:</span>
        <span>{{ ucfirst($booking->payment_method) }}</span>
    </div>
    <form action="#" method="POST" enctype="multipart/form-data" class="mt-6">
        @csrf
        <div class="mb-4">
            <label for="gcash_payment" class="block text-gray-700 font-semibold mb-2">Upload GCash Payment Screenshot</label>
            <input type="file" name="gcash_payment" id="gcash_payment" accept="image/*" required class="border rounded px-3 py-2 w-full">
        </div>
        <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-2 rounded shadow">Submit Payment</button>
    </form>
    <div class="mt-6 text-center">
        <a href="{{ route('user.bookings.index') }}" class="text-blue-600 hover:underline">Back to My Bookings</a>
    </div>
</div>
@endsection

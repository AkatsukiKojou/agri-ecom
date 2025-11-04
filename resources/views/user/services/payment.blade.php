@extends('user.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg mt-8">

    <!-- Header -->
    <h2 class="text-3xl font-bold text-green-700 mb-6">Booking Review</h2>

    <!-- Image -->
    @if($service->images)
        <div class="mb-6">
          <img src="{{ asset('storage/' . $service->images) }}"
              alt="{{ $service->service_name }}"
                 class="w-full h-64 object-cover rounded-lg shadow-md">
        </div>
    @endif

    <!-- Service Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $service->service_name }}</h3>
        </div>
        <div>
            <p class="text-lg text-gray-600"><strong>Price/Day:</strong> ₱{{ number_format($service->price, 2) }}</p>
        </div>
    </div>

    <!-- Booking Form -->
    <form action="{{ route('user.services.finalizeBooking', $service->id) }}" method="POST" class="space-y-6 mt-4">
        @csrf

        <!-- Booking Dates -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="booking_start" class="block font-medium text-gray-700">Start Date</label>
                <input type="date" name="booking_start" id="booking_start"
                       class="mt-1 p-2 border border-gray-300 rounded-md w-full"
                       value="{{ old('booking_start', $booking_start) }}"
                       min="{{ now()->toDateString() }}" required>
            </div>

            <div>
                <label for="booking_end" class="block font-medium text-gray-700">End Date</label>
                <input type="date" name="booking_end" id="booking_end"
                       class="mt-1 p-2 border border-gray-300 rounded-md w-full"
                       value="{{ old('booking_end', $booking_end) }}"
                       min="{{ now()->toDateString() }}" required>
            </div>
        </div>

        <!-- Total Calculation -->
        <div class="mt-4">
            <p class="text-gray-700 mb-1"><strong>Total Days:</strong> <span id="totalDays">1</span> day(s)</p>
            <p class="text-xl font-semibold text-green-800">
                Total Price: ₱<span id="totalPrice">{{ number_format($service->price, 2) }}</span>
            </p>
            <input type="hidden" name="total_price" id="total_price_hidden" value="{{ $service->price }}">
        </div>

        <!-- Payment Method -->
        <div>
            <label for="payment_method" class="block font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" id="payment_method" required
                    class="mt-2 p-3 border border-gray-300 rounded-md w-full focus:ring-green-500 focus:border-green-500">
                <option value="" disabled selected>Choose a method</option>
                <option value="onsite">Cash On Site</option>
                <option value="gcash">GCash</option>
            </select>
        </div>
<!-- Total Calculation -->
<div class="mt-4">
    <p class="text-gray-700 mb-1"><strong>Total Days:</strong> <span id="totalDays">1</span> day(s)</p>
    <p class="text-xl font-semibold text-green-800">
        Total Price: ₱<span id="totalPrice">{{ number_format($service->price, 2) }}</span>
    </p>
    <p class="text-lg font-medium text-green-700">
        Downpayment (20%): ₱<span id="downpayment">0.00</span>
    </p>
    <input type="hidden" name="total_price" id="total_price_hidden" value="{{ $service->price }}">
    <input type="hidden" name="downpayment" id="downpayment_hidden" value="0">
</div>

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-green-600 text-white py-3 px-6 rounded-md text-lg font-semibold hover:bg-green-700 transition">
            Confirm & Pay
        </button>
    </form>

    <!-- Back Link -->
    <div class="mt-6">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">
            ← Back to Service
        </a>
    </div>
</div>

<!-- Live Price Calculation Script -->
<script>
    const bookingStart = document.getElementById('booking_start');
    const bookingEnd = document.getElementById('booking_end');
    const totalDaysElem = document.getElementById('totalDays');
    const totalPriceElem = document.getElementById('totalPrice');
    const totalPriceHidden = document.getElementById('total_price_hidden');

    const pricePerDay = {{ $service->price }};

    function updateTotal() {
        const start = new Date(bookingStart.value);
        const end = new Date(bookingEnd.value);
        if (start && end && end >= start) {
            const diffTime = Math.abs(end - start);
            const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const total = days * pricePerDay;
            totalDaysElem.textContent = days;
            totalPriceElem.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2 });
            totalPriceHidden.value = total;
        } else {
            totalDaysElem.textContent = "0";
            totalPriceElem.textContent = "0.00";
            totalPriceHidden.value = 0;
        }
    }

    bookingStart.addEventListener('change', updateTotal);
    bookingEnd.addEventListener('change', updateTotal);

    // Trigger initial calculation on page load
    document.addEventListener('DOMContentLoaded', updateTotal);
</script>
@endsection

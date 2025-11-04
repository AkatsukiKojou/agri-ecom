{{-- filepath: resources/views/user/bookings/receipt.blade.php --}}
@extends('user.layout')

@section('title', 'Booking Receipt')

@section('content')
<br>
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-900 text-center shadow">
    <span class="block text-lg font-bold mb-1">Important Reminder</span>
    <span class="block text-sm">
        Please keep this receipt for your records. You may take a screenshot or download it as a PDF for your convenience.<br>
        Present this receipt as proof of your booking when you arrive at the training service location.
    </span>
</div><div class="max-w-lg mx-auto bg-white shadow-2xl rounded-xl p-8 mt-10 print:p-0 print:shadow-none print:bg-white border-2 border-green-700">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="text-center flex-1">
            <h1 class="text-3xl font-extrabold text-green-800 tracking-wide mb-1">Agri-Ecom</h1>
        </div>
        <div class="ml-4">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-16 h-16">
        </div>
    </div>

    <div class="w-full flex justify-center mb-6">
        <span class="text-xl font-bold text-green-900 text-center">Booking Receipt</span>
    </div>
    <hr class="mb-6 border-green-700">

    <!-- Booking Details -->
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Reference #</span>
            <span class="font-mono font-bold text-green-700 text-lg">
                BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Booked By</span>
            <span class="font-semibold">{{ $booking->full_name ?? 'N/A' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Training Service Name:</span>
            <span class="font-semibold">{{ $booking->service->service_name ?? 'N/A' }}</span>
        </div>
        
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Training Service Location:</span>
            <span>
                @if($booking->service->admin && $booking->service->admin->profile)
                    {{ $booking->service->admin->profile->region ?? '' }}
                    @if($booking->service->admin->profile->province)
                        , {{ $booking->service->admin->profile->province }}<br>
                    @endif
                    @if($booking->service->admin->profile->city || $booking->service->admin->profile->barangay)
                        {{ $booking->service->admin->profile->city ?? '' }}
                        @if($booking->service->admin->profile->city && $booking->service->admin->profile->barangay)
                            ,
                        @endif
                        {{ $booking->service->admin->profile->barangay ?? '' }}
                    @endif
                @else
                    -
                @endif
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Duration</span>
            <span>
                {{ $booking->service->duration ?? '-' }}
                {{ isset($booking->service->duration) && $booking->service->duration > 1 ? '' : '' }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Start Date</span>
            <span>{{ \Carbon\Carbon::parse($booking->booking_start)->format('M d, Y') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">End Date</span>
            <span>
                @php
                    $start = $booking->booking_start ? \Carbon\Carbon::parse($booking->booking_start) : null;
                    $durationStr = $booking->service->duration ?? '';
                    $duration = 0;
                    if (preg_match('/(\d+)\s*(day|week|month|days|weeks|months)/i', $durationStr, $matches)) {
                        $value = (int)$matches[1];
                        $unit = strtolower($matches[2]);
                        if (str_starts_with($unit, 'week')) {
                            $duration = $value * 7;
                        } elseif (str_starts_with($unit, 'month')) {
                            $duration = $value * 30;
                        } else {
                            $duration = $value;
                        }
                    }
                    $end = ($start && $duration > 0) ? $start->copy()->addDays($duration) : null;
                @endphp
                {{ $end ? $end->format('M d, Y') : '-' }}
            </span>
        </div>
        
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Attendees</span>
            <span>{{ $booking->attendees }}</span>
        </div>

 

    </div>

    <!-- Payment Summary -->
    <div class="mt-6 p-4 rounded-lg bg-gray-50 border border-green-200">
        <div class="text-lg font-bold text-green-700 mb-3">Payment Summary</div>
        <div class="flex justify-between mb-1">
            <span class="text-gray-700">Payment Method</span>
            <span class="font-bold text-gray-700">{{ ucfirst($booking->payment_method) }}</span>
        </div>
        <div class="flex justify-between mb-1">
            <span class="text-gray-700">Total Price</span>
            <span class="font-bold text-green-700">₱{{ number_format($booking->total_price, 2) }}</span>
        </div>
        
        @if($booking->payment_method === 'gcash')
        <div class="flex justify-between mb-1">
            <span class="text-gray-700">Amount Paid</span>
            <span class="font-bold text-purple-700">₱{{ number_format($booking->downpayment ?? 0, 2) }}</span>
        </div>
        @endif
        <div class="flex justify-between mb-1">
            <span class="text-gray-700">Balance</span>
            <span class="font-bold text-blue-700">
                @php
                    $paid = $booking->amount_paid ?? 0;
                    $balance = ($booking->total_price ?? 0) - $paid;
                    $downpayment = $booking->downpayment ?? 0;
                    $isGcash = $booking->payment_method === 'gcash';
                    if ($isGcash) {
                        $balance = ($booking->total_price ?? 0) - $downpayment;
                    }
                @endphp
                ₱{{ number_format($balance, 2) }}
            </span>
        </div>
        
        @if($booking->payment_method === 'gcash' && !empty($booking->gcash_reference))
        <div class="flex justify-between mb-1">
            <span class="text-gray-700">GCash Reference #</span>
            <span class="font-mono">{{ $booking->gcash_reference }}</span>
        </div>
        @endif
    </div>


    <!-- Status -->
    <div class="mt-6 flex justify-between items-center">
        <span class="text-gray-700 font-semibold">Status</span>
        @php
            $statusColors = [
                'approved' => 'bg-green-100 text-green-800',
                'pending' => 'bg-yellow-100 text-yellow-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'completed' => 'bg-blue-100 text-blue-800',
            ];
            $badgeClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
        @endphp
        <span class="px-3 py-1 rounded-full font-semibold {{ $badgeClass }}">
            {{ ucfirst($booking->status) }}
        </span>
    </div>

    <!-- Contact -->
    <div class="mt-6 space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Contact Person</span>
            <span>{{ $booking->service->admin && $booking->service->admin->profile ? ($booking->service->admin->profile->farm_owner ?? '-') : '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700 font-semibold">Contact Info</span>
            <span>{{ $booking->service->admin && $booking->service->admin->profile ? ($booking->service->admin->profile->phone_number ?? '-') : '-' }}</span>
        </div>
    </div>

    <hr class="my-6 border-green-700">

    <!-- Footer -->
    <div class="mt-4 text-center text-gray-600 text-sm print:text-xs">
        <p class="mb-2">Thank you for your booking! Please keep this receipt for your records.</p>
        <p>
            If you have any questions, contact us at 
<span class="font-semibold text-green-700">
    {{ $booking->service->admin && $booking->service->admin->profile ? $booking->service->admin->profile->email : 'support@example.com' }}
</span>        </p>
    </div>

    <!-- Print and Download Buttons -->
    <div class="mt-8 flex justify-center print:hidden gap-4">
        
        <a href="{{ route('user.bookings.downloadReceipt', $booking->id) }}" 
            class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-full font-semibold shadow">
            Download PDF
        </a>
    </div>
</div>
@endsection

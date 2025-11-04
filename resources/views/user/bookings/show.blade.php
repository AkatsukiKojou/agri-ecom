@extends('user.layout')

@section('title', 'Booking Details')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 bg-gray-50 p-4 flex items-center justify-center">
            @php
                $service = $booking->service ?? null;
                $imgUrl = null;
                if($service) {
                    if(!empty($service->image_upload)) {
                        $imgUrl = asset('storage/' . $service->image_upload);
                    } elseif(!empty($service->images)) {
                        // images may be json or string
                        if(is_array($service->images)) {
                            $imgUrl = asset('storage/' . ($service->images[0] ?? ''));
                        } else {
                            $decoded = json_decode($service->images, true);
                            if(is_array($decoded) && count($decoded) > 0) {
                                $imgUrl = asset('storage/' . $decoded[0]);
                            } else {
                                $imgUrl = asset('storage/' . $service->images);
                            }
                        }
                    }
                }
            @endphp

            @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="Service Image" class="w-full h-56 object-cover rounded">
            @else
                <div class="w-full h-56 bg-gray-200 flex items-center justify-center rounded">
                    <span class="text-gray-500">No Image Available</span>
                </div>
            @endif
        </div>

        <div class="md:col-span-2 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-green-800">{{ $service->service_name ?? 'Service' }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Booking ID: <span class="font-medium text-gray-700">#{{ $booking->id }}</span></p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold shadow-sm
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            @php
                // Compute trainer (farm owner) safely
                $trainerName = 'N/A';
                if(isset($booking->service) && isset($booking->service->admin) && isset($booking->service->admin->profile) && !empty($booking->service->admin->profile->farm_owner)) {
                    $trainerName = $booking->service->admin->profile->farm_owner;
                } elseif(isset($booking->profile) && !empty($booking->profile->farm_owner)) {
                    $trainerName = $booking->profile->farm_owner;
                }

                // Build readable location. Prefer service admin's profile address, fall back to booking snapshot.
                $adminLocationParts = [];
                if (isset($booking->service) && isset($booking->service->admin) && isset($booking->service->admin->profile)) {
                    $ap = $booking->service->admin->profile;
                    $adminLocationParts = array_filter([
                        $ap->address ?? null,
                        $ap->barangay ?? null,
                        $ap->city ?? null,
                        $ap->province ?? null,
                        $ap->region ?? null,
                    ]);
                }
                $bookingLocationParts = array_filter([
                    $booking->address ?? null,
                    $booking->barangay ?? null,
                    $booking->city ?? null,
                    $booking->province ?? null,
                    $booking->region ?? null,
                ]);
                if (!empty($adminLocationParts)) {
                    $locationString = implode(', ', $adminLocationParts);
                } elseif (!empty($bookingLocationParts)) {
                    $locationString = implode(', ', $bookingLocationParts);
                } else {
                    $locationString = 'N/A';
                }

                // Attendees may be integer or text
                $attendeesDisplay = 'N/A';
                if(isset($booking->attendees)) {
                    if(is_numeric($booking->attendees)) {
                        $attendeesDisplay = (int)$booking->attendees . ' attendee' . ((int)$booking->attendees > 1 ? 's' : '');
                    } else {
                        $attendeesDisplay = $booking->attendees;
                    }
                }
            @endphp

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    @php
                        $startTimeDisplay = null;
                        // Prefer an explicit booking start_time field
                        if (!empty($booking->start_time)) {
                            try {
                                $startTimeDisplay = \Carbon\Carbon::parse($booking->start_time)->format('h:i A');
                            } catch (Exception $e) {
                                $startTimeDisplay = $booking->start_time;
                            }
                        }
                        // Fallback: if booking_start has a time component
                        if (empty($startTimeDisplay) && !empty($booking->booking_start) && method_exists($booking->booking_start, 'format')) {
                            try {
                                $timePart = $booking->booking_start->format('H:i:s');
                                if ($timePart && $timePart !== '00:00:00') {
                                    $startTimeDisplay = $booking->booking_start->format('h:i A');
                                }
                            } catch (Exception $e) {
                                // ignore
                            }
                        }
                        // Final fallback: service start_time
                        if (empty($startTimeDisplay) && isset($service) && !empty($service->start_time)) {
                            try {
                                $startTimeDisplay = \Carbon\Carbon::parse($service->start_time)->format('h:i A');
                            } catch (Exception $e) {
                                $startTimeDisplay = $service->start_time;
                            }
                        }
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700">Start Date</h3>
                            <p class="text-gray-800">{{ $booking->booking_start ? $booking->booking_start->format('F d, Y') : ($booking->created_at ? $booking->created_at->format('F d, Y h:i A') : 'N/A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700">Start Time</h3>
                            <p class="text-gray-800">{{ $startTimeDisplay ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @php
                        // Determine the service/unit to decide whether to show end date
                        $unitValue = null;
                        if (isset($service) && !empty($service->unit)) {
                            $unitValue = strtolower(trim($service->unit));
                        } elseif (!empty($booking->unit)) {
                            $unitValue = strtolower(trim($booking->unit));
                        }
                        // Accept singular or plural forms
                        $allowedUnits = ['session','sessions','day','days','seminar','seminars','training','trainings','program','programs'];
                        $showEndDateByUnit = $unitValue ? in_array($unitValue, $allowedUnits) : true; // default true if unknown
                    @endphp

                    @if($showEndDateByUnit)
                        <div class="mt-4">
                            <h3 class="text-sm font-semibold text-gray-700">End Date</h3>
                            <p class="text-gray-800">{{ $booking->booking_end ? $booking->booking_end->format('F d, Y') : 'N/A' }}</p>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Location:</h3>
                    <p class="text-gray-800">{{ $locationString }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Price:</h3>
                    <p class="text-gray-800">{{ $booking->total_price ? '₱' . number_format($booking->total_price, 2) : 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Duration</h3>
                    <p class="text-gray-800">{{ $service && !empty($service->duration) ? $service->duration : 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Trainer:</h3>
                    <p class="text-gray-800">{{ $trainerName }}</p>
                </div>
                 <div>
                    <h3 class="text-sm font-semibold text-gray-700">Attendees</h3>
                    <p class="text-gray-800">{{ $attendeesDisplay }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Payment Method</h3>
                    <p class="text-gray-800">{{ $booking->payment_method ? ucfirst($booking->payment_method) : 'N/A' }}</p>
                </div>
                @php
                    $statusLower = strtolower($booking->status ?? '');
                    $totalPrice = isset($booking->total_price) ? floatval($booking->total_price) : null;
                    $downpayment = isset($booking->downpayment) ? floatval($booking->downpayment) : 0.0;
                    // Rules:
                    // - If status is approved, cancelled, rejected, or no show: downpayment has not been deducted yet (balance = totalPrice)
                    // - If status is ongoing: hide downpayment display; balance = totalPrice - downpayment
                    // - If status is completed: balance = 0
                    $showDownpayment = true;
                    $balance = null;
                    if ($statusLower === 'completed') {
                        // For completed bookings, remove downpayment row (treated like applied) and balance is 0
                        $showDownpayment = false;
                        $balance = $totalPrice !== null ? 0.0 : null;
                    } elseif ($statusLower === 'pending') {
                        // Pending: downpayment not yet deducted
                        $showDownpayment = true;
                        $balance = $totalPrice !== null ? floatval($totalPrice) : null;
                    } elseif (in_array($statusLower, ['approved','cancelled','rejected','no show'])) {
                        $showDownpayment = true; // still show the downpayment amount, but it's not yet deducted
                        $balance = $totalPrice !== null ? floatval($totalPrice) : null;
                    } elseif ($statusLower === 'ongoing') {
                        $showDownpayment = false; // hide downpayment display in UI
                        $balance = $totalPrice !== null ? max(0.0, floatval($totalPrice) - $downpayment) : null;
                    } else {
                        // default behavior: subtract downpayment from total
                        $showDownpayment = true;
                        $balance = $totalPrice !== null ? max(0.0, floatval($totalPrice) - $downpayment) : null;
                    }
                @endphp

                @if($showDownpayment)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Downpayment</h3>
                    <p class="text-gray-800">
                        {{ $booking->downpayment ? '₱' . number_format($booking->downpayment, 2) : '₱0.00' }}
                    </p>
                </div>
                @endif
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Balance</h3>
                    <p class="text-gray-800">
                        @if($balance === null)
                            N/A
                        @else
                            {{ '₱' . number_format($balance, 2) }}
                        @endif
                    </p>
                </div>
                @php
                    $pm = strtolower(trim($booking->payment_method ?? ''));
                @endphp
                @if(in_array($pm, ['onsite', 'cash on site', 'cash']))
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Downpayment Visit Date</h3>
                    <p class="text-gray-800">{{ $booking->downpayment_visit_date ? \Carbon\Carbon::parse($booking->downpayment_visit_date)->format('F d, Y') : 'N/A' }}</p>
                </div>
                @elseif($pm === 'gcash')
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">GCash Proof</h3>
                    @if($booking->gcash_payment)
                        <p class="text-gray-800"><a href="{{ asset('storage/' . $booking->gcash_payment) }}" target="_blank" class="text-green-700 underline">View uploaded payment proof</a></p>
                    @else
                        <p class="text-gray-800">N/A</p>
                    @endif
                </div>
                @else
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Downpayment Visit Date</h3>
                    <p class="text-gray-800">N/A</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">GCash Proof</h3>
                    <p class="text-gray-800">N/A</p>
                </div>
                @endif
            </div>


        </div>
    </div>
</div>
@endsection

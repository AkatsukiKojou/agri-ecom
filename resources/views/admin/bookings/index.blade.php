@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-8 text-green-800">Manage Bookings</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtering & Search Form -->
    <form id="filterForm" class="mb-8" method="GET" action="{{ route('admin.bookings.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search bookings..." class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-green-400 focus:border-green-400" />
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-green-300 rounded-lg shadow-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="no show" {{ request('status') == 'no show' ? 'selected' : '' }}>No Show</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" id="payment_method" class="mt-1 block w-full border-green-300 rounded-lg shadow-sm">
                    <option value="">All Methods</option>
                    <option value="cash on site" {{ request('payment_method') == 'cash on site' ? 'selected' : '' }}>Cash On Site</option>
                    <option value="gcash" {{ request('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                </select>
            </div>
        </div>
    </form>

    @if($bookings->isEmpty())
        <p class="text-center text-gray-500">No bookings available.</p>
    @else
        <div class="space-y-8" id="bookingsList">
            @foreach($bookings as $booking)
                <div class="booking-card p-6 bg-white shadow-xl rounded-2xl flex flex-col md:flex-row gap-6 items-center border border-green-100 hover:shadow-2xl transition relative"
                    data-status="{{ $booking->status }}"
                    data-start="{{ $booking->booking_start }}"
                    data-end="{{ $booking->booking_end }}">
                    {{-- Service Image with Service Name on Top --}}
                    <div class="flex-shrink-0 w-full md:w-48">
                        @php
                            $images = is_array($booking->service->images)
                                ? $booking->service->images
                                : (json_decode($booking->service->images, true) ?? []);
                            $firstImage = $images[0] ?? $booking->service->images;
                        @endphp
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage) }}"
                                 alt="Service Image"
                                 class="w-full h-40 object-cover rounded-xl border border-green-200 shadow-sm bg-gray-50" />
                        @else
                            <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-xl text-gray-400">
                                No Image
                            </div>
                        @endif
                    </div>

                    {{-- Booking Info --}}
                    <div class="flex-1 flex flex-col gap-2">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">Ref#: BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <h2 class="text-2xl font-bold text-green-700 mb-0">{{ $booking->service->service_name }}</h2>

                        </div>

                        @php
                            $desc = $booking->service->description ?? '';
                            $descLimit = 100;
                            $isDescLong = strlen($desc) > $descLimit;
                            $descModalId = 'descModal_' . $booking->id;
                        @endphp
                        <p class="text-gray-700 flex items-center">
                            <span>
                                {{ $isDescLong ? \Illuminate\Support\Str::limit($desc, $descLimit) : $desc }}
                            </span>
                            @if($isDescLong)
                                <button type="button" class="ml-2 text-blue-600 underline hover:text-blue-800" style="background:none;border:none;cursor:pointer;padding:0;" onclick="document.getElementById('{{ $descModalId }}').style.display='flex'">See more</button>
                            @endif
                        </p>
                        @if($isDescLong)
                        <!-- Description Modal -->
                        <div id="{{ $descModalId }}" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40" style="display:none;">
                            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full relative mx-auto flex flex-col">
                                <button type="button" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold" onclick="document.getElementById('{{ $descModalId }}').style.display='none'">&times;</button>
                                <h3 class="text-lg font-bold mb-4 text-green-700 text-center">Full Description</h3>
                                <div class="text-gray-800 whitespace-pre-line text-sm">{{ $desc }}</div>
                                <button type="button" class="mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mx-auto" onclick="document.getElementById('{{ $descModalId }}').style.display='none'">Close</button>
                            </div>
                        </div>
                        @endif

                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="font-semibold">Booked by:</span> {{ $booking->full_name }}<br>
                                <span class="font-semibold">Booked At:</span> {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y h:i A') }}<br>
                                <span class="font-semibold">Email:</span> {{ $booking->email }}<br>
                                <span class="font-semibold">Phone:</span> {{ $booking->phone ?? 'N/A' }}<br>
                                <span class="font-semibold">Address:</span> {{ $booking->barangay }} {{ $booking->city }} {{ $booking->province }}, {{ $booking->region }}<br>

                            </div>
                            <div>
                                @php
                                    // Determine unit and whether we should show booking end
                                    $unitValue = null;
                                    if (isset($booking->service) && !empty($booking->service->unit)) {
                                        $unitValue = strtolower(trim($booking->service->unit));
                                    } elseif (!empty($booking->unit)) {
                                        $unitValue = strtolower(trim($booking->unit));
                                    }
                                    $allowedUnits = ['session','sessions','day','days','seminar','seminars','training','trainings','program','programs'];
                                    $showEndDateByUnit = $unitValue ? in_array($unitValue, $allowedUnits) : true; // default true if unknown

                                    // Duration and booking end formatting
                                    $durationDisplay = $booking->service->duration ?? ($booking->duration ?? null);
                                    $bookingEndDisplay = null;
                                    if (!empty($booking->booking_end)) {
                                        try {
                                            $bookingEndDisplay = \Carbon\Carbon::parse($booking->booking_end)->format('M d, Y');
                                        } catch (Exception $e) {
                                            $bookingEndDisplay = $booking->booking_end;
                                        }
                                    }
                                @endphp

                                @php
                                    $bookingStartDisplay = null;
                                    if (!empty($booking->booking_start)) {
                                        try {
                                            $bookingStartDisplay     = \Carbon\Carbon::parse($booking->booking_start)->format('M d, Y');
                                        } catch (Exception $e) {
                                            $bookingStartDisplay = $booking->booking_start;
                                        }
                                    }
                                @endphp
                                @php $statusLowerCard = strtolower($booking->status ?? ''); @endphp
                                <span class="font-semibold">Schedule Start:</span> {{ $bookingStartDisplay ?? 'N/A' }}<br>
                                @if($showEndDateByUnit || $statusLowerCard === 'completed')
                                    <span class="font-semibold">Schedule End:</span> {{ $bookingEndDisplay ?? 'N/A' }}<br>
                                @endif
                                <span class="font-semibold">Attendees:</span> {{ $booking->attendees ?? '-' }} <br>

                                <span class="font-semibold">Duration:</span> {{ $durationDisplay ?? 'N/A' }}<br>

                                <span class="font-semibold">Payment:</span> {{ ucfirst($booking->payment_method) }} <br>
                            @php
                                $note = $booking->customer_note ?? '-';
                                $words = preg_split('/\s+/', $note);
                                $firstTen = implode(' ', array_slice($words, 0, 15));
                                $isLong = count($words) > 10;
                                $modalId = 'noteModal_' . $booking->id;
                            @endphp
                            <span class="font-semibold">Note:</span> {{ $firstTen }}@if($isLong)...@endif
                            @if($isLong)
                                <button type="button" class="text-blue-600 underline ml-2" onclick="document.getElementById('{{ $modalId }}').style.display='flex'">View Note</button>
                                <!-- Modal -->
                                <div id="{{ $modalId }}" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40" style="display:none;">
                                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full relative mx-auto flex flex-col">
                                        <button type="button" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold" onclick="document.getElementById('{{ $modalId }}').style.display='none'">&times;</button>
                                        <h3 class="text-lg font-bold mb-4 text-green-700 text-center">Full Note</h3>
                                        <div class="text-gray-800 whitespace-pre-line text-sm">
                                            @php
                                                $modalLines = [];
                                                for ($i = 0; $i < count($words); $i += 10) {
                                                    $modalLines[] = implode(' ', array_slice($words, $i, 10));
                                                }
                                            @endphp
                                            @foreach($modalLines as $line)
                                                {{ $line }}<br>
                                            @endforeach
                                        </div>
                                        <button type="button" class="mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mx-auto" onclick="document.getElementById('{{ $modalId }}').style.display='none'">Close</button>
                                    </div>
                                </div>
                            @endif<br>

                            </div>
                        </div>

                        <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-lg font-bold text-green-800">₱{{ number_format($booking->total_price, 2) }}</span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <!-- Downpayment & GCash Proof moved to bottom right -->
                        </div>
                       

                        {{-- Action Buttons: allow status change for all bookings except cancelled --}}
                        @if($booking->status !== 'cancelled')
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex gap-4">
                                    @php
                                        $isPast = \Carbon\Carbon::parse($booking->booking_start)->lt(\Carbon\Carbon::today());
                                    @endphp
                                    @if($booking->status === 'pending')
                                    @endif
                                    <!-- Status dropdown for both pending and approved; hide when already completed -->
                                    @if(strtolower($booking->status ?? '') !== 'completed')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded border border-green-300 focus:ring-green-400 focus:border-green-400">
                                                <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                                                                <option value="ongoing" {{ $booking->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>

                                                <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                                <option value="no show" {{ $booking->status === 'no show' ? 'selected' : '' }}>No Show</option>
                                            </select>
                                        </form>
                                    @endif
                                </div>
                                @php
                                    $totalPrice = isset($booking->total_price) ? floatval($booking->total_price) : 0.0;
                                    $downpaymentVal = isset($booking->downpayment) ? floatval($booking->downpayment) : 0.0;
                                    $statusLowerAdmin = strtolower($booking->status ?? '');
                                    if ($statusLowerAdmin === 'completed') {
                                        $balanceVal = 0.0;
                                    } elseif ($statusLowerAdmin === 'pending') {
                                        // pending: downpayment not yet deducted
                                        $balanceVal = $totalPrice;
                                    } elseif (in_array($statusLowerAdmin, ['approved','cancelled','rejected','no show'])) {
                                        // approved/rejected/cancelled/no show: downpayment not yet deducted
                                        $balanceVal = $totalPrice;
                                    } elseif ($statusLowerAdmin === 'ongoing') {
                                        // ongoing: downpayment already applied
                                        $balanceVal = max(0.0, $totalPrice - $downpaymentVal);
                                    } else {
                                        // default: subtract downpayment
                                        $balanceVal = max(0.0, $totalPrice - $downpaymentVal);
                                    }
                                @endphp

                                <div class="flex flex-col items-end gap-2">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-semibold border border-blue-200">
                                        Downpayment: ₱{{ number_format($downpaymentVal, 2) }}
                                        @if($statusLowerAdmin === 'ongoing')
                                            <small class="ml-1 text-xs text-gray-600">(applied)</small>
                                        @endif
                                    </span>
                                    <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded font-semibold border border-gray-200">Balance: ₱{{ number_format($balanceVal, 2) }}</span>
                                    @if($booking->payment_method === 'gcash' && $booking->gcash_payment)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-600 font-semibold">GCash Proof:</span>
                                            <a href="{{ asset('storage/' . $booking->gcash_payment) }}" target="_blank" class="inline-block hover:scale-105 transition-transform">
                                                <img src="{{ asset('storage/' . $booking->gcash_payment) }}" alt="GCash Payment" class="h-14 w-14 object-contain border-2 border-blue-200 rounded-lg shadow bg-white" title="View GCash Payment Screenshot">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($booking->status === 'cancelled')
                            <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                                <strong>Cancellation Reason:</strong>
                                @if(!empty($booking->cancel_reason))
                                    {{ $booking->cancel_reason }}
                                @else
                                    <span class="italic text-gray-600">No reason provided.</span>
                                @endif
                            </div>
                        @endif

                      
                    </div>
                </div>
            @endforeach
        </div>
         <!-- Pagination -->
        <div class="mt-6">
            {{ $bookings->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('status');
    const paymentFilter = document.getElementById('payment_method');
    const searchInput = document.getElementById('search');
    const cards = document.querySelectorAll('.booking-card');

    function filterBookings() {
        const status = statusFilter.value.toLowerCase();
    const payment = paymentFilter.value.toLowerCase();
        const search = searchInput.value.trim().toLowerCase();

        cards.forEach(card => {
            let show = true;
            // Status filter
            if (status && card.dataset.status.toLowerCase() !== status) show = false;
        // Payment method filter
        if (payment && card.textContent.toLowerCase().indexOf(payment) === -1) show = false;
            // Search filter
            if (search) {
                const text = card.textContent.toLowerCase();
                if (!text.includes(search)) show = false;
            }
            card.style.display = show ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', filterBookings);
    paymentFilter.addEventListener('change', filterBookings);
    searchInput.addEventListener('input', filterBookings);
});
</script>
@endsection
@extends('user.layout')

@section('title', 'My Bookings')

@section('content')
<div class="min-h-screen bg-white py-12 px-2 md:px-8">
    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-extrabold text-center mb-8 text-green-900 tracking-tight drop-shadow-lg">My Bookings</h1>
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if($bookings->count())
        <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <input type="text" id="bookingSearch" placeholder="Search bookings..." class="w-full md:w-1/3 px-4 py-2 border border-green-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400">
            <select id="bookingStatusFilter" class="w-full md:w-1/6 px-4 py-2 border border-green-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="ongoing">Ongoing</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="completed">Completed</option>
                <option value="no show">No Show</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select id="bookingPaymentFilter" class="w-full md:w-1/6 px-4 py-2 border border-green-300 rounded focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">All Payment</option>
                <option value="gcash">GCash</option>
                <option value="cash on site">Cash On Site</option>
            </select>
        </div>
        <div class="overflow-x-auto rounded-xl shadow-lg bg-white">
            <table class="min-w-full divide-y divide-green-100">
                <thead class="bg-green-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-green-800 uppercase">Ref#</th>
                        <th class="px-4 py-3 texts-left text-xs font-bold text-green-800 uppercase">Training Service</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-green-800 uppercase">Start Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-green-800 uppercase">Duration</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-green-800 uppercase">Attendees</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-green-800 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-green-800 uppercase">Total Price</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-green-800 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-green-50">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-green-50 transition" data-status="{{ strtolower($booking->status) }}" data-payment="{{ strtolower($booking->payment_method) }}">
                        <td class="px-4 py-3 text-xs text-gray-500 font-mono">BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            @php
                                $img = null;
                                if ($booking->service && $booking->service->images) {
                                    $imgArr = is_array($booking->service->images) ? $booking->service->images : json_decode($booking->service->images, true);
                                    $img = isset($imgArr[0]) ? $imgArr[0] : $booking->service->images;
                                }
                            @endphp
                            <img src="{{ $img ? asset('storage/' . ltrim($img, '/')) : asset('default.png') }}" class="w-10 h-10 rounded-lg object-cover border border-green-200 shadow" alt="Service Image">
                            <div>
                                    <div class="font-bold text-blue-900">{{ $booking->service->service_name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($booking->booking_start)->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-green-700 font-semibold">{{ $booking->service->duration ?? '-' }} {{ isset($booking->service->duration) && $booking->service->duration > 1 ? '' : '' }}</td>
                        <td class="px-4 py-3 text-center">{{ $booking->attendees }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'ongoing' => 'bg-blue-100 text-blue-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'no show' => 'bg-gray-100 text-gray-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                ];
                                $status = strtolower($booking->status);
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <div class="mt-1 flex flex-col gap-1">
                               
                            </div>
                        </td>
                        <td class="px-4 py-3 font-bold text-green-700">₱{{ number_format($booking->total_price, 2) }}</td>
                        
                        <td class="px-4 py-3 text-center flex flex-col gap-1 items-center justify-center">
                            <a href="{{ route('user.bookings.show', $booking->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition mb-1">View</a>
                            @if($booking->status === 'pending')
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $canCancel = false;
                                        // If booking has a scheduled start date/time, allow cancellation only if it's at least 24 hours away
                                        if (!empty($booking->booking_start)) {
                                            try {
                                                $bookingStart = $booking->booking_start instanceof \Carbon\Carbon ? $booking->booking_start : \Carbon\Carbon::parse($booking->booking_start);
                                                if ($bookingStart->isFuture() && $bookingStart->diffInHours($now) >= 24) {
                                                    $canCancel = true;
                                                }
                                            } catch (Exception $e) {
                                                // parsing failed - fallback to created-at rule below
                                            }
                                        }

                                        // Fallback: if no booking_start or parsing failed, allow cancellation within 24 hours after booking creation
                                        if (!$canCancel) {
                                            try {
                                                $createdAt = $booking->created_at instanceof \Carbon\Carbon ? $booking->created_at : \Carbon\Carbon::parse($booking->created_at);
                                                if ($now->diffInHours($createdAt) <= 24) {
                                                    $canCancel = true;
                                                }
                                            } catch (Exception $e) {
                                                $canCancel = false;
                                            }
                                        }
                                    @endphp
                                    @if($canCancel)
                                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition mb-1" onclick="openCancelModal({{ $booking->id }})">Cancel</button>
                                    @else
                                        <button type="button" class="bg-red-300 text-white px-3 py-1 rounded text-xs font-semibold transition mb-1 cursor-not-allowed" disabled>Cancel</button>
                                        <span class="text-[10px] text-red-600">Cancellation is only allowed up to 24 hours before the scheduled start, or within 24 hours after booking creation.</span>
                                    @endif
                            @endif
                            @if($booking->status === 'pending' && $booking->payment_method === 'gcash' && !$booking->gcash_payment)
                                <a href="{{ route('user.bookings.pay', $booking->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition mb-1">Pay</a>
                            @endif
                            {{-- Print action removed per UI request --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
        @else
        <div class="flex flex-col items-center justify-center mt-16">
            <img src="https://cdn.jsdelivr.net/gh/edent/SuperTinyIcons/images/svg/undraw_empty_xct9.svg" alt="No Bookings" class="w-32 h-32 mb-2 opacity-80">
            <p class="text-gray-500 text-lg">You have no bookings yet.</p>
            <a href="{{ route('user.services.index') }}" class="mt-4 bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-full font-semibold shadow">Book a Service</a>
        </div>
        @endif
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xs">
        <h2 class="text-lg font-bold mb-3 text-red-700">Cancel Booking</h2>
        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            <label for="cancel_reason" class="block text-sm font-semibold mb-2">Select reason:</label>
            <select name="cancel_reason" id="cancel_reason" required class="border-gray-300 rounded px-2 py-1 text-sm w-full mb-4">
                <option value="">Select reason</option>
                <option value="Change of plans">Change of plans</option>
                <option value="Found another service">Found another service</option>
                <option value="Schedule conflict">Schedule conflict</option>
                <option value="Personal reasons">Personal reasons</option>
                <option value="Other">Other</option>
            </select>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeCancelModal()" class="px-3 py-1 rounded bg-gray-300 text-gray-700 font-semibold">Back</button>
                <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white font-semibold">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('bookingSearch');
    const statusFilter = document.getElementById('bookingStatusFilter');
    const paymentFilter = document.getElementById('bookingPaymentFilter');
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');

    function filterRows() {
        const search = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        const payment = paymentFilter.value;
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            let rowStatus = row.getAttribute('data-status');
            let rowPayment = row.getAttribute('data-payment');
            let matchesSearch = text.includes(search);
            let matchesStatus = !status || rowStatus === status;
            // Normalize payment matching: accept several synonyms for onsite/cash on site
            let matchesPayment = false;
            if (!payment) {
                matchesPayment = true;
            } else {
                const p = payment.toLowerCase().trim();
                const rp = (rowPayment || '').toLowerCase().trim();
                if (p === 'cash on site') {
                    // accept common stored variants
                    matchesPayment = ['onsite', 'on site', 'cash on site', 'cash'].includes(rp);
                } else if (p === 'gcash') {
                    matchesPayment = rp.includes('gcash');
                } else {
                    matchesPayment = rp === p;
                }
            }
            if (matchesSearch && matchesStatus && matchesPayment) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    searchInput.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);
    paymentFilter.addEventListener('change', filterRows);
});

function openCancelModal(bookingId) {
    var modal = document.getElementById('cancelModal');
    var form = document.getElementById('cancelForm');
    form.action = '/user/bookings/' + bookingId;
    modal.style.display = 'flex';
}
function closeCancelModal() {
    var modal = document.getElementById('cancelModal');
    modal.style.display = 'none';
    document.getElementById('cancel_reason').value = '';
}
</script>
@endsection

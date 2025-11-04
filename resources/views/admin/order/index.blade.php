@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-4xl font-bold text-center mb-8 text-green-800">Manage Orders</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded shadow">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Form -->
    <form id="orderFilterForm" method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
        <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                placeholder="Search recipient, address, product name/type, or #order id..." 
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400" />
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="ready_to_pick_up" {{ request('status') == 'ready_to_pick_up' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="to_delivery" {{ request('status') == 'to_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Reject</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
            </select>
        </div>
        <div>
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" id="payment_method"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400">
                <option value="">All</option>
                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery </option>
                <option value="cop" {{ request('payment_method') == 'cop' ? 'selected' : '' }}>Cash on Pick up</option>
            </select>
        </div>
       
    </form>

    <div id="ordersList">
        @if($orders->isEmpty())
            <p class="text-center text-gray-500">No orders found.</p>
        @else
            <div class="space-y-10">
                @foreach($orders as $order)
                @php
                    // Precompute reference so client-side filtering can use it safely
                    $ref = $order->reference ?? ('ORD-' . $order->created_at->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT));
                @endphp
                <div class="p-8 bg-white shadow-2xl rounded-2xl border border-green-200 hover:shadow-green-200 transition-all order-card" data-ref="{{ $ref }}">
                    <!-- Order Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 border-b pb-3 border-green-100">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 text-green-700 rounded-full px-4 py-2 text-lg font-bold shadow">#{{ $order->id }}</div>
                            <div>
                                <div class="text-lg font-semibold text-green-800">Order Details</div>
                                <div class="text-xs text-gray-500">Placed: {{ $order->created_at->format('M d, Y h:i A') }}</div>
                                {{-- reference already computed above for client-side filtering --}}
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="font-semibold text-green-900">Reference:</span>
                                    <span class="text-green-700 font-semibold">{{ $ref }}</span>
                                </div>
                            </div>
                        </div>
                       
                    </div>

                    <!-- Shipping Info () -->
                    <div class="mb-6 text-base text-gray-700">
                        <div class="bg-green-20 rounded-xl p-6 border border-green-100 shadow">
                            {{-- Prefer the order's stored/shipped snapshot (name, phone, address, email). Fall back to the shippingAddress relation only if snapshot fields are empty. --}}
                            @php
                                // Helper closures to safely access shippingAddress when it may be null
                                $sa = $order->shippingAddress ?? null;
                                $recipientName = $order->name ?: ($sa->name ?? null);
                                $phone = $order->phone ?: ($sa->phone ?? null);
                                // If the order already stores a full address string, use it directly.
                                if (!empty($order->address)) {
                                    $fullAddress = $order->address;
                                } else {
                                    // Build address from shippingAddress parts when order snapshot is not present
                                    if ($sa) {
                                        $parts = [
                                            $sa->address ?? null,
                                            $sa->barangay ?? null,
                                            $sa->city ?? null,
                                            $sa->province ?? null,
                                            $sa->region ?? null,
                                        ];
                                        $fullAddress = trim(implode(', ', array_filter($parts)));
                                        if ($fullAddress === '') {
                                            $fullAddress = null;
                                        }
                                    } else {
                                        $fullAddress = null;
                                    }
                                }
                                $email = $order->email ?: ($sa->email ?? null);
                            @endphp

                            <span class="font-semibold text-green-900">Recipient Name:</span> {{ $recipientName ?? 'N/A' }}<br>
                            <span class="font-semibold text-green-900">Phone:</span> {{ $phone ?? 'N/A' }}<br>
                            <span class="font-semibold text-green-900">Full Address:</span>
                            @if(!empty($fullAddress))
                                {{ $fullAddress }}
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                            <br>
                            <span class="font-semibold text-green-900">Email:</span> {{ $email ?? 'N/A' }}
                        </div>
                    </div>

                    <!-- Products and Quantities -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base text-gray-800 mt-2">
                        <div>
                            <span class="font-semibold text-green-700 text-lg">Products:</span>
                            <ul class="space-y-4 mt-4">
                                @foreach($order->items as $item)
                                <li class="flex items-center space-x-8 bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="Product Image" class="w-36 h-36 object-cover rounded-lg shadow border-2 border-green-200" />
                                    @else
                                        <div class="w-36 h-36 bg-gray-200 flex items-center justify-center text-gray-500 rounded-lg border-2 border-green-200">N/A</div>
                                    @endif
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-baseline gap-2">
                                            <span class="block text-lg font-bold text-gray-900">{{ $item->product_name }}</span>
                                            @php
                                                $unit = $item->product->unit ?? ($item->unit ?? null);
                                            @endphp
                                            @if($unit)
                                                <span class="text-sm text-gray-500">· {{ $unit }}</span>
                                            @endif
                                        </div>
                                        @php
                                            $productType = $item->product->type ?? ($item->type ?? null);
                                        @endphp
                                        @if($productType)
                                            <span class="block text-xs text-gray-500">{{ $productType }}</span>
                                        @endif
                                        <span class="block text-green-700 font-semibold text-base">₱{{ number_format($item->price, 2) }}</span>
                                        <span class="block text-sm text-gray-500">Qty: {{ $item->quantity }}</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <span class="font-semibold text-green-700 text-lg">Order Summary:</span>
                            <ul class="mt-4 space-y-3">
                                <li><strong>Shipping Fee:</strong> ₱{{ number_format($order->items->sum('shipping_fee'), 2) }}</li>

                                <li><strong>Payment:</strong> <span class="capitalize">{{ $order->payment_method }}</span></li>
                                <li><strong>Grand Total:</strong> <span class="font-bold text-green-700">₱{{ number_format($order->items->sum(function($item) { return ($item->price * $item->quantity) + ($item->shipping_fee ?? 0); }), 2) }}</span></li>
                                <li>
                                    <strong>Status:</strong>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold shadow-sm bg-green-100 text-green-800">
                                            {{ $order->status_label }}
                                        </span>
                                </li>
                
                                @if($order->status === 'canceled' && $order->cancel_reason)
                                    <li>
                                        <strong>Cancel Reason:</strong>
                                        <span class="text-xs text-red-600">{{ $order->cancel_reason }}</span>
                                    </li>
                                @endif
                                @if($order->cancel_reason && $order->status !== 'canceled')
                                    <li>
                                        <strong>Cancel Reason:</strong>
                                        <span class="text-xs text-gray-700">{{ $order->cancel_reason }}</span>
                                    </li>
                                @endif
                                 <li><strong>Note:</strong>
                                    @php
                                        $msg = $order->shipping_message ?? '';
                                        $words = preg_split('/\s+/', trim($msg));
                                        $wordCount = count($words);
                                        $displayMsg = $wordCount > 20 ? implode(' ', array_slice($words, 0, 20)) : $msg;
                                    @endphp
                                    <span id="msg-short-{{ $order->id }}">{{ $displayMsg }}@if($wordCount > 20)... <button type="button" class="text-green-600 underline" onclick="document.getElementById('msg-full-{{ $order->id }}').style.display='inline';document.getElementById('msg-short-{{ $order->id }}').style.display='none';">See more</button>@endif</span>
                                    @if($wordCount > 20)
                                        <span id="msg-full-{{ $order->id }}" style="display:none;">{{ $msg }} <button type="button" class="text-green-600 underline" onclick="document.getElementById('msg-full-{{ $order->id }}').style.display='none';document.getElementById('msg-short-{{ $order->id }}').style.display='inline';">See less</button></span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Status Update Dropdown (Booking Style) -->
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mr-12">
                        @php
                            $currentStatus = strtolower($order->status ?? '');
                            // statuses that should NOT be updatable from admin UI
                            $nonUpdatable = ['canceled', 'cancelled', 'completed', 'reject'];
                        @endphp

                        @if(in_array($currentStatus, $nonUpdatable))
                            <div class="rounded-lg px-6 py-4 w-full md:w-auto border bg-white">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Update Status:</label>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-2 shadow-sm bg-green-100 text-green-800">
                                    {{ $order->status_label }}
                                </span>

                                @if(in_array($currentStatus, ['canceled', 'cancelled']) && $order->cancel_reason)
                                    <p class="text-xs text-red-700"><strong>Cancel Reason:</strong> {{ $order->cancel_reason }}</p>
                                @elseif($order->cancel_reason)
                                    {{-- show reason if present for reject/completed states too (non-intrusive) --}}
                                    <p class="text-xs text-gray-700"><strong>Reason:</strong> {{ $order->cancel_reason }}</p>
                                @endif

                            </div>
                        @else
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <label for="order-status-{{ $order->id }}" class="block text-sm font-medium text-gray-700 mb-1">Update Status:</label>
                                @php
                                    // Payment-method-aware status options
                                    // For Cash on Pick up (cop): pending -> confirmed -> ready_to_pick_up -> completed
                                    // For Cash on Delivery (cod) and others: pending -> confirmed -> to_delivery -> completed
                                    if (strtolower($order->payment_method ?? '') === 'cop') {
                                        $statusOptions = [
                                            'pending' => 'Pending',
                                            'confirmed' => 'Confirmed',
                                            'ready_to_pick_up' => 'Ready for Pickup',
                                            'completed' => 'Completed',
                                            'reject' => 'Reject',
                                        ];
                                    } else {
                                        $statusOptions = [
                                            'pending' => 'Pending',
                                            'confirmed' => 'Confirmed',
                                            'to_delivery' => 'Out for Delivery',
                                            'completed' => 'Completed',
                                            'reject' => 'Reject',
                                        ];
                                    }

                                    // If the current order status is not in the canonical list, add it so the select shows it
                                    if (!empty($order->status) && !array_key_exists($order->status, $statusOptions)) {
                                        $statusOptions = array_merge([$order->status => ucfirst(str_replace('_', ' ', $order->status))], $statusOptions);
                                    }
                                @endphp
                                <select name="status" id="order-status-{{ $order->id }}" onchange="this.form.submit()" class="px-3 py-2 rounded border border-green-300 focus:ring-green-400 focus:border-green-400">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Client-side reference search only: if the search string looks like an order reference
// (e.g., "#123", "ORD-20251025-0123", or starts with "ref:"), we filter the
// rendered order cards on the page without calling the controller. For other
// searches we fall back to the existing AJAX server-side query.
$(document).ready(function() {
    function fetchOrders() {
        $.ajax({
            url: "{{ route('admin.orders.index') }}",
            type: 'GET',
            data: $('#orderFilterForm').serialize(),
            success: function(data) {
                // Replace the orders list only
                const newOrders = $(data).find('#ordersList').html();
                $('#ordersList').html(newOrders);
            }
        });
    }

    function normalizeRef(text) {
        return String(text || '').toLowerCase().replace(/[^a-z0-9\-]/g, '');
    }

    function isRefSearch(val) {
        if (!val) return false;
        const v = val.trim();
        if (v.startsWith('#')) return true;
        if (/^ref[:\s]/i.test(v)) return true;
        if (/^ord[-_\d]/i.test(v)) return true; // starts with ORD- or ORD123
        if (v.toLowerCase().includes('ord-')) return true;
        return false;
    }

    function filterByRef(val) {
        const needle = normalizeRef(val.replace(/^#/, '').replace(/^ref[:\s]+/i, ''));
        if (!needle) {
            // show all
            $('.order-card').show();
            return;
        }
        $('.order-card').each(function() {
            const ref = normalizeRef($(this).data('ref'));
            if (ref.indexOf(needle) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $('#search').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        const val = $(this).val();
        // If the input looks like a reference, do client-side filter only
        if (isRefSearch(val)) {
            // small debounce for typing
            window.searchTimeout = setTimeout(function() {
                filterByRef(val);
            }, 150);
            return;
        }

        // otherwise, use server-side search (existing behavior)
        window.searchTimeout = setTimeout(fetchOrders, 300);
    });

    // still trigger server fetch when status/payment changes
    $('#status, #payment_method').on('change', fetchOrders);
});
</script>
<script>
function copyRef(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            // non-blocking feedback
            const originalTitle = document.title;
            document.title = 'Reference copied • ' + text;
            setTimeout(() => { document.title = originalTitle; }, 1200);
        }).catch(function() {
            alert('Could not copy reference to clipboard.');
        });
    } else {
        // Fallback for older browsers
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'absolute';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy');
            const originalTitle = document.title;
            document.title = 'Reference copied • ' + text;
            setTimeout(() => { document.title = originalTitle; }, 1200);
        } catch (e) {
            alert('Could not copy reference to clipboard.');
        }
        document.body.removeChild(ta);
    }
}
</script>
</div>
@endsection
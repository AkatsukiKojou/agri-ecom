@extends('user.layout')

@section('content')
<br>
    <div class="text-center mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold text-green-800">My Purchase</h1>
    </div>
  
    <div class="flex justify-center mb-6">
        <div class="w-full max-w-5xl flex items-center">
            <form method="GET" action="{{ route('user.orders') }}" id="userOrdersFilterForm" class="flex-1 max-w-md mx-auto flex items-center space-x-2">
                <input type="text" id="orderSearch" name="search" value="{{ request('search') }}"
                       placeholder="Search order..." 
                       class="flex-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400 text-sm" autocomplete="off" />
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <span id="orderSearchTrigger" class="text-green-600 font-semibold hover:underline cursor-pointer text-sm">Search</span>
            </form>

            <div class="ml-4 flex-shrink-0">
                <a href="{{ route('user.orders.history') }}" class="flex items-center text-green-700 hover:underline mb-0 inline-block font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 17l4 4 4-4m-4-5v9M20 12a8 8 0 10-16 0 8 8 0 0016 0z" />
                    </svg>
                    Orders History
                </a>
            </div>
        </div>
    </div>
    <div class="max-w-5xl mx-auto mt-10 px-4">

    {{-- Filter Tabs  --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-center mb-8 gap-4">
        <div class="flex gap-4 flex-wrap justify-center" style="max-width:100%;">
            @php
                // Tabs: map user-friendly tab keys -> label. Some tab keys are logical and map to DB status values when building links.
                $statuses = [
                    'all' => 'All',
                    'pending' => 'Pending',
                    'to_ship' => 'To Ship',    // maps to DB 'confirmed'
                    'to_receive' => 'To Receive', // maps to DB 'to_delivery' / 'out_for_delivery'
                    'to_pickup' => 'To Pickup', // maps to DB 'ready_to_pick_up'
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled',
                    'completed' => 'Completed',
                ];

                // Determine which tab should be active based on the incoming request status (which contains DB status values)
                $requestedStatus = request('status');
                if (empty($requestedStatus)) {
                    $current = 'all';
                } elseif (in_array($requestedStatus, ['to_delivery', 'out_for_delivery'])) {
                    $current = 'to_receive';
                } elseif ($requestedStatus === 'ready_to_pick_up') {
                    $current = 'to_pickup';
                } elseif ($requestedStatus === 'confirmed') {
                    $current = 'to_ship';
                } elseif (in_array($requestedStatus, ['canceled', 'cancelled'])) {
                    $current = 'cancelled';
                } elseif (in_array($requestedStatus, ['reject', 'rejected'])) {
                    $current = 'rejected';
                } elseif ($requestedStatus === 'completed') {
                    $current = 'completed';
                } elseif ($requestedStatus === 'pending') {
                    $current = 'pending';
                } else {
                    $current = $requestedStatus;
                }
            @endphp
                @foreach($statuses as $key => $label)
                    @php
                        $params = request()->except(['page']);
                        if ($key === 'all') {
                            unset($params['status']);
                        } else {
                            // Map tab key to actual DB status value(s) used by the controller
                                if ($key === 'to_ship') {
                                    $params['status'] = 'confirmed';
                                } elseif ($key === 'to_receive') {
                                    $params['status'] = 'to_delivery';
                                } elseif ($key === 'to_pickup') {
                                    // The user-facing "To Pickup" tab corresponds to the DB status `ready_to_pick_up`
                                    $params['status'] = 'ready_to_pick_up';
                                } else {
                                    $params['status'] = $key;
                                }
                        }
                    @endphp
                    <a href="{{ route('user.orders', $params) }}"
                       class="px-4 py-2 rounded-full text-sm font-semibold transition
                            {{ ($current === $key || ($current === 'all' && !request()->has('status') && $key === 'all')) ? 'bg-green-600 text-white shadow' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
        </div>
       
    </div>

    <div id="ordersList">
    @forelse ($orders as $order)
        @php
            $totalQuantity = $order->items->sum('quantity');
            $userShipping = $order->user->shippingAddresses->where('is_default', true)->first();
            $adminAddress = null;
            if(count($order->items)) {
                $admin = optional($order->items->first()->product)->admin;
                $adminAddress = $admin ? $admin->address : null;
            }
            $canCancel = $order->created_at->diffInMinutes(now()) <= 60 && $order->status === 'pending';
        @endphp
        <div class="border rounded-2xl p-6 mb-8 shadow-lg bg-gradient-to-br from-green-50 to-white hover:shadow-2xl transition">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded">Order #{{ $order->id }}</span>
                        <span class="ml-2 text-xs text-gray-500">Placed on: {{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @php
                        $status = strtolower($order->status ?? 'pending');
                        $payment = strtolower($order->payment_method ?? '');

                        // Determine the middle step depending on payment method
                        if ($payment === 'cop') {
                            $middleKey = 'ready_to_pick_up';
                            $middleLabel = 'Ready for Pickup';
                        } else {
                            $middleKey = 'to_delivery';
                            $middleLabel = 'Out for Delivery';
                        }

                        // Map status to step index: 1 = Requested, 2 = Approved, 3 = Middle (pickup/delivery), 4 = Completed
                        $stepMap = [
                            'pending' => 1,
                            'requested' => 1,
                            'confirmed' => 2,
                            'approved' => 2,
                            $middleKey => 3,
                            'shipped' => 3,
                            'to_delivery' => 3,
                            'ready_to_pick_up' => 3,
                            'completed' => 4,
                        ];

                        $stepIndex = $stepMap[$status] ?? 1;
                    @endphp

                    <span class="font-semibold">Status:</span>
                    @if(in_array($status, ['cancelled', 'rejected']))
                        <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block" aria-hidden="true"></span>
                            <span class="capitalize">{{ $status === 'rejected' ? 'Rejected' : 'Cancelled' }}</span>
                        </span>
                    @else
                        <div class="max-w-full">
                            <div class="flex items-center justify-between">
                                @php
                                    $steps = ['Requested', 'Approved', $middleLabel, 'Completed'];
                                @endphp
                                @foreach($steps as $i => $label)
                                    @php $idx = $i + 1; @endphp
                                    <div class="flex-1 flex items-center">
                                        <div class="flex items-center w-full">
                                            {{-- Dot --}}
                                            @php
                                                $state = $idx < $stepIndex ? 'done' : ($idx == $stepIndex ? 'current' : 'pending');
                                                // If entire order is completed or approved/confirmed, make current/done green
                                                if (in_array($status, ['completed', 'approved', 'confirmed', 'shipped'])) {
                                                    $dotClass = $state === 'done' || $state === 'current' ? 'bg-green-500' : 'bg-gray-300';
                                                    $lineClass = 'bg-green-500';
                                                } else {
                                                    $dotClass = $state === 'done' ? 'bg-green-500' : ($state === 'current' ? 'bg-yellow-400' : 'bg-gray-300');
                                                    $lineClass = $state === 'done' ? 'bg-green-500' : 'bg-gray-200';
                                                }
                                            @endphp
                                            <div class="flex items-center w-full">
                                                <div class="flex items-center">
                                                    <span class="w-3 h-3 rounded-full {{ $dotClass }} inline-block mr-2" aria-hidden="true"></span>
                                                    <span class="text-xs text-gray-600 whitespace-nowrap">{{ $label }}</span>
                                                </div>
                                                @if($idx < count($steps))
                                                    <div class="flex-1 h-px mx-3 {{ $lineClass }}" aria-hidden="true"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-4 mt-2 md:mt-0">
                    <span class="text-sm text-gray-600">
                        <svg class="inline w-5 h-5 text-green-500 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"></path><circle cx="7" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
                        {{ count($order->items) }} item{{ count($order->items) > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            {{-- Products List: image + details (responsive grid: 1/2/3 columns) --}}
            <div class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                    @foreach ($order->items as $item)
                        @php
                            $product = $item->product ?? null;
                            $productName = $item->product_name ?? ($product->name ?? 'N/A');
                            $unit = $product->unit ?? ($item->unit ?? null);
                            $productType = $product->type ?? ($item->type ?? null);
                        @endphp
                        <div class="flex items-start space-x-4 bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center border-2 border-green-100 flex-shrink-0">
                                @if($product && !empty($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $productName }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-400 text-xs">No Image</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-baseline gap-2">
                                            <span class="block text-md font-bold text-gray-900">{{ $productName }}</span>
                                            @if($unit)
                                                <span class="text-sm text-gray-500">· {{ $unit }}</span>
                                            @endif
                                        </div>
                                        @if($productType)
                                            <div class="text-xs text-gray-500">{{ $productType }}</div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-700 font-semibold">₱{{ number_format($item->price, 2) }}</div>
                                        <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700 mb-4">
                <div>
                    @if(strtolower($order->payment_method) === 'cop')
                        @php
                            $adminProfile = null;
                            if(isset($admin) && $admin && $admin->profile) {
                                $adminProfile = $admin->profile;
                            }
                        @endphp
                        <p><span class="font-semibold">Pickup Address:</span>
                            @if($adminProfile)
                                {{ $adminProfile->address ?? '' }}
                                {{ $adminProfile->barangay ?? '' }}
                                {{ $adminProfile->city ?? '' }},
                                {{ $adminProfile->province ?? '' }},
                                {{ $adminProfile->region ?? '' }}
                                <br>
                                <span class="font-semibold">Contact Number:</span> {{ $adminProfile->phone_number ?? 'N/A' }}
                                <br>
                                <span class="font-semibold">Email:</span>  {{ $adminProfile->email ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </p>
                    @else
                        <p>
                            <span class="font-semibold">Receiver:</span>
                            {{ $order->name ?? ($userShipping->name ?? 'N/A') }}
                        </p>
                        <p>
                            <span class="font-semibold">Shipping Address:</span>
                            @if(!empty($order->address))
                                {{ $order->address }}
                            @else
                                {{ $userShipping->address ?? '' }}
                                {{ $userShipping->barangay ?? '' }}
                                {{ $userShipping->city ?? '' }},
                                {{ $userShipping->province ?? '' }},
                                {{ $userShipping->region ?? '' }}
                            @endif
                        </p>
                        <p>
                            <span class="font-semibold">Email:</span>
                            {{ $order->email ?? ($userShipping->email ?? 'N/A') }}
                        </p>
                        <p>
                            <span class="font-semibold">Quantity:</span>
                            {{ $totalQuantity }}
                        </p>
                            
                            
                    @endif
                    <p><span class="font-semibold">Payment:</span>
                        @if(strtolower($order->payment_method) === 'cop')
                            Cash on Pickup
                        @elseif(strtolower($order->payment_method) === 'cod')
                            Cash on Delivery
                        @else
                            {{ strtoupper($order->payment_method ?? 'N/A') }}
                        @endif
                    </p>
                </div>
                <div class="md:text-right mt-2 md:mt-0">
                    @php
                        $orderTotal = $order->items->sum(function($item) {
                            return ($item->price * $item->quantity) + ($item->shipping_fee ?? 0);
                        });
                    @endphp
                    <p class="font-semibold text-green-700 text-lg">
                        Total: ₱{{ number_format($orderTotal, 2) }}
                    </p>
                </div>
            </div>
            @if($canCancel)
                <div class="flex justify-end mt-4">
                    <button type="button"
                        class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold shadow transition cancel-btn"
                        data-order-id="{{ $order->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Order
                    </button>
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-600 text-center mt-10">You have no orders yet.</p>
        <div class="flex justify-center mt-4">
            <a href="{{ route('user.products.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-2 py-2 rounded-lg shadow text-lg transition">
                Buy Products
            </a>
        </div>
    @endforelse
    </div>
    <div class="mt-6 mb-8 text-sm text-gray-600">Showing orders from the last 30 days. Older orders are available in your Order History.</div>
</div>

<!-- Cancel Modal -->
<div id="cancel-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <h2 class="text-lg font-bold text-red-600 mb-4">Cancel Order</h2>
        <form id="cancel-form" method="POST" action="">
            @csrf
            <input type="hidden" name="order_id" id="cancel-order-id">
            <label for="cancel-reason" class="block mb-2 text-sm font-semibold">Select Reason:</label>
            <select name="reason" id="cancel-reason" required class="w-full border border-gray-300 rounded px-3 py-2 mb-4">
                <option value="">-- Choose reason --</option>
                <option value="Changed mind">Changed mind</option>
                <option value="Found cheaper elsewhere">Found cheaper elsewhere</option>
                <option value="Ordered by mistake">Ordered by mistake</option>
                <option value="Other">Other</option>
            </select>
            <div class="flex justify-end gap-2">
                <button type="button" id="cancel-close" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold">Close</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white font-semibold">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show cancel modal
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('cancel-modal').style.display = 'flex';
            document.getElementById('cancel-order-id').value = this.dataset.orderId;
            document.getElementById('cancel-form').action = '/orders/cancel/' + this.dataset.orderId;
        });
    });
    // Close modal
    document.getElementById('cancel-close').onclick = function() {
        document.getElementById('cancel-modal').style.display = 'none';
        document.getElementById('cancel-reason').value = '';
    };
    // Optional: Hide modal on outside click
    document.getElementById('cancel-modal').onclick = function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            document.getElementById('cancel-reason').value = '';
        }
    };
</script>
<script>
// Realtime (AJAX) search for user orders
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('userOrdersFilterForm');
    const searchInput = document.getElementById('orderSearch');
    const ordersList = document.getElementById('ordersList');
    if (!form || !searchInput || !ordersList) return;

    const debounce = (fn, ms) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), ms); }; };

    async function fetchOrders(url) {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const text = await res.text();
            // Parse returned HTML and extract #ordersList
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newList = doc.querySelector('#ordersList');
            if (newList) {
                ordersList.innerHTML = newList.innerHTML;
                // Re-hook cancel button listeners inside replaced content
                document.querySelectorAll('.cancel-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('cancel-modal').style.display = 'flex';
                        document.getElementById('cancel-order-id').value = this.dataset.orderId;
                        document.getElementById('cancel-form').action = '/orders/cancel/' + this.dataset.orderId;
                    });
                });
                // Re-bind pagination link clicks to use AJAX
                bindPaginationLinks();
            }
        } catch (e) {
            console.error('Failed to fetch orders:', e);
        }
    }

    function buildUrlWithParams(base, formEl) {
        const params = new URLSearchParams(new FormData(formEl));
        return base + (params.toString() ? ('?' + params.toString()) : '');
    }

    const debouncedFetch = debounce(() => {
        const url = buildUrlWithParams(form.action, form);
        fetchOrders(url);
    }, 300);

    searchInput.addEventListener('input', function (e) {
        debouncedFetch();
    });

    // Click on the text trigger should also perform the search (useful for users who prefer clicking)
    const searchTriggerElem = document.getElementById('orderSearchTrigger');
    if (searchTriggerElem) {
        searchTriggerElem.addEventListener('click', function (e) {
            e.preventDefault();
            debouncedFetch();
        });
    }

    // Intercept pagination links inside ordersList to load via AJAX
    function bindPaginationLinks() {
        ordersList.querySelectorAll('.pagination a').forEach(a => {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.href;
                if (href) fetchOrders(href);
            });
        });
    }
    bindPaginationLinks();
});
</script>
@endsection@extends('user.layout')

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
                    @php
                        // Determine the middle step label depending on payment method
                        $payment = strtolower($order->payment_method ?? '');
                        if ($payment === 'cop') {
                            $middleKey = 'ready_to_pick_up';
                            $middleLabel = 'Ready for Pickup';
                        } else {
                            // default to COD behavior
                            $middleKey = 'to_delivery';
                            $middleLabel = 'Out for Delivery';
                        }

                        // Map statuses to step index (0..3)
                        $stepMap = [
                            'pending' => 0,
                            'confirmed' => 1,
                            $middleKey => 2,
                            'completed' => 3,
                        ];
                        $currentStep = $stepMap[$order->status] ?? (in_array($order->status, ['reject','rejected','canceled','cancelled']) ? -1 : 0);
                    @endphp

                    <p class="mb-2"><strong>Status:</strong> {{ $order->status_label }}</p>
                    <p class="mb-2"><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p class="mb-2"><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>

                    {{-- Visual progress steps --}}
                    <div class="mt-3">
                        <div class="flex items-center space-x-4 text-xs">
                            @php
                                $steps = [
                                    ['key' => 'pending', 'label' => 'Requested'],
                                    ['key' => 'confirmed', 'label' => 'Approved'],
                                    ['key' => $middleKey, 'label' => $middleLabel],
                                    ['key' => 'completed', 'label' => 'Completed'],
                                ];
                            @endphp

                            @foreach($steps as $index => $s)
                                @php
                                    if ($currentStep === -1) {
                                        // cancelled/rejected state: mark all steps as muted
                                        $state = 'muted';
                                    } elseif ($index < $currentStep) {
                                        $state = 'done';
                                    } elseif ($index == $currentStep) {
                                        $state = 'current';
                                    } else {
                                        $state = 'upcoming';
                                    }
                                @endphp

                                <div class="flex items-center">
                                    <div class="flex flex-col items-center">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center
                                            @if($state === 'done') bg-green-500 text-white
                                            @elseif($state === 'current') bg-green-500 text-white
                                            @elseif($state === 'muted') bg-gray-300 text-gray-600
                                            @else bg-gray-200 text-gray-500 @endif">
                                            {{-- small dot --}}
                                            &nbsp;
                                        </span>
                                        <span class="mt-1 text-center block whitespace-nowrap"
                                            @if($state === 'done') style="color:#047857;" @elseif($state === 'current') style="color:#047857;font-weight:600;" @elseif($state === 'muted') style="color:#6b7280;" @else style="color:#6b7280;" @endif>
                                            {{ $s['label'] }}
                                        </span>
                                    </div>
                                    @if($index < count($steps) - 1)
                                        {{-- connector line --}}
                                        <div class="flex-1 h-px bg-gray-200 mx-2" style="min-width:24px; max-width:80px;">
                                            @if($index < $currentStep)
                                                <div class="h-px bg-green-400" style="width:100%"></div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
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

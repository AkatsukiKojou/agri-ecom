@extends('user.layout')

@section('content')
<br>
  <div class="text-center mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold text-green-800">My Order History</h1>
    </div>
  <div class="max-w-5xl mx-auto mt-10 px-4">
        {{-- Filter Tabs and Search --}}
        <div class="flex items-center gap-3 mb-6 justify-center">
                <label for="orderSearch" class="sr-only">Search orders</label>
                <input id="orderSearch" type="search" placeholder="Search by product name, type or product ID"
                             class="w-full md:w-2/3 px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200" />
                {{-- span na search after search bar (acts like a button) --}}
                <span id="searchBtn" role="button" tabindex="0"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold cursor-pointer select-none">Search</span>
        </div>


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

            // Build a searchable string of product name, type (or category) and product id for client-side search
            $searchText = '';
            foreach($order->items as $item) {
                $prod = optional($item->product);
                $prodName = $prod->name ?? '';
                // try common fields for product type: 'type' or 'category'
                $prodType = $prod->type ?? ($prod->category ?? '');
                $prodId = $prod->id ?? '';
                $searchText .= " {$prodName} {$prodType} {$prodId}";
            }
            $searchText = trim(strtolower($searchText));
        @endphp
        <div class="border rounded-2xl p-6 mb-8 shadow-lg bg-gradient-to-br from-green-50 to-white hover:shadow-2xl transition" data-search="{{ $searchText }}">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded">Order #{{ $order->id }}</span>
                        <span class="ml-2 text-xs text-gray-500">Placed on: {{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @php
                        $status = strtolower($order->status ?? 'pending');
                        // Map status to step index: 1 = Requested, 2 = Approved, 3 = Completed
                        $stepIndex = match($status) {
                            'pending' => 1,
                            'requested' => 1,
                            'confirmed' => 2,
                            'approved' => 2,
                            'shipped' => 3,
                            'completed' => 3,
                            default => 1,
                        };
                    @endphp

                    <span class="font-semibold">Status:</span>
                    @if(in_array($status, ['cancelled', 'rejected']))
                        <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block" aria-hidden="true"></span>
                            <span class="capitalize">{{ $status === 'rejected' ? 'Rejected' : 'Cancelled' }}</span>
                        </span>
                    @else
                        <div class="w-64">
                            <div class="flex items-center justify-between">
                                @php
                                    $steps = ['Requested', 'Approved', 'Completed'];
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
                                                    <span class="text-xs text-gray-600">{{ $label }}</span>
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

            {{-- Product Images --}}
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach ($order->items as $item)
                    <div class="w-14 h-14 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center shadow-sm">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 text-xs">No Image</span>
                        @endif
                    </div>
                @endforeach
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
                            {{ $userShipping->name ?? 'N/A' }}
                        </p>
                        <p>
                            <span class="font-semibold">Shipping Address:</span>
                            {{ $userShipping->address ?? '' }}
                            {{ $userShipping->barangay ?? '' }}
                            {{ $userShipping->city ?? '' }},
                            {{ $userShipping->province ?? '' }},
                            {{ $userShipping->region ?? '' }}
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
       
    @endforelse
    <br><br><br>
    <div class="mt-6 mb-8 text-sm text-gray-600 text-center">These are orders older than 30 days. Recent orders (within 30 days) appear on your Orders page.</div>
</div>
@push('scripts')
<script>
    (function(){
        const input = document.getElementById('orderSearch');
        const btn = document.getElementById('searchBtn');

        function normalize(text){
            return (text || '').toString().toLowerCase();
        }

        function filterOrders(){
            const q = normalize(input.value.trim());
            const containers = document.querySelectorAll('[data-search]');
            containers.forEach(function(el){
                const hay = el.getAttribute('data-search') || '';
                if(!q) {
                    el.style.display = '';
                } else {
                    if(hay.indexOf(q) !== -1) {
                        el.style.display = '';
                    } else {
                        el.style.display = 'none';
                    }
                }
            });
        }

        if(input){
            input.addEventListener('input', filterOrders);
            input.addEventListener('keydown', function(e){
                if(e.key === 'Enter') { e.preventDefault(); filterOrders(); }
            });
        }
        if(btn){
            btn.addEventListener('click', function(){
                input.focus();
                filterOrders();
            });
            btn.addEventListener('keydown', function(e){ if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.focus(); filterOrders(); } });
        }
    })();
</script>
@endpush
@endsection

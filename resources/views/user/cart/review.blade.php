@extends('user.layout')

@section('content')
<br><br>
    <div class="max-w-9xl mx-auto px-6">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="text-3xl font-semibold text-green-800 mb-8">Review Your Order</h1>

        @php
            $defaultAddress = auth()->user()->shippingAddresses()->where('is_default', true)->first();
            $cart = session('cart', []);
            $selected = request('selected_products', session('selected_products', []));
            // $total, $shipping_fee, $shipping_breakdown are now passed from controller
        @endphp

        <div 
            x-data="{
                openModal: {{ $defaultAddress ? 'false' : 'true' }},
                openEdit: false,
                editAddress: {
                    id: null, name: '', phone: '', address: '', is_default: false,
                    region: '', province: '', city: '', barangay: ''
                },
                setEdit(address) {
                    this.editAddress = {...address};
                    this.openEdit = true;
                    this.$nextTick(() => {
                        window.populateEditDropdowns(this.editAddress);
                    });
                }
            }"
        >
            <div class="bg-white rounded-xl shadow-md p-6 mb-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-green-800 mb-2">Customer/Shipping Information</h2>
                    @if($defaultAddress)
                        <p class="text-gray-700"><strong>Name:</strong> {{ $defaultAddress->name }}</p>
                        <p class="text-gray-700"><strong>Phone:</strong> {{ $defaultAddress->phone }}</p>
                        <p class="text-gray-700"><strong>Email:</strong> {{ $defaultAddress->email }}</p>
                        <p class="text-gray-700">
                            <strong>Address:</strong>
                            {{ optional($defaultAddress)->address }},
                            {{ optional($defaultAddress)->barangay }},
                            {{ optional($defaultAddress)->city }},
                            {{ optional($defaultAddress)->province }},
                            {{ optional($defaultAddress)->region }}
                        </p>
                    @else
                        <p class="text-red-600 font-medium">
                            No default shipping address set. Please add one before placing your order.
                        </p>
                    @endif
                </div>
                <div>
                    <button @click="openModal = true"
                            class="text-green-600 font-semibold hover:text-green-800 underline">
                        Change Address
                    </button>
                </div>
            </div>

            {{-- Modal for Address Management --}}
            <div x-show="openModal"
                 x-transition
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 style="display: none;">
                <div @click.away="openModal = false"
                     class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative overflow-y-auto max-h-screen">

                    <button @click="openModal = false"
                            class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">
                        &times;
                    </button>

                    <h3 class="text-xl font-semibold mb-4">Manage Shipping Addresses</h3>

                    {{-- Existing Addresses --}}
                    @forelse(auth()->user()->shippingAddresses as $address)
                        <div class="border rounded-md p-4 mb-4 flex justify-between items-start {{ $address->is_default ? 'border-green-500' : 'border-gray-300' }}">
                            <div>
                                <p class="font-medium text-gray-900">{{ $address->name }}</p>
                                <p class="text-sm text-gray-700">{{ $address->phone }}</p>
                                <p class="text-sm text-gray-700">{{ $address->email }}</p>
                                <p class="text-sm text-gray-700">{{ $address->address }}</p>
                                @if($address->is_default)
                                    <span class="inline-block text-xs mt-1 bg-green-100 text-green-800 px-2 py-0.5 rounded">Default</span>
                                @endif
                            </div>
                            <div class="space-x-2">
                                {{-- Set Default --}}
                                @if(!$address->is_default)
                                   <form action="{{ route('user.shipping.setDefault', $address->id) }}" method="POST" class="inline ajax-address-action" data-action="set-default">
                                        @csrf
                                        @method('PUT')
                                        @foreach($selected as $pid)
                                            <input type="hidden" name="selected_products[]" value="{{ $pid }}">
                                        @endforeach
                                        <input type="hidden" name="redirect_to_review" value="1">
                                        <button class="text-sm text-green-600 hover:underline">Set as Default</button>
                                    </form>
                                @endif
                                {{-- Edit --}}
                                <button type="button"
                                    class="text-sm text-blue-600 hover:underline"
                                    @click="setEdit({
                                        id: {{ $address->id }},
                                        name: '{{ addslashes($address->name) }}',
                                        phone: '{{ addslashes($address->phone) }}',
                                        address: `{{ addslashes($address->address) }}`,
                                        is_default: {{ $address->is_default ? 'true' : 'false' }},
                                        region: '{{ addslashes($address->region) }}',
                                        province: '{{ addslashes($address->province) }}',
                                        city: '{{ addslashes($address->city) }}',
                                        barangay: '{{ addslashes($address->barangay) }}'
                                    })">
                                    Edit
                                </button>
                                {{-- Delete --}}
                                <form action="{{ route('user.shipping.destroy', $address->id) }}" method="POST" class="inline ajax-address-action" data-action="delete" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    @foreach($selected as $pid)
                                        <input type="hidden" name="selected_products[]" value="{{ $pid }}">
                                    @endforeach
                                    <button class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No addresses added yet.</p>
                    @endforelse

                    {{-- Add New Address --}}
                    <h4 class="text-lg font-semibold mt-6 mb-2 border-t pt-4">Add New Address</h4>
                    <form action="{{ route('user.shipping.store') }}" method="POST" class="space-y-4 ajax-address-action" data-action="store">
                        @csrf
                        @foreach($selected as $pid)
                            <input type="hidden" name="selected_products[]" value="{{ $pid }}">
                        @endforeach
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input id="name" name="name" type="text" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('name') }}">
                        </div>
                        <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input id="phone" name="phone" type="text" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500"
                    value="{{ old('phone') }}">
               </div>
               <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500"
                    value="{{ old('email') }}">
                        </div>
                        {{-- Location Dropdowns --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Region</label>
                            <select id="region" name="region" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Select Region</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Province</label>
                            <select id="province" name="province" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Select Province</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City/Municipality</label>
                            <select id="city" name="city" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Select City/Municipality</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barangay</label>
                            <select id="barangay" name="barangay" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Street/House Details</label>
                            <textarea id="address" name="address" rows="2" required
                                      class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500">{{ old('address') }}</textarea>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input id="is_default" name="is_default" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="is_default" class="text-sm text-gray-700">Set as default address</label>
                        </div>
                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <button type="button" @click="openModal = false"
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Edit Address Modal --}}
            <div x-show="openEdit"
                 x-transition
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 style="display: none;">
                <div @click.away="openEdit = false"
                     class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative overflow-y-auto max-h-screen">
                    <button @click="openEdit = false"
                            class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">
                        &times;
                    </button>
                    <h3 class="text-xl font-semibold mb-4">Edit Shipping Address</h3>
                    <form :action="`{{ url('user/shipping') }}/${editAddress.id}`" method="POST" class="space-y-4 ajax-address-action" data-action="update">
                        @csrf
                        @method('PUT')
                        @foreach($selected as $pid)
                            <input type="hidden" name="selected_products[]" value="{{ $pid }}">
                        @endforeach
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" x-model="editAddress.name" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" x-model="editAddress.phone" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500">
               </div>
               <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" x-model="editAddress.email" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Region</label>
                            <select id="edit-region" name="region" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Province</label>
                            <select id="edit-province" name="province" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City/Municipality</label>
                            <select id="edit-city" name="city" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barangay</label>
                            <select id="edit-barangay" name="barangay" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" rows="3" x-model="editAddress.address" required
                                      class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="is_default" value="1" x-model="editAddress.is_default"
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label class="text-sm text-gray-700">Set as default address</label>
                        </div>
                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <button type="button" @click="openEdit = false"
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session()->has('cart') && is_array(session('cart')))
            @if(count($selected) > 0)
                <div class="flex flex-col lg:flex-row gap-8" x-data="{ paymentMethod: 'cod' }">

                    {{-- Products Table --}}
                    <div class="flex-1 bg-white rounded-xl shadow-md overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900">Unit</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900">Quantity</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900" x-show="paymentMethod === 'cod'">Shipping Fee</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-900">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($selected as $productId)
                                    @if(isset($cart[$productId]))
                                        @php
                                            $item = $cart[$productId];
                                            // Find which seller this product belongs to in $shipping_breakdown
                                            $productShippingFee = 0;
                                            if(isset($shipping_breakdown)) {
                                                foreach($shipping_breakdown as $sellerId => $break) {
                                                    foreach($break['items'] as $entry) {
                                                        if($entry['product']->id == $productId) {
                                                            // Proportionally divide seller shipping fee by number of items for display
                                                            $productShippingFee = $break['shipping_fee'] / count($break['items']);
                                                        }
                                                    }
                                                }
                                            }
                                            $subtotal = ($item['price'] * $item['quantity']);
                                        @endphp
                                        <tr class="hover:bg-green-50 transition duration-150">
                                            <td class="px-6 py-4 flex items-center gap-4">
                                                <img src="{{ asset('storage/' . $item['image']) }}"
                                                     alt="{{ $item['name'] }}"
                                                     class="w-16 h-16 rounded-md object-cover border border-gray-300" />
                                                <span class="text-green-900 font-medium">{{ $item['name'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-green-800 font-semibold">₱{{ number_format($item['price'], 2) }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ !empty($item['unit']) ? $item['unit'] : '-' }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ $item['quantity'] }}</td>
                                            <template x-if="paymentMethod === 'cod'">
                                                <td class="px-6 py-4 text-gray-700">
                                                    <div class="text-green-800 font-medium product-shipping-fee" data-product-id="{{ $productId }}" data-shipping-fee="{{ $productShippingFee }}">
                                                        ₱{{ number_format($productShippingFee, 2) }}
                                                    </div>
                                                </td>
                                            </template>
                                            <td class="px-6 py-4 font-semibold text-green-700">
                                                ₱{{ number_format($subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Checkout Summary --}}
                    <div class="w-full max-w-md bg-white rounded-xl shadow-md p-8">
                        <h3 class="text-lg font-semibold text-green-900 mb-6">Order Summary</h3>

                        <form action="{{ route('checkout.otp') }}" method="POST" class="space-y-6">
                            @csrf
                            @foreach($selected as $productId)
                                <input type="hidden" name="selected_products[]" value="{{ $productId }}">
                            @endforeach

                            <!-- Pass shipping fee to controller -->
                            <input type="hidden" name="shipping_fee" value="{{ $shipping_fee ?? 0 }}">

                            {{-- Payment Method --}}
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-green-900 mb-2">
                                    Payment Method
                                </label>
                                <select id="payment_method" name="payment_method" required
                                        x-model="paymentMethod"
                                        class="w-full rounded-md border border-gray-300 px-4 py-2 text-green-900 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Select a Payment Method --</option>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="cop">Cash on Pickup</option>
                                </select>
                            </div>

                            {{-- Pickup Address (Admin) --}}
                            <template x-if="paymentMethod === 'cop'">
                                <div class="mb-2 p-3 rounded bg-green-50 border border-green-200">
                                    <div class="font-semibold text-green-900 mb-1">Pickup Address:</div>
                                    @php
                                        $admin = \App\Models\User::where('role', 'admin')->first();
                                        $adminProfile = $admin ? $admin->profile : null;
                                    @endphp
                                    @if($adminProfile)
                                        <div class="text-green-900 text-sm">
                                            @php
                                                $pickupParts = array_filter([
                                                    $adminProfile->barangay,
                                                    $adminProfile->city,
                                                    $adminProfile->province,
                                                    $adminProfile->region
                                                ]);
                                            @endphp
                                            {{ implode(', ', $pickupParts) }}
                                        </div>
                                    @else
                                        <div class="text-red-600 text-sm">Pickup address not set by admin.</div>
                                    @endif
                                </div>
                            </template>

                            <div id="order-summary-container">
                                @include('user.cart.partials.order_summary')
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500 mt-12 text-lg">No products selected for checkout.</div>
            @endif
        @else
            <div class="text-center text-gray-500 mt-12 text-lg">Your cart is empty.</div>
        @endif
    </div>
</div><script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // For Add New Address
    const regionSelect = document.getElementById('region');
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');

    // For Edit Address
    const editRegion = document.getElementById('edit-region');
    const editProvince = document.getElementById('edit-province');
    const editCity = document.getElementById('edit-city');
    const editBarangay = document.getElementById('edit-barangay');

    // Populate regions for Add
    axios.get('https://psgc.gitlab.io/api/regions/')
        .then(response => {
            response.data.forEach(region => {
                let opt = document.createElement('option');
                opt.value = region.name;
                opt.text = region.name;
                regionSelect.add(opt);
            });
        });

    regionSelect.onchange = function() {
        provinceSelect.length = 1;
        citySelect.length = 1;
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                    .then(response2 => {
                        response2.data.forEach(province => {
                            let opt = document.createElement('option');
                            opt.value = province.name;
                            opt.text = province.name;
                            provinceSelect.add(opt);
                        });
                    });
            });
    };

    provinceSelect.onchange = function() {
        citySelect.length = 1;
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                    .then(response2 => {
                        let province = response2.data.find(p => p.name === provinceSelect.value);
                        if (!province) return;
                        // Cities
                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                            .then(response3 => {
                                response3.data.forEach(city => {
                                    let opt = document.createElement('option');
                                    opt.value = city.name;
                                    opt.text = city.name + " (City)";
                                    citySelect.add(opt);
                                });
                            })
                            .finally(() => {
                                // Municipalities
                                axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                    .then(response4 => {
                                        response4.data.forEach(mun => {
                                            let opt = document.createElement('option');
                                            opt.value = mun.name;
                                            opt.text = mun.name + " (Municipality)";
                                            citySelect.add(opt);
                                        });
                                    });
                            });
                    });
            });
    };

    citySelect.onchange = function() {
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                    .then(response2 => {
                        let province = response2.data.find(p => p.name === provinceSelect.value);
                        if (!province) return;
                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                            .then(response3 => {
                                let city = response3.data.find(c => c.name === citySelect.value.replace(' (City)', ''));
                                if (city) {
                                    axios.get(`https://psgc.gitlab.io/api/cities/${city.code}/barangays/`)
                                        .then(response4 => {
                                            response4.data.forEach(barangay => {
                                                let opt = document.createElement('option');
                                                opt.value = barangay.name;
                                                opt.text = barangay.name;
                                                barangaySelect.add(opt);
                                            });
                                        });
                                } else {
                                    // Try as municipality
                                    axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                        .then(response5 => {
                                            let mun = response5.data.find(m => m.name === citySelect.value.replace(' (Municipality)', ''));
                                            if (mun) {
                                                axios.get(`https://psgc.gitlab.io/api/municipalities/${mun.code}/barangays/`)
                                                    .then(response6 => {
                                                        response6.data.forEach(barangay => {
                                                            let opt = document.createElement('option');
                                                            opt.value = barangay.name;
                                                            opt.text = barangay.name;
                                                            barangaySelect.add(opt);
                                                        });
                                                    });
                                            }
                                        });
                                }
                            });
                    });
            });
    };

    // --------- EDIT ADDRESS DROPDOWNS ---------
    window.populateEditDropdowns = async function(editAddress) {
        // Regions
        editRegion.length = 1;
        await axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                response.data.forEach(region => {
                    let opt = document.createElement('option');
                    opt.value = region.name;
                    opt.text = region.name;
                    editRegion.add(opt);
                });
                if (editAddress.region) editRegion.value = editAddress.region;
            });

        // Provinces
        editProvince.length = 1;
        if (editAddress.region) {
            await axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === editAddress.region);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            response2.data.forEach(province => {
                                let opt = document.createElement('option');
                                opt.value = province.name;
                                opt.text = province.name;
                                editProvince.add(opt);
                            });
                            if (editAddress.province) editProvince.value = editAddress.province;
                        });
                });
        }

        // Cities/Municipalities
        editCity.length = 1;
        if (editAddress.region && editAddress.province) {
            await axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === editAddress.region);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === editAddress.province);
                            if (!province) return;
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    response3.data.forEach(city => {
                                        let opt = document.createElement('option');
                                        opt.value = city.name;
                                        opt.text = city.name + " (City)";
                                        editCity.add(opt);
                                    });
                                })
                                .finally(() => {
                                    axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                        .then(response4 => {
                                            response4.data.forEach(mun => {
                                                let opt = document.createElement('option');
                                                opt.value = mun.name;
                                                opt.text = mun.name + " (Municipality)";
                                                editCity.add(opt);
                                            });
                                            if (editAddress.city) editCity.value = editAddress.city;
                                        });
                                });
                        });
                });
        }

        // Barangays
        editBarangay.length = 1;
        if (editAddress.region && editAddress.province && editAddress.city) {
            await axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === editAddress.region);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === editAddress.province);
                            if (!province) return;
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    let city = response3.data.find(c => c.name === editAddress.city.replace(' (City)', ''));
                                    if (city) {
                                        axios.get(`https://psgc.gitlab.io/api/cities/${city.code}/barangays/`)
                                            .then(response4 => {
                                                response4.data.forEach(barangay => {
                                                    let opt = document.createElement('option');
                                                    opt.value = barangay.name;
                                                    opt.text = barangay.name;
                                                    editBarangay.add(opt);
                                                });
                                                if (editAddress.barangay) editBarangay.value = editAddress.barangay;
                                            });
                                    } else {
                                        // Try as municipality
                                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                            .then(response5 => {
                                                let mun = response5.data.find(m => m.name === editAddress.city.replace(' (Municipality)', ''));
                                                if (mun) {
                                                    axios.get(`https://psgc.gitlab.io/api/municipalities/${mun.code}/barangays/`)
                                                        .then(response6 => {
                                                            response6.data.forEach(barangay => {
                                                                let opt = document.createElement('option');
                                                                opt.value = barangay.name;
                                                                opt.text = barangay.name;
                                                                editBarangay.add(opt);
                                                            });
                                                            if (editAddress.barangay) editBarangay.value = editAddress.barangay;
                                                        });
                                                }
                                            });
                                    }
                                });
                        });
                });
        }
    };
});
</script>
<script>
// Ensure shipping fee DOM shows server-provided values (fixes case when view renders 0 after adding address)
document.addEventListener('DOMContentLoaded', function () {
    // Formatter function to apply server-provided values from data attributes
    function applyShippingAndTotals() {
        // Main summary shipping fee (use ID to avoid colliding with per-product elements)
        const summaryFeeEl = document.getElementById('summary-shipping-fee');
        if (summaryFeeEl) {
            const fee = parseFloat(summaryFeeEl.getAttribute('data-shipping-fee')) || 0;
            summaryFeeEl.textContent = '₱' + fee.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Per-product shipping fees
        document.querySelectorAll('.product-shipping-fee').forEach(el => {
            const fee = parseFloat(el.getAttribute('data-shipping-fee')) || 0;
            el.textContent = '₱' + fee.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });

        // Subtotal (use ID)
        const subtotalEl = document.getElementById('order-subtotal-amount');
        if (subtotalEl) {
            const s = parseFloat(subtotalEl.getAttribute('data-order-subtotal')) || 0;
            subtotalEl.textContent = '₱' + s.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Totals (cod/cop) by ID
        const totalCod = document.getElementById('order-total-cod');
        if (totalCod) {
            const t = parseFloat(totalCod.getAttribute('data-order-total-cod')) || 0;
            totalCod.textContent = '₱' + t.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        const totalCop = document.getElementById('order-total-cop');
        if (totalCop) {
            const t2 = parseFloat(totalCop.getAttribute('data-order-total-cop')) || 0;
            totalCop.textContent = '₱' + t2.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    // Apply once on load
    applyShippingAndTotals();
    // expose globally so other scripts can call it
    window.applyShippingAndTotals = applyShippingAndTotals;

    // Use MutationObserver to re-apply when the checkout summary changes (useful when address is set via redirect or page updates)
    // Observe the document body to catch any updates; the handler will only act when the summary elements exist.
    const observer = new MutationObserver((mutations) => {
        // Small debounce
        clearTimeout(window.__applyTotalsTimeout);
        window.__applyTotalsTimeout = setTimeout(() => applyShippingAndTotals(), 50);
    });
    observer.observe(document.body, { childList: true, subtree: true, characterData: true });

    // Also re-apply when the window gains focus (user returns after address modal or redirect)
    window.addEventListener('focus', () => applyShippingAndTotals());

    // Retry a few times on short intervals to cover race conditions where server-rendered data arrives slightly after load
    let retryCount = 0;
    const retryInterval = setInterval(() => {
        applyShippingAndTotals();
        retryCount++;
        if (retryCount > 6) clearInterval(retryInterval);
    }, 300);

    // If after retries the shipping still shows 0 but a default address exists in the page, reload once to fetch server-calculated totals.
    // This handles cases where the page returned by the server contains correct values but some client-side timing/state left the UI showing 0.
    const checkAndReloadIfNeeded = () => {
        try {
            const summaryFeeEl = document.getElementById('summary-shipping-fee');
            const hasDefaultAddress = !!document.querySelector('.bg-white.rounded-xl.shadow-md.p-6 div p:not(:contains("No default"))');
            // Fallback detection: check the Customer/Shipping Information area for the 'No default' message absence
            const customerBox = document.querySelector('.bg-white.rounded-xl.shadow-md.p-6');
            let defaultExists = false;
            if (customerBox) {
                defaultExists = !/No default shipping address set/.test(customerBox.innerText);
            }

            const summaryText = summaryFeeEl ? summaryFeeEl.textContent.replace(/[^0-9.]/g, '') : '';
            const numeric = parseFloat(summaryText) || 0;

            // Only reload once to avoid loops
            const reloadedKey = 'agri_review_reloaded_for_shipping';
            const alreadyReloaded = sessionStorage.getItem(reloadedKey);

            if (defaultExists && numeric === 0 && !alreadyReloaded) {
                // Wait a short moment to allow any last-moment updates, then reload
                setTimeout(() => {
                    sessionStorage.setItem(reloadedKey, '1');
                    window.location.reload();
                }, 400);
            }
        } catch (e) {
            // swallow errors; not critical
            console.error('checkAndReloadIfNeeded error', e);
        }
    };

    // Run after retries complete
    setTimeout(() => {
        checkAndReloadIfNeeded();
    }, 2500);
});
</script>
<script>
// AJAX handler for address actions: set default, store, update, delete
document.addEventListener('DOMContentLoaded', function () {
    // Utility: parse returned HTML and extract elements by selector
    function parseAndReplaceHtml(htmlString) {
        try {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlString, 'text/html');

                    // Replace customer box
                    const newCustomerBox = doc.querySelector('.bg-white.rounded-xl.shadow-md.p-6');
                    const oldCustomerBox = document.querySelector('.bg-white.rounded-xl.shadow-md.p-6');
                    if (newCustomerBox && oldCustomerBox) oldCustomerBox.replaceWith(newCustomerBox.cloneNode(true));

                    // Update summary shipping fee
                    const newSummary = doc.getElementById('summary-shipping-fee');
                    const oldSummary = document.getElementById('summary-shipping-fee');
                    if (newSummary && oldSummary) {
                        oldSummary.setAttribute('data-shipping-fee', newSummary.getAttribute('data-shipping-fee'));
                        oldSummary.textContent = newSummary.textContent;
                    }

                    // Update subtotal and totals
                    const newSubtotal = doc.getElementById('order-subtotal-amount');
                    const oldSubtotal = document.getElementById('order-subtotal-amount');
                    if (newSubtotal && oldSubtotal) {
                        oldSubtotal.setAttribute('data-order-subtotal', newSubtotal.getAttribute('data-order-subtotal'));
                        oldSubtotal.textContent = newSubtotal.textContent;
                    }

                    const newTotalCod = doc.getElementById('order-total-cod');
                    const oldTotalCod = document.getElementById('order-total-cod');
                    if (newTotalCod && oldTotalCod) {
                        oldTotalCod.setAttribute('data-order-total-cod', newTotalCod.getAttribute('data-order-total-cod'));
                        oldTotalCod.textContent = newTotalCod.textContent;
                    }

                    const newTotalCop = doc.getElementById('order-total-cop');
                    const oldTotalCop = document.getElementById('order-total-cop');
                    if (newTotalCop && oldTotalCop) {
                        oldTotalCop.setAttribute('data-order-total-cop', newTotalCop.getAttribute('data-order-total-cop'));
                        oldTotalCop.textContent = newTotalCop.textContent;
                    }

                    // Update per-product shipping fees (by data-product-id)
                    const newProductFees = doc.querySelectorAll('.product-shipping-fee');
                    newProductFees.forEach(newEl => {
                        const pid = newEl.getAttribute('data-product-id');
                        const oldEl = document.querySelector(`.product-shipping-fee[data-product-id="${pid}"]`);
                        if (oldEl) {
                            oldEl.setAttribute('data-shipping-fee', newEl.getAttribute('data-shipping-fee'));
                            oldEl.textContent = newEl.textContent;
                        }
                    });

                    // Re-run the formatting helper to ensure currency formatting
            if (window.applyShippingAndTotals) window.applyShippingAndTotals();
            return true;
        } catch (e) {
            console.error('Failed to parse response HTML', e);
            return false;
        }
    }
    }

    // Intercept forms with class ajax-address-action
    document.querySelectorAll('form.ajax-address-action').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            // For edit forms, the action may be an Alpine-bound attribute. Resolve it.
            const action = form.getAttribute('action') || form.action;
            const method = (form.querySelector('input[name="_method"]') || { value: (form.method || 'post') }).value || form.method || 'post';

            const data = new FormData(form);
            // axios needs content-type multipart for FormData

            // Post to action url
            axios.post(action, data)
                .then(res => {
                    const contentType = (res.headers && res.headers['content-type']) || '';
                    // If server returned HTML (string), parse it
                    if (typeof res.data === 'string' && contentType.includes('text/html')) {
                        parseAndReplaceHtml(res.data);
                        return;
                    }

                    // If server returned shipping JSON (from setDefault), update DOM directly
                    if (res.data && typeof res.data.shipping_fee !== 'undefined') {
                        // If server provided rendered HTML partial, replace the summary block
                        if (res.data.orderSummaryHtml) {
                            const container = document.querySelector('#order-summary-container');
                            if (container) {
                                container.innerHTML = res.data.orderSummaryHtml;
                                // Re-apply any helper formatting
                                if (window.applyShippingAndTotals) window.applyShippingAndTotals();
                                return;
                            }
                        }
                        const shippingFee = parseFloat(res.data.shipping_fee) || 0;
                        const formatted = '₱' + (shippingFee).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const summaryEl = document.getElementById('summary-shipping-fee');
                        if (summaryEl) {
                            summaryEl.setAttribute('data-shipping-fee', shippingFee);
                            summaryEl.textContent = formatted;
                        }

                        // Update per-product fees if provided
                        if (res.data.product_fees) {
                            Object.keys(res.data.product_fees).forEach(pid => {
                                const fee = parseFloat(res.data.product_fees[pid]) || 0;
                                const el = document.querySelector(`.product-shipping-fee[data-product-id="${pid}"]`);
                                if (el) {
                                    el.setAttribute('data-shipping-fee', fee);
                                    el.textContent = '₱' + fee.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            });
                        }

                        // Update totals
                        const subtotalEl = document.getElementById('order-subtotal-amount');
                        if (subtotalEl && typeof res.data.total !== 'undefined') {
                            subtotalEl.setAttribute('data-order-subtotal', res.data.total);
                            subtotalEl.textContent = '₱' + (res.data.total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        const totalCod = document.getElementById('order-total-cod');
                        if (totalCod && typeof res.data.total !== 'undefined') {
                            totalCod.setAttribute('data-order-total-cod', res.data.total + shippingFee);
                            totalCod.textContent = '₱' + (res.data.total + shippingFee).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        const totalCop = document.getElementById('order-total-cop');
                        if (totalCop && typeof res.data.total !== 'undefined') {
                            totalCop.setAttribute('data-order-total-cop', res.data.total + shippingFee);
                            totalCop.textContent = '₱' + (res.data.total + shippingFee).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }

                        // Re-run the formatting helper if exists
                        if (window.applyShippingAndTotals) window.applyShippingAndTotals();
                        return;
                    }

                    // If JSON indicates redirect or success, fetch the review page and parse it
                    if (res.data && (res.data.redirect_to_review || res.data.success)) {
                        return axios.get(window.location.href).then(r => parseAndReplaceHtml(r.data));
                    }
                })
                .catch(err => {
                    console.error('Address AJAX error', err);
                    // On error fallback to full submit to keep behavior consistent
                    form.removeEventListener('submit', arguments.callee);
                    form.submit();
                });
        });
    });
});
</script>
@endsection
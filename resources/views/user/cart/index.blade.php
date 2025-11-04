@extends('user.layout')
@section('content')
    <!-- Top Bar -->
    <div class="bg-white/90 shadow p-4 mb-4 rounded-xl">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9"/>
                </svg>
                <span class="text-2xl font-bold text-green-800">Shop Cart</span>
            </div>
            <form action="{{ route('shop.search') }}" method="GET" class="flex items-center space-x-2">
                <input type="text" name="query" placeholder="Search products..."
                       class="border border-lime-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-lime-400 w-64 bg-white/80">
                <button type="submit"
                        class="bg-lime-600 hover:bg-lime-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-5xl mx-auto mb-32">
        <h1 class="text-2xl font-bold text-green-800 mb-4">My Shopping Cart</h1>

        @if(session()->has('cart') && count(session()->get('cart')) > 0)
                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <form action="{{ route('checkout.review') }}" method="POST" id="checkout-form">
                @csrf

                <!-- Product Table -->
                <div class="overflow-x-auto bg-white/90 shadow-md rounded-xl mb-6">
                    <table class="min-w-full table-auto">
                        <thead class="bg-lime-100">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" class="select-all-checkbox accent-lime-600" id="select-all-checkbox">
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Product</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Unit Price</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Unit</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Stock</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Quantity</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Total Price</th>
                                <th class="px-4 py-3 text-left font-semibold text-green-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cart = session('cart', []);
                                $selectedId = request('selected');
                                if ($selectedId && isset($cart[$selectedId])) {
                                    $selectedItem = [$selectedId => $cart[$selectedId]];
                                    unset($cart[$selectedId]);
                                    $cart = $selectedItem + $cart;
                                }
                            @endphp
                            @foreach($cart as $productId => $item)
                               <tr class="border-b hover:bg-lime-50 transition">
    <td class="px-4 py-3">
        <input type="checkbox" class="cart-checkbox accent-lime-600" name="selected_products[]"
            value="{{ $productId }}"
            data-price="{{ $item['price'] * $item['quantity'] }}"
            {{ request('selected') == $productId ? 'checked' : '' }}>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center">
            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                 class="w-14 h-14 object-cover rounded-lg border-2 border-lime-200 mr-4 shadow">
            <span class="font-semibold text-green-900">{{ $item['name'] }}</span>
        </div>
    </td>
    <td class="px-4 py-3 text-green-800 font-semibold">₱{{ number_format($item['price'], 2) }}</td>
    <td class="px-4 py-3 text-green-800 font-semibold">
        {{-- Debug removed --}}
        {{ !empty($item['unit']) ? $item['unit'] : '-' }}
    </td>
    <td class="px-4 py-3 text-green-800 font-semibold align-top">
        {{ $item['stock'] ?? 0 }}
    </td>
    <td class="px-4 py-3 align-top">
        <div class="flex items-center space-x-2">
            <button type="button" class="decrease-btn bg-lime-100 px-3 py-1 rounded-lg font-bold text-lg text-green-800 hover:bg-lime-200 transition" data-product-id="{{ $productId }}">−</button>
            <input type="text" class="quantity-input w-12 text-center border border-lime-300 rounded-lg font-semibold text-green-900"
                data-product-id="{{ $productId }}" value="{{ $item['quantity'] }}"
                data-stock="{{ $item['stock'] ?? 9999 }}">
            <input type="hidden" name="quantities[{{ $productId }}]"
                class="hidden-quantity" data-product-id="{{ $productId }}" value="{{ $item['quantity'] }}">
            <button type="button" class="increase-btn bg-lime-100 px-3 py-1 rounded-lg font-bold text-lg text-green-800 hover:bg-lime-200 transition" data-product-id="{{ $productId }}">+</button>
        </div>
        <div class="text-xs text-red-600 stock-warning" style="display:none;">
            Exceeds available stock!
        </div>
    </td>
    <td class="px-4 py-3 total-price text-green-800 font-semibold" data-product-id="{{ $productId }}">
        ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
    </td>
    <td class="px-4 py-3">
        <a href="{{ route('cart.remove', $productId) }}" class="text-red-500 hover:text-red-700 font-semibold transition">Remove</a>
    </td>
</tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Checkout Section -->
                <div class="fixed bottom-0 left-0 right-0 bg-white/95 border-t shadow-lg py-4 px-6 flex flex-col sm:flex-row justify-between items-center z-50 gap-4">
                    <p class="text-lg font-semibold text-green-900">
                        Selected Total: ₱<span id="selected-total">0.00</span>
                    </p>
                    <button type="submit"
                            class="bg-lime-600 hover:bg-lime-700 text-white font-semibold py-3 px-8 rounded-xl shadow transition text-lg disabled:opacity-50"
                            id="checkout-button" disabled>
                        Proceed to Checkout
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-16">
                <p class="text-gray-500 text-lg">Your cart is empty.</p>
                <a href="{{ route('user.products.index') }}" class="mt-4 inline-block text-lime-600 hover:underline font-semibold">Go Shopping</a>
            </div>
        @endif
    </div>

    <script>
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        const totalDisplay = document.getElementById('selected-total');
        const checkoutButton = document.getElementById('checkout-button');

        function updateRowTotal(productId) {
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            const hiddenInput = document.querySelector(`.hidden-quantity[data-product-id="${productId}"]`);
            let quantity = parseInt(input.value);
            const stock = parseInt(input.dataset.stock);

            // Auto-adjust quantity if it exceeds stock
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            if (quantity > stock) quantity = stock;
            input.value = quantity;
            hiddenInput.value = quantity;

            const row = input.closest('tr');
            const unitPriceText = row.querySelector('td:nth-child(3)').textContent.replace('₱', '').replace(',', '');
            const unitPrice = parseFloat(unitPriceText);

            if (!isNaN(quantity) && !isNaN(unitPrice)) {
                const total = unitPrice * quantity;
                row.querySelector('.total-price').textContent = '₱' + total.toFixed(2);
                row.querySelector('.cart-checkbox').dataset.price = total;
                updateTotal();
            }

            // Stock check
            const warning = row.querySelector('.stock-warning');
            const plusBtn = row.querySelector('.increase-btn');
            if (quantity > stock) {
                input.classList.add('border-red-500');
                warning.style.display = '';
                if (plusBtn) plusBtn.disabled = true;
            } else {
                input.classList.remove('border-red-500');
                warning.style.display = 'none';
                if (plusBtn) plusBtn.disabled = false;
            }
        }

        function updateTotal() {
            let total = 0;
            let selected = false;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const price = parseFloat(checkbox.dataset.price);
                    if (!isNaN(price)) {
                        total += price;
                        selected = true;
                    }
                }
            });

            totalDisplay.textContent = total.toFixed(2);
            checkoutButton.disabled = !selected || hasStockError();
        }

        function hasStockError() {
            let error = false;
            document.querySelectorAll('.quantity-input').forEach(input => {
                const stock = parseInt(input.dataset.stock);
                const qty = parseInt(input.value);
                if (qty > stock) {
                    error = true;
                }
            });
            return error;
        }

        document.querySelectorAll('.increase-btn').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.productId;
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const stock = parseInt(input.dataset.stock);
                let value = parseInt(input.value) + 1;
                if (value > stock) value = stock;
                input.value = value;
                updateRowTotal(productId);
            });
        });

        document.querySelectorAll('.decrease-btn').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.productId;
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                let value = Math.max(1, parseInt(input.value) - 1);
                input.value = value;
                updateRowTotal(productId);
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        document.getElementById('select-all-checkbox').addEventListener('change', (e) => {
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateTotal();
        });

        // Prevent submitting with no selected checkboxes or stock error
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!anyChecked) {
                e.preventDefault();
                alert("Please select at least one product before checking out.");
                return;
            }
            if (hasStockError()) {
                e.preventDefault();
                alert("One or more products exceed available stock. Please adjust quantities.");
            }
        });

        updateTotal();

        // Move updateBackendQuantity outside the loop so all listeners can use it
        function updateBackendQuantity(productId, quantity) {
            fetch(`/cart/update/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ quantity })
            })
            .then(response => response.json())
            .then(data => {
                // Optionally show a message or update UI
            });
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                let value = parseInt(this.value.replace(/[^0-9]/g, ''));
                const stock = parseInt(this.dataset.stock);
                if (value > stock) value = stock;
                if (value < 1 || isNaN(value)) value = 1;
                this.value = value;
                updateRowTotal(this.dataset.productId);
                updateBackendQuantity(this.dataset.productId, value);
            });

            input.addEventListener('blur', function() {
                let value = parseInt(this.value);
                const stock = parseInt(this.dataset.stock);
                if (isNaN(value) || value < 1) value = 1;
                if (value > stock) value = stock;
                this.value = value;
                updateRowTotal(this.dataset.productId);
                updateBackendQuantity(this.dataset.productId, value);
            });
        });

        // Remove any previous listeners before adding new ones
        document.querySelectorAll('.increase-btn').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });
        document.querySelectorAll('.decrease-btn').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });

        document.querySelectorAll('.increase-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const stock = parseInt(input.dataset.stock);
                let value = parseInt(input.value) + 1;
                if (value > stock) value = stock;
                input.value = value;
                updateRowTotal(productId);
                updateBackendQuantity(productId, value);
            });
        });

        document.querySelectorAll('.decrease-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                let value = Math.max(1, parseInt(input.value) - 1);
                input.value = value;
                updateRowTotal(productId);
                updateBackendQuantity(productId, value);
            });
        });
    </script>

    <style>
        .max-w-5xl {
            height: calc(100vh - 120px);
            overflow-y: auto;
        }
        .table-auto {
            width: 100%;
        }
        /* Custom scrollbar for cart */
        .max-w-5xl::-webkit-scrollbar {
            width: 8px;
        }
        .max-w-5xl::-webkit-scrollbar-thumb {
            background: #d9f99d;
            border-radius: 8px;
        }
        .max-w-5xl::-webkit-scrollbar-track {
            background: #f7fee7;
        }
        .border-red-500 {
            border-color: #f87171 !important;
        }
    </style>
@endsection
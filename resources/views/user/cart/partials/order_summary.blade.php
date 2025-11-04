<div class="p-6 bg-white rounded-md shadow-sm">
        {{-- Payment method select is defined in the parent review page; this partial reads Alpine's paymentMethod --}}

        <div class="flex justify-between items-center mb-2 text-green-900">
            <span>Subtotal:</span>
            <span id="order-subtotal-amount" data-order-subtotal="{{ $total ?? 0 }}">₱{{ number_format($total ?? 0, 2) }}</span>
        </div>

        <div x-show="paymentMethod === 'cod'" x-cloak class="flex justify-between items-center mb-2 text-green-900" id="shipping-row-cod">
            <span>Shipping Fee:</span>
            <span id="summary-shipping-fee" data-shipping-fee="{{ $shipping_fee ?? 0 }}">₱{{ number_format($shipping_fee ?? 0, 2) }}</span>
        </div>

        <div class="flex justify-between items-center mb-6 font-medium">
            <span>Total:</span>
            <span class="text-green-700 text-xl font-bold">
                <span x-show="paymentMethod === 'cod'" x-cloak id="order-total-cod" data-order-total-cod="{{ ($total ?? 0) + ($shipping_fee ?? 0) }}">₱{{ number_format(($total ?? 0) + ($shipping_fee ?? 0), 2) }}</span>
                <span x-show="paymentMethod === 'cop'" x-cloak id="order-total-cop" data-order-total-cop="{{ ($total ?? 0) }}">₱{{ number_format(($total ?? 0), 2) }}</span>
            </span>
        </div>

        <div>
            <label for="shipping_message" class="block text-sm font-medium text-green-900 mb-2">Message to LSA</label>
            <textarea id="shipping_message" name="shipping_message" rows="3" class="w-full rounded-md border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Please leave a message...">{{ old('shipping_message') }}</textarea>
        </div>
    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-md transition duration-150 mt-4">Place Order</button>
    
</div>
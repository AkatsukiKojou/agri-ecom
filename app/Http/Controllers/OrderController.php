<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Products;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use Log;
use App\Services\ShippingFeeService;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'selected_products' => 'required|array|min:1',
            'payment_method' => 'required',
        ]);

        $inputOtp = $request->input('otp');
        $sessionOtp = session('order_otp');
        $expiry = session('order_otp_expiry');

        if (!$sessionOtp || !$expiry || now()->gt($expiry)) {
            // Return validation error tied to the otp field so the view can show it inline
            return back()->withErrors(['otp' => 'OTP expired. Please try again.'])->withInput();
        }
        if ($inputOtp != $sessionOtp) {
            // Return validation error tied to the otp field so the view can show it inline
            return back()->withErrors(['otp' => 'Incorrect OTP. Please try again.'])->withInput();
        }

        // OTP is valid, proceed to place order
        // Reuse checkout logic but skip OTP step
        // You may want to refactor order creation to a private method for DRY
        // Merge selected products and payment method into request for checkout
        $request->merge([
            'selected_products' => $request->input('selected_products'),
            'payment_method' => $request->input('payment_method')
        ]);

        // If shipping_message was stored in session during OTP step, merge it too
        if (session()->has('checkout_shipping_message')) {
            $request->merge(['shipping_message' => session('checkout_shipping_message')]);
            // Clear it after merging so it doesn't persist
            session()->forget('checkout_shipping_message');
        }
        // Clear OTP from session
        session()->forget(['order_otp', 'order_otp_expiry']);
        return $this->checkout($request);
    }
    public function checkoutOtp(Request $request)
    {
        // If GET, just show the OTP view. The selected_products/payment_method should be
        // available in the session (set during POST) or passed via query/session when redirected back.
        if ($request->isMethod('get')) {
            $selected = session('otp_selected_products', session('selected_products', []));
            $paymentMethod = session('otp_payment_method', session('payment_method', null));

            return view('user.cart.otp', [
                'selected_products' => $selected,
                'payment_method' => $paymentMethod,
            ]);
        }

        // POST: validate incoming checkout data and send OTP
        $request->validate([
            'selected_products' => 'required|array|min:1',
            'payment_method' => 'required',
        ]);

        $user = Auth::user();
        $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();
        if (!$defaultAddress) {
            return redirect()->route('checkout.review')->with('error', 'No default shipping address found.');
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        session(['order_otp' => $otp, 'order_otp_expiry' => now()->addMinutes(5)]);

        // Send OTP to default address email. Wrap in try/catch to avoid SMTP transport exceptions
        try {
            \Mail::to($defaultAddress->email)->send(new \App\Mail\VerificationCodeMail($otp));
        } catch (\Throwable $e) {
            // Log the exception for debugging but do not throw — allow flow to continue so user can still enter OTP
            \Log::error('OTP email send failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $defaultAddress->email ?? null,
            ]);
            // Optionally set a non-fatal flash so the user knows email may not be delivered
            session()->flash('warning', 'We were unable to send the verification email. Please check your email address or try again.');
        }

        // Persist selected_products/payment_method in session so GET view and redirects can access them
        session(['otp_selected_products' => $request->input('selected_products'), 'otp_payment_method' => $request->input('payment_method')]);

        // Persist shipping_message (if any) so it can be merged after OTP verification
        if ($request->filled('shipping_message')) {
            session(['checkout_shipping_message' => $request->input('shipping_message')]);
        }

        return view('user.cart.otp', [
            'selected_products' => $request->input('selected_products'),
            'payment_method' => $request->input('payment_method'),
        ]);
    }
    public function index(Request $request)
    {
        $query = Order::with([
            'items.product',
            'user.shippingAddresses',
            'shippingAddress'
        ])->where('user_id', Auth::id());

        // Only include orders from the last 30 days for the main index
        $thirtyDaysAgo = now()->subDays(30);
        $query->where('created_at', '>=', $thirtyDaysAgo);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  // search within order items product_name or the related product's name/type
                  ->orWhereHas('items', function($qi) use ($search) {
                      $qi->where('product_name', 'like', "%$search%")
                         ->orWhereHas('product', function($qp) use ($search) {
                             $qp->where('name', 'like', "%$search%")
                                ->orWhere('type', 'like', "%$search%");
                         });
                  });
            });
        }

        $orders = $query->latest()->get();

        return view('user.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('user.order.show', compact('order'));
    }

    public function checkout(Request $request)
    {
            $request->validate([
                'selected_products' => 'required|array|min:1',
            ]);


        $cart = session('cart', []);
        $selected = $request->input('selected_products', []);
        $user = Auth::user();

        // Get default shipping address
        $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();
        if (!$defaultAddress) {
            session()->flash('selected_products', $selected);
            return back()->with('error', 'Please add a default shipping address before placing your order.');
        }


        // Use ShippingFeeService for consistent shipping fee calculation
        $shippingService = new ShippingFeeService();
        [$shipping_fee, $shipping_breakdown, $total] = $shippingService->calculate(
            $cart,
            $selected,
            $user,
            function($address) { return $this->getCoordinates($address); }
        );

        // Calculate per-item shipping fee
        $orderItems = [];
        foreach ($selected as $productId) {
            if (isset($cart[$productId])) {
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
                $orderItems[] = [
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'shipping_fee' => $productShippingFee,
                ];
            }
        }

        if (empty($orderItems)) {
            return redirect()->route('checkout.review', ['selected_products' => $selected])
                ->with('error', 'No valid products selected.');
        }

        $paymentMethod = $request->input('payment_method', 'manual');

        // If payment method is cash on pickup, set shipping fee to 0
        if ($paymentMethod === 'cop') {
            $shipping_fee = 0;
            foreach ($orderItems as &$item) {
                $item['shipping_fee'] = 0;
            }
            unset($item); // break reference
        }

        DB::beginTransaction();
        try {
            // Snapshot the full address and email so past orders don't change when the user updates addresses later.
            $formattedAddress = implode(', ', array_filter([
                $defaultAddress->address ?? null,
                $defaultAddress->barangay ?? null,
                $defaultAddress->city ?? null,
                $defaultAddress->province ?? null,
                $defaultAddress->region ?? null,
            ]));

            // Create the order (now saving shipping_fee and email snapshot)
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $defaultAddress->name,
                'phone' => $defaultAddress->phone,
                'address' => $formattedAddress ?: ($defaultAddress->address ?? null),
                'email' => $defaultAddress->email ?? null,
                'total_price' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_fee' => $shipping_fee,
                'shipping_message' => $request->input('shipping_message'),
            ]);

            // Notify all admins via database only (no email)
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\OrderPlacedNotification($order));
            }

            // Save order items and update stock
            foreach ($orderItems as $item) {
                $product = Products::find($item['product_id']);
                if (!$product) throw new \Exception("Product not found.");
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $order->items()->create($item);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();
            // Log order creation
            \App\Models\ActivityLog::create([
                'user_admin' => Auth::user()->name ?? 'Unknown',
                'action' => 'User Placed Order',
                'details' => 'User #' . $user->id . ' placed Order #' . $order->id,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.review', ['selected_products' => $selected])
                ->with('error', $e->getMessage());
        }

            // Remove ordered items from cart
            foreach ($selected as $productId) {
                unset($cart[$productId]);
            }
            session(['cart' => $cart]);

            return view('user.cart.order_success');
        }

    // protected function computeDistance($lat1, $lng1, $lat2, $lng2)
    // {
    //     $earthRadius = 6371; // km
    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLng = deg2rad($lng2 - $lng1);

    //     $a = sin($dLat / 2) ** 2 +
    //         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
    //         sin($dLng / 2) ** 2;

    //     $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    //     return $earthRadius * $c;
    // }

    // protected function getCoordinates($address)
    // {
    //     $apiKey = env('OPENROUTESERVICE_API_KEY');
    //     $url = 'https://api.openrouteservice.org/geocode/search';

    //     $response = Http::withHeaders([
    //         'Authorization' => $apiKey,
    //     ])->get($url, [
    //         'text' => $address,
    //         'size' => 1,
    //     ]);

    //     if ($response->successful() && isset($response['features'][0]['geometry']['coordinates'])) {
    //         $coords = $response['features'][0]['geometry']['coordinates'];
    //         return [
    //             'lat' => $coords[1],
    //             'lng' => $coords[0],
    //         ];
    //     }

    //     return null;
    // }

    public function index1()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->get();
        return view('user.order.index1', compact('orders')); // fixed path
    }

public function cancel(Request $request, $id)
{
    $order = Order::with('items.product')->findOrFail($id);

    // Only allow cancel if pending and within 1 hour
    if ($order->status !== 'pending' || $order->created_at->diffInMinutes(now()) > 60) {
        return back()->with('error', 'Order can no longer be cancelled.');
    }

    // Return product quantities
    foreach ($order->items as $item) {
        if ($item->product) {
            $item->product->stock_quantity += $item->quantity;
            $item->product->save();
        }
    }

    $order->status = 'cancelled';
    $order->cancel_reason = $request->input('reason');
    $order->cancelled_at = now();
    $order->save();

    \App\Models\ActivityLog::create([
        'user_admin' => Auth::user()->name ?? 'Unknown',
        'action' => 'User Cancelled Order',
        'details' => 'User #' . Auth::id() . ' cancelled Order #' . $order->id,
        'timestamp' => now(),
    ]);

    return redirect()->route('user.orders')->with('success', 'Order cancelled and product stocks restored.');
}
   /**
     * Compute the distance between two coordinates using the Haversine formula.
     */
    protected function computeDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Get coordinates from OpenRouteService API.
     */
    protected function getCoordinates($address)
    {
        $apiKey = env('OPENROUTESERVICE_API_KEY');
        $url = 'https://api.openrouteservice.org/geocode/search';

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->get($url, [
            'text' => $address,
            'size' => 1,
        ]);

        if ($response->successful() && isset($response['features'][0]['geometry']['coordinates'])) {
            $coords = $response['features'][0]['geometry']['coordinates'];
            return [
                'lat' => $coords[1],
                'lng' => $coords[0],
            ];
        }

        return null;
    }



    public function history(Request $request) {
        $query = Order::with([
            'items.product',
            'user.shippingAddresses',
            'shippingAddress'
        ])->where('user_id', Auth::id());

        // Only include orders older than 30 days for history
        $thirtyDaysAgo = now()->subDays(30);
        $query->where('created_at', '<', $thirtyDaysAgo);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhereHas('items', function($qi) use ($search) {
                      $qi->where('product_name', 'like', "%$search%")
                         ->orWhereHas('product', function($qp) use ($search) {
                             $qp->where('name', 'like', "%$search%")
                                ->orWhere('type', 'like', "%$search%");
                         });
                  });
            });
        }

        $orders = $query->latest()->get();

        return view('user.order.history', compact('orders'));
    }
}

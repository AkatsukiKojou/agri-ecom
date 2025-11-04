<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $selectedIds = $request->input('selected_products', []);
        $cart = session()->get('cart', []);

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'No products selected.');
        }

        $selectedItems = [];
        $total = 0;

        foreach ($selectedIds as $productId) {
            if (isset($cart[$productId])) {
                $item = $cart[$productId];
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;
                $selectedItems[$productId] = $item;
            }
        }

        // Remove selected items from cart
        foreach ($selectedItems as $productId => $item) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Checkout completed successfully!');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'location' => 'required|string|max:255',
            'contact' => 'required|string|max:50',
            'shipping' => 'required|in:standard,express',
            'payment' => 'required|in:cod,gcash,paypal',
        ]);

        $items = session('checkout_items', []);
        $totalAmount = 0;

        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Create the order
        $order = Order::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'location' => $validated['location'],
            'contact' => $validated['contact'],
            'shipping' => $validated['shipping'],
            'payment' => $validated['payment'],
            'total' => $totalAmount,
        ]);

        // Create order items
        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
            ]);
        }

        // Clear session cart and checkout items
        session()->forget('cart');
        session()->forget('checkout_items');

        return redirect()->route('user.products.index')->with('success', 'Order placed successfully!');
    // }

    // public function review(Request $request)
    // {
    //     $cart = session('cart', []);
    //     $selected = $request->input('selected_products', old('selected_products', []));
    //     $user = Auth::user();

    //     // Get default shipping address of user
    //     $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();

    //     if (!$defaultAddress) {
    //         session()->flash('selected_products', $selected);
    //         return back()->with('error', 'Please add a default shipping address before placing your order.');
    //     }

    //     // Get admin profile (assuming admin address is in profile)
    //     $admin = User::where('role', 'admin')->first();
    //     $adminProfile = $admin->profile; // assuming you have a profile() relationship

    //     $userFullAddress = implode(', ', array_filter([
    //         $defaultAddress->barangay,
    //         $defaultAddress->city,
    //         $defaultAddress->province,
    //         $defaultAddress->region
    //     ]));
    //     $adminFullAddress = implode(', ', array_filter([
    //         $adminProfile->barangay,
    //         $adminProfile->city,
    //         $adminProfile->province,
    //         $adminProfile->region
    //     ]));

  

    //     $userCoords = $this->getCoordinates($userFullAddress);
    //     $adminCoords = $this->getCoordinates($adminFullAddress);

    //     // Log coordinates for debugging
      

    //     if (!$userCoords || !$adminCoords) {
    //         return back()->with('error', 'Unable to get coordinates for shipping calculation. Please check your address.');
    //     }

    //     $distance = $this->computeDistance(
    //         $userCoords['lat'], $userCoords['lng'],
    //         $adminCoords['lat'], $adminCoords['lng']
    //     );


    //     // Delivery fee logic
    //     $base_fee = 30;
    //     $base_distance = 3;
    //     $per_km_rate = 10;
    //     $extra_item_fee = 5;

    //     // Compute total and items
    //     $total = 0;
    //     $total_items = 0;
    //     foreach ($selected as $productId) {
    //         if (isset($cart[$productId])) {
    //             $item = $cart[$productId];
    //             $total += $item['price'] * $item['quantity'];
    //             $total_items += $item['quantity'];
    //         }
    //     }

    //     $extra_items = max(0, $total_items - 1);
    //     $extra_distance = max(0, $distance - $base_distance);

    //     $delivery_fee = $base_fee + ($extra_distance * $per_km_rate) + ($extra_items * $extra_item_fee);


    //     return view('user.cart.review', compact(
    //         'cart',
    //         'selected',
    //         'total',
    //         'delivery_fee'
    //     ));
    // }

    // /**
    //  * Compute the distance between two coordinates using the Haversine formula.
    //  */
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

    // /**
    //  * Get coordinates from OpenRouteService API.
    //  */
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
}
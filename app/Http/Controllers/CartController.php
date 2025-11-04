<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\User;


class CartController extends Controller
{
    // Show the shopping cart
    public function index()
    {
        return view('user.cart.index');
    }

    // Add a product to the cart
public function add(Request $request, $productId)
{
    $product = Products::findOrFail($productId);

    $cart = session()->get('cart', []);
    $quantity = (int) $request->input('quantity', 1);

    // If product already in cart, add but limit to stock
        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['quantity'] + $quantity;
            // Limit to available stock
            $cart[$productId]['quantity'] = min($newQty, $product->stock_quantity);
        } else {
            // Limit to available stock
            $cart[$productId] = [
                'name' => $product->name,
                'quantity' => min($quantity, $product->stock_quantity),
                'price' => $product->price,
                'image' => $product->image,
                'stock' => $product->stock_quantity,
                'unit' => $product->unit ?? '-',
            ];
    }

    session()->put('cart', $cart);

    return back()->with('success', 'Product Successfully Added to cart!');
}
    // Remove a product from the cart
    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        // Remove product from cart
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    // Update quantity of a product in the cart
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    
        $cart = session()->get('cart', []);
    
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
    
        // Optionally, you can return the updated cart or success message
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart' => $cart
        ]);
    }


public function review(Request $request)
{

    $cart = session('cart', []);
    // Selected products: prefer request -> session -> all cart items
    $selected = $request->input('selected_products', session('selected_products', []));
    if (empty($selected)) {
        $selected = array_keys($cart);
    }
    $user = Auth::user();

    // Auto-adjust quantities if they exceed stock
    foreach ($selected as $productId) {
        if (isset($cart[$productId])) {
            $product = Products::find($productId);
            $requestedQty = $cart[$productId]['quantity'];
            $stock = $product ? $product->stock_quantity : $cart[$productId]['stock'];
            if ($requestedQty > $stock) {
                $cart[$productId]['quantity'] = $stock;
            }
        }
    }
    session()->put('cart', $cart);

    // Group items by seller (admin_id)
    $itemsBySeller = [];
    foreach ($selected as $productId) {
        if (isset($cart[$productId])) {
            $product = Products::find($productId);
            if ($product) {
                $sellerId = $product->admin_id ?? $product->user_id ?? null;
                if ($sellerId) {
                    $itemsBySeller[$sellerId][] = [
                        'product' => $product,
                        'cart_item' => $cart[$productId],
                    ];
                }
            }
        }
    }

    // Get user shipping address
    $userFullAddress = null;
    $userCoords = null;
    if (method_exists($user, 'shippingAddresses')) {
        $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();
        if ($defaultAddress) {
            $userFullAddress = implode(', ', array_filter([
                $defaultAddress->barangay,
                $defaultAddress->city,
                $defaultAddress->province,
                $defaultAddress->region
            ]));
            $userCoords = $this->getCoordinates($userFullAddress);
        }
    }

    $shipping_fee = 0;
    $shipping_breakdown = [];
    $total = 0;

    foreach ($itemsBySeller as $sellerId => $items) {
        // Get seller profile/address
        $seller = User::find($sellerId);
        $profile = $seller ? $seller->profile : null;
        if (!$profile) continue;
        $sellerFullAddress = implode(', ', array_filter([
            $profile->barangay,
            $profile->city,
            $profile->province,
            $profile->region
        ]));
        $sellerCoords = $this->getCoordinates($sellerFullAddress);

        $distance = 0;
        $base_fee = 20;
        $base_distance = 3;
        $rate_per_km = 5;
        $extra_item_fee = 40;

        if ($userCoords && $sellerCoords) {
            $distance = $this->computeDistance(
                $userCoords['lat'], $userCoords['lng'],
                $sellerCoords['lat'], $sellerCoords['lng']
            );
            if ($distance > 100) {
                $distance = 100;
            }
        }

        // Compute total items for this seller
        $seller_total_items = 0;
        $seller_total = 0;
        foreach ($items as $entry) {
            $seller_total_items += $entry['cart_item']['quantity'];
            $seller_total += $entry['cart_item']['price'] * $entry['cart_item']['quantity'];
        }
        $extra_distance = max(0, $distance - $base_distance);
        $extra_distance_fee = $extra_distance * $rate_per_km;
        $extra_items = max(0, $seller_total_items - 1);
        $extra_item_fee_total = $extra_items * $extra_item_fee;
        $seller_shipping_fee = $base_fee + $extra_distance_fee + $extra_item_fee_total;

        // Fallback: if no user coordinates (no default address), do NOT charge shipping yet.
        // If we have seller coordinates but no user coordinates, we cannot compute distance-based fee
        // and we should wait until user sets an address. If userCoords exists but sellerCoords
        // is missing, fall back to base fee + extra item fee for that seller.
        if (!$userCoords || !$sellerCoords) {
            $distance = 0;
            $extra_distance_fee = 0;
            if (!$userCoords) {
                // No default user address -> defer charging shipping until address is set
                $seller_shipping_fee = 0;
            } else {
                // User coords exist but seller coords missing -> apply base fallback
                $seller_shipping_fee = $base_fee + $extra_item_fee_total;
            }
        }

        $shipping_fee += $seller_shipping_fee;
        $total += $seller_total;
        $shipping_breakdown[$sellerId] = [
            'seller' => $seller,
            'profile' => $profile,
            'items' => $items,
            'distance' => $distance,
            'shipping_fee' => $seller_shipping_fee,
            'total' => $seller_total,
        ];
    }

    return view('user.cart.review', compact(
        'cart',
        'selected',
        'total',
        'shipping_fee',
        'shipping_breakdown'
    ));
}

// ...existing code...

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

        // Try OpenRouteService first (if API key present). Wrap in try/catch to avoid
        // throwing a ConnectionException to the HTTP layer which would cause a 500.
        try {
            if ($apiKey) {
                $url = 'https://api.openrouteservice.org/geocode/search';
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->timeout(8)->get($url, [
                    'text' => $address,
                    'size' => 1,
                ]);

                if ($response->successful() && isset($response['features'][0]['geometry']['coordinates'])) {
                    $coords = $response['features'][0]['geometry']['coordinates'];
                    return ['lat' => $coords[1], 'lng' => $coords[0]];
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::warning('OpenRouteService connection failed: ' . $e->getMessage());
            // continue to fallback
        } catch (\Exception $e) {
            \Log::warning('OpenRouteService error: ' . $e->getMessage());
            // continue to fallback
        }

        // Fallback: Nominatim (OpenStreetMap) - good for dev/testing. Also wrapped in try/catch.
        try {
            $n = Http::timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);
            if ($n->successful() && isset($n->json()[0]['lat'])) {
                return ['lat' => floatval($n->json()[0]['lat']), 'lng' => floatval($n->json()[0]['lon'])];
            }
        } catch (\Exception $e) {
            \Log::warning('Nominatim fallback failed: ' . $e->getMessage());
        }

        return null;
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

    // TODO: Save to orders table
    // Example:
    // Order::create([...]);

    session()->forget('cart');
    session()->forget('checkout_items');

    return redirect()->route('admin.orders.index')->with('success', 'Order placed successfully!');
}

    public function addToCart(Request $request, $id)
    {
        $product = Products::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = (int) $request->input('quantity', 1);

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock_quantity) {
            return redirect()->route('cart.index')->with('error', 'Quantity exceeds available stock!');
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $newQty;
            $cart[$id]['stock'] = $product->stock_quantity;
            $cart[$id]['unit'] = $product->unit ?? '-';
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $quantity,
                'stock' => $product->stock_quantity,
                'unit' => $product->unit ?? '-',
            ];
        }
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }


    public function buyNow(Request $request, $id)
    {
        $product = Products::findOrFail($id);
        $quantity = (int) $request->input('quantity', 1);
        $cart = session()->get('cart', []);

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock_quantity) {
            return redirect()->route('cart.index')->with('error', 'Quantity exceeds available stock!');
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $newQty;
            $cart[$id]['stock'] = $product->stock_quantity;
            $cart[$id]['unit'] = $product->unit ?? '-';
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $quantity,
                'stock' => $product->stock_quantity,
                'unit' => $product->unit ?? '-',
            ];
        }
        session()->put('cart', $cart);

        return redirect()->route('cart.index', ['selected' => $id])
            ->with('success', 'Product added to cart! You can review or add more items before checkout.');
    }

public function updateQuantity(Request $request)
{
    $productId = $request->input('product_id');
    $quantity = max(1, (int) $request->input('quantity'));

    $cart = session()->get('cart', []);

    if (isset($cart[$productId])) {
        $stock = isset($cart[$productId]['stock']) ? (int)$cart[$productId]['stock'] : 999999;
        if ($quantity > $stock) {
            return redirect()->route('cart.index')->with('error', 'Quantity exceeds available stock!');
        }
        $cart[$productId]['quantity'] = $quantity;
        session()->put('cart', $cart);
    }

    return redirect()->route('cart.review');
}



}
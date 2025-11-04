<?php


namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ShippingFeeService;
use Illuminate\Support\Facades\Http as HttpClient;
class ShippingController extends Controller
{
    public function index()
    {
        $shippingAddresses = Auth::user()->shippingAddresses;
        return view('user.shipping.index', compact('shippingAddresses'));
    }


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'region' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'barangay' => 'required|string|max:255',
        'address' => 'required|string|max:1000',
    ]);

    $user = Auth::user();

    // If set as default, remove default from others
    if ($request->has('is_default')) {
        $user->shippingAddresses()->update(['is_default' => false]);
    }

    $user->shippingAddresses()->create([
    'name' => $request->name,
    'phone' => $request->phone,
    'email' => $request->email,
    'region' => $request->region,
    'province' => $request->province,
    'city' => $request->city,
    'barangay' => $request->barangay,
    'address' => $request->address,
    'is_default' => $request->has('is_default'),
    ]);

    // Persist selected_products to session so selection remains after reload
    session()->put('selected_products', $request->input('selected_products', []));

    return back()->with('success', 'Address saved!');
}

public function update(Request $request, $id)
{
    $address = Auth::user()->shippingAddresses()->findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'region' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'barangay' => 'required|string|max:255',
        'address' => 'required|string|max:1000',
    ]);

    // If set as default, remove default from others
    if ($request->has('is_default')) {
        Auth::user()->shippingAddresses()->update(['is_default' => false]);
    }

    $address->update([
    'name' => $request->name,
    'phone' => $request->phone,
    'email' => $request->email,
    'region' => $request->region,
    'province' => $request->province,
    'city' => $request->city,
    'barangay' => $request->barangay,
    'address' => $request->address,
    'is_default' => $request->has('is_default'),
    ]);

    // Persist selected_products to session so selection remains after reload
    session()->put('selected_products', $request->input('selected_products', []));

    return back()->with('success', 'Shipping Address updated.');
}

// ...existing code...

// public function setDefault($id)
// {
//     $user = Auth::user();
//     // Set all addresses to not default
//     $user->shippingAddresses()->update(['is_default' => false]);
//     // Set selected address to default
//     $address = $user->shippingAddresses()->findOrFail($id);
//     $address->is_default = true;
//     $address->save();

//     return back()->with('success', 'Default address updated.');
// }

public function setDefault(Request $request, $id)
{
    $user = Auth::user();
    // Set all addresses to not default
    $user->shippingAddresses()->update(['is_default' => false]);
    // Set selected address as default
    $user->shippingAddresses()->where('id', $id)->update(['is_default' => true]);

    $selected = $request->input('selected_products', []);

    // Persist selected products into session so a page reload retains the selection
    if (!empty($selected)) {
        session()->put('selected_products', $selected);
    }

    // If this is an AJAX request (address set from review), compute shipping and return JSON
    if ($request->ajax() || $request->wantsJson()) {
        $cart = session('cart', []);
        // If selected not provided, assume all products in cart
        if (empty($selected)) {
            $selected = array_keys($cart);
        }

        $shippingService = new ShippingFeeService();
        [$shipping_fee, $shipping_breakdown, $total] = $shippingService->calculate($cart, $selected, $user, [$this, 'getCoordinates']);

        // Build per-product shipping fees (simple map productId => fee)
        $productFees = [];
        foreach ($shipping_breakdown as $sellerId => $break) {
            $countItems = count($break['items']) ?: 1;
            $per = $countItems ? ($break['shipping_fee'] / $countItems) : 0;
            foreach ($break['items'] as $entry) {
                $prod = $entry['product'] ?? null;
                $pid = $prod->id ?? null;
                if ($pid) {
                    $productFees[$pid] = round($per, 2);
                }
            }
        }

        // Optionally persist into session
        session(['shipping_fee' => $shipping_fee, 'shipping_breakdown' => $shipping_breakdown, 'order_total' => $total]);

        // Render the order summary partial so client can replace the block if desired
        $orderSummaryHtml = view('user.cart.partials.order_summary', [
            'total' => $total,
            'shipping_fee' => $shipping_fee,
        ])->render();

        return response()->json([
            'success' => true,
            'shipping_fee' => $shipping_fee,
            'formatted_shipping_fee' => number_format($shipping_fee, 2),
            'product_fees' => $productFees,
            'total' => $total,
            'formatted_total' => number_format($total + ($shipping_fee ?? 0), 2),
            'orderSummaryHtml' => $orderSummaryHtml,
        ]);
    }

    // If coming from review page via normal form submit, redirect back to review with selected products
    if ($request->has('redirect_to_review')) {
        return redirect()->route('checkout.review', ['selected_products' => $selected])
            ->with('success', 'Default address updated!');
    }

    // Otherwise, just go back
    return back()->with('success', 'Default address updated!');
}

/**
 * Get coordinates for an address using OpenRouteService (fallbacks possible).
 */
protected function getCoordinates($address)
{
    $apiKey = env('OPENROUTESERVICE_API_KEY');

    if ($apiKey) {
        $url = 'https://api.openrouteservice.org/geocode/search';
        $response = HttpClient::withHeaders(['Authorization' => $apiKey])
                        ->get($url, ['text' => $address, 'size' => 1]);
        if ($response->successful() && isset($response['features'][0]['geometry']['coordinates'])) {
            $coords = $response['features'][0]['geometry']['coordinates'];
            return ['lat' => $coords[1], 'lng' => $coords[0]];
        }
    }

    // Fallback: Nominatim OpenStreetMap for local/dev env
    try {
        $n = HttpClient::get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);
        if ($n->successful() && isset($n->json()[0]['lat'])) {
            return ['lat' => floatval($n->json()[0]['lat']), 'lng' => floatval($n->json()[0]['lon'])];
        }
    } catch (\Exception $e) {
        \Log::warning('Fallback geocode failed: '.$e->getMessage());
    }

    return null;
}

        // public function setDefault(Request $request, $id)
        // {
        //     $user = Auth::user();
        //     // Set all addresses to not default
        //     $user->shippingAddresses()->update(['is_default' => false]);
        //     // Set selected address as default
        //     $user->shippingAddresses()->where('id', $id)->update(['is_default' => true]);

        //     // Flash selected_products to session
        //     session()->flash('selected_products', $request->input('selected_products', []));

        //     return redirect()->route('checkout.review')->with('success', 'Default Shipping Address updated!');
        // }

    public function edit($id)
    {
        $address = Auth::user()->shippingAddresses()->findOrFail($id);
        return view('user.shipping.edit', compact('address'));
    }

    public function destroy(Request $request, $id)
    {
        $address = Auth::user()->shippingAddresses()->findOrFail($id);
        $address->delete();

    // Persist selected_products to session so selection remains after reload
    session()->put('selected_products', $request->input('selected_products', []));

        return back()->with('success', 'Shipping Address deleted.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\StockIn;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
   
public function index(Request $request)
{
    $query = Products::where('admin_id', Auth::id());

    // Search by name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter by product type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Filter by unit
    if ($request->filled('unit')) {
        $query->where('unit', $request->unit);
    }

    // Filter by price range
    if ($request->filled('price')) {
        [$min, $max] = explode('-', $request->price);
        $query->whereBetween('price', [(float)$min, (float)$max]);
    }

    // Stock sorting
    if ($request->filled('stock')) {
        if ($request->stock === 'low-high') {
            $query->orderBy('stock_quantity', 'asc');
        } elseif ($request->stock === 'high-low') {
            $query->orderBy('stock_quantity', 'desc');
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    // Only show not deleted
    $products = $query->whereNull('deleted_at')
    ->paginate(50)
        ->appends($request->except('page'));

    // For filter dropdowns
    $units = Products::select('unit')->distinct()->pluck('unit');
    $types = Products::select('type')->distinct()->pluck('type');

    return view('admin.product.products', compact('products', 'units', 'types'));
}

    public function create()
    {
        return view('admin.product.addproducts');
    }

    public function store(Request $request)
    {
    $rules = [
        'name.*' => 'required|string|max:255',
        'type.*' => 'required|string|max:50',
        'type_custom.*' => 'nullable|string|max:50',
        'unit.*' => 'required|string',
        'unit_custom.*' => 'nullable|string|max:50',
        'price.*' => 'required|numeric|min:0',
        'stock.*' => 'nullable|integer|min:0',
        'description.*' => 'nullable|string|max:255',
        'image.*' => 'nullable|image|max:2048',
        'address.*' => 'nullable|string|max:255',
    ];

    $validator = Validator::make($request->all(), $rules);

    // Per-row check: if unit is 'others', require the corresponding unit_custom
    $units = $request->input('unit', []);
    $customs = $request->input('unit_custom', []);
    foreach ($units as $i => $u) {
        if ($u === 'others') {
            $val = isset($customs[$i]) ? trim($customs[$i]) : '';
            if ($val === '') {
                $validator->errors()->add("unit_custom.$i", "The custom unit is required for row " . ($i + 1) . ".");
            }
        }
    }

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    foreach ($request->name as $key => $name) {
        $product = new Products();
        $product->name = $name;
    // Prefer custom type if provided
    $customTypes = $request->input('type_custom', []);
    $customType = isset($customTypes[$key]) ? trim($customTypes[$key]) : null;
    $product->type = !empty($customType) ? $customType : $request->type[$key];
        	// Prefer custom unit if provided
        	$customUnits = $request->input('unit_custom', []);
        	$customUnit = isset($customUnits[$key]) ? trim($customUnits[$key]) : null;
        	$product->unit = !empty($customUnit) ? $customUnit : $request->unit[$key];
        $product->price = $request->price[$key];
    // Set stock if provided in the add-products modal; default to 0
    $stocks = $request->input('stock', []);
    $product->stock_quantity = isset($stocks[$key]) ? (int) $stocks[$key] : 0;
        $product->availability = 1;
        $product->description = $request->description[$key];

        // Address and geocoding
        $address = $request->address[$key] ?? null;
        $product->address = $address;
        if ($address) {
            $coords = $this->getLatLngFromAddress($address);
            if ($coords) {
                $product->latitude = $coords['lat'];
                $product->longitude = $coords['lng'];
            }
        }

        if ($request->hasFile('image') && $request->file('image')[$key]) {
            $product->image = $request->file('image')[$key]->store('products', 'public');
        }

        $product->admin_id = Auth::id();
        $product->save();

        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'Created Product',
            'details' => 'Product #' . $product->id . ' created',
            'timestamp' => now(),
        ]);
    }

    session()->flash('message', 'Product created successfully!');
    return redirect()->route('products.index')->with('message', 'Products added successfully!');
}
// Add Stock Method
public function addStock(Request $request, Products $product)
{
    $request->validate([
        'add_quantity' => 'required|integer|min:1',
    ]);

    $qty = (int)$request->input('add_quantity');

    // Create stock-in history record and increment product stock
    DB::transaction(function() use ($product, $qty) {
        StockIn::create([
            'product_id' => $product->id,
            'quantity' => $qty,
            'date_received' => now(),
            'remarks' => 'Added from Products Listing',
        ]);

        $product->increment('stock_quantity', $qty);
    });

    return redirect()->route('products.index')->with('message', 'Stock updated and recorded in inventory history!');
}

    public function show(string $id)
    {
        $product = Products::where('admin_id', Auth::id())->findOrFail($id);
        return view("admin.product.showproducts", compact('product'));
    }

    public function edit($id)
    {
        $product = Products::where('admin_id', Auth::id())->findOrFail($id);
        return response()->json($product);
    }


public function update(Request $request, $id)
{
    $product = Products::where('admin_id', Auth::id())->findOrFail($id);

    $rules = [
    'name' => 'required|string|max:255',
    'type' => 'required|string|max:50',
    'custom_type' => 'nullable|string|max:50',
    'unit' => 'required|string|max:50',
    'custom_unit' => 'nullable|string|max:50',
    'price' => 'required|numeric',
    'description' => 'nullable|string',
    'image' => 'nullable|image|max:1024',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($request->input('unit') === 'others') {
        if (!trim($request->input('custom_unit', ''))) {
            $validator->errors()->add('custom_unit', 'The custom unit is required when Others is selected.');
        }
    }

    if (trim(strtolower($request->input('type', ''))) === 'others') {
        if (!trim($request->input('custom_type', ''))) {
            $validator->errors()->add('custom_type', 'The custom type is required when Others is selected.');
        }
    }

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Prefer custom_type and custom_unit when provided
    $finalType = $request->custom_type && trim($request->custom_type) !== '' ? trim($request->custom_type) : $request->type;
    $finalUnit = $request->custom_unit && trim($request->custom_unit) !== '' ? trim($request->custom_unit) : $request->unit;

    $product->update([
    'name' => $request->name,
    'type' => $finalType,
    'unit' => $finalUnit,
    'price' => $request->price,
    'description' => $request->description,
    'image' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : $product->image,
]);

    \App\Models\ActivityLog::create([
        'user_admin' => Auth::user()->name ?? 'Unknown',
        'action' => 'Updated Product',
        'details' => 'Product #' . $product->id . ' updated',
        'timestamp' => now(),
    ]);
    return redirect()->route('products.index')->with('message', 'Product updated successfully');
}
    public function destroy(Products $product)
    {
        $product = Products::where('admin_id', Auth::id())->findOrFail($product->id);
        Storage::disk('public')->delete($product->image);
        $product->delete();

        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'Deleted Product',
            'details' => 'Product #' . $product->id . ' deleted',
            'timestamp' => now(),
        ]);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function inventory()
    {
        $products = Products::where('admin_id', Auth::id())->get();
        return view('admin.product.inventory', compact('products'));
    }

    public function forceDelete($id)
    {
        $product = Products::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return redirect()->route('products.archivedIndex')->with('success', 'Product permanently deleted.');
    }
    public function searchOtherProducts(Request $request, $adminId)
{
    $query = $request->input('q');
    $excludeId = $request->input('exclude');
    $products = \App\Models\Products::where('admin_id', $adminId)
        ->where('id', '!=', $excludeId)
        ->where('name', 'like', "%{$query}%")
        ->get();
    return response()->json($products->values());
}


//for customer
public function customerproducts(Request $request)
{

    $products = Products::where('availability', true)
        // ->where('stock_quantity', '>', 0) // Only show products with stock > 0
        ->when($request->has('unit') && !empty($request->unit), function($query) use ($request) {
            return $query->where('unit', 'like', '%' . $request->input('unit') . '%');
        })
        ->when($request->has('sort') && in_array($request->input('sort'), ['price', 'name', 'created_at']), function($query) use ($request) {
            // Adjust the allowed columns for sorting here
            return $query->orderBy($request->input('sort'), 'asc');
        })
    ->get();
    return view('user.products.index', compact('products'));
}


public function search(Request $request)
{
     $query = $request->input('query');

    // Retrieve products matching the query in name, description, or admin's name
    $products = Products::where('availability', true)
                        ->where(function($q) use ($query) {
                            $q->where('name', 'LIKE', '%' . $query . '%')
                              ->orWhere('description', 'LIKE', '%' . $query . '%')
                              ->orWhereHas('admin', function($q) use ($query) {
                                  $q->where('name', 'LIKE', '%' . $query . '%'); // Search by admin's name
                              });
                        })
                    ->paginate(50); // Example pagination

    return view('user.products.index', compact('products', 'query'));
}
public function getLatLngFromAddress($address)
{
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
        'address' => $address,
        'key' => $apiKey,
    ]);

    if ($response->successful() && isset($response['results'][0])) {
        $location = $response['results'][0]['geometry']['location'];
        return [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ];
    }

    return null;
}
public function bulkArchive(Request $request)
{
    $ids = $request->input('archive_ids', []);
    if (!empty($ids)) {
        // Example: Soft delete (if using SoftDeletes)
        Products::whereIn('id', $ids)->delete();

        // Log activity for each archived product
        foreach ($ids as $id) {
            \App\Models\ActivityLog::create([
                'user_admin' => Auth::user()->name ?? 'Unknown',
                'action' => 'Archived Product',
                'details' => 'Product #' . $id . ' archived',
                'timestamp' => now(),
            ]);
        }
    }
    // Redirect to archived products page after archiving
    return redirect()->route('products.archived.index')->with('message', 'Selected products archived.');
}

// Reduce stock for a product
public function reduceStock(Request $request, $id)
{
    $request->validate([
        'reduce_quantity' => 'required|integer|min:1',
    ]);
    $product = Products::findOrFail($id);
    $reduceQty = (int)$request->input('reduce_quantity');
    if ($reduceQty > $product->stock_quantity) {
        return back()->with('message', 'Cannot reduce more than available stock.');
    }
    // Record reduction as negative stock-in (so inventory history shows the change).
    DB::transaction(function() use ($product, $reduceQty) {
        StockIn::create([
            'product_id' => $product->id,
            'quantity' => -1 * $reduceQty,
            'date_received' => now(),
            'remarks' => 'Reduced from Products page',
        ]);

        $product->decrement('stock_quantity', $reduceQty);
    });

    return back()->with('message', 'Stock reduced and recorded in inventory history.');
}
}
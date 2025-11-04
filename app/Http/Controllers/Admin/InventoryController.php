<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\StockIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory items.
     */
    public function index(Request $request)
    {
        $query = Products::query();

        if ($q = $request->query('q')) {
            $query->where(function($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('type', 'like', "%{$q}%");
            });
        }

        // stock filters: low (<5) or out (=0)
        if ($filter = $request->query('stock_filter')) {
            if ($filter === 'low') {
                $query->where('stock_quantity', '<', 5)->where('stock_quantity', '>', 0);
            } elseif ($filter === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

    $items = $query->orderBy('name')->paginate(15);

        // Map fields expected by the view
        $items->getCollection()->transform(function($p) {
            $p->stock = $p->stock_quantity ?? 0;
            // ensure 'type' is available (product type)
            $p->type = $p->type ?? null;
            $p->status = ($p->blocklisted ?? false) ? 'unavailable' : ($p->status ?? 'available');
            $p->status_label = ucfirst($p->status);
            return $p;
        });

        // Additional aggregates for the inventory UI
        $products = Products::orderBy('name')->get();
        $totalProducts = $products->count();
        $totalStock = $products->sum('stock_quantity');
        $lowStockProducts = Products::where('stock_quantity', '<=', 5)->get();
        $stockIns = StockIn::with('product')->latest()->limit(50)->get();
        $stockInThisMonth = StockIn::whereBetween('date_received', [now()->startOfMonth(), now()->endOfMonth()])->sum('quantity');

        return view('admin.inventory.index', compact('items', 'products', 'totalProducts', 'totalStock', 'lowStockProducts', 'stockIns', 'stockInThisMonth'));
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'stock' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Products::create([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'unit' => $data['unit'] ?? null,
            'price' => $data['price'] ?? 0,
            'stock_quantity' => $data['stock'] ?? 0,
            'image' => $data['image'] ?? null,
            'blocklisted' => ($data['status'] ?? 'available') === 'unavailable',
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Products::findOrFail($id);
        // For now redirect back to index — edit UI not implemented yet
        return redirect()->route('admin.inventory.index')->with('error', 'Edit view not implemented yet.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Products::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'stock' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $item->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? $item->type,
            'unit' => $data['unit'] ?? $item->unit,
            'price' => $data['price'] ?? $item->price,
            'stock_quantity' => $data['stock'] ?? $item->stock_quantity,
            'image' => $data['image'] ?? $item->image,
            'blocklisted' => ($data['status'] ?? 'available') === 'unavailable',
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Add stock to product.
     */
    public function addStock(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'date_received' => 'nullable|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $item = Products::findOrFail($id);

        DB::transaction(function() use ($item, $data) {
            // record stock in history
            StockIn::create([
                'product_id' => $item->id,
                'quantity' => $data['amount'],
                'date_received' => $data['date_received'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            // increment product stock
            $item->increment('stock_quantity', $data['amount']);
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Stock added successfully.');
    }

    /**
     * Store stock-in using product_id from form (UI-friendly endpoint).
     */
    public function storeStock(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_added' => 'required|integer|min:1',
            'date_received' => 'nullable|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $item = Products::findOrFail($data['product_id']);

        DB::transaction(function() use ($item, $data) {
            StockIn::create([
                'product_id' => $item->id,
                'quantity' => $data['quantity_added'],
                'date_received' => $data['date_received'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            $item->increment('stock_quantity', $data['quantity_added']);
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Stock added successfully.');
    }

    /**
     * Reduce stock from product.
     */
    public function reduceStock(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $item = Products::findOrFail($id);
        $current = $item->stock_quantity ?? 0;
        $new = max(0, $current - $data['amount']);
        $item->stock_quantity = $new;
        $item->save();

        return redirect()->route('admin.inventory.index')->with('success', 'Stock reduced successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Products::findOrFail($id);
        // soft delete
        $item->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Item deleted.');
    }
}

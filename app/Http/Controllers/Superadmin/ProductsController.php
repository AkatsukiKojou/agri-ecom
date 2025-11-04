<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::query();

        // Filter blocklisted if requested
        if ($request->has('blocklist')) {
            $query->where('blocklisted', true);
        } else {
            $query->where('blocklisted', false);
        }

        // Search by admin name, product name, type, unit
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%")
                  ->orWhere('unit', 'like', "%$search%")
                  ->orWhere('id', $search)
                  ->orWhere('id', 'like', "%$search%")
                  ->orWhereHas('admin', function($adminQ) use ($search) {
                      $adminQ->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%")
                  ;});
            });
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
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $min = $request->input('min_price', 0);
            $max = $request->input('max_price', 9999999);
            $query->whereBetween('price', [$min, $max]);
        }

        $query->orderBy('created_at', 'desc');
    $products = $query->paginate(7)->appends($request->except('page'));

        $units = Products::select('unit')->distinct()->pluck('unit');
        $types = Products::select('type')->distinct()->pluck('type');

        return view('superadmin.products.index', compact('products', 'units', 'types'));
    }

    public function blocklist($id)
    {
        $product = Products::findOrFail($id);
        $product->blocklisted = true;
        $product->save();
        return redirect()->back()->with('success', 'Product blocklisted.');
    }

    public function unblocklist($id)
    {
        $product = Products::findOrFail($id);
        $product->blocklisted = false;
        $product->save();
        return redirect()->back()->with('success', 'Product removed from blocklist.');
    }
}

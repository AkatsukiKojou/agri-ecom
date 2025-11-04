<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class UserProductController extends Controller
{

public function index(Request $request)
{
    $query = Products::query();

    if ($request->filled('search')) {
        $searchTerm = $request->search;

        $query->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('category', 'like', "%{$searchTerm}%");
    }

    $products = $query->paginate(6);

    return view('user.products', compact('products'));
}
public function show($id)
{
    $product = Products::with('admin.profile', 'admin.products')->findOrFail($id);

    $otherProducts = $product->admin->products()
                        ->where('id', '!=', $product->id)
                        ->latest()
                        ->take(4)
                        ->get();

return view('user.products.show', compact('product', 'otherProducts'));
}
}

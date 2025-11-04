<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Service;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ArchiveController extends Controller
{
    // Archive (soft delete) product
  public function archived(Request $request)
{
    $query = $request->input('search');
    $archived = Products::onlyTrashed()
        ->when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->get();

    return view('admin.product.archived', compact('archived'));
}

    // View all archived products
    public function index()
    {
$archived = Products::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->get();
      return view('admin.product.archived', compact('archived'));
    }

    // Restore archived product
    public function restore($id)
    {
$product = Products::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->findOrFail($id);
                    $product->restore();

        return redirect()->route('products.archived.index')->with('message', 'Product restored successfully.');
    }
    // public function forceDelete($id)
    // {
    //     // Find the soft-deleted product
    //     $product = Products::onlyTrashed()
    //         ->where('admin_id', Auth::id())
    //         ->findOrFail($id);
    //     // Permanently delete the product
    //     $product->forceDelete();

    //     // Redirect back to the archived products page
    //     return redirect()->route('products.archivedIndex')->with('success', 'Product permanently deleted.');
    // }
    public function forceDelete($id)
{
    $product = Products::withTrashed()->findOrFail($id);
    $product->forceDelete();
    return redirect()->route('products.archived.index')->with('message', 'Product permanently deleted.');
}

    //for Services
    public function archives($id)
    {
          $service = Service::where('admin_id', Auth::id())->findOrFail($id);
        $service->delete();


        return redirect()->route('services.index')->with('message', 'Service archived successfully.');
    }

    // View all archived products
    public function indexs()
    {
    $archived = Service::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->get();
    return view('admin.services.archived', compact('archived'));
    }

    // Restore archived product
    public function restores($id)
    {
        $service = Service::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->findOrFail($id);
         $service->restore();

        return redirect()->route('services.archived.index')->with('message', 'Service restored successfully.');
    }
    
    public function forceDeletes($id)
    {
        // Find the soft-deleted product
        $service = Service::onlyTrashed()
            ->where('admin_id', Auth::id())
            ->findOrFail($id);
        // Permanently delete the product
        $service->forceDelete();

        // Redirect back to the archived products page
        return redirect()->route('services.archived.index')->with('success', 'Training Service permanently deleted.');
    }
    public function bulkArchive(Request $request)
{
    $ids = $request->input('archive_ids', []);
    if (!empty($ids)) {
        Products::whereIn('id', $ids)->update(['archived' => true]);
    }
    return redirect()->back()->with('message', 'Selected products archived.');
}
}

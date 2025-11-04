<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profile;
use App\Models\Products;
use App\Models\Service;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function SuperAdminDashboard()
    {
        return view('superadmin.dashboard');
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            return redirect('/login');
        }
    }
    public function SuperAdminLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Prevent browser back after logout
        return response()->redirectTo('/login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    //ManageUsers
     public function users(Request $request)
    {
        $query = User::where('role', 'user')->where('blocklisted', false);
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            return redirect('/login');
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $isNumeric = is_numeric($search);
            $query->where(function($q) use ($search, $isNumeric) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
                if ($isNumeric) {
                    $q->orWhere('id', $search);
                }
            });
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('superadmin.users.index', compact('users'));
    }
      // Show user details for superadmin
    public function showUser($id)
    {
        $user = User::with('profile')->findOrFail($id);
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/login');
        }
        return view('superadmin.users.show', compact('user'));
    }

    // Delete user for superadmin
    public function destroyUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/login');
        }
        $user->delete();
        return redirect()->route('superadmin.users')->with('success', 'User deleted successfully!');
    }
    // Blocklist user
    public function blocklistUser($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $user->blocklisted = true;
        $user->save();
    return redirect()->route('superadmin.users.blocklist')->with('success', 'User blocklisted and moved to blocklist.');
    }

    // Unblocklist user
    public function unblocklistUser($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $user->blocklisted = false;
        $user->save();
        return redirect()->back()->with('success', 'User removed from blocklist.');
    }

    // Blocklist view for users
    public function userBlocklistView(Request $request)
    {
        $query = User::where('role', 'user')->where('blocklisted', true)->with('profile');
        if ($request->filled('search')) {
            $search = $request->search;
            $isNumeric = is_numeric($search);
            $query->where(function($q) use ($search, $isNumeric) {
                // match on user fields
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('region', 'like', "%$search%")
                  ->orWhere('province', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%")
                  ->orWhere('barangay', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%");

                // match on related profile fields as well
                $q->orWhereHas('profile', function($profileQ) use ($search) {
                    $profileQ->where('region', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%")
                            ->orWhere('barangay', 'like', "%$search%")
                            ->orWhere('city', 'like', "%$search%")
                            ->orWhere('province', 'like', "%$search%");
                });

                if ($isNumeric) {
                    $q->orWhere('id', $search);
                }
            });
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->only('search'));
        return view('superadmin.users.blocklist', compact('users'));
    }

    // Manage admin
 
public function index(Request $request)
{
    $query = User::where('role', 'admin')->where('blocklisted', false)->with('profile');
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    if ($request->inactive === '1') {
        $query->where(function($q) use ($thirtyDaysAgo) {
            $q->whereNull('last_login_at')
              ->orWhere('last_login_at', '<', $thirtyDaysAgo);
        });
    }

    // Search by name, email, address fields or numeric ID
    if ($request->search) {
        $search = $request->search;
        $isNumeric = is_numeric($search);
        $query->where(function($q) use ($request, $search, $isNumeric) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhereHas('profile', function($profileQ) use ($search) {
                  $profileQ->where('barangay', 'like', '%' . $search . '%')
                           ->orWhere('city', 'like', '%' . $search . '%')
                           ->orWhere('province', 'like', '%' . $search . '%')
                           ->orWhere('region', 'like', '%' . $search . '%');
              });
            if ($isNumeric) {
                $q->orWhere('id', $search);
            }
        });
    }

    $admins = $query->paginate(10);

    return view('superadmin.manageadmins.index', compact('admins'));
}

public function create()
{
    return view('superadmin.manageadmins.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);
        // Normalize email
        $email = strtolower(trim($request->email));

        // Check for existing user by email (defensive - DB unique index also protects us)
        $existing = \App\Models\User::where('email', $email)->first();
        if ($existing) {
            // If user exists but isn't an admin, optionally convert role or notify. For now, return with message.
            return redirect()->back()->withInput()->with('warning', 'A user with that email already exists.');
        }

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
            'status' => 'active',
        ]);

        return redirect()->route('manageadmins.index')->with('success', 'Admin added successfully!');
}
  public function show($id)
    {
    $admin = User::where('role', 'admin')->with('profile')->findOrFail($id);
    $admin->products_count = $admin->products()->count();
    $admin->services_count = $admin->services()->count();
    $admin->profile_followers_count = $admin->profileFollowers()->count();
    $admin->profile_likes_count = $admin->profileLikes()->count();
    return view('superadmin.manageadmins.show', compact('admin'));
    }

    public function edit($id)
    {
        $admin = User::where('role', 'admin')->with('profile')->findOrFail($id);
        return view('superadmin.manageadmins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->update($request->only(['name', 'email']));
        if ($admin->profile) {
            $admin->profile->update($request->only(['barangay', 'city', 'province', 'region', 'phone_number']));
        }
        return redirect()->route('manageadmins.index')->with('success', 'Admin updated successfully!');
    }

    public function destroy($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();
        return redirect()->route('manageadmins.index')->with('success', 'Admin deleted successfully!');
    }
    // Update admin password
    public function updateAdminPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->password = bcrypt($request->password);
        $admin->save();
        return redirect()->route('manageadmins.show', $id)->with('success', 'Password updated successfully!');
    }



  // Blocklist admin
    public function blocklistAdmin($id)
    {
    $admin = User::where('role', 'admin')->findOrFail($id);
    $admin->blocklisted = true;
    $admin->save();
    return redirect()->route('manageadmins.index')->with('success', 'Admin blocklisted and moved to blocklist.');
    }

    // Unblocklist admin
    public function unblocklistAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->blocklisted = false;
        $admin->save();
        return redirect()->back()->with('success', 'Admin removed from blocklist.');
    }

    // Blocklist view
    public function blocklistView(Request $request)
    {
        $query = User::where('role', 'admin')->where('blocklisted', true)->with('profile');
        if ($request->filled('search')) {
            $search = $request->search;
            $isNumeric = is_numeric($search);
            $query->where(function($q) use ($search, $isNumeric) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('profile', function($profileQ) use ($search) {
                      $profileQ->where('farm_name', 'like', "%$search%")
                               ->orWhere('address', 'like', "%$search%")
                               ->orWhere('barangay', 'like', "%$search%")
                               ->orWhere('city', 'like', "%$search%")
                               ->orWhere('province', 'like', "%$search%");
                  });
                if ($isNumeric) {
                    $q->orWhere('id', $search);
                }
            });
        }
        $admins = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->only('search'));
        return view('superadmin.manageadmins.blocklist', compact('admins'));
    }





    // MAnageProducts

public function products(Request $request)
{
    $query = Products::with(['owner.profile']);

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    if ($request->type) {
        $query->where('type', $request->type);
    }
    if ($request->unit) {
        $query->where('unit', $request->unit);
    }
    if ($request->min_price) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->max_price) {
        $query->where('price', '<=', $request->max_price);
    }

    $products = $query->paginate(10);

    // For filter dropdowns
    $types = Products::select('type')->distinct()->pluck('type')->filter()->values();
    $units = Products::select('unit')->distinct()->pluck('unit')->filter()->values();

    return view('superadmin.products.index', compact('products', 'types', 'units'));
}
public function showProduct($id)
{
    $product = Products::with('owner')->findOrFail($id);
    return view('superadmin.products.show', compact('product'));
}

public function createProduct()
{
    return view('superadmin.products.create');
}
public function storeProduct(Request $request)
{
    $request->validate([
        'name' => 'required',
        'category' => 'required',
        'description' => 'required',
        'image' => 'nullable|image|max:2048',
    ]);
    $data = $request->only(['name', 'category', 'description']);
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }
    $data['owner_id'] = Auth::id(); // or select owner
    $data['status'] = 'active';
    Products::create($data);
    return redirect()->route('superadmin.products')->with('success', 'Product added!');
}
public function editProduct($id)
{
    $product = Products::findOrFail($id);
    return view('superadmin.products.edit', compact('product'));
}
public function updateProduct(Request $request, $id)
{
    $product = Products::findOrFail($id);
    $request->validate([
        'name' => 'required',
        'category' => 'required',
        'description' => 'required',
        'image' => 'nullable|image|max:2048',
    ]);
    $data = $request->only(['name', 'category', 'description']);
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }
    $product->update($data);
    return redirect()->route('superadmin.products')->with('success', 'Product updated!');
}
public function productsAnalytics()
{
    $total = Products::count();
    $active = Products::where('status', 'active')->count();
    $blocked = Products::where('status', 'blocked')->count();
    $topCategories = Products::select('category')->groupBy('category')->orderByRaw('COUNT(*) DESC')->limit(5)->pluck('category');
    return view('superadmin.products.analytics', compact('total', 'active', 'blocked', 'topCategories'));
}
   
    // Soft delete (block) a product
    public function destroyProduct($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        return redirect()->route('superadmin.products')->with('success', 'Product blocked and moved to blocklist!');
    }





    // Manage Services
    public function services(Request $request)
    {
        $query = Service::query();
        // Search by category, service name, or unit
        if ($request->filled('search')) {
            $search = $request->search;
            $isNumeric = is_numeric($search);
            $query->where(function($q) use ($search, $isNumeric) {
                                $q->where('service_name', 'like', "%$search%")
                                    ->orWhere('unit', 'like', "%$search%");
                                if ($isNumeric) {
                                    // allow searching by numeric ID
                                    $q->orWhere('id', $search);
                                }
            });
        }
        // Filter by category
        if ($request->filled('category')) {
            // category filter intentionally left as-is; services are categorized by service_name
        }
        // Filter by unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('superadmin.services.index', compact('services'));
    }

    // Block a service (move to blocklist)
    public function blockService($id)
    {
    $service = Service::findOrFail($id);
    $service->delete(); // Soft delete
    return redirect()->route('superadmin.services')->with('success', 'Service blocklisted!');
    }

    // Blocklist view for services
    public function blocklistServices(Request $request)
    {
        $query = Service::onlyTrashed();
        if ($request->filled('search')) {
            $search = $request->search;
            $isNumeric = is_numeric($search);
            $query->where(function($q) use ($search, $isNumeric) {
                $q->where('service_name', 'like', "%$search%")
                  ->orWhere('unit', 'like', "%$search%");
                if ($isNumeric) {
                    $q->orWhere('id', $search);
                }
            });
        }
        $services = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->only('search'));
        return view('superadmin.services.blocklist', compact('services'));
    }

       // Restore a soft-deleted service
    public function restoreService($id)
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();
        return redirect()->route('superadmin.services.blocklist')->with('success', 'Service restored!');
    }



    // Report and Analytics

public function reports()
{
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return redirect('/login');
        }
    // User stats
    $totalUsers = User::count();
    $totalAdmins = User::where('role', 'admin')->count();
    $totalFarmers = User::where('role', 'farmer')->count();
    $totalBuyers = User::where('role', 'user')->count();

    // New users per month (last 6 months)
    $userGrowth = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('count', 'month');

    // Product stats
    $totalProducts = Products::count();
    $productsPerType = Products::select('type', DB::raw('count(*) as total'))
        ->groupBy('type')->pluck('total', 'type');
    $outOfStock = Products::where('stock_quantity', '<=', 0)->count();

    // Top 5 products (by stock or price as example)
    $topProducts = Products::orderBy('stock_quantity', 'desc')->take(9)->get();

    // Inactive admins (no login in 30 days)
    $inactiveAdmins = User::where('role', 'admin')
        ->where(function($q) {
            $q->whereNull('last_login_at')
              ->orWhere('last_login_at', '<', Carbon::now()->subDays(30));
        })->count();

    // Top sellers (admins/LSA with most product sales)
    $topSellers = User::where('role', 'admin')
        ->withCount(['products as products_sold' => function($query) {
            $query->join('order_items', 'products.id', '=', 'order_items.product_id');
        }])
        ->orderByDesc('products_sold')
        ->take(5)
        ->get();

    // Top service bookers (admins/LSA with most booked services)
    $topServiceBookers = User::where('role', 'admin')
        ->withCount(['bookings as services_booked'])
        ->orderByDesc('services_booked')
        ->take(5)
        ->get();

    // Service stats
    $totalServices = \App\Models\Service::count();
    $servicesPerType = \App\Models\Service::select('service_name', DB::raw('count(*) as total'))
        ->groupBy('service_name')
        ->pluck('total', 'service_name');
    $unavailableServices = \App\Models\Service::where('is_available', 0)->count();
    $topServices = \App\Models\Service::withCount('bookings')->orderByDesc('bookings_count')->take(9)->get();

    return view('superadmin.reports.index', compact(
        'totalUsers', 'totalAdmins', 'totalFarmers', 'totalBuyers', 'userGrowth', 'totalProducts', 'productsPerType', 'outOfStock', 'topProducts', 'inactiveAdmins', 'topSellers', 'topServiceBookers', 'totalServices', 'servicesPerType', 'unavailableServices', 'topServices'
    ));
}


  
}

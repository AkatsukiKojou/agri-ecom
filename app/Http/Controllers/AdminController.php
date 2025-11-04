<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Products;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function AdminDashboard()
    {
        $adminId = Auth::id();

        // Total Products and Services
        $totalProducts = Products::where('admin_id', $adminId)->count();
        $totalServices = Service::where('admin_id', $adminId)->count();

        // Total Bookings (for services owned by this admin)
        $totalBookings = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.admin_id', $adminId)
            ->count();

        // Total Orders (all orders for products owned by this admin)
        $totalOrders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->distinct('orders.id')
            ->count('orders.id');


        // Product Sales (sum of all approved orders for this admin's products)

        // Product Sales (sum of all completed orders for this admin's products)
        $productSales = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_items.price * order_items.quantity'));

        // Service Sales (sum of all approved bookings for this admin's services)

        // Service Sales (sum of all completed bookings for this admin's services)
        $serviceSales = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.admin_id', $adminId)
            ->where('bookings.status', 'completed')
            ->sum('bookings.total_price');

        // Total Sales (products + services)
        $totalSales = $productSales + $serviceSales;

        // Monthly Sales Data (for chart)
        // Build a 12-element array (Jan..Dec) combining product order totals and service booking totals
        $salesData = array_fill(0, 12, 0);

        // Product monthly totals (approved orders)
        $productMonthly = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->where('orders.status', 'approved')
            ->whereYear('orders.created_at', now()->year)
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.price * order_items.quantity) as total')
            ->groupBy('month')
            ->get();

        foreach ($productMonthly as $sale) {
            $index = (int)$sale->month - 1;
            $salesData[$index] += (float) round($sale->total, 2);
        }

        // Service monthly totals (completed bookings)
        $serviceMonthly = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.admin_id', $adminId)
            ->where('bookings.status', 'completed')
            ->whereYear('bookings.created_at', now()->year)
            ->selectRaw('MONTH(bookings.created_at) as month, SUM(bookings.total_price) as total')
            ->groupBy('month')
            ->get();

        foreach ($serviceMonthly as $sale) {
            $index = (int)$sale->month - 1;
            $salesData[$index] += (float) round($sale->total, 2);
        }



// Booking Events for Calendar (show only ongoing bookings for this admin's services)
            
$bookingEvents = DB::table('bookings')
    ->join('services', 'bookings.service_id', '=', 'services.id')
    ->where('services.admin_id', $adminId)
    ->where('bookings.status', 'ongoing')
    ->select(
        'bookings.booking_start',
        'bookings.booking_end',
        'services.service_name as service_name'
    )
    ->get()
    ->map(function($booking) {
        return [
            'title' => $booking->service_name ?? 'Booking',
            'start' => $booking->booking_start ? date('Y-m-d', strtotime($booking->booking_start)) : null,
            'end'   => $booking->booking_end ? date('Y-m-d', strtotime($booking->booking_end)) : null,
        ];
    })
    ->filter(function($event) {
        return !empty($event['start']);
    })
    ->values();

// ...existing code...

        // Latest 5 Approved Orders for this admin's products
        $latestOrders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->where('orders.status', 'approved')
            ->select('orders.id', 'orders.name', 'orders.total_price', 'orders.created_at')
            ->distinct()
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        $salesLabels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        // Count only users with role 'user'
        $totalUsers = User::where('role', 'user')->count();

        // User Growth: new users with role 'user' this month
        $userGrowth = User::where('role', 'user')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Top Product: product with the most sales (by quantity) for this admin
        $topProductRow = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();
        $topProduct = $topProductRow ? $topProductRow->name . ' (' . $topProductRow->total_qty . ' sold)' : 'N/A';

            // Total Buyers: unique users who bought products or booked services for this admin
            $productBuyerIds = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.admin_id', $adminId)
                ->whereNotNull('orders.user_id')
                ->pluck('orders.user_id')->unique();

            $serviceBuyerIds = DB::table('bookings')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('services.admin_id', $adminId)
                ->whereNotNull('bookings.user_id')
                ->pluck('bookings.user_id')->unique();

            $totalBuyers = $productBuyerIds->merge($serviceBuyerIds)->unique()->count();
                return view('admin.dashboard', compact(
                    'totalProducts',
                    'totalServices',
                    'totalBookings',
                    'totalOrders',
                    'productSales',
                    'serviceSales',
                    'totalSales',
                    'salesData',
                    'salesLabels',
                    'latestOrders',
                    'bookingEvents',
                    'totalUsers',
                    'userGrowth',
                    'topProduct',
                    'totalBuyers'
                ));
    }



    public function dashboard()
    {
        $adminId = Auth::id();

        // Followers
        $followers = DB::table('profile_follower')
            ->where('followed_id', $adminId)
            ->join('users', 'profile_follower.follower_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'profile_follower.created_at')
            ->get();

        // Likes
        $likes = DB::table('profile_likes')
            ->where('profile_id', $adminId)
            ->join('users', 'profile_likes.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'profile_likes.created_at')
            ->get();

    // Merge and sort by date
    $notifications = collect();

    foreach ($followers as $f) {
        $notifications->push([
            'type' => 'follow',
            'user_id' => $f->id,
            'message' => "{$f->name} followed you",
            'created_at' => $f->created_at,
        ]);
    }
    foreach ($likes as $l) {
        $notifications->push([
            'type' => 'like',
            'user_id' => $l->id,
            'message' => "{$l->name} liked your profile",
            'created_at' => $l->created_at,
        ]);
    }

    $notifications = $notifications->sortByDesc('created_at')->take(10);

    return view('admin.dashboard', compact('notifications'));
}
public function sales()
{
    // You can add logic here to show sales details or reports
    return view('admin.sales'); // Make sure you have a resources/views/admin/sales.blade.php
}
}
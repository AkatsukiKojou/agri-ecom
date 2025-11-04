<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    
    public function index()
    {


        $adminId = Auth::id();
        // Income from completed orders: sum of (price * quantity) for order items of this admin's products where the parent order status is 'completed'
        $completed_income = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.admin_id', $adminId)
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_items.price * order_items.quantity'));

        // Sales: total sales (price * quantity) for order items of this admin's products
        $sales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->sum(DB::raw('order_items.price * order_items.quantity'));

        // Orders: count unique orders for this admin's products
        $orders = \App\Models\OrderItem::whereHas('product', function($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        })->distinct('order_id')->count('order_id');

        $bookings = \App\Models\Booking::whereHas('service', function($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        })->count();
        $products = \App\Models\Products::where('admin_id', $adminId)->count();
        $services = \App\Models\Service::where('admin_id', $adminId)->count();
        $revenue = $sales;


        // Products Completed: count of order items for this admin's products where the parent order status is 'completed'
        $products_completed = \App\Models\OrderItem::whereHas('product', function($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        })->whereHas('order', function($q) {
            $q->where('status', 'completed');
        })->count();


    // Services Completed: count of bookings for this admin's services where status is 'completed' only
    $services_completed = \App\Models\Booking::whereHas('service', function($q) use ($adminId) {
        $q->where('admin_id', $adminId);
    })->where('status', 'completed')->count();

    // Total income from completed services (only 'completed' status)
    $services_completed_income = \App\Models\Booking::whereHas('service', function($q) use ($adminId) {
        $q->where('admin_id', $adminId);
    })->where('status', 'completed')->sum('total_price');

    // Top Products Sold: list of products with most sales (by quantity) for this admin
    $topProducts = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('products.admin_id', $adminId)
        ->where('orders.status', 'completed');

                if ($dateFrom) {
                    $topProducts->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $topProducts->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($search) {
                    $s = $search;
                    $topProducts->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }

                $topProducts = $topProducts
                    ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'))
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('total_qty')
                    ->limit(5)
                    ->get();
        
    // Top Services Booked: list of services with most bookings (by count) for this admin, status approved or completed
    $topServices = DB::table('bookings')
        ->join('services', 'bookings.service_id', '=', 'services.id')
        ->where('services.admin_id', $adminId)
        ->where('bookings.status', 'completed')
    ->select('services.service_name', DB::raw('COUNT(bookings.id) as total_booked'))
    ->groupBy('services.id', 'services.service_name')
        ->orderByDesc('total_booked')
        ->limit(5)
        ->get();

    return view('admin.reports.index', compact('sales', 'orders', 'bookings', 'products', 'services', 'revenue', 'products_completed', 'services_completed', 'completed_income', 'services_completed_income', 'topProducts', 'topServices'));
    }



    // For Products Report

        public function productsreport (Request $request) {
            $adminId = Auth::id();

            // Validate and normalize date inputs (optional)
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:1970|max:2100'
            ]);

            $dateFrom = $validated['date_from'] ?? null;
            $dateTo = $validated['date_to'] ?? null;
            $month = $validated['month'] ?? null;
            $year = $validated['year'] ?? null;
            $search = $request->input('search');
            $status = $request->input('status');

            // Normalize status filter: some parts of the app store 'rejected' while UI sends 'reject'
            // We'll convert the incoming status into an array of acceptable DB values to use with whereIn.
            $statusVariants = null;
            if ($status) {
                if ($status === 'reject' || $status === 'rejected') {
                    $statusVariants = ['reject', 'rejected'];
                } else {
                    $statusVariants = [$status];
                }
            }

            // If month is provided and dates are empty, compute first and last day of that month (given year or current year)
            if ($month && !$dateFrom && !$dateTo) {
                $year = $year ?? date('Y');
                $dateFrom = date('Y-m-d', strtotime("{$year}-{$month}-01"));
                // compute last day
                $dateTo = date('Y-m-d', strtotime("{$dateFrom} +1 month -1 day"));
            }

            // If month is NOT provided but a year IS provided, treat the filter as the whole year (Jan 1 to Dec 31)
            // If year is null (user chose "All Years"), leave dateFrom/dateTo null to indicate no time restriction
            elseif (!$month && $year) {
                $dateFrom = date('Y-m-d', strtotime("{$year}-01-01"));
                $dateTo = date('Y-m-d', strtotime("{$year}-12-31"));
            }

            // If only one bound provided, keep the other null. If date_from > date_to, swap them.
            if ($dateFrom && $dateTo && strtotime($dateFrom) > strtotime($dateTo)) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }
                // Get all products for this admin
                $products = \App\Models\Products::where('admin_id', $adminId)->get();

                // Total products count
                $totalProducts = $products->count();

                // Total product sales (sum of price * quantity for order items of this admin's products)
                // We want the reported charts/tables to respect the status filter, but the displayed
                // "Total Product Sales" should always reflect completed orders only (business rule).
                // Build a base query for order_items joined to products/orders
                $totalProductSalesBaseQuery = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('products.admin_id', $adminId);
                

                // Apply date/search bounds to the base query (used for both filtered and completed-only totals)
                // Apply date/search bounds to the base query (used for both filtered and completed-only totals)
                // NOTE: use orders.created_at for date filtering so totals align with the rest of the report
                $applyBounds = function($query) use ($dateFrom, $dateTo, $search) {
                    if ($dateFrom) {
                        $query->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                    }
                    if ($dateTo) {
                        $query->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                    }
                    if ($search) {
                        $s = $search;
                        $query->where(function($q) use ($s) {
                            $q->where('products.name', 'like', "%{$s}%")
                              ->orWhere('products.type', 'like', "%{$s}%")
                              ->orWhere('products.unit', 'like', "%{$s}%")
                              ->orWhere('orders.name', 'like', "%{$s}%");
                            if (is_numeric($s)) {
                                $q->orWhere('order_items.price', $s);
                            }
                        });
                    }
                };

                // 1) Compute totalProductSalesCompleted: always sum completed orders only (respecting date/search bounds)
                $totalProductSalesCompletedQuery = (clone $totalProductSalesBaseQuery);
                $applyBounds($totalProductSalesCompletedQuery);
                $totalProductSalesCompletedQuery->where('orders.status', 'completed');
                $totalProductSales = $totalProductSalesCompletedQuery->sum(DB::raw('order_items.price * order_items.quantity'));

                // 2) For other parts of the report that should respect the UI status filter, build a filtered total query if needed
                // (we keep existing behavior where charts/tables pick up the status filter via $statusVariants)

                // Number of unique orders for this admin's products
                $orderQuery = \App\Models\OrderItem::whereHas('product', function($q) use ($adminId) {
                    $q->where('admin_id', $adminId);
                });

                if ($dateFrom) {
                    $orderQuery->whereHas('order', function($q) use ($dateFrom) {
                        $q->where('created_at', '>=', $dateFrom . ' 00:00:00');
                    });
                }
                if ($dateTo) {
                    $orderQuery->whereHas('order', function($q) use ($dateTo) {
                        $q->where('created_at', '<=', $dateTo . ' 23:59:59');
                    });
                }

                if ($status) {
                    $orderQuery->whereHas('order', function($q) use ($statusVariants) {
                        if (count($statusVariants) > 1) {
                            $q->whereIn('status', $statusVariants);
                        } else {
                            $q->where('status', $statusVariants[0]);
                        }
                    });
                }

                if ($search) {
                    $s = $search;
                    $orderQuery->where(function($q) use ($s) {
                        $q->whereHas('product', function($q2) use ($s) {
                            $q2->where('name', 'like', "%{$s}%")
                               ->orWhere('type', 'like', "%{$s}%")
                               ->orWhere('unit', 'like', "%{$s}%");
                        });
                        $q->orWhereHas('order', function($q2) use ($s) {
                            $q2->where('name', 'like', "%{$s}%");
                        });
                        if (is_numeric($s)) {
                            $q->orWhere('price', $s);
                        }
                    });
                }

                $numberOfOrders = $orderQuery->distinct('order_id')->count('order_id');

                // Top buyer (by quantity) for this admin's products
                $topBuyerQuery = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.admin_id', $adminId)
                ;

                if ($dateFrom) {
                    $topBuyerQuery->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $topBuyerQuery->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($status) {
                    if (count($statusVariants) > 1) {
                        $topBuyerQuery->whereIn('orders.status', $statusVariants);
                    } else {
                        $topBuyerQuery->where('orders.status', $statusVariants[0]);
                    }
                }

                if ($search) {
                    $s = $search;
                    $topBuyerQuery->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }

                $topBuyerRow = $topBuyerQuery
                    ->select('orders.user_id', 'orders.name', DB::raw('SUM(order_items.quantity) as total_qty'))
                    ->groupBy('orders.user_id', 'orders.name')
                    ->orderByDesc('total_qty')
                    ->first();

                $topBuyer = $topBuyerRow ? $topBuyerRow->name : null;
                // also expose the top buyer id and total quantity so the view can show more context
                $topBuyerId = $topBuyerRow ? $topBuyerRow->user_id : null;
                $topBuyerQty = $topBuyerRow ? (int) $topBuyerRow->total_qty : 0;

                // Chart: sales by product name (completed orders)
                $productChartQuery = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('products.admin_id', $adminId);
                // apply default 'completed' when no explicit status filter provided
                if (!$status) {
                    $productChartQuery->where('orders.status', 'completed');
                }

                if ($dateFrom) {
                    $productChartQuery->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $productChartQuery->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($status) {
                    if (count($statusVariants) > 1) {
                        $productChartQuery->whereIn('orders.status', $statusVariants);
                    } else {
                        $productChartQuery->where('orders.status', $statusVariants[0]);
                    }
                }

                if ($search) {
                    $s = $search;
                    $productChartQuery->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }

                $productChart = $productChartQuery
                    ->select('products.id', 'products.name', 'products.type', 'products.unit', 'products.price', DB::raw('SUM(order_items.quantity) as total_qty'))
                    ->groupBy('products.id', 'products.name', 'products.type', 'products.unit', 'products.price')
                    ->orderByDesc('total_qty')
                    ->get();

                $productLabels = $productChart->pluck('name');
                $productData = $productChart->pluck('total_qty');

                // Top products sold (first 5 from the productChart collection)
                $topProductsSold = $productChart->take(5);

                // Chart: sales by product TYPE (completed orders) - preferred for product-type chart
                $productTypeChartQuery = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('products.admin_id', $adminId);
                if (!$status) {
                    $productTypeChartQuery->where('orders.status', 'completed');
                }

                if ($dateFrom) {
                    $productTypeChartQuery->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $productTypeChartQuery->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($status) {
                    if (count($statusVariants) > 1) {
                        $productTypeChartQuery->whereIn('orders.status', $statusVariants);
                    } else {
                        $productTypeChartQuery->where('orders.status', $statusVariants[0]);
                    }
                }

                if ($search) {
                    $s = $search;
                    $productTypeChartQuery->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }

                $productTypeChart = $productTypeChartQuery
                    ->select(DB::raw("COALESCE(products.type, 'Unspecified') as type"), DB::raw('SUM(order_items.quantity) as total_qty'))
                    ->groupBy('products.type')
                    ->orderByDesc('total_qty')
                    ->get();

                $productTypeLabels = $productTypeChart->pluck('type');
                $productTypeData = $productTypeChart->pluck('total_qty');

                // Chart: order status distribution for this admin's product orders
                $statusChartQuery = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.admin_id', $adminId)
                ;

                if ($dateFrom) {
                    $statusChartQuery->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $statusChartQuery->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($search) {
                    $s = $search;
                    $statusChartQuery->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }

                if ($status) {
                    if (count($statusVariants) > 1) {
                        $statusChartQuery->whereIn('orders.status', $statusVariants);
                    } else {
                        $statusChartQuery->where('orders.status', $statusVariants[0]);
                    }
                }

                $statusChartRows = $statusChartQuery
                    ->select('orders.status', DB::raw('COUNT(DISTINCT orders.id) as total'))
                    ->groupBy('orders.status')
                    ->get();

                $statusLabels = $statusChartRows->pluck('status');
                $statusData = $statusChartRows->pluck('total');

                // Recent order items for admin products (to populate table)
                $recentQuery = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('products.admin_id', $adminId)
                    ->select(
                        'products.name as product_name',
                        'products.type as product_type',
                        'products.unit as unit',
                        'order_items.price as price',
                        'order_items.quantity as quantity',
                        'orders.name as buyer_name',
                        'orders.created_at as order_date',
                        'orders.status as status'
                    )
                    ;

                    if ($dateFrom) {
                    $recentQuery->where('orders.created_at', '>=', $dateFrom . ' 00:00:00');
                }
                if ($dateTo) {
                    $recentQuery->where('orders.created_at', '<=', $dateTo . ' 23:59:59');
                }

                if ($search) {
                    $s = $search;
                    $recentQuery->where(function($q) use ($s) {
                        $q->where('products.name', 'like', "%{$s}%")
                          ->orWhere('products.type', 'like', "%{$s}%")
                          ->orWhere('products.unit', 'like', "%{$s}%")
                          ->orWhere('orders.name', 'like', "%{$s}%");
                        if (is_numeric($s)) {
                            $q->orWhere('order_items.price', $s);
                        }
                    });
                }
                if ($status) {
                    if (count($statusVariants) > 1) {
                        $recentQuery->whereIn('orders.status', $statusVariants);
                    } else {
                        $recentQuery->where('orders.status', $statusVariants[0]);
                    }
                }

                // If PDF export requested, fetch ALL matching recent items (no pagination) so the PDF contains full table
                $recentOrderItemsAll = null;
                if ($request->input('export') === 'pdf') {
                    $recentOrderItemsAll = (clone $recentQuery)->orderByDesc('orders.created_at')->get();
                }

                // paginate recent items for web view
                $recentOrderItems = $recentQuery->orderByDesc('orders.created_at')->paginate(15)->withQueryString();

                $viewData = compact(
                    'products',
                    'totalProducts',
                    'totalProductSales',
                    'numberOfOrders',
                    'topBuyer',
                    'topBuyerId',
                    'topBuyerQty',
                    'productLabels',
                    'productData',
                    'productTypeLabels',
                    'productTypeData',
                    'statusLabels',
                    'statusData',
                    'recentOrderItems',
                    'topProductsSold'
                );

                // include filter bounds in the view data
                $viewData = array_merge($viewData, ['date_from' => $dateFrom, 'date_to' => $dateTo, 'month' => $month, 'year' => $year]);

                // If export=pdf is requested, generate and return a PDF download using barryvdh/laravel-dompdf
                if ($request->input('export') === 'pdf') {
                    // Use a simplified PDF-only blade so the PDF contains only the summary and products table
                    $viewData['recentOrderItemsAll'] = $recentOrderItemsAll;
                    $html = view('admin.reports.productsreport_pdf', $viewData)->render();
                    $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
                    return $pdf->download('products-report.pdf');
                }

                return view('admin.reports.productsreport', $viewData);
        }






        // for Services Report
        public function servicesreport (Request $request) {
            $adminId = Auth::id();

            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:1970|max:2100',
                'q' => 'nullable|string',
                'status' => 'nullable|string'
            ]);

            $dateFrom = $validated['date_from'] ?? null;
            $dateTo = $validated['date_to'] ?? null;
            $month = $validated['month'] ?? null;
            $year = $validated['year'] ?? null;

            if ($month && !$dateFrom && !$dateTo) {
                $year = $year ?? date('Y');
                $dateFrom = date('Y-m-d', strtotime("{$year}-{$month}-01"));
                $dateTo = date('Y-m-d', strtotime("{$dateFrom} +1 month -1 day"));
            } elseif (!$month && $year) {
                $dateFrom = date('Y-m-d', strtotime("{$year}-01-01"));
                $dateTo = date('Y-m-d', strtotime("{$year}-12-31"));
            }

            if ($dateFrom && $dateTo && strtotime($dateFrom) > strtotime($dateTo)) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }

            $query = \App\Models\Booking::with(['service', 'user'])
                ->whereHas('service', function($q) use ($adminId) {
                    $q->where('admin_id', $adminId);
                });

            // Filter by booking_start bounds (the booking date), not created_at
            if ($dateFrom) {
                $query->where('booking_start', '>=', $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $query->where('booking_start', '<=', $dateTo . ' 23:59:59');
            }
            if ($request->filled('q')) {
                $q = $request->input('q');
                $query->where(function($sub) use ($q) {
                    $sub->where('service_name', 'like', "%{$q}%")
                        ->orWhereHas('service', function($s) use ($q) {
                            $s->where('service_name', 'like', "%{$q}%")
                              ->orWhere('unit', 'like', "%{$q}%");
                        })
                        ->orWhereHas('user', function($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%");
                        })
                        ->orWhere('booked_by_name', 'like', "%{$q}%");
                });
            }

            if ($request->filled('status')) {
                $statusInput = $request->input('status');
                // map common UI values to DB-stored enum values
                // DB stores values like: 'pending', 'approved', 'rejected', 'cancelled', 'no show', 'completed', 'ongoing'
                if ($statusInput === 'no_show') {
                    $status = 'no show';
                } elseif ($statusInput === 'reject') {
                    $status = 'rejected';
                } else {
                    // pass-through for values that already match the DB (pending, approved, completed, cancelled, ongoing, etc.)
                    $status = $statusInput;
                }
                $query->where('status', $status);
            }

            // totals and aggregates
            $totalBookings = (clone $query)->count();
            // Keep original (all statuses) total if needed elsewhere
            $totalBookingSalesAll = (clone $query)->sum('total_price');
            // Compute completed-only booking sales (only bookings with status 'completed')
            $totalBookingSalesCompleted = (clone $query)->where('status', 'completed')->sum('total_price');
            // For the report display we prefer showing only completed bookings income
            $totalBookingSales = $totalBookingSalesCompleted;

            $totalServices = \App\Models\Service::where('admin_id', $adminId)->count();

            // Top service (alias service_name -> name so view can use $topService->name)
            $topService = DB::table('bookings')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('services.admin_id', $adminId)
                // select name, unit, price and booking count so view can show unit and price
                ->select('services.service_name as name', 'services.unit', 'services.price', DB::raw('COUNT(bookings.id) as total_booked'))
                ->groupBy('services.id', 'services.service_name', 'services.unit', 'services.price')
                ->orderByDesc('total_booked')
                ->first();

            // Top booker (user who made the most bookings under this admin's services)
            // Join users so we can reliably use the registered user's name when available,
            // otherwise fall back to bookings.full_name (guest bookings). The bookings table
            // does not have booked_by_name/contact_no/reference_no columns (see migration),
            // so use existing columns: users.name and bookings.full_name.
            $topBookerQuery = DB::table('bookings')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->leftJoin('users', 'bookings.user_id', '=', 'users.id')
                ->where('services.admin_id', $adminId)
                ->select('bookings.user_id', DB::raw('COALESCE(users.name, bookings.full_name, bookings.user_id) as name'), DB::raw('COUNT(bookings.id) as total_bookings'))
                ->groupBy('bookings.user_id', 'users.name', 'bookings.full_name')
                ->orderByDesc('total_bookings');

            if ($dateFrom) {
                $topBookerQuery->where('bookings.booking_start', '>=', $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $topBookerQuery->where('bookings.booking_start', '<=', $dateTo . ' 23:59:59');
            }
            if ($request->filled('q')) {
                $s = $request->input('q');
                $topBookerQuery->where(function($sub) use ($s) {
                    $sub->where('users.name', 'like', "%{$s}%")
                        ->orWhere('bookings.full_name', 'like', "%{$s}%")
                        ->orWhere('services.service_name', 'like', "%{$s}%");
                });
            }

            $topBookerRow = $topBookerQuery->first();
            $topBooker = $topBookerRow ? ($topBookerRow->name ?? null) : null;
            $topBookerId = $topBookerRow ? $topBookerRow->user_id : null;
            $topBookerCount = $topBookerRow ? (int) $topBookerRow->total_bookings : 0;

            // Chart data: bookings per service
            // Chart data: bookings per service (apply same date/search/status filters)
            $serviceCountsQuery = DB::table('bookings')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('services.admin_id', $adminId);
            if ($dateFrom) {
                $serviceCountsQuery->where('bookings.booking_start', '>=', $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $serviceCountsQuery->where('bookings.booking_start', '<=', $dateTo . ' 23:59:59');
            }
            if ($request->filled('q')) {
                $s = $request->input('q');
                $serviceCountsQuery->where('services.service_name', 'like', "%{$s}%");
            }
            if ($request->filled('status')) {
                $sInput = $request->input('status');
                if ($sInput === 'no_show') {
                    $s = 'no show';
                } elseif ($sInput === 'reject') {
                    $s = 'rejected';
                } else {
                    $s = $sInput;
                }
                $serviceCountsQuery->where('bookings.status', $s);
            }
            $serviceCounts = $serviceCountsQuery
                ->select('services.service_name', DB::raw('COUNT(bookings.id) as total'))
                ->groupBy('services.service_name')
                ->orderByDesc('total')
                ->get();

            // Status distribution (apply same filters)
            $statusDistQuery = DB::table('bookings')
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('services.admin_id', $adminId);
            if ($dateFrom) {
                $statusDistQuery->where('bookings.booking_start', '>=', $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $statusDistQuery->where('bookings.booking_start', '<=', $dateTo . ' 23:59:59');
            }
            if ($request->filled('q')) {
                $s = $request->input('q');
                $statusDistQuery->where('services.service_name', 'like', "%{$s}%");
            }
            if ($request->filled('status')) {
                $sInput = $request->input('status');
                if ($sInput === 'no_show') {
                    $s = 'no show';
                } elseif ($sInput === 'reject') {
                    $s = 'rejected';
                } else {
                    $s = $sInput;
                }
                $statusDistQuery->where('bookings.status', $s);
            }
            $statusDistribution = $statusDistQuery
                ->select('bookings.status', DB::raw('COUNT(bookings.id) as total'))
                ->groupBy('bookings.status')
                ->get();

            $productLabels = $serviceCounts->pluck('service_name')->toArray();
            $productData = $serviceCounts->pluck('total')->toArray();

            $statusLabels = $statusDistribution->pluck('status')->map(function($s){ return ucfirst(str_replace('_',' ', $s)); })->toArray();
            $statusData = $statusDistribution->pluck('total')->toArray();

            // paginate for view
            $services = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

            $viewData = compact(
                'services',
                'totalServices',
                'totalBookingSales',
                'totalBookings',
                'topService',
                'topBooker',
                'topBookerId',
                'topBookerCount',
                'productLabels',
                'productData',
                'statusLabels',
                'statusData'
            );

            // include filter bounds
            $viewData = array_merge($viewData, ['filters' => ['date_from' => $dateFrom, 'date_to' => $dateTo, 'month' => $month, 'year' => $year, 'q' => $request->input('q'), 'status' => $request->input('status')]]);

            // Export to PDF if requested
            if ($request->input('export') === 'pdf') {
                $viewData['servicesAll'] = (clone $query)->orderByDesc('created_at')->get();
                $html = view('admin.reports.servicesreport_pdf', $viewData)->render();
                $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
                return $pdf->download('services-report.pdf');
            }

            return view('admin.reports.servicesreport', $viewData);
        }
}

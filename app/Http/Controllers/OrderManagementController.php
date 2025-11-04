<?php


namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderStatusUpdated;


class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $adminId = Auth::id();

        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        $search = $request->get('search');

        $orders = Order::whereHas('items.product', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->with([
                'user',
                'shippingAddress.user', // Eager load shipping address and its user
                'items' => function ($query) use ($adminId) {
                    $query->whereHas('product', function ($q) use ($adminId) {
                        $q->where('admin_id', $adminId);
                    })->with('product');
                }
            ])
            // Allow filtering by any of the known statuses (including completed and reject)
            ->when($status && in_array($status, ['pending', 'confirmed', 'completed', 'reject', 'rejected', 'canceled', 'ready_to_pick_up', 'to_delivery']), function ($query) use ($status) {
                // some records may store 'rejected' instead of 'reject'
                if ($status === 'reject') {
                    $query->whereIn('status', ['reject', 'rejected']);
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($paymentMethod && in_array($paymentMethod, ['cod', 'cop']), function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            // Search by order id, recipient name, order/address, user name/email, product name or product type
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    // attempt exact id match when digits are present (allow #123 or 123 input)
                    $digits = preg_replace('/[^0-9]/', '', $search);
                    if ($digits !== '') {
                        $q->orWhere('id', $digits);
                    }

                    // match recipient name or stored order address snapshot
                    $q->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%');

                    // match fields on the shippingAddress relation (if used)
                    $q->orWhereHas('shippingAddress', function ($qsa) use ($search) {
                        $qsa->where('name', 'like', '%' . $search . '%')
                            ->orWhere('address', 'like', '%' . $search . '%')
                            ->orWhere('barangay', 'like', '%' . $search . '%')
                            ->orWhere('city', 'like', '%' . $search . '%')
                            ->orWhere('province', 'like', '%' . $search . '%');
                    });

                    // match user name/email
                    $q->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%');
                    });

                    // match product name, product_name (snapshot) or product type
                    $q->orWhereHas('items.product', function($q3) use ($search) {
                        $q3->where('product_name', 'like', '%' . $search . '%')
                           ->orWhere('name', 'like', '%' . $search . '%')
                           ->orWhere('type', 'like', '%' . $search . '%');
                    });
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.order.index', compact('orders', 'status', 'paymentMethod', 'search'));
    }

public function updateStatus(Request $request, Order $order)
{
    // Accept both 'reject' (from the UI) and 'rejected' (stored in some records)
    // Allow both legacy 'to_delivery' and 'out_for_delivery' keys
    $request->validate(['status' => 'required|in:pending,confirmed,ready_to_pick_up,to_delivery,out_for_delivery,completed,canceled,rejected,reject']);

    // Prevent update if already canceled or rejected
    if (in_array($order->status, ['canceled', 'rejected', 'reject'])) {
        return back()->with('error', 'Order is ' . $order->status . ' and cannot be updated.');
    }

    $oldStatus = $order->status;
    $newStatus = $request->status === 'reject' ? 'rejected' : $request->status;

    // Use a transaction to ensure stock updates + order status update are atomic
    DB::transaction(function () use ($order, $oldStatus, $newStatus) {
        // Update order status
        $order->status = $newStatus;
        $order->save();

        // If the order is being changed to rejected/canceled, return quantities to stock
        // but only if we haven't already returned stock for this order (stock_returned flag)
        if (in_array($newStatus, ['rejected', 'canceled']) && !in_array($oldStatus, ['rejected', 'reject', 'canceled'])) {
            // reload fresh order inside transaction
            $order->refresh();
            if (!$order->stock_returned) {
                $order->load('items.product');
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        // increment is atomic at the DB level
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }
                // mark as stock returned for auditability and idempotency
                $order->stock_returned = true;
                $order->save();
            }
        }

        // Log admin order status update
        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'Admin Updated Order Status',
            'details' => 'Admin #' . (Auth::id() ?? 'N/A') . ' updated Order #' . $order->id . ' status from ' . $oldStatus . ' to ' . $order->status,
            'timestamp' => now(),
        ]);
    });

    // Send notification to user after transaction
    $order->load(['items', 'items.product', 'admin.profile', 'user']);
    $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $order->status));

    return back()->with('success', 'Order status updated!');
}
}
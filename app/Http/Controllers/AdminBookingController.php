<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBookingController extends Controller
{
    // Display all bookings for the admin
  public function index(Request $request)
{
    // Automatically reject expired pending bookings
    \App\Models\Booking::where('status', 'pending')
        ->whereDate('booking_start', '<=', now()->toDateString())
        ->update(['status' => 'rejected']);

    $adminId = Auth::id(); // Get currently logged-in admin ID

    $query = Booking::with('service')
        ->whereHas('service', function ($q) use ($adminId) {
            $q->where('admin_id', $adminId); // Only bookings for services of this admin
        });

    // Apply status filter if provided
    if ($request->filled('status') && in_array($request->status, Booking::statuses())) {
        $query->where('status', $request->status);
    }

    // Apply payment method filter if provided
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    // Apply search filter if provided
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%$search%")
              ->orWhere('attendees', 'like', "%$search%")
              ->orWhereHas('user', function($uq) use ($search) {
                  $uq->where('name', 'like', "%$search%")
                     ->orWhere('email', 'like', "%$search%")
                     ->orWhere('phone', 'like', "%$search%")
              ;
              })
              ->orWhereHas('service', function($sq) use ($search) {
                  $sq->where('service_name', 'like', "%$search%")
                     ->orWhere('description', 'like', "%$search%")
              ;
              });
        });
    }
    // Apply service type filter if provided
    if ($request->has('service_type') && $request->service_type) {
        // service_type filter removed; keep query unchanged
    }

    // Apply date range filter if provided
    if ($request->has('date_from') && $request->has('date_to')) {
        $query->whereBetween('booking_start', [$request->date_from, $request->date_to]);
    }

    // Fetch bookings, order by latest first, and paginate
    $bookings = $query->orderBy('created_at', 'desc')->paginate(50);


    return view('admin.bookings.index', compact('bookings'));
}
public function approve(Booking $booking)
{
    if ($booking->status === 'pending') {
    $oldStatus = $booking->status;
    // Approve the booking: set to 'ongoing' to mark as active
    $booking->status = 'ongoing';
    $booking->save();

        // Eager load service and admin relationships
    $booking->load(['service.admin.profile', 'user']);
        Log::info('Booking user:', ['user' => $booking->user]);
        // Send notification to user if user exists
        if ($booking->user) {
            $booking->user->notify(new \App\Notifications\BookingStatusUpdated($booking, $oldStatus, $booking->status));
        } else {
            Log::warning('No user found for booking', ['booking_id' => $booking->id]);
        }

        // Mark the associated service as unavailable
        if ($booking->service) {
            $booking->service->is_available = 0;
            $booking->service->save();
        }

        // Log activity for booking status update
        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'Admin Updated Booking Status',
            'details' => 'Booking #' . $booking->id . ' status changed from ' . $oldStatus . ' to ' . $booking->status,
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking approved and service marked as unavailable.');
    }

    return redirect()->route('admin.bookings.index')
        ->with('error', 'Booking cannot be approved. It might already be approved or rejected.');
}

// Reject a booking
    public function reject(Booking $booking)
    {
        if ($booking->status == 'pending') {
            $oldStatus = $booking->status;
            $booking->status = 'Rejected';
            $booking->save();

            // Send notification to user
            $booking->user->notify(new \App\Notifications\BookingStatusUpdated($booking, $oldStatus, $booking->status));

            \App\Models\ActivityLog::create([
                'user_admin' => Auth::user()->name ?? 'Unknown',
                'action' => 'Admin Updated Booking Status',
                'details' => 'Booking #' . $booking->id . ' status changed from ' . $oldStatus . ' to ' . $booking->status,
                'timestamp' => now(),
            ]);
            return redirect()->route('admin.bookings.index')->with('success', 'Booking rejected.');
        }

        return redirect()->route('admin.bookings.index')->with('error', 'Booking cannot be rejected. It might already be approved or rejected.');
    }

    // Update booking status from dropdown
    public function updateStatus(Request $request, Booking $booking)
    {
        $allowed = implode(',', Booking::statuses());
        $request->validate([
            'status' => 'required|in:' . $allowed,
        ]);
        // Do not allow changing status for cancelled bookings
        if (strtolower($booking->status) === 'cancelled') {
            return redirect()->route('admin.bookings.index')->with('error', 'Cannot change status of a cancelled booking.');
        }
        $oldStatus = $booking->status;
        $booking->status = $request->status;
        // If status changed to completed, set booking_end to now() if not already set
        if (strtolower($request->status) === 'completed' && empty($booking->booking_end)) {
            $booking->booking_end = now();
        }
        $booking->save();

        // Optionally notify user
        if ($booking->user) {
            $booking->user->notify(new \App\Notifications\BookingStatusUpdated($booking, $oldStatus, $booking->status));
        }

        // Special logic for 'no show'
        if ($booking->status === 'no show') {
            // Example: log, notify admin, or perform other actions
            Log::info('Booking marked as No Show', ['booking_id' => $booking->id]);
            // You can add more actions here if needed
        }

        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'Admin Updated Booking Status',
            'details' => 'Booking #' . $booking->id . ' status changed from ' . $oldStatus . ' to ' . $booking->status,
            'timestamp' => now(),
        ]);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking status updated successfully.');
    }
}

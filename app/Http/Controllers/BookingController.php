<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Service;
use Carbon\Carbon;
use Dompdf\Dompdf;

class BookingController extends Controller
{

     public function print(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        // You can customize this view for a real receipt/printout
        return view('user.bookings.print', compact('booking'));
    }
     public function pay(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        if ($booking->status !== 'pending' || $booking->payment_method !== 'gcash') {
            return back()->with('error', 'Payment not allowed for this booking.');
        }
        return view('user.bookings.pay', compact('booking'));
    }
public function index()
{
    $bookings = Booking::with('service')
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(10);

    return view('user.bookings.index', compact('bookings'));
}

public function store(Request $request, Service $service)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
    ]);

    // Ensure user has an ongoing booking
    $hasBooking = Booking::where('user_id', Auth::id())
                         ->where('service_id', $service->id)
                         ->where('status', 'ongoing')
                         ->exists();

    if (!$hasBooking) {
        return back()->with('error', 'You must have an approved booking to review.');
    }

    Review::create([
        'user_id' => Auth::id(),
        'service_id' => $service->id,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    \App\Models\ActivityLog::create([
        'user_admin' => Auth::user()->name ?? 'Unknown',
        'action' => 'User Booking Review',
        'details' => 'User #' . Auth::id() . ' submitted a review for Service #' . $service->id,
        'timestamp' => now(),
    ]);
        // Example booking creation (add your actual booking logic here)
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'status' => 'pending',
            // Add other fields as needed
        ]);

        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'User Created Booking',
            'details' => 'User #' . Auth::id() . ' booked Service #' . $service->id . ' (Booking #' . $booking->id . ')',
            'timestamp' => now(),
        ]);

        // If review logic is needed, keep it below or move to another method
        // ...existing review logic...

        return back()->with('success', 'Booking created!');
}
public function cancel(Request $request, Booking $booking)
{
    if ($booking->user_id !== Auth::id()) {
        abort(403);
    }

    if ($booking->status !== 'pending') {
        return back()->with('error', 'Only pending bookings can be canceled.');
    }

    $now = now();
    $canCancel = false;

    // If booking has a scheduled start date/time, allow cancellation only if it's at least 24 hours away
    if (!empty($booking->booking_start)) {
        try {
            $bookingStart = $booking->booking_start instanceof \Carbon\Carbon ? $booking->booking_start : \Carbon\Carbon::parse($booking->booking_start);
            if ($bookingStart->isFuture() && $bookingStart->diffInHours($now) >= 24) {
                $canCancel = true;
            }
        } catch (\Exception $e) {
            // ignore parsing errors and fallback to created-at rule
        }
    }

    // Fallback: allow cancellation within 24 hours after booking creation
    if (! $canCancel) {
        try {
            $createdAt = $booking->created_at instanceof \Carbon\Carbon ? $booking->created_at : \Carbon\Carbon::parse($booking->created_at);
            if ($now->diffInHours($createdAt) <= 24) {
                $canCancel = true;
            }
        } catch (\Exception $e) {
            // leave canCancel false
        }
    }

    if (! $canCancel) {
        return back()->with('error', 'Cancellation is only allowed up to 24 hours before the scheduled start, or within 24 hours after booking creation.');
    }

    // Save cancel reason if provided
    $cancelReason = trim((string) $request->input('cancel_reason', ''));
    if (!empty($cancelReason)) {
        $booking->cancel_reason = $cancelReason;
    }
    $booking->status = 'cancelled';
    $booking->save();

    \App\Models\ActivityLog::create([
        'user_admin' => Auth::user()->name ?? 'Unknown',
        'action' => 'User Cancelled Booking',
        'details' => 'User #' . Auth::id() . ' cancelled Booking #' . $booking->id . (!empty($cancelReason) ? ' (Reason: ' . $cancelReason . ')' : ''),
        'timestamp' => now(),
    ]);

    return back()->with('success', 'Booking cancelled successfully.');
}

    public function downloadReceipt($id)
{
    $booking = Booking::with(['user', 'service'])->findOrFail($id);
    $dompdf = new Dompdf();
    $html = view('user.bookings.pdf', compact('booking'))->render();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="booking_receipt_'.$booking->id.'.pdf"');
}

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        return view('user.bookings.show', compact('booking'));
    }


}

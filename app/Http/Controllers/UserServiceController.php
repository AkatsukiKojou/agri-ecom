<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\User;

class UserServiceController extends Controller
{
    public function verifyBookingOtp(Request $request, $serviceId)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $inputOtp = $request->input('otp');
        $sessionOtp = session('booking_otp');
        $expiry = session('booking_otp_expiry');

        if (!$sessionOtp || !$expiry || now()->gt($expiry)) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new one.']);
        }

        if ($inputOtp != $sessionOtp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        // OTP is valid, finalize booking
        $bookingData = session('booking_data');
        if (!$bookingData) {
            return back()->withErrors(['otp' => 'Booking data missing. Please try again.']);
        }

        // If a temporary gcash upload path was saved in session, move it to permanent storage now
        if (!empty($bookingData['gcash_payment_temp'])) {
            $tempPath = $bookingData['gcash_payment_temp'];
            // Ensure the temp file exists before attempting to move
            if (Storage::disk('public')->exists($tempPath)) {
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION) ?: 'jpg';
                $newName = 'gcash_payments/' . uniqid('gcash_') . '.' . $extension;
                Storage::disk('public')->move($tempPath, $newName);
                // set the final path to be stored on booking record
                $bookingData['gcash_payment'] = $newName;
            }
            unset($bookingData['gcash_payment_temp']);
        }

        // Ensure booking_end is computed server-side to prevent client tampering
        $service = Service::findOrFail($serviceId);
        if (!empty($bookingData['booking_start']) && $this->isDurationUnit($service)) {
            try {
                $days = $this->durationToDays($service->duration ?? '1 day');
                $start = Carbon::parse($bookingData['booking_start']);
                $end = $start->copy()->addDays($days - 1);
                $bookingData['booking_end'] = $end->toDateString();
            } catch (\Exception $e) {
                // fallback: set end same as start
                $bookingData['booking_end'] = $bookingData['booking_start'];
            }
        } else {
            // ensure booking_end is not supplied/persisted for non-duration units
            if (isset($bookingData['booking_end'])) {
                unset($bookingData['booking_end']);
            }
        }

        $booking = Booking::create(array_merge($bookingData, [
            'user_id' => Auth::id(),
            'service_id' => $serviceId,
            'status' => 'pending',
        ]));

        // Log user booking creation in ActivityLog
        \App\Models\ActivityLog::create([
            'user_admin' => Auth::user()->name ?? 'Unknown',
            'action' => 'User Created Booking',
            'details' => 'User #' . Auth::id() . ' booked Service #' . $serviceId . ' (Booking #' . $booking->id . ')',
            'timestamp' => now(),
        ]);

        // Notify the admin of the service about the new booking
        $admin = $service->admin;
        $user = Auth::user();
        if ($admin) {
            $admin->notify(new \App\Notifications\ServiceReserved($booking, $user, $service));
        }

        // Clear OTP and booking data from session
        session()->forget(['booking_otp', 'booking_otp_expiry', 'booking_data']);

        // Show dedicated booking success animation view, then auto-redirect to bookings index after 5 seconds
        return view('user.services.booking_success', [
            'redirectUrl' => url('/user/bookings'),
        ]);
    }
    public function bookingOtp(Request $request, $serviceId)
    {
        $request->validate([
            'booking_start' => 'required|date',
            'payment_method' => 'required|string',
            'total_price' => 'required|numeric',
            'downpayment' => 'required|numeric|min:0',
            'attendees' => 'required|integer|min:1',
            'email' => 'required|email',
        ]);

        $bookingData = $request->except(['_token', 'gcash_payment']);
        // Compute booking_end server-side only for duration-like units to prevent client tampering
        $service = Service::findOrFail($serviceId);
        if (!empty($bookingData['booking_start']) && $this->isDurationUnit($service)) {
            try {
                $days = $this->durationToDays($service->duration ?? '1 day');
                $start = Carbon::parse($bookingData['booking_start']);
                $end = $start->copy()->addDays($days - 1);
                $bookingData['booking_end'] = $end->toDateString();
            } catch (\Exception $e) {
                $bookingData['booking_end'] = $bookingData['booking_start'];
            }
        } else {
            if (isset($bookingData['booking_end'])) {
                unset($bookingData['booking_end']);
            }
        }
        // Handle uploaded gcash image: if present, store temporarily and save path only
        if ($request->hasFile('gcash_payment')) {
            $file = $request->file('gcash_payment');
            // store in a temp folder in public disk
            $tempPath = $file->store('temp/gcash', 'public');
            // Save only path (string) into booking data to avoid serializing UploadedFile
            $bookingData['gcash_payment_temp'] = $tempPath;
        }
        $email = $request->input('email');

        // Generate OTP
        $otp = rand(100000, 999999);
        session([
            'booking_otp' => $otp,
            'booking_otp_expiry' => now()->addMinutes(5),
            'booking_data' => $bookingData,
        ]);

        // Send OTP to provided email
        Mail::to($email)->send(new \App\Mail\VerificationCodeMail($otp));

        return view('user.services.otp', [
            'email' => $email,
            'service_id' => $serviceId,
            'booking_data' => $bookingData,
        ]);
    }
    public function show($id)
    {
        $service = Service::with(['reviews.user', 'admin', 'bookings'])->findOrFail($id);
        $user = Auth::user();
        // Consider only 'ongoing' bookings as the active state for availability checks
        $userHasApprovedBooking = Booking::where('user_id', $user->id)
            ->where('service_id', $id)
            ->where('status', 'ongoing')
            ->exists();
        $userAlreadyReviewed = Review::where('user_id', $user->id)
            ->where('service_id', $id)
            ->exists();

        // Find latest approved booking for this service
        // Use the most recent 'ongoing' booking as the anchor for next-available calculations
        $lastApprovedBooking = $service->bookings()
            ->where('status', 'ongoing')
            ->orderByDesc('booking_start')
            ->first();

        if ($lastApprovedBooking) {
            // If service has a duration in days, add it to booking_end, then add 3 days
            $duration = is_numeric($service->duration) ? intval($service->duration) : 1;
            $nextAvailable = Carbon::parse($lastApprovedBooking->booking_start)
                ->addDays($duration)
                ->addDays(3);
            // If booking_end exists and is after booking_start, use that instead
            if ($lastApprovedBooking->booking_end && $lastApprovedBooking->booking_end > $lastApprovedBooking->booking_start) {
                $nextAvailable = Carbon::parse($lastApprovedBooking->booking_end)->addDays(3);
            }
            $nextAvailableDate = $nextAvailable->toDateString();
        } else {
            $nextAvailableDate = Carbon::now()->addDays(2)->toDateString();
        }

        return view('user.services.show', compact('service', 'userHasApprovedBooking', 'userAlreadyReviewed', 'nextAvailableDate'));
    }
// public function store(Request $request, $serviceId)
// {
//     $request->validate([
//         'start_date' => 'required|date|after_or_equal:today',
//         'end_date' => 'required|date|after_or_equal:start_date',
//     ]);
//
//     $service = Service::findOrFail($serviceId);
//
//     $startDate = Carbon::parse($request->start_date);
//     $endDate = Carbon::parse($request->end_date);
//     $days = $startDate->diffInDays($endDate) + 1;
//
//     $totalPrice = $days * $service->price;
//
//     Booking::create([
//         'user_id' => Auth::id(),
//         'service_id' => $service->id,
//         'start_date' => $startDate,
//         'end_date' => $endDate,
//         'total_price' => $totalPrice,
//         'status' => 'pending',
//     ]);
//
//     return back()->with('success', 'Service successfully booked!');
// }

    public function reviewBooking(Request $request, Service $service)
    {
        $request->validate([
            'booking_start' => 'required|date|after_or_equal:today',
            'booking_end' => 'required|date|after_or_equal:booking_start',
        ]);

        return view('user.services.payment', [
            'service' => $service,
            'booking_start' => $request->booking_start,
            'booking_end' => $request->booking_end,
        ]);
    }   

    public function finalizeBooking(Request $request, Service $service)
    {
        // Prevent direct finalizeBooking POSTs from skipping OTP verification.
        // If booking_data was not created via the OTP flow, redirect back and ask user to go through OTP.
        if (!session('booking_data') && !session('booking_otp_verified')) {
            return redirect()->route('user.services.show', $service->id)->with('error', 'Please complete booking verification via the OTP step before finalizing your booking.');
        }

        // Validate the incoming request

        // Find latest ongoing booking for this service
        $lastApprovedBooking = $service->bookings()
            ->where('status', 'ongoing')
            ->orderByDesc('booking_start')
            ->first();
        if ($lastApprovedBooking) {
            $duration = is_numeric($service->duration) ? intval($service->duration) : 1;
            $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_start)
                ->addDays($duration)
                ->addDays(3);
            if ($lastApprovedBooking->booking_end && $lastApprovedBooking->booking_end > $lastApprovedBooking->booking_start) {
                $nextAvailable = \Carbon\Carbon::parse($lastApprovedBooking->booking_end)->addDays(3);
            }
            $nextAvailableDate = $nextAvailable->toDateString();
        } else {
            $nextAvailableDate = now()->addDays(2)->toDateString();
        }

        $request->validate([
            'booking_start' => ['required', 'date', 'after_or_equal:' . $nextAvailableDate],
            'payment_method' => 'required|string',
            'total_price' => 'required|numeric',
            'downpayment' => 'required|numeric|min:0',
            'attendees' => 'required|integer|min:1',
            'gcash_payment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'email' => 'required|email',
        ]);

        $gcashPaymentPath = null;
        if ($request->payment_method === 'gcash' && $request->hasFile('gcash_payment')) {
            $gcashPaymentPath = $request->file('gcash_payment')->store('gcash_payments', 'public');
        }

        // Recompute booking_end server-side and override any client input
        // Recompute booking_end server-side only when the service unit denotes a duration
        if ($this->isDurationUnit($service)) {
            try {
                $days = $this->durationToDays($service->duration ?? '1 day');
                $start = Carbon::parse($request->booking_start);
                $computedEnd = $start->copy()->addDays($days - 1)->toDateString();
            } catch (\Exception $e) {
                $computedEnd = $request->booking_start;
            }
        } else {
            $computedEnd = null;
        }

        $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'service_id' => $service->id,
                    'status' => 'pending',
                    'booking_start' => $request->booking_start,
                    'booking_end' => $computedEnd,
                    'total_price' => $request->total_price,
                    'payment_method' => $request->payment_method,
                    'downpayment' => $request->downpayment,
                    'gcash_payment' => $gcashPaymentPath,
                    'phone' => $request->phone,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'attendees' => $request->attendees,
                    'region' => $request->region,
                    'province' => $request->province,
                    'city' => $request->city,
                    'barangay' => $request->barangay,
                    'customer_note' => $request->customer_note,
                    'downpayment_visit_date' => $request->downpayment_visit_date,
            ]);
        
            // Log user booking creation in ActivityLog
            \App\Models\ActivityLog::create([
                'user_admin' => Auth::user()->name ?? 'Unknown',
                'action' => 'User Created Booking',
                'details' => 'User #' . Auth::id() . ' booked Service #' . $service->id . ' (Booking #' . $booking->id . ')',
                'timestamp' => now(),
            ]);

        // Notify the admin of the service about the new booking
        $admin = $service->admin;
        $user = Auth::user();
        if ($admin) {
            $admin->notify(new \App\Notifications\ServiceReserved($booking, $user, $service));
        }



        // Redirect back with success message
        return redirect()->route('user.bookings.index')->with('success', 'Booking submitted! Please wait for admin approval.');
    }

    /**
     * Convert a duration string (e.g. "3 days", "2 weeks", "1 month") to a number of days.
     */
    protected function durationToDays($duration)
    {
        $d = strtolower(trim((string) $duration));
        // extract integer from string
        preg_match('/(\d+)/', $d, $m);
        $num = isset($m[1]) ? intval($m[1]) : 1;
        if (strpos($d, 'day') !== false) {
            return max(1, $num);
        }
        if (strpos($d, 'week') !== false) {
            return max(1, $num) * 7;
        }
        if (strpos($d, 'month') !== false) {
            return max(1, $num) * 30;
        }
        // fallback
        return max(1, $num);
    }

    /**
     * Determine whether the given service unit should be treated as a duration
     * (i.e. booking_end should be computed). Matches the front-end allowed list.
     */
    protected function isDurationUnit(Service $service)
    {
        $unit = strtolower(trim((string) ($service->unit ?? '')));
        $allowed = ['session', 'day', 'seminar', 'training', 'program'];
        return in_array($unit, $allowed, true);
    }

}
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Booking;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Booking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $admin = $this->booking->service && $this->booking->service->admin
            ? $this->booking->service->admin
            : null;

        $adminProfile = $admin && $admin->profile ? $admin->profile : null;
        $adminName = $adminProfile && $adminProfile->farm_owner ? $adminProfile->farm_owner : 'Farm Owner';
        $adminImage = $adminProfile && $adminProfile->profile_photo ? $adminProfile->profile_photo : null;

        // Normalize status for matching
        $normalized = str_replace(' ', '_', strtolower(trim($this->newStatus)));

        $statusMessages = [
            'approved' => "Your booking has been approved. Please check your Booking section for details.",
            'ongoing' => "Your booking is currently ongoing. We hope you're having a great experience.",
            'completed' => "Your booking has been completed. Thank you for using our service!",
            'reject' => "We're sorry — your booking has been rejected. Please contact support for more information.",
            'rejected' => "We're sorry — your booking has been rejected. Please contact support for more information.",
            'no_show' => "It looks like you missed your booking (no-show). Please contact support to reschedule or request assistance.",
        ];

        if (isset($statusMessages[$normalized])) {
            $message = $statusMessages[$normalized];
        } else {
            $message = "Your booking status has been updated from {$this->oldStatus} to {$this->newStatus}. You can view it in your Booking section. Thank you for choosing our service!";
        }

        return [
            'booking_id' => $this->booking->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $message,
            'admin_name' => $adminName,
            'admin_image' => $adminImage,
        ];
    }
}

<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable)
    {
        $userPhoto = $this->booking->user->profile->profile_photo ?? null;
        return [
            'booking_id' => $this->booking->id,
            'message' => "New service booking by {$this->booking->user->name}",
            'user_id' => $this->booking->user_id,
            'user_name' => $this->booking->user->name,
            'user_photo' => $userPhoto,
            'service_name' => $this->booking->service->service_name,
            'booking_start' => $this->booking->booking_start->toDateString(),
            'reference' => 'BK-' . str_pad($this->booking->id, 6, '0', STR_PAD_LEFT),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A new service booking has been placed.')
            ->action('View Booking', url(route('admin.bookings.show', $this->booking->id)))
            ->line('Thank you!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => "New service booking by {$this->booking->user->name}",
        ];
    }
}
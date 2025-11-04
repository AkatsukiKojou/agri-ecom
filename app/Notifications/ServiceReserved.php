<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceReserved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $booking;
    public $user;
    public $service;

    public function __construct($booking, $user, $service)
    {
        $this->booking = $booking;
        $this->user = $user;
        $this->service = $service;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking',
            'booking_id' => $this->booking->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_photo' => $this->user->profile->profile_photo ?? null,
            'service_id' => $this->service->id,
            'service_name' => $this->service->service_name ?? $this->service->name ?? 'Service',
            'booking_start' => $this->booking->start_date ?? $this->booking->created_at,
        ];
    }
}

<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public $order;  // <-- add this

    /**
     * Create a new notification instance.
     */
    public function __construct($order)  // <-- accept order here
    {
        $this->order = $order;          // <-- set it
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // Only database notification, no email
    }

    /**
     * Get the array representation of the notification to be saved in DB.
     */
    public function toDatabase($notifiable)
    {
        $user = $this->order->user;
        // Try to get user photo from user->profile->profile_photo, fallback to user->photo, fallback to null
        $photo = null;
        if ($user) {
            if ($user->profile && $user->profile->profile_photo) {
                $photo = $user->profile->profile_photo;
            } elseif (isset($user->photo)) {
                $photo = $user->photo;
            }
        }
        // Get product names from related product model if available, else fallback to product_name field
        $productNames = $this->order->items->map(function($item) {
            if ($item->product && $item->product->name) {
                return $item->product->name;
            }
            return $item->product_name;
        })->toArray();
        $productList = implode(', ', $productNames);
        return [
            'order_id' => $this->order->id,
            'message' => "{$this->order->name} placed an order for {$productList}",
            'user_name' => $this->order->name,
            'photo' => $photo,
            'products' => $productList,
        ];
    }

    /**
     * (Optional) Get the mail representation of the notification.
     * Remove this if you don't want email from this notification class.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A new order has been placed.')
            ->action('View Order', url(route('user.orders', $this->order->id)))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => "New order placed by {$this->order->name}",
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $productNames = $this->order->items->pluck('product_name')->implode(', ');
        $productDisplay = $productNames ?: 'your order';
        $firstProduct = $this->order->items->first() && $this->order->items->first()->product
            ? $this->order->items->first()->product
            : null;
        $admin = $firstProduct && $firstProduct->admin ? $firstProduct->admin : null;
        $adminProfile = $admin && $admin->profile ? $admin->profile : null;
        $adminName = $adminProfile && $adminProfile->farm_owner ? $adminProfile->farm_owner : ($admin ? $admin->name : 'Farm Owner');
        $adminImage = $adminProfile && $adminProfile->profile_photo ? $adminProfile->profile_photo : ($admin ? $admin->photo : null);

        // Normalize status for matching
        $normalized = str_replace(' ', '_', strtolower(trim($this->newStatus)));

        $statusMessages = [
            'processing' => "Your order ({$productDisplay}) is now being processed. We'll notify you when it ships.",
            'confirmed' => "Your order ({$productDisplay}) has been confirmed. Thank you for your purchase!",
            // Ready for pick up (display key uses underscore naming like other statuses)
            'ready_to_pick_up' => "Your order ({$productDisplay}) is ready for pickup. Please collect it at the designated pick-up point.",
            // Support both legacy 'to_delivery' key and the clearer 'out_for_delivery'
            'to_delivery' => "Your order ({$productDisplay}) is out for delivery. Expect it soon.",
            'out_for_delivery' => "Your order ({$productDisplay}) is out for delivery. Expect it soon.",
            'completed' => "Your order ({$productDisplay}) is completed. Thank you — we hope to see you again!",
            'complete' => "Your order ({$productDisplay}) is completed. Thank you — we hope to see you again!",
            'reject' => "We're sorry — your order ({$productDisplay}) has been rejected. Please contact support for details.",
        ];

        if (isset($statusMessages[$normalized])) {
            $message = $statusMessages[$normalized];
        } else {
            // Default generic message including the status change
            $productLabel = $productNames ?: 'your order';
            $message = "Your order ({$productLabel}) status has been updated from {$this->oldStatus} to {$this->newStatus}. You can view it in your Purchase section. Thank you for shopping with us!";
        }

        return [
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $message,
            'admin_name' => $adminName,
            'admin_image' => $adminImage,
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
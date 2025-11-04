<?php 
// namespace App\Mail;
// 
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;

// class NewOrderNotification extends Mailable
// {
    // use Queueable, SerializesModels;

    // public $order;
    // public $items;

//     public function __construct($order, $items)
//     {
//         $this->order = $order;
//         $this->items = $items;
//     }

//    public function build()
// {
//     return $this->subject('New Order Received')
//                 ->view('admin.emails.new_order_notification')  // Correct path
//                 ->with([
//                     'order' => $this->order,
//                     'items' => $this->items,
//                 ]);
// }
// }

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $adminItems;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $adminItems)
    {
        $this->order = $order;
        $this->adminItems = $adminItems;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Order Notification')
                    ->view('emails.new_order_notification')
                    ->with([
                        'order' => $this->order,
                        'adminItems' => $this->adminItems,
                    ]);
    }
}
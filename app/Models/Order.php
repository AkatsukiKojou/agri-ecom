<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderItem;
use App\Models\Products;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
                'address',
                'email',
        'total_price',
        'status',
        'payment_method',
          'shipping_fee',
          'shipping_message',
          'stock_returned',
    ];

    protected $casts = [
        'stock_returned' => 'boolean',
    ];

    public function items()
{
    return $this->hasMany(OrderItem::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
  

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    
    public function getTotalQuantityAttribute()
{
    return $this->items->sum('quantity');
}
// In Order.php

public function getTotalPriceAttribute()
{
    return $this->items->sum(function ($item) {
        // return $item->quantity * $item->product->price;
                return $item->product ? $item->quantity * $item->product->price : 0;

    });
}

    public function product()
{
    return $this->belongsTo(Products::class, 'product_id'); // adjust if your FK is different
}
  public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
//     public function shippingAddress() {
//     return $this->hasOne(ShippingAddress::class);
// }
// public function shippingAddress()
// {
//     return $this->hasOne(ShippingAddress::class, 'order_id');
// }
// public function shippingAddress()
// {
//     return $this->belongsTo(ShippingAddress::class, 'user_id');

public function shippingAddress()
{
    // Return the user's default shipping address (if any) to avoid returning an arbitrary address when a user
    // has multiple shipping addresses. Orders should ideally store snapshot fields, but this narrows the fallback.
    return $this->hasOne(ShippingAddress::class, 'user_id', 'user_id')->where('is_default', true);
}

    /**
     * Human-friendly label for the order status.
     */
    public function getStatusLabelAttribute()
    {
        $map = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'ready_to_pick_up' => 'Ready for Pickup',
            'to_delivery' => 'Out for Delivery',
            'out_for_delivery' => 'Out for Delivery',
            'completed' => 'Completed',
            'complete' => 'Completed',
            'canceled' => 'Canceled',
            'cancelled' => 'Cancelled',
            'reject' => 'Reject',
            'rejected' => 'Rejected',
        ];

        $key = $this->status ?? '';
        if (isset($map[$key])) {
            return $map[$key];
        }
        // Fallback: replace underscores and ucfirst
        return ucfirst(str_replace('_', ' ', $key));
    }
}




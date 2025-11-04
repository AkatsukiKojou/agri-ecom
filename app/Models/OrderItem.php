<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    
   
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'shipping_fee',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }public function product()
{
    return $this->belongsTo(Products::class);
}
  public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
      public function shippingAddress() {
    return $this->hasOne(ShippingAddress::class);
}

}

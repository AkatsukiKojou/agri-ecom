<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'region',
        'province',
        'city',
        'barangay',
        'address',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
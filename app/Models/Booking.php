<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{


    use HasFactory;
    protected $casts = [
        'booking_start' => 'date',
        'booking_end' => 'date',
    ];
    protected $fillable = [
        'admin_id',
        'user_id',
        'service_id',
        'booking_start',
        'booking_end',
        'payment_method',
        'total_price',
        'downpayment',
        'gcash_payment',
        'attendees',
        'full_name',
        'email',
        'status',
        'cancel_reason',
        'address',
        'region',
        'province',
        'city',
        'barangay',
        'customer_note',
        'phone',
        'downpayment_visit_date',
    ];

    // Allowed booking statuses
    public const STATUSES = [
        'pending',
        'ongoing',
        'approved',
        'completed',
        'cancelled',
        'rejected',
        'no show',
    ];

    public static function statuses(): array
    {
        return self::STATUSES;
    }

    public function user() {
    return $this->belongsTo(User::class);
}

public function service()
{
    return $this->belongsTo(Service::class, 'service_id');
}
  
    
    public function profile()
    {
        return $this->hasOne(Profile::class, 'admin_id');
    }
    public function products()
    {
        return $this->hasMany(Products::class, 'admin_id');
    }
    
}

<?php

namespace App\Models;
use App\Models\Service;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;
    protected $casts = [
    'booking_start' => 'date',
    'booking_end' => 'date',
];
  protected $fillable = [
    'user_id',
    'service_id',
    'booking_start',
    'booking_end',
    'payment_method',
    'total_price',
    'status',
];

 public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function profile()
    {
        return $this->hasOne(Profile::class, 'admin_id');
    }
public function service() {
    return $this->belongsTo(Service::class);
}
}

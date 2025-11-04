<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{   
    use SoftDeletes, HasFactory;

    protected $casts = [
        'start_time' => 'string',
       
    ];

    protected $fillable = [
        'admin_id',
        'service_name',
        'unit',
        'price',
        'start_time',
        'duration',
        'description',
        'images',
        'is_available',
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                $service->admin_id = Auth::user()->id;
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bookings() {
        return $this->hasMany(Booking::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}


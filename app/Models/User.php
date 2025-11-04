<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Products;
use App\Models\ProfileFollower;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ShippingAddress;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
use  HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
 
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'username','email', 'password', 'phone', 'address','profile_image','gender', 'date_of_birth','role','status','region','province','city','barangay','first_name','last_name','latitude',
        'longitude', 'blocklisted', 'verification_code'];
    


    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $guarded = [];
    protected $attributes = [
    'photo' => null,
];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocklisted' => 'boolean',
        ];
    }

    // Scope to get only non-blocklisted admins
    public function scopeNotBlocklisted($query)
    {
        return $query->where('blocklisted', false);
    }

        public function bookings()
        {
            return $this->hasMany(\App\Models\Booking::class, 'user_id');
        }
     
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
 
    public function products()
    {
        return $this->hasMany(Products::class, 'admin_id');
    }
    
   
public function profile()
{
    return $this->hasOne(Profile::class, 'admin_id');
}

public function orders()
{
    return $this->hasMany(\App\Models\Order::class);
}


public function shippingAddresses()
{
    return $this->hasMany(\App\Models\ShippingAddress::class);
}

//    public function shippingAddresses()
//     {
//         return $this->hasMany(ShippingAddress::class);
//     }

//  public function profile()
//     {
//         return $this->hasOne(Profile::class);
//     }

public function services()
{
    return $this->hasMany(Service::class, 'admin_id'); // change 'admin_id' to your actual column
}

// Example relationships for followers and likes

// For followers count and display
public function profileFollowers()
{
    return $this->hasMany(ProfileFollower::class, 'user_id');
}

public function profileLikes()
{
    return $this->hasMany(ProfileLike::class, 'user_id');
}

}

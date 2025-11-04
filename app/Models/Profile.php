<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Auth;
class Profile extends Model
{
   
        use HasFactory;
        protected $fillable = [
            'admin_id',
            'farm_name',
            'farm_owner',
            'profile_photo',
            'farm_photo',
            'address',
            'region',
            'province',
            'city',
            'barangay',
            'description',
            'certificate',
            'phone_number',
            'email',            
            'documentary',
        ];
    
     
    
    
    
    protected static function booted()
    {
        static::creating(function ($profiles) {
            // Automatically set admin_id to the logged-in admin's user_id
            if (Auth::check() && Auth::user()->role === 'admin') {
                $profiles->admin_id = Auth::user()->id;
            }
        });
    }
    
//     public function user()
// {
//     return $this->belongsTo(User::class);
// }
public function admin()
{
    return $this->belongsTo(User::class, 'admin_id');
}
public function followers() {
    return $this->hasMany(ProfileFollower::class, 'profile_id');
}
public function likes() {
    return $this->hasMany(ProfileLike::class, 'profile_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function profile()
{
    return $this->hasOne(Profile::class, 'user_id', 'id');
}

}

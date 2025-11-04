<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Products extends Model
{   
    use SoftDeletes;
    use HasFactory;
   
    protected $fillable = ['name', 'type', 'unit', 'price','stock_quantity', 'description', 'image', 'blocklisted'];
    protected $casts = [
        'blocklisted' => 'boolean',
    ];

    // Scope to get only non-blocklisted products
    public function scopeNotBlocklisted($query)
    {
        return $query->where('blocklisted', false);
    }
    protected static function booted()  
    {
        static::creating(function ($product) {
            // Automatically set admin_id to the logged-in admin's user_id
            if (Auth::check() && Auth::user()->role === 'admin') {
                $product->admin_id = Auth::user()->id;
            }
        });
    }


        public function user()
    {
        return $this->belongsTo(User::class);
    }
        public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

// In User model (Admin model)
public function profile()
{
    return $this->hasOne(Profile::class);
}
        public function owner()
{
    // Assuming the owner is a user and the foreign key is owner_id
    return $this->belongsTo(User::class, 'user_id');
}
}

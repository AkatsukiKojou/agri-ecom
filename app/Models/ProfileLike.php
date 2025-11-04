<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileLike extends Model
{
    protected $fillable = ['profile_id', 'user_id'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $fillable = [
        'user_id',
        'reporter_id',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}

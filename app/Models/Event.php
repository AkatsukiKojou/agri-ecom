<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'description', 'image', 'created_by'
    ];

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }
}

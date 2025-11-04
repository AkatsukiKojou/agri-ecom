<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'date_received',
        'remarks',
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}

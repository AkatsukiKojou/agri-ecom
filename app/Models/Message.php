<?php
// app\Models\Message.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'image',
        'service_id',
        'service_name',
        'service_image',
        'service_type',
        'service_price',
        'is_read',
    ];
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
}
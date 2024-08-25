<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'user_id',
        'delivery_status_id',
        'notes',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function status()
    {
        return $this->belongsTo(DeliveryStatus::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

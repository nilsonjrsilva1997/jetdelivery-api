<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = ['fee', 'delivery_status_id'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'delivery_order', 'delivery_id', 'order_id');
    }

    public function status()
    {
        return $this->belongsTo(DeliveryStatus::class, 'delivery_status_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status_name'];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
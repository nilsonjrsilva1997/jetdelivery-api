<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    // Adicione a propriedade `company_fee` ao array de atributos que devem ser convertidos
    protected $casts = [
        'company_fee' => 'float',
    ];

    protected $fillable = ['delivery_people_id', 'fee', 'delivery_status_id', 'company_fee', 'online'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'delivery_order', 'delivery_id', 'order_id');
    }

    public function status()
    {
        return $this->belongsTo(DeliveryStatus::class, 'delivery_status_id', 'id');
    }

    public function delivery_people()
    {
        return $this->belongsTo(DeliveryPeople::class, 'delivery_people_id', 'id');
    }

    public function statusHistories()
    {
        return $this->hasMany(DeliveryStatusHistory::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'order_status_id',
        'delivery_address_id',
        'delivery_date',
        'tracking_code',
        'payment_method_id',
        'delivery_people_id',
        'estimated_delivery_time',
        'total_amount',
        'delivery_fee',
        'company_fee',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // public function restaurant()
    // {
    //     return $this->belongsTo(Restaurant::class);
    // }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPeople::class, 'delivery_people_id');
    }
}

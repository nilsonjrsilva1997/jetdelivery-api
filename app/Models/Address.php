<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street_address',
        'complement',
        'neighborhood',
        'city',
        'state',
        'postal_code',
        'number',
        'latitude',
        'longitude',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function delivery_peoples()
    {
        return $this->hasMany(DeliveryPeople::class);
    }
}

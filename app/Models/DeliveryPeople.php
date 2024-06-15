<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPeople extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address_id', 'phone', 'rg', 'cpf', 'cnh', 'latitude', 'longitude'];

    protected $table = 'delivery_peoples';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}

<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfoodOrder extends Model
{
    use HasFactory;

    protected $table = 'ifood_orders';

    protected $fillable = [
        'data_ifood', 'restaurant_id'
    ];

    protected $casts = [
        'data_ifood' => 'array', // Campo 'data_ifood' será armazenado como JSON no banco de dados
    ];

    // Relacionamento com o restaurante
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'category_id', 
        'cpf_cnpj', 
        'address_id', 
        'phone', 
        'fantasy_name'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_user');
    }

    public function ifood_integration()
    {
        return $this->hasOne(IfoodIntegration::class);
    }
}

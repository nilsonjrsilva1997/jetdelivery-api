<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfoodIntegration extends Model
{
    use HasFactory;

    protected $fillable = ['active', 'restaurant_id', 'authorization_code_verifier', 'access_token', 'refresh_token'];
}

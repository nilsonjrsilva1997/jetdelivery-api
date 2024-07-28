<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'confirmed',
        'meta',
        'uuid',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'confirmed' => 'boolean',
        'meta' => 'json',
        'uuid' => 'string',
    ];
}

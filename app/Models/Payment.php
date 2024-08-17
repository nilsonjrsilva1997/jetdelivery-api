<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_amount',
        'token',
        'description',
        'installments',
        'payment_method_id',
        'payer_email',
        'status'
    ];
}

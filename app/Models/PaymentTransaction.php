<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'token_id', 'amount', 'currency',
        'reference', 'status', 'request_data',
        'response_data', 'gateway_transaction_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function token()
    // {
    //     return $this->belongsTo(PaymentToken::class);
    // }

    public function token()
    {
        return $this->belongsTo(\App\Models\PaymentToken::class, 'token_id', 'id')
                    ->withDefault(); // para que $transaction->token no sea null
    }
        
}
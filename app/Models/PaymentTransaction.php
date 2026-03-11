<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;

    // protected $connection = 'hades';

    protected $table = 'payment_transactions';

    protected $fillable = [
        'user_id',
        'token_id',
        'amount',
        'currency',
        'reference',
        'ruex',
        'email',
        'account_number',
        'request_date',
        'response_date',
        'response_code',
        'authorization_number',
        'bin_id',
        'processor_id',
        'result',
        'tracking',
        'system_tracking',
        'status',
        'id_solicitud',
        'codigo_solicitud',
        'request_data',
        'response_data',
        'gateway_transaction_id'
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'response_date' => 'datetime',
        'request_data' => 'array',
        'response_data' => 'array',
        'amount' => 'decimal:2'
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
         return $this->belongsTo(PaymentToken::class, 'token_id');
    }
       
    public function solicitud()
    {
        return $this->belongsTo(
            \App\Models\SolicitudCambioResidencia::class,
            'id_solicitud'
        );
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'authorized');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'declined');
    }
        
}
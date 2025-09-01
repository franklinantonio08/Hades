<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    // Nombre de la tabla
    protected $table = 'payment_logs';

    // Campos asignables
    protected $fillable = [
        'type',
        'data',
        'ip_address',
        'user_agent',
    ];

    // Si quieres leer/escribir 'data' como array/objeto JSON
    protected $casts = [
        'data' => 'array',
    ];

    // Timestamps habilitados (created_at/updated_at). Por defecto es true.
    // protected $timestamps = true;
}
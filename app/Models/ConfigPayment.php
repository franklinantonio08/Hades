<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPayment extends Model
{
    use HasFactory;

    protected $connection = 'atlas';

    protected $table = 'config_payment';

    protected $fillable = [
        'descripcion',
        'tipo',
        'estatus',
        'infoextra',
        'usuarioId'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RIDMigrantes extends Model
{
    use HasFactory;

    protected $table = 'rid_migrante';

    const STATUS_PENDIENTE = 'Pendiente';
    const STATUS_ENVIADO = 'Enviado';
    const STATUS_FALLIDO = 'Fallido';
}

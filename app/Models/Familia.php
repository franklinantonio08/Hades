<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familia';

    protected $fillable = ['codigo', 'infoextra', 'usuarioId'];

    // Relación opcional (si tienes el modelo de personas)
    public function personas()
    {
        return $this->hasMany(\App\Models\SolicitudCambioPersona::class, 'familiaId');
    }
}

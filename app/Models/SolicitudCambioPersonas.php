<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioPersonas extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_cambio_personas';

    protected $fillable = [
        'solicitud_id',
        'es_titular',
        'parentesco',
        'num_filiacion',
        'documento_tipo',
        'documento_numero',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'genero',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioResidencia::class, 'solicitud_id');
    }

    public function archivos()
    {
        // relación opcional si quieres acceder a los archivos por persona
        return $this->hasMany(SolicitudCambioArchivos::class, 'persona_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioArchivos extends Model
{
    use HasFactory;
    
    protected $table = "solicitudes_cambio_archivos";

    protected $fillable = [
        'solicitud_id',
        'tipo',
        'ruta',
        'nombre_original',
        'mime',
        'tamano',
        'usuario_id',
        'estatus',
    ];

    protected $casts = [
        'tamano'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioResidencia::class, 'solicitud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // URL pública (disk=public)
    public function getUrlAttribute()
    {
        return $this->ruta ? Storage::disk('public')->url($this->ruta) : null;
    }
}

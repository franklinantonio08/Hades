<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioEstados extends Model
{
    use HasFactory;
    
    protected $table = "solicitudes_cambio_estados";

    public $timestamps = false;

    protected $fillable = [
        'solicitud_id',
        'estatus',
        'comentario',
        'usuario_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioResidencia::class, 'solicitud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

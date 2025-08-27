<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioResidencia extends Model
{
    use HasFactory;

    protected $table = "solicitudes_cambio_residencia";

    public const ESTADOS = [
        'Recibida',
        'En revisión',
        'Observada',
        'Aprobada - con pago',
        'Aprobada - sin pago',
        'Multa emitida',
        'Rechazada',
        'Cancelada',
    ];

    protected $fillable = [
        'codigo',
        'usuario_id',
        'inversionista',
        'provincia_id',
        'distrito_id',
        'corregimiento_id',
        'barrio',
        'calle',
        'numero_casa',
        'nombre_edificio',
        'piso',
        'apartamento',
        'nombre_hotel',
        'punto_referencia',
        'domicilio_opcion',
        'recibo_tipo',
        'comentario',
        'multa_id',
        'estatus',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function archivos()
    {
        return $this->hasMany(SolicitudCambioArchivos::class, 'solicitud_id');
    }

    public function estados()
    {
        return $this->hasMany(SolicitudCambioEstados::class, 'solicitud_id')->orderByDesc('id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function multa()
    {
        return $this->belongsTo(Multa::class, 'multa_id');
    }

    // Scopes útiles
    public function scopeEstatus($q, string $estatus)
    {
        return $q->where('estatus', $estatus);
    }

    public function scopeCodigo($q, string $codigo)
    {
        return $q->where('codigo', $codigo);
    }
}

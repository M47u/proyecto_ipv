<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'vivienda_id',
        'user_id',
        'inspeccion_id',
        'titulo',
        'reclamante_nombre',
        'reclamante_telefono',
        'reclamante_email',
        'fecha_reclamo',
        'tipo_reclamo',
        'descripcion',
        'prioridad',
        'estado',
        'fecha_resolucion',
        'notas_resolucion',
    ];

    protected $casts = [
        'fecha_reclamo' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }

    /**
     * Scopes
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'en_proceso']);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Accessors
     */
    public function getPrioridadColorAttribute()
    {
        return match($this->prioridad) {
            'baja' => 'secondary',
            'media' => 'info',
            'alta' => 'warning',
            'urgente' => 'danger',
            default => 'secondary',
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'en_proceso' => 'info',
            'resuelto' => 'success',
            'rechazado' => 'danger',
            default => 'secondary',
        };
    }
}

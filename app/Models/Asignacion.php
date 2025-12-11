<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';

    protected $fillable = [
        'vivienda_id',
        'inspector_id',
        'asignado_por',
        'fecha_asignacion',
        'fecha_limite',
        'estado',
        'prioridad',
        'notas',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_limite' => 'date',
    ];

    /**
     * Relaciones
     */
    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    /**
     * Scopes
     */
    public function scopePorInspector($query, $inspectorId)
    {
        return $query->where('inspector_id', $inspectorId);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'en_progreso']);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    /**
     * Accessors
     */
    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'en_progreso' => 'info',
            'completada' => 'success',
            'cancelada' => 'secondary',
            default => 'secondary',
        };
    }

    public function getPrioridadColorAttribute()
    {
        return match($this->prioridad) {
            'baja' => 'secondary',
            'media' => 'primary',
            'alta' => 'warning',
            'urgente' => 'danger',
            default => 'secondary',
        };
    }

    public function getEstadoTextAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            default => $this->estado,
        };
    }

    public function getPrioridadTextAttribute()
    {
        return match($this->prioridad) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente',
            default => $this->prioridad,
        };
    }

    public function getEstaVencidaAttribute()
    {
        if (!$this->fecha_limite) {
            return false;
        }
        
        return $this->fecha_limite->isPast() && in_array($this->estado, ['pendiente', 'en_progreso']);
    }
}

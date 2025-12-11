<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspeccionFalla extends Model
{
    use HasFactory;

    protected $table = 'inspeccion_fallas';

    public $timestamps = false;

    protected $fillable = [
        'inspeccion_id',
        'categoria',
        'descripcion',
        'gravedad',
        'ubicacion',
        'requiere_accion_inmediata',
    ];

    protected $casts = [
        'requiere_accion_inmediata' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }

    /**
     * Scopes
     */
    public function scopePorGravedad($query, $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }

    public function scopeAccionInmediata($query)
    {
        return $query->where('requiere_accion_inmediata', true);
    }

    /**
     * Accessors
     */
    public function getGravedadColorAttribute()
    {
        return match($this->gravedad) {
            'leve' => 'info',
            'moderada' => 'warning',
            'grave' => 'danger',
            'critica' => 'dark',
            default => 'secondary',
        };
    }

    public function getCategoriaTextAttribute()
    {
        return match($this->categoria) {
            'estructura' => 'Estructura',
            'electrica' => 'Eléctrica',
            'sanitaria' => 'Sanitaria',
            'gas' => 'Gas',
            'pintura' => 'Pintura',
            'aberturas' => 'Aberturas',
            'pisos' => 'Pisos',
            'otras' => 'Otras',
            default => $this->categoria,
        };
    }
}

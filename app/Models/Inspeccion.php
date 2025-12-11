<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    use HasFactory;

    protected $table = 'inspecciones';

    protected $fillable = [
        'vivienda_id',
        'inspector_id',
        'tipo_inspeccion',
        'fecha_inspeccion',
        'estado_general',
        'es_habitable',
        'latitud',
        'longitud',
        'precision_gps',
        'estado_estructura',
        'estado_instalacion_electrica',
        'estado_instalacion_sanitaria',
        'estado_instalacion_gas',
        'estado_pintura',
        'estado_aberturas',
        'estado_pisos',
        'observaciones',
        'conclusiones',
        'requiere_seguimiento',
        'fecha_proximo_seguimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_inspeccion' => 'datetime',
        'fecha_proximo_seguimiento' => 'date',
        'es_habitable' => 'boolean',
        'requiere_seguimiento' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'precision_gps' => 'integer',
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

    public function fotos()
    {
        return $this->hasMany(InspeccionFoto::class);
    }

    public function fallas()
    {
        return $this->hasMany(InspeccionFalla::class);
    }

    public function reclamos()
    {
        return $this->hasMany(Reclamo::class);
    }

    /**
     * Scopes
     */
    public function scopePorInspector($query, $inspectorId)
    {
        return $query->where('inspector_id', $inspectorId);
    }

    public function scopePorFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_inspeccion', [$desde, $hasta]);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_inspeccion', $tipo);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeConUbicacion($query)
    {
        return $query->whereNotNull('latitud')->whereNotNull('longitud');
    }

    /**
     * Accessors
     */
    public function getEstadoGeneralColorAttribute()
    {
        return match($this->estado_general) {
            'excelente' => 'success',
            'bueno' => 'primary',
            'regular' => 'warning',
            'malo' => 'danger',
            'critico' => 'dark',
            default => 'secondary',
        };
    }

    public function getTipoInspeccionTextAttribute()
    {
        return match($this->tipo_inspeccion) {
            'inicial' => 'Inicial',
            'seguimiento' => 'Seguimiento',
            'reclamo' => 'Reclamo',
            'pre_entrega' => 'Pre-Entrega',
            'final' => 'Final',
            default => $this->tipo_inspeccion,
        };
    }

    public function getHasUbicacionAttribute()
    {
        return !is_null($this->latitud) && !is_null($this->longitud);
    }
}

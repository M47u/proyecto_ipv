<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vivienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'direccion',
        'barrio',
        'ciudad',
        'provincia',
        'tipo_vivienda',
        'categoria_vivienda',
        'superficie_cubierta',
        'superficie_terreno',
        'cantidad_ambientes',
        'propietario_actual',
        'telefono_contacto',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'superficie_cubierta' => 'decimal:2',
        'superficie_terreno' => 'decimal:2',
        'cantidad_ambientes' => 'integer',
    ];

    /**
     * Relaciones
     */
    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class);
    }

    public function reclamos()
    {
        return $this->hasMany(Reclamo::class);
    }

    public function ultimaInspeccion()
    {
        return $this->hasOne(Inspeccion::class)->latestOfMany('fecha_inspeccion');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    public function asignacionActual()
    {
        return $this->hasOne(Asignacion::class)
                    ->whereIn('estado', ['pendiente', 'en_progreso'])
                    ->latest('fecha_asignacion');
    }


    /**
     * Scopes
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_vivienda', $tipo);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('codigo', 'like', "%{$termino}%")
              ->orWhere('direccion', 'like', "%{$termino}%");
        });
    }

    /**
     * Accessors
     */
    public function getTipoViviendaTextAttribute()
    {
        return match($this->tipo_vivienda) {
            'proxima_entrega' => 'Próxima Entrega',
            'entregada' => 'Entregada',
            'recuperada' => 'Recuperada',
            default => $this->tipo_vivienda,
        };
    }

    public function getCantidadInspeccionesAttribute()
    {
        return $this->inspecciones()->count();
    }
}

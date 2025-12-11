<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InspeccionFoto extends Model
{
    use HasFactory;

    protected $table = 'inspeccion_fotos';

    public $timestamps = false;

    protected $fillable = [
        'inspeccion_id',
        'ruta_archivo',
        'nombre_original',
        'tipo_foto',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    /**
     * Relaciones
     */
    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }

    /**
     * Accessors
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->ruta_archivo);
    }

    public function getTipoFotoTextAttribute()
    {
        return match($this->tipo_foto) {
            'general' => 'General',
            'estructura' => 'Estructura',
            'instalaciones' => 'Instalaciones',
            'detalle_falla' => 'Detalle de Falla',
            'otra' => 'Otra',
            default => $this->tipo_foto,
        };
    }
}

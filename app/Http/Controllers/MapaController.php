<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\User;
use Illuminate\Http\Request;

class MapaController extends Controller
{
    public function index()
    {
        // Obtener inspectores para el filtro
        $inspectores = User::where('role', 'inspector')
            ->orWhere('role', 'administrador')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('mapa.index', compact('inspectores'));
    }

    public function getInspecciones(Request $request)
    {
        $query = Inspeccion::with(['vivienda', 'inspector'])
            ->conUbicacion(); // Solo inspecciones con coordenadas

        // Filtro por estado general
        if ($request->filled('estado')) {
            $query->where('estado_general', $request->estado);
        }

        // Filtro por tipo de inspección
        if ($request->filled('tipo')) {
            $query->where('tipo_inspeccion', $request->tipo);
        }

        // Filtro por inspector
        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_inspeccion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inspeccion', '<=', $request->fecha_hasta);
        }

        // Filtro por habitabilidad
        if ($request->filled('es_habitable')) {
            $query->where('es_habitable', $request->es_habitable === 'true');
        }

        $inspecciones = $query->get();

        // Convertir a formato GeoJSON
        $features = $inspecciones->map(function ($inspeccion) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float) $inspeccion->longitud,
                        (float) $inspeccion->latitud
                    ]
                ],
                'properties' => [
                    'id' => $inspeccion->id,
                    'vivienda_codigo' => $inspeccion->vivienda->codigo ?? 'N/A',
                    'vivienda_direccion' => $inspeccion->vivienda->direccion ?? 'N/A',
                    'vivienda_barrio' => $inspeccion->vivienda->barrio ?? 'N/A',
                    'inspector_nombre' => $inspeccion->inspector->name ?? 'N/A',
                    'tipo_inspeccion' => $inspeccion->tipo_inspeccion,
                    'tipo_inspeccion_text' => $inspeccion->tipo_inspeccion_text,
                    'fecha_inspeccion' => $inspeccion->fecha_inspeccion->format('d/m/Y H:i'),
                    'estado_general' => $inspeccion->estado_general,
                    'estado_general_color' => $inspeccion->estado_general_color,
                    'es_habitable' => $inspeccion->es_habitable,
                    'observaciones' => $inspeccion->observaciones,
                    'precision_gps' => $inspeccion->precision_gps,
                ]
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function getHeatmapData(Request $request)
    {
        $query = Inspeccion::conUbicacion();

        // Aplicar los mismos filtros que getInspecciones
        if ($request->filled('estado')) {
            $query->where('estado_general', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_inspeccion', $request->tipo);
        }
        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_inspeccion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inspeccion', '<=', $request->fecha_hasta);
        }
        if ($request->filled('es_habitable')) {
            $query->where('es_habitable', $request->es_habitable === 'true');
        }

        $inspecciones = $query->get(['latitud', 'longitud', 'estado_general']);

        // Convertir a formato para heatmap [lat, lng, intensity]
        $heatmapData = $inspecciones->map(function ($inspeccion) {
            // Intensidad basada en el estado (peor estado = mayor intensidad)
            $intensity = match ($inspeccion->estado_general) {
                'critico' => 1.0,
                'malo' => 0.8,
                'regular' => 0.6,
                'bueno' => 0.4,
                'excelente' => 0.2,
                default => 0.5,
            };

            return [
                (float) $inspeccion->latitud,
                (float) $inspeccion->longitud,
                $intensity
            ];
        });

        return response()->json($heatmapData);
    }
}

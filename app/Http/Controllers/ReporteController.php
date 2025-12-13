<?php

namespace App\Http\Controllers;

use App\Models\Vivienda;
use App\Models\Inspeccion;
use App\Models\Reclamo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvolucionViviendaExport;

class ReporteController extends Controller
{
    /**
     * Página principal de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Mostrar formulario de búsqueda para evolución de vivienda
     */
    public function evolucionVivienda($id = null)
    {
        if ($id) {
            return $this->generarEvolucionVivienda($id);
        }

        // Mostrar formulario de búsqueda
        $viviendas = Vivienda::orderBy('codigo')->get(['id', 'codigo', 'direccion']);
        return view('reportes.evolucion-vivienda-form', compact('viviendas'));
    }

    /**
     * Generar vista previa del reporte de evolución
     */
    public function generarEvolucionVivienda($id)
    {
        $vivienda = Vivienda::with([
            'inspecciones' => function($query) {
                $query->orderBy('fecha_inspeccion', 'asc');
            },
            'inspecciones.inspector',
            'inspecciones.fotos',
            'inspecciones.fallas',
            'reclamos' => function($query) {
                $query->orderBy('fecha_reclamo', 'desc');
            },
            'asignaciones' => function($query) {
                $query->orderBy('fecha_asignacion', 'desc');
            },
            'asignaciones.inspector'
        ])->findOrFail($id);

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticasVivienda($vivienda);
        
        // Preparar datos para gráficos
        $datosGraficos = $this->prepararDatosGraficos($vivienda);

        return view('reportes.evolucion-vivienda', compact('vivienda', 'estadisticas', 'datosGraficos'));
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarEvolucionPDF($id)
    {
        $vivienda = Vivienda::with([
            'inspecciones' => function($query) {
                $query->orderBy('fecha_inspeccion', 'asc');
            },
            'inspecciones.inspector',
            'inspecciones.fallas',
            'reclamos'
        ])->findOrFail($id);

        $estadisticas = $this->calcularEstadisticasVivienda($vivienda);
        $datosGraficos = $this->prepararDatosGraficos($vivienda);

        $pdf = Pdf::loadView('reportes.pdf.evolucion-vivienda', compact('vivienda', 'estadisticas', 'datosGraficos'));
        
        $pdf->setPaper('a4', 'portrait');
        
        $nombreArchivo = 'evolucion_vivienda_' . $vivienda->codigo . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarEvolucionExcel($id)
    {
        $vivienda = Vivienda::with([
            'inspecciones' => function($query) {
                $query->orderBy('fecha_inspeccion', 'asc');
            },
            'inspecciones.inspector',
            'inspecciones.fallas',
            'reclamos'
        ])->findOrFail($id);

        $nombreArchivo = 'evolucion_vivienda_' . $vivienda->codigo . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new EvolucionViviendaExport($vivienda), $nombreArchivo);
    }

    /**
     * Calcular estadísticas de la vivienda
     */
    private function calcularEstadisticasVivienda($vivienda)
    {
        $inspecciones = $vivienda->inspecciones;
        
        return [
            'total_inspecciones' => $inspecciones->count(),
            'total_reclamos' => $vivienda->reclamos->count(),
            'reclamos_pendientes' => $vivienda->reclamos->where('estado', 'pendiente')->count(),
            'reclamos_resueltos' => $vivienda->reclamos->where('estado', 'resuelto')->count(),
            'primera_inspeccion' => $inspecciones->first()?->fecha_inspeccion?->format('d/m/Y'),
            'ultima_inspeccion' => $inspecciones->last()?->fecha_inspeccion?->format('d/m/Y'),
            'estado_actual' => $inspecciones->last()?->estado_general ?? 'Sin inspecciones',
            'es_habitable' => $inspecciones->last()?->es_habitable ?? null,
            'requiere_seguimiento' => $inspecciones->last()?->requiere_seguimiento ?? false,
            'total_fallas' => $inspecciones->sum(function($i) { return $i->fallas->count(); }),
            'fallas_criticas' => $inspecciones->sum(function($i) { 
                return $i->fallas->where('gravedad', 'critica')->count(); 
            }),
            'tipos_inspeccion' => $inspecciones->groupBy('tipo_inspeccion')->map->count(),
            'promedio_dias_entre_inspecciones' => $this->calcularPromedioDiasEntreInspecciones($inspecciones),
        ];
    }

    /**
     * Preparar datos para gráficos
     */
    private function prepararDatosGraficos($vivienda)
    {
        $inspecciones = $vivienda->inspecciones;

        // Timeline de estados
        $timelineEstados = $inspecciones->map(function($inspeccion) {
            return [
                'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
                'estado' => $inspeccion->estado_general,
                'color' => $this->getColorEstado($inspeccion->estado_general),
            ];
        });

        // Evolución por áreas
        $evolucionAreas = $inspecciones->map(function($inspeccion) {
            return [
                'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
                'estructura' => $this->estadoANumero($inspeccion->estado_estructura),
                'electrica' => $this->estadoANumero($inspeccion->estado_instalacion_electrica),
                'sanitaria' => $this->estadoANumero($inspeccion->estado_instalacion_sanitaria),
                'gas' => $this->estadoANumero($inspeccion->estado_instalacion_gas),
                'pintura' => $this->estadoANumero($inspeccion->estado_pintura),
                'aberturas' => $this->estadoANumero($inspeccion->estado_aberturas),
                'pisos' => $this->estadoANumero($inspeccion->estado_pisos),
            ];
        });

        // Distribución de fallas
        $fallas = $vivienda->inspecciones->flatMap->fallas;
        $distribucionFallas = $fallas->groupBy('categoria')->map->count();

        // Gravedad de fallas
        $gravedadFallas = $fallas->groupBy('gravedad')->map->count();

        return [
            'timeline_estados' => $timelineEstados,
            'evolucion_areas' => $evolucionAreas,
            'distribucion_fallas' => $distribucionFallas,
            'gravedad_fallas' => $gravedadFallas,
        ];
    }

    /**
     * Calcular promedio de días entre inspecciones
     */
    private function calcularPromedioDiasEntreInspecciones($inspecciones)
    {
        if ($inspecciones->count() < 2) {
            return 'N/A';
        }

        $dias = [];
        for ($i = 1; $i < $inspecciones->count(); $i++) {
            $dias[] = $inspecciones[$i]->fecha_inspeccion->diffInDays($inspecciones[$i-1]->fecha_inspeccion);
        }

        return round(array_sum($dias) / count($dias));
    }

    /**
     * Convertir estado a número para gráficos
     */
    private function estadoANumero($estado)
    {
        return match($estado) {
            'excelente' => 5,
            'bueno' => 4,
            'regular' => 3,
            'malo' => 2,
            'critico' => 1,
            default => 0,
        };
    }

    /**
     * Obtener color según estado
     */
    private function getColorEstado($estado)
    {
        return match($estado) {
            'excelente' => '#22c55e',
            'bueno' => '#3b82f6',
            'regular' => '#facc15',
            'malo' => '#ef4444',
            'critico' => '#991b1b',
            default => '#6b7280',
        };
    }

    // ===================================
    // OTROS REPORTES (Placeholders)
    // ===================================

    public function inspeccionesPorPeriodo(Request $request)
    {
        return redirect()->route('reportes.index')
            ->with('error', 'Módulo en desarrollo');
    }

    public function estadisticasGenerales()
    {
        return redirect()->route('reportes.index')
            ->with('error', 'Módulo en desarrollo');
    }

    public function exportarMapa(Request $request)
    {
        return redirect()->route('reportes.index')
            ->with('error', 'Módulo en desarrollo');
    }
}
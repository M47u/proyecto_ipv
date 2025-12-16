<?php

namespace App\Http\Controllers;

use App\Models\Vivienda;
use App\Models\Inspeccion;
use App\Models\Reclamo;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvolucionViviendaExport;
use App\Exports\InspeccionesPeriodoExport;

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
            'inspecciones' => function ($query) {
                $query->orderBy('fecha_inspeccion', 'asc');
            },
            'inspecciones.inspector',
            'inspecciones.fotos',
            'inspecciones.fallas',
            'reclamos' => function ($query) {
                $query->orderBy('fecha_reclamo', 'desc');
            },
            'asignaciones' => function ($query) {
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
            'inspecciones' => function ($query) {
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
            'inspecciones' => function ($query) {
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
            'total_fallas' => $inspecciones->sum(function ($i) {
                return $i->fallas->count();
            }),
            'fallas_criticas' => $inspecciones->sum(function ($i) {
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
        $timelineEstados = $inspecciones->map(function ($inspeccion) {
            return [
                'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
                'estado' => $inspeccion->estado_general,
                'color' => $this->getColorEstado($inspeccion->estado_general),
            ];
        });

        // Evolución por áreas
        $evolucionAreas = $inspecciones->map(function ($inspeccion) {
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
            $dias[] = $inspecciones[$i]->fecha_inspeccion->diffInDays($inspecciones[$i - 1]->fecha_inspeccion);
        }

        return round(array_sum($dias) / count($dias));
    }

    /**
     * Convertir estado a número para gráficos
     */
    private function estadoANumero($estado)
    {
        return match ($estado) {
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
        return match ($estado) {
            'excelente' => '#22c55e',
            'bueno' => '#3b82f6',
            'regular' => '#facc15',
            'malo' => '#ef4444',
            'critico' => '#991b1b',
            default => '#6b7280',
        };
    }

    /**
     * Mostrar formulario de inspecciones por período
     */
    public function inspeccionesPorPeriodo(Request $request)
    {
        // Si no hay filtros, mostrar formulario
        if (!$request->has('fecha_desde')) {
            $inspectores = User::inspectores()->activos()->get();
            $tiposVivienda = Vivienda::select('tipo_vivienda')
                ->distinct()
                ->pluck('tipo_vivienda');

            return view('reportes.periodo-form', compact('inspectores', 'tiposVivienda'));
        }

        // Procesar filtros y generar reporte
        return $this->generarReportePeriodo($request);
    }

    /**
     * Generar reporte de inspecciones por período
     */
    private function generarReportePeriodo(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $query = Inspeccion::with(['vivienda', 'inspector'])
            ->whereBetween('fecha_inspeccion', [
                $request->fecha_desde . ' 00:00:00',
                $request->fecha_hasta . ' 23:59:59'
            ]);

        // Aplicar filtros adicionales
        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        if ($request->filled('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        if ($request->filled('estado_general')) {
            $query->where('estado_general', $request->estado_general);
        }

        if ($request->filled('tipo_vivienda')) {
            $query->whereHas('vivienda', function ($q) use ($request) {
                $q->where('tipo_vivienda', $request->tipo_vivienda);
            });
        }

        if ($request->filled('es_habitable')) {
            $query->where('es_habitable', $request->es_habitable === '1');
        }

        $inspecciones = $query->orderBy('fecha_inspeccion', 'desc')->get();

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticasPeriodo($inspecciones, $request);

        // Preparar datos para gráficos
        $datosGraficos = $this->prepararDatosGraficosPeriodo($inspecciones);

        return view('reportes.periodo', compact('inspecciones', 'estadisticas', 'datosGraficos', 'request'));
    }

    /**
     * Calcular estadísticas del período
     */
    private function calcularEstadisticasPeriodo($inspecciones, $request)
    {
        return [
            'total_inspecciones' => $inspecciones->count(),
            'total_viviendas' => $inspecciones->pluck('vivienda_id')->unique()->count(),
            'total_inspectores' => $inspecciones->pluck('inspector_id')->unique()->count(),
            'habitables' => $inspecciones->where('es_habitable', true)->count(),
            'no_habitables' => $inspecciones->where('es_habitable', false)->count(),
            'porcentaje_habitables' => $inspecciones->count() > 0
                ? round(($inspecciones->where('es_habitable', true)->count() / $inspecciones->count()) * 100, 1)
                : 0,
            'total_fallas' => $inspecciones->sum(function ($i) {
                return $i->fallas->count();
            }),
            'fallas_criticas' => $inspecciones->sum(function ($i) {
                return $i->fallas->where('gravedad', 'critica')->count();
            }),
            'requieren_seguimiento' => $inspecciones->where('requiere_seguimiento', true)->count(),
            'por_tipo' => $inspecciones->groupBy('tipo_inspeccion')->map->count(),
            'por_estado' => $inspecciones->groupBy('estado_general')->map->count(),
            'por_inspector' => $inspecciones->groupBy('inspector_id')->map(function ($items) {
                return [
                    'nombre' => $items->first()->inspector->name,
                    'total' => $items->count()
                ];
            }),
            'fecha_desde' => $request->fecha_desde,
            'fecha_hasta' => $request->fecha_hasta,
            'promedio_por_dia' => $this->calcularPromedioPorDia($inspecciones, $request),
        ];
    }

    /**
     * Preparar datos para gráficos del período
     */
    private function prepararDatosGraficosPeriodo($inspecciones)
    {
        // Inspecciones por día
        $porDia = $inspecciones->groupBy(function ($inspeccion) {
            return $inspeccion->fecha_inspeccion->format('Y-m-d');
        })->map->count()->sortKeys();

        // Por tipo de inspección
        $porTipo = $inspecciones->groupBy('tipo_inspeccion')->map->count();

        // Por estado general
        $porEstado = $inspecciones->groupBy('estado_general')->map->count();

        // Por inspector
        $porInspector = $inspecciones->groupBy('inspector_id')->map(function ($items) {
            return [
                'nombre' => $items->first()->inspector->name,
                'total' => $items->count()
            ];
        })->sortByDesc('total');

        // Habitabilidad
        $habitabilidad = [
            'habitables' => $inspecciones->where('es_habitable', true)->count(),
            'no_habitables' => $inspecciones->where('es_habitable', false)->count(),
        ];

        return [
            'por_dia' => $porDia,
            'por_tipo' => $porTipo,
            'por_estado' => $porEstado,
            'por_inspector' => $porInspector,
            'habitabilidad' => $habitabilidad,
        ];
    }

    /**
     * Calcular promedio de inspecciones por día
     */
    private function calcularPromedioPorDia($inspecciones, $request)
    {
        if ($inspecciones->count() === 0) {
            return 0;
        }

        $fechaInicio = \Carbon\Carbon::parse($request->fecha_desde);
        $fechaFin = \Carbon\Carbon::parse($request->fecha_hasta);
        $diasTotales = $fechaFin->diffInDays($fechaInicio) + 1;

        return round($inspecciones->count() / $diasTotales, 1);
    }

    /**
     * Exportar reporte período a PDF
     */
    public function exportarPeriodoPDF(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
        ]);

        $query = Inspeccion::with(['vivienda', 'inspector'])
            ->whereBetween('fecha_inspeccion', [
                $request->fecha_desde . ' 00:00:00',
                $request->fecha_hasta . ' 23:59:59'
            ]);

        // Aplicar los mismos filtros
        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }
        if ($request->filled('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }
        if ($request->filled('estado_general')) {
            $query->where('estado_general', $request->estado_general);
        }

        $inspecciones = $query->orderBy('fecha_inspeccion', 'desc')->get();
        $estadisticas = $this->calcularEstadisticasPeriodo($inspecciones, $request);

        $pdf = Pdf::loadView('reportes.pdf.periodo', compact('inspecciones', 'estadisticas', 'request'));
        $pdf->setPaper('a4', 'landscape');

        $nombreArchivo = 'inspecciones_' . $request->fecha_desde . '_a_' . $request->fecha_hasta . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte período a Excel
     */
    public function exportarPeriodoExcel(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
        ]);

        $query = Inspeccion::with(['vivienda', 'inspector', 'fallas'])
            ->whereBetween('fecha_inspeccion', [
                $request->fecha_desde . ' 00:00:00',
                $request->fecha_hasta . ' 23:59:59'
            ]);

        // Aplicar filtros
        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }
        if ($request->filled('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        $inspecciones = $query->orderBy('fecha_inspeccion', 'desc')->get();

        $nombreArchivo = 'inspecciones_' . $request->fecha_desde . '_a_' . $request->fecha_hasta . '.xlsx';

        return Excel::download(new InspeccionesPeriodoExport($inspecciones, $request), $nombreArchivo);
    }

    /**
     * Dashboard Ejecutivo
     */
    public function dashboardEjecutivo(Request $request)
    {
        // Período por defecto: mes actual
        $mesActual = $request->filled('mes') ? $request->mes : now()->format('Y-m');
        $fechaInicio = \Carbon\Carbon::parse($mesActual . '-01')->startOfMonth();
        $fechaFin = \Carbon\Carbon::parse($mesActual . '-01')->endOfMonth();

        // Período anterior (mes pasado) para comparación
        $fechaInicioAnterior = $fechaInicio->copy()->subMonth();
        $fechaFinAnterior = $fechaFin->copy()->subMonth();

        // Obtener datos del período actual
        $datosActuales = $this->obtenerDatosPeriodo($fechaInicio, $fechaFin);

        // Obtener datos del período anterior
        $datosAnteriores = $this->obtenerDatosPeriodo($fechaInicioAnterior, $fechaFinAnterior);

        // Calcular KPIs y comparaciones
        $kpis = $this->calcularKPIs($datosActuales, $datosAnteriores);

        // Obtener alertas y notificaciones
        $alertas = $this->obtenerAlertas();

        // Datos para gráficos de tendencias (últimos 6 meses)
        $tendencias = $this->obtenerTendencias();

        // Top inspectores
        $topInspectores = $this->obtenerTopInspectores($fechaInicio, $fechaFin);

        // Viviendas críticas
        $viviendasCriticas = $this->obtenerViviendasCriticas();

        return view('reportes.dashboard-ejecutivo', compact(
            'kpis',
            'alertas',
            'tendencias',
            'topInspectores',
            'viviendasCriticas',
            'mesActual',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Obtener datos de un período
     */
    private function obtenerDatosPeriodo($fechaInicio, $fechaFin)
    {
        $inspecciones = Inspeccion::with(['vivienda', 'inspector', 'fallas'])
            ->whereBetween('fecha_inspeccion', [$fechaInicio, $fechaFin])
            ->get();

        $reclamos = Reclamo::whereBetween('fecha_reclamo', [$fechaInicio, $fechaFin])
            ->get();

        return [
            'inspecciones' => $inspecciones,
            'total_inspecciones' => $inspecciones->count(),
            'viviendas_inspeccionadas' => $inspecciones->pluck('vivienda_id')->unique()->count(),
            'habitables' => $inspecciones->where('es_habitable', true)->count(),
            'no_habitables' => $inspecciones->where('es_habitable', false)->count(),
            'total_fallas' => $inspecciones->sum(function ($i) {
                return $i->fallas->count();
            }),
            'fallas_criticas' => $inspecciones->sum(function ($i) {
                return $i->fallas->where('gravedad', 'critica')->count();
            }),
            'total_reclamos' => $reclamos->count(),
            'reclamos_pendientes' => $reclamos->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'reclamos_resueltos' => $reclamos->where('estado', 'resuelto')->count(),
            'inspectores_activos' => $inspecciones->pluck('inspector_id')->unique()->count(),
        ];
    }

    /**
     * Calcular KPIs con comparación
     */
    private function calcularKPIs($actual, $anterior)
    {
        return [
            'inspecciones' => [
                'valor' => $actual['total_inspecciones'],
                'cambio' => $this->calcularCambioPercentual(
                    $anterior['total_inspecciones'],
                    $actual['total_inspecciones']
                ),
                'tendencia' => $this->determinarTendencia(
                    $anterior['total_inspecciones'],
                    $actual['total_inspecciones']
                )
            ],
            'habitabilidad' => [
                'valor' => $actual['total_inspecciones'] > 0
                    ? round(($actual['habitables'] / $actual['total_inspecciones']) * 100, 1)
                    : 0,
                'cambio' => $this->calcularCambioPercentualHabitabilidad($anterior, $actual),
                'tendencia' => $this->determinarTendenciaHabitabilidad($anterior, $actual)
            ],
            'reclamos' => [
                'valor' => $actual['total_reclamos'],
                'pendientes' => $actual['reclamos_pendientes'],
                'cambio' => $this->calcularCambioPercentual(
                    $anterior['total_reclamos'],
                    $actual['total_reclamos']
                ),
                'tendencia' => $this->determinarTendencia(
                    $anterior['total_reclamos'],
                    $actual['total_reclamos']
                )
            ],
            'fallas' => [
                'valor' => $actual['total_fallas'],
                'criticas' => $actual['fallas_criticas'],
                'cambio' => $this->calcularCambioPercentual(
                    $anterior['total_fallas'],
                    $actual['total_fallas']
                ),
                'tendencia' => $this->determinarTendencia(
                    $anterior['total_fallas'],
                    $actual['total_fallas']
                )
            ],
            'productividad' => [
                'valor' => $actual['inspectores_activos'] > 0
                    ? round($actual['total_inspecciones'] / $actual['inspectores_activos'], 1)
                    : 0,
                'inspectores' => $actual['inspectores_activos']
            ]
        ];
    }

    /**
     * Calcular cambio percentual
     */
    private function calcularCambioPercentual($anterior, $actual)
    {
        if ($anterior == 0) {
            return $actual > 0 ? 100 : 0;
        }

        return round((($actual - $anterior) / $anterior) * 100, 1);
    }

    /**
     * Determinar tendencia
     */
    private function determinarTendencia($anterior, $actual)
    {
        if ($actual > $anterior)
            return 'up';
        if ($actual < $anterior)
            return 'down';
        return 'stable';
    }

    /**
     * Calcular cambio en habitabilidad
     */
    private function calcularCambioPercentualHabitabilidad($anterior, $actual)
    {
        $porcentajeAnterior = $anterior['total_inspecciones'] > 0
            ? ($anterior['habitables'] / $anterior['total_inspecciones']) * 100
            : 0;

        $porcentajeActual = $actual['total_inspecciones'] > 0
            ? ($actual['habitables'] / $actual['total_inspecciones']) * 100
            : 0;

        return round($porcentajeActual - $porcentajeAnterior, 1);
    }

    /**
     * Determinar tendencia de habitabilidad
     */
    private function determinarTendenciaHabitabilidad($anterior, $actual)
    {
        $porcentajeAnterior = $anterior['total_inspecciones'] > 0
            ? ($anterior['habitables'] / $anterior['total_inspecciones']) * 100
            : 0;

        $porcentajeActual = $actual['total_inspecciones'] > 0
            ? ($actual['habitables'] / $actual['total_inspecciones']) * 100
            : 0;

        if ($porcentajeActual > $porcentajeAnterior)
            return 'up';
        if ($porcentajeActual < $porcentajeAnterior)
            return 'down';
        return 'stable';
    }

    /**
     * Obtener alertas del sistema
     */
    private function obtenerAlertas()
    {
        $alertas = collect();

        // Viviendas no habitables sin seguimiento
        $noHabitablesSinSeguimiento = Inspeccion::where('es_habitable', false)
            ->where('requiere_seguimiento', true)
            ->whereNull('fecha_proximo_seguimiento')
            ->count();

        if ($noHabitablesSinSeguimiento > 0) {
            $alertas->push([
                'tipo' => 'danger',
                'icono' => 'exclamation-triangle',
                'titulo' => 'Viviendas No Habitables Sin Seguimiento',
                'mensaje' => "{$noHabitablesSinSeguimiento} viviendas requieren atención inmediata",
                'accion' => 'Ver Detalle',
                'url' => route('inspecciones.index') . '?es_habitable=0'
            ]);
        }

        // Reclamos pendientes antiguos (más de 30 días)
        $reclamosPendientesAntiguos = Reclamo::where('estado', 'pendiente')
            ->where('fecha_reclamo', '<', now()->subDays(30))
            ->count();

        if ($reclamosPendientesAntiguos > 0) {
            $alertas->push([
                'tipo' => 'warning',
                'icono' => 'clock',
                'titulo' => 'Reclamos Pendientes Antiguos',
                'mensaje' => "{$reclamosPendientesAntiguos} reclamos llevan más de 30 días sin resolver",
                'accion' => 'Ver Reclamos',
                'url' => route('reclamos.index') . '?estado=pendiente'
            ]);
        }

        // Fallas críticas sin resolver
        $fallasCriticas = Inspeccion::whereHas('fallas', function ($q) {
            $q->where('gravedad', 'critica')
                ->where('requiere_accion_inmediata', true);
        })->where('created_at', '>=', now()->subDays(7))->count();

        if ($fallasCriticas > 0) {
            $alertas->push([
                'tipo' => 'danger',
                'icono' => 'tools',
                'titulo' => 'Fallas Críticas Detectadas',
                'mensaje' => "{$fallasCriticas} inspecciones con fallas críticas esta semana",
                'accion' => 'Ver Inspecciones',
                'url' => route('inspecciones.index')
            ]);
        }

        // Seguimientos vencidos
        $seguimientosVencidos = Inspeccion::where('requiere_seguimiento', true)
            ->where('fecha_proximo_seguimiento', '<', now())
            ->count();

        if ($seguimientosVencidos > 0) {
            $alertas->push([
                'tipo' => 'info',
                'icono' => 'calendar-x',
                'titulo' => 'Seguimientos Vencidos',
                'mensaje' => "{$seguimientosVencidos} viviendas requieren inspección de seguimiento",
                'accion' => 'Ver Calendario',
                'url' => route('inspecciones.index')
            ]);
        }

        return $alertas;
    }

    /**
     * Obtener tendencias de los últimos 6 meses
     */
    private function obtenerTendencias()
    {
        $meses = [];
        $dataInspecciones = [];
        $dataHabitabilidad = [];
        $dataReclamos = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mesInicio = $fecha->copy()->startOfMonth();
            $mesFin = $fecha->copy()->endOfMonth();

            $meses[] = $fecha->locale('es')->isoFormat('MMM YYYY');

            // Inspecciones
            $inspecciones = Inspeccion::whereBetween('fecha_inspeccion', [$mesInicio, $mesFin])->get();
            $dataInspecciones[] = $inspecciones->count();

            // Habitabilidad
            $habitables = $inspecciones->where('es_habitable', true)->count();
            $total = $inspecciones->count();
            $dataHabitabilidad[] = $total > 0 ? round(($habitables / $total) * 100, 1) : 0;

            // Reclamos
            $reclamos = Reclamo::whereBetween('fecha_reclamo', [$mesInicio, $mesFin])->count();
            $dataReclamos[] = $reclamos;
        }

        return [
            'labels' => $meses,
            'inspecciones' => $dataInspecciones,
            'habitabilidad' => $dataHabitabilidad,
            'reclamos' => $dataReclamos
        ];
    }

    /**
     * Obtener top inspectores del período
     */
    private function obtenerTopInspectores($fechaInicio, $fechaFin)
    {
        return Inspeccion::selectRaw('inspector_id, COUNT(*) as total')
            ->whereBetween('fecha_inspeccion', [$fechaInicio, $fechaFin])
            ->with('inspector')
            ->groupBy('inspector_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->inspector->name,
                    'total' => $item->total,
                    'email' => $item->inspector->email
                ];
            });
    }

    /**
     * Obtener viviendas en estado crítico
     */
    private function obtenerViviendasCriticas()
    {
        return Vivienda::whereHas('ultimaInspeccion', function ($q) {
            $q->where('es_habitable', false)
                ->orWhere('estado_general', 'critico');
        })
            ->with([
                'ultimaInspeccion',
                'reclamos' => function ($q) {
                    $q->whereIn('estado', ['pendiente', 'en_proceso']);
                }
            ])
            ->limit(10)
            ->get()
            ->map(function ($vivienda) {
                return [
                    'codigo' => $vivienda->codigo,
                    'direccion' => $vivienda->direccion,
                    'estado' => $vivienda->ultimaInspeccion->estado_general,
                    'habitable' => $vivienda->ultimaInspeccion->es_habitable,
                    'fecha_inspeccion' => $vivienda->ultimaInspeccion->fecha_inspeccion,
                    'reclamos_activos' => $vivienda->reclamos->count(),
                    'url' => route('viviendas.show', $vivienda->id)
                ];
            });
    }

    /**
     * Exportar Dashboard a PDF
     */
    public function exportarDashboardPDF(Request $request)
    {
        $mesActual = $request->filled('mes') ? $request->mes : now()->format('Y-m');
        $fechaInicio = \Carbon\Carbon::parse($mesActual . '-01')->startOfMonth();
        $fechaFin = \Carbon\Carbon::parse($mesActual . '-01')->endOfMonth();

        $fechaInicioAnterior = $fechaInicio->copy()->subMonth();
        $fechaFinAnterior = $fechaFin->copy()->subMonth();

        $datosActuales = $this->obtenerDatosPeriodo($fechaInicio, $fechaFin);
        $datosAnteriores = $this->obtenerDatosPeriodo($fechaInicioAnterior, $fechaFinAnterior);
        $kpis = $this->calcularKPIs($datosActuales, $datosAnteriores);
        $alertas = $this->obtenerAlertas();
        $topInspectores = $this->obtenerTopInspectores($fechaInicio, $fechaFin);
        $viviendasCriticas = $this->obtenerViviendasCriticas();

        $pdf = Pdf::loadView('reportes.pdf.dashboard-ejecutivo', compact(
            'kpis',
            'alertas',
            'topInspectores',
            'viviendasCriticas',
            'mesActual',
            'fechaInicio',
            'fechaFin'
        ));

        $pdf->setPaper('a4', 'portrait');

        $nombreArchivo = 'dashboard_ejecutivo_' . $mesActual . '.pdf';
        return $pdf->download($nombreArchivo);
    }


}
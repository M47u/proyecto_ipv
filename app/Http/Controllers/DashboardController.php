<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\Reclamo;
use App\Models\Vivienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'administrador') {
            return $this->dashboardAdministrador();
        } else {
            return $this->dashboardInspector();
        }
    }

    private function dashboardAdministrador()
    {
        // Total de inspecciones del mes actual
        $inspeccionesMesActual = Inspeccion::whereMonth('fecha_inspeccion', now()->month)
            ->whereYear('fecha_inspeccion', now()->year)
            ->count();

        // Total de inspecciones del mes anterior
        $inspeccionesMesAnterior = Inspeccion::whereMonth('fecha_inspeccion', now()->subMonth()->month)
            ->whereYear('fecha_inspeccion', now()->subMonth()->year)
            ->count();

        // Inspecciones por estado
        $inspeccionesPorEstado = Inspeccion::select('estado_general', DB::raw('count(*) as total'))
            ->groupBy('estado_general')
            ->pluck('total', 'estado_general')
            ->toArray();

        // Inspecciones por tipo
        $inspeccionesPorTipo = Inspeccion::select('tipo_inspeccion', DB::raw('count(*) as total'))
            ->groupBy('tipo_inspeccion')
            ->pluck('total', 'tipo_inspeccion')
            ->toArray();

        // Reclamos pendientes
        $reclamosPendientes = Reclamo::with('vivienda')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_reclamo', 'desc')
            ->limit(5)
            ->get();

        // Últimas inspecciones
        $ultimasInspecciones = Inspeccion::with(['vivienda', 'inspector'])
            ->orderBy('fecha_inspeccion', 'desc')
            ->limit(10)
            ->get();

        // Viviendas por tipo
        $viviendasPorTipo = Vivienda::select('tipo_vivienda', DB::raw('count(*) as total'))
            ->where('estado', 'activa')
            ->groupBy('tipo_vivienda')
            ->pluck('total', 'tipo_vivienda')
            ->toArray();

        // Inspecciones por mes (últimos 6 meses)
        $inspeccionesPorMes = Inspeccion::select(
                DB::raw('DATE_FORMAT(fecha_inspeccion, "%Y-%m") as mes'),
                DB::raw('count(*) as total')
            )
            ->where('fecha_inspeccion', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Total de reclamos
        $totalReclamos = Reclamo::count();
        $reclamosPendientesCount = Reclamo::whereIn('estado', ['pendiente', 'en_proceso'])->count();

        return view('dashboard.administrador', compact(
            'inspeccionesMesActual',
            'inspeccionesMesAnterior',
            'inspeccionesPorEstado',
            'inspeccionesPorTipo',
            'reclamosPendientes',
            'ultimasInspecciones',
            'viviendasPorTipo',
            'inspeccionesPorMes',
            'totalReclamos',
            'reclamosPendientesCount'
        ));
    }

    private function dashboardInspector()
    {
        $user = auth()->user();

        // Mis inspecciones del mes
        $misInspeccionesMes = Inspeccion::where('inspector_id', $user->id)
            ->whereMonth('fecha_inspeccion', now()->month)
            ->whereYear('fecha_inspeccion', now()->year)
            ->count();

        // Mis inspecciones pendientes
        $misInspeccionesPendientes = Inspeccion::where('inspector_id', $user->id)
            ->where('estado', 'pendiente')
            ->count();

        // Próximos seguimientos
        $proximosSeguimientos = Inspeccion::with('vivienda')
            ->where('inspector_id', $user->id)
            ->where('requiere_seguimiento', true)
            ->whereNotNull('fecha_proximo_seguimiento')
            ->where('fecha_proximo_seguimiento', '>=', now())
            ->orderBy('fecha_proximo_seguimiento')
            ->limit(5)
            ->get();

        // Mis últimas inspecciones
        $misUltimasInspecciones = Inspeccion::with('vivienda')
            ->where('inspector_id', $user->id)
            ->orderBy('fecha_inspeccion', 'desc')
            ->limit(10)
            ->get();

        // Mis inspecciones por estado
        $misInspeccionesPorEstado = Inspeccion::select('estado_general', DB::raw('count(*) as total'))
            ->where('inspector_id', $user->id)
            ->groupBy('estado_general')
            ->pluck('total', 'estado_general')
            ->toArray();

        // Total de viviendas activas
        $totalViviendas = Vivienda::where('estado', 'activa')->count();

        return view('dashboard.inspector', compact(
            'misInspeccionesMes',
            'misInspeccionesPendientes',
            'proximosSeguimientos',
            'misUltimasInspecciones',
            'misInspeccionesPorEstado',
            'totalViviendas'
        ));
    }
}

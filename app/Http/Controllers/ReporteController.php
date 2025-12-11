<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function evolucionVivienda($id)
    {
        return redirect()->route('reportes.index')
            ->with('error', 'Módulo en desarrollo');
    }

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

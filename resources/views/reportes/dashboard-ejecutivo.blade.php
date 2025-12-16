@extends('layouts.app')

@section('title', 'Dashboard Ejecutivo')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Dashboard Ejecutivo</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-graph-up"></i> Dashboard Ejecutivo</h2>
        <p class="text-muted mb-0">
            {{ $fechaInicio->locale('es')->isoFormat('MMMM YYYY') }}
        </p>
    </div>
    <div class="btn-group" role="group">
        <a href="{{ route('reportes.dashboard-ejecutivo.pdf', ['mes' => $mesActual]) }}" 
           class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Exportar PDF
        </a>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalSeleccionarMes">
            <i class="bi bi-calendar"></i> Cambiar Período
        </button>
    </div>
</div>

<!-- KPIs Principales -->
<div class="row mb-4">
    <!-- KPI: Inspecciones -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Inspecciones</p>
                        <h2 class="mb-0">{{ $kpis['inspecciones']['valor'] }}</h2>
                        <small class="text-{{ $kpis['inspecciones']['cambio'] >= 0 ? 'success' : 'danger' }}">
                            <i class="bi bi-arrow-{{ $kpis['inspecciones']['tendencia'] == 'up' ? 'up' : ($kpis['inspecciones']['tendencia'] == 'down' ? 'down' : 'right') }}"></i>
                            {{ abs($kpis['inspecciones']['cambio']) }}% vs mes anterior
                        </small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class="bi bi-clipboard-check fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI: Habitabilidad -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">% Habitabilidad</p>
                        <h2 class="mb-0">{{ $kpis['habitabilidad']['valor'] }}%</h2>
                        <small class="text-{{ $kpis['habitabilidad']['cambio'] >= 0 ? 'success' : 'danger' }}">
                            <i class="bi bi-arrow-{{ $kpis['habitabilidad']['tendencia'] == 'up' ? 'up' : ($kpis['habitabilidad']['tendencia'] == 'down' ? 'down' : 'right') }}"></i>
                            {{ abs($kpis['habitabilidad']['cambio']) }}% vs mes anterior
                        </small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-3">
                        <i class="bi bi-shield-check fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI: Reclamos -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Reclamos</p>
                        <h2 class="mb-0">{{ $kpis['reclamos']['valor'] }}</h2>
                        <small class="text-warning">
                            <i class="bi bi-clock"></i>
                            {{ $kpis['reclamos']['pendientes'] }} pendientes
                        </small>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                        <i class="bi bi-megaphone fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI: Fallas -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Fallas Detectadas</p>
                        <h2 class="mb-0">{{ $kpis['fallas']['valor'] }}</h2>
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $kpis['fallas']['criticas'] }} críticas
                        </small>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded p-3">
                        <i class="bi bi-tools fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
@if($alertas->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="bi bi-bell"></i> Alertas y Notificaciones
            <span class="badge bg-danger">{{ $alertas->count() }}</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($alertas as $alerta)
            <div class="col-md-6 mb-3">
                <div class="alert alert-{{ $alerta['tipo'] }} mb-0">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-{{ $alerta['icono'] }} fs-3 me-3"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">{{ $alerta['titulo'] }}</h6>
                            <p class="mb-2">{{ $alerta['mensaje'] }}</p>
                            <a href="{{ $alerta['url'] }}" class="btn btn-sm btn-{{ $alerta['tipo'] }}">
                                {{ $alerta['accion'] }} <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Gráficos de Tendencias -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Tendencias (Últimos 6 Meses)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTendencias" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-percent"></i> Habitabilidad Mensual</h5>
            </div>
            <div class="card-body">
                <canvas id="chartHabitabilidad"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Inspectores y Viviendas Críticas -->
<div class="row mb-4">
    <!-- Top Inspectores -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-trophy"></i> Top Inspectores del Mes
                    <span class="badge bg-info">{{ $kpis['productividad']['inspectores'] }} activos</span>
                </h5>
            </div>
            <div class="card-body">
                @if($topInspectores->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Inspector</th>
                                    <th class="text-center">Inspecciones</th>
                                    <th width="40%">Distribución</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topInspectores as $index => $inspector)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <i class="bi bi-trophy-fill text-warning"></i>
                                        @elseif($index == 1)
                                            <i class="bi bi-trophy text-secondary"></i>
                                        @elseif($index == 2)
                                            <i class="bi bi-trophy text-danger"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>{{ $inspector['nombre'] }}</td>
                                    <td class="text-center"><strong>{{ $inspector['total'] }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                 style="width: {{ ($inspector['total'] / $topInspectores->first()['total']) * 100 }}%">
                                                {{ $inspector['total'] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No hay datos disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Viviendas Críticas -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-octagon"></i> Viviendas Críticas
                    <span class="badge bg-danger">{{ $viviendasCriticas->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($viviendasCriticas->count() > 0)
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @foreach($viviendasCriticas as $vivienda)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <strong>{{ $vivienda['codigo'] }}</strong>
                                        @if(!$vivienda['habitable'])
                                            <span class="badge bg-danger">No Habitable</span>
                                        @endif
                                    </h6>
                                    <p class="mb-1 text-muted small">{{ $vivienda['direccion'] }}</p>
                                    <small>
                                        <i class="bi bi-calendar"></i> 
                                        {{ $vivienda['fecha_inspeccion']->diffForHumans() }}
                                        @if($vivienda['reclamos_activos'] > 0)
                                            | <i class="bi bi-megaphone text-warning"></i> 
                                            {{ $vivienda['reclamos_activos'] }} reclamos
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-{{ $vivienda['estado'] == 'critico' ? 'danger' : 'warning' }}">
                                    {{ ucfirst($vivienda['estado']) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">No hay viviendas críticas</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Seleccionar Mes -->
<div class="modal fade" id="modalSeleccionarMes" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Período</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('reportes.dashboard-ejecutivo') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mes" class="form-label">Mes y Año</label>
                        <input type="month" class="form-control" id="mes" name="mes" 
                               value="{{ $mesActual }}" max="{{ now()->format('Y-m') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Aplicar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const tendencias = @json($tendencias);

// Gráfico: Tendencias
const ctxTendencias = document.getElementById('chartTendencias').getContext('2d');
new Chart(ctxTendencias, {
    type: 'line',
    data: {
        labels: tendencias.labels,
        datasets: [
            {
                label: 'Inspecciones',
                data: tendencias.inspecciones,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Reclamos',
                data: tendencias.reclamos,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Gráfico: Habitabilidad
const ctxHabitabilidad = document.getElementById('chartHabitabilidad').getContext('2d');
new Chart(ctxHabitabilidad, {
    type: 'line',
    data: {
        labels: tendencias.labels,
        datasets: [{
            label: '% Habitabilidad',
            data: tendencias.habitabilidad,
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
</script>
@endsection
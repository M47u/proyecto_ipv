@extends('layouts.app')

@section('title', 'Productividad de Inspectores')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Productividad Inspectores</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-person-badge"></i> Productividad de Inspectores</h2>
        <p class="text-muted mb-0">
            Del {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} 
            al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
        </p>
    </div>
    <div class="btn-group" role="group">
        <a href="{{ route('reportes.productividad-inspectores.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}" 
           class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Exportar PDF
        </a>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalFiltros">
            <i class="bi bi-funnel"></i> Filtros
        </button>
    </div>
</div>

<!-- Promedios Generales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Promedio Inspecciones</h6>
                <h2 class="mb-0">{{ $promedios['inspecciones'] }}</h2>
                <small>por inspector</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Productividad Diaria</h6>
                <h2 class="mb-0">{{ $promedios['promedio_diario'] }}</h2>
                <small>inspecciones/día</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">% Habitabilidad</h6>
                <h2 class="mb-0">{{ $promedios['habitabilidad'] }}%</h2>
                <small>promedio general</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h6 class="mb-2">Fallas por Inspección</h6>
                <h2 class="mb-0">{{ $promedios['fallas_por_inspeccion'] }}</h2>
                <small>promedio</small>
            </div>
        </div>
    </div>
</div>

<!-- Ranking Top 3 -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Ranking de Inspectores</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($ranking->take(3) as $index => $inspector)
            <div class="col-md-4 mb-3">
                <div class="card {{ $index == 0 ? 'border-warning' : '' }} h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($index == 0)
                                <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                            @elseif($index == 1)
                                <i class="bi bi-trophy text-secondary" style="font-size: 2.5rem;"></i>
                            @else
                                <i class="bi bi-trophy text-danger" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <h5>{{ $inspector['nombre'] }}</h5>
                        <h3 class="text-primary mb-3">{{ $inspector['puntos'] }} pts</h3>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Inspecciones</small>
                                <strong>{{ $inspector['total_inspecciones'] }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Habitabilidad</small>
                                <strong>{{ $inspector['habitabilidad'] }}%</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Diarias</small>
                                <strong>{{ $inspector['promedio_diario'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Gráficos Comparativos -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Total de Inspecciones</h5>
            </div>
            <div class="card-body">
                <canvas id="chartInspecciones"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Productividad Diaria</h5>
            </div>
            <div class="card-body">
                <canvas id="chartProductividad"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-percent"></i> % Habitabilidad</h5>
            </div>
            <div class="card-body">
                <canvas id="chartHabitabilidad"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-tools"></i> Fallas Detectadas</h5>
            </div>
            <div class="card-body">
                <canvas id="chartFallas"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Detallada -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-table"></i> Detalle por Inspector</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Inspector</th>
                        <th class="text-center">Inspecciones</th>
                        <th class="text-center">Viviendas</th>
                        <th class="text-center">Días Trabajados</th>
                        <th class="text-center">Prom. Diario</th>
                        <th class="text-center">% Habitables</th>
                        <th class="text-center">Total Fallas</th>
                        <th class="text-center">Fallas Críticas</th>
                        <th class="text-center">Seguimientos</th>
                        <th class="text-center">Última Inspección</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datosInspectores as $inspector)
                    <tr>
                        <td>
                            <strong>{{ $inspector['nombre'] }}</strong>
                            <br><small class="text-muted">{{ $inspector['email'] }}</small>
                        </td>
                        <td class="text-center">
                            <h5 class="mb-0">{{ $inspector['total_inspecciones'] }}</h5>
                        </td>
                        <td class="text-center">{{ $inspector['viviendas_inspeccionadas'] }}</td>
                        <td class="text-center">{{ $inspector['dias_trabajados'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $inspector['promedio_diario'] >= $promedios['promedio_diario'] ? 'success' : 'warning' }}">
                                {{ $inspector['promedio_diario'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $inspector['porcentaje_habitables'] >= 80 ? 'success' : ($inspector['porcentaje_habitables'] >= 50 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $inspector['porcentaje_habitables'] }}%">
                                    {{ $inspector['porcentaje_habitables'] }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ $inspector['total_fallas'] }}</td>
                        <td class="text-center">
                            @if($inspector['fallas_criticas'] > 0)
                                <span class="badge bg-danger">{{ $inspector['fallas_criticas'] }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $inspector['seguimientos_generados'] }}</td>
                        <td class="text-center">
                            @if($inspector['ultima_inspeccion'])
                                <small>{{ $inspector['ultima_inspeccion']->format('d/m/Y') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Filtros -->
<div class="modal fade" id="modalFiltros" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros de Período</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('reportes.productividad-inspectores') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                               value="{{ $fechaDesde }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                               value="{{ $fechaHasta }}" required>
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
const datosGraficos = @json($datosGraficos);

// Gráfico: Inspecciones
const ctxInspecciones = document.getElementById('chartInspecciones').getContext('2d');
new Chart(ctxInspecciones, {
    type: 'bar',
    data: {
        labels: datosGraficos.inspectores,
        datasets: [{
            label: 'Inspecciones',
            data: datosGraficos.total_inspecciones,
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

// Gráfico: Productividad Diaria
const ctxProductividad = document.getElementById('chartProductividad').getContext('2d');
new Chart(ctxProductividad, {
    type: 'bar',
    data: {
        labels: datosGraficos.inspectores,
        datasets: [{
            label: 'Inspecciones/Día',
            data: datosGraficos.promedio_diario,
            backgroundColor: '#10b981'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

// Gráfico: Habitabilidad
const ctxHabitabilidad = document.getElementById('chartHabitabilidad').getContext('2d');
new Chart(ctxHabitabilidad, {
    type: 'bar',
    data: {
        labels: datosGraficos.inspectores,
        datasets: [{
            label: '% Habitabilidad',
            data: datosGraficos.habitabilidad,
            backgroundColor: '#22c55e'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { 
            y: { 
                beginAtZero: true,
                max: 100
            } 
        }
    }
});

// Gráfico: Fallas
const ctxFallas = document.getElementById('chartFallas').getContext('2d');
new Chart(ctxFallas, {
    type: 'bar',
    data: {
        labels: datosGraficos.inspectores,
        datasets: [{
            label: 'Fallas Detectadas',
            data: datosGraficos.fallas,
            backgroundColor: '#f59e0b'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endsection
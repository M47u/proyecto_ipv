@extends('layouts.app')

@section('title', 'Estado de Viviendas')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Estado de Viviendas</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-clipboard-data"></i> Estado de Viviendas</h2>
        <p class="text-muted mb-0">Snapshot general del estado actual de todas las viviendas</p>
    </div>
    <div class="btn-group" role="group">
        <a href="{{ route('reportes.estado-viviendas.pdf', request()->all()) }}" 
           class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Exportar PDF
        </a>
        <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#filtros">
            <i class="bi bi-funnel"></i> Filtros
        </button>
    </div>
</div>

<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Total Viviendas</h6>
                <h2 class="mb-0">{{ $estadisticas['total'] }}</h2>
                <small>{{ $estadisticas['activas'] }} activas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Inspeccionadas</h6>
                <h2 class="mb-0">{{ $estadisticas['porcentaje_inspeccionadas'] }}%</h2>
                <small>{{ $estadisticas['inspeccionadas'] }} de {{ $estadisticas['total'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-{{ $estadisticas['porcentaje_habitables'] >= 80 ? 'success' : ($estadisticas['porcentaje_habitables'] >= 60 ? 'warning' : 'danger') }} text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">% Habitabilidad</h6>
                <h2 class="mb-0">{{ $estadisticas['porcentaje_habitables'] }}%</h2>
                <small>{{ $estadisticas['habitables'] }} habitables</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Críticas</h6>
                <h2 class="mb-0">{{ $estadisticas['criticas'] }}</h2>
                <small>requieren atención</small>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<div class="row mb-4">
    @if($estadisticas['sin_inspeccionar'] > 0)
    <div class="col-md-6 mb-3">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>{{ $estadisticas['sin_inspeccionar'] }} viviendas sin inspeccionar</strong>
            <p class="mb-0 small">Programar inspecciones iniciales</p>
        </div>
    </div>
    @endif

    @if($estadisticas['sin_inspeccion_reciente'] > 0)
    <div class="col-md-6 mb-3">
        <div class="alert alert-info">
            <i class="bi bi-clock"></i>
            <strong>{{ $estadisticas['sin_inspeccion_reciente'] }} viviendas sin inspección reciente</strong>
            <p class="mb-0 small">Más de 6 meses sin inspeccionar</p>
        </div>
    </div>
    @endif

    @if($estadisticas['con_reclamos_activos'] > 0)
    <div class="col-md-6 mb-3">
        <div class="alert alert-danger">
            <i class="bi bi-megaphone"></i>
            <strong>{{ $estadisticas['con_reclamos_activos'] }} viviendas con reclamos activos</strong>
            <p class="mb-0 small">Requieren seguimiento</p>
        </div>
    </div>
    @endif
</div>

<!-- Filtros -->
<div class="collapse mb-4" id="filtros">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.estado-viviendas') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Vivienda</label>
                        <select class="form-select" name="tipo_vivienda">
                            <option value="">Todos</option>
                            @foreach($tiposVivienda as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo_vivienda') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria_vivienda">
                            <option value="">Todas</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}" {{ request('categoria_vivienda') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Habitabilidad</label>
                        <select class="form-select" name="habitabilidad">
                            <option value="">Todas</option>
                            <option value="habitable" {{ request('habitabilidad') == 'habitable' ? 'selected' : '' }}>Habitables</option>
                            <option value="no_habitable" {{ request('habitabilidad') == 'no_habitable' ? 'selected' : '' }}>No Habitables</option>
                            <option value="sin_inspeccionar" {{ request('habitabilidad') == 'sin_inspeccionar' ? 'selected' : '' }}>Sin Inspeccionar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('reportes.estado-viviendas') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Por Tipo de Vivienda</h6>
            </div>
            <div class="card-body">
                <canvas id="chartTipo"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Por Estado General</h6>
            </div>
            <div class="card-body">
                <canvas id="chartEstado"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Habitabilidad</h6>
            </div>
            <div class="card-body">
                <canvas id="chartHabitabilidad"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Viviendas -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> Listado de Viviendas
            <span class="badge bg-primary">{{ $viviendas->total() }} registros</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Dirección</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Última Inspección</th>
                        <th>Habitabilidad</th>
                        <th>Reclamos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($viviendas as $vivienda)
                    <tr>
                        <td><strong>{{ $vivienda->codigo }}</strong></td>
                        <td>{{ Str::limit($vivienda->direccion, 40) }}</td>
                        <td><span class="badge bg-secondary">{{ $vivienda->tipo_vivienda_text }}</span></td>
                        <td>{{ $vivienda->categoria_vivienda ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $vivienda->estado == 'activa' ? 'success' : 'secondary' }}">
                                {{ ucfirst($vivienda->estado) }}
                            </span>
                        </td>
                        <td>
                            @if($vivienda->ultimaInspeccion)
                                <small>{{ $vivienda->ultimaInspeccion->fecha_inspeccion->format('d/m/Y') }}</small>
                                <br><small class="text-muted">{{ $vivienda->ultimaInspeccion->fecha_inspeccion->diffForHumans() }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Sin inspeccionar</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($vivienda->ultimaInspeccion)
                                @if($vivienda->ultimaInspeccion->es_habitable)
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                @else
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $reclamosActivos = $vivienda->reclamos->whereIn('estado', ['pendiente', 'en_proceso'])->count();
                            @endphp
                            @if($reclamosActivos > 0)
                                <span class="badge bg-warning text-dark">{{ $reclamosActivos }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('viviendas.show', $vivienda->id) }}" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No se encontraron viviendas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-3">
            {{ $viviendas->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const datosGraficos = @json($datosGraficos);

// Gráfico: Por Tipo
const ctxTipo = document.getElementById('chartTipo').getContext('2d');
new Chart(ctxTipo, {
    type: 'doughnut',
    data: {
        labels: Object.keys(datosGraficos.por_tipo).map(t => t.replace('_', ' ')),
        datasets: [{
            data: Object.values(datosGraficos.por_tipo),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

// Gráfico: Por Estado
const ctxEstado = document.getElementById('chartEstado').getContext('2d');
new Chart(ctxEstado, {
    type: 'bar',
    data: {
        labels: Object.keys(datosGraficos.por_estado).map(e => e.charAt(0).toUpperCase() + e.slice(1)),
        datasets: [{
            label: 'Cantidad',
            data: Object.values(datosGraficos.por_estado),
            backgroundColor: ['#22c55e', '#3b82f6', '#facc15', '#ef4444', '#991b1b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Gráfico: Habitabilidad
const ctxHabitabilidad = document.getElementById('chartHabitabilidad').getContext('2d');
new Chart(ctxHabitabilidad, {
    type: 'pie',
    data: {
        labels: ['Habitables', 'No Habitables', 'Sin Inspeccionar'],
        datasets: [{
            data: [
                datosGraficos.habitabilidad.habitables,
                datosGraficos.habitabilidad.no_habitables,
                datosGraficos.habitabilidad.sin_inspeccionar
            ],
            backgroundColor: ['#22c55e', '#ef4444', '#9ca3af']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endsection
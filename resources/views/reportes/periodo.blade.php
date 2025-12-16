@extends('layouts.app')

@section('title', 'Reporte Inspecciones - ' . $estadisticas['fecha_desde'] . ' al ' . $estadisticas['fecha_hasta'])

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reportes.periodo') }}">Período</a></li>
            <li class="breadcrumb-item active">Resultado</li>
        </ol>
    </nav>
@endsection

@section('content')
    <!-- Header con botones de exportación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-calendar-range"></i> Inspecciones por Período</h2>
            <p class="text-muted mb-0">
                Del {{ \Carbon\Carbon::parse($estadisticas['fecha_desde'])->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($estadisticas['fecha_hasta'])->format('d/m/Y') }}
            </p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('reportes.periodo.pdf', request()->all()) }}" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('reportes.periodo.excel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('reportes.periodo') }}" class="btn btn-secondary">
                <i class="bi bi-funnel"></i> Nuevos Filtros
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Inspecciones</h6>
                            <h2 class="mb-0">{{ $estadisticas['total_inspecciones'] }}</h2>
                            <small>{{ $estadisticas['promedio_por_dia'] }} por día</small>
                        </div>
                        <i class="bi bi-clipboard-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Viviendas Inspeccionadas</h6>
                            <h2 class="mb-0">{{ $estadisticas['total_viviendas'] }}</h2>
                            <small>{{ $estadisticas['total_inspectores'] }} inspectores</small>
                        </div>
                        <i class="bi bi-house-door fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div
                class="card bg-{{ $estadisticas['porcentaje_habitables'] >= 80 ? 'success' : ($estadisticas['porcentaje_habitables'] >= 50 ? 'warning' : 'danger') }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Habitabilidad</h6>
                            <h2 class="mb-0">{{ $estadisticas['porcentaje_habitables'] }}%</h2>
                            <small>{{ $estadisticas['habitables'] }} de {{ $estadisticas['total_inspecciones'] }}</small>
                        </div>
                        <i class="bi bi-shield-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Fallas Detectadas</h6>
                            <h2 class="mb-0">{{ $estadisticas['total_fallas'] }}</h2>
                            <small>{{ $estadisticas['fallas_criticas'] }} críticas</small>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Aplicados -->
    @if($request->filled('inspector_id') || $request->filled('tipo_inspeccion') || $request->filled('estado_general') || $request->filled('tipo_vivienda') || $request->filled('es_habitable'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-funnel"></i> <strong>Filtros aplicados:</strong>
            <div class="mt-2">
                @if($request->filled('inspector_id'))
                    <span class="badge bg-primary me-1">Inspector: {{ \App\Models\User::find($request->inspector_id)->name }}</span>
                @endif
                @if($request->filled('tipo_inspeccion'))
                    <span class="badge bg-primary me-1">Tipo: {{ ucfirst(str_replace('_', ' ', $request->tipo_inspeccion)) }}</span>
                @endif
                @if($request->filled('estado_general'))
                    <span class="badge bg-primary me-1">Estado: {{ ucfirst($request->estado_general) }}</span>
                @endif
                @if($request->filled('tipo_vivienda'))
                    <span class="badge bg-primary me-1">Tipo Vivienda:
                        {{ ucfirst(str_replace('_', ' ', $request->tipo_vivienda)) }}</span>
                @endif
                @if($request->filled('es_habitable'))
                    <span
                        class="badge bg-primary me-1">{{ $request->es_habitable == '1' ? 'Solo Habitables' : 'Solo No Habitables' }}</span>
                @endif
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico: Inspecciones por Día -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Inspecciones por Día</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPorDia" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Habitabilidad -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Habitabilidad</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartHabitabilidad"></canvas>
                    <div class="text-center mt-3">
                        <h4
                            class="text-{{ $estadisticas['porcentaje_habitables'] >= 80 ? 'success' : ($estadisticas['porcentaje_habitables'] >= 50 ? 'warning' : 'danger') }}">
                            {{ $estadisticas['porcentaje_habitables'] }}%
                        </h4>
                        <p class="text-muted mb-0">de viviendas habitables</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos: Distribuciones -->
    <div class="row mb-4">
        <!-- Por Tipo de Inspección -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Por Tipo</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPorTipo"></canvas>
                </div>
            </div>
        </div>

        <!-- Por Estado General -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-speedometer"></i> Por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPorEstado"></canvas>
                </div>
            </div>
        </div>

        <!-- Por Inspector -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Por Inspector</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPorInspector"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Por Tipo de Inspección</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estadisticas['por_tipo'] as $tipo => $cantidad)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</span></td>
                                    <td class="text-end"><strong>{{ $cantidad }}</strong></td>
                                    <td class="text-end">
                                        {{ round(($cantidad / $estadisticas['total_inspecciones']) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-speedometer2"></i> Por Estado General</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estadisticas['por_estado'] as $estado => $cantidad)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ 
                                                            $estado == 'excelente' ? 'success' :
                                ($estado == 'bueno' ? 'primary' :
                                    ($estado == 'regular' ? 'warning' : 'danger'))
                                                        }}">
                                                            {{ ucfirst($estado) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end"><strong>{{ $cantidad }}</strong></td>
                                                    <td class="text-end">
                                                        {{ round(($cantidad / $estadisticas['total_inspecciones']) * 100, 1) }}%</td>
                                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productividad por Inspector -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Productividad por Inspector</h5>
            <span class="badge bg-primary">{{ $estadisticas['total_inspectores'] }} inspectores activos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Inspector</th>
                            <th class="text-center">Inspecciones</th>
                            <th class="text-center">% del Total</th>
                            <th class="text-center">Promedio Diario</th>
                            <th width="40%">Distribución</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estadisticas['por_inspector'] as $inspector)
                            <tr>
                                <td><i class="bi bi-person-circle"></i> {{ $inspector['nombre'] }}</td>
                                <td class="text-center"><strong>{{ $inspector['total'] }}</strong></td>
                                <td class="text-center">
                                    {{ round(($inspector['total'] / $estadisticas['total_inspecciones']) * 100, 1) }}%</td>
                                <td class="text-center">{{ round($inspector['total'] / $estadisticas['promedio_por_dia'], 1) }}
                                </td>
                                <td>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ ($inspector['total'] / $estadisticas['total_inspecciones']) * 100 }}%"
                                            aria-valuenow="{{ $inspector['total'] }}" aria-valuemin="0"
                                            aria-valuemax="{{ $estadisticas['total_inspecciones'] }}">
                                            {{ $inspector['total'] }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Listado de Inspecciones -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Detalle de Inspecciones</h5>
            <span class="badge bg-primary">{{ $inspecciones->count() }} registros</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Código Vivienda</th>
                            <th>Dirección</th>
                            <th>Tipo</th>
                            <th>Inspector</th>
                            <th>Estado</th>
                            <th>Habitable</th>
                            <th>Fallas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspecciones as $inspeccion)
                            <tr>
                                <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $inspeccion->vivienda->codigo }}</strong></td>
                                <td>{{ Str::limit($inspeccion->vivienda->direccion, 30) }}</td>
                                <td><span class="badge bg-secondary">{{ $inspeccion->tipo_inspeccion_text }}</span></td>
                                <td>{{ $inspeccion->inspector->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $inspeccion->estado_general_color }}">
                                        {{ ucfirst($inspeccion->estado_general) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($inspeccion->es_habitable)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($inspeccion->fallas->count() > 0)
                                        <span class="badge bg-warning text-dark">{{ $inspeccion->fallas->count() }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inspecciones.show', $inspeccion->id) }}"
                                        class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No se encontraron inspecciones</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const datosGraficos = @json($datosGraficos);

        // GRÁFICO: Inspecciones por Día
        const ctxPorDia = document.getElementById('chartPorDia').getContext('2d');
        new Chart(ctxPorDia, {
            type: 'line',
            data: {
                labels: Object.keys(datosGraficos.por_dia),
                datasets: [{
                    label: 'Inspecciones',
                    data: Object.values(datosGraficos.por_dia),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // GRÁFICO: Habitabilidad
        const ctxHabitabilidad = document.getElementById('chartHabitabilidad').getContext('2d');
        new Chart(ctxHabitabilidad, {
            type: 'doughnut',
            data: {
                labels: ['Habitables', 'No Habitables'],
                datasets: [{
                    data: [datosGraficos.habitabilidad.habitables, datosGraficos.habitabilidad.no_habitables],
                    backgroundColor: ['#22c55e', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // GRÁFICO: Por Tipo
        const ctxPorTipo = document.getElementById('chartPorTipo').getContext('2d');
        new Chart(ctxPorTipo, {
            type: 'bar',
            data: {
                labels: Object.keys(datosGraficos.por_tipo).map(t => t.replace('_', ' ')),
                datasets: [{
                    label: 'Cantidad',
                    data: Object.values(datosGraficos.por_tipo),
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // GRÁFICO: Por Estado
        const ctxPorEstado = document.getElementById('chartPorEstado').getContext('2d');
        new Chart(ctxPorEstado, {
            type: 'doughnut',
            data: {
                labels: Object.keys(datosGraficos.por_estado).map(e => e.charAt(0).toUpperCase() + e.slice(1)),
                datasets: [{
                    data: Object.values(datosGraficos.por_estado),
                    backgroundColor: ['#22c55e', '#3b82f6', '#facc15', '#ef4444', '#991b1b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // GRÁFICO: Por Inspector
        const ctxPorInspector = document.getElementById('chartPorInspector').getContext('2d');
        new Chart(ctxPorInspector, {
            type: 'bar',
            data: {
                labels: Object.values(datosGraficos.por_inspector).map(i => i.nombre.split(' ')[0]),
                datasets: [{
                    label: 'Inspecciones',
                    data: Object.values(datosGraficos.por_inspector).map(i => i.total),
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
    </script>
@endsection
@extends('layouts.app')

@section('title', 'Evolución de Vivienda - ' . $vivienda->codigo)

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.evolucion-vivienda-form') }}">Evolución</a></li>
        <li class="breadcrumb-item active">{{ $vivienda->codigo }}</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Header con botones de exportación -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-house-check"></i> Evolución de Vivienda</h2>
        <p class="text-muted mb-0">{{ $vivienda->codigo }} - {{ $vivienda->direccion }}</p>
    </div>
    <div class="btn-group" role="group">
        <a href="{{ route('reportes.evolucion-vivienda.pdf', $vivienda->id) }}" 
           class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('reportes.evolucion-vivienda.excel', $vivienda->id) }}" 
           class="btn btn-success">
            <i class="bi bi-file-excel"></i> Exportar Excel
        </a>
        <a href="{{ route('reportes.evolucion-vivienda-form') }}" 
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Otra Vivienda
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
                    </div>
                    <i class="bi bi-clipboard-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-{{ $estadisticas['es_habitable'] === true ? 'success' : ($estadisticas['es_habitable'] === false ? 'danger' : 'secondary') }} text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Estado Actual</h6>
                        <h5 class="mb-0">{{ ucfirst($estadisticas['estado_actual']) }}</h5>
                        <small>{{ $estadisticas['es_habitable'] === true ? 'Habitable' : ($estadisticas['es_habitable'] === false ? 'No Habitable' : 'Sin datos') }}</small>
                    </div>
                    <i class="bi bi-house-door fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Total Fallas</h6>
                        <h2 class="mb-0">{{ $estadisticas['total_fallas'] }}</h2>
                        <small>{{ $estadisticas['fallas_criticas'] }} críticas</small>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Reclamos</h6>
                        <h2 class="mb-0">{{ $estadisticas['total_reclamos'] }}</h2>
                        <small>{{ $estadisticas['reclamos_pendientes'] }} pendientes</small>
                    </div>
                    <i class="bi bi-megaphone fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Datos Generales de la Vivienda -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Datos Generales</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%"><strong>Código:</strong></td>
                        <td>{{ $vivienda->codigo }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Dirección:</strong></td>
                        <td>{{ $vivienda->direccion }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Barrio:</strong></td>
                        <td>{{ $vivienda->barrio ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Ciudad:</strong></td>
                        <td>{{ $vivienda->ciudad }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Tipo:</strong></td>
                        <td><span class="badge bg-primary">{{ $vivienda->tipo_vivienda_text }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Categoría:</strong></td>
                        <td>{{ $vivienda->categoria_vivienda ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%"><strong>Superficie Cubierta:</strong></td>
                        <td>{{ $vivienda->superficie_cubierta ?? 'N/A' }} m²</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Superficie Terreno:</strong></td>
                        <td>{{ $vivienda->superficie_terreno ?? 'N/A' }} m²</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Ambientes:</strong></td>
                        <td>{{ $vivienda->cantidad_ambientes ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Propietario:</strong></td>
                        <td>{{ $vivienda->propietario_actual ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Teléfono:</strong></td>
                        <td>{{ $vivienda->telefono_contacto ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Estado:</strong></td>
                        <td><span class="badge bg-{{ $vivienda->estado == 'activa' ? 'success' : 'secondary' }}">{{ ucfirst($vivienda->estado) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <!-- Gráfico: Timeline de Estados -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Evolución del Estado General</h5>
            </div>
            <div class="card-body">
                <canvas id="chartEstados" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico: Distribución de Fallas -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Fallas por Categoría</h5>
            </div>
            <div class="card-body">
                <canvas id="chartFallas"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico: Evolución por Áreas -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-bar-chart-line"></i> Evolución por Áreas Evaluadas</h5>
    </div>
    <div class="card-body">
        <canvas id="chartAreas" height="80"></canvas>
    </div>
</div>

<!-- Timeline de Inspecciones -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Inspecciones</h5>
    </div>
    <div class="card-body">
        @if($vivienda->inspecciones->count() > 0)
            <div class="timeline">
                @foreach($vivienda->inspecciones as $index => $inspeccion)
                    <div class="timeline-item {{ $index % 2 == 0 ? 'timeline-left' : 'timeline-right' }}">
                        <div class="timeline-marker bg-{{ $inspeccion->estado_general_color }}"></div>
                        <div class="timeline-content">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-{{ $inspeccion->estado_general_color }}">
                                                    {{ ucfirst($inspeccion->estado_general) }}
                                                </span>
                                                <span class="badge bg-secondary">{{ $inspeccion->tipo_inspeccion_text }}</span>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> {{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <a href="{{ route('inspecciones.show', $inspeccion->id) }}" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </div>

                                    <p class="mb-2">
                                        <strong><i class="bi bi-person"></i> Inspector:</strong> 
                                        {{ $inspeccion->inspector->name }}
                                    </p>

                                    @if($inspeccion->es_habitable !== null)
                                        <p class="mb-2">
                                            <strong>Habitabilidad:</strong>
                                            <span class="badge bg-{{ $inspeccion->es_habitable ? 'success' : 'danger' }}">
                                                {{ $inspeccion->es_habitable ? 'Habitable' : 'No Habitable' }}
                                            </span>
                                        </p>
                                    @endif

                                    @if($inspeccion->fallas->count() > 0)
                                        <div class="alert alert-warning alert-sm mb-2">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <strong>{{ $inspeccion->fallas->count() }} falla(s) detectada(s)</strong>
                                            @if($inspeccion->fallas->where('gravedad', 'critica')->count() > 0)
                                                <br><small class="text-danger">
                                                    {{ $inspeccion->fallas->where('gravedad', 'critica')->count() }} crítica(s)
                                                </small>
                                            @endif
                                        </div>
                                    @endif

                                    @if($inspeccion->observaciones)
                                        <div class="border-start border-primary border-3 ps-2 bg-light p-2 rounded">
                                            <small><strong>Observaciones:</strong><br>
                                            {{ $inspeccion->observaciones }}</small>
                                        </div>
                                    @endif

                                    @if($inspeccion->requiere_seguimiento)
                                        <div class="mt-2">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-bell"></i> Requiere Seguimiento
                                            </span>
                                            @if($inspeccion->fecha_proximo_seguimiento)
                                                <small class="text-muted">
                                                    ({{ $inspeccion->fecha_proximo_seguimiento->format('d/m/Y') }})
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay inspecciones registradas para esta vivienda.
            </div>
        @endif
    </div>
</div>

<!-- Tabla de Fallas Detalladas -->
@if($estadisticas['total_fallas'] > 0)
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-tools"></i> Detalle de Fallas Encontradas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Fecha Inspección</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Ubicación</th>
                        <th>Gravedad</th>
                        <th>Acción Inmediata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vivienda->inspecciones as $inspeccion)
                        @foreach($inspeccion->fallas as $falla)
                            <tr>
                                <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($falla->categoria) }}</span></td>
                                <td>{{ $falla->descripcion }}</td>
                                <td>{{ $falla->ubicacion ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $falla->gravedad == 'critica' ? 'danger' : 
                                        ($falla->gravedad == 'grave' ? 'warning' : 
                                        ($falla->gravedad == 'moderada' ? 'info' : 'secondary'))
                                    }}">
                                        {{ ucfirst($falla->gravedad) }}
                                    </span>
                                </td>
                                <td>
                                    @if($falla->requiere_accion_inmediata)
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> SÍ</span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Reclamos -->
@if($vivienda->reclamos->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-megaphone"></i> Reclamos Asociados</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Reclamante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vivienda->reclamos as $reclamo)
                        <tr>
                            <td>{{ $reclamo->fecha_reclamo ? $reclamo->fecha_reclamo->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $reclamo->titulo }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($reclamo->tipo_reclamo ?? 'N/A') }}</span></td>
                            <td>
                                <span class="badge bg-{{ 
                                    $reclamo->prioridad == 'urgente' ? 'danger' : 
                                    ($reclamo->prioridad == 'alta' ? 'warning' : 
                                    ($reclamo->prioridad == 'media' ? 'info' : 'secondary'))
                                }}">
                                    {{ ucfirst($reclamo->prioridad) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $reclamo->estado == 'resuelto' ? 'success' : 
                                    ($reclamo->estado == 'en_proceso' ? 'warning' : 
                                    ($reclamo->estado == 'rechazado' ? 'danger' : 'secondary'))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $reclamo->estado)) }}
                                </span>
                            </td>
                            <td>{{ $reclamo->reclamante_nombre ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('reclamos.show', $reclamo->id) }}" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 100%;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    width: 48%;
}

.timeline-left {
    left: 0;
    padding-right: 30px;
    text-align: right;
}

.timeline-right {
    left: 52%;
    padding-left: 30px;
}

.timeline-marker {
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
    top: 10px;
}

.timeline-left .timeline-marker {
    right: -8px;
}

.timeline-right .timeline-marker {
    left: -8px;
}

.timeline-content {
    text-align: left;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline::before {
        left: 20px;
    }

    .timeline-item {
        width: 100%;
        left: 0 !important;
        padding-left: 50px !important;
        padding-right: 0 !important;
        text-align: left !important;
    }

    .timeline-marker {
        left: 12px !important;
    }
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos de los gráficos desde PHP
const datosGraficos = @json($datosGraficos);

// GRÁFICO 1: Timeline de Estados
const ctxEstados = document.getElementById('chartEstados').getContext('2d');
const chartEstados = new Chart(ctxEstados, {
    type: 'line',
    data: {
        labels: datosGraficos.timeline_estados.map(item => item.fecha),
        datasets: [{
            label: 'Estado General',
            data: datosGraficos.timeline_estados.map((item, index) => {
                const estados = ['critico', 'malo', 'regular', 'bueno', 'excelente'];
                return estados.indexOf(item.estado) + 1;
            }),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: datosGraficos.timeline_estados.map(item => item.color),
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const estados = ['', 'Crítico', 'Malo', 'Regular', 'Bueno', 'Excelente'];
                        return estados[context.parsed.y];
                    }
                }
            }
        },
        scales: {
            y: {
                min: 0,
                max: 6,
                ticks: {
                    stepSize: 1,
                    callback: function(value) {
                        const estados = ['', 'Crítico', 'Malo', 'Regular', 'Bueno', 'Excelente'];
                        return estados[value] || '';
                    }
                }
            }
        }
    }
});

// GRÁFICO 2: Distribución de Fallas
if (Object.keys(datosGraficos.distribucion_fallas).length > 0) {
    const ctxFallas = document.getElementById('chartFallas').getContext('2d');
    const chartFallas = new Chart(ctxFallas, {
        type: 'doughnut',
        data: {
            labels: Object.keys(datosGraficos.distribucion_fallas).map(cat => cat.charAt(0).toUpperCase() + cat.slice(1)),
            datasets: [{
                data: Object.values(datosGraficos.distribucion_fallas),
                backgroundColor: [
                    '#ef4444',
                    '#f59e0b',
                    '#10b981',
                    '#3b82f6',
                    '#8b5cf6',
                    '#ec4899',
                    '#14b8a6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// GRÁFICO 3: Evolución por Áreas
const ctxAreas = document.getElementById('chartAreas').getContext('2d');
const chartAreas = new Chart(ctxAreas, {
    type: 'radar',
    data: {
        labels: ['Estructura', 'Eléctrica', 'Sanitaria', 'Gas', 'Pintura', 'Aberturas', 'Pisos'],
        datasets: datosGraficos.evolucion_areas.map((inspeccion, index) => ({
            label: inspeccion.fecha,
            data: [
                inspeccion.estructura,
                inspeccion.electrica,
                inspeccion.sanitaria,
                inspeccion.gas,
                inspeccion.pintura,
                inspeccion.aberturas,
                inspeccion.pisos
            ],
            borderColor: `hsl(${index * 360 / datosGraficos.evolucion_areas.length}, 70%, 50%)`,
            backgroundColor: `hsla(${index * 360 / datosGraficos.evolucion_areas.length}, 70%, 50%, 0.1)`,
            pointBackgroundColor: `hsl(${index * 360 / datosGraficos.evolucion_areas.length}, 70%, 50%)`,
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: `hsl(${index * 360 / datosGraficos.evolucion_areas.length}, 70%, 50%)`
        }))
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            r: {
                min: 0,
                max: 5,
                ticks: {
                    stepSize: 1,
                    callback: function(value) {
                        const estados = ['N/A', 'Crítico', 'Malo', 'Regular', 'Bueno', 'Excelente'];
                        return estados[value] || '';
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    font: {
                        size: 10
                    }
                }
            }
        }
    }
});
</script>
@endsection
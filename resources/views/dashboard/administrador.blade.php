@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-speedometer2"></i> Dashboard - Administrador</h2>
        <p class="text-muted">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="stat-label">Inspecciones Este Mes</div>
                    <div class="stat-value">{{ $inspeccionesMesActual }}</div>
                    @if($inspeccionesMesAnterior > 0)
                        @php
                            $cambio = (($inspeccionesMesActual - $inspeccionesMesAnterior) / $inspeccionesMesAnterior) * 100;
                        @endphp
                        <small class="text-{{ $cambio >= 0 ? 'success' : 'danger' }}">
                            <i class="bi bi-{{ $cambio >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ abs(round($cambio, 1)) }}% vs mes anterior
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="stat-label">Reclamos Pendientes</div>
                    <div class="stat-value text-warning">{{ $reclamosPendientesCount }}</div>
                    <small class="text-muted">de {{ $totalReclamos }} totales</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="stat-label">Viviendas Entregadas</div>
                    <div class="stat-value text-success">{{ $viviendasPorTipo['entregada'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-info">
                <div class="card-body">
                    <div class="stat-label">Próximas Entregas</div>
                    <div class="stat-value text-info">{{ $viviendasPorTipo['proxima_entrega'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Inspecciones por Mes</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartInspeccionesMes" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Inspecciones por Estado</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartInspeccionesEstado" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Reclamos Pendientes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Reclamos Pendientes</h6>
                    <a href="{{ route('reclamos.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($reclamosPendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Vivienda</th>
                                        <th>Tipo</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reclamosPendientes as $reclamo)
                                        <tr>
                                            <td>
                                                {{ $reclamo->fecha_reclamo ? $reclamo->fecha_reclamo->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('viviendas.show', $reclamo->vivienda) }}">
                                                    {{ $reclamo->vivienda->codigo }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ ucfirst($reclamo->tipo_reclamo) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $reclamo->prioridad_color }}">
                                                    {{ ucfirst($reclamo->prioridad) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $reclamo->estado_color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $reclamo->estado)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">No hay reclamos pendientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Inspecciones -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Últimas Inspecciones</h6>
                    <a href="{{ route('inspecciones.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Vivienda</th>
                                    <th>Inspector</th>
                                    <th>Tipo</th>
                                    <th>Estado General</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasInspecciones as $inspeccion)
                                    <tr>
                                        <td>{{ $inspeccion->fecha_inspeccion ? $inspeccion->fecha_inspeccion->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                                        <td>
                                            <a href="{{ route('viviendas.show', $inspeccion->vivienda) }}">
                                                {{ $inspeccion->vivienda->codigo }}
                                            </a>
                                        </td>
                                        <td>{{ $inspeccion->inspector->name }}</td>
                                        <td>{{ $inspeccion->tipo_inspeccion_text }}</td>
                                        <td>
                                            <span class="badge badge-estado-{{ $inspeccion->estado_general }}">
                                                {{ ucfirst($inspeccion->estado_general) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Gráfico de Inspecciones por Mes
        const ctxMes = document.getElementById('chartInspeccionesMes').getContext('2d');
        new Chart(ctxMes, {
            type: 'bar',
            data: {
                labels: {!! json_encode($inspeccionesPorMes->pluck('mes')) !!},
                datasets: [{
                    label: 'Inspecciones',
                    data: {!! json_encode($inspeccionesPorMes->pluck('total')) !!},
                    backgroundColor: 'rgba(30, 64, 175, 0.7)',
                    borderColor: 'rgba(30, 64, 175, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de Inspecciones por Estado
        const ctxEstado = document.getElementById('chartInspeccionesEstado').getContext('2d');
        new Chart(ctxEstado, {
            type: 'doughnut',
            data: {
                labels: ['Excelente', 'Bueno', 'Regular', 'Malo', 'Crítico'],
                datasets: [{
                    data: [
                                {{ $inspeccionesPorEstado['excelente'] ?? 0 }},
                                {{ $inspeccionesPorEstado['bueno'] ?? 0 }},
                                {{ $inspeccionesPorEstado['regular'] ?? 0 }},
                                {{ $inspeccionesPorEstado['malo'] ?? 0 }},
                        {{ $inspeccionesPorEstado['critico'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#7f1d1d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
@endpush
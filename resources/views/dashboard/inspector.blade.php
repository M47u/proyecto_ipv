@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-speedometer2"></i> Dashboard - Inspector</h2>
        <p class="text-muted">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="stat-label">Mis Inspecciones Este Mes</div>
                    <div class="stat-value">{{ $misInspeccionesMes }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="stat-label">Inspecciones Pendientes</div>
                    <div class="stat-value text-warning">{{ $misInspeccionesPendientes }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-info">
                <div class="card-body">
                    <div class="stat-label">Próximos Seguimientos</div>
                    <div class="stat-value text-info">{{ $proximosSeguimientos->count() }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="stat-label">Viviendas Activas</div>
                    <div class="stat-value text-success">{{ $totalViviendas }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de Nueva Inspección -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-4">
                    <h4><i class="bi bi-clipboard-plus"></i> ¿Listo para una nueva inspección?</h4>
                    <p class="mb-3">Registra una nueva inspección de vivienda</p>
                    <a href="{{ route('inspecciones.create') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-plus-circle"></i> Nueva Inspección
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximos Seguimientos -->
    @if($proximosSeguimientos->count() > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Próximos Seguimientos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha Programada</th>
                                        <th>Vivienda</th>
                                        <th>Dirección</th>
                                        <th>Estado Anterior</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximosSeguimientos as $inspeccion)
                                        <tr>
                                            <td>
                                                <strong>{{ $inspeccion->fecha_proximo_seguimiento->format('d/m/Y') }}</strong>
                                                @if($inspeccion->fecha_proximo_seguimiento->isToday())
                                                    <span class="badge bg-danger ms-2">HOY</span>
                                                @elseif($inspeccion->fecha_proximo_seguimiento->isTomorrow())
                                                    <span class="badge bg-warning ms-2">MAÑANA</span>
                                                @endif
                                            </td>
                                            <td>{{ $inspeccion->vivienda->codigo }}</td>
                                            <td>{{ $inspeccion->vivienda->direccion }}</td>
                                            <td>
                                                <span class="badge badge-estado-{{ $inspeccion->estado_general }}">
                                                    {{ ucfirst($inspeccion->estado_general) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('inspecciones.show', $inspeccion) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
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
    @endif

    <!-- Gráfico de Mis Inspecciones -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Mis Inspecciones por Estado</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMisInspecciones" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Últimas Inspecciones -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Mis Últimas Inspecciones</h6>
                    <a href="{{ route('inspecciones.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    @if($misUltimasInspecciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Vivienda</th>
                                        <th>Dirección</th>
                                        <th>Tipo</th>
                                        <th>Estado General</th>
                                        <th>Habitable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misUltimasInspecciones as $inspeccion)
                                        <tr>
                                            <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('viviendas.show', $inspeccion->vivienda) }}">
                                                    {{ $inspeccion->vivienda->codigo }}
                                                </a>
                                            </td>
                                            <td>{{ $inspeccion->vivienda->direccion }}</td>
                                            <td>{{ $inspeccion->tipo_inspeccion_text }}</td>
                                            <td>
                                                <span class="badge badge-estado-{{ $inspeccion->estado_general }}">
                                                    {{ ucfirst($inspeccion->estado_general) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($inspeccion->es_habitable)
                                                    <span class="badge bg-success">Sí</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">No has realizado inspecciones aún</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Gráfico de Mis Inspecciones por Estado
        const ctx = document.getElementById('chartMisInspecciones').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Excelente', 'Bueno', 'Regular', 'Malo', 'Crítico'],
                datasets: [{
                    data: [
                    {{ $misInspeccionesPorEstado['excelente'] ?? 0 }},
                    {{ $misInspeccionesPorEstado['bueno'] ?? 0 }},
                    {{ $misInspeccionesPorEstado['regular'] ?? 0 }},
                    {{ $misInspeccionesPorEstado['malo'] ?? 0 }},
                        {{ $misInspeccionesPorEstado['critico'] ?? 0 }}
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
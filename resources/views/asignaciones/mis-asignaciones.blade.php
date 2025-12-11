@extends('layouts.app')

@section('title', 'Mis Asignaciones')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Mis Asignaciones</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="mb-4"><i class="bi bi-clipboard-check"></i> Mis Asignaciones de Viviendas</h4>
    </div>
</div>

<!-- Asignaciones Pendientes -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-warning"><i class="bi bi-clock-history"></i> Pendientes ({{ $asignacionesPendientes->count() }})</h5>
        <div class="row">
            @forelse($asignacionesPendientes as $asignacion)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-{{ $asignacion->prioridad_color }}">
                    <div class="card-header bg-{{ $asignacion->prioridad_color }} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>{{ $asignacion->vivienda->codigo }}</strong>
                            <span class="badge bg-light text-dark">{{ $asignacion->prioridad_text }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt"></i> 
                            <strong>{{ $asignacion->vivienda->direccion }}</strong>
                        </p>
                        <p class="mb-2 text-muted small">
                            {{ $asignacion->vivienda->barrio ?? 'Sin barrio' }}, {{ $asignacion->vivienda->ciudad }}
                        </p>
                        
                        <hr>
                        
                        <p class="mb-1 small">
                            <i class="bi bi-calendar"></i> 
                            <strong>Asignada:</strong> {{ $asignacion->fecha_asignacion->format('d/m/Y') }}
                        </p>
                        @if($asignacion->fecha_limite)
                        <p class="mb-1 small {{ $asignacion->esta_vencida ? 'text-danger fw-bold' : '' }}">
                            <i class="bi bi-calendar-x"></i> 
                            <strong>Límite:</strong> {{ $asignacion->fecha_limite->format('d/m/Y') }}
                            @if($asignacion->esta_vencida)
                                <span class="badge bg-danger">VENCIDA</span>
                            @endif
                        </p>
                        @endif
                        
                        @if($asignacion->notas)
                        <hr>
                        <p class="mb-0 small">
                            <i class="bi bi-sticky"></i> 
                            <strong>Notas:</strong><br>
                            {{ Str::limit($asignacion->notas, 100) }}
                        </p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <a href="{{ route('inspecciones.create', ['vivienda_id' => $asignacion->vivienda_id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-clipboard-plus"></i> Iniciar Inspección
                            </a>
                            <form action="{{ route('asignaciones.cambiar-estado', $asignacion) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="en_progreso">
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-play-circle"></i> Marcar En Progreso
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No tienes asignaciones pendientes
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Asignaciones En Progreso -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-info"><i class="bi bi-arrow-repeat"></i> En Progreso ({{ $asignacionesEnProgreso->count() }})</h5>
        <div class="row">
            @forelse($asignacionesEnProgreso as $asignacion)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>{{ $asignacion->vivienda->codigo }}</strong>
                            <span class="badge bg-light text-dark">{{ $asignacion->prioridad_text }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt"></i> 
                            <strong>{{ $asignacion->vivienda->direccion }}</strong>
                        </p>
                        <p class="mb-2 text-muted small">
                            {{ $asignacion->vivienda->barrio ?? 'Sin barrio' }}, {{ $asignacion->vivienda->ciudad }}
                        </p>
                        
                        <hr>
                        
                        <p class="mb-1 small">
                            <i class="bi bi-calendar"></i> 
                            <strong>Asignada:</strong> {{ $asignacion->fecha_asignacion->format('d/m/Y') }}
                        </p>
                        @if($asignacion->fecha_limite)
                        <p class="mb-1 small {{ $asignacion->esta_vencida ? 'text-danger fw-bold' : '' }}">
                            <i class="bi bi-calendar-x"></i> 
                            <strong>Límite:</strong> {{ $asignacion->fecha_limite->format('d/m/Y') }}
                            @if($asignacion->esta_vencida)
                                <span class="badge bg-danger">VENCIDA</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <a href="{{ route('inspecciones.create', ['vivienda_id' => $asignacion->vivienda_id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-clipboard-plus"></i> Crear Inspección
                            </a>
                            <form action="{{ route('asignaciones.cambiar-estado', $asignacion) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="completada">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check-circle"></i> Marcar Completada
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No tienes asignaciones en progreso
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Asignaciones Completadas Recientes -->
<div class="row">
    <div class="col-12">
        <h5 class="text-success"><i class="bi bi-check-circle"></i> Completadas Recientemente ({{ $asignacionesCompletadas->count() }})</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Vivienda</th>
                        <th>Dirección</th>
                        <th>Fecha Asignación</th>
                        <th>Fecha Completada</th>
                        <th>Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignacionesCompletadas as $asignacion)
                    <tr>
                        <td><strong>{{ $asignacion->vivienda->codigo }}</strong></td>
                        <td>{{ $asignacion->vivienda->direccion }}</td>
                        <td>{{ $asignacion->fecha_asignacion->format('d/m/Y') }}</td>
                        <td>{{ $asignacion->updated_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $asignacion->prioridad_color }}">
                                {{ $asignacion->prioridad_text }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No hay asignaciones completadas recientes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

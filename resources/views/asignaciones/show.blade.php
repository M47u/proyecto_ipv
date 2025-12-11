@extends('layouts.app')

@section('title', 'Detalle de Asignación')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
            <li class="breadcrumb-item active">Detalle</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Detalle de Asignación</h5>
                    <div>
                        @can('update', $asignacion)
                            <a href="{{ route('asignaciones.edit', $asignacion) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información de la Vivienda -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-house-door"></i> Información de la
                            Vivienda</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Código:</strong> {{ $asignacion->vivienda->codigo }}</p>
                                <p><strong>Dirección:</strong> {{ $asignacion->vivienda->direccion }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Barrio:</strong> {{ $asignacion->vivienda->barrio ?? '-' }}</p>
                                <p><strong>Tipo:</strong> {{ $asignacion->vivienda->tipo ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Inspector -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-person-badge"></i> Inspector Asignado
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> {{ $asignacion->inspector->name }}</p>
                                <p><strong>Email:</strong> {{ $asignacion->inspector->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Asignado por:</strong> {{ $asignacion->asignadoPor->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de la Asignación -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-calendar-check"></i> Detalles de la
                            Asignación</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha de Asignación:</strong>
                                    {{ $asignacion->fecha_asignacion->format('d/m/Y') }}</p>
                                <p>
                                    <strong>Fecha Límite:</strong>
                                    @if($asignacion->fecha_limite)
                                        {{ $asignacion->fecha_limite->format('d/m/Y') }}
                                        @if($asignacion->esta_vencida)
                                            <span class="badge bg-danger">Vencida</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Sin fecha límite</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong>Estado:</strong>
                                    <span class="badge bg-{{ $asignacion->estado_color }}">
                                        {{ $asignacion->estado_text }}
                                    </span>
                                </p>
                                <p>
                                    <strong>Prioridad:</strong>
                                    <span class="badge bg-{{ $asignacion->prioridad_color }}">
                                        {{ $asignacion->prioridad_text }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    @if($asignacion->notas)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2"><i class="bi bi-sticky"></i> Notas / Instrucciones</h6>
                            <div class="alert alert-info">
                                {{ $asignacion->notas }}
                            </div>
                        </div>
                    @endif

                    <!-- Fechas de Registro -->
                    <div class="mb-3">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-clock-history"></i> Información del
                            Registro</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small"><strong>Creado:</strong>
                                    {{ $asignacion->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small"><strong>Última actualización:</strong>
                                    {{ $asignacion->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <div>
                            @can('update', $asignacion)
                                <a href="{{ route('asignaciones.edit', $asignacion) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            @endcan
                            @can('delete', $asignacion)
                                <form action="{{ route('asignaciones.destroy', $asignacion) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información adicional -->
        <div class="col-md-4">
            <!-- Estado de la Asignación -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Estado Actual</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge bg-{{ $asignacion->estado_color }} fs-5">
                            {{ $asignacion->estado_text }}
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-{{ $asignacion->prioridad_color }} fs-6">
                            Prioridad: {{ $asignacion->prioridad_text }}
                        </span>
                    </div>
                    @if($asignacion->esta_vencida)
                        <div class="mt-3">
                            <div class="alert alert-danger mb-0">
                                <i class="bi bi-exclamation-triangle"></i> Asignación Vencida
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            @if(auth()->user()->role === 'inspector' && auth()->id() === $asignacion->inspector_id)
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-lightning"></i> Acciones Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('asignaciones.cambiar-estado', $asignacion) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Cambiar Estado</label>
                                <select name="estado" class="form-select form-select-sm">
                                    <option value="pendiente" {{ $asignacion->estado === 'pendiente' ? 'selected' : '' }}>
                                        Pendiente</option>
                                    <option value="en_progreso" {{ $asignacion->estado === 'en_progreso' ? 'selected' : '' }}>En
                                        Progreso</option>
                                    <option value="completada" {{ $asignacion->estado === 'completada' ? 'selected' : '' }}>
                                        Completada</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-check-circle"></i> Actualizar Estado
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
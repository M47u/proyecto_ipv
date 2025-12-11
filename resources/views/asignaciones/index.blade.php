@extends('layouts.app')

@section('title', 'Asignaciones de Viviendas')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Asignaciones</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Asignaciones de Viviendas</h5>
                @can('create', App\Models\Asignacion::class)
                <a href="{{ route('asignaciones.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle"></i> Nueva Asignación
                </a>
                @endcan
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('asignaciones.index') }}" class="mb-4">
                    <div class="row g-3">
                        @if(auth()->user()->role === 'administrador')
                        <div class="col-md-3">
                            <label class="form-label">Inspector</label>
                            <select name="inspector_id" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($inspectores as $inspector)
                                <option value="{{ $inspector->id }}" {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                    {{ $inspector->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prioridad</label>
                            <select name="prioridad" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgente" {{ request('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de Asignaciones -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Vivienda</th>
                                <th>Inspector</th>
                                <th>Fecha Asignación</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asignaciones as $asignacion)
                            <tr class="{{ $asignacion->esta_vencida ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $asignacion->vivienda->codigo }}</strong><br>
                                    <small class="text-muted">{{ $asignacion->vivienda->direccion }}</small>
                                </td>
                                <td>{{ $asignacion->inspector->name }}</td>
                                <td>{{ $asignacion->fecha_asignacion->format('d/m/Y') }}</td>
                                <td>
                                    @if($asignacion->fecha_limite)
                                        {{ $asignacion->fecha_limite->format('d/m/Y') }}
                                        @if($asignacion->esta_vencida)
                                            <span class="badge bg-danger">Vencida</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $asignacion->estado_color }}">
                                        {{ $asignacion->estado_text }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $asignacion->prioridad_color }}">
                                        {{ $asignacion->prioridad_text }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @can('view', $asignacion)
                                        <a href="{{ route('asignaciones.show', $asignacion) }}" 
                                           class="btn btn-info" 
                                           title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @endcan
                                        @can('update', $asignacion)
                                        <a href="{{ route('asignaciones.edit', $asignacion) }}" 
                                           class="btn btn-warning" 
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $asignacion)
                                        <form action="{{ route('asignaciones.destroy', $asignacion) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay asignaciones registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $asignaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

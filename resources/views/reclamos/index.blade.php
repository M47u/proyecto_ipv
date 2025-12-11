@extends('layouts.app')

@section('title', 'Reclamos')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Reclamos</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-exclamation-triangle"></i> Gestión de Reclamos</h2>
    <a href="{{ route('reclamos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Reclamo
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filtros -->
        <form action="{{ route('reclamos.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Buscar por título o código vivienda..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                    <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="prioridad" class="form-select">
                    <option value="">Todas las prioridades</option>
                    <option value="urgente" {{ request('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                    <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Vivienda</th>
                        <th>Título</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reclamos as $reclamo)
                    <tr>
                        <td>#{{ $reclamo->id }}</td>
                        <td>{{ $reclamo->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('viviendas.show', $reclamo->vivienda) }}" class="text-decoration-none">
                                <strong>{{ $reclamo->vivienda->codigo }}</strong>
                            </a>
                        </td>
                        <td>{{ Str::limit($reclamo->titulo, 30) }}</td>
                        <td>
                            <span class="badge bg-{{ $reclamo->prioridad_color }}">
                                {{ ucfirst($reclamo->prioridad) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $reclamo->estado_color }}">
                                {{ str_replace('_', ' ', ucfirst($reclamo->estado)) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reclamos.show', $reclamo) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('reclamos.edit', $reclamo) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->role === 'administrador')
                                <form action="{{ route('reclamos.destroy', $reclamo) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este reclamo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No se encontraron reclamos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando {{ $reclamos->firstItem() ?? 0 }} a {{ $reclamos->lastItem() ?? 0 }}
                de {{ $reclamos->total() }} reclamos
            </div>
            {{ $reclamos->links() }}
        </div>
    </div>
</div>
@endsection

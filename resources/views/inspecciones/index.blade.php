@extends('layouts.app')

@section('title', 'Inspecciones')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Inspecciones</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-clipboard-check"></i> Gestión de Inspecciones</h2>
    <a href="{{ route('inspecciones.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Inspección
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filtros -->
        <form action="{{ route('inspecciones.index') }}" method="GET" class="row g-3 mb-4">
            @if(auth()->user()->role === 'administrador')
            <div class="col-12 col-md-3">
                <select name="inspector_id" class="form-select">
                    <option value="">Todos los inspectores</option>
                    @foreach($inspectores as $inspector)
                        <option value="{{ $inspector->id }}" {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                            {{ $inspector->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-6 col-md-2">
                <select name="estado_general" class="form-select">
                    <option value="">Estado General</option>
                    <option value="excelente" {{ request('estado_general') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                    <option value="bueno" {{ request('estado_general') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                    <option value="regular" {{ request('estado_general') == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="malo" {{ request('estado_general') == 'malo' ? 'selected' : '' }}>Malo</option>
                </select>
            </div>
            
            <div class="col-6 col-md-2">
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
            </div>

            <div class="col-12 col-md-1">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                    <i class="bi bi-search"></i><span class="d-md-none ms-2">Filtrar</span>
                </button>
            </div>
        </form>

        <!-- Tabla (Desktop) -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Vivienda</th>
                        <th>Inspector</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Habitable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspecciones as $inspeccion)
                    <tr>
                        <td>#{{ $inspeccion->id }}</td>
                        <td>
                            {{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}<br>
                            <small class="text-muted">{{ $inspeccion->fecha_inspeccion->format('H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('viviendas.show', $inspeccion->vivienda) }}" class="text-decoration-none">
                                <strong>{{ $inspeccion->vivienda->codigo }}</strong>
                            </a><br>
                            <small class="text-muted">{{ Str::limit($inspeccion->vivienda->direccion, 20) }}</small>
                        </td>
                        <td>{{ $inspeccion->inspector->name }}</td>
                        <td>{{ $inspeccion->tipo_inspeccion_text }}</td>
                        <td>
                            <span class="badge badge-estado-{{ $inspeccion->estado_general }}">
                                {{ ucfirst($inspeccion->estado_general) }}
                            </span>
                        </td>
                        <td>
                            @if($inspeccion->es_habitable)
                                <i class="bi bi-check-circle-fill text-success" title="Habitable"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger" title="No Habitable"></i>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inspecciones.show', $inspeccion) }}" class="btn btn-outline-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('inspecciones.edit', $inspeccion) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">No se encontraron inspecciones</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Cards (Mobile) -->
        <div class="d-md-none">
            @forelse($inspecciones as $inspeccion)
            <div class="mobile-card">
                <div class="mobile-card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>#{{ $inspeccion->id }}</strong> - 
                            <a href="{{ route('viviendas.show', $inspeccion->vivienda) }}">
                                {{ $inspeccion->vivienda->codigo }}
                            </a>
                        </div>
                        <span class="badge badge-estado-{{ $inspeccion->estado_general }}">
                            {{ ucfirst($inspeccion->estado_general) }}
                        </span>
                    </div>
                </div>
                
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Fecha</span>
                    <span class="mobile-card-value">
                        {{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}
                    </span>
                </div>
                
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Dirección</span>
                    <span class="mobile-card-value">
                        {{ Str::limit($inspeccion->vivienda->direccion, 30) }}
                    </span>
                </div>
                
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Inspector</span>
                    <span class="mobile-card-value">{{ $inspeccion->inspector->name }}</span>
                </div>
                
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Tipo</span>
                    <span class="mobile-card-value">{{ $inspeccion->tipo_inspeccion_text }}</span>
                </div>
                
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Habitable</span>
                    <span class="mobile-card-value">
                        @if($inspeccion->es_habitable)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </span>
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('inspecciones.show', $inspeccion) }}" class="btn btn-outline-info btn-sm flex-fill">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('inspecciones.edit', $inspeccion) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-x" style="font-size: 2rem;"></i>
                <p class="mt-2">No se encontraron inspecciones</p>
            </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando {{ $inspecciones->firstItem() ?? 0 }} a {{ $inspecciones->lastItem() ?? 0 }} 
                de {{ $inspecciones->total() }} inspecciones
            </div>
            {{ $inspecciones->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Viviendas')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Viviendas</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-house"></i> Gestión de Viviendas</h2>
        @if(auth()->user()->role === 'administrador')
            <a href="{{ route('viviendas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Vivienda
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filtros -->
            <form action="{{ route('viviendas.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por código o dirección..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="tipo_vivienda" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="proxima_entrega" {{ request('tipo_vivienda') == 'proxima_entrega' ? 'selected' : '' }}>
                            Próxima Entrega
                        </option>
                        <option value="entregada" {{ request('tipo_vivienda') == 'entregada' ? 'selected' : '' }}>
                            Entregada
                        </option>
                        <option value="recuperada" {{ request('tipo_vivienda') == 'recuperada' ? 'selected' : '' }}>
                            Recuperada
                        </option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>

            <!-- Tabla de viviendas (Desktop) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Dirección</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Inspecciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($viviendas as $vivienda)
                            <tr>
                                <td>
                                    <strong>{{ $vivienda->codigo }}</strong>
                                </td>
                                <td>
                                    {{ $vivienda->direccion }}
                                    @if($vivienda->barrio)
                                        <br><small class="text-muted">{{ $vivienda->barrio }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($vivienda->tipo_vivienda === 'proxima_entrega')
                                        <span class="badge bg-info">Próxima Entrega</span>
                                    @elseif($vivienda->tipo_vivienda === 'entregada')
                                        <span class="badge bg-success">Entregada</span>
                                    @else
                                        <span class="badge bg-warning">Recuperada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vivienda->estado === 'activa')
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $vivienda->inspecciones_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('viviendas.show', $vivienda) }}" class="btn btn-outline-info"
                                            title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(auth()->user()->role === 'administrador')
                                            <a href="{{ route('viviendas.edit', $vivienda) }}" class="btn btn-outline-primary"
                                                title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form action="{{ route('viviendas.destroy', $vivienda) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('¿Desactivar esta vivienda?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Desactivar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No se encontraron viviendas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards (Mobile) -->
            <div class="d-md-none">
                @forelse($viviendas as $vivienda)
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>{{ $vivienda->codigo }}</strong>
                                @if($vivienda->tipo_vivienda === 'proxima_entrega')
                                    <span class="badge bg-info">Próxima Entrega</span>
                                @elseif($vivienda->tipo_vivienda === 'entregada')
                                    <span class="badge bg-success">Entregada</span>
                                @else
                                    <span class="badge bg-warning">Recuperada</span>
                                @endif
                            </div>
                        </div>

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Dirección</span>
                            <span class="mobile-card-value">{{ $vivienda->direccion }}</span>
                        </div>

                        @if($vivienda->barrio)
                            <div class="mobile-card-row">
                                <span class="mobile-card-label">Barrio</span>
                                <span class="mobile-card-value">{{ $vivienda->barrio }}</span>
                            </div>
                        @endif

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Estado</span>
                            <span class="mobile-card-value">
                                @if($vivienda->estado === 'activa')
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </span>
                        </div>

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Inspecciones</span>
                            <span class="mobile-card-value">
                                <span class="badge bg-primary">{{ $vivienda->inspecciones_count }}</span>
                            </span>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('viviendas.show', $vivienda) }}" class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            @if(auth()->user()->role === 'administrador')
                                <a href="{{ route('viviendas.edit', $vivienda) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No se encontraron viviendas</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="text-muted small">
                        Mostrando <strong class="text-dark">{{ $viviendas->firstItem() ?? 0 }}</strong> a
                        <strong class="text-dark">{{ $viviendas->lastItem() ?? 0 }}</strong>
                        de <strong class="text-dark">{{ $viviendas->total() }}</strong> viviendas
                    </span>
                </div>
                <div>
                    {{ $viviendas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
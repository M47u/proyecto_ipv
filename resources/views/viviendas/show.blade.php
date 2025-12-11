@extends('layouts.app')

@section('title', 'Detalle de Vivienda')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('viviendas.index') }}">Viviendas</a></li>
        <li class="breadcrumb-item active">{{ $vivienda->codigo }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-house"></i> {{ $vivienda->codigo }}</h2>
    <div>
        @if(auth()->user()->role === 'administrador')
        <a href="{{ route('viviendas.edit', $vivienda) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        @endif
        <a href="{{ route('inspecciones.create', ['vivienda_id' => $vivienda->id]) }}" class="btn btn-success">
            <i class="bi bi-clipboard-plus"></i> Nueva Inspección
        </a>
    </div>
</div>

<!-- Información de la Vivienda -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información General</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Código:</strong><br>
                        {{ $vivienda->codigo }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Tipo:</strong><br>
                        @if($vivienda->tipo_vivienda === 'proxima_entrega')
                            <span class="badge bg-info">Próxima Entrega</span>
                        @elseif($vivienda->tipo_vivienda === 'entregada')
                            <span class="badge bg-success">Entregada</span>
                        @else
                            <span class="badge bg-warning">Recuperada</span>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong>Dirección:</strong><br>
                        {{ $vivienda->direccion }}
                        @if($vivienda->barrio), {{ $vivienda->barrio }}@endif<br>
                        {{ $vivienda->ciudad }}, {{ $vivienda->provincia }}
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Superficie Cubierta:</strong><br>
                        {{ $vivienda->superficie_cubierta ? number_format($vivienda->superficie_cubierta, 2) . ' m²' : '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Superficie Terreno:</strong><br>
                        {{ $vivienda->superficie_terreno ? number_format($vivienda->superficie_terreno, 2) . ' m²' : '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Ambientes:</strong><br>
                        {{ $vivienda->cantidad_ambientes ?? '-' }}
                    </div>
                </div>
                
                @if($vivienda->propietario_actual)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Propietario:</strong><br>
                        {{ $vivienda->propietario_actual }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Teléfono:</strong><br>
                        {{ $vivienda->telefono_contacto ?? '-' }}
                    </div>
                </div>
                @endif
                
                @if($vivienda->observaciones)
                <div class="row">
                    <div class="col-md-12">
                        <strong>Observaciones:</strong><br>
                        {{ $vivienda->observaciones }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Estadísticas</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Inspecciones:</span>
                        <strong>{{ $vivienda->inspecciones->count() }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Reclamos:</span>
                        <strong>{{ $vivienda->reclamos->count() }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Estado:</span>
                        @if($vivienda->estado === 'activa')
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </div>
                </div>
                @if($vivienda->inspecciones->count() > 0)
                <div>
                    <div class="d-flex justify-content-between">
                        <span>Última Inspección:</span>
                        <strong>{{ $vivienda->inspecciones->first()->fecha_inspeccion->format('d/m/Y') }}</strong>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Historial de Inspecciones -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Inspecciones</h6>
    </div>
    <div class="card-body">
        @if($vivienda->inspecciones->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Inspector</th>
                        <th>Tipo</th>
                        <th>Estado General</th>
                        <th>Habitable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vivienda->inspecciones as $inspeccion)
                    <tr>
                        <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}</td>
                        <td>{{ $inspeccion->inspector->name }}</td>
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
                        <td>
                            <a href="{{ route('inspecciones.show', $inspeccion) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center text-muted py-4">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
            No hay inspecciones registradas para esta vivienda
        </p>
        @endif
    </div>
</div>
@endsection

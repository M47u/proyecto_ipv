@extends('layouts.app')

@section('title', 'Detalle de Reclamo')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reclamos.index') }}">Reclamos</a></li>
        <li class="breadcrumb-item active">Reclamo #{{ $reclamo->id }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-exclamation-triangle"></i> Reclamo #{{ $reclamo->id }}</h2>
        <p class="text-muted mb-0">
            Registrado el {{ $reclamo->created_at->format('d/m/Y H:i') }} por {{ $reclamo->usuario->name }}
        </p>
    </div>
    <div>
        <a href="{{ route('reclamos.edit', $reclamo) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        @if(auth()->user()->role === 'administrador')
        <form action="{{ route('reclamos.destroy', $reclamo) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('¿Está seguro de eliminar este reclamo? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">{{ $reclamo->titulo }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Estado:</strong><br>
                        <span class="badge bg-{{ $reclamo->estado_color }} fs-6 mt-1">
                            {{ str_replace('_', ' ', ucfirst($reclamo->estado)) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Prioridad:</strong><br>
                        <span class="badge bg-{{ $reclamo->prioridad_color }} fs-6 mt-1">
                            {{ ucfirst($reclamo->prioridad) }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <strong>Descripción:</strong>
                    <p class="mt-2 p-3 bg-light rounded">{{ $reclamo->descripcion }}</p>
                </div>

                @if($reclamo->fecha_resolucion || $reclamo->notas_resolucion)
                <div class="card border-{{ $reclamo->estado === 'resuelto' ? 'success' : ($reclamo->estado === 'rechazado' ? 'danger' : 'secondary') }} mb-3">
                    <div class="card-header bg-{{ $reclamo->estado === 'resuelto' ? 'success' : ($reclamo->estado === 'rechazado' ? 'danger' : 'secondary') }} text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-{{ $reclamo->estado === 'resuelto' ? 'check-circle' : ($reclamo->estado === 'rechazado' ? 'x-circle' : 'info-circle') }}"></i>
                            {{ $reclamo->estado === 'resuelto' ? 'Resolución' : ($reclamo->estado === 'rechazado' ? 'Rechazado' : 'Información de Cierre') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($reclamo->fecha_resolucion)
                        <p class="mb-2"><strong>Fecha:</strong> {{ $reclamo->fecha_resolucion->format('d/m/Y') }}</p>
                        @endif
                        @if($reclamo->notas_resolucion)
                        <p class="mb-0"><strong>Notas:</strong> {{ $reclamo->notas_resolucion }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-house"></i> Vivienda Asociada</h6>
            </div>
            <div class="card-body">
                <h4>{{ $reclamo->vivienda->codigo }}</h4>
                <p class="text-muted">{{ $reclamo->vivienda->direccion }}</p>
                <hr>
                <div class="d-grid">
                    <a href="{{ route('viviendas.show', $reclamo->vivienda) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye"></i> Ver Vivienda
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Tiempos</h6>
            </div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-2">
                    <span>Creado:</span>
                    <span class="text-end">{{ $reclamo->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Actualizado:</span>
                    <span class="text-end">{{ $reclamo->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($reclamo->fecha_resolucion)
                <div class="d-flex justify-content-between text-success fw-bold">
                    <span>Resuelto:</span>
                    <span class="text-end">{{ $reclamo->fecha_resolucion->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

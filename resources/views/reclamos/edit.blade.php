@extends('layouts.app')

@section('title', 'Editar Reclamo')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reclamos.index') }}">Reclamos</a></li>
        <li class="breadcrumb-item active">Editar #{{ $reclamo->id }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Reclamo #{{ $reclamo->id }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reclamos.update', $reclamo) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Vivienda</label>
                        <input type="text" class="form-control" value="{{ $reclamo->vivienda->codigo }} - {{ $reclamo->vivienda->direccion }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título del Reclamo *</label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo"
                               name="titulo"
                               value="{{ old('titulo', $reclamo->titulo) }}"
                               required>
                        @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select class="form-select @error('prioridad') is-invalid @enderror"
                                    id="prioridad"
                                    name="prioridad"
                                    required>
                                <option value="baja" {{ old('prioridad', $reclamo->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('prioridad', $reclamo->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('prioridad', $reclamo->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgente" {{ old('prioridad', $reclamo->prioridad) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('prioridad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select @error('estado') is-invalid @enderror"
                                    id="estado"
                                    name="estado"
                                    required>
                                <option value="pendiente" {{ old('estado', $reclamo->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ old('estado', $reclamo->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="resuelto" {{ old('estado', $reclamo->estado) == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                <option value="rechazado" {{ old('estado', $reclamo->estado) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                            </select>
                            @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción Detallada *</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                  id="descripcion"
                                  name="descripcion"
                                  rows="5"
                                  required>{{ old('descripcion', $reclamo->descripcion) }}</textarea>
                        @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Resolución</h6>
                            <div class="mb-3">
                                <label for="fecha_resolucion" class="form-label">Fecha Resolución</label>
                                <input type="date"
                                       class="form-control"
                                       id="fecha_resolucion"
                                       name="fecha_resolucion"
                                       value="{{ old('fecha_resolucion', $reclamo->fecha_resolucion ? $reclamo->fecha_resolucion->format('Y-m-d') : '') }}">
                            </div>
                            <div class="mb-3">
                                <label for="notas_resolucion" class="form-label">Notas de Resolución</label>
                                <textarea class="form-control"
                                          id="notas_resolucion"
                                          name="notas_resolucion"
                                          rows="3">{{ old('notas_resolucion', $reclamo->notas_resolucion) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('reclamos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Actualizar Reclamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

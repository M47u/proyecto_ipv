@extends('layouts.app')

@section('title', 'Nuevo Reclamo')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reclamos.index') }}">Reclamos</a></li>
        <li class="breadcrumb-item active">Nuevo Reclamo</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Registrar Nuevo Reclamo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reclamos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="vivienda_id" class="form-label">Vivienda *</label>
                        <select class="form-select @error('vivienda_id') is-invalid @enderror"
                                id="vivienda_id"
                                name="vivienda_id"
                                required>
                            <option value="">Seleccione vivienda...</option>
                            @foreach($viviendas as $v)
                                <option value="{{ $v->id }}" {{ (old('vivienda_id') == $v->id || (isset($vivienda) && $vivienda->id == $v->id)) ? 'selected' : '' }}>
                                    {{ $v->codigo }} - {{ $v->direccion }}
                                </option>
                            @endforeach
                        </select>
                        @error('vivienda_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título del Reclamo *</label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo"
                               name="titulo"
                               value="{{ old('titulo') }}"
                               placeholder="Ej: Filtración en techo"
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
                                <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('prioridad', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgente" {{ old('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('prioridad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado Inicial *</label>
                            <select class="form-select @error('estado') is-invalid @enderror"
                                    id="estado"
                                    name="estado"
                                    required>
                                <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
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
                                  required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('reclamos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Reclamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

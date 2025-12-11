@extends('layouts.app')

@section('title', 'Nueva Asignación')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
        <li class="breadcrumb-item active">Nueva Asignación</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Nueva Asignación de Vivienda</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('asignaciones.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="inspector_id" class="form-label">Inspector *</label>
                            <select class="form-select @error('inspector_id') is-invalid @enderror" 
                                    id="inspector_id" 
                                    name="inspector_id" 
                                    required>
                                <option value="">Seleccione un inspector...</option>
                                @foreach($inspectores as $inspector)
                                <option value="{{ $inspector->id }}" {{ old('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                    {{ $inspector->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('inspector_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vivienda_id" class="form-label">Vivienda *</label>
                            <select class="form-select @error('vivienda_id') is-invalid @enderror" 
                                    id="vivienda_id" 
                                    name="vivienda_id" 
                                    required>
                                <option value="">Seleccione una vivienda...</option>
                                @foreach($viviendas as $vivienda)
                                <option value="{{ $vivienda->id }}" {{ old('vivienda_id') == $vivienda->id ? 'selected' : '' }}>
                                    {{ $vivienda->codigo }} - {{ $vivienda->direccion }}
                                </option>
                                @endforeach
                            </select>
                            @error('vivienda_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fecha_asignacion" class="form-label">Fecha de Asignación *</label>
                            <input type="date" 
                                   class="form-control @error('fecha_asignacion') is-invalid @enderror" 
                                   id="fecha_asignacion" 
                                   name="fecha_asignacion" 
                                   value="{{ old('fecha_asignacion', date('Y-m-d')) }}" 
                                   required>
                            @error('fecha_asignacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="fecha_limite" class="form-label">Fecha Límite</label>
                            <input type="date" 
                                   class="form-control @error('fecha_limite') is-invalid @enderror" 
                                   id="fecha_limite" 
                                   name="fecha_limite" 
                                   value="{{ old('fecha_limite') }}">
                            @error('fecha_limite')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opcional</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas / Instrucciones</label>
                        <textarea class="form-control @error('notas') is-invalid @enderror" 
                                  id="notas" 
                                  name="notas" 
                                  rows="4"
                                  placeholder="Agregue notas o instrucciones especiales para el inspector...">{{ old('notas') }}</textarea>
                        @error('notas')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Crear Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2 en el select de viviendas
    $('#vivienda_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar vivienda por código o dirección...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    // También inicializar Select2 en el select de inspector para consistencia
    $('#inspector_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un inspector...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });
});
</script>
@endpush
@endsection

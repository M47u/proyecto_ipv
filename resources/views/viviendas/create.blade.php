@extends('layouts.app')

@section('title', 'Nueva Vivienda')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('viviendas.index') }}">Viviendas</a></li>
        <li class="breadcrumb-item active">Nueva Vivienda</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-house-add"></i> Nueva Vivienda</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('viviendas.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="codigo" class="form-label">Código *</label>
                            <input type="text" 
                                   class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="{{ old('codigo') }}" 
                                   placeholder="VIV-2024-XXXX"
                                   required>
                            @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tipo_vivienda" class="form-label">Tipo de Vivienda *</label>
                            <select class="form-select @error('tipo_vivienda') is-invalid @enderror" 
                                    id="tipo_vivienda" 
                                    name="tipo_vivienda" 
                                    required>
                                <option value="">Seleccione...</option>
                                <option value="proxima_entrega" {{ old('tipo_vivienda') == 'proxima_entrega' ? 'selected' : '' }}>
                                    Próxima Entrega
                                </option>
                                <option value="entregada" {{ old('tipo_vivienda') == 'entregada' ? 'selected' : '' }}>
                                    Entregada
                                </option>
                                <option value="recuperada" {{ old('tipo_vivienda') == 'recuperada' ? 'selected' : '' }}>
                                    Recuperada
                                </option>
                            </select>
                            @error('tipo_vivienda')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado" 
                                    required>
                                <option value="activa" {{ old('estado', 'activa') == 'activa' ? 'selected' : '' }}>
                                    Activa
                                </option>
                                <option value="inactiva" {{ old('estado') == 'inactiva' ? 'selected' : '' }}>
                                    Inactiva
                                </option>
                            </select>
                            @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="categoria_vivienda" class="form-label">Prototipo de Vivienda</label>
                            <select class="form-select @error('categoria_vivienda') is-invalid @enderror" 
                                    id="categoria_vivienda" 
                                    name="categoria_vivienda">
                                <option value="">Seleccione...</option>
                                <option value="37m2(c5)" {{ old('categoria_vivienda') == '37m2(c5)' ? 'selected' : '' }}>
                                    37m2(c5)
                                </option>
                                <option value="F" {{ old('categoria_vivienda') == 'F' ? 'selected' : '' }}>
                                    F
                                </option>
                                <option value="F DIS" {{ old('categoria_vivienda') == 'F DIS' ? 'selected' : '' }}>
                                    F DIS
                                </option>
                                <option value="H" {{ old('categoria_vivienda') == 'H' ? 'selected' : '' }}>
                                    H
                                </option>
                                <option value="C" {{ old('categoria_vivienda') == 'C' ? 'selected' : '' }}>
                                    C
                                </option>
                            </select>
                            @error('categoria_vivienda')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <input type="text" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="{{ old('direccion') }}" 
                                   required>
                            @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="barrio" class="form-label">Barrio</label>
                            <input type="text" 
                                   class="form-control @error('barrio') is-invalid @enderror" 
                                   id="barrio" 
                                   name="barrio" 
                                   value="{{ old('barrio') }}">
                            @error('barrio')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ciudad" class="form-label">Ciudad *</label>
                            <input type="text" 
                                   class="form-control @error('ciudad') is-invalid @enderror" 
                                   id="ciudad" 
                                   name="ciudad" 
                                   value="{{ old('ciudad', 'Formosa') }}" 
                                   required>
                            @error('ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="provincia" class="form-label">Provincia *</label>
                            <input type="text" 
                                   class="form-control @error('provincia') is-invalid @enderror" 
                                   id="provincia" 
                                   name="provincia" 
                                   value="{{ old('provincia', 'Formosa') }}" 
                                   required>
                            @error('provincia')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="superficie_cubierta" class="form-label">Superficie Cubierta (m²)</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('superficie_cubierta') is-invalid @enderror" 
                                   id="superficie_cubierta" 
                                   name="superficie_cubierta" 
                                   value="{{ old('superficie_cubierta') }}">
                            @error('superficie_cubierta')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="superficie_terreno" class="form-label">Superficie Terreno (m²)</label>
                            <input type="number" 
                                   step="0.01"
                                   class="form-control @error('superficie_terreno') is-invalid @enderror" 
                                   id="superficie_terreno" 
                                   name="superficie_terreno" 
                                   value="{{ old('superficie_terreno') }}">
                            @error('superficie_terreno')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="cantidad_ambientes" class="form-label">Cantidad de Ambientes</label>
                            <input type="number" 
                                   class="form-control @error('cantidad_ambientes') is-invalid @enderror" 
                                   id="cantidad_ambientes" 
                                   name="cantidad_ambientes" 
                                   value="{{ old('cantidad_ambientes') }}"
                                   min="1"
                                   max="10">
                            @error('cantidad_ambientes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="propietario_actual" class="form-label">Propietario Actual</label>
                            <input type="text" 
                                   class="form-control @error('propietario_actual') is-invalid @enderror" 
                                   id="propietario_actual" 
                                   name="propietario_actual" 
                                   value="{{ old('propietario_actual') }}">
                            @error('propietario_actual')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono_contacto" class="form-label">Teléfono de Contacto</label>
                            <input type="text" 
                                   class="form-control @error('telefono_contacto') is-invalid @enderror" 
                                   id="telefono_contacto" 
                                   name="telefono_contacto" 
                                   value="{{ old('telefono_contacto') }}"
                                   placeholder="3794123456">
                            @error('telefono_contacto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                  id="observaciones" 
                                  name="observaciones" 
                                  rows="3">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('viviendas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Crear Vivienda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

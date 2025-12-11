@extends('layouts.app')

@section('title', 'Editar Inspección')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inspecciones.index') }}">Inspecciones</a></li>
        <li class="breadcrumb-item active">Editar #{{ $inspeccion->id }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Inspección #{{ $inspeccion->id }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('inspecciones.update', $inspeccion) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Sección 1: Datos Generales (Solo lectura vivienda) -->
                    <h6 class="text-primary border-bottom pb-2 mb-3">1. Datos Generales</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vivienda</label>
                            <input type="text" class="form-control" value="{{ $inspeccion->vivienda->codigo }} - {{ $inspeccion->vivienda->direccion }}" disabled>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="fecha_inspeccion" class="form-label">Fecha *</label>
                            <input type="datetime-local" 
                                   class="form-control @error('fecha_inspeccion') is-invalid @enderror" 
                                   id="fecha_inspeccion" 
                                   name="fecha_inspeccion" 
                                   value="{{ old('fecha_inspeccion', $inspeccion->fecha_inspeccion->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('fecha_inspeccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="tipo_inspeccion" class="form-label">Tipo Inspección *</label>
                            <select class="form-select @error('tipo_inspeccion') is-invalid @enderror" 
                                    id="tipo_inspeccion" 
                                    name="tipo_inspeccion" 
                                    required>
                                <option value="inicial" {{ old('tipo_inspeccion', $inspeccion->tipo_inspeccion) == 'inicial' ? 'selected' : '' }}>Inicial</option>
                                <option value="seguimiento" {{ old('tipo_inspeccion', $inspeccion->tipo_inspeccion) == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                <option value="reclamo" {{ old('tipo_inspeccion', $inspeccion->tipo_inspeccion) == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                                <option value="pre_entrega" {{ old('tipo_inspeccion', $inspeccion->tipo_inspeccion) == 'pre_entrega' ? 'selected' : '' }}>Pre-Entrega</option>
                                <option value="final" {{ old('tipo_inspeccion', $inspeccion->tipo_inspeccion) == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('tipo_inspeccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sección 2: Evaluación General -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">2. Evaluación del Estado</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="estado_general" class="form-label">Estado General *</label>
                            <select class="form-select @error('estado_general') is-invalid @enderror" 
                                    id="estado_general" 
                                    name="estado_general" 
                                    required>
                                <option value="excelente" {{ old('estado_general', $inspeccion->estado_general) == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                <option value="bueno" {{ old('estado_general', $inspeccion->estado_general) == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="regular" {{ old('estado_general', $inspeccion->estado_general) == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="malo" {{ old('estado_general', $inspeccion->estado_general) == 'malo' ? 'selected' : '' }}>Malo</option>
                                <option value="critico" {{ old('estado_general', $inspeccion->estado_general) == 'critico' ? 'selected' : '' }}>Crítico</option>
                            </select>
                            @error('estado_general')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 mb-3 pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="es_habitable" name="es_habitable" value="1" {{ old('es_habitable', $inspeccion->es_habitable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="es_habitable">Es Habitable</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach(['estructura' => 'Estructura', 'instalacion_electrica' => 'Inst. Eléctrica', 'instalacion_sanitaria' => 'Inst. Sanitaria', 'instalacion_gas' => 'Inst. Gas', 'pintura' => 'Pintura', 'aberturas' => 'Aberturas', 'pisos' => 'Pisos'] as $key => $label)
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ $label }}</label>
                            <select class="form-select form-select-sm" name="estado_{{ $key }}">
                                <option value="">-</option>
                                <option value="excelente" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                <option value="bueno" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="regular" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="malo" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'malo' ? 'selected' : '' }}>Malo</option>
                                <option value="critico" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'critico' ? 'selected' : '' }}>Crítico</option>
                                <option value="no_aplica" {{ old('estado_'.$key, $inspeccion->{'estado_'.$key}) == 'no_aplica' ? 'selected' : '' }}>No Aplica</option>
                            </select>
                        </div>
                        @endforeach
                    </div>

                    <!-- Sección 3: Fallas (Solo agregar nuevas) -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4 d-flex justify-content-between">
                        <span>3. Agregar Nuevas Fallas</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-falla">
                            <i class="bi bi-plus-circle"></i> Agregar Falla
                        </button>
                    </h6>
                    
                    @if($inspeccion->fallas->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted">Fallas existentes (no editables aquí):</small>
                        <ul class="list-group list-group-flush small">
                            @foreach($inspeccion->fallas as $falla)
                            <li class="list-group-item bg-light text-muted">
                                {{ $falla->categoria_text }} - {{ $falla->descripcion }} ({{ ucfirst($falla->gravedad) }})
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div id="fallas-container"></div>

                    <!-- Sección 4: Fotos (Solo agregar nuevas) -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4 d-flex justify-content-between">
                        <span>4. Agregar Nuevas Fotos</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-foto">
                            <i class="bi bi-camera"></i> Agregar Foto
                        </button>
                    </h6>
                    
                    @if($inspeccion->fotos->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted">Fotos existentes: {{ $inspeccion->fotos->count() }}</small>
                    </div>
                    @endif

                    <div id="fotos-container"></div>

                    <!-- Sección 5: Conclusiones -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">5. Conclusiones y Seguimiento</h6>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones Generales</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $inspeccion->observaciones) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="conclusiones" class="form-label">Conclusión Final</label>
                        <textarea class="form-control" id="conclusiones" name="conclusiones" rows="2">{{ old('conclusiones', $inspeccion->conclusiones) }}</textarea>
                    </div>

                    <div class="row bg-light p-3 rounded">
                        <div class="col-md-4">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" id="requiere_seguimiento" name="requiere_seguimiento" value="1" {{ old('requiere_seguimiento', $inspeccion->requiere_seguimiento) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="requiere_seguimiento">Requiere Seguimiento</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="fecha-seguimiento-container" style="display: none;">
                            <label for="fecha_proximo_seguimiento" class="form-label">Fecha Próxima Visita</label>
                            <input type="date" class="form-control" id="fecha_proximo_seguimiento" name="fecha_proximo_seguimiento" value="{{ old('fecha_proximo_seguimiento', $inspeccion->fecha_proximo_seguimiento ? $inspeccion->fecha_proximo_seguimiento->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <hr class="mt-4">
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('inspecciones.show', $inspeccion) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Actualizar Inspección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Templates (Mismos que create) -->
<template id="falla-template">
    <div class="card mb-3 border-warning falla-item">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select class="form-select form-select-sm" name="fallas_categoria[]" required>
                        <option value="">Categoría...</option>
                        <option value="estructural">Estructural</option>
                        <option value="humedad">Humedad</option>
                        <option value="instalaciones">Instalaciones</option>
                        <option value="carpinteria">Carpintería</option>
                        <option value="terminaciones">Terminaciones</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select form-select-sm" name="fallas_gravedad[]" required>
                        <option value="leve">Leve</option>
                        <option value="moderada">Moderada</option>
                        <option value="grave">Grave</option>
                        <option value="critica">Crítica (Acción Inmediata)</option>
                    </select>
                </div>
                <div class="col-md-5 mb-2">
                    <input type="text" class="form-control form-control-sm" name="fallas_descripcion[]" placeholder="Descripción de la falla" required>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-falla">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" class="form-control form-control-sm" name="fallas_ubicacion[]" placeholder="Ubicación específica">
                </div>
            </div>
        </div>
    </div>
</template>

<template id="foto-template">
    <div class="card mb-3 border-info foto-item">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2">
                    <input type="file" class="form-control form-control-sm" name="fotos[]" accept="image/*" required>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select form-select-sm" name="fotos_tipo[]">
                        <option value="general">General</option>
                        <option value="estructura">Estructura</option>
                        <option value="instalaciones">Instalaciones</option>
                        <option value="detalle_falla">Detalle Falla</option>
                        <option value="otra">Otra</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control form-control-sm" name="fotos_descripcion[]" placeholder="Descripción de la imagen">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-foto">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de Fallas
    const fallasContainer = document.getElementById('fallas-container');
    const btnAddFalla = document.getElementById('btn-add-falla');
    const fallaTemplate = document.getElementById('falla-template');

    btnAddFalla.addEventListener('click', function() {
        const clone = fallaTemplate.content.cloneNode(true);
        fallasContainer.appendChild(clone);
    });

    fallasContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-falla')) {
            e.target.closest('.falla-item').remove();
        }
    });

    // Manejo de Fotos
    const fotosContainer = document.getElementById('fotos-container');
    const btnAddFoto = document.getElementById('btn-add-foto');
    const fotoTemplate = document.getElementById('foto-template');

    btnAddFoto.addEventListener('click', function() {
        const clone = fotoTemplate.content.cloneNode(true);
        fotosContainer.appendChild(clone);
    });

    fotosContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-foto')) {
            e.target.closest('.foto-item').remove();
        }
    });

    // Lógica de Seguimiento
    const checkSeguimiento = document.getElementById('requiere_seguimiento');
    const containerSeguimiento = document.getElementById('fecha-seguimiento-container');
    const inputFechaSeguimiento = document.getElementById('fecha_proximo_seguimiento');

    function toggleSeguimiento() {
        if (checkSeguimiento.checked) {
            containerSeguimiento.style.display = 'block';
            inputFechaSeguimiento.required = true;
        } else {
            containerSeguimiento.style.display = 'none';
            inputFechaSeguimiento.required = false;
        }
    }

    checkSeguimiento.addEventListener('change', toggleSeguimiento);
    toggleSeguimiento();
});
</script>
@endpush

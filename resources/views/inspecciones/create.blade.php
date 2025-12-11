@extends('layouts.app')

@section('title', 'Nueva Inspección')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inspecciones.index') }}">Inspecciones</a></li>
        <li class="breadcrumb-item active">Nueva Inspección</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clipboard-plus"></i> Registrar Nueva Inspección</h5>
                <span id="gps-status" class="badge bg-warning text-dark"><i class="bi bi-geo-alt"></i> Buscando GPS...</span>
            </div>
            <div class="card-body">
                <form action="{{ route('inspecciones.store') }}" method="POST" enctype="multipart/form-data" id="inspection-form">
                    @csrf

                    <!-- Coordenadas GPS (Ocultas) -->
                    <input type="hidden" name="latitud" id="latitud">
                    <input type="hidden" name="longitud" id="longitud">
                    <input type="hidden" name="precision_gps" id="precision_gps">

                    <!-- Sección 1: Datos Generales -->
                    <h6 class="text-primary border-bottom pb-2 mb-3">1. Datos Generales</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vivienda_id" class="form-label">Vivienda *</label>
                            <select class="form-select @error('vivienda_id') is-invalid @enderror"
                                    id="vivienda_id"
                                    name="vivienda_id"
                                    required>
                                <option value="" selected disabled>Seleccione vivienda...</option>
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

                        <div class="col-md-3 mb-3">
                            <label for="fecha_inspeccion" class="form-label">Fecha *</label>
                            <input type="datetime-local"
                                   class="form-control @error('fecha_inspeccion') is-invalid @enderror"
                                   id="fecha_inspeccion"
                                   name="fecha_inspeccion"
                                   value="{{ old('fecha_inspeccion', now()->format('Y-m-d\TH:i')) }}"
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
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="inicial" {{ old('tipo_inspeccion') == 'inicial' ? 'selected' : '' }}>Inicial</option>
                                <option value="seguimiento" {{ old('tipo_inspeccion') == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                <option value="reclamo" {{ old('tipo_inspeccion') == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                                <option value="pre_entrega" {{ old('tipo_inspeccion') == 'pre_entrega' ? 'selected' : '' }}>Pre-Entrega</option>
                                <option value="final" {{ old('tipo_inspeccion') == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('tipo_inspeccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sección 2: Evaluación del Estado -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">2. Evaluación del Estado</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="estado_general" class="form-label">Estado General *</label>
                            <select class="form-select @error('estado_general') is-invalid @enderror"
                                    id="estado_general"
                                    name="estado_general"
                                    required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="excelente" {{ old('estado_general') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                <option value="bueno" {{ old('estado_general') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="regular" {{ old('estado_general') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="malo" {{ old('estado_general') == 'malo' ? 'selected' : '' }}>Malo</option>
                                <option value="critico" {{ old('estado_general') == 'critico' ? 'selected' : '' }}>Crítico</option>
                            </select>
                            @error('estado_general')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 mb-3 pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="es_habitable" name="es_habitable" value="1" {{ old('es_habitable', 1) ? 'checked' : '' }}>
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
                                <option value="excelente" {{ old('estado_'.$key) == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                <option value="bueno" {{ old('estado_'.$key) == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="regular" {{ old('estado_'.$key) == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="malo" {{ old('estado_'.$key) == 'malo' ? 'selected' : '' }}>Malo</option>
                                <option value="critico" {{ old('estado_'.$key) == 'critico' ? 'selected' : '' }}>Crítico</option>
                                <option value="no_aplica" {{ old('estado_'.$key) == 'no_aplica' ? 'selected' : '' }}>No Aplica</option>
                            </select>
                        </div>
                        @endforeach
                    </div>

                    <!-- Sección 3: Fallas -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4 d-flex justify-content-between">
                        <span>3. Fallas Detectadas</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-falla">
                            <i class="bi bi-plus-circle"></i> Agregar Falla
                        </button>
                    </h6>
                    <div id="fallas-container">
                        <!-- Las fallas se agregarán aquí dinámicamente -->
                    </div>
                    <div class="alert alert-light text-center" id="no-fallas-msg">
                        <small class="text-muted">No hay fallas registradas. Click en "Agregar Falla" si es necesario.</small>
                    </div>

                    <!-- Sección 4: Fotos -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4 d-flex justify-content-between">
                        <span>4. Evidencia Fotográfica</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-foto">
                            <i class="bi bi-camera"></i> Agregar Foto
                        </button>
                    </h6>
                    <div id="fotos-container">
                        <!-- Las fotos se agregarán aquí dinámicamente -->
                    </div>
                    <div class="alert alert-light text-center" id="no-fotos-msg">
                        <small class="text-muted">Click en "Agregar Foto" para subir evidencia.</small>
                    </div>

                    <!-- Sección 5: Conclusiones -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">5. Conclusiones y Seguimiento</h6>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones Generales</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="conclusiones" class="form-label">Conclusión Final</label>
                        <textarea class="form-control" id="conclusiones" name="conclusiones" rows="2">{{ old('conclusiones') }}</textarea>
                    </div>

                    <div class="row bg-light p-3 rounded">
                        <div class="col-md-4">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" id="requiere_seguimiento" name="requiere_seguimiento" value="1" {{ old('requiere_seguimiento') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="requiere_seguimiento">Requiere Seguimiento</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="fecha-seguimiento-container" style="display: none;">
                            <label for="fecha_proximo_seguimiento" class="form-label">Fecha Próxima Visita</label>
                            <input type="date" class="form-control" id="fecha_proximo_seguimiento" name="fecha_proximo_seguimiento" value="{{ old('fecha_proximo_seguimiento') }}">
                        </div>
                    </div>

                    <hr class="mt-4">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('inspecciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Guardar Inspección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template para Falla -->
<template id="falla-template">
    <div class="card mb-3 border-warning falla-item">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select class="form-select form-select-sm" name="fallas_categoria[]" required>
                        <option value="" selected disabled>Categoría...</option>
                        <option value="estructura">Estructura</option>
                        <option value="electrica">Inst. Eléctrica</option>
                        <option value="sanitaria">Inst. Sanitaria</option>
                        <option value="gas">Inst. Gas</option>
                        <option value="pintura">Pintura</option>
                        <option value="aberturas">Aberturas</option>
                        <option value="pisos">Pisos</option>
                        <option value="otras">Otras</option>
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
                    <input type="text" class="form-control form-control-sm" name="fallas_ubicacion[]" placeholder="Ubicación específica (ej: Baño principal, pared norte)">
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Template para Foto -->
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
    // Initialize Select2 on vivienda dropdown
    $('#vivienda_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione vivienda...',
        allowClear: true,
        width: '100%'
    });

    // 1. Geolocalización
    const gpsStatus = document.getElementById('gps-status');

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitud').value = position.coords.latitude;
                document.getElementById('longitud').value = position.coords.longitude;
                document.getElementById('precision_gps').value = position.coords.accuracy;

                gpsStatus.className = 'badge bg-success';
                gpsStatus.innerHTML = '<i class="bi bi-geo-alt-fill"></i> GPS Activo (±' + Math.round(position.coords.accuracy) + 'm)';
            },
            function(error) {
                console.error("Error GPS:", error);
                gpsStatus.className = 'badge bg-danger';
                gpsStatus.innerHTML = '<i class="bi bi-geo-alt-fill"></i> GPS Error';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        gpsStatus.className = 'badge bg-secondary';
        gpsStatus.innerHTML = 'GPS No Soportado';
    }

    // 2. Manejo de Fallas
    const fallasContainer = document.getElementById('fallas-container');
    const btnAddFalla = document.getElementById('btn-add-falla');
    const noFallasMsg = document.getElementById('no-fallas-msg');
    const fallaTemplate = document.getElementById('falla-template');

    btnAddFalla.addEventListener('click', function() {
        const clone = fallaTemplate.content.cloneNode(true);
        fallasContainer.appendChild(clone);
        noFallasMsg.style.display = 'none';
    });

    fallasContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-falla')) {
            e.target.closest('.falla-item').remove();
            if (fallasContainer.children.length === 0) {
                noFallasMsg.style.display = 'block';
            }
        }
    });

    // 3. Manejo de Fotos
    const fotosContainer = document.getElementById('fotos-container');
    const btnAddFoto = document.getElementById('btn-add-foto');
    const noFotosMsg = document.getElementById('no-fotos-msg');
    const fotoTemplate = document.getElementById('foto-template');

    btnAddFoto.addEventListener('click', function() {
        const clone = fotoTemplate.content.cloneNode(true);
        fotosContainer.appendChild(clone);
        noFotosMsg.style.display = 'none';
    });

    fotosContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-foto')) {
            e.target.closest('.foto-item').remove();
            if (fotosContainer.children.length === 0) {
                noFotosMsg.style.display = 'block';
            }
        }
    });

    // 4. Lógica de Seguimiento
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
            inputFechaSeguimiento.value = '';
        }
    }

    checkSeguimiento.addEventListener('change', toggleSeguimiento);
    toggleSeguimiento(); // Estado inicial
});
</script>
@endpush

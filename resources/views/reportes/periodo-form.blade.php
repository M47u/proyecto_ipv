@extends('layouts.app')

@section('title', 'Inspecciones por Período')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
            <li class="breadcrumb-item active">Inspecciones por Período</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="page-header mb-4">
        <h2><i class="bi bi-calendar-range"></i> Reporte: Inspecciones por Período</h2>
        <p class="text-muted">Genera un reporte filtrable de inspecciones realizadas en un rango de fechas</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reportes.periodo') }}" method="GET">
                <div class="row mb-3">
                    <!-- Rango de Fechas -->
                    <div class="col-md-6">
                        <label for="fecha_desde" class="form-label">Fecha Desde <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_desde') is-invalid @enderror" 
                               id="fecha_desde" name="fecha_desde" 
                               value="{{ old('fecha_desde', request('fecha_desde', now()->subMonth()->format('Y-m-d'))) }}" 
                               required>
                        @error('fecha_desde')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_hasta') is-invalid @enderror" 
                               id="fecha_hasta" name="fecha_hasta" 
                               value="{{ old('fecha_hasta', request('fecha_hasta', now()->format('Y-m-d'))) }}" 
                               required>
                        @error('fecha_hasta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="text-muted mb-3"><i class="bi bi-sliders"></i> Filtros Adicionales (Opcionales)</h6>

                <div class="row mb-3">
                    <!-- Inspector -->
                    <div class="col-md-6">
                        <label for="inspector_id" class="form-label">Inspector</label>
                        <select class="form-select" id="inspector_id" name="inspector_id">
                            <option value="">Todos los inspectores</option>
                            @foreach($inspectores as $inspector)
                                <option value="{{ $inspector->id }}" 
                                    {{ old('inspector_id', request('inspector_id')) == $inspector->id ? 'selected' : '' }}>
                                    {{ $inspector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Inspección -->
                    <div class="col-md-6">
                        <label for="tipo_inspeccion" class="form-label">Tipo de Inspección</label>
                        <select class="form-select" id="tipo_inspeccion" name="tipo_inspeccion">
                            <option value="">Todos los tipos</option>
                            <option value="inicial" {{ old('tipo_inspeccion', request('tipo_inspeccion')) == 'inicial' ? 'selected' : '' }}>Inicial</option>
                            <option value="seguimiento" {{ old('tipo_inspeccion', request('tipo_inspeccion')) == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                            <option value="reclamo" {{ old('tipo_inspeccion', request('tipo_inspeccion')) == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                            <option value="pre_entrega" {{ old('tipo_inspeccion', request('tipo_inspeccion')) == 'pre_entrega' ? 'selected' : '' }}>Pre-Entrega</option>
                            <option value="final" {{ old('tipo_inspeccion', request('tipo_inspeccion')) == 'final' ? 'selected' : '' }}>Final</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Estado General -->
                    <div class="col-md-6">
                        <label for="estado_general" class="form-label">Estado General</label>
                        <select class="form-select" id="estado_general" name="estado_general">
                            <option value="">Todos los estados</option>
                            <option value="excelente" {{ old('estado_general', request('estado_general')) == 'excelente' ? 'selected' : '' }}>Excelente</option>
                            <option value="bueno" {{ old('estado_general', request('estado_general')) == 'bueno' ? 'selected' : '' }}>Bueno</option>
                            <option value="regular" {{ old('estado_general', request('estado_general')) == 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="malo" {{ old('estado_general', request('estado_general')) == 'malo' ? 'selected' : '' }}>Malo</option>
                            <option value="critico" {{ old('estado_general', request('estado_general')) == 'critico' ? 'selected' : '' }}>Crítico</option>
                        </select>
                    </div>

                    <!-- Tipo de Vivienda -->
                    <div class="col-md-6">
                        <label for="tipo_vivienda" class="form-label">Tipo de Vivienda</label>
                        <select class="form-select" id="tipo_vivienda" name="tipo_vivienda">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposVivienda as $tipo)
                                <option value="{{ $tipo }}" 
                                    {{ old('tipo_vivienda', request('tipo_vivienda')) == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Habitabilidad -->
                    <div class="col-md-6">
                        <label for="es_habitable" class="form-label">Habitabilidad</label>
                        <select class="form-select" id="es_habitable" name="es_habitable">
                            <option value="">Todas las viviendas</option>
                            <option value="1" {{ old('es_habitable', request('es_habitable')) === '1' ? 'selected' : '' }}>Solo Habitables</option>
                            <option value="0" {{ old('es_habitable', request('es_habitable')) === '0' ? 'selected' : '' }}>Solo No Habitables</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    
                    <div>
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Información de ayuda -->
    <div class="alert alert-info mt-4" role="alert">
        <i class="bi bi-info-circle"></i> 
        <strong>Información:</strong>
        <ul class="mb-0 mt-2">
            <li>Las fechas son obligatorias para generar el reporte</li>
            <li>Los filtros adicionales son opcionales y permiten refinar los resultados</li>
            <li>Una vez generado el reporte, podrás exportarlo en formato PDF o Excel</li>
            <li>El reporte incluirá estadísticas, gráficos y el detalle completo de las inspecciones</li>
        </ul>
    </div>
@endsection

@section('scripts')
    <script>
        // Validación de fechas en el cliente
        document.getElementById('fecha_hasta').addEventListener('change', function() {
            const fechaDesde = document.getElementById('fecha_desde').value;
            const fechaHasta = this.value;
            
            if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
                alert('La fecha hasta no puede ser anterior a la fecha desde');
                this.value = fechaDesde;
            }
        });

        // Auto-ajustar fecha hasta cuando se cambia fecha desde
        document.getElementById('fecha_desde').addEventListener('change', function() {
            const fechaDesde = this.value;
            const fechaHasta = document.getElementById('fecha_hasta').value;
            
            if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
                document.getElementById('fecha_hasta').value = fechaDesde;
            }
        });
    </script>
@endsection

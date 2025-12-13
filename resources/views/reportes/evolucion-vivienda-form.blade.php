@extends('layouts.app')

@section('title', 'Reporte Evolución de Vivienda')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Evolución de Vivienda</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-house-check"></i> Reporte de Evolución de Vivienda
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>¿Qué incluye este reporte?</strong>
                    <ul class="mb-0 mt-2">
                        <li>Historial completo de inspecciones</li>
                        <li>Gráficos de evolución por áreas</li>
                        <li>Detalle de todas las fallas encontradas</li>
                        <li>Reclamos asociados</li>
                        <li>Estadísticas y métricas clave</li>
                    </ul>
                </div>

                <form id="formReporte" method="GET">
                    <div class="mb-4">
                        <label for="vivienda_id" class="form-label">
                            <i class="bi bi-search"></i> Seleccione la Vivienda
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg" id="vivienda_id" name="vivienda_id" required>
                            <option value="">-- Seleccione una vivienda --</option>
                            @foreach($viviendas as $vivienda)
                                <option value="{{ $vivienda->id }}">
                                    {{ $vivienda->codigo }} - {{ $vivienda->direccion }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Busque por código o dirección
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="btnPrevisualizar" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye"></i> Previsualizar Reporte
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="bi bi-file-pdf text-danger fs-1"></i>
                            <h6 class="mt-2">Exportar PDF</h6>
                            <small class="text-muted">Formato imprimible</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="bi bi-file-excel text-success fs-1"></i>
                            <h6 class="mt-2">Exportar Excel</h6>
                            <small class="text-muted">Análisis de datos</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="bi bi-graph-up text-primary fs-1"></i>
                            <h6 class="mt-2">Vista Web</h6>
                            <small class="text-muted">Gráficos interactivos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Debug: Verificar que el script se carga
console.log('Script de reportes cargado');

document.addEventListener('DOMContentLoaded', function() {
    const btnPrevisualizar = document.getElementById('btnPrevisualizar');
    const selectVivienda = document.getElementById('vivienda_id');
    
    console.log('Botón encontrado:', btnPrevisualizar);
    console.log('Select encontrado:', selectVivienda);
    
    if (btnPrevisualizar) {
        btnPrevisualizar.addEventListener('click', function(e) {
            console.log('Click detectado en botón');
            e.preventDefault();
            
            const viviendaId = selectVivienda.value;
            console.log('Vivienda ID seleccionada:', viviendaId);
            
            if (!viviendaId || viviendaId === '') {
                alert('Por favor seleccione una vivienda');
                return;
            }
            
            // Construir URL usando route helper de Laravel
            const url = "{{ route('reportes.vivienda', ':id') }}".replace(':id', viviendaId);
            console.log('Redirigiendo a:', url);
            
            // Redirigir a la vista previa
            window.location.href = url;
        });
    } else {
        console.error('No se encontró el botón btnPrevisualizar');
    }
});
</script>
@endsection
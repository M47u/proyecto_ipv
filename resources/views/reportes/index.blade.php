@extends('layouts.app')

@section('title', 'Reportes')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header mb-4">
    <h2><i class="bi bi-file-earmark-bar-graph"></i> Centro de Reportes</h2>
    <p class="text-muted">Genera reportes detallados en PDF o Excel</p>
</div>

<div class="row">
    <!-- REPORTE 1: Evolución de Vivienda -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm hover-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-house-check fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Evolución de Vivienda</h5>
                </div>
                
                <p class="card-text text-muted">
                    Historial completo de inspecciones, fallas y reclamos de una vivienda específica con gráficos de evolución.
                </p>

                <div class="mt-3">
                    <span class="badge bg-success me-1"><i class="bi bi-check-circle"></i> Disponible</span>
                    <span class="badge bg-info"><i class="bi bi-file-pdf"></i> PDF</span>
                    <span class="badge bg-success"><i class="bi bi-file-excel"></i> Excel</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('reportes.evolucion-vivienda-form') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-play-circle"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- REPORTE 2: Inspecciones por Período -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-calendar-range fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Inspecciones por Período</h5>
                </div>
                
                <p class="card-text text-muted">
                    Listado filtrable de inspecciones en un rango de fechas con estadísticas y comparativas.
                </p>

                <div class="mt-3">
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Próximamente</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-lock"></i> En Desarrollo
                </button>
            </div>
        </div>
    </div>

    <!-- REPORTE 3: Productividad Inspectores -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Productividad Inspectores</h5>
                </div>
                
                <p class="card-text text-muted">
                    Desempeño individual y comparativo de inspectores con métricas de productividad.
                </p>

                <div class="mt-3">
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Próximamente</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-lock"></i> En Desarrollo
                </button>
            </div>
        </div>
    </div>

    <!-- REPORTE 4: Estado de Viviendas -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-clipboard-data fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Estado de Viviendas</h5>
                </div>
                
                <p class="card-text text-muted">
                    Snapshot del estado actual de todas las viviendas con indicadores de habitabilidad.
                </p>

                <div class="mt-3">
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Próximamente</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-lock"></i> En Desarrollo
                </button>
            </div>
        </div>
    </div>

    <!-- REPORTE 5: Dashboard Ejecutivo -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-graph-up fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Dashboard Ejecutivo</h5>
                </div>
                
                <p class="card-text text-muted">
                    Resumen general mensual con KPIs principales y gráficos de tendencia.
                </p>

                <div class="mt-3">
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Próximamente</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-lock"></i> En Desarrollo
                </button>
            </div>
        </div>
    </div>

    <!-- REPORTE 6: Mapa de Inspecciones -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-map fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0">Mapa de Inspecciones</h5>
                </div>
                
                <p class="card-text text-muted">
                    Visualización geográfica de todas las inspecciones realizadas con exportación.
                </p>

                <div class="mt-3">
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Próximamente</span>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-lock"></i> En Desarrollo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>
@endsection
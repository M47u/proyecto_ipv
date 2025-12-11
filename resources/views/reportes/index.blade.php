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
<div class="page-header">
    <h2><i class="bi bi-file-earmark-bar-graph"></i> Reportes</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            <strong>Módulo en desarrollo:</strong> El sistema de reportes con exportación PDF/Excel estará disponible próximamente.
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5><i class="bi bi-file-pdf text-danger"></i> Reportes PDF</h5>
                        <ul class="text-muted">
                            <li>Evolución de vivienda</li>
                            <li>Estadísticas generales</li>
                            <li>Mapa de inspecciones</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5><i class="bi bi-file-excel text-success"></i> Reportes Excel</h5>
                        <ul class="text-muted">
                            <li>Inspecciones por período</li>
                            <li>Listado de viviendas</li>
                            <li>Reclamos pendientes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

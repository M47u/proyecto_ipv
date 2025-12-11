@extends('layouts.app')

@section('title', 'Detalle de Inspección')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inspecciones.index') }}">Inspecciones</a></li>
        <li class="breadcrumb-item active">Inspección #{{ $inspeccion->id }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-clipboard-check"></i> Inspección #{{ $inspeccion->id }}</h2>
        <p class="text-muted mb-0">
            Vivienda: <a href="{{ route('viviendas.show', $inspeccion->vivienda) }}">{{ $inspeccion->vivienda->codigo }}</a> |
            Fecha: {{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }}
        </p>
    </div>
    <div>
        <a href="{{ route('inspecciones.edit', $inspeccion) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Editar</a>
        <form action="{{ route('inspecciones.destroy', $inspeccion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta inspección? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Eliminar</button>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <!-- Información General -->
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-info-circle"></i> Información General</h6></div>
            <div class="card-body">
                <p><strong>Tipo de inspección:</strong> {{ ucfirst($inspeccion->tipo_inspeccion) }}</p>
                <p><strong>Estado general:</strong> {{ ucfirst($inspeccion->estado_general) }}</p>
                <p><strong>Habitabilidad:</strong> @if($inspeccion->es_habitable) <span class="badge bg-success">Habitable</span> @else <span class="badge bg-danger">No habitable</span> @endif</p>
            </div>
        </div>

        <!-- Evaluación por áreas -->
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-list-check"></i> Evaluación por Áreas</h6></div>
            <div class="card-body">
                <div class="row">
                    @foreach([
                        'estructura' => 'Estructura',
                        'instalacion_electrica' => 'Inst. Eléctrica',
                        'instalacion_sanitaria' => 'Inst. Sanitaria',
                        'instalacion_gas' => 'Inst. Gas',
                        'pintura' => 'Pintura',
                        'aberturas' => 'Aberturas',
                        'pisos' => 'Pisos'
                    ] as $field => $label)
                        <div class="col-md-4 mb-3">
                            <strong>{{ $label }}:</strong>
                            {{ ucfirst($inspeccion->{'estado_'.$field} ?? '-') }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Fallas -->
        @if($inspeccion->fallas->count() > 0)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark"><h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Fallas Detectadas ({{ $inspeccion->fallas->count() }})</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Gravedad</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inspeccion->fallas as $falla)
                            <tr>
                                <td><span class="badge bg-{{ $falla->gravedad_color }}">{{ ucfirst($falla->gravedad) }}</span></td>
                                <td>{{ $falla->categoria_text }}</td>
                                <td>{{ $falla->descripcion }}</td>
                                <td>{{ $falla->ubicacion ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Observaciones y Conclusiones -->
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-card-text"></i> Observaciones y Conclusiones</h6></div>
            <div class="card-body">
                @if($inspeccion->observaciones)
                <div class="mb-3"><strong>Observaciones:</strong><p class="mb-0">{{ $inspeccion->observaciones }}</p></div>
                @endif
                @if($inspeccion->conclusiones)
                <div class="mb-3"><strong>Conclusión Final:</strong><p class="mb-0">{{ $inspeccion->conclusiones }}</p></div>
                @endif
                @if($inspeccion->requiere_seguimiento)
                <div class="alert alert-warning mb-0"><i class="bi bi-calendar-event"></i> <strong>Requiere Seguimiento:</strong> Programado para el {{ \Carbon\Carbon::parse($inspeccion->fecha_proximo_seguimiento)->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Galería de Fotos -->
        @if($inspeccion->fotos->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-images"></i> Evidencia Fotográfica ({{ $inspeccion->fotos->count() }})</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($inspeccion->fotos as $foto)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100">
                            <a href="{{ Storage::url($foto->ruta_archivo) }}" target="_blank">
                                <img src="{{ Storage::url($foto->ruta_archivo) }}" class="card-img-top" alt="Foto inspección" style="height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body p-2">
                                <span class="badge bg-secondary mb-1">{{ ucfirst($foto->tipo_foto) }}</span>
                                @if($foto->descripcion)
                                <p class="card-text small text-muted">{{ Str::limit($foto->descripcion, 50) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Mapa -->
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-geo-alt"></i> Ubicación</h6></div>
            <div class="card-body p-0">
                @if($inspeccion->latitud && $inspeccion->longitud)
                <div id="map-detail" style="height: 300px;"></div>
                @else
                <div class="text-center py-5 text-muted"><i class="bi bi-geo-alt-slash" style="font-size: 2rem;"></i><p class="mt-2">Sin coordenadas GPS registradas</p></div>
                @endif
            </div>
            @if($inspeccion->latitud)
            <div class="card-footer bg-white small text-muted">Lat: {{ $inspeccion->latitud }}, Lng: {{ $inspeccion->longitud }}<br>Precisión: ±{{ $inspeccion->precision_gps ?? '?' }}m</div>
            @endif
        </div>
        <!-- Inspector -->
        <div class="card mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-person-badge"></i> Inspector</h6></div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            {{ substr($inspeccion->inspector->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ $inspeccion->inspector->name }}</h6>
                        <small class="text-muted">{{ $inspeccion->inspector->email }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($inspeccion->latitud && $inspeccion->longitud)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map-detail').setView([{{ $inspeccion->latitud }}, {{ $inspeccion->longitud }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        var marker = L.marker([{{ $inspeccion->latitud }}, {{ $inspeccion->longitud }}]).addTo(map);
        marker.bindPopup('<b>{{ $inspeccion->vivienda->codigo }}</b><br>{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}').openPopup();
    });
</script>
@endif
@endpush
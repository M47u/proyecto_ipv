@extends('layouts.app')

@section('title', 'Mapa de Inspecciones')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
        <li class="breadcrumb-item active">Mapa de Inspecciones</li>
    </ol>
</nav>
@endsection

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    #map {
        height: 600px !important;
        min-height: 600px;
        width: 100% !important;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }
    
    .map-container {
        height: 600px;
        min-height: 600px;
        padding: 0;
        margin: 0;
    }
    
    .leaflet-container {
        height: 100%;
        width: 100%;
        border-radius: 8px;
    }
    
    .leaflet-popup-content {
        min-width: 250px;
    }
    
    .popup-content h6 {
        margin-bottom: 10px;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .popup-info {
        font-size: 0.9rem;
    }
    
    .popup-info p {
        margin-bottom: 5px;
    }
    
    .popup-info strong {
        color: #34495e;
    }
    
    .badge-excelente { background-color: #27ae60; }
    .badge-bueno { background-color: #3498db; }
    .badge-regular { background-color: #f39c12; }
    .badge-malo { background-color: #e74c3c; }
    .badge-critico { background-color: #c0392b; }
    
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .legend-marker {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 10px;
        border: 2px solid white;
        box-shadow: 0 0 4px rgba(0,0,0,0.3);
    }
    
    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">🗺️ Mapa de Inspecciones</h2>
            <p class="text-muted mb-0">Visualización geográfica de todas las inspecciones realizadas</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                <i class="fas fa-filter me-1"></i> Filtros
            </button>
            <button type="button" class="btn btn-danger" id="btnExportarPDF">
                <i class="fas fa-file-pdf me-1"></i> Exportar PDF
            </button>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Inspecciones</h6>
                            <h3 class="mb-0">{{ $estadisticas['total_inspecciones'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-map-marked-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Buenas/Excelentes</h6>
                            <h3 class="mb-0 text-success">{{ $estadisticas['excelentes'] + $estadisticas['buenas'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Regulares</h6>
                            <h3 class="mb-0 text-warning">{{ $estadisticas['regulares'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Malas/Críticas</h6>
                            <h3 class="mb-0 text-danger">{{ $estadisticas['malas'] + $estadisticas['criticas'] }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leyenda y Mapa -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body map-container">
                    <div id="map" style="height: 600px !important; min-height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Leyenda</h6>
                </div>
                <div class="card-body">
                    <div class="legend-item">
                        <div class="legend-marker" style="background-color: #27ae60;"></div>
                        <span>Excelente</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker" style="background-color: #3498db;"></div>
                        <span>Bueno</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker" style="background-color: #f39c12;"></div>
                        <span>Regular</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker" style="background-color: #e74c3c;"></div>
                        <span>Malo</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker" style="background-color: #c0392b;"></div>
                        <span>Crítico</span>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Resumen</h6>
                    <p class="mb-2">
                        <strong>Promedio de Puntaje:</strong><br>
                        <span class="badge bg-info">{{ $estadisticas['promedio_puntaje'] }}%</span>
                    </p>
                    <p class="mb-2">
                        <strong>Total Marcadores:</strong><br>
                        {{ $estadisticas['total_inspecciones'] }}
                    </p>
                </div>
            </div>

            <!-- Filtros Activos -->
            @if(request()->hasAny(['estado', 'fecha_desde', 'fecha_hasta', 'inspector_id', 'puntaje_min', 'puntaje_max']))
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros Activos</h6>
                </div>
                <div class="card-body">
                    @if(request('estado_general'))
                        <span class="badge bg-secondary mb-2 d-block">Estado: {{ ucfirst(request('estado_general')) }}</span>
                    @endif
                    @if(request('fecha_desde'))
                        <span class="badge bg-secondary mb-2 d-block">Desde: {{ request('fecha_desde') }}</span>
                    @endif
                    @if(request('fecha_hasta'))
                        <span class="badge bg-secondary mb-2 d-block">Hasta: {{ request('fecha_hasta') }}</span>
                    @endif
                    @if(request('inspector_id'))
                        @php $inspector = $inspectores->find(request('inspector_id')); @endphp
                        <span class="badge bg-secondary mb-2 d-block">Inspector: {{ $inspector->name ?? 'N/A' }}</span>
                    @endif
                    @if(request('puntaje_min'))
                        <span class="badge bg-secondary mb-2 d-block">Puntaje Mín: {{ request('puntaje_min') }}%</span>
                    @endif
                    @if(request('puntaje_max'))
                        <span class="badge bg-secondary mb-2 d-block">Puntaje Máx: {{ request('puntaje_max') }}%</span>
                    @endif
                    
                    <a href="{{ route('reportes.mapa-inspecciones') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-times me-1"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filtrar Mapa de Inspecciones</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('reportes.mapa-inspecciones') }}" id="formFiltros">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado General</label>
                            <select name="estado_general" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="excelente" {{ request('estado_general') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                <option value="bueno" {{ request('estado_general') == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="regular" {{ request('estado_general') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="malo" {{ request('estado_general') == 'malo' ? 'selected' : '' }}>Malo</option>
                                <option value="critico" {{ request('estado_general') == 'critico' ? 'selected' : '' }}>Crítico</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Inspector</label>
                            <select name="inspector_id" class="form-select">
                                <option value="">Todos los inspectores</option>
                                @foreach($inspectores as $inspector)
                                    <option value="{{ $inspector->id }}" {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                        {{ $inspector->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Puntaje Mínimo (%)</label>
                            <input type="number" name="puntaje_min" class="form-control" min="0" max="100" 
                                   value="{{ request('puntaje_min') }}" placeholder="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Puntaje Máximo (%)</label>
                            <input type="number" name="puntaje_max" class="form-control" min="0" max="100" 
                                   value="{{ request('puntaje_max') }}" placeholder="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
// Variable para evitar inicialización múltiple
let mapInitialized = false;

// Esperar a que todo esté completamente cargado
window.addEventListener('load', function() {
    if (!mapInitialized) {
        initMap();
    }
});

// También intentar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (!mapInitialized) {
        initMap();
    }
});

function initMap() {
    // Evitar múltiples inicializaciones
    if (mapInitialized) {
        console.log('Mapa ya inicializado, saltando...');
        return;
    }
    
    try {
        console.log('=== INICIANDO MAPA DE INSPECCIONES ===');
        console.log('Leaflet disponible:', typeof L !== 'undefined');
        
        // Verificar que Leaflet esté cargado
        if (typeof L === 'undefined') {
            throw new Error('Leaflet no está cargado. Verifica que las librerías estén incluidas correctamente.');
        }
        
        // Verificar que el contenedor exista
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            throw new Error('Contenedor del mapa no encontrado');
        }
        
        console.log('Contenedor del mapa:', mapContainer);
        console.log('Dimensiones del contenedor:', {
            width: mapContainer.offsetWidth,
            height: mapContainer.offsetHeight,
            clientWidth: mapContainer.clientWidth,
            clientHeight: mapContainer.clientHeight
        });
        
        // Datos GeoJSON desde el backend
        const geojsonData = @json($geojson);
        console.log('GeoJSON recibido:', geojsonData);
        console.log('Número de features:', geojsonData?.features?.length || 0);
        
        // Verificar que hay datos
        if (!geojsonData || !geojsonData.features || geojsonData.features.length === 0) {
            console.warn('No hay datos georreferenciados disponibles');
            mapContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #7f8c8d; flex-direction: column;">
                    <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                    <p><strong>No hay inspecciones georreferenciadas para mostrar</strong></p>
                    <p><small>Las inspecciones deben tener coordenadas de latitud y longitud</small></p>
                </div>
            `;
            mapInitialized = true;
            return;
        }
        
        console.log('Inicializando mapa Leaflet...');
        
        // Limpiar el contenedor y remover cualquier instancia previa
        mapContainer.innerHTML = '';
        mapContainer._leaflet_id = null; // Importante: limpiar el ID de Leaflet
        
        // Crear el mapa con configuración explícita
        const map = L.map('map', {
            center: [-28.4696, -65.7795],
            zoom: 13,
            zoomControl: true,
            attributionControl: true
        });
        
        console.log('Mapa creado:', map);
        
        // Agregar capa de tiles
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 3
        });
        
        tileLayer.addTo(map);
        console.log('Capa de tiles agregada');
        
        // Función para obtener color por estado
        function getColorByEstado(estado) {
            const colores = {
                'excelente': '#27ae60',
                'bueno': '#3498db',
                'regular': '#f39c12',
                'malo': '#e74c3c',
                'critico': '#c0392b'
            };
            return colores[estado] || '#95a5a6';
        }
        
        // Función para crear ícono de marcador
        function getMarkerIcon(estado) {
            const color = getColorByEstado(estado);
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [25, 25],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });
        }
        
        // Crear grupo de marcadores con clustering
        const markers = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 15
        });
        
        console.log('Agregando marcadores...');
        let marcadoresAgregados = 0;
        
        // Agregar cada marcador
        geojsonData.features.forEach((feature, index) => {
            try {
                const props = feature.properties;
                const coords = feature.geometry.coordinates;
                
                if (!coords || coords.length < 2) {
                    console.warn(`Feature ${index} no tiene coordenadas válidas`);
                    return;
                }
                
                const lat = parseFloat(coords[1]);
                const lng = parseFloat(coords[0]);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.warn(`Feature ${index} tiene coordenadas inválidas:`, coords);
                    return;
                }
                
                console.log(`Marcador ${index + 1}: [${lat}, ${lng}] - ${props.estado}`);
                
                const marker = L.marker([lat, lng], {
                    icon: getMarkerIcon(props.estado)
                });
                
                const popupContent = `
                    <div class="popup-content">
                        <h6><i class="fas fa-home me-2"></i>${props.codigo_vivienda}</h6>
                        <div class="popup-info">
                            <p><strong>Dirección:</strong> ${props.direccion}</p>
                            <p><strong>Estado:</strong> <span class="badge badge-${props.estado}">${props.estado}</span></p>
                            <p><strong>Puntaje:</strong> ${props.puntaje}%</p>
                            <p><strong>Fecha:</strong> ${props.fecha}</p>
                            <p><strong>Inspector:</strong> ${props.inspector}</p>
                            <p><strong>Observaciones:</strong><br><small>${props.observaciones}</small></p>
                            <hr class="my-2">
                            <a href="/inspecciones/${props.id}" class="btn btn-sm btn-primary w-100" target="_blank">
                                <i class="fas fa-eye me-1"></i> Ver Detalle
                            </a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, { maxWidth: 300 });
                markers.addLayer(marker);
                marcadoresAgregados++;
                
            } catch (error) {
                console.error(`Error procesando feature ${index}:`, error);
            }
        });
        
        console.log(`Total de marcadores agregados: ${marcadoresAgregados}`);
        
        // Agregar el grupo de marcadores al mapa
        map.addLayer(markers);
        
        // Ajustar vista para mostrar todos los marcadores
        if (marcadoresAgregados > 0) {
            const bounds = markers.getBounds();
            console.log('Bounds de marcadores:', bounds);
            map.fitBounds(bounds, { padding: [50, 50] });
        }
        
        // Forzar recálculo del tamaño del mapa
        setTimeout(() => {
            map.invalidateSize(true);
            console.log('Tamaño del mapa recalculado');
        }, 250);
        
        // Marcar como inicializado
        mapInitialized = true;
        
        console.log('=== MAPA INICIALIZADO CORRECTAMENTE ===');
        
        // Exportar PDF
        const btnExportar = document.getElementById('btnExportarPDF');
        if (btnExportar) {
            btnExportar.addEventListener('click', function() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route("reportes.mapa-inspecciones.pdf") }}?' + params.toString();
            });
        }
        
    } catch (error) {
        console.error('=== ERROR CRÍTICO AL INICIALIZAR MAPA ===');
        console.error('Error:', error);
        console.error('Stack:', error.stack);
        
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #e74c3c; flex-direction: column; padding: 20px; text-align: center;">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p><strong>Error al cargar el mapa</strong></p>
                    <p><small>${error.message}</small></p>
                    <p><small>Revisa la consola del navegador (F12) para más detalles</small></p>
                </div>
            `;
        }
        
        mapInitialized = true; // Marcar como inicializado incluso en error para evitar reintentos
    }
}
</script>
@endsection
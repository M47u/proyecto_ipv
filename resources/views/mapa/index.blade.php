@extends('layouts.app')

@section('title', 'Mapa de Inspecciones')

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mapa</li>
        </ol>
    </nav>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css" />
    <style>
        #map {
            height: calc(100vh - 220px);
            min-height: 500px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .filters-panel {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filters-panel h5 {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group {
            margin-bottom: 1rem;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .filter-group select,
        .filter-group input {
            font-size: 0.875rem;
        }

        .map-legend {
            position: absolute;
            bottom: 30px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 180px;
        }

        .map-legend h6 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.813rem;
        }

        .legend-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .marker-excelente {
            background-color: #10b981;
        }

        .marker-bueno {
            background-color: #3b82f6;
        }

        .marker-regular {
            background-color: #f59e0b;
        }

        .marker-malo {
            background-color: #ef4444;
        }

        .marker-critico {
            background-color: #7f1d1d;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.2);
        }

        .leaflet-popup-content {
            margin: 1rem;
            min-width: 250px;
        }

        .popup-header {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .popup-info {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .popup-info strong {
            color: #374151;
            display: inline-block;
            min-width: 90px;
        }

        .popup-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        .btn-filter {
            background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%);
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }

        .btn-reset {
            background: #6c757d;
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .layer-control {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 0.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .layer-control label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
        }

        .layer-control label:last-child {
            margin-bottom: 0;
        }

        .stats-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .stat-item {
            text-align: center;
        }

        .stat-item .value {
            font-size: 1.75rem;
            font-weight: 700;
            display: block;
        }

        .stat-item .label {
            font-size: 0.813rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            #map {
                height: 400px;
            }

            .map-legend {
                bottom: 10px;
                right: 10px;
                font-size: 0.75rem;
            }

            .stats-bar {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        /* Animación de carga */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            border-radius: 8px;
        }

        .loading-spinner {
            text-align: center;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-map"></i> Mapa de Inspecciones</h2>
    </div>

    <!-- Estadísticas -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="value" id="total-inspecciones">0</span>
            <span class="label">Inspecciones</span>
        </div>
        <div class="stat-item">
            <span class="value" id="total-visible">0</span>
            <span class="label">Visibles</span>
        </div>
        <div class="stat-item">
            <span class="value" id="total-clusters">0</span>
            <span class="label">Clusters</span>
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="filters-panel">
        <h5><i class="bi bi-funnel"></i> Filtros</h5>
        <form id="filters-form">
            <div class="row">
                <div class="col-md-2 filter-group">
                    <label for="filter-estado">Estado General</label>
                    <select class="form-select form-select-sm" id="filter-estado" name="estado">
                        <option value="">Todos</option>
                        <option value="excelente">Excelente</option>
                        <option value="bueno">Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="malo">Malo</option>
                        <option value="critico">Crítico</option>
                    </select>
                </div>
                <div class="col-md-2 filter-group">
                    <label for="filter-tipo">Tipo</label>
                    <select class="form-select form-select-sm" id="filter-tipo" name="tipo">
                        <option value="">Todos</option>
                        <option value="inicial">Inicial</option>
                        <option value="seguimiento">Seguimiento</option>
                        <option value="reclamo">Reclamo</option>
                        <option value="pre_entrega">Pre-Entrega</option>
                        <option value="final">Final</option>
                    </select>
                </div>
                <div class="col-md-2 filter-group">
                    <label for="filter-inspector">Inspector</label>
                    <select class="form-select form-select-sm" id="filter-inspector" name="inspector_id">
                        <option value="">Todos</option>
                        @foreach($inspectores as $inspector)
                            <option value="{{ $inspector->id }}">{{ $inspector->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 filter-group">
                    <label for="filter-fecha-desde">Desde</label>
                    <input type="date" class="form-control form-control-sm" id="filter-fecha-desde" name="fecha_desde">
                </div>
                <div class="col-md-2 filter-group">
                    <label for="filter-fecha-hasta">Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="filter-fecha-hasta" name="fecha_hasta">
                </div>
                <div class="col-md-2 filter-group d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-filter btn-sm flex-grow-1">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <button type="button" class="btn btn-secondary btn-reset btn-sm" id="btn-reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Mapa -->
    <div class="card">
        <div class="card-body p-0" style="position: relative;">
            <div id="map"></div>

            <!-- Controles de Capas -->
            <div class="layer-control">
                <label>
                    <input type="radio" name="layer" value="markers" checked>
                    <i class="bi bi-geo-alt-fill"></i> Marcadores
                </label>
                <label>
                    <input type="radio" name="layer" value="heatmap">
                    <i class="bi bi-fire"></i> Mapa de Calor
                </label>
            </div>

            <!-- Leyenda -->
            <div class="map-legend">
                <h6><i class="bi bi-info-circle"></i> Estado</h6>
                <div class="legend-item">
                    <div class="legend-marker marker-excelente"></div>
                    <span>Excelente</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-bueno"></div>
                    <span>Bueno</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-regular"></div>
                    <span>Regular</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-malo"></div>
                    <span>Malo</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-critico"></div>
                    <span>Crítico</span>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loading-overlay" style="display: none;">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Cargando inspecciones...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        let map;
        let markersLayer;
        let heatmapLayer;
        let currentLayer = 'markers';

        // Inicializar mapa
        function initMap() {
            // Crear mapa centrado en Argentina (Corrientes)
            map = L.map('map').setView([-27.4692, -58.8306], 13);

            // Agregar capa base de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Inicializar capa de marcadores con clustering
            markersLayer = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                iconCreateFunction: function (cluster) {
                    const count = cluster.getChildCount();
                    let size = 'small';
                    if (count > 10) size = 'medium';
                    if (count > 50) size = 'large';

                    return L.divIcon({
                        html: '<div><span>' + count + '</span></div>',
                        className: 'marker-cluster marker-cluster-' + size,
                        iconSize: L.point(40, 40)
                    });
                }
            });

            map.addLayer(markersLayer);

            // Cargar inspecciones
            loadInspecciones();
        }

        // Obtener color según estado
        function getMarkerColor(estado) {
            const colors = {
                'excelente': '#10b981',
                'bueno': '#3b82f6',
                'regular': '#f59e0b',
                'malo': '#ef4444',
                'critico': '#7f1d1d'
            };
            return colors[estado] || '#6c757d';
        }

        // Crear icono personalizado
        function createCustomIcon(estado) {
            const color = getMarkerColor(estado);
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });
        }

        // Crear contenido del popup
        function createPopupContent(properties) {
            const estadoColors = {
                'success': '#10b981',
                'primary': '#3b82f6',
                'warning': '#f59e0b',
                'danger': '#ef4444',
                'dark': '#7f1d1d'
            };

            const badgeColor = estadoColors[properties.estado_general_color] || '#6c757d';
            const habitableIcon = properties.es_habitable ?
                '<i class="bi bi-check-circle-fill text-success"></i>' :
                '<i class="bi bi-x-circle-fill text-danger"></i>';

            return `
            <div class="popup-header">
                <i class="bi bi-house-door"></i> ${properties.vivienda_codigo}
            </div>
            <div class="popup-info">
                <div><strong>Dirección:</strong> ${properties.vivienda_direccion}</div>
                <div><strong>Barrio:</strong> ${properties.vivienda_barrio}</div>
                <div><strong>Inspector:</strong> ${properties.inspector_nombre}</div>
                <div><strong>Tipo:</strong> ${properties.tipo_inspeccion_text}</div>
                <div><strong>Fecha:</strong> ${properties.fecha_inspeccion}</div>
                <div><strong>Habitable:</strong> ${habitableIcon} ${properties.es_habitable ? 'Sí' : 'No'}</div>
                ${properties.precision_gps ? `<div><strong>Precisión GPS:</strong> ${properties.precision_gps}m</div>` : ''}
                <div class="popup-badge" style="background-color: ${badgeColor}; color: white;">
                    ${properties.estado_general.toUpperCase()}
                </div>
                <div class="mt-2">
                    <a href="/inspecciones/${properties.id}" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-eye"></i> Ver Detalle
                    </a>
                </div>
            </div>
        `;
        }

        // Cargar inspecciones
        function loadInspecciones(filters = {}) {
            showLoading(true);

            const params = new URLSearchParams(filters);

            fetch(`{{ route('mapa.inspecciones') }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    // Limpiar capas existentes
                    markersLayer.clearLayers();
                    if (heatmapLayer) {
                        map.removeLayer(heatmapLayer);
                        heatmapLayer = null;
                    }

                    if (data.features && data.features.length > 0) {
                        // Agregar marcadores
                        data.features.forEach(feature => {
                            const coords = feature.geometry.coordinates;
                            const props = feature.properties;

                            const marker = L.marker([coords[1], coords[0]], {
                                icon: createCustomIcon(props.estado_general)
                            });

                            marker.bindPopup(createPopupContent(props), {
                                maxWidth: 300,
                                className: 'custom-popup'
                            });

                            markersLayer.addLayer(marker);
                        });

                        // Ajustar vista al contenido
                        map.fitBounds(markersLayer.getBounds(), { padding: [50, 50] });

                        // Actualizar estadísticas
                        updateStats(data.features.length);
                    } else {
                        updateStats(0);
                        alert('No se encontraron inspecciones con los filtros seleccionados');
                    }

                    showLoading(false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showLoading(false);
                    alert('Error al cargar las inspecciones');
                });
        }

        // Cargar heatmap
        function loadHeatmap(filters = {}) {
            showLoading(true);

            const params = new URLSearchParams(filters);

            fetch(`{{ route('mapa.heatmap') }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    // Limpiar capas
                    markersLayer.clearLayers();
                    if (heatmapLayer) {
                        map.removeLayer(heatmapLayer);
                    }

                    if (data && data.length > 0) {
                        // Crear capa de heatmap
                        heatmapLayer = L.heatLayer(data, {
                            radius: 25,
                            blur: 15,
                            maxZoom: 17,
                            max: 1.0,
                            gradient: {
                                0.0: '#10b981',
                                0.3: '#3b82f6',
                                0.5: '#f59e0b',
                                0.7: '#ef4444',
                                1.0: '#7f1d1d'
                            }
                        }).addTo(map);

                        updateStats(data.length);
                    } else {
                        updateStats(0);
                        alert('No hay datos suficientes para el mapa de calor');
                    }

                    showLoading(false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showLoading(false);
                    alert('Error al cargar el mapa de calor');
                });
        }

        // Actualizar estadísticas
        function updateStats(total) {
            document.getElementById('total-inspecciones').textContent = total;
            document.getElementById('total-visible').textContent = total;

            if (currentLayer === 'markers' && markersLayer) {
                const clusters = markersLayer.getLayers().filter(layer => layer instanceof L.MarkerCluster);
                document.getElementById('total-clusters').textContent = clusters.length;
            } else {
                document.getElementById('total-clusters').textContent = '0';
            }
        }

        // Mostrar/ocultar loading
        function showLoading(show) {
            document.getElementById('loading-overlay').style.display = show ? 'flex' : 'none';
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function () {
            initMap();

            // Filtros
            document.getElementById('filters-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const filters = {};

                for (let [key, value] of formData.entries()) {
                    if (value) filters[key] = value;
                }

                if (currentLayer === 'markers') {
                    loadInspecciones(filters);
                } else {
                    loadHeatmap(filters);
                }
            });

            // Reset
            document.getElementById('btn-reset').addEventListener('click', function () {
                document.getElementById('filters-form').reset();
                if (currentLayer === 'markers') {
                    loadInspecciones();
                } else {
                    loadHeatmap();
                }
            });

            // Cambio de capa
            document.querySelectorAll('input[name="layer"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    currentLayer = this.value;

                    const formData = new FormData(document.getElementById('filters-form'));
                    const filters = {};
                    for (let [key, value] of formData.entries()) {
                        if (value) filters[key] = value;
                    }

                    if (currentLayer === 'markers') {
                        if (heatmapLayer) map.removeLayer(heatmapLayer);
                        map.addLayer(markersLayer);
                        loadInspecciones(filters);
                    } else {
                        map.removeLayer(markersLayer);
                        loadHeatmap(filters);
                    }
                });
            });
        });
    </script>
@endpush
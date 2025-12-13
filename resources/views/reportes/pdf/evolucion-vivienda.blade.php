<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Evolución - {{ $vivienda->codigo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            margin: 0;
        }

        .metadata {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #1e3a8a;
        }

        .metadata p {
            margin: 2px 0;
            font-size: 9pt;
        }

        .section-title {
            background-color: #e5e7eb;
            padding: 8px 10px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 11pt;
            border-left: 4px solid #3b82f6;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
        }

        .stat-box h3 {
            font-size: 20pt;
            color: #1e3a8a;
            margin: 5px 0;
        }

        .stat-box p {
            font-size: 8pt;
            color: #6b7280;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        table.info-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.info-table td:first-child {
            font-weight: bold;
            width: 30%;
            color: #6b7280;
        }

        table.data-table {
            font-size: 8pt;
        }

        table.data-table thead {
            background-color: #1e3a8a;
            color: white;
        }

        table.data-table th,
        table.data-table td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            color: white;
        }

        .badge-success { background-color: #22c55e; }
        .badge-danger { background-color: #ef4444; }
        .badge-warning { background-color: #f59e0b; color: #000; }
        .badge-info { background-color: #3b82f6; }
        .badge-secondary { background-color: #6b7280; }

        .timeline-item {
            margin-bottom: 12px;
            padding: 10px;
            border-left: 3px solid #3b82f6;
            background-color: #f9fafb;
            page-break-inside: avoid;
        }

        .timeline-item h4 {
            font-size: 10pt;
            margin-bottom: 5px;
            color: #1e3a8a;
        }

        .timeline-item p {
            margin: 3px 0;
            font-size: 9pt;
        }

        .alert {
            padding: 8px 10px;
            margin: 10px 0;
            border-left: 4px solid;
            background-color: #fef3c7;
            border-color: #f59e0b;
        }

        .alert p {
            margin: 0;
            font-size: 9pt;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #f3f4f6;
            border-top: 2px solid #1e3a8a;
            padding: 8px 20px;
            font-size: 8pt;
            color: #6b7280;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        @page {
            margin: 100px 50px 50px 50px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE DE EVOLUCIÓN DE VIVIENDA</h1>
        <p>Instituto Provincial de la Vivienda - Formosa</p>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <p><strong>Código Vivienda:</strong> {{ $vivienda->codigo }}</p>
        <p><strong>Fecha de Generación:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-box">
            <p>INSPECCIONES</p>
            <h3>{{ $estadisticas['total_inspecciones'] }}</h3>
        </div>
        <div class="stat-box">
            <p>FALLAS</p>
            <h3>{{ $estadisticas['total_fallas'] }}</h3>
            <p style="color: #ef4444;">{{ $estadisticas['fallas_criticas'] }} críticas</p>
        </div>
        <div class="stat-box">
            <p>RECLAMOS</p>
            <h3>{{ $estadisticas['total_reclamos'] }}</h3>
            <p style="color: #f59e0b;">{{ $estadisticas['reclamos_pendientes'] }} pendientes</p>
        </div>
        <div class="stat-box">
            <p>ESTADO ACTUAL</p>
            <h3 style="font-size: 12pt;">{{ strtoupper($estadisticas['estado_actual']) }}</h3>
            <p style="color: {{ $estadisticas['es_habitable'] ? '#22c55e' : '#ef4444' }};">
                {{ $estadisticas['es_habitable'] ? 'Habitable' : 'No Habitable' }}
            </p>
        </div>
    </div>

    <!-- Datos Generales -->
    <div class="section-title">DATOS GENERALES</div>
    <table class="info-table">
        <tr>
            <td>Código</td>
            <td>{{ $vivienda->codigo }}</td>
            <td>Tipo</td>
            <td>{{ $vivienda->tipo_vivienda_text }}</td>
        </tr>
        <tr>
            <td>Dirección</td>
            <td>{{ $vivienda->direccion }}</td>
            <td>Categoría</td>
            <td>{{ $vivienda->categoria_vivienda ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Barrio</td>
            <td>{{ $vivienda->barrio ?? 'N/A' }}</td>
            <td>Superficie Cubierta</td>
            <td>{{ $vivienda->superficie_cubierta ?? 'N/A' }} m²</td>
        </tr>
        <tr>
            <td>Ciudad</td>
            <td>{{ $vivienda->ciudad }}</td>
            <td>Superficie Terreno</td>
            <td>{{ $vivienda->superficie_terreno ?? 'N/A' }} m²</td>
        </tr>
        <tr>
            <td>Propietario</td>
            <td>{{ $vivienda->propietario_actual ?? 'N/A' }}</td>
            <td>Cantidad Ambientes</td>
            <td>{{ $vivienda->cantidad_ambientes ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Teléfono</td>
            <td>{{ $vivienda->telefono_contacto ?? 'N/A' }}</td>
            <td>Estado</td>
            <td><strong>{{ ucfirst($vivienda->estado) }}</strong></td>
        </tr>
    </table>

    <!-- Métricas Adicionales -->
    <div class="section-title">MÉTRICAS DE INSPECCIÓN</div>
    <table class="info-table">
        <tr>
            <td>Primera Inspección</td>
            <td>{{ $estadisticas['primera_inspeccion'] }}</td>
            <td>Última Inspección</td>
            <td>{{ $estadisticas['ultima_inspeccion'] }}</td>
        </tr>
        <tr>
            <td>Promedio días entre inspecciones</td>
            <td>{{ $estadisticas['promedio_dias_entre_inspecciones'] }} días</td>
            <td>Requiere Seguimiento</td>
            <td>{{ $estadisticas['requiere_seguimiento'] ? 'SÍ' : 'NO' }}</td>
        </tr>
    </table>

    <!-- Salto de página -->
    <div class="page-break"></div>

    <!-- Historial de Inspecciones -->
    <div class="section-title">HISTORIAL DE INSPECCIONES</div>
    @if($vivienda->inspecciones->count() > 0)
        @foreach($vivienda->inspecciones as $inspeccion)
            <div class="timeline-item">
                <h4>
                    {{ $inspeccion->fecha_inspeccion->format('d/m/Y H:i') }} - 
                    <span class="badge badge-{{ 
                        $inspeccion->estado_general == 'excelente' ? 'success' : 
                        ($inspeccion->estado_general == 'bueno' ? 'info' : 
                        ($inspeccion->estado_general == 'regular' ? 'warning' : 'danger'))
                    }}">
                        {{ strtoupper($inspeccion->estado_general) }}
                    </span>
                    <span class="badge badge-secondary">{{ $inspeccion->tipo_inspeccion_text }}</span>
                </h4>
                <p><strong>Inspector:</strong> {{ $inspeccion->inspector->name }}</p>
                <p><strong>Habitabilidad:</strong> 
                    <span class="badge badge-{{ $inspeccion->es_habitable ? 'success' : 'danger' }}">
                        {{ $inspeccion->es_habitable ? 'HABITABLE' : 'NO HABITABLE' }}
                    </span>
                </p>

                @if($inspeccion->fallas->count() > 0)
                    <div class="alert">
                        <p><strong>⚠️ {{ $inspeccion->fallas->count() }} falla(s) detectada(s)</strong></p>
                        @if($inspeccion->fallas->where('gravedad', 'critica')->count() > 0)
                            <p style="color: #dc2626;">
                                {{ $inspeccion->fallas->where('gravedad', 'critica')->count() }} CRÍTICA(S)
                            </p>
                        @endif
                    </div>
                @endif

                @if($inspeccion->observaciones)
                    <p><strong>Observaciones:</strong> {{ $inspeccion->observaciones }}</p>
                @endif

                @if($inspeccion->conclusiones)
                    <p><strong>Conclusiones:</strong> {{ $inspeccion->conclusiones }}</p>
                @endif
            </div>
        @endforeach
    @else
        <p>No hay inspecciones registradas.</p>
    @endif

    <!-- Salto de página si hay fallas -->
    @if($estadisticas['total_fallas'] > 0)
        <div class="page-break"></div>

        <!-- Detalle de Fallas -->
        <div class="section-title">DETALLE DE FALLAS ENCONTRADAS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                    <th>Gravedad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vivienda->inspecciones as $inspeccion)
                    @foreach($inspeccion->fallas as $falla)
                        <tr>
                            <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($falla->categoria) }}</td>
                            <td>{{ $falla->descripcion }}</td>
                            <td>{{ $falla->ubicacion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $falla->gravedad == 'critica' ? 'danger' : 
                                    ($falla->gravedad == 'grave' ? 'warning' : 
                                    ($falla->gravedad == 'moderada' ? 'info' : 'secondary'))
                                }}">
                                    {{ strtoupper($falla->gravedad) }}
                                </span>
                            </td>
                            <td>{{ $falla->requiere_accion_inmediata ? 'SÍ' : 'No' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Reclamos si existen -->
    @if($vivienda->reclamos->count() > 0)
        <div class="page-break"></div>

        <div class="section-title">RECLAMOS ASOCIADOS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Reclamante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vivienda->reclamos as $reclamo)
                    <tr>
                        <td>{{ $reclamo->fecha_reclamo ? $reclamo->fecha_reclamo->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $reclamo->titulo }}</td>
                        <td>{{ ucfirst($reclamo->tipo_reclamo ?? 'N/A') }}</td>
                        <td>
                            <span class="badge badge-{{ 
                                $reclamo->prioridad == 'urgente' ? 'danger' : 
                                ($reclamo->prioridad == 'alta' ? 'warning' : 'info')
                            }}">
                                {{ strtoupper($reclamo->prioridad) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ 
                                $reclamo->estado == 'resuelto' ? 'success' : 
                                ($reclamo->estado == 'en_proceso' ? 'warning' : 'secondary')
                            }}">
                                {{ strtoupper(str_replace('_', ' ', $reclamo->estado)) }}
                            </span>
                        </td>
                        <td>{{ $reclamo->reclamante_nombre ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    IPV - Sistema de Inspecciones | Generado: {{ date('d/m/Y H:i') }}
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
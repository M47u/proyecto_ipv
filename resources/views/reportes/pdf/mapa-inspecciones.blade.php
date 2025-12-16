<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mapa de Inspecciones</title>
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
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 18pt;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 9pt;
        }
        
        .info-box {
            background-color: #ecf0f1;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .info-box h3 {
            color: #2c3e50;
            font-size: 11pt;
            margin-bottom: 8px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .stat-value {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 8pt;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        .section-title {
            background-color: #34495e;
            color: white;
            padding: 8px 12px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 11pt;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        
        thead {
            background-color: #34495e;
            color: white;
        }
        
        th {
            padding: 8px 5px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
        }
        
        td {
            padding: 7px 5px;
            border-bottom: 1px solid #dee2e6;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 600;
            color: white;
        }
        
        .badge-excelente { background-color: #27ae60; }
        .badge-bueno { background-color: #3498db; }
        .badge-regular { background-color: #f39c12; }
        .badge-malo { background-color: #e74c3c; }
        .badge-critico { background-color: #c0392b; }
        
        .zona-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        
        .zona-section h4 {
            color: #2c3e50;
            font-size: 10pt;
            margin-bottom: 8px;
        }
        
        .zona-stats {
            display: table;
            width: 100%;
            font-size: 8pt;
        }
        
        .zona-stat {
            display: table-cell;
            padding: 5px;
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #7f8c8d; }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .summary-box {
            background-color: #e8f4f8;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        
        .summary-box p {
            margin-bottom: 5px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🗺️ Reporte de Mapa de Inspecciones</h1>
        <p>Instituto Provincial de la Vivienda - IPV</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Filtros Aplicados -->
    @if($filtros_aplicados)
    <div class="info-box">
        <h3>🔍 Filtros Aplicados</h3>
        <p>
            @if(isset($filtros_aplicados['estado']))
                <strong>Estado:</strong> {{ $filtros_aplicados['estado'] }} &nbsp;|&nbsp;
            @endif
            @if(isset($filtros_aplicados['inspector']))
                <strong>Inspector:</strong> {{ $filtros_aplicados['inspector'] }} &nbsp;|&nbsp;
            @endif
            @if(isset($filtros_aplicados['fecha_desde']) && isset($filtros_aplicados['fecha_hasta']))
                <strong>Período:</strong> {{ $filtros_aplicados['fecha_desde'] }} al {{ $filtros_aplicados['fecha_hasta'] }}
            @endif
            @if(isset($filtros_aplicados['puntaje_min']))
                <strong>Puntaje Mín:</strong> {{ $filtros_aplicados['puntaje_min'] }}% &nbsp;|&nbsp;
            @endif
            @if(isset($filtros_aplicados['puntaje_max']))
                <strong>Puntaje Máx:</strong> {{ $filtros_aplicados['puntaje_max'] }}%
            @endif
        </p>
    </div>
    @endif

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ $estadisticas['total_inspecciones'] }}</span>
            <span class="stat-label">Total Inspecciones</span>
        </div>
        <div class="stat-item">
            <span class="stat-value" style="color: #27ae60;">{{ $estadisticas['excelentes'] + $estadisticas['buenas'] }}</span>
            <span class="stat-label">Buenas/Excelentes</span>
        </div>
        <div class="stat-item">
            <span class="stat-value" style="color: #f39c12;">{{ $estadisticas['regulares'] }}</span>
            <span class="stat-label">Regulares</span>
        </div>
        <div class="stat-item">
            <span class="stat-value" style="color: #e74c3c;">{{ $estadisticas['malas'] + $estadisticas['criticas'] }}</span>
            <span class="stat-label">Malas/Críticas</span>
        </div>
    </div>

    <!-- Resumen por Zona -->
    @if($por_zona->count() > 0)
    <div class="section-title">📍 Distribución por Zona</div>
    
    @foreach($por_zona as $zona => $datos)
    <div class="zona-section">
        <h4>{{ $zona }}</h4>
        <div class="zona-stats">
            <div class="zona-stat">
                <strong>Total:</strong> {{ $datos['cantidad'] }}
            </div>
            <div class="zona-stat">
                <strong style="color: #27ae60;">Excelentes:</strong> {{ $datos['excelentes'] }}
            </div>
            <div class="zona-stat">
                <strong style="color: #3498db;">Buenas:</strong> {{ $datos['buenas'] }}
            </div>
            <div class="zona-stat">
                <strong style="color: #f39c12;">Regulares:</strong> {{ $datos['regulares'] }}
            </div>
            <div class="zona-stat">
                <strong style="color: #e74c3c;">Malas:</strong> {{ $datos['malas'] }}
            </div>
            <div class="zona-stat">
                <strong style="color: #c0392b;">Críticas:</strong> {{ $datos['criticas'] }}
            </div>
            <div class="zona-stat">
                <strong>Promedio:</strong> {{ number_format($datos['promedio_puntaje'], 1) }}%
            </div>
        </div>
    </div>
    @endforeach
    @endif

    <!-- Listado Detallado de Inspecciones -->
    <div class="section-title">📋 Detalle de Inspecciones Georreferenciadas</div>
    
    @if($inspecciones->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Código</th>
                <th style="width: 20%;">Dirección</th>
                <th style="width: 10%;">Coordenadas</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 8%;">Puntaje</th>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 15%;">Inspector</th>
                <th style="width: 17%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspecciones as $inspeccion)
            <tr>
                <td><strong>{{ $inspeccion->vivienda->codigo }}</strong></td>
                <td>{{ Str::limit($inspeccion->vivienda->direccion, 30) }}</td>
                <td class="text-center" style="font-size: 7pt;">
                    {{ number_format($inspeccion->latitud, 4) }},
                    {{ number_format($inspeccion->longitud, 4) }}   
                </td>
                <td>
                    <span class="badge badge-{{ strtolower($inspeccion->estado_general) }}">
                        {{ ucfirst($inspeccion->estado_general) }}
                    </span>
                </td>
                <td class="text-center">
                    <strong>{{ number_format($inspeccion->puntaje_total, 1) }}%</strong>
                </td>
                <td class="text-center">
                    {{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}
                </td>
                <td>
                    {{ Str::limit($inspeccion->inspector->name, 20) }}
                </td>
                <td>
                    <small>{{ Str::limit($inspeccion->observaciones_generales ?? 'Sin observaciones', 50) }}</small>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No hay inspecciones georreferenciadas con los filtros aplicados
    </div>
    @endif

    <!-- Resumen Final -->
    <div class="summary-box" style="margin-top: 20px;">
        <h3 style="color: #2c3e50; font-size: 10pt; margin-bottom: 8px;">📊 Resumen del Reporte</h3>
        <p><strong>Total de Inspecciones Georreferenciadas:</strong> {{ $estadisticas['total_inspecciones'] }}</p>
        <p><strong>Puntaje Promedio General:</strong> {{ $estadisticas['promedio_puntaje'] }}%</p>
        <p><strong>Tasa de Buenas/Excelentes:</strong> 
            @if($estadisticas['total_inspecciones'] > 0)
                {{ number_format((($estadisticas['excelentes'] + $estadisticas['buenas']) / $estadisticas['total_inspecciones']) * 100, 1) }}%
            @else
                0%
            @endif
        </p>
        <p><strong>Zonas Cubiertas:</strong> {{ $por_zona->count() }}</p>
    </div>

    <!-- Nota sobre el Mapa -->
    <div class="info-box" style="margin-top: 15px;">
        <p style="font-size: 8pt; text-align: center;">
            <strong>Nota:</strong> Este reporte muestra únicamente las inspecciones con coordenadas geográficas registradas. 
            Para visualizar el mapa interactivo completo, acceda a la versión web del reporte.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Instituto Provincial de la Vivienda (IPV) | Sistema de Inspecciones | Página <span class="pagenum"></span></p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Inspecciones - Período</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
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
            font-size: 8pt;
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
            font-size: 18pt;
            color: #1e3a8a;
            margin: 5px 0;
        }

        .stat-box p {
            font-size: 7pt;
            color: #6b7280;
            margin: 0;
        }

        .section-title {
            background-color: #e5e7eb;
            padding: 6px 10px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 10pt;
            border-left: 4px solid #3b82f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        table.summary-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.summary-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #6b7280;
        }

        table.data-table thead {
            background-color: #1e3a8a;
            color: white;
        }

        table.data-table th,
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            color: white;
        }

        .badge-success {
            background-color: #22c55e;
        }

        .badge-danger {
            background-color: #ef4444;
        }

        .badge-warning {
            background-color: #f59e0b;
            color: #000;
        }

        .badge-info {
            background-color: #3b82f6;
        }

        .badge-secondary {
            background-color: #6b7280;
        }

        .badge-primary {
            background-color: #1e3a8a;
        }

        .chart-placeholder {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            padding: 30px;
            text-align: center;
            margin: 10px 0;
            color: #9ca3af;
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
            font-size: 7pt;
            color: #6b7280;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        @page {
            margin: 80px 40px 50px 40px;
            size: landscape;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #22c55e;
        }

        .text-danger {
            color: #ef4444;
        }

        .text-warning {
            color: #f59e0b;
        }

        .progress-bar {
            height: 15px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
            color: white;
            font-size: 7pt;
            line-height: 15px;
            padding: 0 5px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE DE INSPECCIONES POR PERÍODO</h1>
        <p>Instituto Provincial de la Vivienda - Formosa</p>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($request->fecha_desde)->format('d/m/Y') }} al
            {{ \Carbon\Carbon::parse($request->fecha_hasta)->format('d/m/Y') }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
        @if($request->filled('inspector_id'))
            <p><strong>Filtrado por Inspector:</strong> {{ \App\Models\User::find($request->inspector_id)->name }}</p>
        @endif
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-box">
            <p>INSPECCIONES</p>
            <h3>{{ $estadisticas['total_inspecciones'] }}</h3>
            <p>{{ $estadisticas['promedio_por_dia'] }} por día</p>
        </div>
        <div class="stat-box">
            <p>VIVIENDAS</p>
            <h3>{{ $estadisticas['total_viviendas'] }}</h3>
            <p>inspeccionadas</p>
        </div>
        <div class="stat-box">
            <p>HABITABILIDAD</p>
            <h3>{{ $estadisticas['porcentaje_habitables'] }}%</h3>
            <p class="{{ $estadisticas['porcentaje_habitables'] >= 80 ? 'text-success' : 'text-warning' }}">
                {{ $estadisticas['habitables'] }} habitables
            </p>
        </div>
        <div class="stat-box">
            <p>FALLAS</p>
            <h3>{{ $estadisticas['total_fallas'] }}</h3>
            <p class="text-danger">{{ $estadisticas['fallas_criticas'] }} críticas</p>
        </div>
    </div>

    <!-- Distribución por Tipo -->
    <div class="section-title">DISTRIBUCIÓN POR TIPO DE INSPECCIÓN</div>
    <table class="summary-table">
        @foreach($estadisticas['por_tipo'] as $tipo => $cantidad)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $tipo)) }}</td>
                <td class="text-right">
                    <strong>{{ $cantidad }}</strong>
                    ({{ round(($cantidad / $estadisticas['total_inspecciones']) * 100, 1) }}%)
                </td>
            </tr>
        @endforeach
    </table>

    <!-- Distribución por Estado -->
    <div class="section-title">DISTRIBUCIÓN POR ESTADO GENERAL</div>
    <table class="summary-table">
        @foreach($estadisticas['por_estado'] as $estado => $cantidad)
            <tr>
                <td>
                    <span class="badge badge-{{ 
                        $estado == 'excelente' ? 'success' :
            ($estado == 'bueno' ? 'info' :
                ($estado == 'regular' ? 'warning' : 'danger'))
                    }}">
                        {{ strtoupper($estado) }}
                    </span>
                </td>
                <td class="text-right">
                    <strong>{{ $cantidad }}</strong>
                    ({{ round(($cantidad / $estadisticas['total_inspecciones']) * 100, 1) }}%)
                </td>
            </tr>
        @endforeach
    </table>

    <!-- Productividad por Inspector -->
    <div class="section-title">PRODUCTIVIDAD POR INSPECTOR</div>
    <table class="summary-table">
        @foreach($estadisticas['por_inspector'] as $inspector)
            <tr>
                <td>{{ $inspector['nombre'] }}</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ ($inspector['total'] / $estadisticas['total_inspecciones']) * 100 }}%">
                            {{ $inspector['total'] }}
                            ({{ round(($inspector['total'] / $estadisticas['total_inspecciones']) * 100, 1) }}%)
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>

    <!-- Salto de página -->
    <div class="page-break"></div>

    <!-- Detalle de Inspecciones -->
    <div class="section-title">DETALLE DE INSPECCIONES ({{ $inspecciones->count() }} REGISTROS)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">Fecha</th>
                <th width="10%">Código</th>
                <th width="20%">Dirección</th>
                <th width="10%">Tipo</th>
                <th width="15%">Inspector</th>
                <th width="10%">Estado</th>
                <th width="7%">Hab.</th>
                <th width="7%">Fallas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspecciones as $inspeccion)
                    <tr>
                        <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}</td>
                        <td><strong>{{ $inspeccion->vivienda->codigo }}</strong></td>
                        <td>{{ Str::limit($inspeccion->vivienda->direccion, 30) }}</td>
                        <td>{{ $inspeccion->tipo_inspeccion_text }}</td>
                        <td>{{ Str::limit($inspeccion->inspector->name, 20) }}</td>
                        <td>
                            <span class="badge badge-{{ 
                                $inspeccion->estado_general == 'excelente' ? 'success' :
                ($inspeccion->estado_general == 'bueno' ? 'info' :
                    ($inspeccion->estado_general == 'regular' ? 'warning' : 'danger'))
                            }}">
                                {{ strtoupper($inspeccion->estado_general) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($inspeccion->es_habitable)
                                <span class="text-success">✓</span>
                            @else
                                <span class="text-danger">✗</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($inspeccion->fallas->count() > 0)
                                <span class="badge badge-warning">{{ $inspeccion->fallas->count() }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    IPV - Sistema de Inspecciones | Generado: {{ now()->format('d/m/Y H:i') }}
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
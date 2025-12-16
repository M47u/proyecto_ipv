<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Viviendas</title>
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
            font-size: 18pt;
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

        .kpi-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .kpi-box {
            display: inline-block;
            width: 23%;
            padding: 10px;
            margin-right: 2%;
            text-align: center;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            vertical-align: top;
        }

        .kpi-box:last-child {
            margin-right: 0;
        }

        .kpi-box h3 {
            font-size: 18pt;
            color: #1e3a8a;
            margin: 5px 0;
        }

        .kpi-box p {
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

        .alert-box {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid;
            page-break-inside: avoid;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-color: #f59e0b;
        }

        .alert-info {
            background-color: #dbeafe;
            border-color: #3b82f6;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-color: #ef4444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        table thead {
            background-color: #1e3a8a;
            color: white;
        }

        table th {
            padding: 5px 4px;
            border: 1px solid #1e3a8a;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 5px 4px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-success { background-color: #22c55e; color: white; }
        .badge-danger { background-color: #ef4444; color: white; }
        .badge-warning { background-color: #f59e0b; color: #000; }
        .badge-secondary { background-color: #6b7280; color: white; }

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

        .text-center { text-align: center; }

        @page {
            margin: 80px 30px 50px 30px;
            size: landscape;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE DE ESTADO DE VIVIENDAS</h1>
        <p>Instituto Provincial de la Vivienda - Formosa</p>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
        @if($request->filled('tipo_vivienda'))
            <p><strong>Filtro Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $request->tipo_vivienda)) }}</p>
        @endif
        @if($request->filled('habitabilidad'))
            <p><strong>Filtro Habitabilidad:</strong> {{ ucfirst($request->habitabilidad) }}</p>
        @endif
    </div>

    <!-- KPIs -->
    <div class="kpi-container">
        <div class="kpi-box">
            <p>TOTAL VIVIENDAS</p>
            <h3>{{ $estadisticas['total'] }}</h3>
            <p>{{ $estadisticas['activas'] }} activas</p>
        </div>
        <div class="kpi-box">
            <p>INSPECCIONADAS</p>
            <h3>{{ $estadisticas['porcentaje_inspeccionadas'] }}%</h3>
            <p>{{ $estadisticas['inspeccionadas'] }} viviendas</p>
        </div>
        <div class="kpi-box">
            <p>HABITABILIDAD</p>
            <h3>{{ $estadisticas['porcentaje_habitables'] }}%</h3>
            <p>{{ $estadisticas['habitables'] }} habitables</p>
        </div>
        <div class="kpi-box">
            <p>CRÍTICAS</p>
            <h3>{{ $estadisticas['criticas'] }}</h3>
            <p>requieren atención</p>
        </div>
    </div>

    <!-- Alertas -->
    @if($estadisticas['sin_inspeccionar'] > 0)
    <div class="alert-box alert-warning">
        <p><strong>⚠️ {{ $estadisticas['sin_inspeccionar'] }} viviendas sin inspeccionar</strong></p>
        <p style="font-size: 7pt; margin-top: 3px;">Se recomienda programar inspecciones iniciales</p>
    </div>
    @endif

    @if($estadisticas['sin_inspeccion_reciente'] > 0)
    <div class="alert-box alert-info">
        <p><strong>ℹ️ {{ $estadisticas['sin_inspeccion_reciente'] }} viviendas sin inspección reciente</strong></p>
        <p style="font-size: 7pt; margin-top: 3px;">Más de 6 meses sin inspeccionar</p>
    </div>
    @endif

    @if($estadisticas['con_reclamos_activos'] > 0)
    <div class="alert-box alert-danger">
        <p><strong>🚨 {{ $estadisticas['con_reclamos_activos'] }} viviendas con reclamos activos</strong></p>
        <p style="font-size: 7pt; margin-top: 3px;">Requieren seguimiento inmediato</p>
    </div>
    @endif

    <!-- Distribución -->
    <div class="section-title">DISTRIBUCIÓN POR TIPO Y CATEGORÍA</div>
    <table>
        <thead>
            <tr>
                <th width="50%">Tipo de Vivienda</th>
                <th width="50%" class="text-center">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estadisticas['por_tipo'] as $tipo => $cantidad)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $tipo)) }}</td>
                <td class="text-center"><strong>{{ $cantidad }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($estadisticas['por_categoria']->count() > 0)
    <table>
        <thead>
            <tr>
                <th width="50%">Categoría</th>
                <th width="50%" class="text-center">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estadisticas['por_categoria'] as $categoria => $cantidad)
            <tr>
                <td>{{ $categoria }}</td>
                <td class="text-center"><strong>{{ $cantidad }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Listado de Viviendas -->
    <div class="section-title">LISTADO DE VIVIENDAS ({{ $viviendas->count() }} REGISTROS)</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Código</th>
                <th width="25%">Dirección</th>
                <th width="12%">Tipo</th>
                <th width="10%">Categoría</th>
                <th width="10%">Estado</th>
                <th width="13%">Última Insp.</th>
                <th width="10%">Habitab.</th>
                <th width="10%">Reclamos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($viviendas->take(100) as $vivienda)
            <tr>
                <td><strong>{{ $vivienda->codigo }}</strong></td>
                <td>{{ Str::limit($vivienda->direccion, 35) }}</td>
                <td>{{ Str::limit($vivienda->tipo_vivienda_text, 12) }}</td>
                <td>{{ $vivienda->categoria_vivienda ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $vivienda->estado == 'activa' ? 'success' : 'secondary' }}">
                        {{ strtoupper($vivienda->estado) }}
                    </span>
                </td>
                <td>
                    @if($vivienda->ultimaInspeccion)
                        {{ $vivienda->ultimaInspeccion->fecha_inspeccion->format('d/m/Y') }}
                    @else
                        <span class="badge badge-warning">Sin insp.</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($vivienda->ultimaInspeccion)
                        @if($vivienda->ultimaInspeccion->es_habitable)
                            ✓
                        @else
                            ✗
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $reclamosActivos = $vivienda->reclamos->whereIn('estado', ['pendiente', 'en_proceso'])->count();
                    @endphp
                    @if($reclamosActivos > 0)
                        <span class="badge badge-warning">{{ $reclamosActivos }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($viviendas->count() > 100)
    <p style="text-align: center; color: #6b7280; font-size: 8pt; margin-top: 10px;">
        * Se muestran las primeras 100 viviendas. Total: {{ $viviendas->count() }}
    </p>
    @endif

    <!-- Resumen y Recomendaciones -->
    <div class="page-break"></div>
    <div class="section-title">ANÁLISIS Y RECOMENDACIONES</div>

    <div class="alert-box alert-info">
        <p><strong>📊 Resumen del Estado Actual:</strong></p>
        <p style="font-size: 8pt; margin-top: 5px;">
            • Total de viviendas: {{ $estadisticas['total'] }} ({{ $estadisticas['activas'] }} activas, {{ $estadisticas['inactivas'] }} inactivas)<br>
            • Cobertura de inspección: {{ $estadisticas['porcentaje_inspeccionadas'] }}% ({{ $estadisticas['inspeccionadas'] }} de {{ $estadisticas['total'] }})<br>
            • Habitabilidad general: {{ $estadisticas['porcentaje_habitables'] }}% ({{ $estadisticas['habitables'] }} habitables, {{ $estadisticas['no_habitables'] }} no habitables)<br>
            • Viviendas críticas: {{ $estadisticas['criticas'] }}<br>
            • Con reclamos activos: {{ $estadisticas['con_reclamos_activos'] }}
        </p>
    </div>

    @if($estadisticas['porcentaje_inspeccionadas'] < 80)
    <div class="alert-box alert-warning">
        <p><strong>⚠️ Cobertura de Inspección Baja:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            Solo el {{ $estadisticas['porcentaje_inspeccionadas'] }}% de las viviendas han sido inspeccionadas.
            Se recomienda aumentar la frecuencia de inspecciones para alcanzar al menos 80% de cobertura.
        </p>
    </div>
    @endif

    @if($estadisticas['porcentaje_habitables'] < 70)
    <div class="alert-box alert-danger">
        <p><strong>🚨 Alerta de Habitabilidad:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            El porcentaje de habitabilidad es {{ $estadisticas['porcentaje_habitables'] }}%, por debajo del estándar recomendado (70%).
            Se requiere un plan de acción inmediato para mejorar las condiciones de las viviendas.
        </p>
    </div>
    @endif

    <div class="alert-box alert-info">
        <p><strong>💡 Recomendaciones:</strong></p>
        <p style="font-size: 8pt; margin-top: 5px;">
            @if($estadisticas['sin_inspeccionar'] > 0)
                1. Programar inspecciones iniciales para las {{ $estadisticas['sin_inspeccionar'] }} viviendas sin inspeccionar<br>
            @endif
            @if($estadisticas['sin_inspeccion_reciente'] > 10)
                2. Realizar inspecciones de seguimiento en las {{ $estadisticas['sin_inspeccion_reciente'] }} viviendas sin inspección reciente<br>
            @endif
            @if($estadisticas['criticas'] > 0)
                3. Priorizar atención en las {{ $estadisticas['criticas'] }} viviendas en estado crítico<br>
            @endif
            @if($estadisticas['con_reclamos_activos'] > 0)
                4. Resolver los reclamos activos en {{ $estadisticas['con_reclamos_activos'] }} viviendas<br>
            @endif
            @if($estadisticas['porcentaje_habitables'] < 80)
                5. Implementar programa de mejoras para aumentar el porcentaje de habitabilidad<br>
            @endif
            6. Mantener registro actualizado del estado de cada vivienda<br>
            7. Establecer calendario de inspecciones periódicas (cada 6 meses mínimo)
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        IPV - Estado de Viviendas | Confidencial | Generado: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ejecutivo - {{ $mesActual }}</title>
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
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11pt;
            margin: 0;
        }

        .metadata {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #1e3a8a;
        }

        .metadata p {
            margin: 2px 0;
            font-size: 9pt;
        }

        .kpis-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .kpi-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .kpi-box.primary {
            border-left: 4px solid #3b82f6;
        }

        .kpi-box.success {
            border-left: 4px solid #22c55e;
        }

        .kpi-box.warning {
            border-left: 4px solid #f59e0b;
        }

        .kpi-box.danger {
            border-left: 4px solid #ef4444;
        }

        .kpi-box p {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .kpi-box h2 {
            font-size: 24pt;
            color: #1e3a8a;
            margin: 5px 0;
        }

        .kpi-box small {
            font-size: 7pt;
            display: block;
            margin-top: 5px;
        }

        .section-title {
            background-color: #1e3a8a;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 11pt;
        }

        .alert-box {
            border-left: 4px solid;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fef3c7;
        }

        .alert-box.danger {
            border-color: #ef4444;
            background-color: #fee2e2;
        }

        .alert-box.warning {
            border-color: #f59e0b;
            background-color: #fef3c7;
        }

        .alert-box.info {
            border-color: #3b82f6;
            background-color: #dbeafe;
        }

        .alert-box h6 {
            font-size: 10pt;
            margin-bottom: 3px;
        }

        .alert-box p {
            font-size: 9pt;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
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

        .list-item {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .list-item h6 {
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .list-item p {
            font-size: 8pt;
            color: #6b7280;
            margin: 0;
        }

        .progress-bar {
            height: 18px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
            color: white;
            font-size: 7pt;
            line-height: 18px;
            padding: 0 5px;
            text-align: center;
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
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
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
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>DASHBOARD EJECUTIVO</h1>
        <p>Instituto Provincial de la Vivienda - Formosa</p>
        <p style="margin-top: 5px;">{{ $fechaInicio->locale('es')->isoFormat('MMMM YYYY') }}</p>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <p><strong>Período:</strong> {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
    </div>

    <!-- KPIs Principales -->
    <div class="kpis-grid">
        <div class="kpi-box primary">
            <p>INSPECCIONES</p>
            <h2>{{ $kpis['inspecciones']['valor'] }}</h2>
            <small class="{{ $kpis['inspecciones']['cambio'] >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $kpis['inspecciones']['cambio'] >= 0 ? '▲' : '▼' }}
                {{ abs($kpis['inspecciones']['cambio']) }}% vs anterior
            </small>
        </div>
        <div class="kpi-box success">
            <p>HABITABILIDAD</p>
            <h2>{{ $kpis['habitabilidad']['valor'] }}%</h2>
            <small class="{{ $kpis['habitabilidad']['cambio'] >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $kpis['habitabilidad']['cambio'] >= 0 ? '▲' : '▼' }}
                {{ abs($kpis['habitabilidad']['cambio']) }}% vs anterior
            </small>
        </div>
        <div class="kpi-box warning">
            <p>RECLAMOS</p>
            <h2>{{ $kpis['reclamos']['valor'] }}</h2>
            <small class="text-warning">
                {{ $kpis['reclamos']['pendientes'] }} pendientes
            </small>
        </div>
        <div class="kpi-box danger">
            <p>FALLAS</p>
            <h2>{{ $kpis['fallas']['valor'] }}</h2>
            <small class="text-danger">
                {{ $kpis['fallas']['criticas'] }} críticas
            </small>
        </div>
    </div>

    <!-- Alertas -->
    @if($alertas->count() > 0)
        <div class="section-title">ALERTAS Y NOTIFICACIONES ({{ $alertas->count() }})</div>
        @foreach($alertas as $alerta)
            <div class="alert-box {{ $alerta['tipo'] }}">
                <h6>⚠ {{ $alerta['titulo'] }}</h6>
                <p>{{ $alerta['mensaje'] }}</p>
            </div>
        @endforeach
    @endif

    <!-- Top Inspectores -->
    <div class="section-title">TOP INSPECTORES DEL MES</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Inspector</th>
                <th width="15%" class="text-center">Inspecciones</th>
                <th width="35%">Rendimiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topInspectores as $index => $inspector)
                <tr>
                    <td class="text-center">
                        @if($index == 0)
                            🥇
                        @elseif($index == 1)
                            🥈
                        @elseif($index == 2)
                            🥉
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $inspector['nombre'] }}</td>
                    <td class="text-center"><strong>{{ $inspector['total'] }}</strong></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ ($inspector['total'] / $topInspectores->first()['total']) * 100 }}%">
                                {{ round(($inspector['total'] / $topInspectores->first()['total']) * 100, 0) }}%
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Métrica de Productividad -->
    <div style="background-color: #f0f9ff; padding: 10px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 9pt;">
            <strong>Productividad Promedio:</strong> {{ $kpis['productividad']['valor'] }} inspecciones por inspector
            ({{ $kpis['productividad']['inspectores'] }} inspectores activos)
        </p>
    </div>

    <div class="page-break"></div>

    <!-- Viviendas Críticas -->
    <div class="section-title">VIVIENDAS EN ESTADO CRÍTICO ({{ $viviendasCriticas->count() }})</div>

    @if($viviendasCriticas->count() > 0)
        @foreach($viviendasCriticas as $vivienda)
            <div class="list-item">
                <h6>
                    <strong>{{ $vivienda['codigo'] }}</strong>
                    @if(!$vivienda['habitable'])
                        <span class="badge badge-danger">NO HABITABLE</span>
                    @endif
                    <span class="badge badge-{{ $vivienda['estado'] == 'critico' ? 'danger' : 'warning' }}">
                        {{ strtoupper($vivienda['estado']) }}
                    </span>
                </h6>
                <p>
                    {{ $vivienda['direccion'] }}<br>
                    Última inspección: {{ $vivienda['fecha_inspeccion']->format('d/m/Y') }}
                    @if($vivienda['reclamos_activos'] > 0)
                        | {{ $vivienda['reclamos_activos'] }} reclamo(s) activo(s)
                    @endif
                </p>
            </div>
        @endforeach
    @else
        <p style="text-align: center; color: #6b7280; padding: 20px;">
            No hay viviendas en estado crítico
        </p>
    @endif

    <!-- Resumen de Gestión -->
    <div class="section-title" style="margin-top: 30px;">RESUMEN DE GESTIÓN</div>
    <table class="data-table">
        <tr>
            <td width="50%"><strong>Total de Inspecciones Realizadas</strong></td>
            <td width="50%">{{ $kpis['inspecciones']['valor'] }}</td>
        </tr>
        <tr>
            <td><strong>Porcentaje de Habitabilidad</strong></td>
            <td>{{ $kpis['habitabilidad']['valor'] }}%</td>
        </tr>
        <tr>
            <td><strong>Reclamos Recibidos</strong></td>
            <td>{{ $kpis['reclamos']['valor'] }}</td>
        </tr>
        <tr>
            <td><strong>Reclamos Pendientes de Resolución</strong></td>
            <td class="text-warning">{{ $kpis['reclamos']['pendientes'] }}</td>
        </tr>
        <tr>
            <td><strong>Fallas Detectadas</strong></td>
            <td>{{ $kpis['fallas']['valor'] }}</td>
        </tr>
        <tr>
            <td><strong>Fallas Críticas</strong></td>
            <td class="text-danger">{{ $kpis['fallas']['criticas'] }}</td>
        </tr>
        <tr>
            <td><strong>Inspectores Activos</strong></td>
            <td>{{ $kpis['productividad']['inspectores'] }}</td>
        </tr>
        <tr>
            <td><strong>Productividad Promedio</strong></td>
            <td>{{ $kpis['productividad']['valor'] }} inspecciones/inspector</td>
        </tr>
    </table>

    <!-- Recomendaciones -->
    <div class="section-title" style="margin-top: 20px;">RECOMENDACIONES</div>
    <div style="background-color: #f9fafb; padding: 15px; border-left: 4px solid #3b82f6;">
        <ul style="margin: 0; padding-left: 20px; font-size: 9pt;">
            @if($kpis['habitabilidad']['valor'] < 80)
                <li style="margin-bottom: 5px;">
                    <strong>Atención:</strong> El porcentaje de habitabilidad está por debajo del 80%.
                    Se recomienda implementar plan de acción correctiva.
                </li>
            @endif

            @if($kpis['reclamos']['pendientes'] > 10)
                <li style="margin-bottom: 5px;">
                    <strong>Prioridad:</strong> Hay {{ $kpis['reclamos']['pendientes'] }} reclamos pendientes.
                    Asignar recursos adicionales para su resolución.
                </li>
            @endif

            @if($kpis['fallas']['criticas'] > 0)
                <li style="margin-bottom: 5px;">
                    <strong>Urgente:</strong> {{ $kpis['fallas']['criticas'] }} fallas críticas detectadas.
                    Requieren intervención inmediata.
                </li>
            @endif

            @if($viviendasCriticas->count() > 5)
                <li style="margin-bottom: 5px;">
                    <strong>Seguimiento:</strong> {{ $viviendasCriticas->count() }} viviendas en estado crítico.
                    Programar inspecciones de seguimiento urgentes.
                </li>
            @endif

            @if($kpis['inspecciones']['cambio'] < -10)
                <li style="margin-bottom: 5px;">
                    <strong>Productividad:</strong> Disminución significativa en inspecciones
                    ({{ abs($kpis['inspecciones']['cambio']) }}%).
                    Revisar carga de trabajo y disponibilidad de inspectores.
                </li>
            @endif
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    IPV - Dashboard Ejecutivo | Confidencial
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
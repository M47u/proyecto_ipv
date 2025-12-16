<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productividad de Inspectores</title>
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

        .ranking-item {
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .ranking-item.first {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }

        .ranking-item h4 {
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .ranking-item p {
            font-size: 8pt;
            margin: 2px 0;
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

        .alert-box {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid;
            page-break-inside: avoid;
        }

        .alert-info {
            background-color: #dbeafe;
            border-color: #3b82f6;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-color: #f59e0b;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-color: #ef4444;
        }

        .alert-success {
            background-color: #dcfce7;
            border-color: #22c55e;
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

        .text-center { text-align: center; }
        .text-right { text-align: right; }

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
        <h1>REPORTE DE PRODUCTIVIDAD DE INSPECTORES</h1>
        <p>Instituto Provincial de la Vivienda - Formosa</p>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Total de Inspectores:</strong> {{ $datosInspectores->count() }}</p>
    </div>

    <!-- KPIs -->
    <div class="kpi-container">
        <div class="kpi-box">
            <p>PROMEDIO INSPECCIONES</p>
            <h3>{{ $promedios['inspecciones'] }}</h3>
            <p>por inspector</p>
        </div>
        <div class="kpi-box">
            <p>PRODUCTIVIDAD DIARIA</p>
            <h3>{{ $promedios['promedio_diario'] }}</h3>
            <p>inspecciones/día</p>
        </div>
        <div class="kpi-box">
            <p>% HABITABILIDAD</p>
            <h3>{{ $promedios['habitabilidad'] }}%</h3>
            <p>promedio general</p>
        </div>
        <div class="kpi-box">
            <p>FALLAS/INSPECCIÓN</p>
            <h3>{{ $promedios['fallas_por_inspeccion'] }}</h3>
            <p>promedio</p>
        </div>
    </div>

    <!-- Ranking Top 3 -->
    <div class="section-title">🏆 TOP 3 INSPECTORES</div>
    
    @php
        $rankingActivos = $ranking->filter(function($i) {
            return $i['total_inspecciones'] > 0;
        });
    @endphp

    @if($rankingActivos->count() > 0)
        @foreach($rankingActivos->take(3) as $index => $inspector)
        <div class="ranking-item {{ $index == 0 ? 'first' : '' }}">
            <h4>
                @if($index == 0) 🥇
                @elseif($index == 1) 🥈
                @else 🥉
                @endif
                {{ $inspector['nombre'] }} - {{ $inspector['puntos'] }} puntos
            </h4>
            <p><strong>Inspecciones:</strong> {{ $inspector['total_inspecciones'] }} | 
               <strong>Habitabilidad:</strong> {{ $inspector['habitabilidad'] }}% | 
               <strong>Promedio Diario:</strong> {{ $inspector['promedio_diario'] }}</p>
        </div>
        @endforeach
    @else
        <div class="alert-box alert-warning">
            <p style="text-align: center; margin: 0;">No hay inspectores con actividad en el período seleccionado.</p>
        </div>
    @endif

    <!-- Tabla Detallada -->
    <div class="section-title">DETALLE POR INSPECTOR</div>
    <table>
        <thead>
            <tr>
                <th>Inspector</th>
                <th class="text-center">Insp.</th>
                <th class="text-center">Viv.</th>
                <th class="text-center">Días</th>
                <th class="text-center">Prom/Día</th>
                <th class="text-center">% Hab.</th>
                <th class="text-center">Fallas</th>
                <th class="text-center">F.Crit</th>
                <th class="text-center">Seg.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosInspectores as $inspector)
            <tr>
                <td><strong>{{ $inspector['nombre'] }}</strong></td>
                <td class="text-center">{{ $inspector['total_inspecciones'] }}</td>
                <td class="text-center">{{ $inspector['viviendas_inspeccionadas'] }}</td>
                <td class="text-center">{{ $inspector['dias_trabajados'] }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $inspector['promedio_diario'] >= $promedios['promedio_diario'] ? 'success' : 'warning' }}">
                        {{ $inspector['promedio_diario'] }}
                    </span>
                </td>
                <td class="text-center">{{ $inspector['porcentaje_habitables'] }}%</td>
                <td class="text-center">{{ $inspector['total_fallas'] }}</td>
                <td class="text-center">
                    @if($inspector['fallas_criticas'] > 0)
                        <span class="badge badge-danger">{{ $inspector['fallas_criticas'] }}</span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ $inspector['seguimientos_generados'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Sistema de Puntuación -->
    <div class="section-title">SISTEMA DE PUNTUACIÓN</div>
    <div class="alert-box alert-info">
        <p><strong>Criterios de evaluación:</strong></p>
        <p style="font-size: 8pt; margin-top: 5px;">
            • Cantidad de inspecciones: 40% (4 puntos por inspección)<br>
            • Porcentaje de habitabilidad: 30% (3 puntos por cada %)<br>
            • Productividad diaria: 20% (20 puntos por inspección/día)<br>
            • Penalización por fallas críticas: -10% (10 puntos negativos por cada falla crítica)
        </p>
    </div>

    <!-- Análisis -->
    <div class="section-title">ANÁLISIS Y RECOMENDACIONES</div>
    
    @php
        $mejorInspector = $ranking->first();
        $inspectoresConTrabajo = $datosInspectores->filter(function($i) {
            return $i['total_inspecciones'] > 0;
        });
        $inspectoresBajaProductividad = $inspectoresConTrabajo->filter(function($i) use ($promedios) {
            return $i['promedio_diario'] < ($promedios['promedio_diario'] * 0.7);
        });
        $inspectoresBajaHabitabilidad = $inspectoresConTrabajo->filter(function($i) {
            return $i['porcentaje_habitables'] < 70 && $i['total_inspecciones'] > 0;
        });
    @endphp

    @if($mejorInspector && $mejorInspector['total_inspecciones'] > 0)
    <div class="alert-box alert-success">
        <p><strong>✅ Desempeño Destacado:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            <strong>{{ $mejorInspector['nombre'] }}</strong> lidera el ranking con {{ $mejorInspector['puntos'] }} puntos,
            {{ $mejorInspector['total_inspecciones'] }} inspecciones y {{ $mejorInspector['habitabilidad'] }}% de habitabilidad.
        </p>
    </div>
    @else
    <div class="alert-box alert-warning">
        <p><strong>⚠️ Sin Actividad:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            No hay inspectores con actividad registrada en el período seleccionado.
        </p>
    </div>
    @endif

    @if($inspectoresBajaProductividad->count() > 0)
    <div class="alert-box alert-warning">
        <p><strong>⚠️ Baja Productividad:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            {{ $inspectoresBajaProductividad->count() }} inspector(es) por debajo del 70% del promedio:
            {{ $inspectoresBajaProductividad->pluck('nombre')->join(', ') }}
        </p>
    </div>
    @endif

    @if($inspectoresBajaHabitabilidad->count() > 0)
    <div class="alert-box alert-danger">
        <p><strong>🚨 Baja Habitabilidad:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            {{ $inspectoresBajaHabitabilidad->count() }} inspector(es) con menos del 70% de habitabilidad:
            {{ $inspectoresBajaHabitabilidad->pluck('nombre')->join(', ') }}
        </p>
    </div>
    @endif

    <div class="alert-box alert-info">
        <p><strong>💡 Recomendaciones:</strong></p>
        <p style="font-size: 8pt; margin-top: 3px;">
            @if($promedios['promedio_diario'] < 3)
                • Productividad promedio baja ({{ $promedios['promedio_diario'] }}/día). Optimizar rutas.<br>
            @endif
            @if($promedios['habitabilidad'] < 80)
                • Habitabilidad promedio {{ $promedios['habitabilidad'] }}%. Mejorar controles de calidad.<br>
            @endif
            • Capacitación continua para homogeneizar criterios de evaluación.<br>
            • Establecer metas individuales basadas en estos indicadores.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        IPV - Reporte de Productividad | Confidencial | Generado: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
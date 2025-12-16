<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InspeccionesPeriodoExport implements WithMultipleSheets
{
    protected $inspecciones;
    protected $request;

    public function __construct($inspecciones, $request)
    {
        $this->inspecciones = $inspecciones;
        $this->request = $request;
    }

    public function sheets(): array
    {
        return [
            new ResumenPeriodoSheet($this->inspecciones, $this->request),
            new InspeccionesDetalleSheet($this->inspecciones),
            new FallasPeriodoSheet($this->inspecciones),
        ];
    }
}

// ==========================================
// HOJA 1: RESUMEN
// ==========================================
class ResumenPeriodoSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $inspecciones;
    protected $request;

    public function __construct($inspecciones, $request)
    {
        $this->inspecciones = $inspecciones;
        $this->request = $request;
    }

    public function collection()
    {
        $data = collect([
            ['REPORTE DE INSPECCIONES POR PERÍODO'],
            ['Período', $this->request->fecha_desde . ' al ' . $this->request->fecha_hasta],
            ['Fecha Generación', now()->format('d/m/Y H:i')],
            [],
            ['ESTADÍSTICAS GENERALES'],
            ['Total Inspecciones', $this->inspecciones->count()],
            ['Viviendas Inspeccionadas', $this->inspecciones->pluck('vivienda_id')->unique()->count()],
            ['Inspectores Participantes', $this->inspecciones->pluck('inspector_id')->unique()->count()],
            ['Viviendas Habitables', $this->inspecciones->where('es_habitable', true)->count()],
            ['Viviendas No Habitables', $this->inspecciones->where('es_habitable', false)->count()],
            [
                '% Habitabilidad',
                $this->inspecciones->count() > 0
                ? round(($this->inspecciones->where('es_habitable', true)->count() / $this->inspecciones->count()) * 100, 1) . '%'
                : '0%'
            ],
            [],
            ['POR TIPO DE INSPECCIÓN'],
        ]);

        // Agregar tipos de inspección
        $porTipo = $this->inspecciones->groupBy('tipo_inspeccion')->map->count();
        foreach ($porTipo as $tipo => $cantidad) {
            $data->push([ucfirst(str_replace('_', ' ', $tipo)), $cantidad]);
        }

        $data->push([]);
        $data->push(['POR ESTADO GENERAL']);

        // Agregar estados
        $porEstado = $this->inspecciones->groupBy('estado_general')->map->count();
        foreach ($porEstado as $estado => $cantidad) {
            $data->push([ucfirst($estado), $cantidad]);
        }

        $data->push([]);
        $data->push(['POR INSPECTOR']);

        // Agregar inspectores
        $porInspector = $this->inspecciones->groupBy('inspector_id');
        foreach ($porInspector as $items) {
            $data->push([
                $items->first()->inspector->name,
                $items->count()
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Indicador', 'Valor'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
        ]);

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);

        return [];
    }

    public function title(): string
    {
        return 'Resumen';
    }
}

// ==========================================
// HOJA 2: DETALLE DE INSPECCIONES
// ==========================================
class InspeccionesDetalleSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $inspecciones;

    public function __construct($inspecciones)
    {
        $this->inspecciones = $inspecciones;
    }

    public function collection()
    {
        return $this->inspecciones->map(function ($inspeccion) {
            return [
                'id' => $inspeccion->id,
                'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y H:i'),
                'codigo_vivienda' => $inspeccion->vivienda->codigo,
                'direccion' => $inspeccion->vivienda->direccion,
                'tipo_vivienda' => $inspeccion->vivienda->tipo_vivienda_text,
                'tipo_inspeccion' => $inspeccion->tipo_inspeccion_text,
                'inspector' => $inspeccion->inspector->name,
                'estado_general' => ucfirst($inspeccion->estado_general),
                'habitable' => $inspeccion->es_habitable ? 'SÍ' : 'NO',
                'estructura' => ucfirst($inspeccion->estado_estructura ?? 'N/A'),
                'electrica' => ucfirst($inspeccion->estado_instalacion_electrica ?? 'N/A'),
                'sanitaria' => ucfirst($inspeccion->estado_instalacion_sanitaria ?? 'N/A'),
                'gas' => ucfirst($inspeccion->estado_instalacion_gas ?? 'N/A'),
                'pintura' => ucfirst($inspeccion->estado_pintura ?? 'N/A'),
                'aberturas' => ucfirst($inspeccion->estado_aberturas ?? 'N/A'),
                'pisos' => ucfirst($inspeccion->estado_pisos ?? 'N/A'),
                'total_fallas' => $inspeccion->fallas->count(),
                'requiere_seguimiento' => $inspeccion->requiere_seguimiento ? 'SÍ' : 'NO',
                'observaciones' => $inspeccion->observaciones ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Código Vivienda',
            'Dirección',
            'Tipo Vivienda',
            'Tipo Inspección',
            'Inspector',
            'Estado General',
            'Habitable',
            'Estructura',
            'Eléctrica',
            'Sanitaria',
            'Gas',
            'Pintura',
            'Aberturas',
            'Pisos',
            'Total Fallas',
            'Req. Seguimiento',
            'Observaciones'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
        ]);

        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('S')->setWidth(40);

        return [];
    }

    public function title(): string
    {
        return 'Inspecciones';
    }
}

// ==========================================
// HOJA 3: FALLAS DEL PERÍODO
// ==========================================
class FallasPeriodoSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $inspecciones;

    public function __construct($inspecciones)
    {
        $this->inspecciones = $inspecciones;
    }

    public function collection()
    {
        $fallas = collect();

        foreach ($this->inspecciones as $inspeccion) {
            foreach ($inspeccion->fallas as $falla) {
                $fallas->push([
                    'inspeccion_id' => $inspeccion->id,
                    'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
                    'codigo_vivienda' => $inspeccion->vivienda->codigo,
                    'direccion' => $inspeccion->vivienda->direccion,
                    'categoria' => ucfirst($falla->categoria),
                    'descripcion' => $falla->descripcion,
                    'gravedad' => ucfirst($falla->gravedad),
                    'ubicacion' => $falla->ubicacion ?? 'N/A',
                    'accion_inmediata' => $falla->requiere_accion_inmediata ? 'SÍ' : 'NO',
                ]);
            }
        }

        return $fallas;
    }

    public function headings(): array
    {
        return [
            'ID Inspección',
            'Fecha',
            'Código Vivienda',
            'Dirección',
            'Categoría',
            'Descripción',
            'Gravedad',
            'Ubicación',
            'Acción Inmediata'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ef4444']],
        ]);

        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(50);

        return [];
    }

    public function title(): string
    {
        return 'Fallas';
    }
}
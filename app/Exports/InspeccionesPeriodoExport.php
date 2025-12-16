<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InspeccionesPeriodoExport implements FromCollection, WithHeadings, WithStyles
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
}
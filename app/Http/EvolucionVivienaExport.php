<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class EvolucionViviendaExport implements WithMultipleSheets
{
    protected $vivienda;

    public function __construct($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function sheets(): array
    {
        return [
            new DatosGeneralesSheet($this->vivienda),
            new InspeccionesSheet($this->vivienda),
            new FallasSheet($this->vivienda),
            new ReclamosSheet($this->vivienda),
        ];
    }
}

// ==========================================
// HOJA 1: DATOS GENERALES
// ==========================================
class DatosGeneralesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $vivienda;

    public function __construct($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function collection()
    {
        return collect([
            ['Código', $this->vivienda->codigo],
            ['Dirección', $this->vivienda->direccion],
            ['Barrio', $this->vivienda->barrio ?? 'N/A'],
            ['Ciudad', $this->vivienda->ciudad],
            ['Tipo', $this->vivienda->tipo_vivienda_text],
            ['Categoría', $this->vivienda->categoria_vivienda ?? 'N/A'],
            ['Superficie Cubierta', $this->vivienda->superficie_cubierta . ' m²'],
            ['Superficie Terreno', $this->vivienda->superficie_terreno . ' m²'],
            ['Ambientes', $this->vivienda->cantidad_ambientes],
            ['Propietario', $this->vivienda->propietario_actual ?? 'N/A'],
            ['Teléfono', $this->vivienda->telefono_contacto ?? 'N/A'],
            ['Estado', $this->vivienda->estado],
            [],
            ['ESTADÍSTICAS'],
            ['Total Inspecciones', $this->vivienda->inspecciones->count()],
            ['Total Reclamos', $this->vivienda->reclamos->count()],
            ['Primera Inspección', $this->vivienda->inspecciones->first()?->fecha_inspeccion?->format('d/m/Y') ?? 'N/A'],
            ['Última Inspección', $this->vivienda->inspecciones->last()?->fecha_inspeccion?->format('d/m/Y') ?? 'N/A'],
            ['Estado Actual', $this->vivienda->inspecciones->last()?->estado_general ?? 'Sin inspecciones'],
            ['Es Habitable', $this->vivienda->inspecciones->last()?->es_habitable ? 'SÍ' : 'NO'],
        ]);
    }

    public function headings(): array
    {
        return ['Campo', 'Valor'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            14 => ['font' => ['bold' => true, 'size' => 12]],
            'A:B' => ['alignment' => ['vertical' => 'center']],
        ];
    }

    public function title(): string
    {
        return 'Datos Generales';
    }
}

// ==========================================
// HOJA 2: INSPECCIONES
// ==========================================
class InspeccionesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $vivienda;

    public function __construct($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function collection()
    {
        return $this->vivienda->inspecciones->map(function($inspeccion) {
            return [
                'id' => $inspeccion->id,
                'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y H:i'),
                'tipo' => $inspeccion->tipo_inspeccion_text,
                'inspector' => $inspeccion->inspector->name,
                'estado_general' => ucfirst($inspeccion->estado_general),
                'habitable' => $inspeccion->es_habitable ? 'SÍ' : 'NO',
                'estructura' => ucfirst($inspeccion->estado_estructura ?? 'N/A'),
                'eléctrica' => ucfirst($inspeccion->estado_instalacion_electrica ?? 'N/A'),
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
            'Tipo',
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
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
        ]);

        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(40);

        return [];
    }

    public function title(): string
    {
        return 'Inspecciones';
    }
}

// ==========================================
// HOJA 3: FALLAS
// ==========================================
class FallasSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $vivienda;

    public function __construct($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function collection()
    {
        $fallas = collect();

        foreach ($this->vivienda->inspecciones as $inspeccion) {
            foreach ($inspeccion->fallas as $falla) {
                $fallas->push([
                    'inspeccion_id' => $inspeccion->id,
                    'fecha_inspeccion' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
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
            'Fecha Inspección',
            'Categoría',
            'Descripción',
            'Gravedad',
            'Ubicación',
            'Acción Inmediata'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ef4444']],
        ]);

        $sheet->getColumnDimension('D')->setWidth(50);

        return [];
    }

    public function title(): string
    {
        return 'Fallas Detectadas';
    }
}

// ==========================================
// HOJA 4: RECLAMOS
// ==========================================
class ReclamosSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $vivienda;

    public function __construct($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function collection()
    {
        return $this->vivienda->reclamos->map(function($reclamo) {
            return [
                'id' => $reclamo->id,
                'fecha' => $reclamo->fecha_reclamo ? $reclamo->fecha_reclamo->format('d/m/Y H:i') : 'N/A',
                'titulo' => $reclamo->titulo,
                'tipo' => ucfirst($reclamo->tipo_reclamo ?? 'N/A'),
                'prioridad' => ucfirst($reclamo->prioridad),
                'estado' => ucfirst($reclamo->estado),
                'reclamante' => $reclamo->reclamante_nombre ?? 'N/A',
                'telefono' => $reclamo->reclamante_telefono ?? 'N/A',
                'descripcion' => $reclamo->descripcion,
                'fecha_resolucion' => $reclamo->fecha_resolucion ? $reclamo->fecha_resolucion->format('d/m/Y') : 'Pendiente',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Título',
            'Tipo',
            'Prioridad',
            'Estado',
            'Reclamante',
            'Teléfono',
            'Descripción',
            'Fecha Resolución'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'facc15']],
        ]);

        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(50);

        return [];
    }

    public function title(): string
    {
        return 'Reclamos';
    }
}
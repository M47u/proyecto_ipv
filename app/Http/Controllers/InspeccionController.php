<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Inspeccion;
use App\Models\InspeccionFalla;
use App\Models\InspeccionFoto;
use App\Models\Vivienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InspeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Inspeccion::class);

        $query = Inspeccion::query()->with(['vivienda', 'inspector']);

        // Filtro por inspector (si es admin, puede filtrar. Si es inspector, solo ve las suyas o asignadas)
        if (auth()->user()->role === 'inspector') {
            $query->where('inspector_id', auth()->id());
        } elseif ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_inspeccion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_inspeccion', '<=', $request->fecha_hasta);
        }

        // Filtro por estado general
        if ($request->filled('estado_general')) {
            $query->where('estado_general', $request->estado_general);
        }

        // Filtro por tipo
        if ($request->filled('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        $inspecciones = $query->orderBy('fecha_inspeccion', 'desc')->paginate(15);
        $inspectores = \App\Models\User::where('role', 'inspector')->get();

        return view('inspecciones.index', compact('inspecciones', 'inspectores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Inspeccion::class);

        $vivienda_id = $request->get('vivienda_id');
        $vivienda = null;

        if ($vivienda_id) {
            $vivienda = Vivienda::find($vivienda_id);
        }

        $viviendas = Vivienda::where('estado', 'activa')->orderBy('codigo')->get();

        return view('inspecciones.create', compact('viviendas', 'vivienda'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Inspeccion::class);

        $validated = $request->validate([
            'vivienda_id' => 'required|exists:viviendas,id',
            'tipo_inspeccion' => 'required|in:inicial,seguimiento,reclamo,pre_entrega,final',
            'fecha_inspeccion' => 'required|date',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'precision_gps' => 'nullable|numeric',
            'estado_general' => 'required|in:excelente,bueno,regular,malo,critico',
            'es_habitable' => 'boolean',

            // Evaluación detallada según migración
            'estado_estructura' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_electrica' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_sanitaria' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_gas' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_pintura' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_aberturas' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_pisos' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',

            'observaciones' => 'nullable|string',
            'conclusiones' => 'nullable|string',
            'requiere_seguimiento' => 'boolean',
            'fecha_proximo_seguimiento' => 'nullable|required_if:requiere_seguimiento,1|date|after:fecha_inspeccion',

            // Fotos
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|max:5120', // Max 5MB
            'fotos_descripcion' => 'nullable|array',
            'fotos_tipo' => 'nullable|array',

            // Fallas
            'fallas_categoria' => 'nullable|array',
            'fallas_descripcion' => 'nullable|array',
            'fallas_gravedad' => 'nullable|array',
            'fallas_ubicacion' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear Inspección
            $inspeccion = Inspeccion::create([
                'vivienda_id' => $validated['vivienda_id'],
                'inspector_id' => auth()->id(),
                'tipo_inspeccion' => $validated['tipo_inspeccion'],
                'fecha_inspeccion' => $validated['fecha_inspeccion'],
                'latitud' => $validated['latitud'],
                'longitud' => $validated['longitud'],
                'precision_gps' => $validated['precision_gps'],
                'estado_general' => $validated['estado_general'],
                'es_habitable' => $request->has('es_habitable'),

                'estado_estructura' => $validated['estado_estructura'] ?? null,
                'estado_instalacion_electrica' => $validated['estado_instalacion_electrica'] ?? null,
                'estado_instalacion_sanitaria' => $validated['estado_instalacion_sanitaria'] ?? null,
                'estado_instalacion_gas' => $validated['estado_instalacion_gas'] ?? null,
                'estado_pintura' => $validated['estado_pintura'] ?? null,
                'estado_aberturas' => $validated['estado_aberturas'] ?? null,
                'estado_pisos' => $validated['estado_pisos'] ?? null,

                'observaciones' => $validated['observaciones'],
                'conclusiones' => $validated['conclusiones'],
                'requiere_seguimiento' => $request->has('requiere_seguimiento'),
                'fecha_proximo_seguimiento' => $validated['fecha_proximo_seguimiento'] ?? null,
                'estado' => 'completada',
            ]);

            // 2. Guardar Fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $foto) {
                    $path = $foto->store('inspecciones/' . $inspeccion->id, 'public');

                    InspeccionFoto::create([
                        'inspeccion_id' => $inspeccion->id,
                        'ruta_archivo' => $path,
                        'nombre_original' => $foto->getClientOriginalName(),
                        'tipo_foto' => $request->fotos_tipo[$index] ?? 'general',
                        'descripcion' => $request->fotos_descripcion[$index] ?? null,
                        'orden' => $index,
                    ]);
                }
            }

            // 3. Guardar Fallas
            if ($request->filled('fallas_categoria')) {
                foreach ($request->fallas_categoria as $index => $categoria) {
                    if (!empty($categoria)) {
                        InspeccionFalla::create([
                            'inspeccion_id' => $inspeccion->id,
                            'categoria' => $categoria,
                            'descripcion' => $request->fallas_descripcion[$index] ?? '',
                            'gravedad' => $request->fallas_gravedad[$index] ?? 'leve',
                            'ubicacion' => $request->fallas_ubicacion[$index] ?? null,
                            'requiere_accion_inmediata' => ($request->fallas_gravedad[$index] ?? 'leve') === 'critica',
                        ]);
                    }
                }
            }

            // Registrar actividad
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'crear_inspeccion',
                'table_name' => 'inspecciones',
                'record_id' => $inspeccion->id,
                'description' => "Inspección creada para vivienda {$inspeccion->vivienda->codigo}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('inspecciones.show', $inspeccion)
                ->with('success', 'Inspección registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar la inspección: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inspeccion $inspeccion)
    {
        $this->authorize('view', $inspeccion);

        $inspeccion->load(['vivienda', 'inspector', 'fotos', 'fallas', 'reclamos']);
        return view('inspecciones.show', compact('inspeccion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspeccion $inspeccion)
    {
        $this->authorize('update', $inspeccion);

        $viviendas = Vivienda::where('estado', 'activa')->orderBy('codigo')->get();

        return view('inspecciones.edit', compact('inspeccion', 'viviendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inspeccion $inspeccion)
    {
        $this->authorize('update', $inspeccion);

        $validated = $request->validate([
            'tipo_inspeccion' => 'required|in:inicial,seguimiento,reclamo,pre_entrega,final',
            'fecha_inspeccion' => 'required|date',
            'estado_general' => 'required|in:excelente,bueno,regular,malo,critico',
            'es_habitable' => 'boolean',

            'estado_estructura' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_electrica' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_sanitaria' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_instalacion_gas' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_pintura' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_aberturas' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',
            'estado_pisos' => 'nullable|in:excelente,bueno,regular,malo,critico,no_aplica',

            'observaciones' => 'nullable|string',
            'conclusiones' => 'nullable|string',
            'requiere_seguimiento' => 'boolean',
            'fecha_proximo_seguimiento' => 'nullable|required_if:requiere_seguimiento,1|date',

            // Nuevas Fotos
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|max:5120',
            'fotos_descripcion' => 'nullable|array',
            'fotos_tipo' => 'nullable|array',

            // Nuevas Fallas
            'fallas_categoria' => 'nullable|array',
            'fallas_descripcion' => 'nullable|array',
            'fallas_gravedad' => 'nullable|array',
            'fallas_ubicacion' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $inspeccion->update([
                'tipo_inspeccion' => $validated['tipo_inspeccion'],
                'fecha_inspeccion' => $validated['fecha_inspeccion'],
                'estado_general' => $validated['estado_general'],
                'es_habitable' => $request->has('es_habitable'),

                'estado_estructura' => $validated['estado_estructura'] ?? null,
                'estado_instalacion_electrica' => $validated['estado_instalacion_electrica'] ?? null,
                'estado_instalacion_sanitaria' => $validated['estado_instalacion_sanitaria'] ?? null,
                'estado_instalacion_gas' => $validated['estado_instalacion_gas'] ?? null,
                'estado_pintura' => $validated['estado_pintura'] ?? null,
                'estado_aberturas' => $validated['estado_aberturas'] ?? null,
                'estado_pisos' => $validated['estado_pisos'] ?? null,

                'observaciones' => $validated['observaciones'],
                'conclusiones' => $validated['conclusiones'],
                'requiere_seguimiento' => $request->has('requiere_seguimiento'),
                'fecha_proximo_seguimiento' => $validated['fecha_proximo_seguimiento'] ?? null,
            ]);

            // Guardar Nuevas Fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $foto) {
                    $path = $foto->store('inspecciones/' . $inspeccion->id, 'public');

                    InspeccionFoto::create([
                        'inspeccion_id' => $inspeccion->id,
                        'ruta_archivo' => $path,
                        'nombre_original' => $foto->getClientOriginalName(),
                        'tipo_foto' => $request->fotos_tipo[$index] ?? 'general',
                        'descripcion' => $request->fotos_descripcion[$index] ?? null,
                        'orden' => $index,
                    ]);
                }
            }

            // Guardar Nuevas Fallas
            if ($request->filled('fallas_categoria')) {
                foreach ($request->fallas_categoria as $index => $categoria) {
                    if (!empty($categoria)) {
                        InspeccionFalla::create([
                            'inspeccion_id' => $inspeccion->id,
                            'categoria' => $categoria,
                            'descripcion' => $request->fallas_descripcion[$index] ?? '',
                            'gravedad' => $request->fallas_gravedad[$index] ?? 'leve',
                            'ubicacion' => $request->fallas_ubicacion[$index] ?? null,
                            'requiere_accion_inmediata' => ($request->fallas_gravedad[$index] ?? 'leve') === 'critica',
                        ]);
                    }
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'actualizar_inspeccion',
                'table_name' => 'inspecciones',
                'record_id' => $inspeccion->id,
                'description' => "Inspección actualizada: {$inspeccion->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('inspecciones.show', $inspeccion)
                ->with('success', 'Inspección actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspeccion $inspeccion)
    {
        $this->authorize('delete', $inspeccion);

        // Eliminar fotos del storage
        foreach ($inspeccion->fotos as $foto) {
            Storage::disk('public')->delete($foto->ruta_archivo);
        }

        $inspeccion->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'eliminar_inspeccion',
            'table_name' => 'inspecciones',
            'record_id' => $inspeccion->id,
            'description' => "Inspección eliminada: {$inspeccion->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('inspecciones.index')
            ->with('success', 'Inspección eliminada exitosamente');
    }

    /**
     * Get JSON data for map
     */
    public function getMapData()
    {
        $inspecciones = Inspeccion::with('vivienda')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function ($inspeccion) {
                return [
                    'id' => $inspeccion->id,
                    'lat' => $inspeccion->latitud,
                    'lng' => $inspeccion->longitud,
                    'estado' => $inspeccion->estado_general,
                    'vivienda' => $inspeccion->vivienda->codigo,
                    'direccion' => $inspeccion->vivienda->direccion,
                    'fecha' => $inspeccion->fecha_inspeccion->format('d/m/Y'),
                    'url' => route('inspecciones.show', $inspeccion)
                ];
            });

        return response()->json($inspecciones);
    }
}

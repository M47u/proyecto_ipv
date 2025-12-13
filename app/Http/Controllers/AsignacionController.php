<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asignacion;
use App\Models\User;
use App\Models\Vivienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Asignacion::class);

        $query = Asignacion::query()->with(['vivienda', 'inspector', 'asignadoPor']);

        // Si es inspector, solo ve sus asignaciones
        if (auth()->user()->role === 'inspector') {
            $query->where('inspector_id', auth()->id());
        } elseif ($request->filled('inspector_id')) {
            // Si es admin, puede filtrar por inspector
            $query->where('inspector_id', $request->inspector_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_asignacion', '<=', $request->fecha_hasta);
        }

        $asignaciones = $query->orderBy('fecha_asignacion', 'desc')->paginate(15);
        $inspectores = User::where('role', 'inspector')->where('is_active', true)->get();

        return view('asignaciones.index', compact('asignaciones', 'inspectores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Asignacion::class);

        $inspectores = User::where('role', 'inspector')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $viviendas = Vivienda::where('estado', 'activa')
            ->orderBy('codigo')
            ->get();

        return view('asignaciones.create', compact('inspectores', 'viviendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Asignacion::class);

        $validated = $request->validate([
            'vivienda_id' => 'required|exists:viviendas,id',
            'inspector_id' => 'required|exists:users,id',
            'fecha_asignacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_asignacion',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'notas' => 'nullable|string',
        ], [
            'vivienda_id.required' => 'La vivienda es obligatoria.',
            'vivienda_id.exists' => 'La vivienda seleccionada no existe.',
            'inspector_id.required' => 'El inspector es obligatorio.',
            'inspector_id.exists' => 'El inspector seleccionado no existe.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
            'fecha_limite.date' => 'La fecha límite debe ser una fecha válida.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser igual o posterior a la fecha de asignación.',
            'prioridad.required' => 'La prioridad es obligatoria.',
            'prioridad.in' => 'La prioridad debe ser: baja, media, alta o urgente.',
        ]);

        DB::beginTransaction();

        try {
            $asignacion = Asignacion::create([
                'vivienda_id' => $validated['vivienda_id'],
                'inspector_id' => $validated['inspector_id'],
                'asignado_por' => auth()->id(),
                'fecha_asignacion' => $validated['fecha_asignacion'],
                'fecha_limite' => $validated['fecha_limite'],
                'prioridad' => $validated['prioridad'],
                'notas' => $validated['notas'],
                'estado' => 'pendiente',
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'crear_asignacion',
                'table_name' => 'asignaciones',
                'record_id' => $asignacion->id,
                'description' => "Asignación creada: Vivienda {$asignacion->vivienda->codigo} a Inspector {$asignacion->inspector->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('asignaciones.index')
                ->with('success', 'Asignación creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la asignación: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asignacion $asignacion)
    {
        $this->authorize('view', $asignacion);

        $asignacion->load(['vivienda', 'inspector', 'asignadoPor']);

        return view('asignaciones.show', compact('asignacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asignacion $asignacion)
    {
        $this->authorize('update', $asignacion);

        $inspectores = User::where('role', 'inspector')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $viviendas = Vivienda::where('estado', 'activa')
            ->orderBy('codigo')
            ->get();

        return view('asignaciones.edit', compact('asignacion', 'inspectores', 'viviendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asignacion $asignacion)
    {
        $this->authorize('update', $asignacion);

        // Validación base para todos los usuarios
        $rules = [
            'fecha_asignacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_asignacion',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'notas' => 'nullable|string',
        ];

        // Solo administradores pueden cambiar inspector y vivienda
        if (auth()->user()->role === 'administrador') {
            $rules['inspector_id'] = 'required|exists:users,id';
            $rules['vivienda_id'] = 'required|exists:viviendas,id';
        }

        $messages = [
            'vivienda_id.required' => 'La vivienda es obligatoria.',
            'vivienda_id.exists' => 'La vivienda seleccionada no existe.',
            'inspector_id.required' => 'El inspector es obligatorio.',
            'inspector_id.exists' => 'El inspector seleccionado no existe.',
            'fecha_asignacion.required' => 'La fecha de asignación es obligatoria.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
            'fecha_limite.date' => 'La fecha límite debe ser una fecha válida.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser igual o posterior a la fecha de asignación.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: pendiente, en_progreso, completada o cancelada.',
            'prioridad.required' => 'La prioridad es obligatoria.',
            'prioridad.in' => 'La prioridad debe ser: baja, media, alta o urgente.',
        ];

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $asignacion->update($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'actualizar_asignacion',
                'table_name' => 'asignaciones',
                'record_id' => $asignacion->id,
                'description' => "Asignación actualizada: {$asignacion->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('asignaciones.index')
                ->with('success', 'Asignación actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asignacion $asignacion)
    {
        $this->authorize('delete', $asignacion);

        $asignacion->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'eliminar_asignacion',
            'table_name' => 'asignaciones',
            'record_id' => $asignacion->id,
            'description' => "Asignación eliminada: {$asignacion->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('asignaciones.index')
            ->with('success', 'Asignación eliminada exitosamente');
    }

    /**
     * Vista especial para inspectores con sus asignaciones
     */
    public function misAsignaciones()
    {
        $asignacionesPendientes = Asignacion::where('inspector_id', auth()->id())
            ->where('estado', 'pendiente')
            ->with('vivienda')
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_limite', 'asc')
            ->get();

        $asignacionesEnProgreso = Asignacion::where('inspector_id', auth()->id())
            ->where('estado', 'en_progreso')
            ->with('vivienda')
            ->orderBy('fecha_limite', 'asc')
            ->get();

        $asignacionesCompletadas = Asignacion::where('inspector_id', auth()->id())
            ->where('estado', 'completada')
            ->with('vivienda')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('asignaciones.mis-asignaciones', compact(
            'asignacionesPendientes',
            'asignacionesEnProgreso',
            'asignacionesCompletadas'
        ));
    }

    /**
     * Cambiar estado de asignación (para inspectores)
     */
    public function cambiarEstado(Request $request, Asignacion $asignacion)
    {
        $this->authorize('update', $asignacion);

        $validated = $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        ]);

        $asignacion->update(['estado' => $validated['estado']]);

        return back()->with('success', 'Estado actualizado exitosamente');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Reclamo;
use App\Models\Vivienda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReclamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reclamo::query()->with(['vivienda', 'usuario']);

        // Filtro por búsqueda (código vivienda o descripción)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhereHas('vivienda', function($qv) use ($search) {
                      $qv->where('codigo', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $reclamos = $query->orderBy('prioridad', 'desc') // Alta prioridad primero
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('reclamos.index', compact('reclamos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $vivienda_id = $request->get('vivienda_id');
        $vivienda = null;

        if ($vivienda_id) {
            $vivienda = Vivienda::find($vivienda_id);
        }

        $viviendas = Vivienda::where('estado', 'activa')->orderBy('codigo')->get();

        return view('reclamos.create', compact('viviendas', 'vivienda'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vivienda_id' => 'required|exists:viviendas,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:alta,media,baja,urgente',
            'estado' => 'required|in:pendiente,en_proceso,resuelto,rechazado',
        ]);

        $validated['user_id'] = auth()->id(); // Usuario que crea el reclamo

        $reclamo = Reclamo::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'crear_reclamo',
            'table_name' => 'reclamos',
            'record_id' => $reclamo->id,
            'description' => "Reclamo creado: {$reclamo->titulo}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('reclamos.index')->with('success', 'Reclamo registrado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reclamo $reclamo)
    {
        $reclamo->load(['vivienda', 'usuario']);
        return view('reclamos.show', compact('reclamo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reclamo $reclamo)
    {
        $viviendas = Vivienda::where('estado', 'activa')->orderBy('codigo')->get();
        return view('reclamos.edit', compact('reclamo', 'viviendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reclamo $reclamo)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:alta,media,baja,urgente',
            'estado' => 'required|in:pendiente,en_proceso,resuelto,rechazado',
            'fecha_resolucion' => 'nullable|date',
            'notas_resolucion' => 'nullable|string',
        ]);

        // Si cambia a resuelto y no tiene fecha, poner hoy
        if ($validated['estado'] === 'resuelto' && !$reclamo->fecha_resolucion && empty($validated['fecha_resolucion'])) {
            $validated['fecha_resolucion'] = now();
        }

        $reclamo->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'actualizar_reclamo',
            'table_name' => 'reclamos',
            'record_id' => $reclamo->id,
            'description' => "Reclamo actualizado: {$reclamo->id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('reclamos.index')->with('success', 'Reclamo actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reclamo $reclamo)
    {
        // Solo admin puede eliminar
        if (auth()->user()->role !== 'administrador') {
            abort(403);
        }

        $reclamo->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'eliminar_reclamo',
            'table_name' => 'reclamos',
            'record_id' => $reclamo->id,
            'description' => "Reclamo eliminado: {$reclamo->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('reclamos.index')->with('success', 'Reclamo eliminado exitosamente');
    }
}

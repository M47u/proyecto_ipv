<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Vivienda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ViviendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vivienda::query();

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->buscar($request->search);
        }

        // Filtro por tipo
        if ($request->filled('tipo_vivienda')) {
            $query->porTipo($request->tipo_vivienda);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $viviendas = $query->withCount('inspecciones')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('viviendas.index', compact('viviendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('viviendas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:viviendas,codigo',
            'direccion' => 'required|string|max:255|min:10',
            'barrio' => 'nullable|string|max:100',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'tipo_vivienda' => ['required', Rule::in(['proxima_entrega', 'entregada', 'recuperada'])],
            'superficie_cubierta' => 'nullable|numeric|min:0',
            'superficie_terreno' => 'nullable|numeric|min:0',
            'cantidad_ambientes' => 'nullable|integer|min:1|max:10',
            'propietario_actual' => 'nullable|string|max:255',
            'telefono_contacto' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
            'estado' => ['required', Rule::in(['activa', 'inactiva'])],
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Este código ya está registrado',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 10 caracteres',
            'tipo_vivienda.required' => 'El tipo de vivienda es obligatorio',
            'cantidad_ambientes.max' => 'La cantidad de ambientes no puede ser mayor a 10',
        ]);

        // Validar que superficie cubierta <= superficie terreno
        if ($validated['superficie_cubierta'] && $validated['superficie_terreno']) {
            if ($validated['superficie_cubierta'] > $validated['superficie_terreno']) {
                return back()->withErrors([
                    'superficie_cubierta' => 'La superficie cubierta no puede ser mayor a la superficie del terreno'
                ])->withInput();
            }
        }

        $vivienda = Vivienda::create($validated);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'crear_vivienda',
            'table_name' => 'viviendas',
            'record_id' => $vivienda->id,
            'description' => "Vivienda creada: {$vivienda->codigo}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('viviendas.index')->with('success', 'Vivienda creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vivienda $vivienda)
    {
        $vivienda->load(['inspecciones.inspector', 'reclamos']);
        
        return view('viviendas.show', compact('vivienda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vivienda $vivienda)
    {
        return view('viviendas.edit', compact('vivienda'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vivienda $vivienda)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('viviendas')->ignore($vivienda->id)],
            'direccion' => 'required|string|max:255|min:10',
            'barrio' => 'nullable|string|max:100',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'tipo_vivienda' => ['required', Rule::in(['proxima_entrega', 'entregada', 'recuperada'])],
            'superficie_cubierta' => 'nullable|numeric|min:0',
            'superficie_terreno' => 'nullable|numeric|min:0',
            'cantidad_ambientes' => 'nullable|integer|min:1|max:10',
            'propietario_actual' => 'nullable|string|max:255',
            'telefono_contacto' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
            'estado' => ['required', Rule::in(['activa', 'inactiva'])],
        ]);

        // Validar superficies
        if ($validated['superficie_cubierta'] && $validated['superficie_terreno']) {
            if ($validated['superficie_cubierta'] > $validated['superficie_terreno']) {
                return back()->withErrors([
                    'superficie_cubierta' => 'La superficie cubierta no puede ser mayor a la superficie del terreno'
                ])->withInput();
            }
        }

        $vivienda->update($validated);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'actualizar_vivienda',
            'table_name' => 'viviendas',
            'record_id' => $vivienda->id,
            'description' => "Vivienda actualizada: {$vivienda->codigo}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('viviendas.index')->with('success', 'Vivienda actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Request $request, Vivienda $vivienda)
    {
        // Cambiar estado a inactiva en lugar de eliminar
        $vivienda->update(['estado' => 'inactiva']);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'desactivar_vivienda',
            'table_name' => 'viviendas',
            'record_id' => $vivienda->id,
            'description' => "Vivienda desactivada: {$vivienda->codigo}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('viviendas.index')->with('success', 'Vivienda desactivada exitosamente');
    }

    /**
     * Get historial de inspecciones (JSON)
     */
    public function getHistorial(Vivienda $vivienda)
    {
        $inspecciones = $vivienda->inspecciones()
            ->with('inspector')
            ->orderBy('fecha_inspeccion', 'desc')
            ->get();

        return response()->json($inspecciones);
    }
}

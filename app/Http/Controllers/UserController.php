<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtro por estado
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('usuarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['administrador', 'inspector'])],
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role.required' => 'El rol es obligatorio',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $user = User::create($validated);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'crear_usuario',
            'table_name' => 'users',
            'record_id' => $user->id,
            'description' => "Usuario creado: {$user->name} ({$user->email})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        $this->authorize('view', $usuario);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        $this->authorize('update', $usuario);
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $this->authorize('update', $usuario);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($usuario->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['administrador', 'inspector'])],
            'is_active' => 'boolean',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'role.required' => 'El rol es obligatorio',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $usuario->update($validated);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'actualizar_usuario',
            'table_name' => 'users',
            'record_id' => $usuario->id,
            'description' => "Usuario actualizado: {$usuario->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Request $request, User $usuario)
    {
        $this->authorize('delete', $usuario);

        // No permitir eliminar usuario autenticado
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puede eliminar su propio usuario');
        }

        // Desactivar en lugar de eliminar
        $usuario->update(['is_active' => false]);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'desactivar_usuario',
            'table_name' => 'users',
            'record_id' => $usuario->id,
            'description' => "Usuario desactivado: {$usuario->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado exitosamente');
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(Request $request, User $usuario)
    {
        $this->authorize('update', $usuario);

        $defaultPassword = 'Inspector123!';
        $usuario->update([
            'password' => Hash::make($defaultPassword),
        ]);

        // Registrar actividad
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'resetear_password',
            'table_name' => 'users',
            'record_id' => $usuario->id,
            'description' => "Contraseña reseteada para: {$usuario->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Contraseña reseteada a: {$defaultPassword}");
    }
}

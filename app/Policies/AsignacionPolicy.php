<?php

namespace App\Policies;

use App\Models\Asignacion;
use App\Models\User;

class AsignacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Administradores pueden ver todas, inspectores solo las suyas
        return $user->role === 'administrador' || $user->role === 'inspector';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asignacion $asignacion): bool
    {
        // Administradores pueden ver todas
        if ($user->role === 'administrador') {
            return true;
        }

        // Inspectores solo pueden ver sus propias asignaciones
        return $user->id === $asignacion->inspector_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo administradores pueden crear asignaciones
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asignacion $asignacion): bool
    {
        // Administradores pueden actualizar todo
        if ($user->role === 'administrador') {
            return true;
        }

        // Inspectores solo pueden cambiar el estado de sus propias asignaciones
        return $user->id === $asignacion->inspector_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asignacion $asignacion): bool
    {
        // Solo administradores pueden eliminar asignaciones
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asignacion $asignacion): bool
    {
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asignacion $asignacion): bool
    {
        return $user->role === 'administrador';
    }
}

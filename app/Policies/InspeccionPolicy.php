<?php

namespace App\Policies;

use App\Models\Inspeccion;
use App\Models\User;

class InspeccionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos los autenticados pueden ver lista
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inspeccion $inspeccion): bool
    {
        return $user->role === 'administrador' || $user->id === $inspeccion->inspector_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos pueden crear
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inspeccion $inspeccion): bool
    {
        // Solo inspector que la creó o administrador
        return $user->role === 'administrador' || $user->id === $inspeccion->inspector_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inspeccion $inspeccion): bool
    {
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Inspeccion $inspeccion): bool
    {
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Inspeccion $inspeccion): bool
    {
        return $user->role === 'administrador';
    }
}

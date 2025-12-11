<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relaciones
     */
    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class, 'inspector_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'inspector_id');
    }

    public function asignacionesPendientes()
    {
        return $this->hasMany(Asignacion::class, 'inspector_id')
                    ->where('estado', 'pendiente');
    }

    public function asignacionesActivas()
    {
        return $this->hasMany(Asignacion::class, 'inspector_id')
                    ->whereIn('estado', ['pendiente', 'en_progreso']);
    }


    /**
     * Scopes
     */
    public function scopeInspectores($query)
    {
        return $query->where('role', 'inspector');
    }

    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessors
     */
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            'administrador' => 'Administrador',
            'inspector' => 'Inspector',
            default => $this->role,
        };
    }
}

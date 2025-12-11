@extends('layouts.app')

@section('title', 'Usuarios')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-people"></i> Gestión de Usuarios</h2>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filtros -->
        <form action="{{ route('usuarios.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Buscar por nombre o email..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Todos los roles</option>
                    <option value="administrador" {{ request('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="inspector" {{ request('role') == 'inspector' ? 'selected' : '' }}>Inspector</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="is_active" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            <i class="bi bi-person-circle"></i> {{ $usuario->name }}
                        </td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->role === 'administrador')
                                <span class="badge bg-danger">
                                    <i class="bi bi-shield-check"></i> Administrador
                                </span>
                            @else
                                <span class="badge bg-primary">
                                    <i class="bi bi-clipboard-check"></i> Inspector
                                </span>
                            @endif
                        </td>
                        <td>{{ $usuario->phone ?? '-' }}</td>
                        <td>
                            @if($usuario->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('usuarios.edit', $usuario) }}" 
                                   class="btn btn-outline-primary"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <form action="{{ route('usuarios.reset-password', $usuario) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Resetear contraseña de este usuario?')">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-outline-warning"
                                            title="Resetear Contraseña">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form>
                                
                                @if($usuario->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $usuario) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Desactivar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger"
                                            title="Desactivar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No se encontraron usuarios</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} 
                de {{ $usuarios->total() }} usuarios
            </div>
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection

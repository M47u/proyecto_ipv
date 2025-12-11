<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e40af">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title') - IPV Inspecciones</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <style>
        :root {
            --primary: #1e40af;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --critical: #7f1d1d;
            --light: #f8fafc;
            --dark: #1e293b;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary) !important;
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            min-height: 44px;
            /* Touch-friendly */
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .sidebar .nav-link.active {
            background-color: #e7f3ff;
            border-left-color: var(--primary);
            color: var(--primary);
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            padding: 2rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }

        .badge-estado-excelente {
            background-color: var(--success);
        }

        .badge-estado-bueno {
            background-color: #3b82f6;
        }

        .badge-estado-regular {
            background-color: var(--warning);
        }

        .badge-estado-malo {
            background-color: var(--danger);
        }

        .badge-estado-critico {
            background-color: var(--critical);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            min-height: 44px;
            /* Touch-friendly */
        }

        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .stat-card {
            border-left: 4px solid var(--primary);
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mobile Optimizations */
        @media (max-width: 767.98px) {
            .main-content {
                padding: 1rem;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            /* Hide desktop sidebar, show offcanvas */
            .sidebar {
                display: none;
            }

            /* Make buttons more touch-friendly */
            .btn,
            .form-control,
            .form-select {
                min-height: 44px;
                font-size: 16px;
                /* Prevents zoom on iOS */
            }

            /* Stack stat cards */
            .stat-card {
                margin-bottom: 1rem;
            }

            /* Improve table responsiveness */
            .table-responsive {
                border: none;
            }

            /* Better spacing for mobile */
            .page-header {
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
            }

            /* Mobile card view for tables */
            .mobile-card {
                display: block;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                padding: 1rem;
                margin-bottom: 1rem;
                background: white;
            }

            .mobile-card-header {
                font-weight: 600;
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #e9ecef;
            }

            .mobile-card-row {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f8f9fa;
            }

            .mobile-card-row:last-child {
                border-bottom: none;
            }

            .mobile-card-label {
                font-weight: 500;
                color: #6c757d;
                font-size: 0.875rem;
            }

            .mobile-card-value {
                text-align: right;
            }
        }

        /* Tablet optimizations */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .main-content {
                padding: 1.5rem;
            }

            .sidebar .nav-link {
                font-size: 0.9rem;
            }
        }

        /* Touch-friendly improvements for all devices */
        .btn-group-sm>.btn {
            min-height: 38px;
        }

        .dropdown-item {
            min-height: 44px;
            display: flex;
            align-items: center;
        }

        /* Offcanvas sidebar for mobile */
        .offcanvas-sidebar {
            width: 280px;
        }

        /* Hamburger menu button */
        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 767.98px) {
            .mobile-menu-btn {
                display: inline-block;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <!-- Mobile Menu Button -->
            <button class="btn btn-link mobile-menu-btn me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mobileSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-building"></i> IPV Inspecciones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('password.change') }}">
                                    <i class="bi bi-key"></i> Cambiar Contraseña
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">
                <i class="bi bi-building"></i> Menú
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                @if(auth()->user()->role === 'administrador')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                            href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                            href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('viviendas.*') ? 'active' : '' }}"
                        href="{{ route('viviendas.index') }}">
                        <i class="bi bi-house"></i> Viviendas
                    </a>
                </li>

                @if(auth()->user()->role === 'administrador')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}"
                            href="{{ route('asignaciones.index') }}">
                            <i class="bi bi-clipboard-check"></i> Asignaciones
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('asignaciones.mis-asignaciones') ? 'active' : '' }}"
                            href="{{ route('asignaciones.mis-asignaciones') }}">
                            <i class="bi bi-clipboard-check"></i> Mis Asignaciones
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inspecciones.*') ? 'active' : '' }}"
                        href="{{ route('inspecciones.index') }}">
                        <i class="bi bi-clipboard-data"></i> Inspecciones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reclamos.*') ? 'active' : '' }}"
                        href="{{ route('reclamos.index') }}">
                        <i class="bi bi-exclamation-triangle"></i> Reclamos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mapa.*') ? 'active' : '' }}"
                        href="{{ route('mapa.index') }}">
                        <i class="bi bi-map"></i> Mapa
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        @if(auth()->user()->role === 'administrador')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                                    href="{{ route('usuarios.index') }}">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                                    href="{{ route('reportes.index') }}">
                                    <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('viviendas.*') ? 'active' : '' }}"
                                href="{{ route('viviendas.index') }}">
                                <i class="bi bi-house"></i> Viviendas
                            </a>
                        </li>

                        @if(auth()->user()->role === 'administrador')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}"
                                    href="{{ route('asignaciones.index') }}">
                                    <i class="bi bi-clipboard-check"></i> Asignaciones
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('asignaciones.mis-asignaciones') ? 'active' : '' }}"
                                    href="{{ route('asignaciones.mis-asignaciones') }}">
                                    <i class="bi bi-clipboard-check"></i> Mis Asignaciones
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('inspecciones.*') ? 'active' : '' }}"
                                href="{{ route('inspecciones.index') }}">
                                <i class="bi bi-clipboard-data"></i> Inspecciones
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reclamos.*') ? 'active' : '' }}"
                                href="{{ route('reclamos.index') }}">
                                <i class="bi bi-exclamation-triangle"></i> Reclamos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('mapa.*') ? 'active' : '' }}"
                                href="{{ route('mapa.index') }}">
                                <i class="bi bi-map"></i> Mapa
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <!-- Breadcrumbs -->
                @yield('breadcrumbs')

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-x-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Errores de validación:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>
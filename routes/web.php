<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\ReclamoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViviendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Inspecciones IPV
|--------------------------------------------------------------------------
*/

// ============================================
// AUTENTICACIÓN
// ============================================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// RUTAS AUTENTICADAS
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Cambio de contraseña
    Route::get('/cambiar-password', [AuthController::class, 'showChangePasswordForm'])
        ->name('password.change');
    Route::post('/cambiar-password', [AuthController::class, 'changePassword'])
        ->name('password.update');
    
    // ============================================
    // USUARIOS (solo administrador)
    // ============================================
    Route::middleware(['role:administrador'])->group(function () {
        Route::resource('usuarios', UserController::class)->parameters([
            'usuarios' => 'usuario'
        ]);
        Route::post('/usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])
            ->name('usuarios.reset-password');
    });
    
    // ============================================
    // VIVIENDAS
    // ============================================
    Route::resource('viviendas', ViviendaController::class)->parameters([
        'viviendas' => 'vivienda'
    ]);
    Route::get('/viviendas/{vivienda}/historial', [ViviendaController::class, 'getHistorial'])
        ->name('viviendas.historial');
    
    // ============================================
    // INSPECCIONES
    // ============================================
    Route::resource('inspecciones', InspeccionController::class)->parameters([
        'inspecciones' => 'inspeccion'
    ]);
    Route::get('/inspecciones-map-data', [InspeccionController::class, 'getMapData'])
        ->name('inspecciones.map-data');
    
    // ============================================
    // RECLAMOS
    // ============================================
    Route::resource('reclamos', ReclamoController::class)->parameters([
        'reclamos' => 'reclamo'
    ]);
    Route::post('/reclamos/{reclamo}/resolve', [ReclamoController::class, 'resolve'])
        ->name('reclamos.resolve');
    
    // ============================================
    // MAPA
    // ============================================
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');
    Route::get('/mapa/inspecciones', [MapaController::class, 'getInspecciones'])
        ->name('mapa.inspecciones');
    Route::get('/mapa/heatmap', [MapaController::class, 'getHeatmapData'])
        ->name('mapa.heatmap');
    
    // ============================================
    // ASIGNACIONES
    // ============================================
    // Ruta especial para inspectores
    Route::get('/mis-asignaciones', [AsignacionController::class, 'misAsignaciones'])
        ->name('asignaciones.mis-asignaciones');
    
    // Cambiar estado de asignación
    Route::patch('/asignaciones/{asignacion}/cambiar-estado', [AsignacionController::class, 'cambiarEstado'])
        ->name('asignaciones.cambiar-estado');
    
    // CRUD completo
    Route::resource('asignaciones', AsignacionController::class)->parameters([
        'asignaciones' => 'asignacion'
    ]);

    
// ============================================
// REPORTES (solo administrador)
// ============================================
Route::middleware(['role:administrador'])
    ->prefix('reportes')
    ->name('reportes.')
    ->group(function () {
        // Índice principal
        Route::get('/', [ReporteController::class, 'index'])
            ->name('index');
        
        // Evolución de Vivienda
        Route::get('/evolucion-vivienda', [ReporteController::class, 'evolucionVivienda'])
            ->name('evolucion-vivienda-form');
        Route::get('/vivienda/{vivienda}', [ReporteController::class, 'generarEvolucionVivienda'])
            ->name('vivienda');
        Route::get('/vivienda/{vivienda}/pdf', [ReporteController::class, 'exportarEvolucionPDF'])
            ->name('evolucion-vivienda.pdf');
        Route::get('/vivienda/{vivienda}/excel', [ReporteController::class, 'exportarEvolucionExcel'])
            ->name('evolucion-vivienda.excel');
        
        // Otros reportes (placeholders)
        Route::get('/periodo', [ReporteController::class, 'inspeccionesPorPeriodo'])
            ->name('periodo');
        Route::get('/estadisticas', [ReporteController::class, 'estadisticasGenerales'])
            ->name('estadisticas');
        Route::get('/mapa-export', [ReporteController::class, 'exportarMapa'])
            ->name('mapa-export');
    });
});
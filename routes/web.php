<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PromotorController;
use App\Http\Controllers\ProductoController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta temporal para configurar el sistema y limpiar caché
Route::get('/setup-system', function () {
    try {
        // Limpiamos caché para forzar que Laravel lea el .env correctamente
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        \App\Models\ActivityLog::create(['action' => 'SYSTEM', 'description' => 'Sistema reiniciado y sincronizado']);
        return "Migraciones, seeders y prueba de bitácora ejecutados perfectamente. Revisa la BD.";
    } catch (\Exception $e) {
        return "ERROR AL MIGRAR: " . $e->getMessage() . " <br><br> EN LÍNEA: " . $e->getLine() . " <br> ARCHIVO: " . $e->getFile();
    }
});


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Gestión de Usuarios (Protegido por permiso)
    Route::middleware(['permission:manage_users'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Gestión de Roles (Protegido por permiso)
    Route::middleware(['permission:manage_roles'])->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Bitácora (Protegido por permiso)
    Route::middleware(['permission:view_audit_log'])->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    });

    // Perfil y Configuraciones
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Productos y Catálogo (Ver es público para usuarios autenticados, Gestionar requiere permiso)
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');

    Route::middleware(['permission:manage_inventory'])->group(function () {
        Route::resource('promotores', PromotorController::class);
        Route::resource('productos', ProductoController::class)->except(['index']);
    });

    // Horarios (CU7 y CU8)
    Route::get('horarios', [\App\Http\Controllers\HorarioController::class, 'index'])
        ->name('horarios.index')
        ->middleware('permission:view_schedules');
        
    Route::middleware(['permission:manage_schedules'])->group(function () {
        Route::resource('horarios', \App\Http\Controllers\HorarioController::class)->except(['index']);
    });

    // Servicios (CU14)
    Route::middleware(['permission:manage_services'])->group(function () {
        Route::resource('servicios', \App\Http\Controllers\ServicioController::class);
    });

    // Clientes (CU11)
    Route::middleware(['permission:manage_appointments'])->group(function () {
        Route::get('clientes', [\App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
    });
});

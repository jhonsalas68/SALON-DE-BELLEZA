<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PromotorController;
use App\Http\Controllers\ProductoController;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
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

// Password reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgetPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');


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

    // Clientes y Citas (CU9, CU10, CU11, CU12, CU19)
    Route::middleware(['permission:manage_appointments'])->group(function () {
        Route::resource('clientes', \App\Http\Controllers\ClienteController::class);
        Route::resource('citas', \App\Http\Controllers\CitaController::class);
        Route::post('citas/{cita}/asignar-estilista', [\App\Http\Controllers\CitaController::class, 'asignarEstilista'])->name('citas.asignar-estilista');
        Route::post('citas/{cita}/completar', [\App\Http\Controllers\CitaController::class, 'completar'])->name('citas.completar');
    });

    // Promociones (CU17)
    Route::middleware(['permission:manage_promotions'])->group(function () {
        Route::resource('promociones', \App\Http\Controllers\PromocionController::class);
    });

    // Ventas (CU22, CU23)
    Route::middleware(['permission:manage_sales'])->group(function () {
        Route::resource('ventas', \App\Http\Controllers\VentaController::class);
        Route::post('ventas/{venta}/update-status', [\App\Http\Controllers\VentaController::class, 'updateStatus'])->name('ventas.update-status');
    });

    // Rutas públicas de Stripe y Tickets para clientes (bajo middleware auth)
    Route::get('ventas/{venta}/stripe-success', [\App\Http\Controllers\VentaController::class, 'stripeSuccess'])->name('ventas.stripe.success');
    Route::get('ventas/{venta}/stripe-cancel', [\App\Http\Controllers\VentaController::class, 'stripeCancel'])->name('ventas.stripe.cancel');
    Route::get('ventas/{venta}/ticket', [\App\Http\Controllers\VentaController::class, 'ticket'])->name('ventas.ticket');
    Route::get('citas/{cita}/ticket', [\App\Http\Controllers\CitaController::class, 'showTicket'])->name('citas.show-ticket');

    // Comisiones (CU8)
    Route::middleware(['permission:view_commissions'])->group(function () {
        Route::get('comisiones', [\App\Http\Controllers\ComisionController::class, 'index'])->name('comisiones.index');
        Route::post('comisiones/{comision}/pagar', [\App\Http\Controllers\ComisionController::class, 'pagar'])->name('comisiones.pagar');
    });

    // Alertas (CU15)
    Route::middleware(['permission:view_stock_alerts'])->group(function () {
        Route::get('alertas', [\App\Http\Controllers\AlertaController::class, 'index'])->name('alertas.index');
        Route::post('alertas/{alerta}/leer', [\App\Http\Controllers\AlertaController::class, 'marcarLeida'])->name('alertas.leer');
        Route::post('alertas/leer-todas', [\App\Http\Controllers\AlertaController::class, 'marcarTodasLeidas'])->name('alertas.leer-todas');
    });

    // Reportes Administrativos (CU20)
    Route::middleware(['permission:view_reports'])->group(function () {
        Route::get('reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/imprimir', [\App\Http\Controllers\ReporteController::class, 'imprimir'])->name('reportes.imprimir');
    });

    // Buscador por Voz con Gemini AI
    Route::post('/ai/voice-search', [\App\Http\Controllers\AiVoiceSearchController::class, 'processVoice'])->name('ai.voice-search');

    // Arqueo y Cierre de Caja Chica (Opción 4)
    Route::get('cajas', [\App\Http\Controllers\CajaController::class, 'index'])->name('cajas.index');
    Route::post('cajas/abrir', [\App\Http\Controllers\CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::post('cajas/{caja}/cerrar', [\App\Http\Controllers\CajaController::class, 'cerrar'])->name('cajas.cerrar');
    Route::post('cajas/{caja}/movimiento', [\App\Http\Controllers\CajaController::class, 'storeMovimiento'])->name('cajas.movimiento');

    // Encuestas de Satisfacción y Valoraciones NPS (Opción 5)
    Route::get('valoraciones', [\App\Http\Controllers\ValoracionController::class, 'index'])->name('valoraciones.index');
    Route::post('valoraciones', [\App\Http\Controllers\ValoracionController::class, 'store'])->name('valoraciones.store');

    // Rutas exclusivas para el portal de clientes (Landing)
    Route::post('/client/appointments', [\App\Http\Controllers\LandingController::class, 'agendarCita'])->name('client.appointments.store');
    Route::post('/client/buy', [\App\Http\Controllers\LandingController::class, 'comprarProducto'])->name('client.products.buy');
    Route::get('/client/available-hours', [\App\Http\Controllers\LandingController::class, 'getHorariosDisponibles'])->name('client.appointments.hours');
});


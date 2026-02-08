<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityExceptionController;
use App\Http\Controllers\BusinessHourController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\ServicePackage; 

// PAGINAS PÚBLICAS ===============================================================================
// ================================================================================================

// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    return Inertia::render('Loading');
});

Route::get('/inicio', [LandingController::class, 'index'])->name('landing.index');

Route::get('/proceso', [LandingController::class, 'process'])->name('landing.process');
Route::get('/servicios/{category?}', [LandingController::class, 'services'])->name('landing.services');
Route::get('/portafolio/{category?}', [LandingController::class, 'portfolio'])->name('landing.portfolio'); 
Route::get('/contacto', [LandingController::class, 'contact'])->name('landing.contact');

// Rutas de Citas (Públicas)
// 1. Guardar cita
Route::resource('appointments', AppointmentController::class)->only(['store']);
// 2. [MOVIDO AQUÍ] Consultar días deshabilitados (público para el calendario)
Route::get('/appointments/disabled-days', [AppointmentController::class, 'disabledDays'])->name('appointments.disabled-days');
// 3. [MOVIDO AQUÍ] Checar disponibilidad de horas (público para el calendario)
Route::get('/appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('appointments.check');


// NUEVA RUTA: Guardar mensajes de contacto
Route::post('/contacto-mensaje', [ContactMessageController::class, 'store'])->name('contact.store');

// Endpoint ligero para obtener lista de servicios en el Modal de Citas
Route::get('/api/services-list', function () {
    return ServicePackage::where('is_active', true)
        ->orderBy('title', 'asc')
        ->pluck('title');
})->name('api.services.list');


// INTRANET (PANEL ADMINISTRATIVO) ================================================================
// ================================================================================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // 0. Dashboard Principal
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    // ---------------------------- CONFIGURACIÓN / ROLES ----------------------------
    Route::resource('roles', RoleController::class)->except(['show', 'create', 'edit']);
    
    Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy');

    // 1. Reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 2. Punto de Venta (POS)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    // ---------------- [NUEVOS MÓDULOS DE GESTIÓN] ---------------- //
    
    // Clientes
    Route::resource('clients', ClientController::class);

    // Inventario (Productos)
    Route::put('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::resource('products', ProductController::class);

    // Proveedores
    Route::resource('suppliers', SupplierController::class);

    // Cajas Registradoras
    Route::resource('cash-registers', CashRegisterController::class);

    // ------------------------------------------------------------- //

    // 3. Gestión de Página (CMS)
    Route::prefix('cms')->name('cms.')->group(function () {
        // Vista principal
        Route::get('/', [PageManagementController::class, 'index'])->name('index');
        
        // Rutas para Paquetes
        Route::post('/packages', [PageManagementController::class, 'storePackage'])->name('packages.store');
        Route::put('/packages/{package}', [PageManagementController::class, 'updatePackage'])->name('packages.update');
        Route::delete('/packages/{package}', [PageManagementController::class, 'destroyPackage'])->name('packages.destroy');

        // Rutas para Portafolio
        Route::post('/portfolio', [PageManagementController::class, 'storePortfolioItem'])->name('portfolio.store');
        Route::post('/portfolio/{item}', [PageManagementController::class, 'updatePortfolioItem'])->name('portfolio.update'); 
        Route::delete('/portfolio/{item}', [PageManagementController::class, 'destroyPortfolioItem'])->name('portfolio.destroy');
    });

    // 4. Usuarios (Gestión de usuarios del sistema)
    Route::resource('users', UserController::class);

    // 5. Citas (Gestión Administrativa)
    Route::resource('appointments-admin', AppointmentController::class)
        ->parameters(['appointments-admin' => 'appointment'])
        ->except(['store'])
        ->names('appointments');
        
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    
    // Ruta específica para cambio de estatus rápido
    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    
    // NOTA: Se movió 'check-availability' arriba para que sea pública
    // NOTA: Se movió 'disabled-days' arriba para que sea pública

    // 6. Mensajes de Contacto (Buzón de entrada)
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy'])->names('contact-messages');
    Route::put('/contact-messages/{contact_message}/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');

    // Rutas de notificaciones
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [NotificationController::class, 'bulkAction'])->name('bulk');
    });

    // ---------------------------- CONFIGURACIÓN DE AGENDA ----------------------------
    Route::get('/settings/calendar', [BusinessHourController::class, 'index'])->name('settings.calendar.index');
    Route::put('/settings/business-hours', [BusinessHourController::class, 'update'])->name('business-hours.update');
    
    Route::post('/settings/availability-exceptions', [AvailabilityExceptionController::class, 'store'])->name('availability-exceptions.store');
    Route::delete('/settings/availability-exceptions/{exception}', [AvailabilityExceptionController::class, 'destroy'])->name('availability-exceptions.destroy');
    
});

// eliminacion de archivo, imagen o video global
Route::delete('/media/{media}', function (Media $media) {
    try {
        $media->delete(); 
        // return response()->json(['message' => 'Archivo eliminado correctamente.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al eliminar el archivo.'], 500);
    }
})->name('media.delete-file');
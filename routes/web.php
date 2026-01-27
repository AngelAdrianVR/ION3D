<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\LandingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// PAGINAS PÚBLICAS ===============================================================================
// ================================================================================================

// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    return Inertia::render('Loading');
});

Route::get('/inicio', function () {
    return Inertia::render('Landing/Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/proceso', [LandingController::class, 'process'])->name('landing.process');
Route::get('/servicios/{category?}', [LandingController::class, 'services'])->name('landing.services'); // Parámetro opcional 'category'
Route::get('/portafolio/{category?}', [LandingController::class, 'portfolio'])->name('landing.portfolio'); // Parámetro opcional 'category'
Route::get('/contacto', [LandingController::class, 'contact'])->name('landing.contact');

// Rutas de Citas (Públicas para registro)
Route::resource('appointments', AppointmentController::class)->only(['store']);

// NUEVA RUTA: Guardar mensajes de contacto
Route::post('/contacto-mensaje', [ContactMessageController::class, 'store'])->name('contact.store');



// INTRANET =======================================================================================
// ================================================================================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

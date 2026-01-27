<?php

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

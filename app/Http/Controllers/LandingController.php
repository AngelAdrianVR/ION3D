<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    /**
     * Muestra la vista de Proceso.
     */
    public function process()
    {
        return Inertia::render('Landing/Process');
    }

    /**
     * Muestra la vista de Servicios.
     * Recibe una categoría opcional para preseleccionar la pestaña.
     */
    public function services($category = null)
    {
        // Si no hay categoría, por defecto usamos la primera (ej. 'escaneo-personas')
        // Esto se manejará en el frontend, pero pasamos el parámetro limpio.
        return Inertia::render('Landing/Services', [
            'category' => $category
        ]);
    }

    /**
     * Muestra la vista de Portafolio.
     * Recibe una categoría opcional para preseleccionar la pestaña.
     */
    public function portfolio($category = null)
    {
        return Inertia::render('Landing/Portfolio', [
            'category' => $category
        ]);
    }

    /**
     * Muestra la vista de Contacto.
     */
    public function contact()
    {
        return Inertia::render('Landing/Contact');
    }
}
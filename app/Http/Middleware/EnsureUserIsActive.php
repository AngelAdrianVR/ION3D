<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si hay un usuario logueado y si su estatus 'is_active' es false
        if (Auth::check() && !Auth::user()->is_active) {
            
            // Forzamos el cierre de sesión
            Auth::guard('web')->logout();

            // Invalidamos la sesión por seguridad
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirigimos al login con un mensaje
            // Nota: Asegúrate de que tu vista de Login muestre errores de sesión o 'status'
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.'
            ]);
        }

        return $next($request);
    }
}
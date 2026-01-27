<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        // 2. Crear registro
        ContactMessage::create($validated);

        // 3. Redirigir (Inertia manejará el onSuccess en el frontend)
        return Redirect::back()->with('success', 'Mensaje recibido correctamente.');
    }
}
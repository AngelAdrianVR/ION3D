<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityException;
use Illuminate\Http\Request;

class AvailabilityExceptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:availability_exceptions,date',
            'reason' => 'required|string|max:255',
            'is_closed' => 'boolean',
            'open_time' => 'nullable|required_if:is_closed,false',
            'close_time' => 'nullable|required_if:is_closed,false',
        ]);

        // Formatear tiempos si vienen de JS
        if (isset($validated['open_time'])) {
             // Asegurar formato H:i
             $validated['open_time'] = substr($validated['open_time'], 0, 5);
        }
        if (isset($validated['close_time'])) {
             $validated['close_time'] = substr($validated['close_time'], 0, 5);
        }

        AvailabilityException::create($validated);

        return back()->with('success', 'Día especial agregado correctamente.');
    }

    public function destroy(AvailabilityException $exception)
    {
        $exception->delete();
        return back()->with('success', 'Excepción eliminada.');
    }
}
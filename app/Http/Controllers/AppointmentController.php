<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * Almacena una nueva cita desde el formulario público.
     */
    public function store(Request $request)
    {
        // Validación básica
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'start_time' => 'required|date|after:now',
            'internal_notes' => 'nullable|string',
            // 'service_package_id' => 'nullable|exists:service_packages,id' // Descomentar si tienes la tabla lista
        ]);

        // Lógica de fechas (Asumimos 1 hora de duración por defecto)
        // El frontend envía timestamp en milisegundos o string ISO
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHour();

        Appointment::create([
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        return redirect()->back()->with('success', '¡Cita agendada correctamente! Nos pondremos en contacto contigo.');
    }

    public function show(Appointment $appointment)
    {
        //
    }

    public function edit(Appointment $appointment)
    {
        //
    }

    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    public function destroy(Appointment $appointment)
    {
        //
    }
}
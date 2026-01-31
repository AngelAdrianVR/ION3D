<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * Verifica la disponibilidad de horarios para una fecha específica.
     * Retorna un JSON con los slots y su estado (available/busy).
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 (Domingo) - 6 (Sábado)

        // --- REGLAS DE NEGOCIO ---

        // 1. Días Cerrados (Sábado y Domingo)
        // Carbon::SATURDAY es 6, SUNDAY es 0
        if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
            return response()->json([]); // Retorna lista vacía (Cerrado)
        }

        // 2. Horarios de Apertura y Cierre
        $startHour = 9; // Abre a las 9:00 AM todos los días hábiles
        $endHour = 17;  // Cierra a las 5:00 PM por defecto (Martes - Viernes)

        // Regla específica: Lunes cierra a las 2:00 PM (14:00)
        if ($dayOfWeek === Carbon::MONDAY) {
            $endHour = 14; 
        }

        // --- CONSULTA DE OCUPACIÓN REAL ---

        // Buscamos citas existentes en esa fecha
        $existingAppointments = Appointment::whereDate('start_time', $date->format('Y-m-d'))
            ->pluck('start_time') // Obtenemos solo la columna de hora inicio
            ->map(function ($time) {
                // Formateamos a 'HH:mm' para comparar fácilmente (Ej: "09:00")
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // --- GENERACIÓN DE SLOTS ---
        
        $slots = [];
        
        // Iteramos hora por hora desde apertura hasta cierre
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            // Formato de hora "09:00", "10:00"...
            $timeString = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            
            // Verificamos si esta hora ya existe en la base de datos
            $status = in_array($timeString, $existingAppointments) ? 'busy' : 'available';

            $slots[] = [
                'time' => $timeString,
                'status' => $status
            ];
        }

        return response()->json($slots);
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
        ]);

        // Verificación doble en backend para evitar colisiones simultáneas
        $exists = Appointment::where('start_time', $validated['start_time'])->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['date' => 'Lo sentimos, este horario acaba de ser ocupado. Por favor selecciona otro.']);
        }

        // Lógica de fechas (Asumimos 1 hora de duración por defecto)
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHour();

        // 1. Crear la cita
        $appointment = Appointment::create([
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        // 2. Notificar a los usuarios con rol "Super admin"
        try {
            $admins = User::role('Super admin')->get();
            
            if ($admins->count() > 0) {
                Notification::sendNow($admins, new NewAppointmentNotification($appointment));
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de cita: ' . $e->getMessage());
        }

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
<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\BusinessHour;       // Importante
use App\Models\AvailabilityException; // Importante
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Listado principal de citas (Admin Panel).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $appointments = Appointment::with('client') // Eager load si usas clientes registrados
            ->when($search, function ($query, $search) {
                $query->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('guest_phone', 'like', "%{$search}%")
                      ->orWhere('internal_notes', 'like', "%{$search}%")
                      ->orWhereHas('client', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->orderBy('start_time', 'desc') // Las más recientes primero
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Appointments/Index', [ // Ajusta la ruta de tu carpeta si es necesario
            'appointments' => $appointments,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Muestra el detalle de una cita (Puede usarse para una página dedicada o API).
     */
    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load('client'));
    }

    /**
     * Actualiza la información general de la cita (Fecha, notas, etc).
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'start_time' => 'required|date',
            'internal_notes' => 'nullable|string',
            // Si permites cambiar la hora, deberías validar disponibilidad aquí también, 
            // pero para admin a veces se permite sobreescribir (force).
        ]);

        // Recalcular end_time si cambia el start_time (Asumiendo 1 hora de duración)
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHour();

        $appointment->update([
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'internal_notes' => $validated['internal_notes'],
        ]);

        return redirect()->back()->with('success', 'Cita actualizada correctamente.');
    }

    /**
     * Método específico para cambiar el estatus rápidamente.
     * Corregido para coincidir con el ENUM de la BD.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Validamos solo los valores permitidos por tu ENUM
        $validated = $request->validate([
            'status' => 'required|in:Pendiente,Confirmada,Completada,Cancelada,No Asistió',
        ]);

        $appointment->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Estatus actualizado.');
    }

    /**
     * Elimina una cita.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->back()->with('success', 'Cita eliminada correctamente.');
    }

    /**
     * Retorna los días bloqueados (Cerrados por semana o Excepciones/Festivos)
     */
    public function disabledDays()
    {
        // 1. Días de la semana que siempre están cerrados (Ej: [0, 6] para Domingo y Sábado)
        $closedWeekDays = BusinessHour::where('is_closed', true)
            ->pluck('day_of_week')
            ->toArray();

        // 2. Fechas específicas cerradas (Excepciones futuras)
        $closedDates = AvailabilityException::where('date', '>=', now()->startOfDay())
            ->where('is_closed', true)
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            })
            ->toArray();

        return response()->json([
            'week_days' => $closedWeekDays,
            'dates' => $closedDates
        ]);
    }

    /**
     * Verifica la disponibilidad de horarios basándose en la configuración de la BD.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($request->date);
        
        // Variables iniciales
        $isOpen = false;
        $openTime = null;
        $closeTime = null;

        // 1. Revisar Excepciones (Prioridad Alta: Festivos, días especiales)
        $exception = AvailabilityException::where('date', $date->format('Y-m-d'))->first();

        if ($exception) {
            if (!$exception->is_closed) {
                $isOpen = true;
                $openTime = Carbon::parse($exception->open_time);
                $closeTime = Carbon::parse($exception->close_time);
            }
            // Si $exception->is_closed es true, $isOpen se mantiene en false
        } else {
            // 2. Revisar Horario Semanal (Si no hay excepción)
            // Carbon dayOfWeek: 0 (Domingo) - 6 (Sábado)
            $dayOfWeek = $date->dayOfWeek; 
            
            $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();

            if ($businessHour && !$businessHour->is_closed) {
                $isOpen = true;
                $openTime = Carbon::parse($businessHour->open_time);
                $closeTime = Carbon::parse($businessHour->close_time);
            }
        }

        // Si está cerrado, retornamos array vacío
        if (!$isOpen || !$openTime || !$closeTime) {
            return response()->json([]);
        }

        // --- CONSULTA DE OCUPACIÓN REAL ---

        // Buscamos citas existentes en esa fecha para bloquear esos horarios
        $existingAppointments = Appointment::whereDate('start_time', $date->format('Y-m-d'))
            ->pluck('start_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // --- GENERACIÓN DE SLOTS ---
        
        $slots = [];
        $currentSlot = $openTime->copy();
        
        // Generamos slots de 1 hora.
        // El bucle corre mientras la hora de inicio del slot sea MENOR a la hora de cierre.
        // Ejemplo: Si cierra a las 17:00, la última cita permitida es de 16:00 a 17:00.
        while ($currentSlot->format('H:i') < $closeTime->format('H:i')) {
            
            $timeString = $currentSlot->format('H:i');
            
            // Verificamos colisión con citas existentes
            $status = in_array($timeString, $existingAppointments) ? 'busy' : 'available';

            // Verificación extra: Si es el día de HOY, bloquear horas pasadas
            if ($date->isToday() && $currentSlot->lt(now())) {
                $status = 'busy'; 
            }

            $slots[] = [
                'time' => $timeString,
                'status' => $status
            ];

            // Incremento de 1 hora
            $currentSlot->addHour();
        }

        return response()->json($slots);
    }

    /**
     * Almacena una nueva cita.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'start_time' => 'required|date|after:now', // after:now evita citas en el pasado
            'internal_notes' => 'nullable|string',
        ]);

        // Verificación doble de concurrencia
        $exists = Appointment::where('start_time', $validated['start_time'])->exists();
        
        if ($exists) {
            // Si Inertia maneja errores, esto aparecerá en el form
            return redirect()->back()->withErrors(['start_time' => 'Lo sentimos, este horario acaba de ser reservado por otro usuario.']);
        }

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHour();

        $appointment = Appointment::create([
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        // Notificar admins
        try {
            $admins = User::role('Super admin')->get();
            if ($admins->count() > 0) {
                Notification::sendNow($admins, new NewAppointmentNotification($appointment));
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', '¡Cita agendada correctamente!');
    }
}
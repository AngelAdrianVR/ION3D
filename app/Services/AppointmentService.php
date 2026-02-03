<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\AvailabilityException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AppointmentService
{
    /**
     * Obtiene la disponibilidad mensual para mostrar en el calendario.
     * Devuelve un array con cada día y su estado (color).
     */
    public function getMonthAvailability(int $year, int $month)
    {
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // 1. Cargar configuraciones en memoria para no hacer queries por cada día
        $businessHours = BusinessHour::all()->keyBy('day_of_week');
        $exceptions = AvailabilityException::whereBetween('date', [$startOfMonth, $endOfMonth])
                                            ->get()
                                            ->keyBy(fn($item) => $item->date->format('Y-m-d'));

        // 2. Cargar todas las citas confirmadas del mes
        $appointments = Appointment::whereBetween('start_time', [$startOfMonth, $endOfMonth])
                                   ->whereIn('status', ['Confirmada', 'Pendiente']) // Ignoramos canceladas
                                   ->get()
                                   ->groupBy(fn($item) => Carbon::parse($item->start_time)->format('Y-m-d'));

        $calendarData = [];
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek; // 0 (Sun) - 6 (Sat)

            // A. Determinar horario operativo del día
            $openTime = null;
            $closeTime = null;
            $isClosed = false;

            // Revisar excepciones primero
            if ($exceptions->has($dateStr)) {
                $exception = $exceptions->get($dateStr);
                $isClosed = $exception->is_closed;
                $openTime = $exception->open_time ? Carbon::parse($dateStr . ' ' . $exception->open_time) : null;
                $closeTime = $exception->close_time ? Carbon::parse($dateStr . ' ' . $exception->close_time) : null;
            } 
            // Si no hay excepción, usar horario estándar
            elseif ($businessHours->has($dayOfWeek)) {
                $hours = $businessHours->get($dayOfWeek);
                $isClosed = $hours->is_closed;
                $openTime = $hours->open_time ? Carbon::parse($dateStr . ' ' . $hours->open_time) : null;
                $closeTime = $hours->close_time ? Carbon::parse($dateStr . ' ' . $hours->close_time) : null;
            } else {
                // Si no hay config, asumimos cerrado por seguridad
                $isClosed = true;
            }

            // B. Calcular Slots Totales Disponibles (Intervalos de 1 hora)
            $totalSlots = 0;
            if (!$isClosed && $openTime && $closeTime) {
                // Diferencia en horas
                $totalSlots = $openTime->diffInHours($closeTime);
            }

            // C. Contar ocupación real
            $dayAppointments = $appointments->has($dateStr) ? $appointments->get($dateStr)->count() : 0;
            
            // D. Calcular Disponibilidad Restante
            $availableSlots = max(0, $totalSlots - $dayAppointments);
            
            // E. Determinar "Nivel de Disponibilidad" (Semáforo)
            $status = 'closed';
            $color = 'gray'; // Por defecto

            if (!$isClosed && $totalSlots > 0) {
                if ($availableSlots == 0) {
                    $status = 'full';
                    $color = 'gray'; // Lleno
                } else {
                    $ratio = $availableSlots / $totalSlots;

                    if ($ratio >= 0.6) {
                        $status = 'high';
                        $color = 'green';
                    } elseif ($ratio >= 0.3) {
                        $status = 'medium';
                        $color = 'amber'; // o yellow
                    } else {
                        $status = 'low';
                        $color = 'red';
                    }
                }
            }

            $calendarData[] = [
                'date' => $dateStr,
                'is_today' => $date->isToday(),
                'status' => $status, // high, medium, low, full, closed
                'color' => $color,
                'slots_available' => $availableSlots,
                'slots_total' => $totalSlots
            ];
        }

        return $calendarData;
    }

    /**
     * Obtiene los horarios específicos (slots) disponibles para un día dado.
     */
    public function getDaySlots(string $dateStr)
    {
        $date = Carbon::parse($dateStr);
        $dayOfWeek = $date->dayOfWeek;
        
        // Lógica similar para determinar apertura/cierre
        $exception = AvailabilityException::where('date', $dateStr)->first();
        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();

        $openTime = null;
        $closeTime = null;
        $isClosed = false;

        if ($exception) {
            $isClosed = $exception->is_closed;
            $openTime = $exception->open_time;
            $closeTime = $exception->close_time;
        } elseif ($businessHour) {
            $isClosed = $businessHour->is_closed;
            $openTime = $businessHour->open_time;
            $closeTime = $businessHour->close_time;
        } else {
            return []; // Cerrado
        }

        if ($isClosed || !$openTime || !$closeTime) {
            return [];
        }

        // Generar slots de 1 hora
        $start = Carbon::parse($dateStr . ' ' . $openTime);
        $end = Carbon::parse($dateStr . ' ' . $closeTime);
        
        // Obtener citas ya ocupadas ese día
        $bookedTimes = Appointment::whereDate('start_time', $date)
            ->whereIn('status', ['Confirmada', 'Pendiente'])
            ->get()
            ->pluck('start_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $slots = [];
        
        // Loop mientras la hora de inicio + 1 hora sea menor o igual al cierre
        while ($start->copy()->addHour()->lte($end)) {
            $timeString = $start->format('H:i');
            
            // Si la hora NO está en la lista de ocupados
            if (!in_array($timeString, $bookedTimes)) {
                $slots[] = $timeString;
            }
            
            $start->addHour();
        }

        return $slots;
    }
}
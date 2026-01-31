<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\AvailabilityException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class BusinessHourController extends Controller
{
    /**
     * Muestra la vista principal de configuración de calendario.
     */
    public function index()
    {
        // Asegurar que existan registros para los 7 días
        $this->ensureBusinessHoursExist();

        $businessHours = BusinessHour::orderBy('day_of_week')->get();
        
        // Obtenemos excepciones futuras o recientes (últimos 30 días para historial)
        $exceptions = AvailabilityException::where('date', '>=', now()->subDays(30))
            ->orderBy('date')
            ->get();

        return Inertia::render('Setting/AppointmentCalendar/Index', [
            'businessHours' => $businessHours,
            'exceptions' => $exceptions,
        ]);
    }

    /**
     * Actualiza los horarios de la semana.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'hours' => 'required|array',
            'hours.*.id' => 'required|exists:business_hours,id',
            'hours.*.open_time' => 'nullable|date_format:H:i:s', // Naive UI a veces manda H:i:s
            'hours.*.close_time' => 'nullable|date_format:H:i:s',
            'hours.*.is_closed' => 'boolean',
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['hours'] as $hourData) {
                BusinessHour::where('id', $hourData['id'])->update([
                    'open_time' => $hourData['open_time'],
                    'close_time' => $hourData['close_time'],
                    'is_closed' => $hourData['is_closed'],
                ]);
            }
        });

        return back()->with('success', 'Horarios semanales actualizados correctamente.');
    }

    private function ensureBusinessHoursExist()
    {
        if (BusinessHour::count() < 7) {
            for ($i = 0; $i <= 6; $i++) {
                BusinessHour::firstOrCreate(
                    ['day_of_week' => $i],
                    ['is_closed' => true] // Por defecto cerrado si se crea nuevo
                );
            }
        }
    }
}
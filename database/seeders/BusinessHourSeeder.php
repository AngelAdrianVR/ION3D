<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHour;
use App\Models\AvailabilityException;
use Carbon\Carbon;

class BusinessHourSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar tabla previa
        BusinessHour::truncate();
        AvailabilityException::truncate();

        // 2. Crear horario Lunes (1) a Viernes (5)
        // 09:00 AM - 06:00 PM
        $days = [
            1 => 'Lunes', 
            2 => 'Martes', 
            3 => 'Miércoles', 
            4 => 'Jueves', 
            5 => 'Viernes'
        ];

        foreach ($days as $index => $dayName) {
            BusinessHour::create([
                'day_of_week' => $index,
                'open_time' => '09:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ]);
        }

        // 3. Sábado (6) medio día
        BusinessHour::create([
            'day_of_week' => 6,
            'open_time' => '09:00:00',
            'close_time' => '14:00:00',
            'is_closed' => false,
        ]);

        // 4. Domingo (0) Cerrado
        BusinessHour::create([
            'day_of_week' => 0,
            'open_time' => null,
            'close_time' => null,
            'is_closed' => true,
        ]);

        // 5. Excepciones de prueba (Navidad y Año Nuevo próximos)
        $currentYear = date('Y');
        
        AvailabilityException::create([
            'date' => Carbon::create($currentYear, 12, 25),
            'reason' => 'Navidad',
            'is_closed' => true,
        ]);

        AvailabilityException::create([
            'date' => Carbon::create($currentYear + 1, 1, 1),
            'reason' => 'Año Nuevo',
            'is_closed' => true,
        ]);
        
        // Excepción de prueba: Un día con horario reducido por mantenimiento
        AvailabilityException::create([
            'date' => Carbon::now()->addDays(10), // En 10 días
            'reason' => 'Inventario Mensual (Horario Reducido)',
            'is_closed' => false,
            'open_time' => '09:00:00',
            'close_time' => '12:00:00',
        ]);
    }
}
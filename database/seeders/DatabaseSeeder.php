<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Ejecutar primero el seeder de roles y permisos
        $this->call(RoleSeeder::class);

        // Crear un usuario de soporte Super Admin
        $SupportUser = User::factory()->create([
            'name' => 'Soporte DTW',
            'email' => 'angel@gmail.com',
            'password' => bcrypt('321321321'), // Asegúrate de encriptar
        ]);
        
        // Asignar rol al usuario creado
        $SupportUser->assignRole('Super admin');


        // Crear un usuario de Principal Super Admin
        $AdminUser = User::factory()->create([
            'name' => 'Alejandro Cordero',
            'email' => 'alejandro@gmail.com',
            'password' => bcrypt('321321321'), // Asegúrate de encriptar
        ]);
        
        // Asignar rol al usuario creado
        $AdminUser->assignRole('Super admin');

        // Ejecutar el seeder de horas y dias de calendario (citas)
        $this->call(BusinessHourSeeder::class);

        // Crear Caja Registradora Principal
        CashRegister::create([
            'name' => 'Caja Principal',
            'location' => 'Mostrador Principal',
            'is_active' => true,
        ]);
    }
}
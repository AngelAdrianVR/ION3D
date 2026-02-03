<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            // Relación opcional con cliente registrado
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            
            // Datos para invitados (Citas rápidas sin cuenta)
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();

            $table->dateTime('start_time');
            $table->dateTime('end_time');
            
            $table->enum('status', ['Pendiente', 'Confirmada', 'Completada', 'Cancelada', 'No Asistió'])->default('Pendiente');
            
            // Relación opcional con paquete
            $table->foreignId('service_package_id')->nullable()->constrained()->nullOnDelete();
            
            $table->text('internal_notes')->nullable();

            // Índice compuesto para buscar citas en rangos de fechas rápidamente
            $table->index(['start_time', 'end_time', 'status']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

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
        // 1. Horarios de Atención (Configuración Semanal)
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
            $table->tinyInteger('day_of_week')->unique(); 
            $table->time('open_time')->nullable();  // Ej: 09:00:00
            $table->time('close_time')->nullable(); // Ej: 18:00:00
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};

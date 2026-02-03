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
        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // Ej: 2024-12-25
            $table->string('reason')->nullable(); // "Navidad", "Mantenimiento"
            $table->boolean('is_closed')->default(true);
            
            // Si no está cerrado, usa estos horarios en lugar de los default
            $table->time('open_time')->nullable(); 
            $table->time('close_time')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_exceptions');
    }
};

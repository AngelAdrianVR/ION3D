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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            
            // Datos de facturación / crédito
            $table->string('tax_id')->nullable(); // RFC/NIT
            $table->decimal('credit_limit', 12, 2)->default(0); // Límite de crédito
            $table->decimal('current_balance', 12, 2)->default(0); // Cuánto debe actualmente

            // Dirección Principal (Para autocompletar en el POS)
            // $table->string('road_type')->nullable();        // NUEVO: Tipo de vialidad (Calle, Avenida, Blvd)
            $table->string('street')->nullable();           // Calle
            $table->string('exterior_number')->nullable();  // Número Exterior
            $table->string('interior_number')->nullable();  // Número Interior
            $table->string('neighborhood')->nullable();     // Colonia
            $table->string('municipality')->nullable();     // Municipio / Alcaldía
            $table->string('state')->nullable();            // Estado
            $table->string('zip_code')->nullable();         // Código Postal
            $table->string('country')->default('México');   // País

            // personales
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('origin')->nullable()->comment('Ej: Instagram, Google');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

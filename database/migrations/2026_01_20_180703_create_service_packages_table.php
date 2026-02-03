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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('pricing_options')->nullable(); // Agregamos columna JSON para las opciones de precio (Ej: 10cm -> $2000)
            $table->json('features')->nullable(); // Lista de beneficios en JSON
            $table->decimal('price', 10, 2)->nullable(); // Precio base (opcional si se usan opciones de precio)
            $table->boolean('is_promo')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};

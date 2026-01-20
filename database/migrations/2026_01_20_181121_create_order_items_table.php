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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Polimorfismo: Guarda el ID y el TIPO (App\Models\Product o App\Models\ServicePackage)
            $table->morphs('purchasable');
            
            $table->integer('quantity');
            
            // Guardamos precios históricos para que los reportes no cambien si cambias los precios del catálogo
            $table->decimal('unit_price', 10, 2); 
            $table->decimal('cost', 10, 2)->nullable(); // Para calcular utilidad neta histórica
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

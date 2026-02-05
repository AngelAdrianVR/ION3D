<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // ---------------------------------------------------------
        // 2. CUENTAS POR COBRAR (Historial de Crédito)
        // ---------------------------------------------------------
        Schema::create('client_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Empleado que registró el movimiento
            
            // Relación polimórfica: El movimiento viene de una 'Order' (deuda) o un 'Payment' (abono)
            $table->nullableMorphs('reference');

            $table->enum('type', ['Cargo', 'Abono']); // Cargo = Aumenta deuda, Abono = Disminuye deuda
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2); // Saldo después del movimiento
            
            $table->string('description')->nullable(); // Ej: "Compra a crédito Folio #123", "Abono parcial en efectivo"
            $table->timestamps();
        });

        // ---------------------------------------------------------
        // 3. CAJAS Y TURNOS
        // ---------------------------------------------------------
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained();
            $table->foreignId('user_id')->constrained();
            
            $table->decimal('opening_amount', 12, 2);
            $table->decimal('closing_amount', 12, 2)->nullable();
            $table->decimal('calculated_amount', 12, 2)->nullable();
            
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            
            $table->enum('status', ['Abierta', 'Cerrada', 'Error'])->default('Abierta');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            
            $table->enum('type', ['Ingreso', 'Retiro', 'Gasto']);
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->timestamps();
        });

        // ---------------------------------------------------------
        // 4. PROVEEDORES Y COMPRAS
        // ---------------------------------------------------------
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('user_id')->constrained();
            
            $table->string('reference_number')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['Pendiente', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->date('purchase_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
        });

        // ---------------------------------------------------------
        // 5. INVENTARIO (Kardex)
        // ---------------------------------------------------------
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->nullableMorphs('reference'); // Order, Purchase, Adjustment
            
            // Tipos de movimiento en Español
            $table->enum('type', ['Venta', 'Compra', 'Devolucion', 'Ajuste', 'Merma']);
            
            $table->integer('quantity'); // +/-
            $table->integer('stock_after')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ---------------------------------------------------------
        // 6. PAGOS (Soporte Crédito y Mixto)
        // ---------------------------------------------------------
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_register_session_id')->nullable()->constrained();
            
            // 'Crédito' no suma a la caja, sino a la deuda del cliente
            $table->enum('payment_method', ['Efectivo', 'Tarjeta', 'Transferencia', 'Crédito', 'Otro']);
            
            $table->decimal('amount', 12, 2);
            $table->string('transaction_reference')->nullable();
            $table->timestamps();
        });

        // ---------------------------------------------------------
        // 7. ACTUALIZACIÓN DE ÓRDENES (Envíos y Facturación)
        // ---------------------------------------------------------
        Schema::table('orders', function (Blueprint $table) {
            // Relación con la caja
            $table->foreignId('cash_register_session_id')->nullable()->after('user_id')->constrained();
            
            // Montos desglosados
            $table->decimal('subtotal', 12, 2)->after('total_amount')->default(0);
            $table->decimal('tax_amount', 12, 2)->after('subtotal')->default(0);
            $table->decimal('discount_amount', 12, 2)->after('tax_amount')->default(0);

            // DATOS DE ENVÍO (Snapshot de la dirección al momento de la venta)
            // Guardamos esto en JSON para no depender de si el cliente cambia su dirección en el futuro
            $table->json('shipping_address')->nullable()->after('notes')->comment('{calle, ciudad, cp, estado, ref}');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_address');

            // ESTADOS EN ESPAÑOL Y CAMELCASE
            // NOTA: Si la tabla ya tiene datos con enums en inglés, esto fallará. 
            // Esto asume una migración nueva o base de datos vacía.
            
            // Eliminamos la columna anterior si existe para re-crearla con nuevas opciones (Opcional, depende de tu setup)
            // $table->dropColumn('payment_status'); 
            
            // Como es 'Schema::table', solo añadimos columnas nuevas o modificamos existentes.
            // Laravel no soporta cambiar opciones de ENUM nativamente fácil, 
            // así que añadimos columna de estado de envío nueva:
            
            $table->enum('shipping_status', ['Pendiente', 'En_Proceso', 'Enviado', 'Entregado', 'Devuelto'])
                  ->default('Pendiente')
                  ->after('payment_status');
        });
        
        // Ajuste final: Modificar payment_status y status de orders para usar español
        // Esto requiere SQL directo si estás usando MySQL
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('Pagado', 'Pendiente', 'Parcial', 'Crédito') DEFAULT 'Pagado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cash_register_session_id']);
            $table->dropColumn([
                'cash_register_session_id', 
                'subtotal', 
                'tax_amount', 
                'discount_amount', 
                'shipping_address',
                'shipping_cost',
                'shipping_status'
            ]);
        });

        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_register_sessions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('client_ledger');
    }
};
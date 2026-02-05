<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'shipping_address' => 'array', // Convierte el JSON a Array automáticamente
        'sold_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Relación con el turno de caja donde se creó la orden
    public function session(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    // Desglose de pagos (Efectivo, Tarjeta, Crédito, etc.)
    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    // Relación polimórfica para el Kardex (Salida de mercancía)
    public function inventoryMovements(): MorphMany
    {
        return $this->morphMany(InventoryMovement::class, 'reference');
    }
    
    // Relación polimórfica para Cuenta Corriente (Cargo por deuda)
    public function ledgerMovements(): MorphMany
    {
        return $this->morphMany(ClientLedger::class, 'reference');
    }
}

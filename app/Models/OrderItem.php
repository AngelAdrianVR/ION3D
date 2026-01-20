<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'unit_price',
        'cost',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relación inversa a la orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación Polimórfica:
     * Devuelve la instancia del modelo que se compró:
     * App\Models\Product O App\Models\ServicePackage
     */
    public function purchasable()
    {
        return $this->morphTo();
    }
}
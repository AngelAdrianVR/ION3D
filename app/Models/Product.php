<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $guarded = ['id'];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    // Relación polimórfica inversa con Order Items
    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }

    // Historial de movimientos de inventario de este producto
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Colección para la foto del producto
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_image')->singleFile();
    }
}
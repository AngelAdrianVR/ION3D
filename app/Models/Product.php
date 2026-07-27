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

    // Colecciones de medios
    public function registerMediaCollections(): void
    {
        // Múltiples imágenes (ilimitadas)
        $this->addMediaCollection('product_images');
        // Un solo video
        $this->addMediaCollection('product_video')->singleFile();
    }

    /**
     * Obtener todas las imágenes del producto.
     */
    public function getImagesAttribute()
    {
        return $this->getMedia('product_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
            ];
        });
    }

    /**
     * Obtener la URL del video del producto.
     */
    public function getVideoUrlAttribute()
    {
        return $this->getFirstMediaUrl('product_video');
    }
}
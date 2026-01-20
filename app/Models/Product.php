<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'alert_threshold',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'alert_threshold' => 'integer',
    ];

    // Relación Polimórfica: Un producto puede estar en muchas ordenes
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }
    
    // Colección para la foto del producto
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_image')->singleFile();
    }
}
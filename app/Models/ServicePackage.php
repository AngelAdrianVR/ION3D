<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServicePackage extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'features', // Se guarda como JSON
        'price',
        'pricing_options', // Precios dinámicos con opciones adicionales
        'is_promo',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array', // Convierte el JSON a Array automáticamente
        'price' => 'decimal:2',
        'is_promo' => 'boolean',
        'is_active' => 'boolean',
        'pricing_options' => 'array', // precio dinámicos con opciones adicionales
    ];

    // Relación Polimórfica: Un paquete puede estar en muchas ordenes
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }

    // Relación: Un paquete puede estar agendado en muchas citas
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
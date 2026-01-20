<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'features', // Se guarda como JSON
        'price',
        'is_promo',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array', // Convierte el JSON a Array automáticamente
        'price' => 'decimal:2',
        'is_promo' => 'boolean',
        'is_active' => 'boolean',
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
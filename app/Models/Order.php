<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
        'sold_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    // Relación: Pertenece a un cliente (opcional)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: Pertenece al usuario (vendedor) que la creó
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Tiene muchos items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
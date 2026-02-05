<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // Historial de movimientos de saldo
    public function ledger(): HasMany
    {
        return $this->hasMany(ClientLedger::class);
    }
    
    // Helper para verificar crédito disponible
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->current_balance;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'guest_name',
        'guest_phone',
        'start_time',
        'end_time',
        'status',
        'service_package_id',
        'internal_notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relación: Puede pertenecer a un cliente (o ser null)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: Puede estar ligada a un paquete de servicio
    public function servicePackage()
    {
        return $this->belongsTo(ServicePackage::class);
    }
    
    // Helper para obtener el nombre real (sea cliente registrado o invitado)
    public function getCustomerNameAttribute()
    {
        return $this->client ? $this->client->name : $this->guest_name;
    }
}
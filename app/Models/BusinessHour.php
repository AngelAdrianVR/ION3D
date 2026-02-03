<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        // En Laravel 12/11 'datetime' o 'immutable_time' suele ser suficiente
    ];
}
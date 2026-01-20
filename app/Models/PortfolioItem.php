<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PortfolioItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'category',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    // Opcional: Definir colecciones de medios si quieres tamaños específicos
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portfolio')
             ->singleFile(); // Solo una imagen principal por item, o quita esto para galería
    }
}
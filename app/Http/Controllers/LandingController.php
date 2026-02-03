<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    /**
     * Muestra la vista de Proceso.
     */
    public function process()
    {
        return Inertia::render('Landing/Process');
    }

    /**
     * Muestra la vista de Servicios.
     * Ahora obtiene TODAS las imágenes para el carrusel.
     */
    public function services($category = null)
    {
        // Recuperamos solo los paquetes activos
        $packages = ServicePackage::where('is_active', true)
            ->with('media')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pkg) {
                
                // 1. Decodificar y normalizar precios
                $pricing = $pkg->pricing_options;
                if (is_string($pricing)) {
                    $pricing = json_decode($pricing, true);
                }
                if (!is_array($pricing)) {
                    $pricing = [];
                }
                
                $pricing = array_map(function($option) {
                    if (isset($option['price'])) {
                        $option['price'] = (float) $option['price'];
                    }
                    return $option;
                }, $pricing);

                // Obtener todas las URLs de las imágenes
                $images = $pkg->getMedia('package_images')->map(function($media) {
                    return $media->getUrl();
                })->toArray();

                // 2. Construir objeto limpio para la vista
                return [
                    'id' => $pkg->id,
                    'slug' => $pkg->slug ?? Str::slug($pkg->title),
                    'title' => $pkg->title,
                    'subtitle' => 'Servicio Profesional', 
                    'description' => $pkg->description,
                    'features' => $pkg->features ?? [],
                    'prices' => $pricing,
                    'is_promo' => (bool) $pkg->is_promo,
                    // Enviamos array completo de imágenes y fallback
                    'images' => count($images) > 0 ? $images : ['https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'],
                    'image_fallback' => 'https://images.unsplash.com/photo-1631541909061-71e349d1f203?auto=format&fit=crop&q=80&w=1000'
                ];
            });

        return Inertia::render('Landing/Services', [
            'packages' => $packages,
            'initialCategory' => $category
        ]);
    }

    /**
     * Muestra la vista de Portafolio.
     * Ahora obtiene datos reales de la BD.
     */
    public function portfolio($category = null)
    {
        // Obtener todos los items ordenados
        $items = PortfolioItem::with('media')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'category' => $item->category, // 'Impresión', 'Escaneo', 'Modelado'
                    'is_featured' => (bool) $item->is_featured,
                    'img' => $item->getFirstMediaUrl('portfolio_image'),
                    // Slugificamos la categoría para usarla como key en el frontend
                    'category_slug' => Str::slug($item->category) 
                ];
            });

        // Agrupar por el slug de la categoría
        $groupedItems = $items->groupBy('category_slug');

        // Mapeo para manejar URLs antiguas o directas
        // Si entra '/portafolio/comercial' -> lo dirigimos a 'impresion' (ejemplo)
        $initialTab = 'impresion'; // Default
        
        if ($category) {
            // Normalizar categoría entrante
            $normalized = Str::slug($category);
            
            // Mapeo de legacy links a nuevas categorías de BD
            $map = [
                'comercial' => 'impresion',
                'arte' => 'modelado',
                'casos' => 'escaneo'
            ];

            $initialTab = $map[$normalized] ?? $normalized;
        }

        // Si la categoría solicitada no tiene items, volvemos al default
        if (!$groupedItems->has($initialTab) && $groupedItems->isNotEmpty()) {
            $initialTab = $groupedItems->keys()->first();
        }

        return Inertia::render('Landing/Portfolio', [
            'dbItems' => $groupedItems,
            'initialCategory' => $initialTab
        ]);
    }

    /**
     * Muestra la vista de Contacto.
     */
    public function contact()
    {
        return Inertia::render('Landing/Contact');
    }
}
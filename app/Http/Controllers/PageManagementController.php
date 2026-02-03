<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('CMS/Index', [
            'packages' => ServicePackage::with('media')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($pkg) {
                    // Mapeamos imágenes con su ID para poder borrarlas
                    $pkg->images = $pkg->getMedia('package_images')->map(fn($media) => [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->file_name
                    ]);
                    $pkg->preview_image = $pkg->getFirstMediaUrl('package_images');

                    // FIX: Asegurar que pricing_options se decodifique si es string (JSON)
                    // Esto corrige el problema de que los precios no se vean en el modal
                    if (is_string($pkg->pricing_options)) {
                        $pkg->pricing_options = json_decode($pkg->pricing_options, true);
                    }
                    
                    // Asegurarnos que siempre sea un array para evitar errores en Vue
                    if (!is_array($pkg->pricing_options)) {
                         $pkg->pricing_options = [];
                    } else {
                        // CORRECCIÓN TYPE CHECK: Convertimos explícitamente los precios de string a float
                        // NaiveUI requiere tipo Number, o lanzará warning y no mostrará el valor
                        $pkg->pricing_options = array_map(function($option) {
                            if (isset($option['price'])) {
                                $option['price'] = (float) $option['price'];
                            }
                            return $option;
                        }, $pkg->pricing_options);
                    }

                    return $pkg;
                }),
            
            'portfolio' => PortfolioItem::with('media')
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'category' => $item->category,
                        'sort_order' => $item->sort_order,
                        'is_featured' => $item->is_featured,
                        'image_url' => $item->getFirstMediaUrl('portfolio_image'), 
                        // Mapeamos también como objeto media para el lightbox
                        'media_object' => [
                            'url' => $item->getFirstMediaUrl('portfolio_image'),
                            'title' => $item->title
                        ]
                    ];
                }),
        ]);
    }

    // ================= PAQUETES =================

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'pricing_options' => 'nullable|array', // Nuevo Array de objetos
            'is_promo' => 'boolean',
            'is_active' => 'boolean',
            'images.*' => 'image|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['price'] = 0; // Valor por defecto si usas opciones

        $package = ServicePackage::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $package->addMedia($image)->toMediaCollection('package_images');
            }
        }

        return redirect()->back()->with('success', 'Paquete creado.');
    }

    public function updatePackage(Request $request, ServicePackage $package)
    {
        // NOTA: No esperamos _method: PUT, usamos POST directo para archivos
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'pricing_options' => 'nullable|array',
            'is_promo' => 'boolean',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|max:10240',
        ]);

        if ($package->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $package->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $package->addMedia($image)->toMediaCollection('package_images');
            }
        }

        return redirect()->back()->with('success', 'Paquete actualizado.');
    }

    public function destroyPackage(ServicePackage $package)
    {
        $package->delete();
        return redirect()->back()->with('success', 'Paquete eliminado.');
    }

    // ================= PORTAFOLIO =================

    public function storePortfolioItem(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Impresión,Escaneo,Modelado',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'image' => 'required|image|max:10240',
        ]);

        $item = PortfolioItem::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('image')) {
            $item->addMediaFromRequest('image')->toMediaCollection('portfolio_image');
        }

        return redirect()->back()->with('success', 'Item añadido.');
    }

    public function updatePortfolioItem(Request $request, PortfolioItem $item)
    {
        // Usamos POST directo
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Impresión,Escaneo,Modelado',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:10240',
        ]);

        $item->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'],
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('image')) {
            $item->clearMediaCollection('portfolio_image');
            $item->addMediaFromRequest('image')->toMediaCollection('portfolio_image');
        }

        return redirect()->back()->with('success', 'Item actualizado.');
    }

    public function destroyPortfolioItem(PortfolioItem $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Item eliminado.');
    }
}
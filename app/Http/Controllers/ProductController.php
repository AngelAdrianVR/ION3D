<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        $searchTerm = $request->input('search');

        $products = Product::query()
            ->when($searchTerm, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sale_price' => $product->sale_price,
                    'stock_quantity' => $product->stock_quantity,
                    'alert_threshold' => $product->alert_threshold,
                    'is_active' => $product->is_active,
                    'image_url' => $product->getFirstMediaUrl('product_image'),
                ];
            });

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Product::create($request->except('image'));

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                    ->toMediaCollection('product_image');
        }

        return Redirect::route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Muestra el recurso especificado (Con historial de movimientos).
     */
    public function show(Request $request, Product $product)
    {
        $product->image_url = $product->getFirstMediaUrl('product_image');

        // Filtro de fecha para movimientos
        $filterDate = $request->input('date');
        
        if ($filterDate) {
            // CORRECCIÓN: Si es numérico, lo tratamos como timestamp en milisegundos (JS)
            // De lo contrario intentamos parsearlo como string
            if (is_numeric($filterDate)) {
                $date = Carbon::createFromTimestampMs($filterDate);
            } else {
                $date = Carbon::parse($filterDate);
            }
        } else {
            $date = now();
        }

        $movements = $product->inventoryMovements()
            ->with('user')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->latest()
            ->get()
            ->map(function ($mov) {
                return [
                    'id' => $mov->id,
                    'type' => $mov->type,
                    'quantity' => $mov->quantity,
                    'stock_after' => $mov->stock_after,
                    'description' => $mov->description,
                    'created_at_formatted' => $mov->created_at->format('d M Y, h:i A'),
                    'user_name' => $mov->user ? $mov->user->name : 'Sistema',
                ];
            });

        return Inertia::render('Products/Show', [
            'product' => $product,
            'movements' => $movements,
            'filters' => [
                'date' => $date->timestamp * 1000 // Devolvemos MS para el datepicker
            ]
        ]);
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'stock_quantity' => $product->stock_quantity,
                'alert_threshold' => $product->alert_threshold,
                'is_active' => (bool) $product->is_active,
                'image_url' => $product->getFirstMediaUrl('product_image'),
            ]
        ]);
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable',
        ]);

        $product->update($request->except('image'));

        if ($request->hasFile('image')) {
            $product->clearMediaCollection('product_image');
            $product->addMediaFromRequest('image')
                    ->toMediaCollection('product_image');
        }

        return Redirect::route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return Redirect::route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo de un producto.
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Producto {$status} correctamente.");
    }
}
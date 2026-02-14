<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMovementController extends Controller
{
    /**
     * Guarda un nuevo movimiento manual de inventario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'direction' => 'required|in:in,out', // Entrada o Salida
            'type' => 'required|in:Venta,Compra,Devolucion,Ajuste,Merma',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Determinar el signo de la cantidad
        $quantity = $request->quantity;
        if ($request->direction === 'out') {
            $quantity = -$quantity;
        }

        // Validar stock suficiente si es salida
        if ($quantity < 0 && ($product->stock_quantity + $quantity) < 0) {
            return back()->withErrors(['quantity' => 'Stock insuficiente para realizar esta salida.']);
        }

        DB::transaction(function () use ($product, $request, $quantity) {
            // 1. Calcular nuevo stock
            $newStock = $product->stock_quantity + $quantity;

            // 2. Actualizar producto
            $product->update(['stock_quantity' => $newStock]);

            // 3. Registrar movimiento
            InventoryMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $quantity,
                'stock_after' => $newStock,
                'description' => $request->description,
                'reference_type' => null, // Es manual
                'reference_id' => null,
            ]);
        });

        return back()->with('success', 'Movimiento registrado correctamente.');
    }
}
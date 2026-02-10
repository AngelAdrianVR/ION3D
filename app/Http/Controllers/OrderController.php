<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Muestra el listado de ventas con filtros.
     */
    public function index(Request $request)
    {
        $query = Order::with(['client', 'user', 'items'])
            ->orderBy('sold_at', 'desc');

        // 1. Búsqueda por Folio o Nombre de Cliente
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', "%{$request->search}%")
                  ->orWhereHas('client', function($c) use ($request) {
                      $c->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        // 2. Filtro por Rango de Fechas
        if ($request->date_range && is_array($request->date_range)) {
            // Naive UI envía timestamps en milisegundos
            $startDate = Carbon::createFromTimestampMs($request->date_range[0])->startOfDay();
            $endDate = Carbon::createFromTimestampMs($request->date_range[1])->endOfDay();
            
            $query->whereBetween('sold_at', [$startDate, $endDate]);
        }

        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'date_range']),
        ]);
    }

    /**
     * Muestra el detalle de una venta.
     */
    public function show(Order $order)
    {
        // Cargamos relaciones necesarias, incluyendo las imágenes de los productos (purchasable.media)
        $order->load(['client', 'user', 'items.purchasable.media']);
        
        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    // Nuevo método para actualizar estatus desde el Stepper
    public function updateShippingStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pendiente,En_Proceso,Enviado,Entregado'
        ]);

        if ($order->shipping_status === 'Devuelto') {
            return back()->with('error', 'No se puede cambiar el estatus de una orden devuelta.');
        }

        $order->update(['shipping_status' => $request->status]);

        return back()->with('success', "Estatus actualizado a {$request->status}");
    }

    /**
     * Procesa la devolución de una orden completa.
     * Restaura el stock de los productos y marca el envío como 'Devuelto'.
     */
    public function returnOrder(Order $order)
    {
        // Validar si ya está devuelta para evitar duplicidad de stock
        if ($order->shipping_status === 'Devuelto') {
            return back()->with('error', 'Esta orden ya ha sido devuelta anteriormente.');
        }

        try {
            DB::transaction(function () use ($order) {
                // 1. Iterar sobre los items para devolver stock
                foreach ($order->items as $item) {
                    // Solo devolvemos stock si es un Producto (no servicios)
                    if ($item->purchasable_type === 'App\Models\Product') {
                        $product = Product::find($item->purchasable_id);
                        
                        if ($product) {
                            // Aumentar stock
                            $product->increment('stock_quantity', $item->quantity);

                            // Registrar movimiento en el Kardex
                            InventoryMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'reference_type' => get_class($order),
                                'reference_id' => $order->id,
                                'type' => 'Devolucion',
                                'quantity' => $item->quantity, // Positivo porque entra al almacén
                                'stock_after' => $product->stock_quantity,
                                'description' => "Devolución de Venta #{$order->id}"
                            ]);
                        }
                    }
                }

                // 2. Actualizar estado de la orden
                // Usamos shipping_status 'Devuelto' según tu migración
                $order->update([
                    'shipping_status' => 'Devuelto',
                    'notes' => $order->notes . " | [Sistema]: Orden devuelta por " . auth()->user()->name . " el " . now()->format('d/m/Y H:i'),
                    // Opcional: Si deseas marcarla como no pagada o dejar el registro financiero intacto, depende de tu lógica contable.
                    // $order->payment_status = '...'; 
                ]);
            });

            return back()->with('success', 'La orden ha sido devuelta y el stock restaurado.');

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al procesar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una orden (Soft Delete o permanente según modelo).
     * Nota: Esto normalmente es solo administrativo. La devolución es lo que ajusta inventario.
     */
    public function destroy(Order $order)
    {
        try {
            // Opcional: Podrías forzar una devolución antes de eliminar si no se ha hecho
            // if ($order->shipping_status !== 'Devuelto') { ... lógica de devolución ... }

            $order->delete();
            return back()->with('success', 'Venta eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la venta.');
        }
    }
}
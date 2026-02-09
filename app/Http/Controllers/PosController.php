<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\ClientLedger;
use App\Models\InventoryMovement; // Importamos el modelo
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    /**
     * Vista principal del Punto de Venta
     */
    public function index()
    {
        // 1. Verificar si el usuario tiene una caja abierta
        $activeSession = CashRegisterSession::where('user_id', Auth::id())
            ->where('status', 'Abierta')
            ->latest()
            ->first();

        // 2. Cargar catálogo (Productos y Paquetes) con IMÁGENES
        $products = Product::with('media')
            ->where('is_active', true)
            ->get()
            ->map(function ($p) {
                $img = $p->getFirstMediaUrl('product_image', 'thumb') ?: $p->getFirstMediaUrl();
                
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'sale_price' => (float)$p->sale_price,
                    'stock_quantity' => $p->stock_quantity,
                    'is_active' => $p->is_active,
                    'image_url' => $img ?: null,
                    'type' => 'product',
                    'class_type' => Product::class,
                    'pricing_options' => [], // Productos no tienen opciones de precio variables por ahora
                ];
            });

        $packages = ServicePackage::with('media')
            ->where('is_active', true)
            ->get()
            ->map(function ($p) {
                $img = $p->getFirstMediaUrl('package_images', 'thumb') ?: $p->getFirstMediaUrl();

                // Decodificar opciones de precio si existen (JSON)
                // Aseguramos que sea un array usable en JS
                $options = $p->pricing_options;
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }

                return [
                    'id' => $p->id,
                    'name' => $p->title,
                    'sku' => 'PKG-' . $p->id,
                    'sale_price' => (float)$p->price,
                    'stock_quantity' => 9999,
                    'is_active' => $p->is_active,
                    'image_url' => $img ?: null,
                    'type' => 'service',
                    'class_type' => ServicePackage::class,
                    'pricing_options' => $options ?? [], // Array de opciones
                ];
            });

        // Fusionamos para el buscador global
        $catalog = $products->concat($packages);

        // 3. Obtener Cajas disponibles (si no tiene sesión abierta)
        $availableRegisters = $activeSession ? [] : CashRegister::where('is_active', true)->get();

        return Inertia::render('Pos/Index', [
            'catalog' => $catalog,
            'activeSession' => $activeSession,
            'availableRegisters' => $availableRegisters,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Buscar clientes para el select (Autocompletado)
     */
    public function searchClients(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query) return [];

        return Client::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'current_balance' => (float)$client->current_balance,
                    'credit_limit' => (float)$client->credit_limit,
                    'available_credit' => max(0, $client->credit_limit - $client->current_balance),
                    // Dirección para autocompletar envío
                    'address' => [
                        'street' => $client->street,
                        'exterior_number' => $client->exterior_number,
                        'neighborhood' => $client->neighborhood,
                        'municipality' => $client->municipality,
                        'state' => $client->state,
                        'zip_code' => $client->zip_code,
                    ]
                ];
            });
    }

    /**
     * Crear cliente rápido desde el modal de cobro
     */
    public function storeQuickClient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'tax_id' => 'nullable|string'
        ]);

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'client' => $client
        ]);
    }

    /**
     * Abrir Caja Registradora
     */
    public function openRegister(Request $request)
    {
        $validated = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric|min:0'
        ]);

        CashRegisterSession::create([
            'cash_register_id' => $validated['cash_register_id'],
            'user_id' => Auth::id(),
            'opening_amount' => $validated['opening_amount'],
            'opened_at' => now(),
            'status' => 'Abierta'
        ]);

        return redirect()->back()->with('success', 'Caja abierta correctamente.');
    }

    /**
     * PROCESAR VENTA (STORE)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.class_type' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sale_price' => 'required|numeric',
            // 'items.*.name' => 'required|string', // Recibimos el nombre (que puede incluir la opción seleccionada)
            
            // Datos de Pago
            'payment_method' => 'required|in:Efectivo,Tarjeta,Transferencia,Crédito,Mixto',
            'amount_received' => 'required|numeric', 
            
            // Envío
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_address' => 'nullable|array', 
            'requires_shipping' => 'boolean',
            
            // Notas
            'notes' => 'nullable|string'
        ]);

        $session = CashRegisterSession::where('user_id', Auth::id())
            ->where('status', 'Abierta')
            ->first();

        if (!$session) {
            return back()->withErrors(['error' => 'No tienes una caja abierta.']);
        }

        try {
            DB::transaction(function () use ($validated, $session) {
                
                // 1. Cálculos de Totales y Stock
                $subtotal = 0;
                $orderId = null; // Lo necesitamos antes de crear items, pero InventoryMovement necesita Order ID.
                // Laravel transaction permite crear la orden y luego los items.

                // Calculamos totales primero para crear la orden
                foreach ($validated['items'] as $item) {
                    $subtotal += ($item['quantity'] * $item['sale_price']);
                }

                $shippingCost = $validated['requires_shipping'] ? ($validated['shipping_cost'] ?? 0) : 0;
                $totalAmount = $subtotal + $shippingCost;
                
                // Validar Crédito
                $paymentStatus = 'Pagado';
                if ($validated['payment_method'] === 'Crédito') {
                    $paymentStatus = 'Crédito';
                    if (!$validated['client_id']) {
                        throw new \Exception("Las ventas a crédito requieren un cliente registrado.");
                    }
                    $client = Client::find($validated['client_id']);
                    $newBalance = $client->current_balance + $totalAmount;
                    if ($client->credit_limit > 0 && $newBalance > $client->credit_limit) {
                        throw new \Exception("El cliente excede su límite de crédito.");
                    }
                }

                $shippingStatus = $validated['requires_shipping'] ? 'Pendiente' : 'Entregado';

                // 2. Crear Orden
                $order = Order::create([
                    'client_id' => $validated['client_id'],
                    'user_id' => Auth::id(),
                    'cash_register_session_id' => $session->id,
                    'total_amount' => $totalAmount,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $paymentStatus,
                    'shipping_status' => $shippingStatus, 
                    'shipping_address' => $validated['requires_shipping'] ? json_encode($validated['shipping_address']) : null,
                    'notes' => $validated['notes'],
                    'sold_at' => now(),
                ]);

                // 3. Procesar Items y Movimientos de Inventario
                foreach ($validated['items'] as $item) {
                    
                    // A. Crear Order Item
                    // Usamos el precio que viene del front (que puede ser de una opción específica)
                    OrderItem::create([
                        'order_id' => $order->id,
                        'purchasable_type' => $item['class_type'],
                        'purchasable_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['sale_price'],
                        // Podríamos guardar el nombre específico en alguna columna extra si fuera necesario,
                        // pero por ahora el modelo no lo tiene.
                    ]);

                    // B. Descontar Stock y Registrar Kardex (SOLO PRODUCTOS)
                    if ($item['class_type'] === Product::class) {
                        $product = Product::lockForUpdate()->find($item['id']);
                        
                        if ($product->stock_quantity < $item['quantity']) {
                            throw new \Exception("Stock insuficiente para: {$product->name}");
                        }
                        
                        $product->decrement('stock_quantity', $item['quantity']);

                        // --- REGISTRO DE MOVIMIENTO DE INVENTARIO ---
                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'reference_type' => Order::class,
                            'reference_id' => $order->id,
                            'type' => 'Venta',
                            'quantity' => -1 * abs($item['quantity']), // Cantidad negativa para salida
                            'stock_after' => $product->stock_quantity,
                            'description' => "Venta Folio #{$order->id}"
                        ]);
                    }
                }

                // 4. Manejo Financiero
                if ($validated['payment_method'] === 'Crédito') {
                    $client = Client::find($validated['client_id']);
                    $client->increment('current_balance', $totalAmount);

                    ClientLedger::create([
                        'client_id' => $client->id,
                        'user_id' => Auth::id(),
                        'reference_type' => Order::class,
                        'reference_id' => $order->id,
                        'type' => 'Cargo',
                        'amount' => $totalAmount,
                        'balance_after' => $client->current_balance,
                        'description' => "Compra Crédito Folio #{$order->id}"
                    ]);

                } else {
                    if ($validated['payment_method'] === 'Efectivo') {
                        CashMovement::create([
                            'cash_register_session_id' => $session->id,
                            'user_id' => Auth::id(),
                            'type' => 'Ingreso',
                            'amount' => $totalAmount,
                            'reason' => "Venta Folio #{$order->id}"
                        ]);
                    }
                }

            }); // Fin Transaction

            return redirect()->route('pos.index')->with('success', 'Venta registrada con éxito.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
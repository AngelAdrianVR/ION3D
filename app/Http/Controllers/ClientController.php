<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Muestra el listado de clientes con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('tax_id', 'like', "%{$search}%") // RFC o NIT
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'tax_id' => $client->tax_id,
                'municipality' => $client->municipality,
                'state' => $client->state,
                'current_balance' => (float) $client->current_balance, // Saldo deuda
                'credit_limit' => (float) $client->credit_limit,
                'status_label' => $client->deleted_at ? 'Inactivo' : 'Activo',
                'credit_usage_percent' => $client->credit_limit > 0 
                    ? round(($client->current_balance / $client->credit_limit) * 100) 
                    : 0,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Muestra el formulario para crear un cliente.
     */
    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    /**
     * Guarda el cliente en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            // Dirección
            'street' => 'nullable|string|max:255',
            'exterior_number' => 'nullable|string|max:20',
            'interior_number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Muestra detalles del cliente y su historial (Ledger).
     */
    public function show(Client $client)
    {
        // 1. Cargar historial financiero (Ledger)
        $ledger = $client->ledger()
            ->with('user:id,name')
            ->latest()
            ->limit(50) // Limitamos para rendimiento, o usar paginación si prefieres
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'date' => $entry->created_at->format('d/m/Y H:i'),
                    'type' => $entry->type, // 'Cargo' o 'Abono'
                    'amount' => $entry->amount,
                    'balance_after' => $entry->balance_after,
                    'description' => $entry->description,
                    'user_name' => $entry->user->name ?? 'Sistema',
                ];
            });

        // 2. Cargar historial de compras (Orders) con items
        $orders = $client->orders()
            ->with(['items.product', 'user:id,name'])
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'date' => $order->created_at->format('d/m/Y'),
                    'total' => $order->total_amount,
                    'status' => $order->payment_status, // 'Pagado', 'Pendiente', 'Crédito'
                    'items_count' => $order->items->count(),
                    'summary' => $order->items->take(3)->map(fn($item) => 
                        "{$item->quantity}x {$item->product->name}"
                    )->join(', ') . ($order->items->count() > 3 ? '...' : ''),
                ];
            });

        return Inertia::render('Clients/Show', [
            'client' => $client,
            'ledger' => $ledger,
            'orders' => $orders,
            'stats' => [
                'total_purchases' => $client->orders()->count(),
                'last_purchase' => $client->orders()->latest()->first()?->created_at?->diffForHumans() ?? 'Nunca',
            ]
        ]);
    }

    /**
     * Muestra formulario de edición.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Actualiza la información del cliente.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'street' => 'nullable|string|max:255',
            'exterior_number' => 'nullable|string|max:20',
            'interior_number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Información del cliente actualizada.');
    }

    /**
     * Eliminación (Soft Delete).
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
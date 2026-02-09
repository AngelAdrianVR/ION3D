<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientLedgerController extends Controller
{
    /**
     * Registra un pago (Abono) o cargo manual a la cuenta del cliente.
     */
    public function storePayment(Request $request, Client $client)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:Abono,Cargo', // Abono reduce deuda, Cargo aumenta
            'description' => 'nullable|string|max:255',
            'payment_method' => 'required|string', // Efectivo, Transferencia, etc.
        ]);

        try {
            DB::transaction(function () use ($request, $client) {
                $amount = $request->amount;
                $currentBalance = $client->current_balance;
                
                // Calcular nuevo saldo
                if ($request->type === 'Abono') {
                    $newBalance = $currentBalance - $amount;
                } else {
                    $newBalance = $currentBalance + $amount;
                }

                // 1. Crear registro en el historial (Ledger)
                ClientLedger::create([
                    'client_id' => $client->id,
                    'user_id' => Auth::id(), // Usuario autenticado
                    'type' => $request->type,
                    'amount' => $amount,
                    'balance_after' => $newBalance,
                    'description' => $request->description ?? ($request->type . ' manual'),
                    // 'reference_type' => ... podrías ligarlo a un modelo Payment si existiera
                ]);

                // 2. Actualizar saldo del cliente
                $client->update(['current_balance' => $newBalance]);
            });

            return redirect()->back()->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}
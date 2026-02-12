<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index()
    {
        $registers = CashRegister::orderBy('id', 'asc')->get();

        return Inertia::render('CashRegisters/Index', [
            'registers' => $registers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        CashRegister::create($validated);

        return redirect()->back()->with('success', 'Caja registradora creada correctamente.');
    }

    /**
     * Muestra el detalle de la caja y su historial de sesiones (Cortes).
     */
    public function show(Request $request, CashRegister $cashRegister)
    {
        // 1. Obtener filtros de fecha (Default: Mes actual)
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // 2. Obtener historial de sesiones (Cortes)
        $sessions = $cashRegister->sessions()
            ->with('user:id,name,profile_photo_path')
            ->whereYear('opened_at', $year)
            ->whereMonth('opened_at', $month)
            ->orderBy('opened_at', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'user_name' => $session->user->name,
                    'user_avatar' => $session->user->profile_photo_path,
                    'opened_at' => Carbon::parse($session->opened_at)->format('d/m/Y H:i'),
                    'closed_at' => $session->closed_at ? Carbon::parse($session->closed_at)->format('d/m/Y H:i') : null,
                    'status' => $session->status,
                    'opening_amount' => (float) $session->opening_amount,
                    'closing_amount' => (float) $session->closing_amount,
                    'calculated_amount' => (float) $session->calculated_amount,
                    'difference' => $session->closed_at ? ($session->closing_amount - $session->calculated_amount) : 0,
                    'notes' => $session->notes,
                    // Calcular duración
                    'duration' => $session->closed_at 
                        ? Carbon::parse($session->opened_at)->diffForHumans(Carbon::parse($session->closed_at), true)
                        : 'En curso'
                ];
            });

        // 3. Obtener sesión activa actual (si existe) independientemente de la fecha
        $activeSession = $cashRegister->sessions()
            ->with('user:id,name')
            ->where('status', 'Abierta')
            ->first();

        // 4. Estadísticas del mes seleccionado
        $stats = [
            'total_sessions' => $sessions->count(),
            'total_cash_in' => $sessions->sum('closing_amount'), // Total dinero reportado
            'discrepancies' => $sessions->where('status', 'Cerrada')->sum('difference'),
        ];

        return Inertia::render('CashRegisters/Show', [
            'register' => $cashRegister,
            'sessions' => $sessions,
            'activeSession' => $activeSession,
            'filters' => [
                'month' => (int)$month,
                'year' => (int)$year
            ],
            'stats' => $stats
        ]);
    }

    public function update(Request $request, CashRegister $cashRegister)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $cashRegister->update($validated);

        return redirect()->back()->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(CashRegister $cashRegister)
    {
        if ($cashRegister->id === 1) {
            return redirect()->back()->with('error', 'No se puede eliminar la Caja Principal.');
        }

        $cashRegister->delete();

        return redirect()->route('cash-registers.index')->with('success', 'Caja eliminada correctamente.');
    }
}
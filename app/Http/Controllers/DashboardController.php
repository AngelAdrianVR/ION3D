<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Product;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. KPI Cards (Estadísticas principales)
        $stats = [
            'daily_sales' => Order::whereDate('sold_at', $today)
                ->where('payment_status', '!=', 'Cancelada') // Asumiendo que agregaste estado o filtras por payment_status
                ->sum('total_amount'),
            
            'monthly_sales' => Order::whereDate('sold_at', '>=', $startOfMonth)
                ->sum('total_amount'),

            'pending_appointments' => Appointment::whereDate('start_time', '>=', $today)
                ->where('status', 'Pendiente')
                ->count(),

            'new_clients' => Client::whereDate('created_at', '>=', $startOfMonth)->count(),
        ];

        // 2. Órdenes Recientes (Últimas 5)
        $recentOrders = Order::with('client:id,name,email')
            ->latest('sold_at')
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'client_name' => $order->client ? $order->client->name : 'Venta de Mostrador',
                    'total' => $order->total_amount,
                    'status' => $order->payment_status, // Pagado, Pendiente, Parcial
                    'date' => Carbon::parse($order->sold_at)->diffForHumans(),
                ];
            });

        // 3. Citas de Hoy (Agenda)
        $todayAppointments = Appointment::with('client:id,name')
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get()
            ->map(function ($appt) {
                return [
                    'id' => $appt->id,
                    'client' => $appt->client ? $appt->client->name : $appt->guest_name,
                    'time' => Carbon::parse($appt->start_time)->format('H:i A'),
                    'status' => $appt->status,
                    'service' => 'Servicio General', // Podrías cargar la relación service_package si existe
                ];
            });

        // 4. Alertas de Inventario (Bajo stock)
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'alert_threshold')
            ->where('is_active', true)
            ->take(5)
            ->get(['id', 'name', 'stock_quantity', 'alert_threshold']);

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'todayAppointments' => $todayAppointments,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Obtener las últimas notificaciones para el dropdown (JSON).
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(15) // Limitamos a 15 para el dropdown
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marcar una notificación individual como leída.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas como leídas.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->back();
    }

    /**
     * Eliminar una notificación.
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Acciones masivas (Marcar seleccionados como leídos o eliminar).
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id',
            'action' => 'required|in:read,delete'
        ]);

        $query = auth()->user()->notifications()->whereIn('id', $request->ids);

        if ($request->action === 'read') {
            $query->get()->markAsRead();
            $message = 'Notificaciones marcadas como leídas.';
        } else {
            $query->delete();
            $message = 'Notificaciones eliminadas.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}
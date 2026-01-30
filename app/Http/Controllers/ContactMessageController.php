<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $messages = ContactMessage::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            })
            ->orderBy('is_read', 'asc') // Primero los no leídos
            ->orderBy('created_at', 'desc') // Luego los más recientes
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ContactMessages/Index', [
            'messages' => $messages,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validar
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        // 2. Crear registro
        $message = ContactMessage::create($validated);

        // 3. Notificar a los Super admins
        // Buscamos usuarios que tengan el rol 'Super admin'
        // Ajusta 'roles' y 'name' según tu estructura de base de datos de roles
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'Super admin');
        })->get();

        if ($admins->count() > 0) {
            // Usamos sendNow para forzar el envío inmediato y evitar problemas con colas (Queues)
            Notification::sendNow($admins, new NewContactMessageNotification($message));
        }

        // 4. Redirigir
        return Redirect::back()->with('success', 'Mensaje recibido correctamente.');
    }

    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);

        return Redirect::back()->with('success', 'Mensaje marcado como leído.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return Redirect::back()->with('success', 'Mensaje eliminado correctamente.');
    }
}
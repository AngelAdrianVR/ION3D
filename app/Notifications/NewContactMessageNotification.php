<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contactMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Solo base de datos como en tu ejemplo
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuevo Mensaje de Contacto',
            'type' => 'contact_message',
            'url' => route('contact-messages.index'),
            'message' => "Has recibido un nuevo mensaje de {$this->contactMessage->name}.",
            'icon' => 'fa-solid fa-envelope', // Icono de sobre
            'color' => 'text-blue-500', // Opcional: para darle color en el front si lo deseas
        ];
    }
}
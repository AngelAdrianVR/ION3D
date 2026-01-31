<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // Formateamos la fecha para que sea legible en la notificación
        $fecha = Carbon::parse($this->appointment->start_time)->format('d/m/Y H:i');

        return [
            'title' => 'Nueva Cita Agendada',
            'type' => 'appointment',
            'url' => route('appointments.index'), // Asumimos que esta ruta existe para ver el calendario
            'message' => "{$this->appointment->guest_name} ha agendado una cita para el {$fecha}.",
            'icon' => 'fa-solid fa-calendar-check', // Icono de calendario
            'color' => 'text-teal-500', // Color distintivo para citas
        ];
    }
}
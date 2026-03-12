<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectedNotification extends Notification
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Deine Registrierung bei Wollradar')
            ->greeting('Hallo ' . ($notifiable->name ?: '') . ',')
            ->line('vielen Dank für dein Interesse an Wollradar.')
            ->line('Leider können derzeit keine neuen Logins vergeben werden, daher konnten wir deine Registrierung im Moment nicht freigeben.')
            ->line('Falls sich daran etwas ändert, kannst du dich gern zu einem späteren Zeitpunkt erneut registrieren.');
    }
}

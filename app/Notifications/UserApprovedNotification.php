<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification
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
            ->subject('Dein Wollradar-Zugang wurde freigegeben')
            ->greeting('Hallo ' . ($notifiable->name ?: 'und willkommen') . ',')
            ->line('dein Zugang zu Wollradar wurde soeben freigegeben.')
            ->line('Du kannst dich jetzt mit deiner E-Mail-Adresse und deinem Passwort anmelden.')
            ->action('Jetzt anmelden', route('login'))
            ->line('Viel Freude mit Wollradar.');
    }
}

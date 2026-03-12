<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewUserPendingApproval extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $pendingUser
    ) {
    }

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
            ->subject('Neue Registrierung wartet auf Freigabe')
            ->greeting('Hallo ' . ($notifiable->name ?: 'Admin') . ',')
            ->line('ein neuer Benutzer hat sich bei Wollradar registriert und wartet auf Freigabe.')
            ->line('Name: ' . $this->pendingUser->name)
            ->line('E-Mail: ' . $this->pendingUser->email)
            ->action('Freigaben öffnen', route('admin.users.pending'))
            ->line('Bitte prüfe die Registrierung und gib den Benutzer bei Bedarf frei.');
    }
}

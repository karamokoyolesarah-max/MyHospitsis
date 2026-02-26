<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExternalDoctorValidationNotification extends Notification
{
    use Queueable;

    public $status; // 'approved' or 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage);

        if ($this->status === 'approved') {
            return $message
                ->subject('Votre compte HospitSIS est validé !')
                ->greeting('Félicitations Dr. ' . $notifiable->nom)
                ->line('Votre inscription a été validée par notre équipe.')
                ->line('Vous pouvez dès à présent vous connecter et commencer à utiliser la plateforme.')
                ->action('Se connecter', route('external.login'))
                ->line('Bienvenue dans la communauté HospitSIS.');
        } else {
            return $message
                ->subject('Mise à jour de votre inscription HospitSIS')
                ->greeting('Bonjour Dr. ' . $notifiable->nom)
                ->line('Après étude de votre dossier, nous ne pouvons malheureusement pas valider votre inscription pour le moment.')
                ->line('Cela peut être dû à des documents illisibles ou une impossibilité de vérifier vos informations.')
                ->line('Merci de contacter le support pour plus d\'informations.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

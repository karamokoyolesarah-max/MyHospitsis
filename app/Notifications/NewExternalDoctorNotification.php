<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewExternalDoctorNotification extends Notification
{
    use Queueable;

    public $doctor;

    /**
     * Create a new notification instance.
     */
    public function __construct($doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle Inscription Médecin Externe : Dr. ' . $this->doctor->nom)
            ->greeting('Bonjour Admin,')
            ->line('Un nouveau médecin externe vient de s\'inscrire et nécessite une validation.')
            ->line('**Nom :** Dr. ' . $this->doctor->prenom . ' ' . $this->doctor->nom)
            ->line('**Spécialité :** ' . $this->doctor->specialite)
            ->line('**Email :** ' . $this->doctor->email)
            ->line('**Affiliation :** ' . ($this->doctor->affiliation_type === 'hospital' ? 'Hôpital: ' . $this->doctor->affiliation_name : 'Superviseur: ' . $this->doctor->affiliation_name))
            ->action('Voir la demande', url('/admin-system/specialists'))
            ->line('Veuillez vérifier les documents et la vidéo d\'identité avant validation.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_external_doctor',
            'doctor_id' => $this->doctor->id,
            'doctor_name' => $this->doctor->nom . ' ' . $this->doctor->prenom,
            'message' => 'Nouveau médecin en attente de validation',
        ];
    }
}

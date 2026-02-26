<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class PaymentConfirmedNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $patientName;
    protected $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, string $patientName, float $amount)
    {
        $this->appointment = $appointment;
        $this->patientName = $patientName;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmed',
            'title' => 'Paiement confirmé',
            'message' => "Le patient {$this->patientName} a confirmé le paiement de " . number_format($this->amount, 0, ',', ' ') . " FCFA pour la consultation du " . $this->appointment->appointment_datetime->format('d/m/Y à H:i'),
            'appointment_id' => $this->appointment->id,
            'amount' => $this->amount,
            'patient_name' => $this->patientName,
            'icon' => 'credit-card',
            'color' => 'emerald',
            'action_url' => route('external.appointments'),
        ];
    }
}

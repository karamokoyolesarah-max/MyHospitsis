<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ExternalRechargeNotification extends Notification
{
    use Queueable;

    protected $rechargePayload;

    public function __construct(array $payload)
    {
        $this->rechargePayload = $payload;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouvelle demande de rechargement')
                    ->greeting('Bonjour')
                    ->line('Un médecin externe a soumis une demande de rechargement.')
                    ->line('Montant : ' . ($this->rechargePayload['amount'] ?? 'N/A') . ' FCFA')
                    ->line('Mode : ' . ($this->rechargePayload['payment_method'] ?? 'N/A'))
                    ->line('Numéro payeur : ' . ($this->rechargePayload['phone_number'] ?? 'N/A'))
                    ->action('Voir le rechargement', url('/superadmin'))
                    ->line('Merci.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'external_recharge',
            'recharge' => $this->rechargePayload,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => $this->toDatabase($notifiable),
        ]);
    }

    // Optional: placeholder for SMS sending (requires external provider)
    // public function toSms($notifiable)
    // {
    //     // Example: integrate with Nexmo/Twilio or other provider
    //     // return (new SmsMessage)->content('Nouvelle demande de rechargement: ' . ($this->rechargePayload['amount'] ?? '') . ' FCFA');
    // }
}

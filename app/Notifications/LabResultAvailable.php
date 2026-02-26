<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LabResultAvailable extends Notification
{
    use Queueable;

    public $labRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($labRequest)
    {
        $this->labRequest = $labRequest;
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
            'type' => 'lab_result',
            'title' => 'Résultats d\'analyse disponibles',
            'message' => "Les résultats pour {$this->labRequest->test_name} du patient {$this->labRequest->patient_name} sont disponibles.",
            'patient_name' => $this->labRequest->patient_name,
            'test_name' => $this->labRequest->test_name,
            'lab_request_id' => $this->labRequest->id,
            'patient_vital_id' => $this->labRequest->patient_vital_id,
            'action_url' => $this->getActionUrl(),
        ];
    }

    protected function getActionUrl()
    {
        if ($this->labRequest->patient_vital_id) {
            return route('medical-records.show', $this->labRequest->patient_vital_id);
        }

        // Fallback: Try to find patient by IPU and link to medical file
        $patient = \App\Models\Patient::where('ipu', $this->labRequest->patient_ipu)->first();
        if ($patient) {
            return route('patients.medical-file', $patient->id);
        }

        // Final fallback
        return route('dashboard');
    }
}

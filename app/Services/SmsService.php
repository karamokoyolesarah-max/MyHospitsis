<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = config('cinetpay.sms.provider', 'twilio');
        $this->config = config('cinetpay.sms.' . $this->provider, []);
    }

    /**
     * Send a recharge confirmation SMS
     * 
     * @param string $phoneNumber Phone number with country code
     * @param int $amount Amount recharged
     * @param int $newBalance New balance after recharge
     * @param int|null $activationFee Activation fee deducted (if any)
     * @return bool
     */
    public function sendRechargeConfirmation(
        string $phoneNumber,
        int $amount,
        int $newBalance,
        ?int $activationFee = null
    ): bool {
        $message = "HospitSIS: Votre rechargement de " . number_format($amount, 0, ',', ' ') . " FCFA a été confirmé.";
        
        if ($activationFee) {
            $message .= " Frais d'activation: " . number_format($activationFee, 0, ',', ' ') . " FCFA.";
        }
        
        $message .= " Nouveau solde: " . number_format($newBalance, 0, ',', ' ') . " FCFA.";

        return $this->send($phoneNumber, $message);
    }

    /**
     * Send account activation alert
     * 
     * @param string $phoneNumber
     * @param string $expirationDate
     * @return bool
     */
    public function sendActivationAlert(string $phoneNumber, string $expirationDate): bool
    {
        $message = "HospitSIS: Votre compte est maintenant actif jusqu'au {$expirationDate}. Vous pouvez recevoir des rendez-vous.";

        return $this->send($phoneNumber, $message);
    }

    /**
     * Send payment failure notification
     * 
     * @param string $phoneNumber
     * @param string $reason
     * @return bool
     */
    public function sendPaymentFailure(string $phoneNumber, string $reason = ''): bool
    {
        $message = "HospitSIS: Votre rechargement a échoué.";
        if ($reason) {
            $message .= " Raison: {$reason}.";
        }
        $message .= " Veuillez réessayer.";

        return $this->send($phoneNumber, $message);
    }

    /**
     * Send a generic SMS
     * 
     * @param string $to Phone number
     * @param string $message SMS content
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        if (empty($this->provider) || $this->provider === 'null') {
            Log::info('SMS: Provider disabled, message not sent', ['to' => $to, 'message' => $message]);
            return true; // Silently succeed if SMS is disabled
        }

        try {
            return match ($this->provider) {
                'twilio' => $this->sendViaTwilio($to, $message),
                'infobip' => $this->sendViaInfobip($to, $message),
                default => $this->logOnly($to, $message),
            };
        } catch (\Exception $e) {
            Log::error('SMS: Failed to send', [
                'provider' => $this->provider,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $to, string $message): bool
    {
        $sid = $this->config['sid'] ?? '';
        $token = $this->config['token'] ?? '';
        $from = $this->config['from'] ?? '';

        if (empty($sid) || empty($token) || empty($from)) {
            Log::warning('SMS: Twilio not configured properly');
            return false;
        }

        $to = $this->formatPhoneNumber($to);

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'To' => $to,
                'From' => $from,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            Log::info('SMS: Sent via Twilio', ['to' => $to, 'sid' => $response->json('sid')]);
            return true;
        }

        Log::error('SMS: Twilio error', ['to' => $to, 'response' => $response->json()]);
        return false;
    }

    /**
     * Send SMS via Infobip
     */
    protected function sendViaInfobip(string $to, string $message): bool
    {
        $apiKey = $this->config['api_key'] ?? '';
        $baseUrl = $this->config['base_url'] ?? '';
        $from = $this->config['from'] ?? 'HospitSIS';

        if (empty($apiKey) || empty($baseUrl)) {
            Log::warning('SMS: Infobip not configured properly');
            return false;
        }

        $to = $this->formatPhoneNumber($to);

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/sms/2/text/advanced", [
            'messages' => [
                [
                    'from' => $from,
                    'destinations' => [['to' => $to]],
                    'text' => $message,
                ],
            ],
        ]);

        if ($response->successful()) {
            Log::info('SMS: Sent via Infobip', ['to' => $to]);
            return true;
        }

        Log::error('SMS: Infobip error', ['to' => $to, 'response' => $response->json()]);
        return false;
    }

    /**
     * Just log the message (for testing/development)
     */
    protected function logOnly(string $to, string $message): bool
    {
        Log::info('SMS: [LOG ONLY]', ['to' => $to, 'message' => $message]);
        return true;
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces and special characters
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If starts with 0, assume Côte d'Ivoire (+225)
        if (str_starts_with($phone, '0') && strlen($phone) == 10) {
            $phone = '+225' . substr($phone, 1);
        }

        // Ensure + prefix
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}

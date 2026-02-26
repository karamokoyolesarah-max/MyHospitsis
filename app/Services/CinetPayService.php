<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ExternalDoctorRecharge;

class CinetPayService
{
    protected string $siteId;
    protected string $apiKey;
    protected string $webhookSecret;
    protected string $baseUrl;
    protected string $currency;
    protected bool $testMode;

    public function __construct()
    {
        $this->siteId = config('cinetpay.site_id');
        $this->apiKey = config('cinetpay.api_key');
        $this->webhookSecret = config('cinetpay.webhook_secret');
        $this->baseUrl = config('cinetpay.base_url');
        $this->currency = config('cinetpay.currency', 'XOF');
        $this->testMode = config('cinetpay.test_mode', true);
    }

    /**
     * Initiate a payment with CinetPay
     * 
     * @param int $amount Amount in FCFA
     * @param string $transactionRef Unique transaction reference
     * @param array $metadata Additional data to store
     * @param string $notifyUrl Webhook URL for notifications
     * @param string $returnUrl URL to redirect user after payment
     * @return array ['success' => bool, 'payment_url' => string|null, 'transaction_id' => string|null, 'error' => string|null]
     */
    public function initiatePayment(
        int $amount,
        string $transactionRef,
        array $metadata = [],
        string $notifyUrl = null,
        string $returnUrl = null
    ): array {
        try {
            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionRef,
                'amount' => $amount,
                'currency' => $this->currency,
                'description' => 'Rechargement compte HospitSIS',
                'notify_url' => $notifyUrl ?? route('webhook.recharge'),
                'return_url' => $returnUrl ?? route('external.recharge.callback'),
                'channels' => 'ALL',
                'metadata' => json_encode($metadata),
                // Customer info (optional but recommended)
                'customer_name' => $metadata['customer_name'] ?? 'Client',
                'customer_surname' => $metadata['customer_surname'] ?? '',
                'customer_email' => $metadata['customer_email'] ?? '',
                'customer_phone_number' => $metadata['customer_phone'] ?? '',
                'customer_address' => $metadata['customer_address'] ?? '',
                'customer_city' => $metadata['customer_city'] ?? 'Abidjan',
                'customer_country' => $metadata['customer_country'] ?? 'CI',
            ];

            Log::info('CinetPay: Initiating payment', ['ref' => $transactionRef, 'amount' => $amount]);

            $response = Http::timeout(30)
                ->when(app()->isLocal(), fn ($h) => $h->withoutVerifying())
                ->post($this->baseUrl . '/payment', $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['data']['payment_url'])) {
                Log::info('CinetPay: Payment initiated successfully', [
                    'ref' => $transactionRef,
                    'payment_token' => $data['data']['payment_token'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment_url' => $data['data']['payment_url'],
                    'payment_token' => $data['data']['payment_token'] ?? null,
                    'error' => null,
                ];
            }

            Log::error('CinetPay: Failed to initiate payment', [
                'ref' => $transactionRef,
                'response' => $data,
            ]);

            return [
                'success' => false,
                'payment_url' => null,
                'payment_token' => null,
                'error' => $data['message'] ?? 'Erreur lors de l\'initialisation du paiement',
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay: Exception during payment initiation', [
                'ref' => $transactionRef,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'payment_url' => null,
                'payment_token' => null,
                'error' => 'Erreur de connexion au service de paiement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check the status of a transaction
     * 
     * @param string $transactionId
     * @return array ['success' => bool, 'status' => string|null, 'data' => array|null, 'error' => string|null]
     */
    public function checkTransaction(string $transactionId): array
    {
        try {
            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId,
            ];

            $response = Http::timeout(30)
                ->when(app()->isLocal(), fn ($h) => $h->withoutVerifying())
                ->post(config('cinetpay.check_url'), $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['data'])) {
                $status = $data['data']['status'] ?? 'UNKNOWN';
                
                return [
                    'success' => true,
                    'status' => $this->mapStatus($status),
                    'cinetpay_status' => $status,
                    'data' => $data['data'],
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'status' => null,
                'data' => null,
                'error' => $data['message'] ?? 'Impossible de vérifier la transaction',
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay: Exception during transaction check', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => null,
                'data' => null,
                'error' => 'Erreur de connexion: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate webhook signature (HMAC-SHA256)
     * 
     * @param string $payload Raw JSON payload
     * @param string $signature Signature from X-CINETPAY-SIGNATURE header
     * @return bool
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            Log::warning('CinetPay: Webhook secret not configured, skipping signature validation');
            return true; // Skip validation if not configured (not recommended in production)
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Map CinetPay status to internal status
     */
    protected function mapStatus(string $cinetpayStatus): string
    {
        return match (strtoupper($cinetpayStatus)) {
            'ACCEPTED', 'SUCCESS' => 'completed',
            'REFUSED', 'CANCELLED', 'FAILED' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Generate a unique transaction reference
     */
    public static function generateTransactionRef(string $prefix = 'RCH'): string
    {
        return $prefix . date('YmdHis') . strtoupper(substr(uniqid(), -4));
    }
}

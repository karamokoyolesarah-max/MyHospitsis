<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ExternalDoctorRecharge;
use App\Models\MedecinExterne;
use App\Models\TransactionLog;
use App\Models\CommissionRate;
use App\Services\CinetPayService;
use App\Services\SmsService;

class RechargeWebhookController extends Controller
{
    protected CinetPayService $cinetpay;
    protected SmsService $sms;

    public function __construct(CinetPayService $cinetpay, SmsService $sms)
    {
        $this->cinetpay = $cinetpay;
        $this->sms = $sms;
    }

    /**
     * Handle CinetPay webhook for recharges
     */
    public function handle(Request $request)
    {
        Log::info('Webhook received', ['payload' => $request->all()]);

        // Get raw payload for signature validation
        $rawPayload = $request->getContent();
        $signature = $request->header('X-CINETPAY-SIGNATURE', '');

        // Validate signature
        if (!$this->cinetpay->validateWebhookSignature($rawPayload, $signature)) {
            Log::warning('Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Parse payload
        $data = $request->all();
        $transactionId = $data['cpm_trans_id'] ?? $data['transaction_id'] ?? null;
        $status = strtoupper($data['cpm_result'] ?? $data['status'] ?? 'UNKNOWN');

        if (!$transactionId) {
            Log::error('Webhook: Missing transaction ID');
            return response()->json(['error' => 'Missing transaction ID'], 400);
        }

        // Find the recharge record
        $recharge = ExternalDoctorRecharge::where('transaction_id', $transactionId)
            ->orWhere('cinetpay_transaction_id', $transactionId)
            ->first();

        if (!$recharge) {
            Log::error('Webhook: Recharge not found', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Recharge not found'], 404);
        }

        // Already processed?
        if ($recharge->status === 'completed') {
            Log::info('Webhook: Recharge already completed', ['id' => $recharge->id]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Store response
        $recharge->cinetpay_response = $data;
        $recharge->save();

        // Process based on status
        if (in_array($status, ['ACCEPTED', 'SUCCESS', '00'])) {
            return $this->handleSuccess($recharge, $data);
        } else {
            return $this->handleFailure($recharge, $data, $status);
        }
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccess(ExternalDoctorRecharge $recharge, array $data)
    {
        DB::beginTransaction();

        try {
            $user = MedecinExterne::find($recharge->medecin_externe_id);
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            // 1. Update recharge status
            $recharge->status = 'completed';
            $recharge->cinetpay_transaction_id = $data['cpm_trans_id'] ?? $data['transaction_id'] ?? null;
            $recharge->save();

            // 2. Credit balance
            $user->balance += $recharge->amount;
            
            // 3. Handle activation fee if needed
            $message = 'Rechargement confirmé';
            $activationFee = 0;
            
            $activeRate = CommissionRate::where('is_active', true)->first();
            $defaultActivationFee = $activeRate ? $activeRate->activation_fee : 4000;

            $isExpired = !$user->plan_expires_at || $user->plan_expires_at->isPast();

            if ($isExpired && $user->balance >= $defaultActivationFee) {
                $activationFee = $defaultActivationFee;
                $user->balance -= $activationFee;
                $user->plan_expires_at = now()->addDays(30);

                TransactionLog::create([
                    'source_type' => 'specialist',
                    'source_id' => $user->id,
                    'amount' => $recharge->amount,
                    'fee_applied' => $activationFee,
                    'net_income' => $activationFee,
                    'description' => "FRAIS_ACTIVATION: Activation mensuelle via CinetPay",
                ]);
            } else {
                TransactionLog::create([
                    'source_type' => 'specialist',
                    'source_id' => $user->id,
                    'amount' => $recharge->amount,
                    'fee_applied' => 0,
                    'net_income' => 0,
                    'description' => "Rechargement solde via CinetPay",
                ]);
            }

            $user->save();

            // 4. Send SMS confirmation
            try {
                $this->sms->sendRechargeConfirmation(
                    $recharge->phone_number,
                    (int) $recharge->amount,
                    (int) $user->balance,
                    $activationFee > 0 ? $activationFee : null
                );
                $recharge->sms_sent_at = now();
                $recharge->save();
            } catch (\Exception $e) {
                Log::error('Webhook: SMS failed', ['error' => $e->getMessage()]);
            }

            DB::commit();

            Log::info('Webhook: Payment processed successfully', [
                'recharge_id' => $recharge->id,
                'amount' => $recharge->amount,
                'new_balance' => $user->balance,
            ]);

            return response()->json(['message' => 'Success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook: Processing failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle failed payment
     */
    protected function handleFailure(ExternalDoctorRecharge $recharge, array $data, string $status)
    {
        $reason = $data['cpm_error_message'] ?? $data['message'] ?? "Statut: {$status}";

        $recharge->status = 'failed';
        $recharge->failure_reason = $reason;
        $recharge->save();

        // Send failure SMS
        try {
            $this->sms->sendPaymentFailure($recharge->phone_number, $reason);
            $recharge->sms_sent_at = now();
            $recharge->save();
        } catch (\Exception $e) {
            Log::error('Webhook: Failure SMS failed', ['error' => $e->getMessage()]);
        }

        Log::info('Webhook: Payment failed', [
            'recharge_id' => $recharge->id,
            'reason' => $reason,
        ]);

        return response()->json(['message' => 'Failure recorded'], 200);
    }
}

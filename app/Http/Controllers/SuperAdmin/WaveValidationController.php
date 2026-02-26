<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ExternalDoctorRecharge;
use App\Models\MedecinExterne;
use App\Models\TransactionLog;
use App\Models\CommissionRate;
use App\Services\SmsService;

class WaveValidationController extends Controller
{
    protected SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Display pending Wave recharges for validation
     */
    public function index()
    {
        $pendingRecharges = ExternalDoctorRecharge::where('requires_manual_validation', true)
            ->where('status', 'pending')
            ->with('medecinExterne')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $recentValidated = ExternalDoctorRecharge::where('requires_manual_validation', true)
            ->whereIn('status', ['completed', 'failed'])
            ->with(['medecinExterne'])
            ->orderBy('validated_at', 'desc')
            ->take(10)
            ->get();

        return redirect()->route('superadmin.dashboard', ['tab' => 'wave-validation']);
    }

    /**
     * Approve a Wave recharge
     */
    public function approve(Request $request, ExternalDoctorRecharge $recharge)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        if ($recharge->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette recharge a déjà été traitée.');
        }

        DB::beginTransaction();

        try {
            $user = MedecinExterne::find($recharge->medecin_externe_id);
            
            if (!$user) {
                throw new \Exception('Médecin non trouvé');
            }

            // 1. Update recharge status
            $recharge->status = 'completed';
            $recharge->validated_by = Auth::guard('superadmin')->id();
            $recharge->validated_at = now();
            $recharge->validation_notes = $request->notes;
            $recharge->save();

            // 2. Credit balance
            $user->balance += $recharge->amount;
            
            // 3. Handle activation fee if needed
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
                    'description' => "FRAIS_ACTIVATION: Activation mensuelle via Wave (validation manuelle)",
                ]);
            } else {
                TransactionLog::create([
                    'source_type' => 'specialist',
                    'source_id' => $user->id,
                    'amount' => $recharge->amount,
                    'fee_applied' => 0,
                    'net_income' => 0,
                    'description' => "Rechargement solde via Wave (validation manuelle)",
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
                \Log::error('Wave validation: SMS failed', ['error' => $e->getMessage()]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Rechargement Wave validé avec succès ! ' . number_format($recharge->amount) . ' FCFA crédités.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Wave validation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Reject a Wave recharge
     */
    public function reject(Request $request, ExternalDoctorRecharge $recharge)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($recharge->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette recharge a déjà été traitée.');
        }

        $recharge->status = 'failed';
        $recharge->failure_reason = $request->rejection_reason;
        $recharge->validated_by = Auth::guard('superadmin')->id();
        $recharge->validated_at = now();
        $recharge->validation_notes = $request->rejection_reason;
        $recharge->save();

        // Send rejection SMS
        try {
            $this->sms->sendPaymentFailure($recharge->phone_number, $request->rejection_reason);
            $recharge->sms_sent_at = now();
            $recharge->save();
        } catch (\Exception $e) {
            \Log::error('Wave rejection: SMS failed', ['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('info', 'Rechargement Wave rejeté.');
    }
}

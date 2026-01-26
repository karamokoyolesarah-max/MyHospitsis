<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Hospital;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\SubscriptionPlan;
use App\Models\CommissionRate;
use App\Models\CommissionBracket;
use App\Models\MedecinExterne;
use App\Models\SpecialistWallet;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    // === AUTHENTICATION METHODS ===
    
    public function showLoginForm()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect to verification page
            return redirect()->route('superadmin.verify');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis sont incorrects.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('superadmin.login');
    }

    // === VERIFICATION METHODS ===
    
    public function showVerifyForm()
    {
        // Vérifier que l'utilisateur est bien connecté avec le guard superadmin
        if (!Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.login')
                ->withErrors(['error' => 'Vous devez d\'abord vous connecter.']);
        }
        
        return view('superadmin.verify-code');
    }

    public function verifyCode(Request $request)
    {
        // Trim the access code to remove any spaces
        $request->merge(['access_code' => trim($request->access_code)]);

        $request->validate([
            'access_code' => 'required|string|size:8'
        ]);

        // Get the authenticated superadmin
        $superadmin = auth()->guard('superadmin')->user();

        if (!$superadmin) {
            return redirect()->route('superadmin.login')
                ->withErrors(['error' => 'Session expirée. Veuillez vous reconnecter.']);
        }

        // Trim et comparer les codes (insensible à la casse)
        $inputCode = trim(strtoupper($request->access_code));
        $storedCode = trim(strtoupper($superadmin->access_code));

        // Debug logging
        \Log::info('SuperAdmin Verification Debug', [
            'input_code' => $inputCode,
            'stored_code' => $storedCode,
            'input_length' => strlen($inputCode),
            'stored_length' => strlen($storedCode),
            'superadmin_id' => $superadmin->id,
            'match' => $inputCode === $storedCode
        ]);

        if ($inputCode === $storedCode) {
            // Set session to mark as verified
            session(['superadmin_verified' => true]);

            return redirect()->route('superadmin.dashboard')
                ->with('success', 'Code vérifié avec succès. Bienvenue dans le panneau Super Admin.');
        }

        return back()->withErrors(['access_code' => 'Code secret incorrect.']);
    }

    // === DASHBOARD ===
    
    public function dashboard()
    {
        // On récupère tous les hôpitaux pour les afficher sur le dashboard
        $hospitals = Hospital::withCount('users')->get();

        // Récupérer tous les spécialistes
        $allSpecialists = MedecinExterne::orderBy('created_at', 'desc')->get();
        $pendingSpecialists = $allSpecialists->where('statut', 'inactif');

        // Statistiques dynamiques pour le dashboard
        $stats = [
            'active_hospitals' => $hospitals->where('is_active', true)->count(),
            'total_users' => $hospitals->sum('users_count'),
            'total_patients' => \App\Models\Patient::count(),
            'pending_validations' => $pendingSpecialists->count(),
            'total_saas_revenue' => (float) TransactionLog::sum('net_income'),
            'total_commissions' => (float) TransactionLog::where('description', 'like', '%commission%')->sum('net_income'),
            'monthly_saas_revenue' => (float) TransactionLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('net_income'),
            'monthly_commissions' => (float) TransactionLog::where('description', 'like', '%commission%')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('net_income'),
            'activation_fee' => 4000, 
            'average_commission' => 15,
        ];

        return view('superadmin.dashboard', compact('hospitals', 'stats', 'allSpecialists', 'pendingSpecialists'));
    }

    // === HOSPITAL MANAGEMENT ===
    
    public function storeHospital(Request $request)
    {
        // Validation stricte
        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'hospital_address' => 'required|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Création de l'Hôpital
                $hospital = Hospital::create([
                    'name' => $request->hospital_name,
                    'slug' => Str::slug($request->hospital_name),
                    'address' => $request->hospital_address,
                    'is_active' => true,
                ]);

                // 2. Création du compte Administrateur de cet hôpital
                User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => Hash::make($request->admin_password),
                    'role' => 'admin',
                    'hospital_id' => $hospital->id,
                    'is_active' => true
                ]);
            });

            return redirect()->back()->with('success', 'L\'hôpital et son administrateur ont été créés avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function getHospitalDetails($hospitalId)
    {
        try {
            $hospital = Hospital::with([
                'users.service',
                'services.users',
                'services.prestations',
                'prestations.service'
            ])->findOrFail($hospitalId);

            // Calculer les statistiques
            $stats = [
                'total_users' => $hospital->users->count(),
                'total_services' => $hospital->services->count(),
                'total_prestations' => $hospital->prestations->count(),
                'active_users' => $hospital->users->where('is_active', true)->count(),
            ];

            // Ajouter une catégorie par défaut aux prestations si elles n'en ont pas
            $hospital->prestations->each(function ($prestation) {
                if (!$prestation->category) {
                    $prestation->category = 'general';
                }
            });

            return response()->json([
                'success' => true,
                'hospital' => $hospital,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de l\'hôpital: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleHospitalStatus(Request $request, $hospitalId)
    {
        try {
            $hospital = Hospital::findOrFail($hospitalId);
            $hospital->is_active = $request->boolean('is_active');
            $hospital->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut de l\'hôpital mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    public function validateSpecialist(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        try {
            $specialist = MedecinExterne::findOrFail($id);

            if ($request->action === 'approve') {
                $specialist->statut = 'actif';
                $specialist->save();

                // Initialize wallet if it doesn't exist
                SpecialistWallet::firstOrCreate(
                    ['specialist_id' => $specialist->id],
                    ['balance' => 0, 'is_activated' => false, 'is_blocked' => false]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Spécialiste validé avec succès.'
                ]);
            } else {
                // For rejection, we might want to delete or keep as inactive
                // For now, let's just keep it inactive or maybe delete it
                // $specialist->delete(); 
                return response()->json([
                    'success' => true,
                    'message' => 'Spécialiste rejeté.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    // === WALLET MANAGEMENT ===
    
    public function blockSpecialistWallet(Request $request, $specialistId)
    {
        try {
            $wallet = SpecialistWallet::where('specialist_id', $specialistId)->firstOrFail();
            $wallet->is_blocked = true;
            $wallet->save();

            TransactionLog::create([
                'source_type' => 'specialist',
                'source_id' => $specialistId,
                'amount' => 0,
                'fee_applied' => 0,
                'net_income' => 0,
                'description' => 'Portefeuille bloqué par Super Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Portefeuille bloqué avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du blocage du portefeuille'
            ], 500);
        }
    }

    public function unblockSpecialistWallet(Request $request, $specialistId)
    {
        try {
            $wallet = SpecialistWallet::where('specialist_id', $specialistId)->firstOrFail();
            $wallet->is_blocked = false;
            $wallet->save();

            TransactionLog::create([
                'source_type' => 'specialist',
                'source_id' => $specialistId,
                'amount' => 0,
                'fee_applied' => 0,
                'net_income' => 0,
                'description' => 'Portefeuille débloqué par Super Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Portefeuille débloqué avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déblocage du portefeuille'
            ], 500);
        }
    }

    public function adjustSpecialistBalance(Request $request, $specialistId)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        try {
            $wallet = SpecialistWallet::where('specialist_id', $specialistId)->firstOrFail();
            $wallet->balance += $request->amount;
            $wallet->save();

            TransactionLog::create([
                'source_type' => 'specialist',
                'source_id' => $specialistId,
                'amount' => $request->amount,
                'fee_applied' => 0,
                'net_income' => $request->amount > 0 ? $request->amount : 0,
                'description' => 'Ajustement manuel du solde par Super Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solde ajusté avec succès',
                'new_balance' => $wallet->balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajustement du solde'
            ], 500);
        }
    }

    // === COMMISSION DEDUCTION ===
    
    public function deductCommission(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:medecins_externes,id',
            'service_type' => 'required|string',
            'service_amount' => 'required|numeric|min:0'
        ]);

        try {
            // Calculate commission based on fixed tiers
            $commissionPercentage = $this->calculateCommissionPercentage($request->service_amount);
            $commissionAmount = ($request->service_amount * $commissionPercentage) / 100;

            $wallet = SpecialistWallet::where('specialist_id', $request->specialist_id)->first();

            if (!$wallet || !$wallet->is_activated || $wallet->is_blocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Portefeuille non activé ou bloqué'
                ], 400);
            }

            if ($wallet->balance < $commissionAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant pour la commission'
                ], 400);
            }

            $wallet->balance -= $commissionAmount;
            $wallet->save();

            TransactionLog::create([
                'source_type' => 'specialist',
                'source_id' => $request->specialist_id,
                'amount' => -$commissionAmount,
                'fee_applied' => $commissionPercentage,
                'net_income' => $commissionAmount,
                'description' => "Commission {$commissionPercentage}% sur acte de {$request->service_amount} FCFA"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commission prélevée avec succès',
                'commission_amount' => $commissionAmount,
                'remaining_balance' => $wallet->balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du prélèvement de la commission'
            ], 500);
        }
    }

    private function calculateCommissionPercentage($amount)
    {
        if ($amount >= 50000) {
            return 35;
        } elseif ($amount >= 26000) {
            return 20;
        } elseif ($amount >= 5000) {
            return 10;
        } else {
            return 0;
        }
    }

    // === SPECIALIST ACTIVATION ===
    
    public function processSpecialistActivation(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:medecins_externes,id',
            'payment_amount' => 'required|numeric|min:10000'
        ]);

        try {
            return $this->repartirPaiement($request->payment_amount, $request->specialist_id, 'activation');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement d\'activation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testSpecialistRecharge(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:medecins_externes,id',
        ]);

        try {
            $testAmount = 10000;
            return $this->repartirPaiement($testAmount, $request->specialist_id, 'activation');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la simulation de recharge: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTestSpecialists()
    {
        try {
            $specialists = MedecinExterne::with('wallet')->get()->map(function ($specialist) {
                $wallet = $specialist->wallet;
                return [
                    'id' => $specialist->id,
                    'name' => trim(($specialist->prenom ?? '') . ' ' . ($specialist->nom ?? '')) ?: 'Spécialiste inconnu',
                    'specialty' => $specialist->specialite ?? 'Non spécifiée',
                    'balance' => $wallet ? $wallet->balance : 0,
                    'is_activated' => $wallet ? $wallet->is_activated : false,
                    'is_blocked' => $wallet ? $wallet->is_blocked : false,
                    'status' => $wallet && $wallet->is_activated && !$wallet->is_blocked ? 'ACTIF' : 'INACTIF',
                ];
            });

            return response()->json([
                'success' => true,
                'specialists' => $specialists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des spécialistes: ' . $e->getMessage()
            ], 500);
        }
    }

    private function repartirPaiement($montant, $specialistId, $type = 'consultation')
    {
        DB::transaction(function () use ($montant, $specialistId, $type) {
            if ($type === 'activation') {
                $adminAmount = 4000;
                $walletAmount = 6000;

                TransactionLog::create([
                    'source_type' => 'hospital',
                    'source_id' => 0,
                    'amount' => $adminAmount,
                    'fee_applied' => 40,
                    'net_income' => $adminAmount,
                    'description' => 'Frais d\'activation spécialiste - 4,000 FCFA'
                ]);

                $wallet = SpecialistWallet::firstOrCreate(
                    ['specialist_id' => $specialistId],
                    ['balance' => 0, 'is_activated' => true, 'is_blocked' => false]
                );
                $wallet->balance += $walletAmount;
                $wallet->is_activated = true;
                $wallet->activated_at = now();
                $wallet->save();

                TransactionLog::create([
                    'source_type' => 'specialist',
                    'source_id' => $specialistId,
                    'amount' => $walletAmount,
                    'fee_applied' => 0,
                    'net_income' => 0,
                    'description' => 'Recharge initiale portefeuille - 6,000 FCFA'
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Paiement traité avec succès'
        ]);
    }

    // === SUBSCRIPTION PLANS ===
    
    public function getSubscriptionPlans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return response()->json(['plans' => $plans]);
    }

    public function storeSubscriptionPlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_type' => 'required|in:hopital_physique,clinique_privee',
            'price' => 'required|numeric|min:0',
            'duration_unit' => 'required|in:month,year',
            'duration_value' => 'required|integer|min:1',
            'features' => 'required|array',
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'target_type' => $request->target_type,
            'price' => $request->price,
            'duration_unit' => $request->duration_unit,
            'duration_value' => $request->duration_value,
            'features' => $request->features,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'message' => 'Plan d\'abonnement créé avec succès'
        ]);
    }

    public function updateSubscriptionPlan(Request $request, $planId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_unit' => 'required|in:month,year',
            'duration_value' => 'required|integer|min:1',
            'features' => 'required|array',
        ]);

        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_unit' => $request->duration_unit,
            'duration_value' => $request->duration_value,
            'features' => $request->features,
        ]);

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'message' => 'Plan d\'abonnement mis à jour avec succès'
        ]);
    }

    public function deleteSubscriptionPlan($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan d\'abonnement supprimé avec succès'
        ]);
    }

    // === COMMISSION RATES ===
    
    public function getCommissionRates()
    {
        $rates = CommissionRate::with('brackets')->where('is_active', true)->get()->map(function ($rate) {
            $firstBracket = $rate->brackets->first();
            $bracketCount = $rate->brackets->count();

            return [
                'id' => $rate->id,
                'service_type' => 'Commissions par tranches de prix',
                'activation_fee' => $rate->activation_fee,
                'commission_percentage' => $firstBracket ? $firstBracket->percentage . '%' : '0%',
                'brackets_summary' => $rate->brackets->map(function ($bracket) {
                    if ($bracket->max_price) {
                        return number_format($bracket->min_price) . ' - ' . number_format($bracket->max_price) . ' FCFA → ' . $bracket->percentage . '%';
                    } else {
                        return number_format($bracket->min_price) . '+ FCFA → ' . $bracket->percentage . '%';
                    }
                })->join(' | '),
                'bracket_count' => $bracketCount,
                'is_active' => $rate->is_active,
                'created_at' => $rate->created_at,
                'brackets' => $rate->brackets
            ];
        });

        return response()->json(['rates' => $rates]);
    }

    public function showCommissionRate($rateId)
    {
        $rate = CommissionRate::with('brackets')->find($rateId);

        if (!$rate) {
            return response()->json(['success' => false, 'message' => 'Règle non trouvée'], 404);
        }

        return response()->json([
            'success' => true,
            'rate' => [
                'id' => $rate->id,
                'service_type' => 'Commissions par tranches de prix',
                'activation_fee' => $rate->activation_fee,
                'is_active' => $rate->is_active,
                'brackets' => $rate->brackets->map(function ($bracket) {
                    return [
                        'id' => $bracket->id,
                        'min_price' => $bracket->min_price,
                        'max_price' => $bracket->max_price,
                        'percentage' => $bracket->percentage,
                        'order' => $bracket->order
                    ];
                })->sortBy('order')->values()
            ]
        ]);
    }

    public function storeCommissionRate(Request $request)
    {
        $request->validate([
            'brackets' => 'required|array|min:1',
            'brackets.*.min_price' => 'required|numeric|min:0',
            'brackets.*.max_price' => 'nullable|numeric|min:0',
            'brackets.*.percentage' => 'required|numeric|min:0|max:100',
            'brackets.*.order' => 'required|integer|min:1',
            'activation_fee' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $rate = CommissionRate::create([
                    'service_type' => 'price_based_commissions',
                    'activation_fee' => $request->activation_fee,
                    'commission_percentage' => 0,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                foreach ($request->brackets as $bracketData) {
                    $rate->brackets()->create([
                        'min_price' => $bracketData['min_price'],
                        'max_price' => $bracketData['max_price'] ?? null,
                        'percentage' => $bracketData['percentage'],
                        'order' => $bracketData['order'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Règle de commission créée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la règle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCommissionRate(Request $request, $rateId)
    {
        $request->validate([
            'brackets' => 'required|array|min:1',
            'brackets.*.min_price' => 'required|numeric|min:0',
            'brackets.*.max_price' => 'nullable|numeric|min:0',
            'brackets.*.percentage' => 'required|numeric|min:0|max:100',
            'brackets.*.order' => 'required|integer|min:1',
            'activation_fee' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $rate = CommissionRate::findOrFail($rateId);

        try {
            DB::transaction(function () use ($request, $rate) {
                $rate->update([
                    'service_type' => 'price_based_commissions',
                    'activation_fee' => $request->activation_fee,
                    'commission_percentage' => 0,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                $rate->brackets()->delete();

                foreach ($request->brackets as $bracketData) {
                    $rate->brackets()->create([
                        'min_price' => $bracketData['min_price'],
                        'max_price' => $bracketData['max_price'] ?? null,
                        'percentage' => $bracketData['percentage'],
                        'order' => $bracketData['order'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Règle de commission mise à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteCommissionRate($rateId)
    {
        $rate = CommissionRate::findOrFail($rateId);
        $rate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Règle de commission supprimée avec succès'
        ]);
    }

    // === FINANCIAL MONITORING ===
    
    public function getFinancialMonitoring()
    {
        try {
            $transactions = TransactionLog::latest()->take(10)->get();
            
            $stats = [
                'total_revenue' => (float) TransactionLog::sum('net_income'),
                'activation_fees' => (float) TransactionLog::where('description', 'like', 'FRAIS_ACTIVATION%')->sum('net_income'),
                'specialist_commissions' => (float) TransactionLog::where('description', 'like', '%commission%')->sum('net_income'),
                'hospital_subscriptions' => (float) TransactionLog::where('source_type', 'hospital')->where('description', 'like', '%abonnement%')->sum('net_income'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_transactions' => $transactions,
                'hospitals' => Hospital::where('is_active', true)->take(10)->get(),
                'specialists' => MedecinExterne::take(10)->get()->map(function($spec) {
                    return [
                        'id' => $spec->id,
                        'specialist_id' => $spec->id, // Pour la compatibilité JS
                        'name' => $spec->nom . ' ' . $spec->prenom,
                        'balance' => (float) $spec->balance,
                        'status' => strtoupper($spec->statut ?? 'INACTIF')
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Financial Monitoring Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getInvoices()
    {
        $invoices = \App\Models\Invoice::with(['patient', 'hospital'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                $paidAmount = $invoice->status === 'paid' ? $invoice->total : 0;
                $remainingAmount = $invoice->total - $paidAmount;

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'hospital_name' => $invoice->hospital ? $invoice->hospital->name : 'Hôpital inconnu',
                    'patient_name' => $invoice->patient ? $invoice->patient->name : 'Patient inconnu',
                    'total_amount' => $invoice->total,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'status' => $invoice->status === 'paid' ? 'PAYÉ' : 'IMPAYÉ',
                    'created_at' => $invoice->created_at->format('d/m/Y'),
                ];
            });

        $totalRevenue = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalPending = $totalRevenue - $totalPaid;

        return response()->json([
            'invoices' => $invoices,
            'stats' => [
                'total_revenue' => $totalRevenue,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'paid_invoices' => $invoices->where('status', 'PAYÉ')->count(),
                'pending_invoices' => $invoices->where('status', 'IMPAYÉ')->count(),
                'partial_invoices' => 0,
            ]
        ]);
    }
}
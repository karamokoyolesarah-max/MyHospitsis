<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\SubscriptionPlan;
use App\Models\CommissionRate;
use App\Models\CommissionBracket;
use App\Models\MedecinExterne;
use App\Models\SpecialistWallet;
use App\Models\TransactionLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

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
            'access_code' => 'required|string|min:8|max:10'
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
        $hospitals = Hospital::with(['subscriptionPlan'])->withCount('users')->get();

        // Récupérer tous les spécialistes
        $allSpecialists = MedecinExterne::orderBy('created_at', 'desc')->get();
        $pendingSpecialists = $allSpecialists->where('statut', 'inactif');

        // Récupérer la règle de commission active
        $activeCommissionRate = CommissionRate::where('is_active', true)->first();
        $activationFee = $activeCommissionRate ? (float) $activeCommissionRate->activation_fee : 4000;
        
        // Calculer une commission moyenne indicative (moyenne simple des tranches)
        $avgCommission = 0;
        if ($activeCommissionRate && $activeCommissionRate->brackets->count() > 0) {
            $avgCommission = (float) $activeCommissionRate->brackets->avg('percentage');
        } else {
            $avgCommission = 15;
        }

        // Statistiques dynamiques pour le dashboard
        $pendingRecharges = \App\Models\ExternalDoctorRecharge::where('requires_manual_validation', true)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $recentValidated = \App\Models\ExternalDoctorRecharge::where('requires_manual_validation', true)
            ->whereIn('status', ['completed', 'rejected'])
            ->orderBy('validated_at', 'desc')
            ->take(20)
            ->get();

        $pendingRechargesCount = $pendingRecharges->total();
        $stats = [
            'active_hospitals' => $hospitals->where('is_active', true)->count(),
            'total_users' => $hospitals->sum('users_count'),
            'total_patients' => \App\Models\Patient::count(),
            'pending_validations' => $pendingSpecialists->count() + $pendingRechargesCount,
            'pending_wave_recharges' => $pendingRechargesCount,
            'total_saas_revenue' => (float) TransactionLog::sum('net_income'),
            'total_commissions' => (float) TransactionLog::where('description', 'like', '%commission%')->sum('net_income'),
            'monthly_saas_revenue' => (float) TransactionLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('net_income'),
            'monthly_commissions' => (float) TransactionLog::where('description', 'like', '%commission%')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('net_income'),
            'activation_fee' => $activationFee, 
            'average_commission' => $avgCommission,
        ];
        // Récupérer les paramètres de paiement (numéros + QR Codes)
        $paymentSettings = Setting::where('group', 'payment')->pluck('value', 'key')->toArray();

        // Récupérer les paiements patients (rendez-vous avec consultation terminée ou payée)
        $patientPayments = Appointment::with(['patient', 'medecinExterne', 'doctor'])
            ->where(function ($q) {
                $q->whereNotNull('payment_transaction_id')
                  ->orWhereNotNull('patient_confirmation_end_at');
            })
            ->orderBy('updated_at', 'desc')
            ->take(100)
            ->get();

        return view('superadmin.dashboard', compact('hospitals', 'stats', 'allSpecialists', 'pendingSpecialists', 'pendingRecharges', 'recentValidated', 'paymentSettings', 'patientPayments'));
    }

    // === PATIENT PAYMENTS LIVE DATA (AJAX) ===

    public function getPatientPaymentsData()
    {
        $payments = Appointment::with(['patient', 'medecinExterne', 'doctor'])
            ->where(function ($q) {
                $q->whereNotNull('payment_transaction_id')
                  ->orWhereNotNull('patient_confirmation_end_at');
            })
            ->orderBy('updated_at', 'desc')
            ->take(100)
            ->get();

        $confirmed = $payments->whereNotNull('payment_transaction_id');
        $pending = $payments->whereNull('payment_transaction_id')->whereNotNull('patient_confirmation_end_at');

        $rows = '';
        foreach ($payments as $appt) {
            $patientInitials = strtoupper(substr($appt->patient->prenom ?? 'P', 0, 1)) . strtoupper(substr($appt->patient->nom ?? '', 0, 1));
            $patientName = $appt->patient->full_name ?? 'Patient inconnu';
            $patientPhone = $appt->patient->phone ?? '';

            $doctorHtml = '<span class="text-slate-400 text-sm">N/A</span>';
            if ($appt->medecinExterne) {
                $doctorHtml = '<div class="font-bold text-sm text-slate-700">Dr. ' . e($appt->medecinExterne->prenom) . ' ' . e($appt->medecinExterne->nom) . '</div><div class="text-[10px] text-slate-400">' . e($appt->medecinExterne->specialite ?? 'Généraliste') . '</div>';
            } elseif ($appt->doctor) {
                $doctorHtml = '<div class="font-bold text-sm text-slate-700">' . e($appt->doctor->name) . '</div><div class="text-[10px] text-slate-400">Interne</div>';
            }

            $typeHtml = $appt->consultation_type === 'home'
                ? '<span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-lg text-[10px] font-bold uppercase"><i class="bi bi-house-door-fill"></i> Domicile</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase"><i class="bi bi-hospital"></i> Hôpital</span>';

            $amount = number_format($appt->total_amount ?? 0, 0, ',', ' ');
            $method = $appt->payment_method ? '<span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold uppercase"><i class="bi bi-phone"></i> ' . e($appt->payment_method) . '</span>' : '<span class="text-slate-300 text-xs">—</span>';

            if ($appt->payment_transaction_id) {
                $statusHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Payé</span>';
            } elseif ($appt->patient_confirmation_end_at) {
                $statusHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-black uppercase"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> En attente</span>';
            } else {
                $statusHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-50 text-slate-500 rounded-full text-[10px] font-black uppercase"><span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> En cours</span>';
            }

            $txId = $appt->payment_transaction_id
                ? '<code class="text-[10px] bg-slate-100 px-2 py-1 rounded font-mono text-slate-600">' . e($appt->payment_transaction_id) . '</code>'
                : '<span class="text-slate-300 text-xs">—</span>';

            $date = $appt->appointment_datetime->format('d/m/Y');
            $time = $appt->appointment_datetime->format('H:i');

            $rows .= '<tr class="hover:bg-slate-50/50 transition">'
                . '<td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-bold">' . $patientInitials . '</div><div><div class="font-bold text-sm text-slate-900">' . e($patientName) . '</div><div class="text-[10px] text-slate-400">' . e($patientPhone) . '</div></div></div></td>'
                . '<td class="px-6 py-4">' . $doctorHtml . '</td>'
                . '<td class="px-6 py-4">' . $typeHtml . '</td>'
                . '<td class="px-6 py-4"><div class="font-black text-sm text-slate-900">' . $amount . ' <span class="text-[10px] text-slate-400">FCFA</span></div></td>'
                . '<td class="px-6 py-4">' . $method . '</td>'
                . '<td class="px-6 py-4">' . $statusHtml . '</td>'
                . '<td class="px-6 py-4">' . $txId . '</td>'
                . '<td class="px-6 py-4"><div class="text-sm text-slate-700">' . $date . '</div><div class="text-[10px] text-slate-400">' . $time . '</div></td>'
                . '</tr>';
        }

        if (empty($rows)) {
            $rows = '<tr><td colspan="8" class="px-6 py-16 text-center"><div class="text-slate-300 text-5xl mb-4"><i class="bi bi-credit-card-2-front"></i></div><div class="font-bold text-slate-500">Aucun paiement à afficher</div></td></tr>';
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'confirmed' => $confirmed->count(),
                'pending' => $pending->count(),
                'total_amount' => number_format($confirmed->sum('total_amount'), 0, ',', ' '),
                'monthly' => $confirmed->filter(fn($a) => $a->updated_at->month === now()->month)->count(),
            ],
            'html' => $rows,
        ]);
    }

    // === SETTINGS MANAGEMENT ===
    
    public function updateSettings(Request $request)
    {
        // Validation des champs
        $request->validate([
            'orange_money_number' => 'nullable|string|max:20',
            'mtn_money_number' => 'nullable|string|max:20',
            'moov_money_number' => 'nullable|string|max:20',
            'wave_number' => 'nullable|string|max:20',
            'qr_orange' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_mtn' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_moov' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_wave' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $settings = [
                    'payment_orange_money_number' => $request->orange_money_number,
                    'payment_mtn_money_number' => $request->mtn_money_number,
                    'payment_moov_money_number' => $request->moov_money_number,
                    'payment_wave_number' => $request->wave_number,
                ];

                // Traitement des uploads de QR Code
                $qrFields = [
                    'qr_orange' => 'payment_qr_orange',
                    'qr_mtn' => 'payment_qr_mtn',
                    'qr_moov' => 'payment_qr_moov',
                    'qr_wave' => 'payment_qr_wave',
                ];

                foreach ($qrFields as $inputName => $settingKey) {
                    if ($request->hasFile($inputName)) {
                        // Supprimer l'ancienne image si elle existe
                        $oldSetting = Setting::where('key', $settingKey)->first();
                        if ($oldSetting && $oldSetting->value && Storage::disk('public')->exists($oldSetting->value)) {
                            Storage::disk('public')->delete($oldSetting->value);
                        }

                        // Sauvegarder la nouvelle image
                        $path = $request->file($inputName)->store('payment_qrs', 'public');
                        $settings[$settingKey] = $path;
                    }
                }

                foreach ($settings as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'group' => 'payment'
                        ]
                    );
                }
            });

            return redirect()->back()->with('success', 'Configuration API de paiement mise à jour avec succès');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
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

                // 3. Création des 3 Caisses Réglementaires par défaut
                $services = [
                    ['name' => 'Accueil / Admissions', 'code' => 'ACC'],
                    ['name' => 'Pharmacie / Laboratoire', 'code' => 'PHL'],
                    ['name' => 'Urgences / Nuit', 'code' => 'URG'],
                ];

                foreach ($services as $index => $sData) {
                    $service = Service::create([
                        'hospital_id' => $hospital->id,
                        'name' => $sData['name'],
                        'code' => $sData['code'] . $hospital->id . rand(10, 99),
                        'description' => 'Service ' . $sData['name'],
                        'type' => 'support'
                    ]);

                    User::create([
                        'name' => 'Caissier ' . ($index + 1) . ' (' . $sData['name'] . ')',
                        'email' => 'cashier' . ($index + 1) . '.' . $hospital->id . '@hopit.sis',
                        'password' => Hash::make('password123'),
                        'role' => 'cashier',
                        'hospital_id' => $hospital->id,
                        'service_id' => $service->id,
                        'is_active' => true
                    ]);
                }
            });

            return redirect()->back()->with('success', 'L\'hôpital, son administrateur et ses 3 caisses ont été créés avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function initializeDefaultCashiers($hospitalId)
    {
        try {
            $hospital = Hospital::findOrFail($hospitalId);
            
            DB::transaction(function () use ($hospital) {
                $services = [
                    ['name' => 'Accueil / Admissions', 'code' => 'ACC'],
                    ['name' => 'Pharmacie / Laboratoire', 'code' => 'PHL'],
                    ['name' => 'Urgences / Nuit', 'code' => 'URG'],
                ];

                foreach ($services as $index => $sData) {
                    $service = Service::where('hospital_id', $hospital->id)
                                      ->where('name', $sData['name'])
                                      ->first();
                    
                    if (!$service) {
                        $service = Service::create([
                            'hospital_id' => $hospital->id,
                            'name' => $sData['name'],
                            'code' => $sData['code'] . $hospital->id . rand(10, 99),
                            'description' => 'Service ' . $sData['name'],
                            'type' => 'support'
                        ]);
                    }

                    $cashierExists = User::where('hospital_id', $hospital->id)
                                         ->where('service_id', $service->id)
                                         ->where('role', 'cashier')
                                         ->exists();
                    
                    if (!$cashierExists) {
                        User::create([
                            'name' => 'Caissier ' . ($index + 1) . ' (' . $sData['name'] . ')',
                            'email' => 'cashier' . ($index + 1) . '.' . Str::random(4) . '@hopital-' . $hospital->id . '.com',
                            'password' => Hash::make('password123'),
                            'role' => 'cashier',
                            'hospital_id' => $hospital->id,
                            'service_id' => $service->id,
                            'is_active' => true
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Les 3 caisses réglementaires ont été initialisées avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getHospitalDetails($hospitalId)
    {
        try {
            $hospital = Hospital::with([
                'users.service',
                'services.users',
                'services.prestations',
                'prestations.service',
                'patients'
            ])->findOrFail($hospitalId);

            // Calculer les statistiques
            $stats = [
                'total_users' => $hospital->users->count(),
                'total_services' => $hospital->services->count(),
                'total_prestations' => $hospital->prestations->count(),
                'total_patients' => $hospital->patients->count(),
                'total_cashiers' => $hospital->users->where('role', 'cashier')->count(),
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
                // Supprimer les fichiers uploadés du storage
                $filesToDelete = [
                    $specialist->diplome_path,
                    $specialist->id_card_recto_path,
                    $specialist->id_card_verso_path,
                    $specialist->video_verification_path,
                ];
                foreach ($filesToDelete as $filePath) {
                    if ($filePath && \Storage::disk('public')->exists($filePath)) {
                        \Storage::disk('public')->delete($filePath);
                    }
                }

                // Supprimer le portefeuille s'il existe
                SpecialistWallet::where('specialist_id', $specialist->id)->delete();

                // Supprimer définitivement le compte
                $specialist->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Demande rejetée et compte supprimé.'
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
        $activeRate = CommissionRate::where('is_active', true)->first();
        
        if (!$activeRate) {
            // Fallback to old hardcoded logic if no dynamic rule exists
            if ($amount >= 50000) return 35;
            if ($amount >= 26000) return 20;
            if ($amount >= 5000) return 10;
            return 0;
        }

        $bracket = $activeRate->brackets()
            ->where('min_price', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_price')
                      ->orWhere('max_price', '>=', $amount);
            })
            ->first();

        return $bracket ? (float) $bracket->percentage : 0;
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

    public function allSpecialistsList()
    {
        $specialists = MedecinExterne::with(['wallet'])->orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.specialists.index', compact('specialists'));
    }

    public function allHospitalsList()
    {
        $hospitals = Hospital::with(['subscriptionPlan'])->orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.hospitals.index', compact('hospitals'));
    }

    public function getSpecialistDetails($id)
    {
        try {
            $specialist = MedecinExterne::with(['wallet'])->findOrFail($id);
            $transactions = TransactionLog::where('source_type', 'specialist')
                ->where('source_id', $id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'success' => true,
                'specialist' => [
                    'id' => $specialist->id,
                    'name' => $specialist->prenom . ' ' . $specialist->nom,
                    'email' => $specialist->email,
                    'specialite' => $specialist->specialite,
                    'status' => strtoupper($specialist->statut),
                    'wallet' => $specialist->wallet ? [
                        'balance' => (float)$specialist->wallet->balance,
                        'is_activated' => (bool)$specialist->wallet->is_activated,
                        'is_blocked' => (bool)$specialist->wallet->is_blocked,
                        'activated_at' => $specialist->wallet->activated_at ? $specialist->wallet->activated_at->format('d/m/Y H:i') : null
                    ] : null
                ],
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSpecialistProfile($id)
    {
        $specialist = MedecinExterne::with(['wallet', 'prestations'])->findOrFail($id);
        
        // Récupérer les transactions financières
        $transactions = TransactionLog::where('source_type', 'specialist')
            ->where('source_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Pour les RDV, on va chercher dans la table TransactionLog ceux qui sont liés à des actes
        // car la relation Appointment/MedecinExterne n'est peut-être pas encore directe
        $consultations = TransactionLog::where('source_type', 'specialist')
            ->where('source_id', $id)
            ->where('description', 'like', '%CONSULTATION%')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques
        $stats = [
            'total_earned' => $transactions->sum('net_income'), // Pour le système
            'specialist_balance' => $specialist->wallet ? $specialist->wallet->balance : 0,
            'prestations_count' => $specialist->prestations->count(),
            'consultations_count' => $consultations->count(),
        ];

        return view('superadmin.specialists.show', compact('specialist', 'transactions', 'consultations', 'stats'));
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
                'hospitals' => Hospital::with('subscriptionPlan')->where('is_active', true)->take(10)->get(),
                'specialists' => MedecinExterne::with('wallet')->take(10)->get()->map(function($spec) {
                    return [
                        'id' => $spec->id,
                        'specialist_id' => $spec->id, // Pour la compatibilité JS
                        'name' => ($spec->nom ?? '') . ' ' . ($spec->prenom ?? ''),
                        'balance' => $spec->wallet ? (float) $spec->wallet->balance : 0,
                        'status' => strtoupper($spec->statut ?? 'INACTIF'),
                        'is_paid' => $spec->wallet && $spec->wallet->is_activated,
                        'paid_at' => $spec->wallet && $spec->wallet->activated_at ? $spec->wallet->activated_at->format('d/m/Y') : null
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
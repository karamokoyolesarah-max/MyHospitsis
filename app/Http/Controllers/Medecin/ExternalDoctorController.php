<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\MedecinExterne;
use App\Models\ExternalDoctorPrestation;
use App\Models\ExternalDoctorRecharge;
use App\Models\CommissionRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\TransactionLog;

class ExternalDoctorController extends Controller
{
    /**
     * Calcule le pourcentage de commission basé sur le prix et les tranches actives
     */
    private function calculateCommissionPercentage($price)
    {
        // Récupérer le taux de commission actif (supposons qu'il y en ait un global ou par type)
        // Ici on prend le premier actif, à adapter selon votre logique métier (par ex: type de service)
        $activeRate = CommissionRate::where('is_active', true)->with('brackets')->first();

        if (!$activeRate) {
            return 0; // Pas de configuration, pas de commission ? Ou une valeur par défaut
        }

        // Si des tranches sont définies, on cherche la bonne tranche
        if ($activeRate->brackets->isNotEmpty()) {
            foreach ($activeRate->brackets as $bracket) {
                if ($price >= $bracket->min_price && ($bracket->max_price === null || $price <= $bracket->max_price)) {
                    return $bracket->percentage;
                }
            }
        }
        
        // Si aucune tranche ne correspond (ou pas de tranches), on retourne le taux global
        return $activeRate->commission_percentage;
    }

    public function showLoginForm()
    {
        return view('medecin.external.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('medecin_externe')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('medecin_externe')->user();

            if ($user->statut !== 'actif') {
                Auth::guard('medecin_externe')->logout();
                return back()->withErrors(['email' => 'Votre compte est en attente de validation ou a été désactivé.']);
            }

            $request->session()->regenerate();

            return redirect()->route('external.doctor.external.dashboard')
                ->with('success', 'Bienvenue sur votre espace, Dr ' . $user->nom . ' !');
        }

        return back()->withErrors(['email' => 'Les identifiants fournis sont incorrects.'])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('auth.external-doctor-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:medecins_externes',
            'telephone' => 'required|string|max:20',
            'specialite' => 'required|string|max:255',
            'numero_ordre' => 'required|string|max:255|unique:medecins_externes',
            'adresse_residence' => 'nullable|string',
            'diplome' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_card_recto' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_card_verso' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'specialite' => $request->specialite,
            'numero_ordre' => $request->numero_ordre,
            'adresse_residence' => $request->adresse_residence,
            'password' => Hash::make($request->password),
            'statut' => 'inactif',
        ];

        // Gérer les fichiers
        if ($request->hasFile('diplome')) {
            $data['diplome_path'] = $request->file('diplome')->store('doctors/documents', 'public');
        }
        if ($request->hasFile('id_card_recto')) {
            $data['id_card_recto_path'] = $request->file('id_card_recto')->store('doctors/documents', 'public');
        }
        if ($request->hasFile('id_card_verso')) {
            $data['id_card_verso_path'] = $request->file('id_card_verso')->store('doctors/documents', 'public');
        }

        $doctor = MedecinExterne::create($data);

        // Au lieu de le connecter tout de suite (ce qui déclencherait EnsureUserIsActive s'il est inactif)
        // On le redirige vers la page de connexion avec un message de succès
        return redirect()->route('external.login')->with('success', 'Votre compte a été créé avec succès. Votre demande est en cours de validation par l\'administrateur.');
    }

    public function dashboard()
    {
        $user = Auth::guard('medecin_externe')->user();

        // Auto-désactivation si le forfait a expiré
        if ($user->is_available && !$user->hasPlanActive()) {
            $user->update(['is_available' => false]);
        }

        // Statistiques de base
        $stats = [
            'total_patients' => 0,
            'total_prescriptions' => 0,
            'total_appointments' => 0,
            'total_prestations' => $user->prestations()->count(),
        ];

        return view('medecin.external.dashboard', compact('stats', 'user'));
    }

    // == PATIENTS ==
    public function patients()
    {
        $user = Auth::guard('medecin_externe')->user();
        $patients = collect(); // À implémenter avec les relations réelles
        
        return view('medecin.external.patients', compact('user', 'patients'));
    }

    // == DOSSIERS PARTAGÉS ==
    public function sharedRecords()
    {
        $user = Auth::guard('medecin_externe')->user();
        $records = collect(); // À implémenter
        
        return view('medecin.external.shared-records', compact('user', 'records'));
    }

    // == PRESCRIPTIONS ==
    public function prescriptions()
    {
        $user = Auth::guard('medecin_externe')->user();
        $prescriptions = collect(); // À implémenter
        
        return view('medecin.external.prescriptions', compact('user', 'prescriptions'));
    }

    // == RENDEZ-VOUS ==
    public function appointments()
    {
        $user = Auth::guard('medecin_externe')->user();
        $appointments = collect(); // À implémenter
        
        return view('medecin.external.appointments', compact('user', 'appointments'));
    }

    // == PRESTATIONS ==
    public function prestations()
    {
        $user = Auth::guard('medecin_externe')->user();
        $prestations = $user->prestations()->orderBy('created_at', 'desc')->get();
        
        // Récupérer le taux actif pour le JS
        $activeRate = CommissionRate::where('is_active', true)->with('brackets')->first();
        
        return view('medecin.external.prestations', compact('user', 'prestations', 'activeRate'));
    }

    public function storePrestation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $user = Auth::guard('medecin_externe')->user();
        
        // Calcul automatique de la commission
        $commissionPercentage = $this->calculateCommissionPercentage($request->price);

        $user->prestations()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'commission_percentage' => $commissionPercentage, // Automatique
            'is_active' => true,
        ]);

        return redirect()->route('external.prestations')->with('success', 'Prestation ajoutée avec succès. Taux de commission appliqué : ' . $commissionPercentage . '%');
    }

    public function updatePrestation(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $user = Auth::guard('medecin_externe')->user();
        $prestation = $user->prestations()->findOrFail($id);

        // Recalcul automatique de la commission si le prix change
        $commissionPercentage = $this->calculateCommissionPercentage($request->price);

        $prestation->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'commission_percentage' => $commissionPercentage, // Automatique
        ]);

        return redirect()->route('external.prestations')->with('success', 'Prestation mise à jour avec succès. Nouveau taux : ' . $commissionPercentage . '%');
    }

    public function togglePrestation($id)
    {
        $user = Auth::guard('medecin_externe')->user();
        $prestation = $user->prestations()->findOrFail($id);
        $prestation->update(['is_active' => !$prestation->is_active]);

        return redirect()->route('external.prestations')->with('success', 'Statut de la prestation modifié.');
    }

    public function destroyPrestation($id)
    {
        $user = Auth::guard('medecin_externe')->user();
        $user->prestations()->findOrFail($id)->delete();

        return redirect()->route('external.prestations')->with('success', 'Prestation supprimée.');
    }

    // == PROFIL ==
    public function profile()
    {
        $user = Auth::guard('medecin_externe')->user();
        return view('medecin.external.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('medecin_externe')->user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'specialite' => 'required|string|max:255',
            'adresse_cabinet' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:10240', // Validation photo (10MB max)
        ]);

        $data = $request->only([
            'nom', 'prenom', 'telephone', 'specialite', 'adresse_cabinet', 'adresse_residence'
        ]);

        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        $user->update($data);

        return redirect()->route('external.profile')->with('success', 'Profil mis à jour avec succès.');
    }

    // == PARAMÈTRES ==
    public function settings()
    {
        $user = Auth::guard('medecin_externe')->user();
        return view('medecin.external.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('medecin_externe')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('external.settings')->with('success', 'Mot de passe modifié avec succès.');
    }

    // == DISPONIBILITÉ ==
    public function toggleAvailability()
    {
        $user = Auth::guard('medecin_externe')->user();
        
        // Si le forfait n'est pas actif, on ne peut pas être disponible
        if (!$user->hasPlanActive()) {
            if ($user->is_available) {
                $user->update(['is_available' => false]);
            }
            return redirect()->back()->with('error', 'Votre compte n\'est pas actif. Veuillez recharger pour payer les frais d\'activation.');
        }
        
        $user->update(['is_available' => !$user->is_available]);

        $status = $user->is_available ? 'activée' : 'désactivée';
        return redirect()->back()->with('success', "Disponibilité {$status} avec succès.");
    }

    // == RECHARGEMENT ==
    public function recharge()
    {
        $user = Auth::guard('medecin_externe')->user();

        // Auto-désactivation si le forfait a expiré
        if ($user->is_available && !$user->hasPlanActive()) {
            $user->update(['is_available' => false]);
        }

        $recharges = $user->recharges()->orderBy('created_at', 'desc')->take(10)->get();
        
        return view('medecin.external.recharge', compact('user', 'recharges'));
    }

    public function initiateRecharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'payment_method' => 'required|in:mtn,orange,wave',
            'phone_number' => 'required|string|max:20',
        ]);

        $user = Auth::guard('medecin_externe')->user();

        // Créer l'enregistrement du rechargement
        $recharge = $user->recharges()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
        ]);

        // SIMULATION DU PAIEMENT REUSSI
        
        // 1. Mettre à jour le statut du rechargement
        $recharge->update(['status' => 'completed']);

        // 2. Créditer le compte du médecin (solde brut)
        $user->balance += $recharge->amount;
        
        // 3. Gestion des frais d'activation et validité
        $message = 'Paiement confirmé ! Votre compte a été rechargé.';
        
        // Récupérer le taux de commission actif pour avoir les frais d'activation
        $activeRate = CommissionRate::where('is_active', true)->first();
        $activationFee = $activeRate ? $activeRate->activation_fee : 4000; // Par défaut 4000 si non configuré

        // Vérifier si le compte est expiré ou n'a jamais été activé
        // On force aussi le prélèvement si la date est dans plus de 5 ans (cas du "à vie" à corriger)
        $isExpired = !$user->plan_expires_at || $user->plan_expires_at->isPast();
        $isFixedLife = $user->plan_expires_at && $user->plan_expires_at->year > (now()->year + 5);

        if ($isExpired || $isFixedLife) {
            
            // On vérifie si le solde est suffisant pour payer les frais
            if ($user->balance >= $activationFee) {
                // Prélèvement des frais
                $user->balance -= $activationFee;
                
                // Activation pour 30 jours (Abonnement Mensuel)
                $user->plan_expires_at = now()->addDays(30);
                $user->save(); // Sauvegarde immédiate
                
                // Enregistrement de la transaction pour le Super Admin
                TransactionLog::create([
                    'source_type' => 'specialist',
                    'source_id' => $user->id,
                    'amount' => $recharge->amount,
                    'fee_applied' => $activationFee,
                    'net_income' => $activationFee,
                    'description' => "FRAIS_ACTIVATION: Activation mensuelle spécialiste",
                ]);

                $message .= " Les frais d'activation de " . number_format($activationFee) . " FCFA ont été prélevés. Votre compte est actif pour 30 jours.";
            } else {
                $message .= " Attention: Votre solde est insuffisant pour payer les frais d'activation (" . number_format($activationFee) . " FCFA). Veuillez recharger à nouveau.";
            }
        } else {
             // Si le compte est déjà actif, on prolonge simplement ? Ou on laisse le solde s'accumuler ?
             // Si l'utilisateur recharge alors qu'il est actif, c'est pour anticiper les commissions de prestations.
             // On ne prélève pas de frais d'activation ici, l'argent reste sur le solde pour les commissions futures.
             
             // Optionnel : on loggue quand même le dépôt mais avec 0 de revenu pour le SAAS
             TransactionLog::create([
                'source_type' => 'specialist',
                'source_id' => $user->id,
                'amount' => $recharge->amount,
                'fee_applied' => 0,
                'net_income' => 0, // Pas de gain direct pour le SAAS sur un simple dépôt sans activation
                'description' => "Rechargement solde (Dépôt)",
            ]);
        }
        
        $user->save();

        return redirect()->route('external.recharge')->with('success', $message);
    }

}

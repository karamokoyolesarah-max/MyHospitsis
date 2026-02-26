<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\MedecinExterne;
use App\Models\Setting;
use App\Models\ExternalDoctorPrestation;
use App\Models\ExternalDoctorRecharge;
use App\Models\SuperAdmin;
use App\Notifications\ExternalRechargeNotification;
use App\Models\CommissionRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\TransactionLog;
use Barryvdh\DomPDF\Facade\Pdf;

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

        MedecinExterne::create($data);

        return redirect()->route('external.login')->with('success', 'Votre demande d\'inscription a été soumise avec succès. Elle sera validée par l\'administration prochainement.');
    }

    public function dashboard()
    {
        $user = Auth::guard('medecin_externe')->user();

        // Auto-désactivation si le forfait a expiré
        if ($user->is_available && !$user->hasPlanActive()) {
            $user->update(['is_available' => false]);
        }

        // Statistiques réelles
        $stats = [
            'total_patients' => \App\Models\Appointment::where('medecin_externe_id', $user->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            
            'total_prescriptions' => \App\Models\Prescription::where('medecin_externe_id', $user->id)->count(),

            'total_appointments' => \App\Models\Appointment::where('medecin_externe_id', $user->id)->count(),
            
            'total_prestations' => $user->prestations()->count(),
            
            'total_revenue' => \App\Models\Appointment::where('medecin_externe_id', $user->id)
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];

        // Agenda du jour
        $todayAppointments = \App\Models\Appointment::where('medecin_externe_id', $user->id)
            ->whereDate('appointment_datetime', now()->toDateString())
            ->with('patient')
            ->orderBy('appointment_datetime', 'asc')
            ->get();

        return view('medecin.external.dashboard', compact('stats', 'user', 'todayAppointments'));
    }

    // == PATIENTS ==
    public function patients()
    {
        $user = Auth::guard('medecin_externe')->user();
        
        // Récupérer les patients qui ont eu au moins un rendez-vous avec ce médecin
        $patients = \App\Models\Patient::whereHas('appointments', function($q) use ($user) {
            $q->where('medecin_externe_id', $user->id);
        })->withCount(['appointments' => function($q) use ($user) {
            $q->where('medecin_externe_id', $user->id);
        }])->paginate(15);
        
        return view('medecin.external.patients', compact('user', 'patients'));
    }

    // == DOSSIERS PARTAGÉS ==
    public function sharedRecords()
    {
        $user = Auth::guard('medecin_externe')->user();
        
        // Dossiers médicaux (PatientVital) explicitement liés à ce médecin
        $records = \App\Models\PatientVital::where('medecin_externe_id', $user->id)
            ->with(['patient'])
            ->latest()
            ->paginate(15);

        // Documents créés par le médecin
        $documents = \App\Models\MedicalDocument::where('medecin_externe_id', $user->id)
            ->with(['patient'])
            ->latest()
            ->take(20)
            ->get();
        
        return view('medecin.external.shared-records', compact('user', 'records', 'documents'));
    }

    // == PRESCRIPTIONS ==
    public function prescriptions()
    {
        $user = Auth::guard('medecin_externe')->user();
        
        // Prescriptions explicitement liées à ce médecin
        $prescriptions = \App\Models\Prescription::where('medecin_externe_id', $user->id)
            ->with(['patient'])
            ->latest()
            ->paginate(15);
        
        return view('medecin.external.prescriptions', compact('user', 'prescriptions'));
    }

    public function createPrescription(Request $request)
    {
        $user = Auth::guard('medecin_externe')->user();
        $patients = \App\Models\Patient::whereHas('appointments', function($q) use ($user) {
            $q->where('medecin_externe_id', $user->id);
        })->get();

        $selected_patient = null;
        if ($request->has('patient_id')) {
            $selected_patient = \App\Models\Patient::find($request->patient_id);
        }

        return view('medecin.external.prescriptions.create', compact('user', 'patients', 'selected_patient'));
    }

    public function storePrescription(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medication' => 'required|string',
            'type' => 'required|string',
            'duration' => 'nullable|string',
            'instructions' => 'nullable|string',
            'category' => 'required|in:pharmacy,nurse',
        ]);

        $user = Auth::guard('medecin_externe')->user();

        \App\Models\Prescription::create([
            'medecin_externe_id' => $user->id,
            'patient_id' => $request->patient_id,
            'medication' => $request->medication,
            'dosage' => $request->duration, // On utilise duration pour dosage dans ce contexte simplifié
            'instructions' => $request->instructions,
            'category' => $request->category,
            'start_date' => now(),
            'status' => 'active',
        ]);

        $user = Auth::guard('medecin_externe')->user();

        $prescription = \App\Models\Prescription::create([
            'medecin_externe_id' => $user->id,
            'patient_id' => $request->patient_id,
            'medication' => $request->medication,
            'dosage' => $request->duration, // On utilise duration pour dosage dans ce contexte simplifié
            'instructions' => $request->instructions,
            'category' => $request->category,
            'start_date' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('external.prescriptions')->with('success', 'Ordonnance créée avec succès.');
    }

    public function generatePrescriptionPdf(\App\Models\Prescription $prescription)
    {
        $user = Auth::guard('medecin_externe')->user();
        
        // Vérification de sécurité : le médecin doit être le créateur
        if ($prescription->medecin_externe_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $patient = $prescription->patient;

        $pdf = Pdf::loadView('medecin.external.pdf.prescription', [
            'prescription' => $prescription,
            'doctor' => $user,
            'patient' => $patient,
        ]);

        return $pdf->download('ordonnance-' . $prescription->id . '.pdf');
    }

    // == DOCUMENTS MÉDICAUX ==
    public function createDocument(Request $request)
    {
        $user = Auth::guard('medecin_externe')->user();
        $patients = \App\Models\Patient::whereHas('appointments', function($q) use ($user) {
            $q->where('medecin_externe_id', $user->id);
        })->get();

        $type = $request->get('type', 'report'); // report, certificate, liaison
        
        // Pré-sélection du patient si l'ID est passé en paramètre
        $selected_patient = null;
        if ($request->has('patient_id')) {
            $selected_patient = \App\Models\Patient::find($request->patient_id);
        }

        return view('medecin.external.documents.create', compact('user', 'patients', 'type', 'selected_patient'));
    }

    public function storeDocument(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = Auth::guard('medecin_externe')->user();
        $patient = \App\Models\Patient::findOrFail($request->patient_id);

        // 1. Créer l'enregistrement DB
        $document = \App\Models\MedicalDocument::create([
            'medecin_externe_id' => $user->id,
            'patient_id' => $request->patient_id,
            'document_type' => $request->type,
            'title' => $request->title,
            'content' => $request->content, // Sauvegarder le contenu brut aussi
            'is_validated' => true,
            'validated_at' => now(),
        ]);

        // 2. Générer le PDF
        $pdf = Pdf::loadView('medecin.external.pdf.document', [
            'document' => $document,
            'doctor' => $user,
            'patient' => $patient,
            'content' => $request->content,
        ]);

        // 3. Sauvegarder le fichier
        $fileName = 'doc_' . $document->id . '_' . time() . '.pdf';
        $filePath = 'documents/external/' . $user->id . '/' . $fileName; // storage/app/public/...
        
        Storage::disk('public')->put($filePath, $pdf->output());

        // 4. Mettre à jour le chemin
        $document->update([
            'file_name' => $request->title . '.pdf',
            'file_path' => $filePath,
            'mime_type' => 'application/pdf',
            'file_size' => Storage::disk('public')->size($filePath),
        ]);

        return redirect()->route('external.shared-records')->with('success', 'Document créé et généré avec succès.');
    }

    public function downloadDocument(\App\Models\MedicalDocument $document)
    {
        $user = Auth::guard('medecin_externe')->user();

        // Vérification de sécurité
        if ($document->medecin_externe_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            // Si le fichier physique n'existe pas, on tente de le regénérer à la volée (fallback)
             $patient = $document->patient;
             $pdf = Pdf::loadView('medecin.external.pdf.document', [
                'document' => $document,
                'doctor' => $user,
                'patient' => $patient,
                'content' => $document->content ?? '',
            ]);
            return $pdf->download($document->file_name);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    // == RENDEZ-VOUS ==
    public function appointments()
    {
        $user = Auth::guard('medecin_externe')->user();
        $appointments = \App\Models\Appointment::where('medecin_externe_id', $user->id)
            ->with(['patient', 'prestations'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);
        
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
            'profile_photo' => 'nullable|image|max:10240',
            'payment_orange_number' => 'nullable|string|max:20',
            'payment_mtn_number' => 'nullable|string|max:20',
            'payment_moov_number' => 'nullable|string|max:20',
            'payment_wave_number' => 'nullable|string|max:20',
            'qr_orange' => 'nullable|image|max:2048',
            'qr_mtn' => 'nullable|image|max:2048',
            'qr_moov' => 'nullable|image|max:2048',
            'qr_wave' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nom', 'prenom', 'telephone', 'specialite', 'adresse_cabinet', 'adresse_residence',
            'payment_orange_number', 'payment_mtn_number', 'payment_moov_number', 'payment_wave_number'
        ]);

        // Profile Photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // QR Codes
        $qrFields = [
            'qr_orange' => 'payment_qr_orange',
            'qr_mtn' => 'payment_qr_mtn',
            'qr_moov' => 'payment_qr_moov',
            'qr_wave' => 'payment_qr_wave',
        ];

        foreach ($qrFields as $inputName => $dbColumn) {
            if ($request->hasFile($inputName)) {
                // Supprimer l'ancien QR si il existe
                if ($user->$dbColumn && Storage::disk('public')->exists($user->$dbColumn)) {
                    Storage::disk('public')->delete($user->$dbColumn);
                }
                $data[$dbColumn] = $request->file($inputName)->store('payment_qrs', 'public');
            }
        }

        $user->update($data);

        return redirect()->route('external.profile')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Mise à jour des configurations de paiement uniquement (numéros + QR Codes)
     */
    public function updatePaymentConfig(Request $request)
    {
        $user = Auth::guard('medecin_externe')->user();

        $request->validate([
            'payment_orange_number' => 'nullable|string|max:20',
            'payment_mtn_number' => 'nullable|string|max:20',
            'payment_moov_number' => 'nullable|string|max:20',
            'payment_wave_number' => 'nullable|string|max:20',
            'qr_orange' => 'nullable|image|max:2048',
            'qr_mtn' => 'nullable|image|max:2048',
            'qr_moov' => 'nullable|image|max:2048',
            'qr_wave' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'payment_orange_number', 'payment_mtn_number', 'payment_moov_number', 'payment_wave_number'
        ]);

        // QR Codes
        $qrFields = [
            'qr_orange' => 'payment_qr_orange',
            'qr_mtn' => 'payment_qr_mtn',
            'qr_moov' => 'payment_qr_moov',
            'qr_wave' => 'payment_qr_wave',
        ];

        foreach ($qrFields as $inputName => $dbColumn) {
            if ($request->hasFile($inputName)) {
                // Supprimer l'ancien QR si il existe
                if ($user->$dbColumn && Storage::disk('public')->exists($user->$dbColumn)) {
                    Storage::disk('public')->delete($user->$dbColumn);
                }
                $data[$dbColumn] = $request->file($inputName)->store('payment_qrs', 'public');
            }
        }

        $user->update($data);

        return redirect()->route('external.profile')->with('success', 'Configurations de paiement mises à jour avec succès.');
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
        $paymentSettings = Setting::where('group', 'payment')->pluck('value', 'key');
        
        return view('medecin.external.recharge', compact('user', 'recharges', 'paymentSettings'));
    }

    public function initiateRecharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'payment_method' => 'required|in:mtn,orange,wave,moov',
            'phone_number' => 'required|string|max:20',
            'transaction_ref' => 'required_if:payment_method,wave|nullable|string|min:8|max:100', // For Wave manual validation, at least 8 chars
        ]);

        $user = Auth::guard('medecin_externe')->user();
        $paymentMethod = $request->payment_method;

        // Generate unique transaction reference
        $transactionRef = \App\Services\CinetPayService::generateTransactionRef('RCH');

        // Create recharge record
        $recharge = $user->recharges()->create([
            'amount' => $request->amount,
            'phone_number' => $request->phone_number,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionRef,
            'status' => 'pending',
            'requires_manual_validation' => ($paymentMethod === 'wave'),
        ]);

        // Notify SuperAdmin(s)
        try {
            $payload = [
                'recharge_id' => $recharge->id,
                'user_id' => $user->id,
                'user_name' => $user->nom . ' ' . $user->prenom,
                'amount' => $recharge->amount,
                'payment_method' => $recharge->payment_method,
                'phone_number' => $recharge->phone_number,
                'status' => $recharge->status,
            ];

            $superadmins = SuperAdmin::all();
            foreach ($superadmins as $sa) {
                $sa->notify(new ExternalRechargeNotification($payload));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify superadmins about external recharge', ['error' => $e->getMessage()]);
        }

        // ===== WAVE: Manual validation flow =====
        if ($paymentMethod === 'wave') {
            // Store the user's transaction reference for admin verification
            if ($request->filled('transaction_ref')) {
                $recharge->update([
                    'cinetpay_transaction_id' => $request->transaction_ref,
                ]);
            }

            return redirect()->route('external.recharge')
                ->with('info', 'Votre demande de rechargement Wave a été soumise. Elle sera validée par l\'administrateur après vérification du paiement. Référence: ' . $transactionRef);
        }

        // ===== CinetPay: API integration for MTN/Orange/Moov =====
        $cinetpay = new \App\Services\CinetPayService();
        
        $result = $cinetpay->initiatePayment(
            (int) $request->amount,
            $transactionRef,
            [
                'customer_name' => $user->prenom,
                'customer_surname' => $user->nom,
                'customer_email' => $user->email,
                'customer_phone' => $request->phone_number,
                'recharge_id' => $recharge->id,
                'medecin_id' => $user->id,
            ]
        );

        if ($result['success'] && $result['payment_url']) {
            // Update recharge with payment token
            $recharge->update([
                'payment_token' => $result['payment_token'],
            ]);

            // Redirect to CinetPay payment page
            return redirect()->away($result['payment_url']);
        }

        // Payment initiation failed
        $recharge->update([
            'status' => 'failed',
            'failure_reason' => $result['error'] ?? 'Erreur inconnue',
        ]);

        return redirect()->route('external.recharge')
            ->with('error', 'Impossible d\'initier le paiement: ' . ($result['error'] ?? 'Erreur inconnue') . '. Veuillez réessayer.');
    }

    /**
     * Handle callback after CinetPay payment (user returns)
     */
    public function handleRechargeCallback(Request $request)
    {
        $transactionId = $request->get('transaction_id') ?? $request->get('cpm_trans_id');
        
        if (!$transactionId) {
            return redirect()->route('external.recharge')
                ->with('warning', 'Paiement en cours de traitement. Veuillez patienter quelques instants.');
        }

        $recharge = \App\Models\ExternalDoctorRecharge::where('transaction_id', $transactionId)->first();

        if (!$recharge) {
            return redirect()->route('external.recharge')
                ->with('error', 'Transaction introuvable.');
        }

        // Check status with CinetPay
        $cinetpay = new \App\Services\CinetPayService();
        $result = $cinetpay->checkTransaction($transactionId);

        if ($result['success'] && $result['status'] === 'completed') {
            return redirect()->route('external.recharge')
                ->with('success', 'Paiement confirmé ! Votre compte a été rechargé.');
        } elseif ($result['status'] === 'failed') {
            return redirect()->route('external.recharge')
                ->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        }

        // Still pending
        return redirect()->route('external.recharge')
            ->with('info', 'Paiement en attente de confirmation. Vous recevrez un SMS une fois le paiement validé.');
    }

    /**
     * Mettre à jour la localisation du médecin en temps réel
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'appointment_id' => 'nullable|exists:appointments,id'
        ]);

        $doctor = Auth::guard('medecin_externe')->user();
        
        // Si un ID de rendez-vous est fourni, on met à jour ce RDV spécifique
        if ($request->appointment_id) {
            $appointment = \App\Models\Appointment::where('id', $request->appointment_id)
                ->where('medecin_externe_id', $doctor->id)
                ->first();

            if ($appointment) {
                $appointment->update([
                    'doctor_current_latitude' => $request->latitude,
                    'doctor_current_longitude' => $request->longitude,
                ]);
            }
        }

        // On peut aussi mettre à jour la position globale du médecin pour de futures recherches
        $doctor->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Changer le statut d'un rendez-vous (En route, Arrivé, etc.)
     */
    public function updateAppointmentStatus(Request $request, \App\Models\Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|string|in:accepted,on_the_way,arrived,completed,cancelled'
        ]);

        $doctor = Auth::guard('medecin_externe')->user();

        // Si on accepte, on peut s'auto-assigner si aucun médecin n'est assigné
        if ($request->status === 'accepted' && $appointment->medecin_externe_id === null) {
            $appointment->medecin_externe_id = $doctor->id;
        }

        if ((int)$appointment->medecin_externe_id !== (int)$doctor->id) {
            return response()->json(['error' => 'Non autorisé : ce rendez-vous ne vous est pas assigné'], 403);
        }

        $updateData = ['status' => $request->status];

        if ($request->status === 'on_the_way') {
            $updateData['travel_started_at'] = now();
        } elseif ($request->status === 'completed') {
            $updateData['travel_completed_at'] = now();
        }

        try {
            $appointment->update($updateData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur technique : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'status' => 'success',
            'new_status' => $request->status
        ]);
    }

    // Générer la facture PDF pour un rendez-vous
    public function generateInvoicePdf(\App\Models\Appointment $appointment)
    {
        // Vérifier que le médecin est bien celui du rendez-vous
        if ($appointment->medecin_externe_id !== Auth::guard('medecin_externe')->id()) {
            abort(403, 'Accès non autorisé');
        }

        $pdf = Pdf::loadView('pdf.external_doctor_invoice', [
            'appointment' => $appointment,
            'doctor' => $appointment->medecinExterne,
            'patient' => $appointment->patient,
        ]);

        return $pdf->download('facture-' . $appointment->id . '.pdf');
    }
}

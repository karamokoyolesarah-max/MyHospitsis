<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, MedicalRecord, Prescription, Invoice, PatientVital};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class PatientPortalController extends Controller 
{
    public function __construct()
    {
        $this->middleware('auth:patients'); 
    }

    public function dashboard()
    {
        \Log::info('=== ACCÈS AU DASHBOARD PATIENT ===');
        \Log::info('Guard patients authentifié ?', [
            'authentifié' => Auth::guard('patients')->check() ? 'OUI' : 'NON',
            'patient_id' => Auth::guard('patients')->id() ?? 'N/A',
        ]);

        $patient = Auth::guard('patients')->user();

        if (!$patient) {
            \Log::error('AUCUN PATIENT CONNECTÉ - Redirection vers login');
            return redirect()->route('login');
        }

        \Log::info('Patient trouvé', [
            'id' => $patient->id,
            'nom' => $patient->full_name,
            'email' => $patient->email,
        ]);

        $patient->load([
            'referringDoctor',
            'appointments' => fn($query) => $query->latest()->take(5)
        ]);

        $totalAppointments = $patient->appointments()->count();
        // Compter les prescriptions basées sur vitals
        $totalPrescriptions = $patient->vitals()
            ->whereNotNull('ordonnance')
            ->where('ordonnance', '!=', '')
            ->where('is_visible_to_patient', true)
            ->count();

        $upcomingAppointments = $patient->appointments()
            ->where('appointment_datetime', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('appointment_datetime')
            ->with(['doctor', 'service'])
            ->take(3)
            ->get();

        // Récupérer les dossiers récents depuis PatientVital
        $recentRecords = $patient->vitals()
            ->where('is_visible_to_patient', true)
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        \Log::info('Chargement de la vue dashboard');

        return view('patients.auth.dashboard', compact(
            'patient', 
            'upcomingAppointments', 
            'recentRecords', 
            'totalAppointments', 
            'totalPrescriptions'
        ));
    }

    public function appointments()
    {
        $patient = Auth::guard('patients')->user();

        $appointments = $patient->appointments()
            ->with(['doctor', 'service', 'hospital', 'prestations'])
            ->latest()
            ->paginate(10);

        return view('portal.appointments', compact('appointments'));
    }
    public function showBookAppointmentForm()
{
    $patient = Auth::guard('patients')->user();
    
    // 1. Récupérer tous les hôpitaux
    $hospitals = \App\Models\Hospital::all();

    // 2. Préparer les services groupés par hôpital pour le JavaScript
    // On récupère les services (ou prestations) liés aux hôpitaux
    $allServices = \App\Models\Service::withoutGlobalScope('hospital_filter')->get(); 

    $servicesAndPrestations = [];
    foreach ($allServices as $service) {
        // On crée un tableau où la clé est l'ID de l'hôpital
        $servicesAndPrestations[$service->hospital_id][] = [
            'id' => $service->id,
            'name' => $service->name,
            'price' => $service->price
        ];
    }

    return view('patients.auth.book-appointment', compact('patient', 'hospitals', 'servicesAndPrestations'));
}
    /**
     * Afficher la page de sélection du type de consultation
     */
    public function showConsultationTypeSelector()
    {
        $patient = Auth::guard('patients')->user();
        return view('portal.consultation-type-selector', compact('patient'));
    }

    /**
     * Afficher le formulaire de rendez-vous avec type pré-sélectionné (hôpital)
     */
    public function bookAppointmentHospital()
    {
        return $this->bookAppointmentWithType('hospital');
    }

    /**
     * Afficher le formulaire de rendez-vous avec type pré-sélectionné (domicile)
     */
    public function bookAppointmentHome()
    {
        return $this->bookAppointmentWithType('home');
    }

    /**
     * Méthode privée pour afficher le formulaire avec un type spécifique
     */
    private function bookAppointmentWithType($consultationType)
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = \App\Models\Hospital::where('is_active', true)->get();

        $hospitalsData = [];
        $specialties = [];

        if ($consultationType === 'home') {
            // Pour le domicile, on récupère les spécialités des médecins externes actifs (en tant que chaînes)
            $specialties = \App\Models\MedecinExterne::where('statut', 'actif')
                ->where('is_available', true)
                ->whereNotNull('specialite')
                ->distinct()
                ->pluck('specialite')
                ->toArray();
        } else {
            // Pour l'hôpital, on peut utiliser les noms des services médicaux comme spécialités
            $specialties = \App\Models\Service::where('type', 'medical')
                ->where('is_active', true)
                ->distinct()
                ->pluck('name')
                ->toArray();
        }

        foreach ($hospitals as $hospital) {
            $services = \App\Models\Service::withoutGlobalScope('hospital_filter')
                ->where('hospital_id', $hospital->id)
                ->where('is_active', true)
                ->whereIn('type', ['medical', 'technical']) // Filtrer seulement les pôles Soins et Technique
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => $s->consultation_price ?? 0
                ])->toArray();

            $prestations = \App\Models\Prestation::withoutGlobalScope('hospital_filter')
                ->where('hospital_id', $hospital->id)
                ->where('is_active', true)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price ?? 0,
                    'service_id' => $p->service_id
                ])->toArray();

            $hospitalsData[$hospital->id] = [
                'services' => $services,
                'prestations' => $prestations,
                'address' => $hospital->address
            ];
        }

        return view('portal.book-appointment', compact('patient', 'hospitals', 'hospitalsData', 'consultationType', 'specialties'));
    }

    /**
     * Récupérer les médecins externes par spécialité (AJAX)
     */
    public function getExternalDoctorsBySpecialty($specialty)
    {
        $doctors = \App\Models\MedecinExterne::where('specialite', $specialty)
            ->where('statut', 'actif')
            ->where('is_available', true)
            ->get()
            ->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'full_name' => $doctor->prenom . ' ' . $doctor->nom,
                    'photo' => $doctor->profile_photo_path ? asset('storage/' . $doctor->profile_photo_path) : asset('assets/img/default-avatar.png'),
                    'consultation_price' => $doctor->consultation_price ?? 15000,
                    'base_travel_fee' => $doctor->base_travel_fee ?? 5000,
                    'travel_fee_per_km' => $doctor->travel_fee_per_km ?? 500,
                    'latitude' => $doctor->latitude,
                    'longitude' => $doctor->longitude,
                ];
            });

        return response()->json($doctors);
    }

    /**
     * Récupérer les médecins internes par spécialité/service (AJAX)
     */
    public function getInternalDoctorsBySpecialty($specialty)
    {
        // On cherche le service correspondant au nom de la spécialité
        $service = \App\Models\Service::where('name', $specialty)->first();
        
        $query = \App\Models\User::where('role', 'doctor')
            ->where('is_active', true);

        if ($service) {
            $query->where('service_id', $service->id);
        }

        $doctors = $query->get()
            ->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'full_name' => $doctor->name,
                    'photo' => $doctor->profile_photo_path ? asset('storage/' . $doctor->profile_photo_path) : asset('assets/img/default-avatar.png'),
                    'specialty' => $doctor->service?->name ?? 'Généraliste',
                ];
            });

        return response()->json($doctors);
    }

    /**
     * Géocoder une adresse et calculer les frais de déplacement (AJAX)
     */
    public function calculateHomeVisitFees(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'medecin_externe_id' => 'nullable|exists:medecins_externes,id',
            'hospital_id' => 'nullable|exists:hospitals,id'
        ]);

        $geoService = new \App\Services\GeolocationService();
        
        // 1. Géocoder l'adresse du patient
        $patientGeo = $geoService->geocodeAddress($request->address);
        
        if (!$patientGeo) {
            return response()->json(['error' => 'Impossible de localiser cette adresse.'], 422);
        }

        // 2. Calcul des frais
        $consultationPrice = 0;
        if ($request->filled('medecin_externe_id')) {
            $doctor = \App\Models\MedecinExterne::findOrFail($request->medecin_externe_id);
            $consultationPrice = $doctor->consultation_price ?? 15000;
            $docLat = $doctor->latitude ?? 5.3484; 
            $docLon = $doctor->longitude ?? -4.0305;
            $distance = $geoService->calculateDistance($docLat, $docLon, $patientGeo['latitude'], $patientGeo['longitude']);
            $fees = $geoService->calculateTravelFee($doctor, $distance);
            $isInRange = $geoService->isWithinRange($doctor, $distance);
        } else {
            // Frais par défaut si pas de médecin sélectionné
            $fees = [
                'total_travel_fee' => 5000,
                'distance_km' => 0
            ];
            $isInRange = true;
        }

        // 3. Calcul de la TVA et du Total
        $subtotal = $consultationPrice + $fees['total_travel_fee'];
        $taxRate = 0.18; // TVA 18%
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;
        
        return response()->json([
            'patient_geo' => $patientGeo,
            'fees' => $fees,
            'consultation_price' => $consultationPrice,
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'is_in_range' => $isInRange
        ]);
    }


    /**
     * Récupérer les prestations de consultation d'un hôpital via AJAX
     */
    public function getHospitalServices($hospitalId)
    {
        $prestations = \App\Models\Prestation::withoutGlobalScope('hospital_filter')
            ->where('hospital_id', $hospitalId)
            ->where('category', 'consultation')
            ->where('is_active', true)
            ->get()
            ->map(function($prestation) {
                return [
                    'id' => $prestation->id,
                    'name' => $prestation->name,
                    'price' => $prestation->price,
                ];
            });

        return response()->json($prestations);
    }

    public function profile()
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = \App\Models\Hospital::where('is_active', true)->get();
        return view('portal.profile', compact('patient', 'hospitals'));
    }

    public function updateProfile(Request $request)
    {
        $patient = Auth::guard('patients')->user();

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:patients,email,' . $patient->id, 
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['allergies'])) {
            $validated['allergies'] = array_filter(array_map('trim', explode(',', $validated['allergies'])));
        }

        $patient->update($validated);

        return back()->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function medicalHistory()
    {
        $patient = Auth::guard('patients')->user();
        
        // Utiliser la relation vitals() car c'est là que sont stockées les infos médicales partagées
        // FILTRE: On exclut les enregistrements avec doctor_id NULL s'il existe un doublon avec un médecin assigné
        $records = $patient->vitals()
            ->where('is_visible_to_patient', true)
            ->where(function($query) {
                // Inclure uniquement les records avec un doctor_id assigné
                // OU les records où il n'y a pas de doublon avec un médecin assigné
                $query->whereNotNull('doctor_id')
                      ->orWhereNotExists(function($subQuery) {
                          $subQuery->select(\DB::raw(1))
                              ->from('patient_vitals as pv2')
                              ->whereColumn('pv2.patient_ipu', 'patient_vitals.patient_ipu')
                              ->whereRaw('DATE(pv2.created_at) = DATE(patient_vitals.created_at)')
                              ->whereNotNull('pv2.doctor_id');
                      });
            })
            ->with(['doctor', 'service', 'hospital', 'room.doctor'])
            ->latest()
            ->paginate(10);

        return view('portal.medical-history', compact('records'));
    }

    public function prescriptions()
    {
        $patient = Auth::guard('patients')->user();
        
        // 1. Récupérer les ordonnances depuis la table patient_vitals (Ancien système / Résumés)
        $vitalPrescriptions = $patient->vitals()
            ->where('is_visible_to_patient', true)
            ->whereNotNull('ordonnance')
            ->where('ordonnance', '!=', '')
            ->with(['doctor', 'hospital'])
            ->get();

        // 2. Récupérer les ordonnances de la table prescriptions (Nouveau système)
        // On ne montre que la catégorie 'pharmacy' pour une ordonnance officielle
        $realPrescriptions = \App\Models\Prescription::where('patient_id', $patient->id)
            ->where('is_visible_to_patient', true)
            ->where('category', '!=', 'nurse')
            ->with(['doctor', 'hospital'])
            ->get();

        // On merge (ou on traite séparément si la vue est complexe)
        // Pour simplifier ici, on les passe toutes
        $allPrescriptions = $realPrescriptions->concat($vitalPrescriptions)->sortByDesc('created_at');

        // Pagination manuelle
        $page = request()->get('page', 1);
        $perPage = 10;
        $prescriptions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPrescriptions->forPage($page, $perPage),
            $allPrescriptions->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
            
        return view('portal.prescriptions', compact('prescriptions'));
    }

    public function showMedicalRecord($id)
    {
        $patient = Auth::guard('patients')->user();
        
        $record = $patient->vitals()
            ->where('id', $id)
            ->where('is_visible_to_patient', true)
            ->with(['doctor', 'service', 'hospital', 'labRequests', 'room.doctor'])
            ->firstOrFail();

        // Déterminer le médecin de cette consultation
        $consultationDoctorId = $record->doctor_id ?? $record->room?->doctor_id;

        // 1. Récupérer les ordonnances réelles (Table Prescription)
        // IMPORTANT: On prend seulement les ordonnances du MÊME médecin que cette consultation
        $date = $record->created_at->toDateString();
        $realPrescriptions = Prescription::where('patient_id', $patient->id)
            ->where('is_visible_to_patient', true)
            ->where('category', '!=', 'nurse') // Uniquement ordonnance pharmacie
            ->when($consultationDoctorId, function($query) use ($consultationDoctorId) {
                // Filtrer par le médecin de cette consultation
                $query->where('doctor_id', $consultationDoctorId);
            })
            ->where(function($q) use ($date, $record) {
                $q->whereDate('created_at', $date)
                  ->orWhere('created_at', '>=', $record->created_at->subHours(12))
                  ->where('created_at', '<=', $record->created_at->addHours(24));
            })
            ->with(['doctor', 'hospital'])
            ->latest()
            ->get();

        // On ne récupère plus les observations cliniques/journal de soins pour le patient
        $clinicalObservations = collect(); 

        return view('portal.medical-record-show', compact('record', 'realPrescriptions', 'clinicalObservations'));
    }

    public function showAdmission($id)
    {
        $patient = Auth::guard('patients')->user();

        // 1. Récupérer l'admission
        $admission = \App\Models\Admission::withoutGlobalScope('hospital_filter')
            ->where('id', $id)
            ->where('patient_id', $patient->id)
            ->with(['doctor', 'hospital', 'room.service', 'bed'])
            ->firstOrFail();

        // 2. Définir la plage de temps de l'admission (avec tolérance pour le début)
        $startTime = $admission->admission_date->subHours(24);
        $endTime = $admission->discharge_date ?? now();

        // 3. Récupérer TOUS les éléments médicaux durant cette période
        // A. Dossiers de constantes / Visites
        $vitals = $patient->vitals()
            ->where('is_visible_to_patient', true)
            ->whereBetween('created_at', [$startTime, $endTime])
            ->with(['doctor', 'service'])
            ->latest()
            ->get();
            
        // B. Ordonnances (si elles sont sur une table séparée)
        // ... (Logique similaire si besoin, sinon elles sont souvent liées aux vitals)

        // C. Résultats Labo
        // ... (Idem)

        return view('portal.medical-admission-show', compact('admission', 'vitals'));
    }

    public function downloadPrescription($id)
    {
        $patient = Auth::guard('patients')->user();

        // 1. Chercher d'abord dans la table Prescription (Nouveau système)
        // On restreint au type officiel (Pharmacie) uniquement
        $prescription = Prescription::where('id', $id)
            ->where('patient_id', $patient->id)
            ->where('is_visible_to_patient', true)
            ->where('category', '!=', 'nurse') // Sécurité supplémentaire
            ->with(['doctor', 'hospital', 'medecinExterne'])
            ->first();

        if ($prescription) {
            // Tentative de récupération du motif du dossier médical correspondant
            $relatedRecord = $patient->vitals()
                ->whereDate('created_at', '<=', $prescription->created_at->toDateString())
                ->latest()
                ->first();
                
            $motif = $relatedRecord ? $relatedRecord->reason : 'Non spécifié';

            // Vue spécifique pour le modèle Prescription
            return view('portal.pdf_prescription_real', [
                'prescription' => $prescription,
                'motif' => $motif
            ]);
        }

        // 2. Repli vers PatientVital (Ancien système / Note rapide)
        $record = $patient->vitals()
            ->where('id', $id)
            ->where('is_visible_to_patient', true)
            ->with(['doctor', 'service', 'hospital'])
            ->first();

        if (!$record || empty($record->ordonnance)) {
            return back()->with('error', 'Aucune ordonnance trouvée.');
        }

        // Temporarily returning HTML view that can be printed as PDF
        return view('portal.pdf_prescription', compact('record'));
    }

    public function downloadInvoice($id)
    {
        $patient = Auth::guard('patients')->user();
        \Log::info('Tentative de téléchargement de facture', [
            'patient_id' => $patient?->id,
            'invoice_id' => $id
        ]);

        if (!$patient) {
            \Log::error('Patient non authentifié lors du téléchargement de facture');
            abort(401);
        }

        $invoice = Invoice::where('id', $id)->first();
        if (!$invoice) {
            \Log::error('Facture non trouvée en base de données', ['id' => $id]);
            abort(404, 'Facture non trouvée.');
        }

        if ($invoice->patient_id != $patient->id) {
            \Log::warning('Tentative d\'accès à une facture d\'un autre patient', [
                'demandeur_id' => $patient->id,
                'proprietaire_id' => $invoice->patient_id,
                'invoice_id' => $id
            ]);
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette facture.');
        }

        // Return view for PDF
        return view('portal.pdf_invoice', compact('invoice', 'patient'));
    }

    public function invoices()
    {
        $patient = Auth::guard('patients')->user();
        $invoices = Invoice::where('patient_id', $patient->id)->latest()->paginate(10);
        return view('portal.invoices', compact('invoices'));
    }

    public function messaging()
    {
        $patient = Auth::guard('patients')->user();
        $conversations = []; 
        return view('portal.messaging', compact('conversations'));
    }

    public function documents()
    {
        $patient = Auth::guard('patients')->user();
        
        // --- 0. RÉCUPÉRER LES PÉRIODES D'ADMISSION ---
        $admissions = \App\Models\Admission::where('patient_id', $patient->id)->get();

        // Helper pour déterminer si un document a été créé DURANT (ou juste avant) une admission
        $getAdmissionStatus = function($date) use ($admissions) {
            foreach ($admissions as $adm) {
                // On inclut 12h avant l'admission pour capturer les examens faits aux urgences/accueil avant de monter en chambre
                $start = $adm->admission_date->copy()->subHours(12);
                $end = $adm->discharge_date ? $adm->discharge_date->copy()->addHours(6) : now()->addDay(); 
                
                if ($date->between($start, $end)) {
                    return 'Admission';
                }
            }
            return 'Non Admission';
        };

        // --- 1. AGGREGATION DES DOCUMENTS ---
        $allDocs = collect();

        // A. Factures
        $invoices = \App\Models\Invoice::where('patient_id', $patient->id)
            ->with(['appointment.service', 'admission.room.service', 'labRequest.doctor.service'])
            ->latest()
            ->get();

        foreach($invoices as $inv) {
            $serviceName = 'Administration';
            if ($inv->admission && $inv->admission->room && $inv->admission->room->service) {
                $serviceName = $inv->admission->room->service->name;
            } elseif ($inv->appointment && $inv->appointment->service) {
                $serviceName = $inv->appointment->service->name;
            } elseif ($inv->labRequest && $inv->labRequest->doctor && $inv->labRequest->doctor->service) {
                $serviceName = $inv->labRequest->doctor->service->name;
            } elseif ($inv->service) {
                $serviceName = $inv->service->name;
            }

            $context = 'Hôpital';
            if ($inv->appointment && $inv->appointment->consultation_type === 'home') {
                $context = 'Maison';
            }

            // Statut Admission/Non Admission
            $status = $inv->admission_id ? 'Admission' : $getAdmissionStatus($inv->created_at);

            $allDocs->push([
                'id' => 'inv-'.$inv->id,
                'type' => 'Facture',
                'title' => 'Facture #' . $inv->invoice_number,
                'date' => $inv->created_at,
                'service' => $serviceName,
                'context' => $context,
                'status' => $status,
                'download_route' => route('patient.invoices.pdf', $inv->id),
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'text-gray-600'
            ]);
        }

        // B. Ordonnances (Table dédiée)
        $prescriptions = \App\Models\Prescription::where('patient_id', $patient->id)
            ->where('is_visible_to_patient', true)
            ->with(['doctor.service'])
            ->latest()
            ->get();
        
        foreach($prescriptions as $pres) {
            $serviceName = 'Consultation Générale';
            if ($pres->doctor && $pres->doctor->service_id) {
                $srv = \App\Models\Service::find($pres->doctor->service_id);
                if ($srv) $serviceName = $srv->name;
            }

            $allDocs->push([
                'id' => 'pres-'.$pres->id,
                'type' => 'Ordonnance',
                'title' => 'Ordonnance du ' . $pres->created_at->format('d/m/Y'),
                'date' => $pres->created_at,
                'service' => $serviceName,
                'context' => 'Hôpital',
                'status' => $getAdmissionStatus($pres->created_at),
                'download_route' => route('patient.prescriptions.download', $pres->id),
                'icon' => 'fas fa-prescription-bottle-alt',
                'color' => 'text-blue-600'
            ]);
        }

        // C. Ordonnances (Vitals)
        $vitalPrescriptions = $patient->vitals()
            ->whereNotNull('ordonnance')
            ->where('ordonnance', '!=', '')
            ->where('is_visible_to_patient', true)
            ->with(['doctor', 'service'])
            ->get();

        foreach($vitalPrescriptions as $vital) {
            $serviceName = $vital->service ? $vital->service->name : ($vital->doctor && $vital->doctor->service_id ? \App\Models\Service::find($vital->doctor->service_id)->name : 'Consultation Générale');
            
            $status = $vital->related_admission || $getAdmissionStatus($vital->created_at) === 'Admission' ? 'Admission' : 'Non Admission';

            $allDocs->push([
                'id' => 'vital-pres-'.$vital->id,
                'type' => 'Ordonnance',
                'title' => 'Ordonnance (Dossier) du ' . $vital->created_at->format('d/m/Y'),
                'date' => $vital->created_at,
                'service' => $serviceName,
                'context' => 'Hôpital',
                'status' => $status,
                'download_route' => route('patient.prescriptions.download', $vital->id),
                'icon' => 'fas fa-file-medical',
                'color' => 'text-blue-600'
            ]);
        }

        // D. Résultats Labo
        $labRequests = \App\Models\LabRequest::where('patient_ipu', $patient->ipu)
            ->where('is_visible_to_patient', true)
            ->with(['doctor.service']) 
            ->latest()
            ->get();

        foreach ($labRequests as $req) {
            $serviceName = 'Laboratoire';
            if ($req->doctor && $req->doctor->service) {
                $serviceName = $req->doctor->service->name;
            }

            $hasFile = !empty($req->result_file);
            $isAvailable = $hasFile || !empty($req->result);

            $allDocs->push([
                'id' => 'lab-'.$req->id,
                'type' => 'Test',
                'title' => 'Résultat: ' . $req->test_name,
                'date' => $req->created_at,
                'service' => $serviceName,
                'context' => 'Hôpital',
                'status' => $getAdmissionStatus($req->created_at),
                'download_route' => $hasFile ? asset('storage/'.$req->result_file) : null,
                'icon' => 'fas fa-flask',
                'color' => $isAvailable ? 'text-purple-600' : 'text-gray-400'
            ]);
        }

        // E. Documents Uploadés
        $uploadedDocs = $patient->documents()
             ->where('is_visible_to_patient', true)
             ->latest()
             ->get();

        foreach($uploadedDocs as $doc) {
            $allDocs->push([
                'id' => 'doc-'.$doc->id,
                'type' => 'Autre Document',
                'title' => $doc->title,
                'date' => $doc->created_at,
                'service' => 'Divers',
                'context' => 'Hôpital',
                'status' => $getAdmissionStatus($doc->created_at),
                'icon' => 'fas fa-file-alt',
                'color' => 'text-gray-500'
            ]);
        }



        // --- 2. HIERARCHISATION REVISITÉE ---
        /*
        Niveau 1 : Les Services (Couleurs spécifiques)
        Niveau 2 : Le Type de Séjour (Maison vs Hôpital)
        Niveau 3 : L'état du patient (Admission vs Non Admission - Hôpital uniquement)
        Niveau 4 : Les Documents finaux (Factures vs Ordonnances vs Tests)
        */
        
        // Initialisation avec les services dynamiques de la base de données (Pôle Soins uniquement)
        $services = \App\Models\Service::where('type', 'medical')->get();
        
        // Structure de base des dossiers finaux incluant Test
        $baseFolders = ['Facture' => [], 'Ordonnance' => [], 'Test' => []];
        
        $folders = [];
        foreach ($services as $service) {
            $folders[$service->name] = [
                'color' => $service->color ?? 'purple',
                'icon' => $service->icon ?? 'fas fa-folder',
                'children' => [
                    'Hôpital' => [
                        'Admission' => $baseFolders,
                        'Non Admission' => $baseFolders,
                    ],
                    'Maison' => $baseFolders,
                ]
            ];
        }

        foreach($allDocs as $doc) {
            $svc = $doc['service'];
            $env = $doc['context']; // 'Hôpital' or 'Maison'
            $stat = $doc['status']; // 'Admission' or 'Non Admission'
            $type = $doc['type'];   // 'Facture', 'Ordonnance', 'Test'

            // 1. Déterminer si le service correspond à un service en base
            $svcKey = null;
            foreach ($folders as $name => $f) {
                if (mb_stripos($svc, $name) !== false || mb_stripos($name, $svc) !== false) {
                    $svcKey = $name;
                    break;
                }
            }

            // Si pas trouvé dans les dossiers existants (par ex. un nouveau service ou nom différent)
            if (!$svcKey) {
                $svcKey = $svc;
                $folders[$svcKey] = [
                    'color' => 'purple',
                    'children' => [
                        'Hôpital' => [
                            'Admission' => $baseFolders,
                            'Non Admission' => $baseFolders,
                        ],
                        'Maison' => $baseFolders,
                    ]
                ];
            }

            // Normalisation du Type
            $finalType = 'Autre';
            if (stripos($type, 'Facture') !== false) $finalType = 'Facture';
            elseif (stripos($type, 'Ordonnance') !== false) $finalType = 'Ordonnance';
            elseif (stripos($type, 'Test') !== false) $finalType = 'Test';

            // 3 & 4. Rangement
            if ($env === 'Hôpital') {
                // Ensure nesting exists just in case
                if (!isset($folders[$svcKey]['children'][$env][$stat][$finalType])) {
                    $folders[$svcKey]['children'][$env][$stat][$finalType] = [];
                }
                $folders[$svcKey]['children'][$env][$stat][$finalType][] = $doc;
            } else {
                if (!isset($folders[$svcKey]['children'][$env][$finalType])) {
                    $folders[$svcKey]['children'][$env][$finalType] = [];
                }
                $folders[$svcKey]['children'][$env][$finalType][] = $doc;
            }
        }

        return view('portal.documents', compact('folders'));
    }

    public function storeAppointment(Request $request)
    {
        // Pré-traitement pour mapper les champs _home vers les champs standards si besoin
        if ($request->consultation_type === 'home') {
            if ($request->has('service_id_home')) $request->merge(['service_id' => $request->service_id_home]);
            if ($request->has('prestation_id_home')) $request->merge(['prestation_id' => $request->prestation_id_home]);
        }

        $validated = $request->validate([
            'consultation_type' => 'required|in:hospital,home',
            'hospital_id' => 'required|exists:hospitals,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'prestation_id' => 'nullable|exists:prestations,id',
            'doctor_id' => 'nullable|exists:users,id',  // Ajout pour sélection optionnelle du médecin
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'home_address' => 'required_if:consultation_type,home|nullable|string',
            'medecin_externe_id' => 'nullable|exists:medecins_externes,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'calculated_distance' => 'nullable|numeric',
            'calculated_travel_fee' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
        ]);

        $patient = Auth::guard('patients')->user();

        // Mettre à jour la géolocalisation du patient si fournie
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $patient->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'formatted_address' => $request->home_address
            ]);
        }

        // Combiner date et heure
        $appointmentDateTime = $validated['appointment_date'] . ' ' . $validated['appointment_time'];

        $serviceId = $validated['service_id'];
        $prestationId = $validated['prestation_id'];

        // === LOGIQUE D'ASSIGNATION DU MÉDECIN ===
        $assignedDoctorId = $validated['doctor_id'] ?? null;

        // Créer le rendez-vous
        $appointment = Appointment::create([
            'patient_id' => Auth::guard('patients')->id(),
            'hospital_id' => $validated['hospital_id'],
            'service_id' => $serviceId,
            'doctor_id' => $assignedDoctorId, // Peut être null si non sélectionné ou si externe
            'medecin_externe_id' => $validated['medecin_externe_id'] ?? null,
            'appointment_datetime' => $appointmentDateTime,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending', 
            'consultation_type' => $validated['consultation_type'],
            'home_address' => $validated['home_address'] ?? null,
            'calculated_distance_km' => $validated['calculated_distance'] ?? null,
            'calculated_travel_fee' => $validated['calculated_travel_fee'] ?? null,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'total_amount' => $validated['total_amount'] ?? 0,
        ]);

        // Attach the prestation if selected
        // Attach the prestation if selected
        if ($prestationId) {
            $prestation = \App\Models\Prestation::find($prestationId);
            if ($prestation) {
                $appointment->prestations()->attach($prestationId, [
                    'quantity' => 1,
                    'unit_price' => $prestation->price,
                    'total' => $prestation->price,
                    'added_at' => now(),
                ]);
            }
        }

        // ON NE CRÉE PLUS LE DOSSIER MÉDICAL ICI.
        // Il sera créé par l'infirmier après le paiement.

        return redirect()->route('patient.appointments.confirmation', $appointment->id)
            ->with('success', 'Votre demande de rendez-vous a été enregistrée avec succès.');
    }


    public function cancelAppointment(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();

        // Vérifier que le rendez-vous appartient au patient
        if ((int)$appointment->patient_id !== (int)$patient->id) {
            \Log::warning('Tentative d\'annulation de RDV non autorisé (403)', [
                'rdv_id' => $appointment->id,
                'patient_rdv_id' => $appointment->patient_id,
                'patient_session_id' => $patient->id
            ]);
            abort(403, 'Vous n\'êtes pas autorisé à annuler ce rendez-vous.');
        }

        // Vérifier que le rendez-vous peut être annulé (pas déjà passé et pas déjà annulé)
        if ($appointment->appointment_datetime <= now() || $appointment->status === 'cancelled') {
            return back()->with('error', 'Ce rendez-vous ne peut pas être annulé.');
        }

        // Annuler le rendez-vous
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Votre rendez-vous a été annulé avec succès.');
    }

    /**
     * Suivre le déplacement du médecin en temps réel
     */
    public function trackAppointment(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();

        if ($appointment->patient_id !== $patient->id) {
            abort(403);
        }

        if ($appointment->consultation_type !== 'home') {
            return redirect()->route('patient.appointments')->with('error', 'Le suivi n\'est disponible que pour les consultations à domicile.');
        }

        $appointment->load(['medecinExterne', 'patient']);

        return view('portal.track-appointment', compact('appointment'));
    }

    /**
     * API pour récupérer les données de suivi en temps réel
     */
    public function getTrackingData(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();

        if ($appointment->patient_id !== $patient->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json([
            'status' => $appointment->status,
            'doctor_location' => [
                'lat' => (float)$appointment->doctor_current_latitude,
                'lng' => (float)$appointment->doctor_current_longitude,
            ],
            'estimated_arrival_time' => $appointment->estimated_arrival_time ? $appointment->estimated_arrival_time->format('H:i') : null,
            'distance_km' => $appointment->calculated_distance_km,
        ]);
    }

    public function confirmStart(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();
        if ($appointment->patient_id !== $patient->id) abort(403);
        
        $appointment->update([
            'patient_confirmation_start_at' => now(),
        ]);
        
        return back()->with('success', 'Début de consultation confirmé.');
    }

    public function confirmEnd(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();
         if ($appointment->patient_id !== $patient->id) abort(403);

        $appointment->update([
            'patient_confirmation_end_at' => now(),
            'status' => 'completed' 
        ]);
        
        return back()->with('success', 'Fin de consultation confirmée.');
    }

    public function confirmPayment(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();
        if ($appointment->patient_id !== $patient->id) abort(403);

        // Ensure consultation is completed and not yet paid
        if (!$appointment->patient_confirmation_end_at || $appointment->payment_transaction_id) {
            return back()->with('error', 'Paiement non autorisé pour ce rendez-vous.');
        }

        // Update payment status (using a placeholder transaction ID until actual integration)
        $transactionId = 'MANUAL_' . strtoupper(uniqid());
        $appointment->update([
            'payment_transaction_id' => $transactionId,
            'payment_method' => 'mobile_money',
        ]);

        // Send notification to the external doctor
        if ($appointment->medecinExterne) {
            $appointment->medecinExterne->notify(
                new \App\Notifications\PaymentConfirmedNotification(
                    $appointment,
                    $patient->full_name,
                    $appointment->total_amount ?? 0
                )
            );
        }

        // Send notification to SuperAdmin (assuming User with role 'admin' or 'super_admin')
        $superAdmins = \App\Models\User::where('role', 'admin')->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(
                new \App\Notifications\PaymentConfirmedNotification(
                    $appointment,
                    $patient->full_name,
                    $appointment->total_amount ?? 0
                )
            );
        }
        
        return back()->with('success', 'Paiement confirmé ! Le médecin et l\'administration ont été notifiés.');
    }

    public function rateDoctor(Request $request, Appointment $appointment)
    {
         $patient = Auth::guard('patients')->user();
         if ($appointment->patient_id !== $patient->id) abort(403);
         
         $validated = $request->validate([
             'rating' => 'required|integer|min:1|max:5',
             'comment' => 'nullable|string|max:500'
         ]);
         
         $appointment->update([
             'rating_stars' => $validated['rating'],
             'rating_comment' => $validated['comment']
         ]);
         
         return back()->with('success', 'Merci pour votre avis !');
    }

    /**
     * Afficher la page de confirmation et de paiement après la prise de rendez-vous.
     */
    public function showConfirmation(Appointment $appointment)
    {
        // Vérifier que le rendez-vous appartient au patient connecté
        if ((int)$appointment->patient_id !== (int)Auth::guard('patients')->id()) {
            \Log::warning('Accès au RDV refusé (403)', [
                'rdv_id' => $appointment->id,
                'patient_rdv_id' => $appointment->patient_id,
                'patient_session_id' => Auth::guard('patients')->id()
            ]);
            abort(403);
        }

        $appointment->load(['hospital', 'service', 'prestations', 'medecinExterne', 'doctor']);

        // Récupérer les paramètres de paiement (numéros + QR Codes) spécifiques à l'hôpital
        $hospital = $appointment->hospital;
        $paymentSettings = [
            'payment_orange_money_number' => $hospital->payment_orange_number,
            'payment_mtn_money_number' => $hospital->payment_mtn_number,
            'payment_moov_money_number' => $hospital->payment_moov_number,
            'payment_wave_number' => $hospital->payment_wave_number,
            'payment_qr_orange' => $hospital->payment_qr_orange,
            'payment_qr_mtn' => $hospital->payment_qr_mtn,
            'payment_qr_moov' => $hospital->payment_qr_moov,
            'payment_qr_wave' => $hospital->payment_qr_wave,
        ];

        // Fallback optionnel : si l'hôpital n'a rien configuré, on peut charger les settings globaux
        // Mais ici l'utilisateur demande explicitement ceux de l'admin de l'hôpital.
        if (empty(array_filter($paymentSettings))) {
            $globalSettings = Setting::where('group', 'payment')->pluck('value', 'key')->toArray();
            $paymentSettings = array_merge($globalSettings, array_filter($paymentSettings));
        }

        return view('portal.appointment-confirmation', compact('appointment', 'paymentSettings'));
    }

    /**
     * Télécharger le bon de consultation / facture proforma en PDF.
     */
    public function downloadAppointmentBill(Appointment $appointment)
    {
        // Vérifier que le rendez-vous appartient au patient connecté
        if ($appointment->patient_id !== Auth::guard('patients')->id()) {
            abort(403);
        }

        // Restriction : la facture n'est téléchargeable qu'après paiement (transaction ID présent)
        if (!$appointment->payment_transaction_id) {
            return back()->with('error', 'Le reçu de paiement sera disponible une fois le règlement validé.');
        }

        $appointment->load(['hospital', 'service', 'prestations', 'medecinExterne', 'doctor', 'patient']);

        $pdf = Pdf::loadView('portal.pdf_appointment_bill', [
            'appointment' => $appointment,
            'hospital' => $appointment->hospital,
            'patient' => $appointment->patient,
        ]);

        return $pdf->download('recu-paiement-' . $appointment->id . '.pdf');
    }
}
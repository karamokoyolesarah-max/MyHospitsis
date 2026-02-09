<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, MedicalRecord, Prescription, Invoice, PatientVital};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    public function bookAppointment()
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = \App\Models\Hospital::where('is_active', true)->get();

        $hospitalsData = [];

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
                ]);

            $prestations = \App\Models\Prestation::withoutGlobalScope('hospital_filter')
                ->where('hospital_id', $hospital->id)
                ->where('is_active', true)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price ?? 0,
                    'service_id' => $p->service_id
                ]);

            $hospitalsData[$hospital->id] = [
                'services' => $services,
                'prestations' => $prestations,
                'address' => $hospital->address
            ];
        }

        return view('portal.book-appointment', compact('patient', 'hospitals', 'hospitalsData'));
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
        $prescriptions = $realPrescriptions->concat($vitalPrescriptions)->sortByDesc('created_at');
            
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
            ->with(['doctor', 'hospital'])
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
        $invoice = Invoice::where('patient_id', $patient->id)->findOrFail($id);
        
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
        ]);

        $patient = Auth::guard('patients')->user();

        // Combiner date et heure
        $appointmentDateTime = $validated['appointment_date'] . ' ' . $validated['appointment_time'];

        $serviceId = $validated['service_id'];
        $prestationId = $validated['prestation_id'];

        // === LOGIQUE D'ASSIGNATION DU MÉDECIN ===
        $assignedDoctorId = $validated['doctor_id'] ?? null;

        // On crée le rendez-vous. Si doctor_id est null, il restera en attente d'approbation.
        $appointment = Appointment::create([
            'patient_id' => Auth::guard('patients')->id(),
            'hospital_id' => $validated['hospital_id'],
            'service_id' => $serviceId,
            'doctor_id' => $assignedDoctorId,
            'appointment_datetime' => $appointmentDateTime,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending', // Toujours 'pending' au début pour nécessiter approbation
            'consultation_type' => $validated['consultation_type'],
            'home_address' => $validated['consultation_type'] === 'home' ? $validated['home_address'] : null,
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
            'added_at' => now(), // <--- AJOUTE CETTE LIGNE ICI
        ]);
    }
}

        // ON NE CRÉE PLUS LE DOSSIER MÉDICAL ICI.
        // Il sera créé par l'infirmier après le paiement.

        return redirect()->route('patient.appointments')
            ->with('success', 'Votre demande de rendez-vous a été enregistrée. Vous serez contacté pour confirmation.');
    }


    public function cancelAppointment(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();

        // Vérifier que le rendez-vous appartient au patient
        if ($appointment->patient_id !== $patient->id) {
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
}
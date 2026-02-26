<?php

namespace App\Http\Controllers;

use App\Models\{Patient, Admission, Appointment, MedicalRecord, Prescription, ClinicalObservation, AuditLog, Hospital};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('ipu', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtres
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $patients = $query->latest()->paginate(20);

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Homme,Femme,Other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        // Convert allergies string to array if present
        if (isset($validated['allergies']) && is_string($validated['allergies'])) {
            $validated['allergies'] = array_map('trim', explode(',', $validated['allergies']));
        }

        // Add hospital_id from authenticated user
        $validated['hospital_id'] = auth()->user()->hospital_id;

        DB::beginTransaction();
        try {
            // Génération de l'IPU unique
            $validated['ipu'] = Patient::generateIpu();

            $patient = Patient::create($validated);

            // Journalisation
            AuditLog::log('create', 'Patient', $patient->id, [
                'description' => 'Création du dossier patient',
                'new' => $patient->toArray()
            ]);

            DB::commit();

            return redirect()->route('patients.show', $patient)
                           ->with('success', 'Patient créé avec succès. IPU: ' . $patient->ipu);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création du patient.']);
        }
    }

    public function show($id)
    {
        // On récupère le patient même s'il est supprimé (withTrashed)
        // et on ignore les scopes globaux pour être sûr de le trouver (withoutGlobalScopes)
        $patient = \App\Models\Patient::withTrashed()->withoutGlobalScopes()->findOrFail($id);

        // Charger les patientVitals ACTIFS UNIQUEMENT (non archivés) pour l'historique
        $patientVitals = \App\Models\PatientVital::where('patient_ipu', $patient->ipu)
            ->where(function($q) {
                // Filtrer uniquement les dossiers actifs (non archivés)
                $q->where('status', '!=', 'archived')
                  ->orWhereNull('status');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Déterminer la date minimale des PatientVitals actifs (pour filtrer les prescriptions et analyses)
        $minActiveDate = $patientVitals->min('created_at');
        
        // Si aucun PatientVital actif, utiliser le début de journée pour voir ce qui a été fait aujourd'hui
        if (!$minActiveDate) {
            $minActiveDate = today();
        }

        // Charger les observations cliniques (la table n'a pas de colonne status)
        // On les filtre par date pour ne montrer que celles du parcours actuel
        $patient->load(['clinicalObservations' => function($query) use ($minActiveDate) {
            $query->where('created_at', '>=', $minActiveDate)
                ->orderBy('observation_datetime', 'desc');
        }, 'clinicalObservations.user', 
        'labRequests' => function($query) use ($minActiveDate) {
            // Filtrer les analyses par date pour le parcours actuel
            $query->where('created_at', '>=', $minActiveDate)
                ->orderBy('created_at', 'desc');
        }, 'labRequests.doctor', 'labRequests.biologist', 'labRequests.labTechnician',
        'prescriptions' => function($query) use ($minActiveDate) {
            // Filtrer les prescriptions par date pour le parcours actuel
            $query->where('created_at', '>=', $minActiveDate)
                ->orderBy('created_at', 'desc');
        }, 'prescriptions.doctor']);

        // Fusionner uniquement les consultations (vitals) et observations cliniques ACTIVES pour la timeline
        $allExams = $patientVitals->concat($patient->clinicalObservations)
                                  ->sortByDesc(function($item) {
            return $item->observation_datetime ?? $item->created_at;
        });

        return view('patients.show', compact('patient', 'allExams', 'patientVitals'));
    }

    public function edit(Patient $patient)
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('patients.edit', compact('patient', 'hospitals'));
    }

     public function update(Request $request, Patient $patient)
{
    \Log::info('Patient update method called', [
        'patient_id' => $patient->id,
        'request_data' => $request->all()
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'dob' => 'nullable|date|before:today',
        'gender' => 'nullable|in:Homme,Femme,Other',
        'hospital_id' => 'nullable|exists:hospitals,id',
        'address' => 'nullable|string|max:500',
        'city' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'blood_group' => 'nullable|string|max:5',
        'allergies' => 'nullable|string',
        'medical_history' => 'nullable|string',
        'is_active' => 'boolean',
    ]);

    // Convert allergies string to array if present
    if (isset($validated['allergies']) && is_string($validated['allergies'])) {
        $validated['allergies'] = array_map('trim', explode(',', $validated['allergies']));
    }

    DB::beginTransaction();
    try {
        $oldData = $patient->toArray();

        $patient->update($validated);

        // Journalisation
        AuditLog::log('update', 'Patient', $patient->id, [
            'description' => 'Mise à jour du dossier patient (Coordonnées & Allergies)',
            'old' => $oldData,
            'new' => $patient->toArray()
        ]);

        DB::commit();

        // On redirige vers l'onglet coordonnées spécifiquement après l'update
        return redirect()->to(route('patients.show', $patient) . '#tab-coords')
                         ->with('success', 'Dossier mis à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
    }
   }

    public function destroy(Patient $patient)
    {
        // Soft delete
        DB::beginTransaction();
        try {
            AuditLog::log('delete', 'Patient', $patient->id, [
                'description' => 'Suppression (soft) du dossier patient',
                'old' => $patient->toArray()
            ]);

            $patient->delete();

            DB::commit();

            return redirect()->route('patients.index')
                           ->with('success', 'Patient supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression.']);
        }
    }

    public function medicalFile(Patient $patient)
    {
        // Dossier médical complet (DPI)
        $user = auth()->user();

        // Vérification des permissions (seuls les médecins et infirmiers du même service)
        if (!$user->isAdmin() && !$user->isDoctor() && !$user->isNurse()) {
            abort(403, 'Accès non autorisé au dossier médical.');
        }

        // Charger toutes les données médicales
        $patient->load([
            'medicalRecords' => fn($q) => $q->with('recordedBy')->latest(),
            'prescriptions' => fn($q) => $q->with('doctor')->latest(),
            'clinicalObservations' => fn($q) => $q->with('user')->latest('observation_datetime'),
            'documents' => fn($q) => $q->where('is_validated', true)->latest(),
        ]);

        // Journalisation de l'accès
        AuditLog::log('view', 'Patient', $patient->id, [
            'description' => 'Consultation du dossier médical complet',
        ]);

        return view('patients.medical-file', compact('patient'));
    }

    public function quickSearch(Request $request)
    {
        // Recherche AJAX rapide pour l'autocomplétion
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('ipu', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'ipu', 'name', 'first_name', 'dob', 'phone']);

        return response()->json($patients->map(function($patient) {
            return [
                'id' => $patient->id,
                'label' => $patient->full_name . ' (' . $patient->ipu . ')',
                'ipu' => $patient->ipu,
                'name' => $patient->full_name,
                'dob' => $patient->dob->format('d/m/Y'),
                'age' => $patient->age,
            ];
        }));
    }
    public function archive($id)
    {
        $patient = \App\Models\Patient::withTrashed()->withoutGlobalScopes()->findOrFail($id);
    DB::beginTransaction();
    try {
        // 1. Récupérer TOUS les PatientVitals actifs (non archivés)
        $activeVitals = \App\Models\PatientVital::where('patient_ipu', $patient->ipu)
            ->where(function($q) {
                $q->where('status', '!=', 'archived')
                  ->orWhereNull('status');
            })
            ->get();
        
        if ($activeVitals->isEmpty()) {
            return back()->with('info', 'Aucune donnée active à archiver.');
        }
        
        // 2. Déterminer la plage de dates des données actives
        $minDate = $activeVitals->min('created_at');
        $maxDate = $activeVitals->max('created_at');
        
        // 3. PARTAGE AUTOMATIQUE : Marquer toutes les prescriptions comme visibles au patient
        \App\Models\Prescription::where('patient_id', $patient->id)
            ->whereBetween('created_at', [$minDate, $maxDate])
            ->update(['is_visible_to_patient' => true]);
        
        // 4. PARTAGE AUTOMATIQUE : Marquer toutes les analyses comme visibles au patient
        \App\Models\LabRequest::where('patient_ipu', $patient->ipu)
            ->whereBetween('created_at', [$minDate, $maxDate])
            ->update(['is_visible_to_patient' => true]);
        
        // 5. PARTAGE AUTOMATIQUE : Marquer les PatientVitals comme visibles
        $activeVitals->each(fn($v) => $v->update(['is_visible_to_patient' => true]));
        
        // 6. Marquer les PatientVitals comme archivés
        $activeVitals->each(fn($v) => $v->update(['status' => 'archived']));
        
        // 7. MISE À JOUR DU STATUT DU RENDEZ-VOUS
        // On cherche le RDV actif le plus récent pour ce patient (aujourd'hui ou hier)
        \App\Models\Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['confirmed', 'paid', 'prepared', 'scheduled'])
            ->whereDate('appointment_datetime', '>=', now()->subDays(1)->toDateString())
            ->whereDate('appointment_datetime', '<=', now()->toDateString())
            ->update(['status' => 'completed']);

        // Journalisation
        AuditLog::log('archive', 'Patient', $patient->id, [
            'description' => 'Archivage du dossier patient, partage automatique et clôture du rendez-vous',
            'vitals_archived' => $activeVitals->count(),
        ]);

        DB::commit();

        // On redirige vers le dashboard du médecin avec un message
        return redirect()->route('medecin.dashboard')
                         ->with('success', 'Le dossier de ' . $patient->name . ' a été archivé et partagé avec le patient.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Erreur lors de l\'archivage : ' . $e->getMessage()]);
    }
 } 

 public function showArchives($id)
 {
    $patient = \App\Models\Patient::withTrashed()->withoutGlobalScopes()->findOrFail($id);
    // Récupérer TOUS les PatientVitals archivés du patient
    $archivedVitals = \App\Models\PatientVital::where('patient_ipu', $patient->ipu)
        ->where('status', 'archived')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Si aucune donnée archivée, rediriger
    if ($archivedVitals->isEmpty()) {
        return redirect()->route('medical_records.archives')
            ->with('info', 'Aucun dossier archivé trouvé pour ce patient.');
    }
    
    // Déterminer la date minimale et maximale de l'ensemble archivé
    $minArchiveDate = $archivedVitals->min('created_at');
    $maxArchiveDate = $archivedVitals->max('created_at');
    
    // Charger toutes les données associées à cette période archivée
    $patient->load([
        'prescriptions' => function($query) use ($minArchiveDate, $maxArchiveDate) {
            $query->whereBetween('created_at', [$minArchiveDate, $maxArchiveDate])
                  ->orderBy('created_at', 'desc');
        },
        'prescriptions.doctor',
        'labRequests' => function($query) use ($minArchiveDate, $maxArchiveDate) {
            $query->whereBetween('created_at', [$minArchiveDate, $maxArchiveDate])
                  ->orderBy('created_at', 'desc');
        },
        'labRequests.doctor',
        'clinicalObservations' => function($query) use ($minArchiveDate, $maxArchiveDate) {
            $query->whereBetween('created_at', [$minArchiveDate, $maxArchiveDate])
                  ->orderBy('observation_datetime', 'desc');
        },
        'clinicalObservations.user'
    ]);
    
    // Fusionner les vitals et observations pour la timeline
    $allExams = $archivedVitals->concat($patient->clinicalObservations)
                              ->sortByDesc(function($item) {
        return $item->observation_datetime ?? $item->created_at;
    });
    
    return view('patients.archives', compact('patient', 'archivedVitals', 'allExams'));
 }
}

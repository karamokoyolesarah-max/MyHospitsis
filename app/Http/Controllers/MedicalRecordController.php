<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientVital;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalRecordController extends Controller
{
    /**
     * Affiche uniquement les patients en attente (onglet Dossiers médicaux)
     */
    public function index()
    {
        $user = auth()->user();
        $query = PatientVital::where(function($q) {
            $q->whereIn('status', ['active', 'consulting'])
              ->orWhereNull('status');
        })
        ->where('status', '!=', 'admitted')
        ->where('hospital_id', $user->hospital_id);

        // Si c'est un médecin, il ne voit que ses dossiers ou ceux de son service non assignés
        if ($user->role === 'doctor' || $user->role === 'internal_doctor') {
            $query->where(function($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->orWhere(function($sub) use ($user) {
                      $sub->whereNull('doctor_id')
                          ->where('service_id', $user->service_id);
                  });
            });
        }

        $records = $query->with(['doctor', 'patient', 'service'])->orderBy('created_at', 'desc')->get();

        return view('medical_records.index', compact('records'));
    }

    /**
     * Affiche uniquement les patients terminés (onglet Archives)
     */
    public function archivesIndex()
    {
        $records = PatientVital::where('status', 'archived')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('patient_vitals')
                    ->where('status', 'archived')
                    ->groupBy('patient_ipu');
            })
            ->with(['doctor', 'patient', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('medical_records.index', compact('records'))->with('is_archive', true);
    }

    public function show($id)
    {
        $record = PatientVital::with(['patient', 'service'])->findOrFail($id);

        // UPDATE STATUS: Si le statut est "active" (En attente), on le passe en "consulting" (En cours)
        // cela permet à l'infirmière de voir le badge Bleu
        if ($record->status === 'active') {
            $record->update(['status' => 'consulting']);
        }
    
    $patientVitals = PatientVital::where('patient_ipu', $record->patient_ipu)
        ->orderBy('created_at', 'desc')
        ->get();

    // --- AJOUTEZ CES DEUX LIGNES ---
    // FILTRE PAR SERVICE : On ne montre que les chambres du service du médecin/infirmier ou du dossier
    $user = auth()->user();
    // Pour les médecins de labo (qui n'ont pas de lits), on utilise le service du dossier
    if (in_array($user->role, ['doctor_lab', 'lab_technician', 'admin', 'superadmin'])) {
        $targetServiceId = $record->service_id;
    } else {
        $targetServiceId = $user->service_id ?? $record->service_id;
    }
    
    $rooms = Room::where('is_active', true)
        ->when($targetServiceId, function($q) use ($targetServiceId) {
            $q->where('service_id', $targetServiceId);
        })
        ->get();

    $availableBeds = Bed::with('room')
        ->where('is_available', true)
        ->whereNotNull('room_id')
        ->whereHas('room', function($q) use ($targetServiceId) {
            $q->where('is_active', true);
            if ($targetServiceId) {
                $q->where('service_id', $targetServiceId);
            }
        })
        ->get();
    // -------------------------------
    // Ajoutez 'rooms' et 'availableBeds' au compact
    return view('medical_records.show', compact('record', 'patientVitals', 'rooms', 'availableBeds'));
}
    /**
     * Affiche le formulaire d'édition d'un dossier médical
     */
    public function edit($id)
    {
        $record = PatientVital::findOrFail($id);

        return view('medical_records.edit', compact('record'));
    }

    /**
 * Met à jour le dossier avec les vraies valeurs saisies par l'infirmier
 */
public function update(Request $request, $id)
{
    $record = PatientVital::findOrFail($id);

    // On valide que les données arrivent bien du formulaire
    $validatedData = $request->validate([
        'temperature'    => 'required|numeric', // numeric pour éviter le texte
        'blood_pressure' => 'required|string',
        'pulse'          => 'required|numeric',
        'weight'         => 'nullable|numeric',
        'height'         => 'nullable|numeric',
        'reason'         => 'required|string',
        'observations'   => 'nullable|string',
        'ordonnance'     => 'nullable|string',
        'is_visible_to_patient' => 'boolean',
        'custom_vitals'  => 'nullable|array',
    ]);

    // ÉTAPE CRUCIALE : On écrase les anciennes données (le fameux 37°C)
    // par ce que l'infirmier a tapé ($validatedData)
    if ($request->has('meta')) {
        $record->update(['meta' => $request->meta]);
    }
    
    $record->update($validatedData);

    // REMOVED: Auto-creation of duplicate PatientVital was creating unwanted records
    // The $record itself is already a PatientVital entry, no need to duplicate it

    return redirect()->back()->with('success', 'Les constantes réelles ont été transmises !');
}

    /**
     * Archive un dossier médical
     */
    public function archive($id)
    {
        $record = PatientVital::findOrFail($id);

        // Archive tous les dossiers du même patient
        PatientVital::where('patient_name', $record->patient_name)
             ->where('patient_ipu', $record->patient_ipu)
            ->update(['status' => 'archived']);

        // Mettre à jour le rendez-vous lié en statut 'completed' pour afficher "Terminé" au patient
        \App\Models\Appointment::whereHas('patient', function($query) use ($record) {
            $query->where('ipu', $record->patient_ipu);
        })
        ->where('status', 'confirmed')
        ->where('appointment_datetime', '>=', now()->subDays(1))
        ->update(['status' => 'completed']);

        return redirect()->route('medical_records.index')
            ->with('success', 'Le dossier a été clôturé et transféré aux archives.');
    }

    /**
     * Admet un patient à l'hôpital
     */
    public function admit(Request $request, $id)
{
    $request->validate([
        'bed_id'  => 'required|exists:beds,id',
    ]);

    DB::transaction(function () use ($request, $id) {
        $record = PatientVital::findOrFail($id);

        if ($record->status === 'admitted') {
            throw new \Exception('Le patient est déjà hospitalisé.');
        }

        // ... (Ton code de création de patient reste identique) ...
        // ... (Ton code de création de patient reste identique) ...
        // CORRECTIF : Recherche globale pour éviter "Duplicate entry"
        $patient = Patient::withoutGlobalScopes()->where('ipu', $record->patient_ipu)->first();
        
        if ($patient) {
            // Si le patient est supprimé (soft delete), on le restaure
            if (method_exists($patient, 'trashed') && $patient->trashed()) {
                $patient->restore();
            }
            // IMPORTANT : Si le patient existe dans un AUTRE hôpital, on pourrait le réaffecter ici
            // Mais pour l'instant, on s'assure juste de ne pas planter.
        }

        if (!$patient) {
            $nameParts = explode(' ', $record->patient_name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? $nameParts[0];

            $patient = Patient::create([
                'first_name' => $firstName,
                'name' => $lastName,
                'ipu' => $record->patient_ipu,
                'hospital_id' => $record->hospital_id,
                'service_id' => $record->service_id,
                'referring_doctor_id' => auth()->id(),
                'dob' => now()->subYears(30)->toDateString(),
                'gender' => 'Homme',
                'phone' => '0000000000',
            ]);
        }

        // 1. Vérifier la disponibilité du lit et récupérer la chambre
        $bed = \App\Models\Bed::findOrFail($request->bed_id);
        $room = $bed->room;

        if ($bed->is_available == false) {
            throw new \Exception('Le lit sélectionné n\'est plus disponible.');
        }

        // 2. Créer l'admission avec le lit
        Admission::create([
            'hospital_id' => $record->hospital_id,
            'patient_id' => $patient->id,
            'room_id' => $room->id,
            'bed_id' => $bed->id,
            'doctor_id' => auth()->id(),
            'admission_date' => now(),
            'admission_type' => 'emergency',
            'status' => 'active',
            'admission_reason' => $record->reason,
        ]);

        // 3. Mettre à jour les statuts
        $record->update(['status' => 'admitted']);

        $bed->update(['is_available' => false]); // Le lit est maintenant occupé

        // Optionnel : ne marquer la chambre occupée que si elle est pleine ?
        // Sinon, on garde ton code actuel :
        $room->update([
            'status' => 'occupied',
            'patient_vital_id' => $record->id,
        ]);
    });

    return redirect()->route('medical_records.index')
        ->with('success', 'Le patient a été admis et le lit a été réservé.');
}
    public function discharge($id)
    {
        DB::transaction(function () use ($id) {
            $admission = Admission::findOrFail($id);

            // Vérifier que l'admission est active
            if ($admission->status !== 'active') {
                throw new \Exception('Le patient n\'est pas hospitalisé.');
            }

            // Trouver la chambre occupée par ce patient
            $room = $admission->room;
            if ($room) {
                // Libérer la chambre
                $room->update([
                    'status' => 'available',
                    'patient_vital_id' => null,
                ]);
            }

            // Libérer le lit
            $bed = $admission->bed;
            if ($bed) {
                $bed->update(['is_available' => true]);
            }

            // Mettre à jour l'admission
            $admission->update([
                'status' => 'discharged',
                'discharge_date' => now(),
            ]);

            // Archiver le dossier médical (PatientVital)
            // Trouver le PatientVital lié à cette admission via le patient
            $patientVital = PatientVital::where('patient_ipu', $admission->patient->ipu ?? null)
                ->where('status', 'admitted')
                ->first();
            if ($patientVital) {
                $patientVital->update(['status' => 'archived']);
            }
        });

        return redirect()->route('medecin.dashboard')
            ->with('success', 'Le patient a été sorti avec succès.');
    }

    /**
     * Partager TOUT le dossier au patient
     */
    public function share(Request $request, $id)
    {
        // On cherche le patient soit par son ID directement, soit via une fiche PatientVital
        $patient = Patient::find($id);
        
        if (!$patient) {
            $record = PatientVital::find($id);
            if ($record) {
                $patient = Patient::where('ipu', $record->patient_ipu)->first();
            }
        }

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient non trouvé.');
        }

        // 1. Partager les constantes / fiches PatientVital
        PatientVital::where('patient_ipu', $patient->ipu)->update(['is_visible_to_patient' => true]);

        // 2. Partager les ClinicalObservations
        \App\Models\ClinicalObservation::where('patient_id', $patient->id)->update(['is_visible_to_patient' => true]);

        // 3. Partager les Prescriptions (Ordonnances)
        \App\Models\Prescription::where('patient_id', $patient->id)->update(['is_visible_to_patient' => true]);

        // 4. Partager les LabRequests
        \App\Models\LabRequest::where('patient_ipu', $patient->ipu)->update(['is_visible_to_patient' => true]);

        // 5. Partager les Documents
        \App\Models\MedicalDocument::where('patient_id', $patient->id)->update(['is_visible_to_patient' => true]);

        return redirect()->back()->with('success', 'Le dossier complet a été partagé au patient avec succès.');
    }

    /**
     * Supprimer un dossier médical
     */
    public function destroy($id)
    {
        $record = PatientVital::findOrFail($id);

        // Supprimer le dossier
        $record->delete();

        return redirect()->back()
            ->with('success', 'Le dossier médical a été supprimé avec succès.');
    }
    public function downloadPdf($id)
{
    $record = PatientVital::with(['patient', 'user', 'service'])->findOrFail($id);
    
    // Si l'utilisateur est un médecin externe, on récupère ses infos
    $doctor = $record->user;
    
    // Si ce n'est pas un utilisateur classique, on cherche si c'est un externe lié via le patient/RDV
    // (Note: Dans une version future, on pourra ajouter medecin_externe_id direct sur patient_vitals)
    if (!$doctor && Auth::guard('medecin_externe')->check()) {
        $doctor = Auth::guard('medecin_externe')->user();
    }
    
    $pdf = Pdf::loadView('pdf.medical_record_pdf', compact('record', 'doctor'));
    
    $filename = str_replace(' ', '_', $record->patient_name) . '_' . date('Ymd') . '.pdf';
    return $pdf->download($filename);
}
}

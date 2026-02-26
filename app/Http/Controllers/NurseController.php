<?php

namespace App\Http\Controllers;

use App\Models\PatientVital;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Admission; // Ajouté pour la clarté
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NurseController extends Controller
{
    /**
     * Affiche le tableau de bord de l'infirmier
     */
    public function index()
    {
        $user = auth()->user();

        // 1. On récupère les RDV PAYÉS (Peu importe la date, tant qu'ils ne sont pas encore préparés)
        $allTodayPaid = Appointment::with(['patient' => function($q) {
                $q->withTrashed()->withoutGlobalScopes();
            }, 'doctor', 'invoices', 'service', 'prestations'])
            ->where('hospital_id', $user->hospital_id)
            ->where('service_id', $user->service_id)
            ->where('status', 'paid')
            ->where(function($q) {
                // On prend ceux prévus aujourd'hui OU ceux payés aujourd'hui (peu importe leur date prévue)
                $q->whereDate('appointment_datetime', now()->toDateString())
                  ->orWhereDate('updated_at', now()->toDateString());
            })
            ->get();

        $appointments = $allTodayPaid->where('type', '!=', 'walk-in');
        $walkIns = $allTodayPaid->where('type', 'walk-in');

        // 2. Historique des dossiers envoyés (Aujourd'hui)
        // 2. Historique des dossiers envoyés (Aujourd'hui)
        // FILTRE: On exclut les doublons avec doctor_id NULL s'il existe une version assignée
        $sentFiles = PatientVital::with('doctor')
            ->where('hospital_id', $user->hospital_id)
            ->where('service_id', $user->service_id) // Strictement mon service
            ->whereDate('created_at', now()->toDateString()) // Voir tous ceux d'aujourd'hui
            ->where(function($query) {
                $query->whereNotNull('doctor_id')
                      ->orWhereNotExists(function($subQuery) {
                          $subQuery->select(\DB::raw(1))
                              ->from('patient_vitals as pv2')
                              ->whereColumn('pv2.patient_ipu', 'patient_vitals.patient_ipu')
                              ->whereRaw('DATE(pv2.created_at) = DATE(patient_vitals.created_at)')
                              ->whereNotNull('pv2.doctor_id');
                      });
            })
            ->latest()
            ->get();

        // 3. Patients de l'hôpital SANS RDV aujourd'hui
        $patientIdsToCheck = $allTodayPaid->pluck('patient_id')->unique()->toArray();
        $sentIpus = $sentFiles->pluck('patient_ipu')->unique()->toArray();

        $patientQuery = Patient::where('hospital_id', $user->hospital_id)
            ->whereNotIn('id', $patientIdsToCheck)
            ->whereNotIn('ipu', $sentIpus);

        // --- CORRECTIF PÉDIATRIE : Filter les adultes (age < 18) ---
        if (str_contains(strtolower($user->service->name ?? ''), 'pédia')) {
            $patientQuery->where(function($q) {
                // Si la date de naissance est renseignée, on vérifie l'âge (< 18 ans)
                $q->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 18')
                  ->orWhereNull('dob'); 
            });
        }

        $patientsWithoutApt = $patientQuery->where(function($query) use ($user) {
                // Filtre : Patients ayant déjà eu un RDV ou une admission dans CE service
                $query->whereHas('appointments', function($q) use ($user) {
                    $q->where('appointments.service_id', $user->service_id);
                })->orWhereHas('admissions.room', function($q) use ($user) {
                    $q->where('rooms.service_id', $user->service_id);
                });
            })
            ->latest()
            ->limit(50)
            ->get();

        // 4. Mes patients hospitalisés (Seulement dans les chambres de mon service)
        $myPatients = Admission::with(['patient' => function($q) {
                $q->withTrashed()->withoutGlobalScopes();
            }, 'room', 'derniersSignes', 'patient.prescriptions' => function($q) {
                // Filtre: Seulement les prescriptions récentes (depuis l'admission -48h)
                // Évite d'afficher tout l'historique du patient
                $q->withoutGlobalScopes()
                  ->where('created_at', '>=', now()->subHours(48))
                  ->latest();
            }])
            ->where('hospital_id', $user->hospital_id)
            ->where('status', 'active')
            ->whereHas('room', function($q) use ($user) {
                $q->where('service_id', $user->service_id);
            })
            ->get();

        return view('nurse.dashboard', [
            'sentFiles' => $sentFiles,
            'appointments' => $appointments,
            'walkIns' => $walkIns,
            'patientsWithoutApt' => $patientsWithoutApt,
            'myPatients' => $myPatients
        ]);
    }

    /**
     * Enregistre les constantes et envoie au médecin
     */
    public function store(Request $request)
    {
        try {
            // Validation coté serveur pour garantir l'intégrité des données
            $request->validate([
                'patient_name' => 'required|string',
                'patient_ipu'  => 'required|string',
                'temperature'  => 'required|numeric',
                'pulse'        => 'required|numeric',
                'reason'       => 'required|string',
            ]);

            // A. ENSURE PATIENT RECORD EXISTS (Auto-create if missing)
            $patient = Patient::withoutGlobalScopes()->where('ipu', $request->patient_ipu)->first();
            
            if (!$patient) {
                // Auto-create patient record using data from the nurse form
                $patient = Patient::create([
                    'hospital_id' => auth()->user()->hospital_id,
                    'ipu' => $request->patient_ipu,
                    'name' => $request->patient_name,
                    'first_name' => $request->patient_name, // Use same as name initially
                    'phone' => $request->phone ?? 'N/A',
                    'email' => null,
                    'dob' => now()->subYears(30), // Default age, can be updated later
                    'gender' => 'Other', // Default, can be updated later
                    'address' => 'Non renseigné',
                    'blood_group' => $request->blood_group ?? null,
                    'allergies' => $request->filled('allergies') 
                        ? array_filter(array_map('trim', explode(',', $request->allergies)))
                        : null,
                    'medical_history' => $request->medical_history ?? null,
                ]);
            } else {
                // Update existing patient with new medical information
                $patientUpdate = [];
                if ($request->filled('blood_group')) $patientUpdate['blood_group'] = $request->blood_group;
                if ($request->filled('allergies')) {
                    $patientUpdate['allergies'] = array_filter(array_map('trim', explode(',', $request->allergies)));
                }
                if ($request->filled('medical_history')) $patientUpdate['medical_history'] = $request->medical_history;

                if (!empty($patientUpdate)) {
                    $patient->update($patientUpdate);
                }
            }

            // B. DÉTERMINATION DU MÉDECIN ASSIGNÉ (Avant toute action)
            // 1. Chercher le RDV correspondant (en priorité 'paid' pour le traiter, et dans MON service)
            $appointment = Appointment::whereHas('patient', function($q) use ($request) {
                    $q->where('ipu', $request->patient_ipu);
                })
                ->where('hospital_id', auth()->user()->hospital_id)
                // On s'assure de traiter le RDV que l'infirmière voit (celui de son service)
                ->where('service_id', auth()->user()->service_id) 
                ->whereIn('status', ['paid', 'scheduled', 'confirmed', 'prepared'])
                // On priorise ceux qui sont 'paid' (à traiter) et pour aujourd'hui
                ->orderByRaw("FIELD(status, 'paid') DESC") 
                ->orderBy('appointment_datetime', 'desc')
                ->first();

            $assignedDoctorId = ($appointment && $appointment->doctor_id) ? $appointment->doctor_id : null;

            // 1.5 VALIDATION : Le médecin doit être de MON service (ex: Maternité)
            // Si l'accueil a assigné un généraliste par erreur, on le change.
            // 1.5 VALIDATION : Le médecin doit être de MON service (ex: Maternité)
            if ($assignedDoctorId) {
                $doc = \App\Models\User::find($assignedDoctorId);
                // Si le médecin n'est pas du même service que l'infirmière (et le RDV), on le révoque pour forcer le Round Robin local
                if ($doc && $doc->service_id !== auth()->user()->service_id) {
                    $assignedDoctorId = null; 
                }
            }

            // 2. Si aucun médecin assigné (ou révoqué), recherche d'un médecin disponible (Round Robin)
            if (!$assignedDoctorId) {
                // Determine day name in English for DB query
                $dayName = strtolower(\Carbon\Carbon::now()->locale('en')->isoFormat('dddd'));
                
                // Pass 1: Strict Availability (Doctors working today)
                $availableDoctors = \App\Models\User::where('hospital_id', auth()->user()->hospital_id)
                    ->where('service_id', auth()->user()->service_id) // Strictement mon service
                    ->whereIn('role', ['doctor', 'internal_doctor'])
                    ->where('is_active', true)
                    ->whereHas('availabilities', function($query) use ($dayName) {
                        $query->where('day_of_week', $dayName)
                              ->where('is_active', true);
                    })
                    ->get();

                // Pass 2: Fallback (All active doctors in service) if no one is "scheduled" today
                // This prevents "Unassigned" if schedules aren't set up
                if ($availableDoctors->isEmpty()) {
                    $availableDoctors = \App\Models\User::where('hospital_id', auth()->user()->hospital_id)
                        ->where('service_id', auth()->user()->service_id)
                        ->whereIn('role', ['doctor', 'internal_doctor'])
                        ->where('is_active', true)
                        ->get();
                }

                $minCount = 9999;
                $bestDoctorId = null;

                foreach ($availableDoctors as $doctor) {
                    $count = PatientVital::where('doctor_id', $doctor->id)
                        ->whereDate('created_at', now()->toDateString())
                        ->where('status', '!=', 'archived')
                        ->count();
                    
                    // Track least busy doctor
                    if ($count < $minCount) {
                        $minCount = $count;
                        $bestDoctorId = $doctor->id;
                    }

                    // Ideal case: Find first one under limit
                    if ($count < 3) {
                        $assignedDoctorId = $doctor->id;
                        break;
                    }
                }
                
                // Fallback: If everyone is busy (>= 3), assign to the least busy one
                if (!$assignedDoctorId && $bestDoctorId) {
                    $assignedDoctorId = $bestDoctorId;
                }
            }

            // C. GESTION DE L'ENREGISTREMENT (Update ou Create)
            
            // CLEANUP : On archive les "vieux" dossiers actifs de ce patient pour éviter les doublons chez le médecin
            // Si l'infirmière envoie un nouveau dossier aujourd'hui, les anciens "En attente" des jours précédents sont obsolètes.
            PatientVital::where('patient_ipu', $request->patient_ipu)
                ->where('status', 'active')
                ->whereDate('created_at', '!=', now()->toDateString())
                ->update(['status' => 'archived']);

            $existingRecord = PatientVital::where('patient_ipu', $request->patient_ipu)
                ->whereDate('created_at', now()->toDateString())
                ->first();

            if ($existingRecord) {
                // On autorise la mise à jour pour TOUS les dossiers du jour
                $updateData = [
                    'patient_name'   => $request->patient_name,
                    'urgency'        => $request->urgency,
                    'reason'         => $request->reason,
                    'temperature'    => $request->temperature,
                    'pulse'          => $request->pulse,
                    'weight'         => $request->weight,
                    'height'         => $request->height,
                    'blood_pressure' => $request->blood_pressure ?? '12/8',
                    'custom_vitals'  => $request->custom_vitals,
                    'user_id'        => auth()->id(),
                    'status'         => 'active',
                ];

                // Si le dossier existant n'a pas de médecin, on lui en assigne un
                if (!$existingRecord->doctor_id && $assignedDoctorId) {
                    $updateData['doctor_id'] = $assignedDoctorId;
                }
                
                // Si on a changé de médecin (correction service), on met à jour
                if ($existingRecord->doctor_id && $assignedDoctorId && $existingRecord->doctor_id !== $assignedDoctorId) {
                     $updateData['doctor_id'] = $assignedDoctorId;
                }

                $existingRecord->update($updateData);

                // Mise à jour du RDV
                if ($appointment) {
                    $appointment->update(['status' => 'prepared']);
                    // On s'assure que le RDV a le bon médecin final
                    if ($assignedDoctorId && $appointment->doctor_id !== $assignedDoctorId) {
                        $appointment->update(['doctor_id' => $assignedDoctorId]);
                    }
                }

                return $request->ajax() 
                    ? response()->json(['success' => true])
                    : redirect()->back()->with('success', 'Dossier mis à jour et repris.');
            }

            // D. CRÉATION NOUVEAU DOSSIER
            PatientVital::create([
                'patient_name'   => $request->patient_name,
                'patient_ipu'    => $request->patient_ipu,
                'urgency'        => $request->urgency,
                'reason'         => $request->reason,
                'temperature'    => $request->temperature,
                'pulse'          => $request->pulse,
                'weight'         => $request->weight,
                'height'         => $request->height,
                'blood_group'    => $request->blood_group,
                'allergies'      => $request->allergies,
                'medical_history'=> $request->medical_history,
                'blood_pressure' => $request->blood_pressure ?? '12/8',
                'custom_vitals'  => $request->custom_vitals,
                'user_id'        => auth()->id(),
                'doctor_id'      => $assignedDoctorId,
                'hospital_id'    => auth()->user()->hospital_id,
                'service_id'     => auth()->user()->service_id,
                'status'         => 'active',
            ]);

            // Mise à jour du RDV
            if ($appointment) {
                // On marque comme préparé pour qu'il disparaisse de la liste (status != 'paid')
                $appointment->update(['status' => 'prepared']); 
                if ($assignedDoctorId && $appointment->doctor_id !== $assignedDoctorId) {
                    $appointment->update(['doctor_id' => $assignedDoctorId]);
                }
            }

            return $request->ajax() 
                ? response()->json(['success' => true])
                : redirect()->back();

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprime un dossier envoyé par erreur
     */
    public function destroy($id)
    {
        try {
            $vital = PatientVital::findOrFail($id);
            // On autorise la suppression quel que soit le créateur (pour la gestion d'équipe)
            // On vérifie juste que ça appartient au même hôpital (déjà filtré par findOrFail si on utilisait un scope, mais ici check manuel prudent)
            if ($vital->hospital_id !== auth()->user()->hospital_id) {
                 return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
            }
            
            $vital->delete();
            
            // Si le dossier avait une admission ou un RDV lié, on pourrait vouloir mettre à jour le statut, mais ici c'est juste le log vital.
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Affiche la page hospitalisation
     */
    public function hospitalisation()
    {
        $user = auth()->user();

        // Récupérer les admissions actives
        $admissions = Admission::with(['patient', 'bed.room'])
            ->where('hospital_id', $user->hospital_id)
            ->where('status', 'active')
            ->get();

        return view('nurse.dashboard', [
            'appointments' => collect(), // Vide pour éviter erreurs
            'sentFiles' => collect(),
            'myPatients' => collect(),
            'hospitalisations' => $admissions->map(function($admission) {
                return [
                    'id' => $admission->id,
                    'patientName' => $admission->patient->name,
                    'room' => $admission->bed->room->name ?? 'N/A',
                    'bed' => $admission->bed->bed_number ?? 'N/A',
                    'admissionDate' => $admission->admission_date->format('d/m/Y'),
                    'status' => 'Actif'
                ];
            }),
            'archives' => collect()
        ]);
    }

    /**
     * Affiche la page archive
     */
    public function archive()
    {
        $user = auth()->user();

        // Récupérer les dossiers médicaux archivés (PatientVital, pas MedicalRecord)
        // FILTRE: Comme pour le portail patient, on exclut les doublons avec doctor_id NULL
        $archivedRecords = \App\Models\PatientVital::where('hospital_id', $user->hospital_id)
            ->where('status', 'archived')
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
            ->with('doctor') // Eager load doctor relationship
            ->latest()
            ->get();

        return view('nurse.dashboard', [
            'appointments' => collect(),
            'sentFiles' => collect(),
            'myPatients' => collect(),
            'hospitalisations' => collect(),
            'archives' => $archivedRecords->map(function($record) {
                return [
                    'id' => $record->id,
                    'patientName' => $record->patient_name,
                    'reason' => $record->reason ?? 'N/A',
                    'archivedDate' => $record->updated_at->format('d/m/Y'),
                    'doctorName' => $record->doctor?->name ?? 'Non assigné'
                ];
            })
        ]);
    }

    /**
     * Endpoint API pour rafraîchir la liste des dossiers envoyés (Polling)
     */
    public function fetchSentFiles()
    {
        $user = auth()->user();
        $sentFiles = PatientVital::with('doctor')
            ->where('hospital_id', $user->hospital_id)
            ->where('service_id', $user->service_id)
            ->whereDate('created_at', now()->toDateString())
            ->where(function($query) {
                $query->whereNotNull('doctor_id')
                      ->orWhereNotExists(function($subQuery) {
                          $subQuery->select(\DB::raw(1))
                              ->from('patient_vitals as pv2')
                              ->whereColumn('pv2.patient_ipu', 'patient_vitals.patient_ipu')
                              ->whereRaw('DATE(pv2.created_at) = DATE(patient_vitals.created_at)')
                              ->whereNotNull('pv2.doctor_id');
                      });
            })
            ->latest()
            ->get();

        return response()->json($sentFiles->map(function($file) {
            return [
                'id' => $file->id,
                'patient_id' => $file->patient->id ?? null,
                'patientName' => $file->patient_name,
                'reason' => $file->reason,
                'sentAt' => $file->created_at->format('H:i'),
                'assignedDoctor' => $file->doctor ? "Dr. " . $file->doctor->name : "Non assigné",
                'status' => $file->status
            ];
        }));
    }
    /**
     * Affiche le dashboard patient dédié à l'infirmier (Lecture seule)
     */
    public function patientDashboard(Patient $patient)
    {
        $user = auth()->user();
        
        // 1. Détection de l'admission active pour filtrer les données
        $activeAdmission = Admission::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->first();
            
        $minDate = $activeAdmission ? $activeAdmission->admission_date : now()->subHours(48);

        // 2. Récupération des vitaux filtrés par date et service
        $patientVitals = \App\Models\PatientVital::where('patient_ipu', $patient->ipu)
            ->where('service_id', $user->service_id)
            ->where('created_at', '>=', $minDate)
            ->where(function($query) {
                $query->whereNotNull('doctor_id')
                      ->orWhereNotExists(function($subQuery) {
                          $subQuery->select(\DB::raw(1))
                               ->from('patient_vitals as pv2')
                               ->whereColumn('pv2.patient_ipu', 'patient_vitals.patient_ipu')
                               ->whereRaw('DATE(pv2.created_at) = DATE(patient_vitals.created_at)')
                               ->whereNotNull('pv2.doctor_id');
                      });
            })
            ->with(['doctor', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $patient->load([
            'clinicalObservations' => function($query) use ($user, $minDate) {
                $query->where('observation_datetime', '>=', $minDate)
                      ->whereHas('user', function($q) use ($user) {
                          $q->where('service_id', $user->service_id);
                      })->orderBy('observation_datetime', 'desc');
            }, 
            'clinicalObservations.user', 
            'labRequests' => function($query) use ($user, $minDate) {
                $query->where('created_at', '>=', $minDate)
                      ->whereHas('doctor', function($q) use ($user) {
                          $q->where('service_id', $user->service_id);
                      })->orderBy('created_at', 'desc');
            }, 
            'labRequests.doctor',
            'prescriptions' => function($query) use ($user, $minDate) {
                $query->withoutGlobalScopes()
                      ->where('created_at', '>=', $minDate)
                      ->whereHas('doctor', function($q) use ($user) {
                          $q->where('service_id', $user->service_id);
                      })
                      ->orderBy('created_at', 'desc');
            }, 
            'prescriptions.doctor'
        ]);

        // Fusionner pour la timeline
        $allExams = $patientVitals->concat($patient->clinicalObservations)
                                  ->sortByDesc(function($item) {
            return $item->observation_datetime ?? $item->created_at;
        });

        return view('nurse.patient_dashboard', compact('patient', 'allExams', 'patientVitals'));
    }
    /**
     * Enregistre une note de soin infirmière
     */
    public function storeCareNote(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'notes' => 'required|string',
                'observation_datetime' => 'nullable|date',
            ]);

            \App\Models\ClinicalObservation::create([
                'patient_id' => $request->patient_id,
                'user_id' => auth()->id(),
                'type' => 'nurse_note',
                'notes' => $request->notes,
                'observation_datetime' => $request->observation_datetime ?? now(),
                'is_published' => true, // Visible directement sur le portail si besoin
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

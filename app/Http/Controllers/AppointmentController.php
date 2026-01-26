<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, Patient, User, Service, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Notification};
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();
    $query = Appointment::with(['patient', 'doctor', 'service']);

    // --- SÉCURITÉ RENFORCÉE ---
    if ($user->isDoctor()) {
        $query->where(function ($q) use ($user) {
            $q->where('doctor_id', $user->id) // Mes rendez-vous
              ->orWhere(function ($subQ) use ($user) {
                  // OU les rendez-vous de mon service sans médecin assigné (pour approbation)
                  $subQ->where('service_id', $user->service_id)
                       ->whereNull('doctor_id')
                       ->where('status', 'pending');
              });
        })->where('status', '!=', 'prepared');
    }
    // On ne rentre dans cette condition QUE si ce n'est pas un docteur et pas un admin
    elseif (!$user->isAdmin() && $user->service_id) {
        $query->where('service_id', $user->service_id);
    }
    // --------------------------

    // Filtres de recherche
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('date')) {
        $query->whereDate('appointment_datetime', $request->date);
    }

    // Protection supplémentaire : Un médecin ne peut pas filtrer d'autres médecins
    if ($request->filled('doctor_id') && $user->isAdmin()) {
        $query->where('doctor_id', $request->doctor_id);
    }

    // Gestion de la vue par défaut (Toujours afficher tous les rendez-vous pour les médecins)
    $query->latest('appointment_datetime');

    $appointments = $query->paginate(20);

    // Liste des médecins pour le filtre (Seulement pour les admins ou les chefs de service)
    $doctors = User::where('role', 'doctor')
                  ->where('is_active', true)
                  ->when(!$user->isAdmin() && $user->service_id, function($q) use ($user) {
                      return $q->where('service_id', $user->service_id);
                  })
                  ->get();

    return view('appointments.index', compact('appointments', 'doctors'));
}

    public function create(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patient = $patientId ? Patient::findOrFail($patientId) : null;
        // NOUVEAU : Charger la liste complète des patients pour le selectbox
        $patients = Patient::where('is_active', true)
                           ->orderBy('name')
                           ->get(['id', 'first_name', 'name', 'ipu']); // Ceci résout l'erreur

        $user = auth()->user();
        
        // Liste des médecins disponibles
        $doctors = User::where('role', 'doctor')
                      ->where('is_active', true)
                      ->when(!$user->isAdmin() && $user->service_id, function($q) use ($user) {
                          return $q->where('service_id', $user->service_id);
                      })
                      ->with('service')
                      ->get();

        $services = Service::where('is_active', true)->get();

         return view('appointments.create', compact('patient', 'patients', 'doctors', 'services'));
    }

    public function store(Request $request)
    {
    
        // 1. CONCATÉNER LA DATE ET L'HEURE (CRITIQUE)
        // La vue envoie 'appointment_date' et 'appointment_time', le modèle attend 'appointment_datetime'.
        $request->merge([
            'appointment_datetime' => $request->input('appointment_date') . ' ' . $request->input('appointment_time'),
        ]);

        // 2. VALIDATION
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            
            // Validation des composants Date/Heure
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            // Validation du champ mergé pour la contrainte `after:now`
            'appointment_datetime' => 'required|date|after:now',
            
            'duration' => 'required|integer|min:15|max:240',
            
            // 'type' est présent directement dans la requête (name="type" dans la vue)
            'type' => 'required|in:consultation,follow_up,emergency,routine_checkup', 
            
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show', // Rendu 'required' basé sur votre vue
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            // ... (autres champs si utilisés)
        ]);
        
        // 3. PRÉPARATION DES DONNÉES
        
        // On retire les champs de la validation qui n'existent pas en base
        unset($validated['appointment_date']);
        unset($validated['appointment_time']);
        
        // 4. VÉRIFICATION DE LA DISPONIBILITÉ (si checkDoctorAvailability est défini)
        // Note: J'ai retiré le code de disponibilité ici pour se concentrer sur l'enregistrement,
        // mais vous devez le laisser si vous l'avez implémenté.
        // Assurez-vous d'avoir la méthode checkDoctorAvailability si vous l'appelez.

        // 5. ENREGISTREMENT EN BASE DE DONNÉES

        DB::beginTransaction();
        try {
            // Création avec les données validées et nettoyées
            $appointment = Appointment::create($validated);

            DB::commit();

            return redirect()->route('appointments.index')
                             ->with('success', 'Rendez-vous créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Décommentez le dd() pour le débogage si l'erreur persiste en base de données
            // dd($e->getMessage()); 
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création du rendez-vous. Vérifiez les logs.']);
        }
    }

    

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'service']);

        // Vérifier les permissions
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        $user = auth()->user();
        
        $doctors = User::where('role', 'doctor')
                      ->where('is_active', true)
                      ->when(!$user->isAdmin() && $user->service_id, function($q) use ($user) {
                          return $q->where('service_id', $user->service_id);
                      })
                      ->get();

        $services = Service::where('is_active', true)->get();

        return view('appointments.edit', compact('appointment', 'doctors', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_datetime' => 'required|date',
            'duration' => 'required|integer|min:15|max:240',
            'status' => 'required|in:scheduled,confirmed,cancelled,completed,no_show',
            'type' => 'required|in:consultation,follow_up,emergency',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier disponibilité si changement de date/médecin
        if ($appointment->doctor_id != $validated['doctor_id'] || 
            $appointment->appointment_datetime != $validated['appointment_datetime']) {
            
            $conflicts = $this->checkDoctorAvailability(
                $validated['doctor_id'],
                $validated['appointment_datetime'],
                $validated['duration'],
                $appointment->id
            );

            if ($conflicts > 0) {
                return back()->withInput()->withErrors([
                    'appointment_datetime' => 'Le médecin n\'est pas disponible à cette date/heure.'
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $oldData = $appointment->toArray();
            
            $appointment->update($validated);

            AuditLog::log('update', 'Appointment', $appointment->id, [
                'description' => 'Modification d\'un rendez-vous',
                'old' => $oldData,
                'new' => $appointment->toArray()
            ]);

            DB::commit();

            return redirect()->route('appointments.show', $appointment)
                           ->with('success', 'Rendez-vous mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour.']);
        }
    }

    public function destroy(Appointment $appointment)
    {
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        DB::beginTransaction();
        try {
            AuditLog::log('delete', 'Appointment', $appointment->id, [
                'description' => 'Suppression d\'un rendez-vous',
                'old' => $appointment->toArray()
            ]);

            $appointment->delete();

            DB::commit();

            return redirect()->route('appointments.index')
                           ->with('success', 'Rendez-vous supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression.']);
        }
    }

    public function confirm(Appointment $appointment)
    {
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        $appointment->update(['status' => 'confirmed']);

        AuditLog::log('update', 'Appointment', $appointment->id, [
            'description' => 'Confirmation du rendez-vous'
        ]);

        return back()->with('success', 'Rendez-vous confirmé.');
    }

    public function cancel(Appointment $appointment)
    {
        if (!$this->canAccessAppointment($appointment)) {
            abort(403, 'Accès non autorisé.');
        }

        $appointment->update(['status' => 'cancelled']);

        AuditLog::log('update', 'Appointment', $appointment->id, [
            'description' => 'Annulation du rendez-vous'
        ]);

        return back()->with('success', 'Rendez-vous annulé.');
    }

    public function doctorAvailability(Request $request, User $doctor)
    {
        // API pour récupérer les disponibilités d'un médecin
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_datetime', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['appointment_datetime', 'duration']);

        // Récupérer les disponibilités configurées
        $availability = $doctor->availability()
            ->where('day_of_week', strtolower(Carbon::parse($date)->englishDayOfWeek))
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            return response()->json([
                'available' => false,
                'message' => 'Le médecin n\'est pas disponible ce jour.'
            ]);
        }

        // Vérifier les congés
        $onLeave = $doctor->leaves()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();

        if ($onLeave) {
            return response()->json([
                'available' => false,
                'message' => 'Le médecin est en congé.'
            ]);
        }

        // Générer les créneaux disponibles
        $slots = $this->generateTimeSlots(
            $availability->start_time,
            $availability->end_time,
            $availability->slot_duration,
            $appointments
        );

        return response()->json([
            'available' => true,
            'slots' => $slots
        ]);
    }
     // ===================================================
    // ✅ MÉTHODE POUR LA MISE À JOUR DU STATUT (AJAX)
    // ===================================================
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // 1. Validation de l'entrée
        $request->validate([
            // La validation des statuts est maintenant correcte grâce à l'importation de Rule
            'status' => [
                'required', 
                'string', 
                Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])
            ],
        ]);

        // 2. Vérification des permissions (si vous gérez les rôles ici)
        if (!$this->canAccessAppointment($appointment)) {
             // 403 Forbidden - Mieux que de laisser Laravel lever une exception
             return response()->json(['success' => false, 'message' => 'Accès non autorisé à cette ressource.'], 403);
        }

        try {
            // Sauvegarder l'ancien statut pour l'audit
            $oldStatus = $appointment->status;
            
            // 3. Mise à jour du statut
            $appointment->status = $request->input('status');
            $appointment->save();

            // Enregistrement d'audit
            AuditLog::log('update', 'Appointment', $appointment->id, [
                'description' => 'Statut mis à jour de ' . $oldStatus . ' à ' . $appointment->status,
                'old_status' => $oldStatus,
                'new_status' => $appointment->status,
            ]);

            // 4. Retourner une réponse JSON positive
            return response()->json([
                'success' => true,
                'message' => 'Statut du rendez-vous mis à jour avec succès.',
                'new_status' => $appointment->status,
            ], 200);

        } catch (\Exception $e) {
            // 5. Gérer les erreurs (Base de données, etc.)
            \Log::error('Erreur de mise à jour du statut du rendez-vous : ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la mise à jour du statut.',
            ], 500);
        }

    }

    // ===================================================
    // ✅ MÉTHODE POUR APPROUVER ET S'ASSIGNER UN RDV
    // ===================================================
    public function approve(Request $request, Appointment $appointment)
    {
        $user = auth()->user();

        // Vérification : Le médecin doit être du même service
        if ($user->service_id !== $appointment->service_id) {
             abort(403, 'Vous ne pouvez approuver que les rendez-vous de votre service.');
        }

        // Vérification : Le RDV doit être "pending" et sans médecin
        if ($appointment->status !== 'pending' || $appointment->doctor_id !== null) {
            return back()->with('error', 'Ce rendez-vous n\'est plus en attente ou a déjà été assigné.');
        }

        try {
            DB::beginTransaction();

            $appointment->update([
                'status' => 'confirmed',
                'doctor_id' => $user->id
            ]);

            AuditLog::log('approve', 'Appointment', $appointment->id, [
                'description' => 'Rendez-vous approuvé et assigné à Dr. ' . $user->name,
                'doctor_id' => $user->id
            ]);

            DB::commit();

            return back()->with('success', 'Rendez-vous approuvé et ajouté à votre agenda.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'approbation.');
        }
    }
    private function checkDoctorAvailability($doctorId, $datetime, $duration, $excludeId = null)
    {
        $startTime = Carbon::parse($datetime);
        $endTime = $startTime->copy()->addMinutes($duration);

        return Appointment::where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('appointment_datetime', [$startTime, $endTime])
                      ->orWhere(function($q) use ($startTime, $endTime) {
                          $q->where('appointment_datetime', '<', $startTime)
                            ->whereRaw('DATE_ADD(appointment_datetime, INTERVAL duration MINUTE) > ?', [$startTime]);
                      });
            })
            ->count();
    }
     

    private function generateTimeSlots($startTime, $endTime, $duration, $existingAppointments)
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($current->addMinutes($duration) <= $end) {
            $slotStart = $current->copy();
            $slotEnd = $slotStart->copy()->addMinutes($duration);

            // Vérifier si le créneau est libre
            $isOccupied = false;
            foreach ($existingAppointments as $appointment) {
                $aptStart = Carbon::parse($appointment->appointment_datetime);
                $aptEnd = $aptStart->copy()->addMinutes($appointment->duration);

                if ($slotStart < $aptEnd && $slotEnd > $aptStart) {
                    $isOccupied = true;
                    break;
                }
            }

            if (!$isOccupied) {
                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'available' => true
                ];
            }
        }

        return $slots;
    }

    private function createRecurringAppointments(Appointment $baseAppointment)
    {
        // Logique pour créer des rendez-vous récurrents
        // À implémenter selon les besoins (hebdomadaire, mensuel, etc.)
    }

    private function canAccessAppointment(Appointment $appointment): bool
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor() && $appointment->doctor_id === $user->id) {
            return true;
        }

        if ($user->service_id && $appointment->service_id === $user->service_id) {
            return true;
        }

        return false;
    }
}
<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admission;
use App\Models\MedicalDocument;
use App\Models\PatientVital;
use Carbon\Carbon;

class MedecinDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Récupération des patients avec leurs constantes (Eager Loading)
        // Note: On utilise withTrashed() pour le patient au cas où le dossier aurait été supprimé par erreur
        $hospitalizedPatients = Admission::with(['patient' => function($q) {
                        $q->withTrashed()->withoutGlobalScopes();
                    }, 'derniersSignes'])
                    ->where('status', 'active')
                    ->where(function($q) use ($user) {
                        $q->where('doctor_id', $user->id);
                        
                        if ($user->service_id) {
                            $q->orWhereHas('room', function($r) use ($user) {
                                $r->where('service_id', $user->service_id);
                            });
                        }
                    })
                    ->get();
        // Alias pour la vue
        $myPatients = $hospitalizedPatients;

        // 2. Calcul des dossiers en attente (assignés à ce médecin et actifs/en consultation)
        $pendingDossiers = PatientVital::where('doctor_id', $user->id)
            ->whereIn('status', ['active', 'consulting'])
            ->count();

        // 3. Nombre de nouvelles admissions dans les dernières 24h
        $newAdmissions = Admission::where('doctor_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        // 4. Compteur automatique des patients critiques
        $criticalPatients = $hospitalizedPatients->filter(function($admission) {
            $signes = $admission->derniersSignes;
            return ($admission->alert_level === 'critical') ||
                   ($signes && ($signes->temperature >= 38.5 || $signes->temperature <= 35.0)) ||
                   ($signes && ($signes->pulse >= 120 || $signes->pulse <= 50));
        })->count();

        // 5. Rendez-vous d'aujourd'hui (Confirmés, payés ou terminés et assignés)
        $todayAppointments = \App\Models\Appointment::with(['patient', 'service'])
            ->where('doctor_id', $user->id)
            ->whereDate('appointment_datetime', today())
            ->whereIn('status', ['confirmed', 'scheduled', 'paid', 'completed'])
            ->orderBy('appointment_datetime')
            ->get();

        // 6. Rendez-vous en attente d'attribution (même service)
        // Récupérer les jours où ce médecin est disponible
        $availableDays = \App\Models\DoctorAvailability::where('doctor_id', $user->id)
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->toArray();

        $pendingServiceAppointments = \App\Models\Appointment::with(['patient', 'service'])
            ->where('hospital_id', $user->hospital_id)
            ->where('service_id', $user->service_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('doctor_id')
            ->get()
            ->filter(function($appointment) use ($availableDays) {
                // Si pas de dispo configurée, on montre tout, sinon on filtre
                if (empty($availableDays)) return true;
                $dayName = strtolower(Carbon::parse($appointment->appointment_datetime)->format('l'));
                return in_array($dayName, $availableDays);
            });

        // 7. Prochains rendez-vous (Après aujourd'hui)
        $upcomingAppointments = \App\Models\Appointment::with(['patient', 'service'])
            ->where('doctor_id', $user->id)
            ->whereDate('appointment_datetime', '>', today())
            ->where('status', 'confirmed')
            ->orderBy('appointment_datetime')
            ->get();

        return view('medecin.dashboard', compact(
            'hospitalizedPatients', 
            'myPatients',
            'pendingDossiers', 
            'criticalPatients', 
            'newAdmissions',
            'todayAppointments',
            'upcomingAppointments',
            'pendingServiceAppointments'
        ));
    }
}
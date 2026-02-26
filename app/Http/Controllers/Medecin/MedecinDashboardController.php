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

        // Si l'utilisateur appartient au Pôle Technique, redirection vers son dashboard spécifique
    if ($user->isTechnical()) {
        if ($user->role === 'doctor_radio' || $user->role === 'radio_technician') {
            return redirect()->route('lab.radiologist.dashboard');
        }
        return redirect()->route('lab.biologist.dashboard');
    }

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
            'upcomingAppointments'
        ));
    }
}
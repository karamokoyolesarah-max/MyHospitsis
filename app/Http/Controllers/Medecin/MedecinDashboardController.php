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
        $medecin = auth()->user() ?: auth()->guard('medecin_externe')->user();

        if (!$medecin) {
            return redirect()->route('login')->withErrors(['identifier' => 'Session expirée ou utilisateur non trouvé.']);
        }

        // Failsafe: Rediriger les médecins externes vers leur propre tableau de bord
        if ($medecin instanceof \App\Models\MedecinExterne || $medecin->role === 'medecin') {
            return redirect()->route('external.doctor.external.dashboard');
        }

        // 1. Récupération des patients avec leurs constantes (Eager Loading)
        // On charge 'derniersSignes' pour éviter les tirets "--" sur le dashboard
        $hospitalizedPatients = Admission::with(['patient', 'derniersSignes'])
                    ->where('status', 'active')
                    ->where('doctor_id', $medecin->id)
                    ->get();

        // 2. Calcul des examens en attente de validation
        $pendingExams = MedicalDocument::whereHas('patient.admissions', function($query) use ($medecin) {
            $query->where('doctor_id', $medecin->id);
        })->where('is_validated', false)->count();

        // 3. Nombre de nouvelles admissions dans les dernières 24h
        $newAdmissions = Admission::where('doctor_id', $medecin->id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        // 4. Compteur automatique des patients critiques
        // Analyse combinée du statut manuel et des constantes réelles (Temp/Pouls)
        $criticalPatients = $hospitalizedPatients->filter(function($admission) {
            $signes = $admission->derniersSignes;
            
            return ($admission->alert_level === 'critical') ||
                   ($signes && ($signes->temperature >= 38.5 || $signes->temperature <= 35.0)) ||
                   ($signes && ($signes->pulse >= 120 || $signes->pulse <= 50));
        })->count();

        return view('medecin.dashboard', compact(
            'medecin',
            'hospitalizedPatients', 
            'pendingExams', 
            'criticalPatients', 
            'newAdmissions'
        ));
    }
}
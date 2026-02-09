<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\PatientVital;
use Illuminate\Http\Request;

class LabRequestController extends Controller
{
    /**
     * Store a new lab request from doctor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_vital_id' => 'nullable|exists:patient_vitals,id',
            'patient_ipu' => 'required|string',
            'patient_name' => 'required|string',
            'tests' => 'nullable|array',
            'custom_test' => 'nullable|string',
            'clinical_info' => 'nullable|string',
        ]);

        $patientVital = $validated['patient_vital_id'] ? PatientVital::find($validated['patient_vital_id']) : null;
        $tests = $validated['tests'] ?? [];
        
        // Ajouter le test personnalisé s'il existe
        if (!empty($validated['custom_test'])) {
            $tests[] = $validated['custom_test'];
        }

        if (empty($tests)) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins un examen.');
        }

        // Créer une demande pour chaque test
        foreach ($tests as $testName) {
            $category = $this->determineCategory($testName);
            
            // Trouver le service technique correspondant (Labo ou Imagerie)
            $targetService = \App\Models\Service::where('hospital_id', auth()->user()->hospital_id)
                ->where('name', 'like', $category === 'imagerie' ? '%Imagerie%' : '%Labo%')
                ->first();

            LabRequest::create([
                'hospital_id' => auth()->user()->hospital_id,
                'patient_vital_id' => $validated['patient_vital_id'],
                'patient_ipu' => $validated['patient_ipu'],
                'patient_name' => $validated['patient_name'],
                'doctor_id' => auth()->id(),
                'service_id' => $targetService ? $targetService->id : (auth()->user()->service_id),
                'test_name' => $testName,
                'test_category' => $category,
                'clinical_info' => $validated['clinical_info'],
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', count($tests) . ' examen(s) prescrit(s) avec succès.');
    }

    /**
     * Determine test category based on name
     */
    private function determineCategory($testName)
    {
        $imagingKeywords = ['Radio', 'TDM', 'IRM', 'Écho', 'Échographie', 'Scanner', 'Doppler', 'Monitoring'];
        
        foreach ($imagingKeywords as $keyword) {
            if (stripos($testName, $keyword) !== false) {
                return 'imagerie';
            }
        }
        
        return 'laboratoire';
    }

    /**
     * Lab technician dashboard
     */
    public function index()
    {
        $pendingRequests = LabRequest::where('hospital_id', auth()->user()->hospital_id)
            ->where('service_id', auth()->user()->service_id)
            ->where('is_paid', true)
            ->whereIn('status', ['pending', 'sample_received', 'in_progress', 'to_be_validated'])
            ->with(['doctor', 'patientVital'])
            ->orderBy('requested_at', 'desc')
            ->get();

        $completedToday = LabRequest::where('hospital_id', auth()->user()->hospital_id)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        return view('lab.dashboard', compact('pendingRequests', 'completedToday'));
    }

    /**
     * Lab technician worklist
     */
    public function worklist(Request $request)
    {
        $query = LabRequest::where('hospital_id', auth()->user()->hospital_id)
            ->where('service_id', auth()->user()->service_id)
            ->where('is_paid', true)
            ->whereIn('status', ['pending', 'sample_received', 'in_progress'])
            ->with(['doctor', 'patientVital'])
            ->orderBy('requested_at', 'desc');

        if ($request->filter === 'urgent') {
            // Need a way to mark urgent, maybe clinical_info contains keyword or a separate flag.
            // For now, let's assume filtering relies on something else or it's just a placeholder.
            // If we lack an urgent flag, we might check clinical_info for 'urgent'
            $query->where('clinical_info', 'like', '%urgent%');
        }

        $pendingRequests = $query->get();

        return view('lab.worklist', compact('pendingRequests'));
    }

    /**
     * Update request status
     */
    public function updateStatus(Request $request, LabRequest $labRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:sample_received,in_progress,completed',
        ]);

        $labRequest->update([
            'status' => $validated['status'],
            'lab_technician_id' => auth()->id(),
            'sample_received_at' => $validated['status'] === 'sample_received' ? now() : $labRequest->sample_received_at,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Submit test result (by Technician)
     */
    public function submitResult(Request $request, LabRequest $labRequest)
    {
        $validated = $request->validate([
            'result' => 'required|string',
            'result_data' => 'nullable|array',
        ]);

        $labRequest->update([
            'result' => $validated['result'],
            'result_data' => $validated['result_data'] ?? null,
            'status' => 'to_be_validated', // Nouvelle étape
            'lab_technician_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Résultat envoyé pour validation au médecin biologiste.');
    }



    /**
     * Biologist Dashboard
     */
    public function biologistDashboard()
    {
        $hospitalId = auth()->user()->hospital_id;
        $today = today();
        
        // Main KPIs
        $toValidateCount = LabRequest::where('hospital_id', $hospitalId)
            ->where('status', 'to_be_validated')
            ->count();
            
        $completedToday = LabRequest::where('hospital_id', $hospitalId)
            ->where('status', 'completed')
            ->whereDate('validated_at', $today)
            ->count();

        $urgentPendingCount = LabRequest::where('hospital_id', $hospitalId)
            ->where('status', 'to_be_validated')
            ->where('clinical_info', 'like', '%urgent%')
            ->count();

        // New KPIS
        $averageTAT = LabRequest::where('hospital_id', $hospitalId)
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->whereNotNull('sample_received_at')
            ->get()
            ->avg(fn($req) => $req->completed_at->diffInMinutes($req->sample_received_at));

        // Recent Activity
        $recentValidations = LabRequest::where('hospital_id', $hospitalId)
            ->where('status', 'completed')
            ->with(['patientVital', 'doctor', 'labTechnician'])
            ->latest('validated_at')
            ->limit(5)
            ->get();

        // Chart Data: Volume by day (Last 7 days)
        $workloadData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $workloadData[] = [
                'day' => now()->subDays($i)->translatedFormat('D'),
                'count' => LabRequest::where('hospital_id', $hospitalId)
                    ->whereDate('validated_at', $date)
                    ->count()
            ];
        }

        return view('lab.biologist.dashboard', compact(
            'toValidateCount', 
            'completedToday', 
            'urgentPendingCount',
            'averageTAT',
            'recentValidations',
            'workloadData'
        ));
    }

    /**
     * List of results to be validated
     */
    public function validationList()
    {
        $resultsToValidate = LabRequest::where('hospital_id', auth()->user()->hospital_id)
            ->where('status', 'to_be_validated')
            ->with(['doctor', 'labTechnician', 'patientVital'])
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('lab.biologist.validation', compact('resultsToValidate'));
    }

    /**
     * Validate and publish result (by Biologist)
     */
    public function validateResult(Request $request, LabRequest $labRequest)
    {
        $labRequest->update([
            'status' => 'completed',
            'biologist_id' => auth()->id(),
            'validated_at' => now(),
            'completed_at' => now(), // On considère terminé au moment de la validation
            'is_visible_to_patient' => true, // Auto-partage au portail patient
        ]);

        // Notification au médecin prescripteur seulement après validation
        if ($labRequest->doctor) {
            $labRequest->doctor->notify(new \App\Notifications\LabResultAvailable($labRequest));
        }

        return redirect()->back()->with('success', 'Résultat validé et publié avec succès.');
    }

    /**
     * Update result before validation
     */
    public function updateResult(Request $request, LabRequest $labRequest)
    {
        $validated = $request->validate([
            'result' => 'required|string',
        ]);

        $labRequest->update([
            'result' => $validated['result'],
            'is_visible_to_patient' => true,
        ]);

        return redirect()->back()->with('success', 'Résultat mis à jour avec succès.');
    }

    /**
     * Biologist Stats
     */
    public function biologistStats()
    {
        return view('lab.biologist.stats');
    }

    /**
     * Afficher l'historique des résultats
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $query = LabRequest::where('hospital_id', $user->hospital_id)
            ->where('status', 'completed')
            ->with(['doctor', 'patientVital', 'biologist', 'labTechnician']);
        
        // Biologists see only results they validated
        // Lab technicians see their service results
        if ($user->role === 'doctor_lab') {
            $query->where('biologist_id', $user->id);
        } else {
            $query->where('service_id', $user->service_id);
        }
        
        // Date/period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('completed_at', today());
                    break;
                case 'week':
                    $query->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('completed_at', now()->month)
                          ->whereYear('completed_at', now()->year);
                    break;
                // 'all' = no filter (default)
            }
        }
        
        $query->orderBy('completed_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('test_name', 'like', "%{$search}%")
                  ->orWhere('patient_ipu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('completed_at', $request->date);
        }

        $completedRequests = $query->paginate(20);

        return view('lab.history', compact('completedRequests'));
    }
}

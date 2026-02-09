<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use Illuminate\Http\Request;

class RadiologyController extends Controller
{
    /**
     * Radiologist Dashboard (Doctor Radio)
     */
    public function dashboard()
    {
        $hospitalId = auth()->user()->hospital_id;
        $today = today();
        
        // Filter for Imaging requests only
        // We assume 'imagerie' category or Service with 'Imagerie' in name
        // Ideally we filter by test_category = 'imagerie' if that column exists and is populated correctly
        // Based on LabRequestController::store, 'test_category' is used.
        
        // Main KPIs
        $toValidateCount = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('status', 'to_be_validated')
            ->count();
            
        $completedToday = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('status', 'completed')
            ->whereDate('validated_at', $today)
            ->count();

        $urgentPendingCount = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('status', 'to_be_validated')
            ->where('clinical_info', 'like', '%urgent%')
            ->count();

        // New KPIS
        $averageTAT = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->whereNotNull('sample_received_at')
            ->get()
            ->avg(fn($req) => $req->completed_at->diffInMinutes($req->sample_received_at));

        // Recent Activity
        $recentValidations = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
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
                    ->where('test_category', 'imagerie')
                    ->whereDate('validated_at', $date)
                    ->count()
            ];
        }

        return view('lab.radiologist.dashboard', compact(
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
            ->where('test_category', 'imagerie')
            ->where('status', 'to_be_validated')
            ->with(['doctor', 'labTechnician', 'patientVital'])
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('lab.radiologist.validation', compact('resultsToValidate'));
    }

    /**
     * Validate and publish result (by Radiologist)
     */
    public function validateResult(Request $request, LabRequest $labRequest)
    {
        // Ensure this is an imaging request
        if ($labRequest->test_category !== 'imagerie') {
            abort(403, 'Unauthorized action.');
        }

        $labRequest->update([
            'status' => 'completed',
            'biologist_id' => auth()->id(), // We might want to rename this column to 'validator_id' later, but for now we reuse it
            'validated_at' => now(),
            'completed_at' => now(),
        ]);

        if ($labRequest->doctor) {
            $labRequest->doctor->notify(new \App\Notifications\LabResultAvailable($labRequest));
        }

        return redirect()->back()->with('success', 'Examen validé et publié avec succès.');
    }

    /**
     * Radiologist Stats
     */
    public function stats()
    {
        return view('lab.radiologist.stats');
    }
    /**
     * Radio Technician Dashboard
     */
    public function technicianDashboard()
    {
        $hospitalId = auth()->user()->hospital_id;

        $completedToday = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->whereDate('updated_at', now()->toDateString())
            ->where('status', 'completed')
            ->where('is_paid', true) 
            ->count();

        $pendingRequests = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->whereIn('status', ['pending', 'sample_received', 'in_progress', 'to_be_validated'])
            ->where('is_paid', true) 
            ->with(['doctor', 'service'])
            ->latest()
            ->get();

        return view('lab.radio_technician.dashboard', compact('completedToday', 'pendingRequests'));
    }

    /**
     * Radio Technician Worklist
     */
    public function technicianWorklist(Request $request)
    {
        $hospitalId = auth()->user()->hospital_id;
        $filter = $request->query('filter', 'pending'); 

        $query = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('is_paid', true) 
            ->with(['doctor', 'service'])
            ->latest();

        if ($filter === 'urgent') {
             $query->whereIn('status', ['pending', 'sample_received', 'in_progress'])
                   ->where('clinical_info', 'like', '%urgent%');
        } elseif ($filter === 'all') {
             $query->whereIn('status', ['pending', 'sample_received', 'in_progress', 'to_be_validated']);
        } else {
             $query->whereIn('status', ['pending', 'sample_received', 'in_progress']);
        }

        $pendingRequests = $query->get();

        return view('lab.radio_technician.worklist', compact('pendingRequests'));
    }

    /**
     * Radio Technician History
     */
    public function technicianHistory(Request $request)
    {
        $hospitalId = auth()->user()->hospital_id;
        
        $query = LabRequest::where('hospital_id', $hospitalId)
            ->where('test_category', 'imagerie')
            ->where('status', 'completed')
            ->with(['doctor', 'labTechnician', 'biologist']) // biologist here refers to radiologist validator
            ->latest('completed_at');

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'like', "%$search%")
                  ->orWhere('patient_ipu', 'like', "%$search%")
                  ->orWhere('test_name', 'like', "%$search%");
            });
        }

        if ($request->date) {
            $query->whereDate('completed_at', $request->date);
        }

        $completedRequests = $query->paginate(20);

        return view('lab.radio_technician.history', compact('completedRequests'));
    }

    /**
     * Radio Technician Inventory
     */
    public function technicianInventory()
    {
        $inventory = \App\Models\LabInventory::where('hospital_id', auth()->user()->hospital_id)
            ->orderBy('name')
            ->get();

        return view('lab.radio_technician.inventory', compact('inventory'));
    }

    /**
     * Update status (Technician)
     */
    public function updateStatus(Request $request, $id)
    {
        $labRequest = LabRequest::findOrFail($id);
        
        // Security check
        if ($labRequest->test_category !== 'imagerie') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:sample_received,in_progress,completed',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'lab_technician_id' => auth()->id(),
        ];

        if ($validated['status'] === 'sample_received') {
            $updateData['sample_received_at'] = now();
        } elseif ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
        }

        $labRequest->update($updateData);

        $message = $validated['status'] === 'sample_received' ? 'Patient pris en charge.' : 'Examen démarré.';
        return back()->with('success', $message);
    }

    /**
     * Store result (Technician)
     */
    public function storeResult(Request $request, $id)
    {
        $labRequest = LabRequest::findOrFail($id);
        
        // Security check
        if ($labRequest->test_category !== 'imagerie') {
            abort(403);
        }

        $validated = $request->validate([
            'result' => 'required|string',
        ]);

        $labRequest->update([
            'result' => $validated['result'],
            'status' => 'to_be_validated', // Sends to Radiologist
            'lab_technician_id' => auth()->id(),
        ]);

        return redirect()->route('lab.radio_technician.worklist')->with('success', 'Résultat/Compte-rendu enregistré et envoyé pour validation.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionController extends Controller
{
    public function create(Request $request)
    {
        $patientId = $request->query('patient_id');
        
        // On récupère le patient sans les scopes globaux pour éviter les problèmes de session d'établissement
        $patient = Patient::withoutGlobalScopes()->find($patientId);
        
        if (!$patient) {
            abort(404, "Patient non trouvé.");
        }

        // Vérification de sécurité manuelle : même hôpital
        if (!auth()->user()->isAdmin() && $patient->hospital_id != auth()->user()->hospital_id) {
            abort(403, "Vous n'avez pas accès à ce patient.");
        }

        $this->authorize('create', Prescription::class);
        return view('prescriptions.create', compact('patient'));
    }

    public function store(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patient = Patient::withoutGlobalScopes()->findOrFail($patientId);
        
        // Sécurité manuelle
        if (!auth()->user()->isAdmin() && $patient->hospital_id !== auth()->user()->hospital_id) {
            abort(403);
        }

        $this->authorize('create', Prescription::class);

        $validated = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'medication'   => 'required|string',
            'duration'     => 'nullable|string',
            'instructions' => 'nullable|string',
            'category'     => 'nullable|string|in:pharmacy,nurse',
        ]);

        Prescription::create([
            'patient_id'      => $validated['patient_id'],
            'doctor_id'       => Auth::id(),
            'hospital_id'     => Auth::user()->hospital_id,
            'medication'      => $validated['medication'],
            'frequency'       => '1x/jour', 
            'start_date'      => now(),
            'instructions'    => "Durée : " . ($validated['duration'] ?? 'N/A') . ". " . ($validated['instructions'] ?? ''),
            'is_signed'       => false,
            'status'          => 'active',
            'allergy_checked' => false,
            'category'        => $validated['category'] ?? 'pharmacy',
            'is_visible_to_patient' => ($validated['category'] ?? 'pharmacy') === 'pharmacy',
        ]);

        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Prescription enregistrée. Pensez à la signer.');
    }

    public function sign(Prescription $prescription)
    {
        $this->authorize('sign', $prescription);

        $prescription->update([
            'is_signed'      => true,
            'signed_at'      => now(),
            'signature_hash' => hash('sha256', Auth::id() . now() . $prescription->id),
        ]);

        return back()->with('success', 'Ordonnance signée numériquement.');
    }

    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'medication' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        $prescription->update([
            'medication'   => $validated['medication'],
            'instructions' => $validated['instructions'] ?? $prescription->instructions,
        ]);

        return back()->with('success', 'Prescription mise à jour.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return back()->with('success', 'Prescription supprimée.');
    }

    public function share($id)
    {
        $prescription = Prescription::withoutGlobalScopes()->findOrFail($id);
        $prescription->update(['is_visible_to_patient' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'L\'ordonnance a été transmise au portail patient.'
        ]);
    }

    public function downloadPdf(Prescription $prescription)
    {
        $patient = $prescription->patient;
        $doctor = $prescription->doctor;
        
        $pdf = Pdf::loadView('pdf.prescription_pdf', compact('prescription', 'patient', 'doctor'));
        
        return $pdf->download('Ordonnance_' . $prescription->id . '.pdf');
    }
}
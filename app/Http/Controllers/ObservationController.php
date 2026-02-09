<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicalObservation;
use Illuminate\Support\Facades\Auth;

class ObservationController extends Controller
{
    // Créer une nouvelle fiche
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'nullable|string',
            'temperature' => 'nullable|numeric|between:30,45', 
            'pulse' => 'nullable|integer|between:20,250',
            'weight' => 'nullable|numeric|between:1,500',
            'height' => 'nullable|numeric|between:30,300',
            'notes' => 'nullable|string',
        ]);

        $type = $request->input('type', 'vitals');
        $is_critical = false;

        if ($type === 'vitals') {
            $is_critical = ($request->temperature >= 38.5 || $request->temperature <= 35.5 || $request->pulse >= 120 || $request->pulse <= 50);
        }

        ClinicalObservation::create([
            'patient_id' => $request->patient_id,
            'user_id' => Auth::id(),
            'type' => $type,
            'temperature' => $request->temperature,
            'pulse' => $request->pulse,
            'weight' => $request->weight,
            'height' => $request->height,
            'notes' => $request->notes,
            'observation_datetime' => now(),
            'is_critical' => $is_critical,
            'value' => $type === 'detailed' ? ($request->notes ?? '') : '', 
        ]);

        return back()->with('success', 'Examen enregistré.');
    }

    public function update(Request $request, $id)
    {
        $obs = ClinicalObservation::findOrFail($id);
        
        $request->validate([
            'temperature' => 'nullable|numeric|between:30,45',
            'pulse' => 'nullable|integer|between:20,250',
        ]);

        $is_critical = ($request->temperature >= 38.5 || $request->temperature <= 35.5 || $request->pulse >= 120 || $request->pulse <= 50);

        $obs->update([
            'weight' => $request->weight,
            'height' => $request->height,
            'temperature' => $request->temperature,
            'pulse' => $request->pulse,
            'notes' => $request->notes,
            'is_critical' => $is_critical,
            'value' => $obs->value ?? '',
        ]);

        return back()->with('success', 'Fiche mise à jour avec succès.');
    }

    public function destroy($id)
    {
        ClinicalObservation::findOrFail($id)->delete();
        return back()->with('success', 'Fiche supprimée.');
    }

    public function sendToPatient($id)
    {
        $fiche = ClinicalObservation::findOrFail($id);
        $fiche->update(['is_visible_to_patient' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'L\'examen a été transmis au portail patient.'
        ]);
    }
}
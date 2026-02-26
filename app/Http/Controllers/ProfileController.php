<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Roles using the premium app layout (HospitSIS aesthetic)
        if ($user->role === 'admin') {
            $user->load('hospital.subscriptionPlan', 'service');
            return view('admin.profile', [
                'user' => $user,
            ]);
        }

        if ($user->isDoctor()) {
            $user->load(['service', 'hospital']);
            $availability = \App\Models\DoctorAvailability::where('doctor_id', $user->id)
                ->where('hospital_id', $user->hospital_id)
                ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
                ->get();
                
            return view('medecin.profile', [
                'user' => $user,
                'availability' => $availability
            ]);
        }

        // Default for other staff (Secretary, Pharmacist, Nurse, etc.) 
        // using the new premium staff profile view
        $user->load(['service', 'hospital']);
        return view('profile.staff', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Initialise le planning par défaut du médecin.
     */
    public function initializeAvailability(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->isDoctor()) {
            return redirect()->route('profile.edit')->with('error', 'Action non autorisée.');
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $hospitalId = $user->hospital_id;

        foreach ($days as $day) {
            \App\Models\DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $user->id,
                    'day_of_week' => $day,
                    'hospital_id' => $hospitalId,
                ],
                [
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'slot_duration' => 30,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('profile.edit')->with('success', 'Votre planning a été initialisé avec les horaires par défaut (08h00 - 16h00).');
    }

    /**
     * Met à jour un créneau de disponibilité.
     */
    public function updateAvailability(Request $request): RedirectResponse
    {
        $request->validate([
            'slot_id' => 'required|exists:doctor_availability,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_active' => 'nullable',
        ]);

        $user = auth()->user();
        $slot = \App\Models\DoctorAvailability::where('doctor_id', $user->id)->findOrFail($request->slot_id);

        $slot->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Disponibilité mise à jour.');
    }

    /**
     * Alterne l'état d'un créneau.
     */
    public function toggleAvailabilitySlot($id): RedirectResponse
    {
        $user = auth()->user();
        $slot = \App\Models\DoctorAvailability::where('doctor_id', $user->id)->findOrFail($id);

        $slot->update(['is_active' => !$slot->is_active]);

        return redirect()->route('profile.edit')->with('success', 'Statut du créneau mis à jour.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Met à jour les paramètres de paiement de l'hôpital.
     */
    public function updatePaymentSettings(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin d'un hôpital
        if ($user->role !== 'admin' || !$user->hospital_id) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }

        // Validation
        $request->validate([
            'orange_money_number' => 'nullable|string|max:20',
            'mtn_money_number' => 'nullable|string|max:20',
            'moov_money_number' => 'nullable|string|max:20',
            'wave_number' => 'nullable|string|max:20',
            'qr_orange' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_mtn' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_moov' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_wave' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $hospital = $user->hospital;
            
            // Mettre à jour les numéros de téléphone
            $hospital->payment_orange_number = $request->orange_money_number;
            $hospital->payment_mtn_number = $request->mtn_money_number;
            $hospital->payment_moov_number = $request->moov_money_number;
            $hospital->payment_wave_number = $request->wave_number;

            // Traitement des uploads de QR Code
            $qrFields = [
                'qr_orange' => 'payment_qr_orange',
                'qr_mtn' => 'payment_qr_mtn',
                'qr_moov' => 'payment_qr_moov',
                'qr_wave' => 'payment_qr_wave',
            ];

            foreach ($qrFields as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    // Supprimer l'ancienne image si elle existe
                    if ($hospital->$dbColumn && Storage::disk('public')->exists($hospital->$dbColumn)) {
                        Storage::disk('public')->delete($hospital->$dbColumn);
                    }

                    // Sauvegarder la nouvelle image
                    $path = $request->file($inputName)->store('payment_qrs', 'public');
                    $hospital->$dbColumn = $path;
                }
            }

            $hospital->save();

            return redirect()->back()->with('success', 'Configuration API de paiement mise à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour la photo de profil de l'utilisateur.
     */
    public function updateProfilePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $user = auth()->user();
            
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Sauvegarder la nouvelle photo
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
            $user->save();

            return redirect()->back()->with('success', 'Photo de profil mise à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour les préférences de notification de l'utilisateur.
     */
    public function updateNotificationSettings(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
        ]);

        return redirect()->back()->with('success', 'Préférences de notification mises à jour.');
    }
}

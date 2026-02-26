<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PatientVital;

class NotificationController extends Controller
{
    /**
     * Marquer toutes les notifications comme lues pour le guard spécifié ou détecté.
     */
    public function markAllAsRead(Request $request)
    {
        $guard = $request->get('guard');
        
        // Si aucun guard n'est spécifié, on essaie de détecter le guard actif
        if (!$guard) {
            if (Auth::guard('superadmin')->check()) $guard = 'superadmin';
            elseif (Auth::guard('medecin_externe')->check()) $guard = 'medecin_externe';
            else $guard = 'web';
        }

        $user = Auth::guard($guard)->user();

        if ($user) {
            // Standard Laravel Notifications
            $user->unreadNotifications->markAsRead();

            // Ad-hoc PatientVital notifications (for doctors/nurses using 'web' guard)
            if ($guard === 'web') {
                $query = PatientVital::where('hospital_id', $user->hospital_id)
                    ->whereNull('read_at');
                
                if ($user->role === 'doctor' || $user->role === 'internal_doctor') {
                    $query->where('doctor_id', $user->id);
                }
                
                $query->update(['read_at' => now()]);
            }
            
            return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
        }

        return back()->with('error', 'Action non autorisée ou session expirée.');
    }

    /**
     * Marquer une notification spécifique comme lue.
     */
    public function markAsRead($id, Request $request)
    {
        $guard = $request->get('guard');
        
        if (!$guard) {
            if (Auth::guard('superadmin')->check()) $guard = 'superadmin';
            elseif (Auth::guard('medecin_externe')->check()) $guard = 'medecin_externe';
            else $guard = 'web';
        }

        $user = Auth::guard($guard)->user();
        $type = $request->get('type', 'standard');

        if (!$user) return back()->with('error', 'Session expirée.');

        if ($type === 'vital') {
            $vital = PatientVital::where('id', $id)
                ->where('hospital_id', $user->hospital_id)
                ->first();
            
            if ($vital) {
                $vital->update(['read_at' => now()]);
            }
        } else {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }
}

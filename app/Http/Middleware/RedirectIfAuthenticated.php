<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        \Log::info('RedirectIfAuthenticated handle', [
            'url' => $request->fullUrl(),
            'guards' => $guards
        ]);
        // dd('RedirectIfAuthenticated handle', $guards);
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Gestion spécifique pour les patients
                if ($guard === 'patients') {
                    return redirect()->route('patient.dashboard');
                }

                // Gestion spécifique pour les médecins externes
                if ($guard === 'medecin_externe') {
                    return redirect()->route('external.doctor.external.dashboard');
                }

                // Pour le guard 'web' (User standard)
                // Redirection selon le rôle
                return match($user->role ?? null) {
                    'doctor', 'internal_doctor' => redirect()->route('medecin.dashboard'),
                    'nurse' => redirect()->route('nurse.dashboard'),
                    'medecin', 'external_doctor', 'specialist' => redirect()->route('external.doctor.external.dashboard'),
                    'cashier' => redirect()->route('cashier.dashboard'),
                    'lab_technician' => redirect()->route('lab.dashboard'),
                    'doctor_lab' => redirect()->route('lab.biologist.dashboard'),
                    'admin', 'administrative' => redirect()->route('dashboard'),
                    default => redirect('/dashboard')
                };
            }
        }

        return $next($request);
    }
}
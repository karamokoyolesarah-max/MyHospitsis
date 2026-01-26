<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    /**
     * Rediriger l'utilisateur vers son dashboard selon son rôle
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $currentRoute = $request->route()->getName();
            
            // Définir les dashboards par rôle
            $roleDashboards = [
                'internal_doctor' => 'doctor.internal.dashboard',
                'doctor' => 'medecin.dashboard',
                'external_doctor' => 'doctor.external.dashboard',
                'medecin' => 'external.doctor.external.dashboard',
                'admin' => 'dashboard',
                'administrative' => 'dashboard',
                'nurse' => 'dashboard',
                'receptionist' => 'dashboard',
            ];
            
            // Définir les routes autorisées par rôle
            $roleRoutes = [
                'internal_doctor' => [
                    'doctor.internal.*',
                    'patients.*',
                    'appointments.*',
                    'prescriptions.*',
                    'admissions.*',
                    'rooms.*',
                    'medical-records.*',
                    'observations.*',
                    'profile.*',
                    'logout'
                ],
                'external_doctor' => [
                    'doctor.external.*',
                    'external.*',
                    'patients.show',
                    'patients.medical-file',
                    'appointments.*',
                    'prescriptions.*',
                    'medical-records.*',
                    'profile.*',
                    'logout'
                ],
                'medecin' => [
                    'doctor.external.*',
                    'external.*',
                    'patients.show',
                    'patients.medical-file',
                    'appointments.*',
                    'prescriptions.*',
                    'medical-records.*',
                    'profile.*',
                    'logout'
                ],
                'admin' => [
                    'dashboard',
                    'patients.*',
                    'appointments.*',
                    'admissions.*',
                    'prescriptions.*',
                    'medical-records.*',
                    'rooms.*',
                    'users.*',
                    'reports.*',
                    'invoices.*',
                    'audit-logs.*',
                    'profile.*',
                    'logout'
                ],
                'administrative' => [
                    'dashboard',
                    'patients.*',
                    'appointments.*',
                    'admissions.*',
                    'rooms.*',
                    'users.*',
                    'reports.*',
                    'invoices.*',
                    'profile.*',
                    'logout'
                ],
                'nurse' => [
                    'dashboard',
                    'patients.*',
                    'appointments.*',
                    'admissions.*',
                    'medical-records.*',
                    'observations.*',
                    'nursing-notes.*',
                    'profile.*',
                    'logout'
                ],
                'receptionist' => [
                    'dashboard',
                    'patients.*',
                    'appointments.*',
                    'admissions.index',
                    'admissions.show',
                    'profile.*',
                    'logout'
                ],
            ];
            
            $userRole = $user->role;
            $allowedRoutes = $roleRoutes[$userRole] ?? [];
            
            // Vérifier si l'utilisateur accède à une route non autorisée
            $isAuthorized = false;
            foreach ($allowedRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute)) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            // Si pas autorisé, rediriger vers son dashboard
            if (!$isAuthorized && isset($roleDashboards[$userRole])) {
                return redirect()->route($roleDashboards[$userRole])
                    ->with('error', 'Accès non autorisé à cette section.');
            }
        }
        
        return $next($request);
    }
}
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
                'doctor_lab' => 'lab.biologist.dashboard',
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
                'doctor_lab' => [
                    'lab.biologist.*',
                    'lab.history',
                    'lab.requests.validate',
                    'profile.*',
                    'logout'
                ],
            ];
            
            $userRole = strtolower($user->role);
            
            // Allow all routes if user is admin
            if ($user->isAdmin() || $userRole === 'admin') {
                return $next($request);
            }

            $allowedRoutes = $roleRoutes[$userRole] ?? [];
            
            // Unification Docteur/Médecin : Si l'un est autorisé, l'autre le devient
            if (empty($allowedRoutes)) {
                if ($userRole === 'medecin') $allowedRoutes = $roleRoutes['doctor'] ?? [];
                if ($userRole === 'doctor') $allowedRoutes = $roleRoutes['medecin'] ?? [];
            }

            // Accès Pôle Technique pour les médecins génériques
            if ($user->isTechnical() && (in_array($userRole, ['doctor', 'medecin']))) {
                $allowedRoutes = array_merge($allowedRoutes, $roleRoutes['doctor_lab'] ?? []);
                // On peut aussi ajouter doctor_radio si besoin
            }
            
            // Vérifier si l'utilisateur accède à une route autorisée
            $isAuthorized = false;
            foreach ($allowedRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute)) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            // Si pas autorisé, on laisse passer si c'est une route commune (profil, logout, etc.)
            $commonRoutes = ['profile.*', 'logout', 'dashboard', 'home'];
            foreach ($commonRoutes as $pattern) {
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
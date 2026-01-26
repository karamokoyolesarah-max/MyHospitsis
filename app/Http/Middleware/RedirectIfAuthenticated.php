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
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirection selon le rôle (compatible avec tous les guards)
                return match($user->role) {
                    'doctor', 'internal_doctor' => redirect()->route('medecin.dashboard'),
                    'nurse' => redirect()->route('nurse.dashboard'),
                    'medecin', 'external_doctor', 'specialist' => redirect()->route('external.doctor.external.dashboard'),
                    'cashier' => redirect()->route('cashier.dashboard'),
                    'admin', 'administrative' => redirect()->route('dashboard'),
                    default => redirect('/dashboard')
                };
            }
        }

        return $next($request);
    }
}
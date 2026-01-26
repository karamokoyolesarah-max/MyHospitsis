<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        // Ne pas rediriger si on est déjà sur une page d'authentification ou si on essaie de s'inscrire/se déconnecter
        if ($request->is('login') || $request->is('logout') || $request->is('*/login') || $request->is('*/register') || $request->is('register/*') || $request->is('select-*')) {
            return $next($request);
        }

        $user = auth()->user() ?: auth()->guard('medecin_externe')->user();

        if ($user) {
            // Skip check for superadmin
            if ($user instanceof \App\Models\SuperAdmin) {
                return $next($request);
            }

            // Handle MedecinExterne
            if ($user instanceof \App\Models\MedecinExterne) {
                if ($user->statut !== 'actif') {
                    // Si l'utilisateur vient de s'inscrire, on le laisse voir une page de confirmation ou on le redirige proprement
                    // Mais on ne le bloque pas s'il essaie de se déconnecter ou de voir son statut
                    auth()->guard('medecin_externe')->logout();
                    return redirect()->route('external.login')
                        ->withErrors(['email' => 'Votre compte est en attente de validation ou a été désactivé.']);
                }
            } 
            // Handle regular User
            else if (isset($user->is_active) && !$user->is_active) {
                auth()->logout();
                return redirect()->route('login')
                    ->withErrors(['identifier' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.']);
            }
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !(auth()->user() instanceof \App\Models\SuperAdmin)) {
            $user = auth()->user();
            
            // Si c'est un utilisateur classique ou patient, on check is_active
            if (isset($user->is_active) && !$user->is_active) {
                auth()->logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.']);
            }

            // Si c'est un médecin externe, on check statut
            if ($user instanceof \App\Models\MedecinExterne && $user->statut !== 'actif') {
                auth()->logout();
                return redirect()->route('external.login')
                    ->withErrors(['email' => 'Votre accès est restreint (Compte non activé ou suspendu).']);
            }
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    
    use AuthenticatesUsers;
/**
 * Intercepte la connexion pour le Super Admin
 */
 
public function login(Request $request)
{
    // 1. Logique Super Admin (ton code actuel)
    $superAdminEmail = env('SUPER_ADMIN_EMAIL');
    $superAdminPassword = env('SUPER_ADMIN_PASSWORD');
    $superAdminCode = env('SUPER_ADMIN_ACCESS_CODE');

    if ($request->email === $superAdminEmail) {
        if ($request->password === $superAdminPassword && $request->access_code === $superAdminCode) {
            $user = \App\Models\User::where('email', $superAdminEmail)->first();
            if ($user) {
                auth()->login($user);
                return $this->authenticated($request, $user);
            }
        }
        throw ValidationException::withMessages([
            'email' => ['Accès Super Admin refusé. Vérifiez vos codes de sécurité.'],
        ]);
    }

    // 2. Tentative de connexion normale (Users / Patients)
    $this->validateLogin($request);

    // On essaie d'abord le guard par défaut (table users)
    if (auth()->guard('web')->attempt($this->credentials($request), $request->filled('remember'))) {
        return $this->sendLoginResponse($request);
    }

    // 3. TENTATIVE POUR LE MÉDECIN EXTERNE (Ta table spécifique)
    // On utilise le guard 'medecin_externe' défini dans ton config/auth.php
    if (auth()->guard('medecin_externe')->attempt($this->credentials($request), $request->filled('remember'))) {
        $user = auth()->guard('medecin_externe')->user();
        
        // Appelle ta fonction personnalisée pour gérer la session hospital_id et les redirections
        return $this->authenticated($request, $user);
    }

    // Si aucune des tentatives ne fonctionne
    return $this->sendFailedLoginResponse($request);
}
    protected function authenticated(Request $request, $user)
    {
        // --- ÉTAPE CRUCIALE POUR LE MULTI-HÔPITAL ---
        // On stocke l'ID de l'hôpital de l'utilisateur en session.
        if (isset($user->hospital_id)) {
            Session::put('hospital_id', $user->hospital_id);
        }

        // 1. Redirection pour l'Admin
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        // 2. Redirection pour le Médecin (Interne)
        if ($user->role === 'doctor') {
            return redirect()->route('medecin.dashboard');
        }

        // 3. Redirection pour l'Infirmier
        if ($user->role === 'nurse') {
            return redirect()->route('nurse.dashboard');
        }

        // 4. Redirection pour le Médecin Externe
        if ($user->role === 'medecin') {
            return redirect()->route('external.doctor.external.dashboard');
        }

        // Par défaut
        return redirect()->intended('/home');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
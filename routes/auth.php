 <?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password};
use App\Http\Controllers\MFAController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Routes d'authentification pour HospitSIS
| Compatible avec Laravel Breeze ou système personnalisé
*/

// ============ INSCRIPTION (Désactivée pour les professionnels) ============
// L'inscription des professionnels se fait uniquement via l'admin

// ============ CONNEXION ============
Route::get('login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');



// ============ DÉCONNEXION ============
Route::post('logout', function (Request $request) {
    // Log de déconnexion
    if (auth()->check()) {
        \App\Models\AuditLog::log('logout', 'User', auth()->id(), [
            'description' => 'Déconnexion',
        ]);
    }

    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

// ============ MOT DE PASSE OUBLIÉ ============
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.reset.update');

// ============ VÉRIFICATION EMAIL (Optionnel) ============
Route::get('verify-email', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('verify-email/{id}/{hash}', function (Request $request) {
    $user = \App\Models\User::findOrFail($request->route('id'));

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('dashboard');
    }

    $user->markEmailAsVerified();

    return redirect()->route('dashboard')->with('success', 'Email vérifié avec succès.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'Un lien de vérification a été envoyé à votre adresse email.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ============ CONFIRMATION MOT DE PASSE ============
Route::get('confirm-password', function () {
    return view('auth.confirm-password');
})->middleware('auth')->name('password.confirm');

Route::post('confirm-password', function (Request $request) {
    if (!Hash::check($request->password, $request->user()->password)) {
        return back()->withErrors([
            'password' => 'Le mot de passe est incorrect.',
        ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended();
})->middleware('auth');

// ============ AUTHENTIFICATION MULTI-FACTEURS (MFA) ============
Route::middleware('auth')->group(function () {
    Route::put('password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
    
    Route::get('mfa/verify', function () {
        return view('auth.mfa-verify');
    })->name('mfa.verify');

    Route::post('mfa/verify', function (Request $request) {
        $request->validate([
            'code' => 'required|string|min:6|max:6',
        ]);

        // Vérification du code MFA (implémentation simplifiée)
        // Dans une vraie application, utilisez une librairie comme PragmaRX/Google2FA
        
        $user = auth()->user();
        
        // Simulation de vérification (à remplacer par une vraie vérification)
        if ($request->code === '123456') { // Code de test
            $request->session()->put('mfa_verified', true);
            
            \App\Models\AuditLog::log('mfa_verified', 'User', $user->id, [
                'description' => 'Authentification MFA réussie',
            ]);
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'code' => 'Code MFA incorrect.',
        ]);
    })->name('mfa.verify.post');

    Route::get('mfa/setup', [MFAController::class, 'setup'])->name('mfa.setup');

    Route::post('mfa/setup', [MFAController::class, 'enable'])->name('mfa.setup.post');

    Route::post('mfa/disable', [MFAController::class, 'disable'])->name('mfa.disable');
});

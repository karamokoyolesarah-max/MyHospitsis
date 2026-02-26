<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Chargement des routes d'authentification
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
                
            // Chargement des routes Nurse
            Route::middleware('web')
                ->group(base_path('routes/nurse.php'));

            // AJOUT : Chargement des routes Super Admin
            Route::middleware('web')
                ->group(base_path('routes/superadmin.php'));
        },
    )
   ->withMiddleware(function (Middleware $middleware) {
        // Redirection intelligente corrigée
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'active_user' => \App\Http\Middleware\EnsureUserIsActive::class,
            'mfa' => \App\Http\Middleware\VerifyMfa::class,
            'log_access' => \App\Http\Middleware\LogSensitiveAccess::class,
            'check_service' => \App\Http\Middleware\CheckServiceAccess::class,
            'superadmin.verified' => \App\Http\Middleware\EnsureSuperAdminVerified::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Exclude webhook endpoints from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'payment/cinetpay/webhook',
            'webhook/recharge',
            'admin-system/payment/cinetpay/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
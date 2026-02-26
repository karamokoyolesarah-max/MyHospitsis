<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

// --- ROUTES DE CONNEXION (NON PROTÉGÉES) ---
Route::prefix('superadmin')->middleware('guest:superadmin')->group(function () {
    Route::get('/login', [SuperAdminController::class, 'showLoginForm'])
        ->name('superadmin.login');
    Route::post('/login', [SuperAdminController::class, 'login'])
        ->name('superadmin.login.post');
});

// --- ROUTES DE VÉRIFICATION (Nécessite d'être connecté mais pas encore vérifié) ---
Route::prefix('admin-system')->middleware(['auth:superadmin'])->group(function () {
    Route::get('/verify', [SuperAdminController::class, 'showVerifyForm'])
        ->name('superadmin.verify');
    Route::post('/verify', [SuperAdminController::class, 'verifyCode'])
        ->name('superadmin.verify.post');
        
    // Webhook endpoint for CinetPay (CSRF excluded for admin-system/* in Kernel)
    Route::post('/payment/cinetpay/webhook', [SubscriptionController::class, 'handleCinetpayWebhook'])
        ->name('superadmin.cinetpay.webhook')
        ->withoutMiddleware(['auth:superadmin']);
});

// --- ROUTES PROTÉGÉES (Nécessitent d'être connecté ET vérifié) ---
Route::middleware(['auth:superadmin', 'superadmin.verified'])->prefix('admin-system')->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])
        ->name('superadmin.dashboard');

    // Gestion des Hôpitaux
    Route::get('/hospitals', [SuperAdminController::class, 'allHospitalsList'])
        ->name('superadmin.hospitals.index');
    Route::post('/hospitals/store', [SuperAdminController::class, 'storeHospital'])
        ->name('superadmin.hospitals.store');
    Route::get('/hospitals/{hospital}/details', [SuperAdminController::class, 'getHospitalDetails'])
        ->name('superadmin.hospitals.details');
    Route::post('/hospitals/{hospital}/toggle-status', [SuperAdminController::class, 'toggleHospitalStatus'])
        ->name('superadmin.hospitals.toggle-status');
    Route::post('/hospitals/{hospital}/initialize-cashiers', [SuperAdminController::class, 'initializeDefaultCashiers'])
        ->name('superadmin.hospitals.initialize-cashiers');

    // Gestion des Spécialistes
    Route::get('/specialists', [SuperAdminController::class, 'allSpecialistsList'])
        ->name('superadmin.specialists.index');
    Route::get('/specialists/{id}/details', [SuperAdminController::class, 'getSpecialistDetails'])
        ->name('superadmin.specialists.details');
    Route::get('/specialists/{id}/profile', [SuperAdminController::class, 'showSpecialistProfile'])
        ->name('superadmin.specialists.show');
    Route::post('/specialists/{id}/validate', [SuperAdminController::class, 'validateSpecialist'])
        ->name('superadmin.specialists.validate');

    // === SUBSCRIPTION PLANS MANAGEMENT ===
    Route::get('/subscription-plans', [SuperAdminController::class, 'getSubscriptionPlans'])
        ->name('superadmin.subscription-plans.index');
    Route::post('/subscription-plans', [SuperAdminController::class, 'storeSubscriptionPlan'])
        ->name('superadmin.subscription-plans.store');
    Route::put('/subscription-plans/{plan}', [SuperAdminController::class, 'updateSubscriptionPlan'])
        ->name('superadmin.subscription-plans.update');
    Route::delete('/subscription-plans/{plan}', [SuperAdminController::class, 'deleteSubscriptionPlan'])
        ->name('superadmin.subscription-plans.destroy');

    // === COMMISSION RATES MANAGEMENT ===
    Route::get('/commission-rates', [SuperAdminController::class, 'getCommissionRates'])
        ->name('superadmin.commission-rates.index');
    Route::get('/commission-rates/{rate}', [SuperAdminController::class, 'showCommissionRate'])
        ->name('superadmin.commission-rates.show');
    Route::post('/commission-rates', [SuperAdminController::class, 'storeCommissionRate'])
        ->name('superadmin.commission-rates.store');
    Route::put('/commission-rates/{rate}', [SuperAdminController::class, 'updateCommissionRate'])
        ->name('superadmin.commission-rates.update');
    Route::delete('/commission-rates/{rate}', [SuperAdminController::class, 'deleteCommissionRate'])
        ->name('superadmin.commission-rates.destroy');

    // === FINANCIAL MONITORING ===
    Route::get('/financial-monitoring', [SuperAdminController::class, 'getFinancialMonitoring'])
        ->name('superadmin.financial-monitoring');

    // Test endpoint for debugging
    Route::get('/test-financial', function() {
        return response()->json(['test' => 'ok', 'timestamp' => now()]);
    })->name('superadmin.test-financial');
    
    Route::post('/specialists/{specialist}/block-wallet', [SuperAdminController::class, 'blockSpecialistWallet'])
        ->name('superadmin.specialists.block-wallet');
    Route::post('/specialists/{specialist}/unblock-wallet', [SuperAdminController::class, 'unblockSpecialistWallet'])
        ->name('superadmin.specialists.unblock-wallet');
    Route::post('/specialists/{specialist}/adjust-balance', [SuperAdminController::class, 'adjustSpecialistBalance'])
        ->name('superadmin.specialists.adjust-balance');
    Route::post('/commission/deduct', [SuperAdminController::class, 'deductCommission'])
        ->name('superadmin.commission.deduct');
    Route::post('/specialists/activation', [SuperAdminController::class, 'processSpecialistActivation'])
        ->name('superadmin.specialists.activation');
    Route::get('/test-specialists', [SuperAdminController::class, 'getTestSpecialists'])
        ->name('superadmin.test-specialists');
    Route::post('/specialists/test-recharge', [SuperAdminController::class, 'testSpecialistRecharge'])
        ->name('superadmin.specialists.test-recharge');

    // Finance (Legacy - to be replaced)
    Route::get('/billing', function() {
        return "Page Finance en attente de configuration";
    })->name('superadmin.billing');

    // === INVOICES MANAGEMENT ===
    Route::get('/invoices', [SuperAdminController::class, 'getInvoices'])
        ->name('superadmin.invoices.index');

    // === WAVE VALIDATION MANAGEMENT ===
    Route::get('/wave-validation', [\App\Http\Controllers\SuperAdmin\WaveValidationController::class, 'index'])
        ->name('superadmin.wave.index');
    Route::post('/wave-validation/{recharge}/validate', [\App\Http\Controllers\SuperAdmin\WaveValidationController::class, 'approve'])
        ->name('superadmin.wave.validate');
    Route::post('/wave-validation/{recharge}/reject', [\App\Http\Controllers\SuperAdmin\WaveValidationController::class, 'reject'])
        ->name('superadmin.wave.reject');

    // === PATIENT PAYMENTS TRACKING ===
    Route::get('/patient-payments/data', [SuperAdminController::class, 'getPatientPaymentsData'])
        ->name('superadmin.patient-payments.data');

    // === SETTINGS ===
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])
        ->name('superadmin.settings.update');

    // Déconnexion
    Route::post('/logout', [SuperAdminController::class, 'logout'])
        ->name('superadmin.logout');
});
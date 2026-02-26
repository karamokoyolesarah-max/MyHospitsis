<?php

use App\Http\Controllers\{
    DashboardController, PatientController, AppointmentController,
    AdmissionController, PrescriptionController, MedicalRecordController,
    UserController, RoomController, InvoiceController, PortalController,
    ReportController, ObservationController, NurseController, ServiceController,
    HospitalController, PrestationController, CashierController, ProfileController,
    LabRequestController, PharmacyController, SecretaryController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\PatientPortalController;
use App\Http\Controllers\Auth\PatientAuthController;
use App\Http\Controllers\Medecin\MedecinDashboardController;
use App\Http\Controllers\Medecin\ExternalDoctorController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuperAdmin\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes - HospitSIS
|--------------------------------------------------------------------------
*/

// ============ GESTION DES NOTIFICATIONS (MULTI-GUARD) ============
Route::middleware(['auth:web,superadmin,medecin_externe'])->group(function () {
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

// ============ PAGE D'ACCUEIL PUBLIQUE ============
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isTechnical()) {
            if ($user->role === 'doctor_radio' || $user->role === 'radio_technician') {
                return redirect()->route('lab.radiologist.dashboard');
            }
            return redirect()->route('lab.biologist.dashboard');
        }

        return match($user->role) {
            'doctor', 'medecin', 'internal_doctor' => redirect()->route('medecin.dashboard'),
            'nurse' => redirect()->route('nurse.dashboard'),
            'cashier' => redirect()->route('cashier.dashboard'),
            'doctor_lab' => redirect()->route('lab.biologist.dashboard'),
            'doctor_radio' => redirect()->route('lab.radiologist.dashboard'),
            'external_doctor' => redirect()->route('external.doctor.external.dashboard'),
            default => redirect()->route('dashboard')
        };
    }
    return view('welcome');
})->name('home');

// Route de diagnostic pour les erreurs 404 en sous-dossier
Route::get('/debug-url', function () {
    return response()->json([
        'uri' => request()->getRequestUri(),
        'path' => request()->path(),
        'base_url' => request()->getBaseUrl(),
        'app_url' => config('app.url'),
    ]);
});


// Route de diagnostic pour les erreurs 404 en sous-dossier

// Route de vérification rapide
// Route::get('/debug-check', function () { ... }); // Removed


// Sélection de portail pour l'inscription
Route::get('/select-portal', function () {
    return view('auth.select-portal');
})->name('select-portal');

// ============ ROUTES D'AUTHENTIFICATION (STAFF) ============
Route::middleware('guest')->group(function () {
    // Sélection d'hôpital
    Route::get('/select-hospital', [HospitalController::class, 'selectHospital'])->name('hospital.select');
    Route::post('/select-hospital', [HospitalController::class, 'processHospitalSelection'])->name('hospital.select.process');

    // Connexion par hôpital
    Route::get('/login/{hospital_slug}', [HospitalController::class, 'showLogin'])->name('hospital.login');

    // CORRECTION : Ajout de $hospital_slug pour que le POST fonctionne
    Route::post('/login/{hospital_slug}', function (\Illuminate\Http\Request $request, $hospital_slug) {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!auth()->user()->is_active) {
                auth()->logout();
                return back()->withErrors(['email' => 'Votre compte a été désactivé.']);
            }


            $user = auth()->user();
            if ($user->isTechnical()) {
                if ($user->role === 'doctor_radio' || $user->role === 'radio_technician') {
                    return redirect()->route('lab.radiologist.dashboard');
                }
                return redirect()->route('lab.biologist.dashboard');
            }

            return match($user->role) {
                'doctor', 'medecin', 'internal_doctor' => redirect()->route('medecin.dashboard'),
                'nurse' => redirect()->route('nurse.dashboard'),
                'doctor_lab' => redirect()->route('lab.biologist.dashboard'),
                'doctor_radio' => redirect()->route('lab.radiologist.dashboard'),
                'admin' => redirect()->route('superadmin.dashboard'),
                'cashier' => redirect()->route('cashier.dashboard'),
                'lab_technician' => redirect()->route('lab.dashboard'),
                'radio_technician' => redirect()->route('lab.radio_technician.dashboard'),
                'administrative' => redirect()->route('admin.dashboard'),
                'receptionist' => redirect()->route('reception.dashboard'),
                'external_doctor' => redirect()->route('external.doctor.external.dashboard'),
                default => redirect()->intended(route('dashboard'))
            };
        }
        return back()->withErrors(['email' => 'Les identifiants fournis sont incorrects.']);
    })->name('hospital.login.process');

    // Connexion générale (pour tous les utilisateurs)
    Route::get('/login', [HospitalController::class, 'showGeneralLogin'])->name('login');
    Route::post('/login', [HospitalController::class, 'processGeneralLogin'])->name('login.process');

    // Inscription par hôpital (C'est ici que ton bouton "Créer mon compte" se connecte)
    Route::get('/register/{hospital_slug}', [HospitalController::class, 'showRegistration'])->name('register');
    Route::post('/register/{hospital_slug}', [UserController::class, 'register'])->name('register.submit');
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout')->middleware('auth');

// ========== PORTAIL PATIENT ==========
Route::prefix('portal')->name('patient.')->group(function () {
    Route::group([], function () {
        Route::get('login', [PatientAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [PatientAuthController::class, 'login'])->name('login.submit');
        Route::get('register', [PatientAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [PatientAuthController::class, 'register'])->name('register.submit');
    });
    
    Route::middleware('auth:patients')->group(function () {
        Route::get('/dashboard', [PatientPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [PatientPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [PatientPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/appointments', [PatientPortalController::class, 'appointments'])->name('appointments');
        Route::delete('/appointments/{appointment}', [PatientPortalController::class, 'cancelAppointment'])->name('cancel-appointment');
        Route::get('/book-appointment', [PatientPortalController::class, 'showConsultationTypeSelector'])->name('book-appointment');
        Route::get('/book-appointment/hospital', [PatientPortalController::class, 'bookAppointmentHospital'])->name('book-appointment.hospital');
        Route::get('/book-appointment/home', [PatientPortalController::class, 'bookAppointmentHome'])->name('book-appointment.home');
        Route::post('/book-appointment', [PatientPortalController::class, 'storeAppointment'])->name('book-appointment.store');
        
        // Route AJAX pour récupérer les services d'un hôpital
        Route::get('/hospitals/{hospital}/services', [PatientPortalController::class, 'getHospitalServices'])->name('hospitals.services');
        
        // Confirmation de rendez-vous
        Route::get('/appointments/{appointment}/confirmation', [PatientPortalController::class, 'showConfirmation'])->name('appointments.confirmation');
        Route::get('/appointments/{appointment}/bill', [PatientPortalController::class, 'downloadAppointmentBill'])->name('appointments.bill');

        // Nouvelles routes AJAX pour consultation à domicile et hôpital
        Route::get('/ajax/doctors-by-specialty/{specialty}', [PatientPortalController::class, 'getExternalDoctorsBySpecialty'])->name('ajax.doctors-by-specialty');
        Route::get('/ajax/internal-doctors-by-specialty/{specialty}', [PatientPortalController::class, 'getInternalDoctorsBySpecialty'])->name('ajax.internal-doctors-by-specialty');
        Route::post('/ajax/calculate-home-fees', [PatientPortalController::class, 'calculateHomeVisitFees'])->name('ajax.calculate-home-fees');
        
        Route::get('/documents', [PatientPortalController::class, 'documents'])->name('documents');
    // Medical History
    Route::get('medical-history', [PatientPortalController::class, 'medicalHistory'])->name('medical-history');
    Route::get('medical-history/{id}', [PatientPortalController::class, 'showMedicalRecord'])->name('medical-history.show');
    Route::get('medical-history/admission/{id}', [PatientPortalController::class, 'showAdmission'])->name('medical-history.admission.show');
        Route::get('/prescriptions', [PatientPortalController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{id}/download', [PatientPortalController::class, 'downloadPrescription'])->name('prescriptions.download');
        Route::get('/invoices', [PatientPortalController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{id}/download', [PatientPortalController::class, 'downloadInvoice'])->name('invoices.pdf');
        Route::get('/health-metrics', [PatientPortalController::class, 'healthMetrics'])->name('health-metrics');
        
        // Routes de validation et de notation
        Route::post('/appointments/{appointment}/confirm-start', [PatientPortalController::class, 'confirmStart'])->name('appointments.confirm-start');
        Route::post('/appointments/{appointment}/confirm-end', [PatientPortalController::class, 'confirmEnd'])->name('appointments.confirm-end');
        Route::post('/appointments/{appointment}/rate', [PatientPortalController::class, 'rateDoctor'])->name('appointments.rate');
        Route::post('/appointments/{appointment}/confirm-payment', [PatientPortalController::class, 'confirmPayment'])->name('appointments.confirm-payment');
        
        // Suivi de rendez-vous
        Route::get('/appointments/{appointment}/track', [PatientPortalController::class, 'trackAppointment'])->name('appointments.track');
        Route::get('/emergency-contacts', [PatientPortalController::class, 'emergencyContacts'])->name('emergency-contacts');
        Route::get('/messaging', [PatientPortalController::class, 'messaging'])->name('messaging');
        
        Route::post('/logout', function (\Illuminate\Http\Request $request) {
            Auth::guard('patients')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('patient.login');
        })->name('logout');
    });
});

// ========== PORTAIL MÉDECIN EXTERNE ==========
Route::prefix('medecin/externe')->name('external.')->group(function () {
    // Inscription (Accessible à tous, même connectés)
    Route::get('/register', [ExternalDoctorController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [ExternalDoctorController::class, 'register'])->name('register.submit');
    
    // OTP Routes
    Route::get('/otp', [ExternalDoctorController::class, 'showOtpForm'])->name('register.otp');
    Route::post('/otp', [ExternalDoctorController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/otp/resend', [ExternalDoctorController::class, 'resendOtp'])->name('register.otp.resend');

    Route::middleware('guest:medecin_externe')->group(function () {
        Route::get('/login', [ExternalDoctorController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ExternalDoctorController::class, 'login'])->name('login.submit');
    });
    
    Route::middleware(['auth:medecin_externe'])->group(function () {
        // Dashboard
        Route::get('/tableau-de-bord', [ExternalDoctorController::class, 'dashboard'])->name('doctor.external.dashboard');
        // Action de déconnexion spécifique
        Route::post('/logout', function (\Illuminate\Http\Request $request) {
            Auth::guard('medecin_externe')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        })->name('logout');
        
        // Toggle disponibilité
        Route::post('/toggle-availability', [ExternalDoctorController::class, 'toggleAvailability'])->name('toggle-availability');
        
        // Patients
        Route::get('/patients', [ExternalDoctorController::class, 'patients'])->name('patients');
        
        // Dossiers partagés
        Route::get('/dossiers', [ExternalDoctorController::class, 'sharedRecords'])->name('shared-records');
        
        // Prescriptions
        Route::get('/prescriptions', [ExternalDoctorController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/creer', [ExternalDoctorController::class, 'createPrescription'])->name('prescriptions.create');
        Route::post('/prescriptions', [ExternalDoctorController::class, 'storePrescription'])->name('prescriptions.store');
        
        // Documents Médicaux (Rapports, Certificats, etc.)
        Route::get('/documents/creer', [ExternalDoctorController::class, 'createDocument'])->name('documents.create');
        Route::post('/documents', [ExternalDoctorController::class, 'storeDocument'])->name('documents.store');
        Route::get('/documents/{document}/download', [ExternalDoctorController::class, 'downloadDocument'])->name('documents.download');
        
        // Rendez-vous
        Route::get('/rendez-vous', [ExternalDoctorController::class, 'appointments'])->name('appointments');
        Route::get('/rendez-vous/{appointment}/invoice', [ExternalDoctorController::class, 'generateInvoicePdf'])->name('appointments.invoice');
        Route::get('/prescriptions/{prescription}/pdf', [ExternalDoctorController::class, 'generatePrescriptionPdf'])->name('prescriptions.pdf');
        
        // Prestations
        Route::get('/prestations', [ExternalDoctorController::class, 'prestations'])->name('prestations');
        Route::post('/prestations', [ExternalDoctorController::class, 'storePrestation'])->name('prestations.store');
        Route::put('/prestations/{id}', [ExternalDoctorController::class, 'updatePrestation'])->name('prestations.update');
        Route::post('/prestations/{id}/toggle', [ExternalDoctorController::class, 'togglePrestation'])->name('prestations.toggle');
        Route::delete('/prestations/{id}', [ExternalDoctorController::class, 'destroyPrestation'])->name('prestations.destroy');
        
        // Profil
        Route::get('/profil', [ExternalDoctorController::class, 'profile'])->name('profile');
        Route::post('/profil', [ExternalDoctorController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profil/payment-config', [ExternalDoctorController::class, 'updatePaymentConfig'])->name('payment-config.update');
        
        // Paramètres
        Route::get('/parametres', [ExternalDoctorController::class, 'settings'])->name('settings');
        Route::put('/parametres/password', [ExternalDoctorController::class, 'updatePassword'])->name('settings.password');
        
        // Rechargement
        Route::get('/recharger', [ExternalDoctorController::class, 'recharge'])->name('recharge');
        Route::post('/recharger', [ExternalDoctorController::class, 'initiateRecharge'])->name('recharge.initiate');
        Route::get('/recharger/callback', [ExternalDoctorController::class, 'handleRechargeCallback'])->name('recharge.callback');
        
        // Suivi et Géolocalisation (Côté Médecin)
        Route::post('/ajax/update-location', [ExternalDoctorController::class, 'updateLocation'])->name('ajax.update-location');
        Route::post('/ajax/appointments/{appointment}/status', [ExternalDoctorController::class, 'updateAppointmentStatus'])->name('ajax.update-status');
    });
});

// Route AJAX pour le polling du patient (Publique ou Patient Auth)
Route::get('/ajax/appointments/{appointment}/tracking-data', [PatientPortalController::class, 'getTrackingData'])->middleware('auth:patients')->name('ajax.appointment.tracking-data');

// ========== ROUTES STAFF (Admin, Réception, etc.) ==========
Route::middleware(['auth', 'active_user'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/invoices/stats', [DashboardController::class, 'getInvoiceStatsApi'])->name('dashboard.invoices.stats');
    Route::get('/dashboard/invoices/data', [DashboardController::class, 'getInvoices'])->name('dashboard.invoices.data');

    // Support & Aide
    Route::get('/help', function () {
        return view('pages.help');
    })->name('help');

    Route::get('/contact-support', function () {
        return view('pages.contact');
    })->name('contact');

    // OBSERVATIONS
    Route::post('/observations/store', [ObservationController::class, 'store'])->name('observations.store');
    Route::put('/observations/{id}', [ObservationController::class, 'update'])->name('observations.update');
    Route::delete('/observations/{id}', [ObservationController::class, 'destroy'])->name('observations.destroy');
    Route::post('/observations/{id}/send', [ObservationController::class, 'sendToPatient'])->name('observations.send');
    Route::post('/prescriptions/{id}/share', [PrescriptionController::class, 'share'])->name('prescriptions.share');

    // PATIENTS
    Route::patch('/patients/{patient}/archive', [PatientController::class, 'archive'])->name('patients.archive');
    
    Route::middleware('role:admin,administrative,doctor,medecin,internal_doctor,doctor_lab,doctor_radio')->group(function () {
        Route::resource('patients', PatientController::class);
    });

    Route::get('/patients/{patient}/medical-file', [PatientController::class, 'medicalFile'])
        ->name('patients.medical-file')
        ->middleware('role:admin,administrative,nurse,doctor,internal_doctor,doctor_lab,doctor_radio');

    Route::get('/patients/search/quick', [PatientController::class, 'quickSearch'])->name('patients.quick-search');

    // RENDEZ-VOUS
    Route::resource('appointments', AppointmentController::class)->except(['create', 'store']);
    Route::middleware('role:admin,administrative,nurse,secretary,doctor_lab,doctor_radio')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    });
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');

    // ADMISSIONS & LITS
    Route::resource('admissions', AdmissionController::class);
    Route::post('/admissions/{admission}/discharge', [AdmissionController::class, 'discharge'])->name('admissions.discharge');
    Route::post('/admissions/{admission}/transfer', [AdmissionController::class, 'transfer'])->name('admissions.transfer');
    Route::get('/bed-management', [RoomController::class, 'bedManagement'])->name('rooms.bed-management');
    Route::post('/rooms/{room}/assign', [RoomController::class, 'assignBed'])->name('rooms.assign');
    Route::post('/rooms/{room}/release', [RoomController::class, 'releaseBed'])->name('rooms.release');
    Route::resource('rooms', RoomController::class)->except(['index']);
     

    // 1. ACCÈS COMMUN (LISTE UNIQUEMENT)
    Route::middleware(['auth', 'role:doctor,nurse,admin,internal_doctor'])->group(function () {
        Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical_records.index');
        Route::get('/patients/{patient}/records', [MedicalRecordController::class, 'index'])->name('patients.records.index');
        // Correction de la virgule ici pour la cohérence
        Route::get('/archives', [MedicalRecordController::class, 'archivesIndex'])->name('medical_records.archives');
        Route::get('/patients/{patient}/archive', [PatientController::class, 'showArchives'])->name('patients.archives.show');
    });

    // 2. ACCÈS MÉDECIN / ADMIN / INFIRMIER UNIQUEMENT (CONSULTATION & ÉDITION)
    Route::middleware(['auth', 'role:doctor,admin,internal_doctor,nurse'])->group(function () {
        Route::get('/medical-records/{record}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
        Route::post('/patients/{patient}/records', [MedicalRecordController::class, 'store'])->name('medical-records.store');
        Route::put('/medical-records/{record}', [MedicalRecordController::class, 'update'])->name('medical_records.update');
        Route::delete('/medical-records/{record}', [MedicalRecordController::class, 'destroy'])->name('medical-records.destroy');
        Route::patch('/medical-records/{id}/archive', [MedicalRecordController::class, 'archive'])->name('medical_records.archive');
        Route::post('/medical-records/{id}/admit', [MedicalRecordController::class, 'admit'])->name('medical_records.admit');
        Route::post('/medical-records/{id}/discharge', [MedicalRecordController::class, 'discharge'])->name('medical_records.discharge');
        Route::post('/medical-records/{id}/share', [MedicalRecordController::class, 'share'])->name('medical_records.share');
        Route::get('/medical-records/{id}/pdf', [MedicalRecordController::class, 'downloadPdf'])->name('medical-records.pdf');
    });

    // PRESCRIPTIONS & FACTURATION
    Route::middleware('role:doctor,medecin,internal_doctor,doctor_lab,doctor_radio')->group(function () {
        Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::resource('prescriptions', PrescriptionController::class);
        Route::post('/prescriptions/{prescription}/sign', [PrescriptionController::class, 'sign'])->name('prescriptions.sign');
        Route::patch('/prescriptions/{prescription}/sign', [PrescriptionController::class, 'sign'])->name('prescriptions.patch_sign');
        Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'downloadPdf'])->name('prescriptions.pdf');
        Route::post('/prescriptions/{id}/share', [PrescriptionController::class, 'share'])->name('prescriptions.share');
    });

    Route::middleware('role:administrative,admin')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/enable-mfa', [UserController::class, 'enableMfa'])->name('users.enable-mfa');
        Route::post('/users/disable-mfa', [UserController::class, 'disableMfa'])->name('users.disable-mfa');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/financial', [ReportController::class, 'financialReport'])->name('reports.financial');
    });

    // Logs et Prestations
    Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs.index');
    Route::resource('services', ServiceController::class)->only(['index', 'create', 'store']);
    Route::resource('prestations', PrestationController::class);

    // Lab Requests
    Route::middleware('role:doctor,internal_doctor,doctor_lab,doctor_radio')->group(function () {
        Route::post('/lab/request', [LabRequestController::class, 'store'])->name('lab.request.store');
    });

    // CASHIER ROUTES (CAISSIER)
    Route::middleware('role:cashier')->group(function () {
        Route::get('/cashier/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard');
        Route::get('/cashier/appointments', [CashierController::class, 'appointments'])->name('cashier.appointments.index');
        Route::get('/cashier/walk-in', [CashierController::class, 'walkInConsultations'])->name('cashier.walk-in.index');
        Route::post('/cashier/walk-in', [CashierController::class, 'createWalkInConsultation'])->name('cashier.walk-in.store');
        Route::get('/cashier/walk-in/{consultation}/details', [CashierController::class, 'getWalkInDetails'])->name('cashier.walk-in.details');
        Route::get('/cashier/lab-requests/{labRequest}/details', [CashierController::class, 'getLabRequestDetails'])->name('cashier.lab.details'); // New Route
        Route::post('/cashier/walk-in/{consultation}/validate-payment', [CashierController::class, 'validateWalkInPayment'])->name('cashier.walk-in.validate-payment');
        Route::get('/cashier/payments', [CashierController::class, 'payments'])->name('cashier.payments.index');
        Route::get('/cashier/insurance-cards', [CashierController::class, 'insuranceCards'])->name('cashier.insurance-cards.index');
        Route::post('/cashier/lab-requests/{labRequest}/pay', [CashierController::class, 'payLabRequest'])->name('cashier.lab.pay'); // New Route
        Route::get('/cashier/invoices', [CashierController::class, 'invoices'])->name('cashier.invoices.index');
        Route::get('/cashier/patients', [CashierController::class, 'patients'])->name('cashier.patients.index');
        Route::post('/cashier/appointments/{appointment}/validate-payment', [CashierController::class, 'validatePayment'])->name('cashier.validate-payment');
        Route::post('/cashier/appointments/{appointment}/reject-payment', [CashierController::class, 'rejectPayment'])->name('cashier.reject-payment');
        Route::get('/cashier/invoices/{invoice}', [CashierController::class, 'showInvoice'])->name('cashier.invoices.show');
        Route::get('/cashier/invoices/{invoice}/print', [CashierController::class, 'printInvoice'])->name('cashier.invoices.print');
        Route::get('/cashier/invoices/{invoice}/pdf', [CashierController::class, 'downloadInvoice'])->name('cashier.invoices.pdf');
        Route::get('/cashier/settings', [CashierController::class, 'settings'])->name('cashier.settings.index');
        Route::put('/cashier/settings/update', [CashierController::class, 'updateSettings'])->name('cashier.settings.update');

        // SIMULATION PAIEMENT (DEV)
        Route::get('/payment/simulation/{id}', [CashierController::class, 'simulatePayment'])->name('simulation.payment');
        Route::post('/payment/simulation/{id}/validate', [CashierController::class, 'processSimulation'])->name('simulation.payment.validate');
    });

    // Profil & Paramètres
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Doctor Availability Routes
    Route::post('/profile/availability/initialize', [ProfileController::class, 'initializeAvailability'])->name('profile.availability.initialize');
    Route::post('/profile/availability/update', [ProfileController::class, 'updateAvailability'])->name('profile.availability.update');
    Route::post('/profile/availability/{id}/toggle', [ProfileController::class, 'toggleAvailabilitySlot'])->name('profile.availability.toggle');
    
    // Hospital Payment Settings (Admin only)
    Route::post('/admin/payment/settings', [ProfileController::class, 'updatePaymentSettings'])->name('admin.payment.settings.update');
    
    // Profile Photo Upload
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    
    // Notification Settings
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotificationSettings'])->name('profile.notifications.update');

    Route::get('/settings', fn() => view('settings.index'))->name('settings');



    Route::middleware(['auth', 'active_user', 'role:admin'])->group(function () {
        Route::get('/subscription/manage', [DashboardController::class, 'manageSubscription'])->name('admin.subscription.manage');
        Route::post('/subscription/change', [DashboardController::class, 'changeSubscription'])->name('admin.subscription.change');
    });

    // Publicly accessible checkout and pay routes for hospitals and specialists (authenticated)
    Route::get('/subscription/{plan}/checkout', [SubscriptionController::class, 'checkout'])
        ->name('subscription.checkout')
        ->middleware('auth');

    Route::post('/subscription/{plan}/pay', [SubscriptionController::class, 'initiatePayment'])
        ->name('subscription.pay')
        ->middleware('auth');

    // Webhook (public) for CinetPay - used by ngrok during testing
    Route::post('/payment/cinetpay/webhook', [SubscriptionController::class, 'handleCinetpayWebhook'])
        ->name('cinetpay.webhook');
    
    // Webhook for Mobile Money payments (walk-in consultations)
    Route::post('/cashier/mobile-money/webhook', [CashierController::class, 'handleMobileMoneyWebhook'])
        ->name('cashier.mobile-money.webhook');

    // ADMIN FINANCE ROUTES
    Route::middleware('role:admin')->group(function() {
        Route::get('/admin/finance', [App\Http\Controllers\Admin\AdminFinanceController::class, 'index'])->name('admin.finance.index');
        Route::get('/admin/finance/daily', [App\Http\Controllers\Admin\AdminFinanceController::class, 'dailyDetails'])->name('admin.finance.daily');
        Route::get('/admin/finance/treasury', [App\Http\Controllers\Admin\AdminFinanceController::class, 'treasuryDetails'])->name('admin.finance.treasury');
        Route::post('/admin/finance/confirm/{id}', [App\Http\Controllers\Admin\AdminFinanceController::class, 'confirmTransfer'])->name('admin.finance.confirm');
        Route::get('/admin/finance/export', [App\Http\Controllers\Admin\AdminFinanceController::class, 'exportInvoices'])->name('admin.finance.export');
        Route::get('/admin/finance/pending', [App\Http\Controllers\Admin\AdminFinanceController::class, 'pendingInvoices'])->name('admin.finance.pending');
        Route::post('/admin/finance/settle-insurance/{invoice}', [App\Http\Controllers\Admin\AdminFinanceController::class, 'settleInsuranceInvoice'])->name('admin.finance.settle');
        Route::get('/admin/finance/bordereau', [App\Http\Controllers\Admin\AdminFinanceController::class, 'exportInsuranceBordereau'])->name('admin.finance.bordereau');
        Route::get('/admin/finance/audit', [App\Http\Controllers\Admin\AdminFinanceController::class, 'auditLogs'])->name('admin.finance.audit');

        // INSURANCE MANAGEMENT ROUTES
        Route::get('/admin/insurance', [App\Http\Controllers\Admin\InsuranceController::class, 'index'])->name('admin.insurance.index');
        Route::post('/admin/insurance/test', [App\Http\Controllers\Admin\InsuranceController::class, 'testVerification'])->name('admin.insurance.test');
        Route::post('/admin/insurance/connector', [App\Http\Controllers\Admin\InsuranceController::class, 'storeConnector'])->name('admin.insurance.store-connector');
    });

    // CASHIER CLOSING ROUTES
    Route::middleware('role:cashier')->group(function() {
        Route::get('/cashier/closing', [App\Http\Controllers\Cashier\CashierClosingController::class, 'index'])->name('cashier.closing.index');
        Route::get('/cashier/closing/totals', [App\Http\Controllers\Cashier\CashierClosingController::class, 'getTotals'])->name('cashier.closing.totals');
        Route::post('/cashier/transfer', [App\Http\Controllers\Cashier\CashierClosingController::class, 'store'])->name('cashier.transfer.store');
    });

    // Webhook for Recharge payments (CinetPay)
    Route::post('/webhook/recharge', [\App\Http\Controllers\Webhook\RechargeWebhookController::class, 'handle'])
        ->name('webhook.recharge');

    // PHARMACY ROUTES
    Route::middleware('role:pharmacist,admin')->group(function() {
        Route::get('/pharmacy/dashboard', [PharmacyController::class, 'dashboard'])->name('pharmacy.dashboard');
        Route::get('/pharmacy/catalog', [PharmacyController::class, 'medsCatalog'])->name('pharmacy.catalog');
        Route::get('/pharmacy/history', [PharmacyController::class, 'movementHistory'])->name('pharmacy.history');
        Route::post('/pharmacy/catalog', [PharmacyController::class, 'addMedication'])->name('pharmacy.catalog.store');
        Route::put('/pharmacy/catalog/{medication}', [PharmacyController::class, 'updateMedication'])->name('pharmacy.catalog.update');
        Route::post('/pharmacy/stock/update', [PharmacyController::class, 'updateStock'])->name('pharmacy.stock.update');
    });

    // SECRETARY ROUTES
    Route::middleware('role:secretary,admin')->group(function() {
        Route::get('/secretary', [SecretaryController::class, 'index'])->name('secretary.index');
        Route::get('/secretary/dashboard', [SecretaryController::class, 'dashboard'])->name('secretary.dashboard');
        Route::get('/secretary/history', [SecretaryController::class, 'assignmentHistory'])->name('secretary.history');
        Route::post('/secretary/appointments/{appointment}/assign', [SecretaryController::class, 'assignDoctor'])->name('secretary.appointments.assign');
        Route::post('/secretary/appointments/{appointment}/archive', [SecretaryController::class, 'archiveAssignment'])->name('secretary.appointments.archive');
        Route::delete('/secretary/appointments/{appointment}/destroy', [SecretaryController::class, 'destroyAssignment'])->name('secretary.appointments.destroy');
        Route::get('/secretary/agendas', [SecretaryController::class, 'doctorAgendas'])->name('secretary.agendas');

        // Nouvelles routes dédiées Patients & RDV pour la secrétaire
        Route::get('/secretary/patients', [SecretaryController::class, 'patientsIndex'])->name('secretary.patients.index');
        Route::get('/secretary/patients/create', [SecretaryController::class, 'patientsCreate'])->name('secretary.patients.create');
        Route::post('/secretary/patients', [SecretaryController::class, 'patientsStore'])->name('secretary.patients.store');

        Route::get('/secretary/appointments/register', [SecretaryController::class, 'appointmentsIndex'])->name('secretary.appointments.index');
        Route::get('/secretary/appointments/book', [SecretaryController::class, 'appointmentsCreate'])->name('secretary.appointments.create');
        Route::post('/secretary/appointments/book', [SecretaryController::class, 'appointmentsStore'])->name('secretary.appointments.store');
    });

}); // FIN DU MIDDLEWARE AUTH PRINCIPAL

// Portails Médecins
Route::prefix('medecin')->name('medecin.')->middleware(['auth:web,medecin_externe'])->group(function () {
    Route::get('/dashboard', [MedecinDashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('medecin/interne')->name('doctor.')->middleware(['auth', 'active_user', 'role:internal_doctor'])->group(function () {
    Route::get('/tableau-de-bord', [MedecinDashboardController::class, 'index'])->name('internal.dashboard');
});


// ============ SERVICES PUBLICS ============
Route::prefix('services')->name('public.services.')->group(function () {
    Route::get('/maternite', [App\Http\Controllers\PublicServiceController::class, 'maternity'])->name('maternity');
    Route::get('/cardiologie', [App\Http\Controllers\PublicServiceController::class, 'cardiology'])->name('cardiology');
    Route::get('/pediatrie', [App\Http\Controllers\PublicServiceController::class, 'pediatrics'])->name('pediatrics');
    Route::get('/nutrition', [App\Http\Controllers\PublicServiceController::class, 'nutrition'])->name('nutrition');
    Route::get('/psychologie', [App\Http\Controllers\PublicServiceController::class, 'psychology'])->name('psychology');
    Route::get('/urgences', [App\Http\Controllers\PublicServiceController::class, 'emergencies'])->name('emergencies');
});

// ROUTES PUBLIQUES
Route::get('/health', fn() => response()->json(['status' => 'ok']))->name('health.check');

// SIMULATEUR D'ASSURANCE (MOCK API)
Route::get('/api/test-insurance/{matricule}', function($matricule) {
    $service = new \App\Services\Insurance\InsuranceService();
    return response()->json($service->verify($matricule));
})->name('api.insurance.verify');

require __DIR__.'/nurse.php';
require __DIR__.'/lab.php';
require __DIR__.'/superadmin.php';

Route::fallback(fn() => response()->view('errors.404', [], 404));
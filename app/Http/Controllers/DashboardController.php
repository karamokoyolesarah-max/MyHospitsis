<?php

namespace App\Http\Controllers;

use App\Models\{Patient, Appointment, Admission, Room, ClinicalAlert, AuditLog, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();

// --- 1. LOGIQUE POUR L'ADMINISTRATEUR ---
if ($user->role === 'admin' || $user->role === 'super_admin') {
    $hospitalId = $user->hospital_id; // On définit la variable pour l'utiliser plus bas

    // Calculate bed statistics dynamically
    $bedStats = \App\Models\Room::where('hospital_id', $hospitalId)
        ->where('is_active', true)
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

    $totalBeds = array_sum($bedStats);
    $occupiedBeds = isset($bedStats['occupied']) ? $bedStats['occupied'] : 0;
    $availableBeds = isset($bedStats['available']) ? $bedStats['available'] : 0;
    $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

    $stats = [
        'totalDoctors'   => \App\Models\User::where('hospital_id', $hospitalId)
                                    ->whereIn('role', ['doctor', 'internal_doctor', 'external_doctor'])
                                    ->where('is_active', true)
                                    ->count(),

        'totalPatients'    => \App\Models\Patient::where('hospital_id', $hospitalId)
                                    ->where('is_active', true)
                                    ->count(),

        'totalServices'           => \App\Models\Service::where('hospital_id', $hospitalId)
                                    ->where('is_active', true)
                                    ->count(),

        'occupancyRate'     => $occupancyRate,

        'today_appointments' => \App\Models\Appointment::where('hospital_id', $hospitalId)
                                    ->whereDate('appointment_datetime', today())
                                    ->count(),
        'pending_appointments' => \App\Models\Appointment::where('hospital_id', $hospitalId)
                                    ->where('status', 'pending')
                                    ->count(),
        'available_beds'       => $availableBeds,
        'total_beds'           => $totalBeds,
        'active_alerts'        => \App\Models\ClinicalAlert::where('hospital_id', $hospitalId)
                                    ->where('is_acknowledged', false)
                                    ->count(),
        'critical_alerts'      => \App\Models\ClinicalAlert::where('hospital_id', $hospitalId)
                                    ->where('is_acknowledged', false)
                                    ->where('severity', 'critical')
                                    ->count(),
    ]; // Le tableau doit se fermer ICI, après toutes les clés

    $todayAppointments = $this->getTodayAppointments($user);
    $inactiveUsers = User::where('hospital_id', $hospitalId)->where('is_active', false)->with('service')->get();
    $recentActivities = $this->getRecentActivities($hospitalId);

    // Add invoices stats for admin dashboard
    $invoiceStats = $this->getInvoiceStatsForDashboard($hospitalId);
    $stats = array_merge($stats, $invoiceStats);

    return view('admin.dashboard', array_merge($stats, compact('todayAppointments', 'inactiveUsers', 'recentActivities')));
}
    // --- 2. LOGIQUE POUR L'INFIRMIER ---
    if ($user->role === 'nurse' || $user->role === 'infirmier') {
        $stats = $this->getStats($user);
        $myPatients = $this->getActiveAdmissions($user);
        
        $appointments = \App\Models\Appointment::with(['patient', 'doctor'])
            ->whereHas('patient')
            ->whereHas('doctor')
            ->where('hospital_id', $user->hospital_id)
            ->where('service_id', $user->service_id)
            ->whereDate('appointment_datetime', '<=', today())
            ->orderBy('appointment_datetime', 'desc')
            ->get();

        $sentFiles = \App\Models\PatientVital::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
        
        return view('nurse.dashboard', compact('stats', 'myPatients', 'appointments', 'sentFiles'));
    }

    // --- 3. LOGIQUE POUR LE CAISSIER ---
    if ($user->role === 'cashier') {
        // Redirect cashiers to their specific dashboard
        return redirect()->route('cashier.dashboard');
    }

    // --- 4. LOGIQUE POUR LE MÉDECIN ---
    $stats = $this->getStats($user);
    $todayAppointments = $this->getTodayAppointments($user);

    $criticalObservations = \App\Models\ClinicalObservation::where('hospital_id', $user->hospital_id)
        ->where('is_critical', true)
        ->with('patient')
        ->orderBy('observation_datetime', 'desc')
        ->take(10)
        ->get();

    $myPatients = $this->getActiveAdmissions($user);
    $criticalPatients = $criticalObservations->count();

    // On compte les dossiers vitaux reçus
    $pendingExams = \App\Models\PatientVital::where('hospital_id', $user->hospital_id)->count();

    // Récupérer les jours où ce médecin est disponible
    $availableDays = \App\Models\DoctorAvailability::where('doctor_id', $user->id)
        ->where('is_active', true)
        ->pluck('day_of_week')
        ->toArray();

    // Récupérer les rendez-vous en attente d'attribution (même service)
    // FILTRE : Uniquement si la date du RDV correspond à un jour ouvré du médecin
    $pendingServiceAppointments = \App\Models\Appointment::with(['patient', 'service'])
        ->where('hospital_id', $user->hospital_id)
        ->where('service_id', $user->service_id)
        ->where('status', 'pending')
        ->whereNull('doctor_id')
        ->get()
        ->filter(function($appointment) use ($availableDays) {
            // Conversion du jour de la semaine en format anglais (ex: 'monday')
            $dayName = strtolower(Carbon::parse($appointment->appointment_datetime)->format('l'));
            return in_array($dayName, $availableDays);
        });

    return view('medecin.dashboard', array_merge(compact(
        'stats',
        'todayAppointments',
        'criticalObservations',
        'myPatients',
        'criticalPatients',
        'pendingExams',
        'pendingServiceAppointments'
    ), ['hospitalizedPatients' => $myPatients]));
}

    private function getStats($user)
    {
        $stats = [];

        // Patients actifs
        $stats['active_patients'] = Patient::where('hospital_id', $user->hospital_id)
            ->where('is_active', true)
            ->count();

        // Rendez-vous aujourd'hui
        $stats['today_appointments'] = Appointment::where('hospital_id', $user->hospital_id)
            ->whereDate('appointment_datetime', today())
            ->when($user->isDoctor(), function($query) use ($user) {
                return $query->where('doctor_id', $user->id);
            })
            ->count();

        $stats['pending_appointments'] = Appointment::where('hospital_id', $user->hospital_id)
            ->whereDate('appointment_datetime', today())
            ->where('status', 'scheduled')
            ->when($user->isDoctor(), function($query) use ($user) {
                return $query->where('doctor_id', $user->id);
            })
            ->count();

        // Statistiques des lits
        $bedStats = Room::where('hospital_id', $user->hospital_id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $stats['available_beds'] = isset($bedStats['available']) ? $bedStats['available'] : 0;
        $stats['occupied_beds'] = isset($bedStats['occupied']) ? $bedStats['occupied'] : 0;
        $stats['cleaning_beds'] = isset($bedStats['cleaning']) ? $bedStats['cleaning'] : 0;
        $stats['total_beds'] = array_sum($bedStats);
        $stats['occupancy_rate'] = $stats['total_beds'] > 0
            ? round(($stats['occupied_beds'] / $stats['total_beds']) * 100, 1)
            : 0;

        // Alertes cliniques
        $stats['active_alerts'] = ClinicalAlert::where('hospital_id', $user->hospital_id)
            ->where('is_acknowledged', false)
            ->count();
        $stats['critical_alerts'] = ClinicalAlert::where('hospital_id', $user->hospital_id)
            ->where('is_acknowledged', false)
            ->where('severity', 'critical')
            ->count();

        return $stats;
    }

    private function getTodayAppointments($user)
    {
        return Appointment::with(['patient', 'doctor', 'service'])
            ->whereDate('appointment_datetime', today())
            ->when($user->isDoctor(), function($query) use ($user) {
                return $query->where('doctor_id', $user->id);
            })
            ->when($user->service_id, function($query) use ($user) {
                return $query->where('service_id', $user->service_id);
            })
            ->orderBy('appointment_datetime')
            ->get();
    }

    private function getActiveAdmissions($user)
    {
        return Admission::with(['patient', 'room', 'doctor'])
            ->whereHas('patient')
            ->where('status', 'active')
            ->when($user->isDoctor(), function($query) use ($user) {
                return $query->where('doctor_id', $user->id);
            })
            ->when($user->service_id && !$user->isAdmin(), function($query) use ($user) {
                return $query->whereHas('room', function($q) use ($user) {
                    $q->where('service_id', $user->service_id);
                });
            })
            ->latest('admission_date')
            ->limit(10)
            ->get();
    }

    private function getClinicalAlerts($user)
    {
        return ClinicalAlert::with(['patient', 'triggeredBy'])
            ->where('is_acknowledged', false)
            ->when(!$user->isAdmin(), function($query) use ($user) {
                // Limiter aux patients du service de l'utilisateur
                return $query->whereHas('patient.admissions', function($q) use ($user) {
                    $q->where('status', 'active')
                      ->whereHas('room', function($r) use ($user) {
                          $r->where('service_id', $user->service_id);
                      });
                });
            })
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function stats(Request $request)
    {
        // API endpoint pour les statistiques dynamiques
        $period = $request->input('period', '7days');
        
        return response()->json([
            'appointments' => $this->getAppointmentStats($period),
            'admissions' => $this->getAdmissionStats($period),
            'occupancy' => $this->getOccupancyTrend($period)
        ]);
    }

    private function getAppointmentStats($period)
    {
        $days = $period === '30days' ? 30 : 7;
        
        return Appointment::selectRaw('DATE(appointment_datetime) as date, COUNT(*) as count')
            ->where('appointment_datetime', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getAdmissionStats($period)
    {
        $days = $period === '30days' ? 30 : 7;
        
        return Admission::selectRaw('DATE(admission_date) as date, COUNT(*) as count')
            ->where('admission_date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getOccupancyTrend($period)
    {
        // Calcul du taux d'occupation sur la période
        $days = $period === '30days' ? 30 : 7;
        
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $totalBeds = Room::where('is_active', true)->count();
            $occupiedBeds = Admission::where('status', 'active')
                ->whereDate('admission_date', '<=', $date)
                ->where(function($q) use ($date) {
                    $q->whereNull('discharge_date')
                      ->orWhereDate('discharge_date', '>=', $date);
                })
                ->count();
            
            $data[] = [
                'date' => $date,
                'rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0
            ];
        }
        
        return $data;
    }

    public function auditLogs(Request $request)
    {
        // Page des logs d'audit (Admin uniquement)
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), function($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->filled('action'), function($query) use ($request) {
                return $query->where('action', $request->action);
            })
            ->when($request->filled('resource_type'), function($query) use ($request) {
                return $query->where('resource_type', $request->resource_type);
            })
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest('created_at')
            ->paginate(50);

        $users = User::select('id', 'name', 'email')->get();
        
        return view('audit-logs.index', compact('logs', 'users'));
    }

    public function auditLogDetail(AuditLog $log)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $log->load('user');
        
        return view('audit-logs.show', compact('log'));
    }

    private function getRecentActivities($hospitalId)
    {
        try {
            $activities = collect();

            // Recent admissions
            $recentAdmissions = Admission::with(['patient', 'room', 'doctor'])
                ->where('hospital_id', $hospitalId)
                ->whereHas('patient')
                ->latest('admission_date')
                ->take(3)
                ->get()
                ->map(function ($admission) {
                    return [
                        'type' => 'admission',
                        'user' => optional($admission->doctor)->name ?? 'Admin Système',
                        'message' => "Patient " . (optional($admission->patient)->name ?? 'Inconnu') . " admis en " . (optional($admission->room)->service ? optional($admission->room)->service->name : 'service') . " - Chambre " . (optional($admission->room)->room_number ?? 'N/A'),
                        'time' => $admission->admission_date,
                        'icon' => 'fas fa-user-plus',
                        'color' => 'success'
                    ];
                });

            // Recent appointments
            $recentAppointments = Appointment::with(['patient', 'doctor'])
                ->where('hospital_id', $hospitalId)
                ->whereHas('patient')
                ->whereHas('doctor')
                ->where('status', 'completed')
                ->latest('appointment_datetime')
                ->take(3)
                ->get()
                ->map(function ($appointment) {
                    return [
                        'type' => 'appointment',
                        'user' => optional($appointment->doctor)->name ?? 'Médecin',
                        'message' => "Rendez-vous terminé avec " . (optional($appointment->patient)->name ?? 'Patient'),
                        'time' => $appointment->appointment_datetime,
                        'icon' => 'fas fa-calendar-check',
                        'color' => 'primary'
                    ];
                });

            // Recent clinical observations
            $recentObservations = \App\Models\ClinicalObservation::with(['patient', 'user'])
                ->where('hospital_id', $hospitalId)
                ->whereHas('patient')
                ->whereHas('user')
                ->latest('created_at')
                ->take(3)
                ->get()
                ->map(function ($observation) {
                    return [
                        'type' => 'observation',
                        'user' => optional($observation->user)->name ?? 'Personnel médical',
                        'message' => "Observation clinique ajoutée pour " . (optional($observation->patient)->name ?? 'Patient') . " - " . ($observation->type ?? 'Type inconnu'),
                        'time' => $observation->created_at,
                        'icon' => 'fas fa-stethoscope',
                        'color' => 'info'
                    ];
                });

            // Recent medical records
            $recentRecords = \App\Models\MedicalRecord::with(['patient', 'recordedBy'])
                ->where('hospital_id', $hospitalId)
                ->whereHas('patient')
                ->whereHas('recordedBy')
                ->latest('created_at')
                ->take(3)
                ->get()
                ->map(function ($record) {
                    return [
                        'type' => 'record',
                        'user' => optional($record->recordedBy)->name ?? 'Médecin',
                        'message' => "Dossier médical mis à jour pour " . (optional($record->patient)->name ?? 'Patient'),
                        'time' => $record->created_at,
                        'icon' => 'fas fa-file-medical',
                        'color' => 'warning'
                    ];
                });

            // Combine all activities and sort by time
            $activities = $recentAdmissions
                ->concat($recentAppointments)
                ->concat($recentObservations)
                ->concat($recentRecords)
                ->sortByDesc('time')
                ->take(10)
                ->map(function ($activity) {
                    $activity['time_ago'] = Carbon::parse($activity['time'])->diffForHumans();
                    return $activity;
                });

            return $activities;
        } catch (\Exception $e) {
            // Log the error and return empty collection to prevent dashboard crash
            \Log::error('Error in getRecentActivities: ' . $e->getMessage());
            return collect();
        }
    }

    private function getInvoiceStatsForDashboard($hospitalId)
    {
        // Get invoice statistics for the hospital
        $invoices = \App\Models\Invoice::where('hospital_id', $hospitalId)->get();

        $totalRevenue = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum(function ($invoice) {
            return $invoice->payments->sum('amount');
        });
        $totalPending = $totalRevenue - $totalPaid;
        $paidInvoices = $invoices->filter(function ($invoice) {
            $paidAmount = $invoice->payments->sum('amount');
            return $paidAmount >= $invoice->total_amount;
        })->count();
        $pendingInvoices = $invoices->count() - $paidInvoices;

        return [
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'total_invoices' => $invoices->count(),
        ];
    }



    public function getInvoiceStatsApi()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $hospitalId = $user->hospital_id;

        // Get invoice statistics for the hospital
        $invoices = \App\Models\Invoice::where('hospital_id', $hospitalId)->get();

        $totalRevenue = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum(function ($invoice) {
            return $invoice->payments->sum('amount');
        });
        $totalPending = $totalRevenue - $totalPaid;
        $paidInvoices = $invoices->filter(function ($invoice) {
            $paidAmount = $invoice->payments->sum('amount');
            return $paidAmount >= $invoice->total_amount;
        })->count();
        $pendingInvoices = $invoices->count() - $paidInvoices;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'total_invoices' => $invoices->count(),
        ]);
    }

    public function getInvoices()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        // Get all invoices for this hospital
        $invoices = \App\Models\Invoice::with(['patient', 'payments'])
            ->where('hospital_id', $user->hospital_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                $remainingAmount = $invoice->total_amount - $totalPaid;

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'patient_name' => $invoice->patient ? $invoice->patient->name : 'Patient inconnu',
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $totalPaid,
                    'remaining_amount' => $remainingAmount,
                    'status' => $remainingAmount <= 0 ? 'PAYÉ' : ($totalPaid > 0 ? 'PARTIELLEMENT PAYÉ' : 'IMPAYÉ'),
                    'created_at' => $invoice->created_at->format('d/m/Y'),
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('d/m/Y') : null,
                ];
            });

        return response()->json(['invoices' => $invoices]);
    }

    public function settings()
    {
        $user = auth()->user();

        return view('settings', compact('user'));
    }

    public function manageSubscription()
    {
        $user = auth()->user();
        // Refresh the hospital relationship to get updated subscription data
        $user->load('hospital.subscriptionPlan');
        $hospital = $user->hospital;
        $currentPlan = $hospital->subscriptionPlan;
        $availablePlans = \App\Models\SubscriptionPlan::where('is_active', true)->get();

        return view('admin.subscription.manage', compact('user', 'hospital', 'currentPlan', 'availablePlans'));
    }

    public function changeSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id'
        ]);

        $user = auth()->user();
        $hospital = $user->hospital;
        $newPlan = \App\Models\SubscriptionPlan::find($request->plan_id);

        // Only process payment for paid plans
        if ($newPlan->price > 0) {
            // Log the subscription payment as SaaS revenue for Super Admin dashboard
            \App\Models\TransactionLog::create([
                'source_type' => 'hospital',
                'source_id' => $hospital->id,
                'amount' => $newPlan->price,
                'fee_applied' => 0, // No fee applied for subscription payments
                'net_income' => $newPlan->price,
                'description' => "Paiement abonnement - {$newPlan->name} - {$newPlan->price} ₣"
            ]);
        }

        $hospital->update([
            'subscription_plan_id' => $request->plan_id
        ]);

        // Log de l'action
        \App\Models\AuditLog::log('subscription_changed', 'Hospital', $hospital->id, [
            'description' => 'Changement de plan d\'abonnement',
            'old_plan' => $hospital->subscriptionPlan->name ?? 'Aucun',
            'new_plan' => $newPlan->name
        ]);

        return redirect()->back()->with('success', 'Plan d\'abonnement mis à jour avec succès.');
    }
}

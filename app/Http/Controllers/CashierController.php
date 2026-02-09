<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\WalkInConsultation;
use App\Models\Service;
use App\Models\Prestation;
use App\Models\LabRequest; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * Tableau de bord avec statistiques
     */
    public function dashboard()
    {
        $user = auth()->user();
        $hospitalId = $user->hospital_id;
        $serviceId = $user->service_id;

        // 1. Pending Payments (Appointments + Walk-ins)
        $pendingAppointmentsQuery = Appointment::where('hospital_id', $hospitalId)
            ->whereNotIn('status', ['paid', 'cancelled']) // Check status first
            ->whereDoesntHave('invoices', function($q) {
                // Double check against actual invoices to avoid "Pending" ghost items if status wasn't updated
                $q->where('status', 'paid');
            })
            ->with(['patient', 'service', 'doctor', 'prestations']);

        $this->applyCashierScope($pendingAppointmentsQuery, $user);
            
        $pendingAppointments = $pendingAppointmentsQuery->get();
            
        $pendingWalkInsQuery = WalkInConsultation::where('hospital_id', $hospitalId)
            ->where('status', 'pending_payment')
            ->with(['patient', 'service', 'prestations']);

        $this->applyCashierScope($pendingWalkInsQuery, $user);

        $pendingWalkIns = $pendingWalkInsQuery->get();

        // Fetch Unpaid Lab Requests
        $pendingLabRequestsQuery = LabRequest::where('hospital_id', $hospitalId)
            ->where('is_paid', false)
            ->with(['patientVital', 'doctor', 'service']);

        $this->applyCashierScope($pendingLabRequestsQuery, $user, true);

        $pendingLabRequests = $pendingLabRequestsQuery->get();

        // Merge collections for display list
        $pendingCount = $pendingAppointments->count() + $pendingWalkIns->count() + $pendingLabRequests->count();

        // Single source of truth for revenue: Use Invoice model with service_id filter
        $paidInvoicesQuery = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'paid');
            
        if ($serviceId) {
            $this->applyCashierScope($paidInvoicesQuery, $user);
        }
            
        $paidInvoices = $paidInvoicesQuery->get();
// ...
        // All Time
        $paidCountTotal = $paidInvoices->count();
        $totalRevenue = $paidInvoices->sum('total');

        // Today
        $todayInvoices = $paidInvoices->filter(function($invoice) {
            return $invoice->created_at->isToday();
        });
        $todayRevenue = $todayInvoices->sum('total');

        $todayInsurance = $todayInvoices->sum(function($invoice) {
            return ($invoice->total * ($invoice->insurance_coverage_rate ?? 0)) / 100;
        });

        $stats = [
            'pending' => $pendingCount,
            'paid_total' => $paidCountTotal,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'today_insurance' => $todayInsurance
        ];

        // Prepare Pending Payments List (Merged)
        $pendingPayments = $pendingAppointments->map(function($item) {
            $item->type_label = 'Rendez-vous';
            $item->payment_type = 'appointment';
            return $item;
        })->concat($pendingWalkIns->map(function($item) {
            $item->type_label = 'Sans RDV';
            $item->payment_type = 'walk-in';
            return $item;
        }))->concat($pendingLabRequests->map(function($item) {
            $item->type_label = 'Analyse Labo';
            $item->payment_type = 'lab_request';
            $item->appointment_datetime = $item->created_at; 
            $item->patient = (object)['name' => $item->patient_name, 'ipu' => $item->patient_ipu];
            $item->prestations = collect(); // FIX: Compatibility with count() and sum()
            
            // Mock service for view compatibility
            $prestation = \App\Models\Prestation::where('name', $item->test_name)
                ->where('hospital_id', $item->hospital_id)
                ->first();
            $item->service = (object)[
                'name' => 'Labo: ' . $item->test_name,
                'price' => $prestation ? $prestation->price : 5000
            ];
            
            return $item;
        }))->sortBy('created_at');

        // Recent Payments (Unified)...
        // (Existing code for recent payments...)
        // We might want to add Lab Requests to recent history too if tracked via Invoices properly.

        $recentPaidInvoicesQuery = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'paid')
            ->where('updated_at', '>=', now()->subDays(30))
            ->with(['patient', 'appointment.service', 'labRequest']);

        if ($serviceId) {
            $this->applyCashierScope($recentPaidInvoicesQuery, $user);
        }

        $recentPayments = $recentPaidInvoicesQuery->latest('updated_at')->take(10)->get();

        return view('cashier.dashboard', compact('pendingPayments', 'recentPayments', 'stats'));
    }

    /**
     * Encaisser une demande d'analyse (LabRequest)
     */
    public function payLabRequest(Request $request, LabRequest $labRequest)
    {
        $user = auth()->user();
        if (!$this->canAccess($user, $labRequest)) {
            abort(403);
        }

        try {
            if ($labRequest->is_paid) {
                return back()->with('info', 'Cette analyse a déjà été payée.');
            }

            if ($request->payment_method === 'Mobile Money') {
                return $this->initiateMobileMoneyPayment($request, $labRequest, 'lab');
            }

            $this->executePayment($labRequest, $request->payment_method ?? 'Espèces', 'lab', $request->mobile_operator);
            return back()->with('success', 'Paiement analyse encaissé !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur paiement analyse : ' . $e->getMessage()]);
        }
    }

    /**
     * Liste de tous les rendez-vous pour la caisse
     */
    public function appointments(Request $request)
    {
        $user = auth()->user();
        $hospitalId = $user->hospital_id;
        $dateFilter = $request->query('date_filter', 'today');
        
        $query = Appointment::where('hospital_id', $hospitalId)
            ->where(function($query) {
                $query->where('type', '!=', 'walk-in')
                      ->orWhereNull('type');
            })
            ->with(['patient', 'service', 'prestations', 'invoices'])
            ->orderBy('appointment_datetime', 'desc');

        // Apply date filter
        switch ($dateFilter) {
            case 'yesterday':
                $query->whereDate('appointment_datetime', \Carbon\Carbon::yesterday());
                break;
            case 'tomorrow':
                $query->whereDate('appointment_datetime', \Carbon\Carbon::tomorrow());
                break;
            case 'this_week':
                $query->whereBetween('appointment_datetime', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                break;
            case 'today':
            default:
                $query->whereDate('appointment_datetime', \Carbon\Carbon::today());
                break;
        }

        $this->applyCashierScope($query, $user);

        $appointments = $query->get()->map(function($apt) {
            $apt->payment_type = 'appointment';
            return $apt;
        });

        $appointments = $appointments->sortByDesc('appointment_datetime');

        return view('cashier.appointments', compact('appointments', 'dateFilter'));
    }

    /**
     * Validation du paiement et génération de facture
     */
    public function validatePayment(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        if ($appointment->hospital_id !== $user->hospital_id) {
            abort(403);
        }
        
        if (!$this->canAccess($user, $appointment)) {
            abort(403, 'Accès non autorisé à ce service.');
        }

        try {
            if ($request->payment_method === 'Mobile Money') {
                return $this->initiateMobileMoneyPayment($request, $appointment, 'appointment');
            }
            $this->executePayment($appointment, $request->payment_method ?? 'Espèces', 'appointment', $request->mobile_operator);
            return back()->with('success', 'Paiement encaissé et facture générée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Échec de la transaction : ' . $e->getMessage()]);
        }
    }

    /**
     * Cœur de la logique de paiement (Partagé par les différentes entrées)
     */
    private function executePayment($target, $paymentMethod, $type = 'appointment', $paymentOperator = null)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $hospitalId = $target->hospital_id;
            
            // 1. Déterminer les montants
            $servicePrice = 0;
            $prestations = $type !== 'lab' ? $target->prestations : collect();
            
            if ($type === 'appointment') {
                $servicePrice = $target->service->price ?? 0;
                $appointment = $target;
                $serviceId = $appointment->service_id;
                $patientId = $appointment->patient_id;
                $admission = \App\Models\Admission::where('appointment_id', $appointment->id)->first();
                $invoicePrefix = 'INV';
                $invoiceIdSuffix = $appointment->id;
                $walkInId = null;
                $labRequestId = null;
            } elseif ($type === 'walk-in') {
                $servicePrice = 0; // Inclus dans les prestations pour les walk-ins
                $appointment = \App\Models\Appointment::updateOrCreate(
                    ['hospital_id' => $hospitalId, 'patient_id' => $target->patient_id, 'type' => 'walk-in', 'appointment_datetime' => $target->consultation_datetime],
                    [
                        'service_id' => $target->service_id,
                        'status' => 'paid',
                        'reason' => 'Consultation Sans RDV',
                    ]
                );
                // Attach prestations if newly created
                if ($appointment->wasRecentlyCreated) {
                    foreach ($prestations as $prestation) {
                        $appointment->prestations()->attach($prestation->id, [
                            'quantity' => $prestation->pivot->quantity,
                            'unit_price' => $prestation->pivot->unit_price,
                            'total' => $prestation->pivot->total,
                        ]);
                    }
                }
                $serviceId = $target->service_id;
                $patientId = $target->patient_id;
                $admission = null;
                $invoicePrefix = 'WALK';
                $invoiceIdSuffix = $target->id;
                $walkInId = $target->id;
                $labRequestId = null;
            } else { // Lab
                $serviceId = $target->service_id;
                $patientId = Patient::withoutGlobalScopes()->where('ipu', $target->patient_ipu)->first()->id ?? null;
                $admission = null;
                $invoicePrefix = 'LAB';
                $invoiceIdSuffix = $target->id;
                $walkInId = null;
                $labRequestId = $target->id;
                $appointment = null;

                // Handle lab test as a "prestation" for invoice item creation
                $prestation = Prestation::where('name', $target->test_name)
                    ->where('hospital_id', $hospitalId)
                    ->first();
                $price = $prestation ? $prestation->price : 5000;

                $prestations = collect([
                    (object)[
                        'id' => $prestation->id ?? null,
                        'name' => $target->test_name,
                        'pivot' => (object)[
                            'quantity' => 1,
                            'unit_price' => $price,
                            'total' => $price,
                        ]
                    ]
                ]);
            }

            $subtotalPrestations = $prestations->sum('pivot.total');
            $subtotal = $servicePrice + $subtotalPrestations;
            $tax = $subtotal * 0.18;
            $total = $subtotal + $tax;

            // Handle Payment Operator (Mobile or Insurance)
            $finalOperator = $paymentOperator;
            $insuranceName = null;
            $insuranceCard = null;
            $insuranceRate = null;

            // NEW: Pull insurance info from target if available (especially for deferred co-payments)
            if ($type === 'walk-in' && isset($target->insurance_name)) {
                $insuranceName = $target->insurance_name;
                $insuranceCard = $target->insurance_card_number;
                $insuranceRate = $target->insurance_coverage_rate;
            }

            if ($paymentMethod === 'Assurance') {
                $insuranceName = request()->insurance_name ?? $insuranceName;
                $insuranceCard = request()->insurance_card_number ?? $insuranceCard;
                $insuranceRate = request()->insurance_coverage_rate ?? $insuranceRate;
                $finalOperator = $insuranceName; // For backwards compatibility or if not Mobile
            } elseif (!$finalOperator) {
                $finalOperator = $target->payment_operator ?? null;
            }

            // 2. Déterminer le statut initial (Gestion de l'assurance partielle)
            $isPartialInsurance = ($paymentMethod === 'Assurance' && $insuranceRate > 0 && $insuranceRate < 100);
            $newStatus = $isPartialInsurance ? 'pending' : 'paid';
            $paidAt = $isPartialInsurance ? null : now();

            // 3. Rechercher une facture en attente existante pour ce target (Éviter les doublons lors du paiement du reliquat)
            $existingInvoiceQuery = Invoice::where('hospital_id', $hospitalId)->where('status', 'pending');
            if ($type === 'appointment') {
                $existingInvoiceQuery->where('appointment_id', $target->id);
            } elseif ($type === 'walk-in') {
                $existingInvoiceQuery->where('walk_in_consultation_id', $target->id);
            } elseif ($type === 'lab') {
                $existingInvoiceQuery->where('lab_request_id', $target->id);
            }
            $invoice = $existingInvoiceQuery->first();

            if ($invoice) {
                // Mise à jour de la facture existante si on paye le ticket modérateur par exemple
                $invoice->update([
                    'payment_method' => $paymentMethod,
                    'payment_operator' => $finalOperator,
                    'insurance_name' => $insuranceName ?? $invoice->insurance_name,
                    'insurance_card_number' => $insuranceCard ?? $invoice->insurance_card_number,
                    'insurance_coverage_rate' => $insuranceRate ?? $invoice->insurance_coverage_rate,
                    'status' => $newStatus,
                    'paid_at' => $paidAt,
                    'cashier_id' => $user->id,
                ]);
            } else {
                // Créer une nouvelle facture
                $invoice = Invoice::create([
                    'hospital_id' => $hospitalId,
                    'service_id' => $serviceId,
                    'invoice_number' => $invoicePrefix . '-' . now()->format('ymdHi') . '-' . str_pad($invoiceIdSuffix, 4, '0', STR_PAD_LEFT),
                    'patient_id' => $patientId,
                    'appointment_id' => $appointment ? $appointment->id : null,
                    'admission_id' => $admission ? $admission->id : null,
                    'walk_in_consultation_id' => $walkInId,
                    'lab_request_id' => $labRequestId,
                    'invoice_date' => now(),
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'status' => $newStatus,
                    'payment_method' => $paymentMethod,
                    'payment_operator' => $finalOperator,
                    'insurance_name' => $insuranceName,
                    'insurance_card_number' => $insuranceCard,
                    'insurance_coverage_rate' => $insuranceRate,
                    'insurance_settlement_status' => ($insuranceRate > 0) ? 'pending' : null,
                    'paid_at' => $paidAt,
                    'cashier_id' => $user->id,
                ]);

                // 4. Créer les items (Seulement si nouvelle facture)
                if ($servicePrice > 0) {
                    InvoiceItem::create([
                        'hospital_id' => $hospitalId,
                        'invoice_id' => $invoice->id,
                        'description' => "Consultation : " . ($appointment->service->name ?? 'Service'),
                        'quantity' => 1,
                        'unit_price' => $servicePrice,
                        'total' => $servicePrice,
                    ]);
                }

                foreach ($prestations as $prestation) {
                    InvoiceItem::create([
                        'hospital_id' => $hospitalId,
                        'invoice_id' => $invoice->id,
                        'description' => $prestation->name,
                        'quantity' => $prestation->pivot->quantity,
                        'unit_price' => $prestation->pivot->unit_price,
                        'total' => $prestation->pivot->total,
                    ]);
                }
            }

            // 5. Marquer la cible comme payée UNIQUEMENT si la facture est "paid"
            if ($invoice->status === 'paid') {
                if ($type === 'appointment') {
                    $target->update(['status' => 'paid']);
                } elseif ($type === 'walk-in') {
                    $target->update(['status' => 'paid']);
                } else { // Lab
                    $target->update(['is_paid' => true]);
                }
            }

            DB::commit();
            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function payments(Request $request)
    {
        $user = auth()->user();
        $hospitalId = $user->hospital_id;
        
        $dateFilter = $request->query('date_filter', 'today');
        $search = $request->query('search');
        $method = $request->query('method');

        // Base query for paid invoices
        $query = Invoice::where('hospital_id', $hospitalId)->where('status', 'paid');
        
        $this->applyCashierScope($query, $user);

        // Date Filtering
        switch ($dateFilter) {
            case 'yesterday':
                $query->whereDate('invoice_date', \Carbon\Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('invoice_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('invoice_date', \Carbon\Carbon::now()->month)
                      ->whereYear('invoice_date', \Carbon\Carbon::now()->year);
                break;
            case 'all':
                break;
            case 'today':
            default:
                $query->whereDate('invoice_date', \Carbon\Carbon::today());
                break;
        }

        // Search Filter (Patient Name or Invoice Number)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Method Filter
        if ($method && $method !== 'all') {
            $query->where('payment_method', 'like', "%{$method}%");
        }

        // Aggregates for the FILTERED set
        $totalRevenue = (clone $query)->sum('total');
        $paymentCount = (clone $query)->count();
        $averagePayment = $paymentCount > 0 ? $totalRevenue / $paymentCount : 0;

        // Paginated results for the list
        $payments = $query->with([
                'patient',
                'appointment.service',
                'appointment.prestations',
                'labRequest',
                'walkInConsultation.service',
                'admission'
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cashier.payments', compact('payments', 'totalRevenue', 'paymentCount', 'averagePayment', 'dateFilter', 'search', 'method'));
    }

    public function invoices() {
        $user = auth()->user();
        $hospitalId = $user->hospital_id;
        $serviceId = $user->service_id;
        
        $query = Invoice::where('hospital_id', $hospitalId)
            ->with(['patient', 'admission.appointment.service', 'admission.appointment.prestations'])
            ->latest();
            
        $this->applyCashierScope($query, $user);
            
        $invoices = $query->with(['patient', 'appointment.service', 'labRequest', 'walkInConsultation.service'])->paginate(15);
        return view('cashier.invoices', compact('invoices'));
    }

    public function patients() {
        // Patients are generally hospital-wide, but we could restrict if needed.
        // For now, let's keep it hospital-wide as cashiers might need to find patients created by others.
        $patients = Patient::where('hospital_id', auth()->user()->hospital_id)
            ->latest()
            ->paginate(20);
        return view('cashier.patients', compact('patients'));
    }

    public function settings() {
        return view('cashier.settings');
    }

    public function rejectPayment(Appointment $appointment) {
        $user = auth()->user();
        // Check service access
        if (!$this->canAccess($user, $appointment)) {
            abort(403, 'Accès non autorisé à ce service.');
        }
        
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'Rendez-vous annulé.');
    }

    public function showInvoice(Invoice $invoice) {
        $user = auth()->user();
        if ($invoice->hospital_id !== $user->hospital_id) {
            abort(403);
        }
        
        $invoice->load(['patient', 'items', 'appointment.service', 'appointment.prestations', 'admission']);
        
        // Check service access
        if (!$this->canAccess($user, $invoice)) {
             abort(403, 'Accès non autorisé à cette facture.');
        }

        return view('cashier.invoice_show', compact('invoice'));
    }

    public function printInvoice(Invoice $invoice) {
        $user = auth()->user();
        if ($invoice->hospital_id !== $user->hospital_id) {
            abort(403);
        }
        
        $invoice->load(['patient', 'items', 'appointment.service', 'appointment.prestations', 'admission', 'hospital']);

        // Check service access
        if (!$this->canAccess($user, $invoice)) {
             abort(403, 'Accès non autorisé à cette facture.');
        }

        return view('cashier.invoices.print', compact('invoice'));
    }

    public function downloadInvoice(Invoice $invoice) {
        $user = auth()->user();
        if ($invoice->hospital_id !== $user->hospital_id) {
            abort(403);
        }

        $invoice->load(['patient', 'items', 'appointment.service', 'appointment.prestations', 'admission', 'hospital', 'labRequest', 'walkInConsultation']);
        
        // Check service access
        if (!$this->canAccess($user, $invoice)) {
            abort(403, 'Accès non autorisé à cette facture.');
        }

        $appointment = $invoice->appointment;
        $total = $invoice->total; // Use invoice total directly


        return response()->view('cashier.invoices.pdf', compact('invoice', 'total'))
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="Facture_'.$invoice->invoice_number.'.html"');
    }

    /**
     * Liste des consultations sans rendez-vous
     */
    public function walkInConsultations(Request $request)
    {
        $user = auth()->user();
        $hospitalId = $user->hospital_id;
        $serviceId = $user->service_id;

        $query = WalkInConsultation::where('hospital_id', $hospitalId)
            ->with(['patient', 'service', 'prestations'])
            ->orderBy('created_at', 'desc');

    // Filter by date
    if ($request->filter) {
        $filter = $request->filter;
        if ($filter == 'today') {
            $query->whereDate('created_at', today());
        } elseif ($filter == 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($filter == 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }
    }

        $this->applyCashierScope($query, $user);

        $walkInConsultations = $query->paginate(15);

        // Fetch Lab Requests for statistics and history (Today's requests or Unpaid)
        $pendingLabRequestsQuery = LabRequest::where('hospital_id', $hospitalId)
            ->where(function($q) {
                $q->where('is_paid', false)
                  ->orWhereDate('updated_at', today());
            })
            ->with(['doctor', 'service', 'patientVital'])
            ->orderBy('is_paid', 'asc') // Unpaid first
            ->orderBy('created_at', 'desc');

        $this->applyCashierScope($pendingLabRequestsQuery, $user, true);

        $pendingLabRequests = $pendingLabRequestsQuery->get();

        // Filter services list for creation form
        $servicesQuery = Service::where('hospital_id', $hospitalId);
        // For walk-in creation, allow selecting any service that the cashier is scoped for
        $this->applyCashierScope($servicesQuery, $user);
        $services = $servicesQuery->get();
        
        foreach($services as $service) {
            $service->consultation_price = $this->getConsultationPrice($service);
        }
        
        $prestations = Prestation::where('hospital_id', $hospitalId)->get(); // Prestations can remain all, or filtered if strict

        return view('cashier.walk-in.index', compact('walkInConsultations', 'pendingLabRequests', 'services', 'prestations'));
    }

    /**
     * Helper to get consultation price
     */
    private function getConsultationPrice($service)
    {
        // Try to find a prestation named like the service or explicitly marked as consultation for this service
        $prestation = Prestation::where('hospital_id', $service->hospital_id)
            ->where(function($q) use ($service) {
                $q->where('service_id', $service->id)
                  ->where('category', 'consultation');
            })
            ->orWhere(function($q) use ($service) {
                $q->where('hospital_id', $service->hospital_id)
                  ->where('name', 'like', '%' . $service->name . '%')
                  ->where('category', 'consultation');
            })
            ->first();

        return $prestation ? $prestation->price : 0;
    }

    /**
     * Créer une nouvelle consultation sans rendez-vous
     */
    public function createWalkInConsultation(Request $request)
    {
        \Log::info('Walk-In Creation Request:', $request->all());
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'patient_age' => 'required|integer|min:0|max:120',
            'patient_email' => 'nullable|email',
            'service_id' => 'required|exists:services,id',
            'consultation_prestation_id' => 'required|exists:prestations,id',
            'payment_mode' => 'required|in:cash,mobile_money,assurance',
            'mobile_operator' => 'required_if:payment_mode,mobile_money|nullable|in:mtn,orange,moov,wave',
            'mobile_number' => 'required_if:payment_mode,mobile_money|nullable|string',
            'insurance_name' => 'required_if:payment_mode,assurance|nullable|string',
            'insurance_card_number' => 'required_if:payment_mode,assurance|nullable|string',
            'insurance_coverage_rate' => 'required_if:payment_mode,assurance|nullable|integer|min:0|max:100',
            'prestation_ids' => 'nullable|array',
            'prestation_ids.*' => 'exists:prestations,id',
        ]);

        $hospitalId = auth()->user()->hospital_id;

        // Créer ou récupérer le patient (Identification par Nom + Téléphone pour éviter les confusions de famille)
        $patient = Patient::firstOrCreate(
            [
                'name' => $request->patient_name, 
                'phone' => $request->patient_phone, 
                'hospital_id' => $hospitalId
            ],
            [
                'first_name' => $request->patient_name,
                'email' => $request->patient_email,
                'hospital_id' => $hospitalId,
                'ipu' => Patient::generateIpu(),
                'dob' => now()->subYears($request->patient_age)->startOfYear(), // Date approx selon l'âge
                'gender' => 'Other',
                'address' => 'Non renseigné',
            ]
        );

        // Créer la consultation
        $consultation = WalkInConsultation::create([
            'hospital_id' => $hospitalId,
            'patient_id' => $patient->id,
            'service_id' => $request->service_id,
            'status' => 'pending_payment',
            'consultation_datetime' => now(),
            'cashier_id' => auth()->id(),
            'insurance_name' => $request->insurance_name,
            'insurance_card_number' => $request->insurance_card_number,
            'insurance_coverage_rate' => $request->insurance_coverage_rate,
        ]);

        // Attacher l'acte principal
        $mainPrestation = Prestation::find($request->consultation_prestation_id);
        if ($mainPrestation) {
            $consultation->prestations()->attach($mainPrestation->id, [
                'quantity' => 1,
                'unit_price' => $mainPrestation->price,
                'total' => $mainPrestation->price,
            ]);
        }

        // Attacher les prestations additionnelles
        if ($request->prestation_ids) {
            foreach ($request->prestation_ids as $prestationId) {
                if ($prestationId == $request->consultation_prestation_id) continue;

                $prestation = Prestation::find($prestationId);
                if ($prestation) {
                    $consultation->prestations()->attach($prestationId, [
                        'quantity' => 1,
                        'unit_price' => $prestation->price,
                        'total' => $prestation->price,
                    ]);
                }
            }
        }

        // Gérer le paiement
    if ($request->payment_mode === 'mobile_money') {
        return $this->initiateMobileMoneyPayment($request, $consultation, 'walk-in');
    }

    // For Insurance, we defer the payment validation to the modal step
    if ($request->payment_mode === 'assurance') {
        return redirect()->route('cashier.walk-in.index')->with('success', 'Consultation créée. Veuillez procéder au paiement du Ticket Modérateur.');
    }

    $paymentMethod = 'Espèces';

    // Pour le cash, on valide immédiatement si c'est "Enregistrer et Encaisser"
    try {
        $this->executePayment($consultation, $paymentMethod, 'walk-in', $request->mobile_operator);
        return redirect()->route('cashier.walk-in.index')->with('success', 'Consultation créée et paiement validé !');
    } catch (\Exception $e) {
        return redirect()->route('cashier.walk-in.index')->with('success', 'Consultation créée mais le paiement a échoué : ' . $e->getMessage());
    }    }

    /**
     * Récupérer les détails d'une consultation
     */
    public function getWalkInDetails(WalkInConsultation $consultation)
    {
        $user = auth()->user();

        if (!$this->canAccess($user, $consultation)) {
            abort(403);
        }

        $consultation->load(['patient', 'service', 'prestations']);
        
        $total = $consultation->prestations->sum('pivot.total');
        $tax = $total * 0.18;
        $grandTotal = $total + $tax;

        // Récupérer la facture en attente si elle existe pour l'assurance
        $pendingInvoice = Invoice::where('walk_in_consultation_id', $consultation->id)
                                ->where('status', 'pending')
                                ->first();

        return view('cashier.walk-in.details', compact('consultation', 'total', 'tax', 'grandTotal', 'pendingInvoice'));
    }

    /**
     * Récupérer les détails d'une analyse labo pour la modale
     */
    public function getLabRequestDetails(LabRequest $labRequest)
    {
        $user = auth()->user();
        if (!$this->canAccess($user, $labRequest)) {
            abort(403);
        }

        $labRequest->load(['doctor', 'service', 'patientVital']);
        
        $prestation = Prestation::where('name', $labRequest->test_name)->first();
        $total = $prestation ? $prestation->price : 5000;
        $tax = $total * 0.18;
        $grandTotal = $total + $tax;

        // Récupérer la facture en attente si elle existe pour l'assurance
        $pendingInvoice = Invoice::where('lab_request_id', $labRequest->id)
                                ->where('status', 'pending')
                                ->first();

        return view('cashier.walk-in.lab_details', compact('labRequest', 'total', 'tax', 'grandTotal', 'pendingInvoice'));
    }

    /**
     * Valider le paiement d'une consultation sans rendez-vous
     */
    public function validateWalkInPayment(Request $request, WalkInConsultation $consultation)
    {
        $user = auth()->user();
        if (!$this->canAccess($user, $consultation)) {
            abort(403);
        }

        try {
            if ($request->payment_method === 'Mobile Money') {
                return $this->initiateMobileMoneyPayment($request, $consultation, 'walk-in');
            }
            $invoice = $this->executePayment($consultation, $request->payment_method ?? 'Espèces', 'walk-in', $request->mobile_operator);
            
            $message = ($invoice->status === 'pending') 
                ? 'Assurance enregistrée. Veuillez maintenant encaisser le ticket modérateur du patient.'
                : 'Paiement validé, facture générée et dossier envoyé à l\'infirmier !';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Échec de la transaction : ' . $e->getMessage()]);
        }
    }

    /**
     * Initiate mobile money payment
     */
    private function initiateMobileMoneyPayment(Request $request, $target, $type = 'appointment')
    {
        $mobileMoneyService = new \App\Services\MobileMoneyService();
        
        $hospitalId = $target->hospital_id;
        $patient = null;
        $description = '';
        $items = [];
        $grandTotal = 0;

        if ($type === 'appointment') {
            $patient = $target->patient;
            $description = 'Rendez-vous - ' . ($target->service->name ?? 'Service');
            $servicePrice = $target->service->price ?? 0;
            $items[] = ['name' => "Consultation : " . ($target->service->name ?? 'Service'), 'quantity' => 1, 'unit_price' => $servicePrice, 'total' => $servicePrice];
            foreach ($target->prestations as $p) {
                $items[] = ['name' => $p->name, 'quantity' => $p->pivot->quantity, 'unit_price' => $p->pivot->unit_price, 'total' => $p->pivot->total];
            }
        } elseif ($type === 'walk-in') {
            $patient = $target->patient;
            $description = 'Consultation Sans RDV - ' . ($target->service->name ?? 'Service');
            foreach ($target->prestations as $p) {
                $items[] = ['name' => $p->name, 'quantity' => $p->pivot->quantity, 'unit_price' => $p->pivot->unit_price, 'total' => $p->pivot->total];
            }
        } elseif ($type === 'lab') {
            $patient = Patient::withoutGlobalScopes()->where('ipu', $target->patient_ipu)->first();
            $description = 'Analyse Labo - ' . $target->test_name;
            $prestation = Prestation::where('name', $target->test_name)->first();
            $price = $prestation ? $prestation->price : 5000;
            $items[] = ['name' => $target->test_name, 'quantity' => 1, 'unit_price' => $price, 'total' => $price];
        }

        $subtotal = collect($items)->sum('total');
        $tax = $subtotal * 0.18;
        $grandTotal = $subtotal + $tax;

        // Deduct insurance if applicable (Co-payment)
        if ($type === 'walk-in' && $target->insurance_coverage_rate > 0) {
            $insurancePart = ($grandTotal * $target->insurance_coverage_rate) / 100;
            $grandTotal = $grandTotal - $insurancePart;
        }

        $paymentData = [
            'amount' => (int) $grandTotal,
            'customer_name' => $patient ? $patient->name : 'Client',
            'customer_surname' => $patient ? ($patient->first_name ?? $patient->name) : 'Client',
            'customer_phone' => $request->mobile_number,
            'customer_email' => $patient ? $patient->email : 'noreply@hospital.com',
            'customer_address' => $patient ? ($patient->address ?? 'N/A') : 'N/A',
            'customer_city' => $patient ? ($patient->city ?? 'N/A') : 'N/A',
            'operator' => $request->mobile_operator,
            'description' => $description,
            'target_id' => $target->id,
            'hospital_id' => $hospitalId,
            'items' => $items
        ];
        
        // Add specific IDs for metadata and set prefix
        if ($type === 'appointment') {
            $paymentData['appointment_id'] = $target->id;
            $paymentData['transaction_prefix'] = 'APT';
        } elseif ($type === 'walk-in') {
            $paymentData['consultation_id'] = $target->id;
            $paymentData['transaction_prefix'] = 'WALK';
        } elseif ($type === 'lab') {
            $paymentData['lab_request_id'] = $target->id;
            $paymentData['transaction_prefix'] = 'LAB';
        }

        $paymentResult = $mobileMoneyService->initiatePayment($paymentData);
        
        if ($paymentResult['success']) {
            $updateData = [
                'payment_transaction_id' => $paymentResult['transaction_id'],
                'payment_method' => 'mobile_money',
                'payment_operator' => $request->mobile_operator,
            ];

            if ($type === 'lab') {
                // LabRequest table might not have these columns, check migration if possible or skip if unsure
                // But typically it's needed to track status.
                // Assuming target has these columns or they were added.
            }

            $target->update($updateData);
            
            return redirect($paymentResult['payment_url'])
                ->with('info', 'Redirection vers la page de paiement...');
        }
        
        return back()->withErrors(['error' => $paymentResult['message'] ?? 'Erreur lors de l\'initialisation du paiement']);
    }

    /**
     * Handle mobile money webhook
     */
    public function handleMobileMoneyWebhook(Request $request)
    {
        $mobileMoneyService = new \App\Services\MobileMoneyService();
        
        if (!$mobileMoneyService->verifyWebhookSignature($request->all())) {
            return response()->json(['status' => 'invalid_signature'], 403);
        }
        
        $transactionId = $request->input('cpm_trans_id');
        $status = $request->input('cpm_result');
        $metadata = json_decode($request->input('metadata'), true);
        
        if ($status == '00') {
            $appointmentId = $metadata['appointment_id'] ?? null;
            $consultationId = $metadata['consultation_id'] ?? null;
            $labRequestId = $metadata['lab_request_id'] ?? null;
            
            $target = null;
            $type = 'appointment';

            if ($appointmentId) {
                $target = \App\Models\Appointment::find($appointmentId);
                $type = 'appointment';
            } elseif ($consultationId) {
                $target = \App\Models\WalkInConsultation::find($consultationId);
                $type = 'walk-in';
            } elseif ($labRequestId) {
                $target = \App\Models\LabRequest::find($labRequestId);
                $type = 'lab';
            }
            
            if ($target) {
                $this->processSuccessfulPayment($target, $request, $type);
            }
        }
        
        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment($target, Request $request, $type = 'appointment')
    {
        try {
            $this->executePayment($target, 'Mobile Money', $type, $target->payment_operator);
            \Log::info('Mobile Money Payment Processed Successfully', ['type' => $type, 'id' => $target->id]);
        } catch (\Exception $e) {
            \Log::error('Mobile Money Payment Processing Error', [
                'type' => $type,
                'id' => $target->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // MÉTHODES DE SIMULATION (DEV ONLY)
    // ==========================================

    public function simulatePayment($transactionId)
    {
        $target = null;
        $amount = 0;
        $insuranceName = null;
        $insuranceRate = 0;

        if (str_starts_with($transactionId, 'APT-')) {
            $target = \App\Models\Appointment::where('payment_transaction_id', $transactionId)->first();
            if ($target) {
                $subtotal = ($target->service->price ?? 0) + $target->prestations->sum('pivot.total');
                $amount = $subtotal * 1.18;
            }
        } elseif (str_starts_with($transactionId, 'WALK-')) {
            $target = \App\Models\WalkInConsultation::where('payment_transaction_id', $transactionId)->first();
            if ($target) {
                $amount = $target->prestations->sum('pivot.total') * 1.18;
                $insuranceName = $target->insurance_name;
                $insuranceRate = $target->insurance_coverage_rate;
            }
        } elseif (str_starts_with($transactionId, 'LAB-')) {
            $target = \App\Models\LabRequest::where('payment_transaction_id', $transactionId)->first();
            if ($target) {
                $prestation = \App\Models\Prestation::where('name', $target->test_name)->first();
                $price = $prestation ? $prestation->price : 5000;
                $amount = $price * 1.18;
            }
        }

        $originalAmount = $amount;
        if ($insuranceRate > 0) {
            $amount = $originalAmount * (1 - ($insuranceRate / 100));
        }

        $operator = $target->payment_operator ?? 'Mobile Money';

        return view('cashier.simulation', compact('transactionId', 'amount', 'operator', 'insuranceName', 'insuranceRate', 'originalAmount'));
    }

    public function processSimulation(Request $request, $transactionId)
    {
        $target = null;
        $type = 'appointment';

        if (str_starts_with($transactionId, 'APT-')) {
            $target = \App\Models\Appointment::where('payment_transaction_id', $transactionId)->firstOrFail();
            $type = 'appointment';
        } elseif (str_starts_with($transactionId, 'WALK-')) {
            $target = \App\Models\WalkInConsultation::where('payment_transaction_id', $transactionId)->firstOrFail();
            $type = 'walk-in';
        } elseif (str_starts_with($transactionId, 'LAB-')) {
            $target = \App\Models\LabRequest::where('payment_transaction_id', $transactionId)->firstOrFail();
            $type = 'lab';
        } else {
            abort(404, "Type de transaction inconnu");
        }
        
        $this->processSuccessfulPayment($target, $request, $type);

        $redirectRoute = match($type) {
            'appointment' => 'cashier.appointments.index',
            'walk-in' => 'cashier.walk-in.index',
            'lab' => 'cashier.walk-in.index',
            default => 'cashier.dashboard'
        };

        return redirect()->route($redirectRoute)->with('success', 'Simulation réussie. Le paiement a été validé !');
    }

    /**
     * Vérifie si l'utilisateur a accès à un modèle spécifique selon sa caisse
     */
    private function canAccess($user, $model)
    {
        if (!$model) return true;
        
        // Vérification de l'hôpital
        if ($model->hospital_id !== $user->hospital_id) {
            return false;
        }

        $query = get_class($model)::where('id', $model->id);
        $isLabRequest = $model instanceof LabRequest;
        
        $this->applyCashierScope($query, $user, $isLabRequest);
        
        return $query->exists();
    }

    /**
     * Applique les filtres de périmètre selon le type de caisse (Accueil vs Labo vs Urgence)
     */
    private function applyCashierScope($query, $user, $isLabRequest = false)
    {
        $service = $user->service;
        if (!$service || !$service->is_caisse) {
            // If not a cashier service, maybe allow everything or nothing? 
            // Standard approach: if not specified, don't filter or filter by hospital.
            return;
        }

        $caisseType = $service->caisse_type;
        $isServiceQuery = $query->getModel() instanceof Service;
        
        if ($caisseType === 'labo') {
            // Caisse Labo : Uniquement Labo
            if ($isLabRequest) {
                $query->where('test_category', '!=', 'imagerie');
            } elseif ($isServiceQuery) {
                $query->where(function($q) {
                    $q->where('caisse_type', 'labo')
                      ->orWhere('name', 'like', '%Labo%');
                });
            } else {
                $query->whereHas('service', function($q) {
                    $q->where('caisse_type', 'labo')
                      ->orWhere('name', 'like', '%Labo%');
                });
            }
        } elseif ($caisseType === 'urgence') {
            // Caisse Urgence : Uniquement Urgence
            if ($isLabRequest) {
                $query->where('id', 0); // Pas de lab requests en caisse urgence ?
            } elseif ($isServiceQuery) {
                $query->where(function($q) {
                   $q->where('caisse_type', 'urgence')
                     ->orWhere('name', 'like', '%Urgence%');
                });
            } else {
                $query->whereHas('service', function($q) {
                    $q->where('caisse_type', 'urgence')
                      ->orWhere('name', 'like', '%Urgence%');
                });
            }
        } else {
            // Caisse Accueil : Tout SAUF Labo et Urgence
            if ($isLabRequest) {
                // Pour l'accueil, on ne montre que l'imagerie dans les LabRequests
                $query->where('test_category', 'imagerie');
            } elseif ($isServiceQuery) {
                $query->where(function($q) {
                    $q->whereNull('caisse_type')
                      ->orWhere(function($typeQ) {
                          $typeQ->where('caisse_type', '!=', 'labo')
                                ->where('caisse_type', '!=', 'urgence');
                      });
                })
                ->where('name', 'not like', '%Labo%')
                ->where('name', 'not like', '%Urgence%');
            } else {
                $query->whereHas('service', function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereNull('caisse_type')
                             ->orWhere(function($typeQ) {
                                 $typeQ->where('caisse_type', '!=', 'labo')
                                       ->where('caisse_type', '!=', 'urgence');
                             });
                    })
                    ->where('name', 'not like', '%Labo%')
                    ->where('name', 'not like', '%Urgence%');
                });
            }
        }
    }
    /**
     * Voir les cartes d'assurance enregistrées
     */
    public function insuranceCards()
    {
        $user = auth()->user();
        $invoices = \App\Models\Invoice::where('hospital_id', $user->hospital_id)
            ->where(function($query) {
                $query->whereNotNull('insurance_name')
                      ->orWhere('insurance_coverage_rate', '>', 0)
                      ->orWhereNotNull('insurance_card_number');
            })
            ->with(['patient'])
            ->latest()
            ->paginate(15);

        return view('cashier.insurance_cards', compact('invoices'));
    }
}

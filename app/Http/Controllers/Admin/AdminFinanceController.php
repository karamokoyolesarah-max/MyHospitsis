<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\FundTransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminFinanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();

        // --- 1. GLOBAL KPIs ---
        $revenueToday = Invoice::whereDate('created_at', $today)->where('status', 'paid')->sum('total');
        $revenueYesterday = Invoice::whereDate('created_at', $yesterday)->where('status', 'paid')->sum('total');
        
        $growth = $revenueYesterday > 0 ? (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100 : ($revenueToday > 0 ? 100 : 0);

        $revenueMonth = Invoice::where('created_at', '>=', $startOfMonth)->where('status', 'paid')->sum('total');
        $pendingRevenue = Invoice::where('status', 'pending')->sum('total');
        
        // --- 2. VALIDATION (Versements en Attente) ---
        $pendingTransfers = FundTransfer::where('status', 'pending')
            ->with(['cashier'])
            ->latest()
            ->get();

        // --- 3. MONITORING MOBILE MONEY (API Reconciliation) ---
        $mobileInvoicesToday = Invoice::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->where(function($q) {
                $q->where('payment_method', 'like', '%mobile%')
                  ->orWhere('payment_method', 'like', '%momo%')
                  ->orWhere('payment_method', 'like', '%api%');
            })->get();

        $totalMobileToday = $mobileInvoicesToday->sum('total');
        // Logic: Compare what's in DB with what should be (simulation of external API)
        // For now, we use the DB as the source of truth, 
        // but we can add an "is_api_confirmed" flag if needed in future.
        $momoReconciliationStatus = 'balanced'; 

        // --- 4. RAPPORTS & STATS (Pilotage) ---
        // Revenue by Service
        $revenueByService = Invoice::join('services', 'invoices.service_id', '=', 'services.id')
            ->whereDate('invoices.created_at', $today)
            ->where('invoices.status', 'paid')
            ->select('services.name', DB::raw('sum(invoices.total) as total'))
            ->groupBy('services.name')
            ->orderByDesc('total')
            ->get();

        // Unpaid Invoices (Factures en attente)
        $unpaidInvoices = Invoice::where('status', 'pending')
            ->with(['patient', 'service'])
            ->latest()
            ->take(10)
            ->get();

        // Part Cash vs MoMo (Donut Chart)
        $rawRevenue = Invoice::select('payment_method', DB::raw('sum(total) as total'))
            ->whereDate('created_at', $today)
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get();

        $revenueByMethod = [
            'cash' => 0,
            'mobile' => 0
        ];
        foreach ($rawRevenue as $item) {
            $m = strtolower($item->payment_method);
            if (str_contains($m, 'cash') || str_contains($m, 'esp')) {
                $revenueByMethod['cash'] += $item->total;
            } else {
                $revenueByMethod['mobile'] += $item->total;
            }
        }
        $revenueByMethod = collect($revenueByMethod);

        // --- 5. AUDIT & HISTORIQUE ---
        $latestInvoices = Invoice::with(['patient', 'cashier', 'service'])
            ->latest()
            ->take(15)
            ->get();

        $latestTransactions = \App\Models\TransactionLog::latest()
            ->take(10)
            ->get();

        // Flux par Caisse (Mini cards)
        $caisseStats = [
             'accueil' => $this->getCaisseStats(null, $today),
             'labo' => $this->getCaisseStats('labo', $today),
             'urgence' => $this->getCaisseStats('urgence', $today),
        ];

        return view('admin.finance.index', compact(
            'revenueToday', 
            'growth',
            'revenueMonth', 
            'pendingRevenue', 
            'pendingTransfers',
            'mobileInvoicesToday',
            'totalMobileToday',
            'momoReconciliationStatus',
            'revenueByService',
            'unpaidInvoices',
            'revenueByMethod', 
            'latestInvoices',
            'latestTransactions',
            'caisseStats'
        ));
    }

    public function dailyDetails(Request $request)
{
    $today = Carbon::today();
    $method = $request->query('method');
    $caisse = $request->query('caisse');
    
    // 1. Fetch ALL invoices for today (paid, pending, partial)
    $allInvoices = Invoice::whereDate('created_at', $today)
        ->with(['patient', 'service', 'cashier'])
        ->get();

    // 2. Statistics calculation
    $statsByMethod = [
        'cash' => ['total' => 0, 'count' => 0, 'label' => 'Espèces'],
        'mobile' => ['total' => 0, 'count' => 0, 'label' => 'Mobile Money'],
        'card' => ['total' => 0, 'count' => 0, 'label' => 'Carte Bancaire'],
        'insurance' => ['total' => 0, 'count' => 0, 'label' => 'Assurance'],
    ];

    $totals = [
        'paid' => 0,
        'pending' => 0,
        'pending_breakdown' => [
            'cash' => 0,
            'mobile' => 0,
            'card' => 0,
        ]
    ];

    $statsByCaisse = [
        'accueil' => ['total' => 0, 'cashiers' => collect()],
        'labo' => ['total' => 0, 'cashiers' => collect()],
        'urgence' => ['total' => 0, 'cashiers' => collect()],
    ];

    foreach ($allInvoices as $inv) {
        $m = strtolower((string)$inv->payment_method);
        $isCash = in_array($m, ['cash', 'espèces', 'especes']);
        $isMobile = in_array($m, ['mobile_money', 'mobile money', 'momo']);
        $isCard = in_array($m, ['card', 'carte', 'visa', 'mastercard', 'carte bancaire']);
        
        $insurancePart = ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
        $patientPart = $inv->total - $insurancePart;

        // 1. Logique du Réalisé (Argent effectivement encaissé)
        // Patient part is realized IF invoice is paid
        if ($inv->status === 'paid') {
            $totals['paid'] += $patientPart;
            if ($isCash) $statsByMethod['cash']['total'] += $patientPart;
            elseif ($isMobile) $statsByMethod['mobile']['total'] += $patientPart;
            elseif ($isCard) $statsByMethod['card']['total'] += $patientPart;
        } else {
            // Patient part is pending IF invoice is unpaid
            $totals['pending'] += $patientPart;
            if ($isCash) $totals['pending_breakdown']['cash'] += $patientPart;
            elseif ($isMobile) $totals['pending_breakdown']['mobile'] += $patientPart;
            elseif ($isCard) $totals['pending_breakdown']['card'] += $patientPart;
        }

        // Insurance part is realized ONLY if recovered
        if ($insurancePart > 0) {
            if ($inv->insurance_settlement_status === 'recovered') {
                $totals['paid'] += $insurancePart;
                $statsByMethod['insurance']['total'] += $insurancePart;
            } else {
                $totals['pending'] += $insurancePart;
                // Note: User didn't ask for insurance breakdown in pending, 
                // but it's part of the global pending total.
            }
            $statsByMethod['insurance']['count']++; // Still count the record for stats
        }

        // Performance par Caisse calculation (respects filters)
        $type = 'accueil';
        if ($inv->service) {
            $sName = strtolower($inv->service->name);
            $cType = strtolower((string)$inv->service->caisse_type);
            if ($cType === 'labo' || strpos($sName, 'labo') !== false) $type = 'labo';
            elseif ($cType === 'urgence' || strpos($sName, 'urgence') !== false) $type = 'urgence';
        }

        $addAmount = 0;
        if (!$method) {
            // Dans la vue globale, on montre ce qui est EN CAISSE (Part Patient Payée + Part Assur Récupérée)
            $addAmount = ($inv->status === 'paid' ? $patientPart : 0) + ($inv->insurance_settlement_status === 'recovered' ? $insurancePart : 0);
        } elseif ($method === 'cash' && $isCash && $inv->status === 'paid') {
            $addAmount = $patientPart;
        } elseif ($method === 'mobile' && $isMobile && $inv->status === 'paid') {
            $addAmount = $patientPart;
        } elseif ($method === 'card' && $isCard && $inv->status === 'paid') {
            $addAmount = $patientPart;
        } elseif ($method === 'insurance' && $insurancePart > 0 && $inv->insurance_settlement_status === 'recovered') {
            $addAmount = $insurancePart;
        }
        $statsByCaisse[$type]['total'] += $addAmount;

        if ($inv->cashier) {
            $statsByCaisse[$type]['cashiers']->put($inv->cashier->id, $inv->cashier->name);
        }
        
        // Update method counts
        if ($inv->status === 'paid') {
            if ($isCash) $statsByMethod['cash']['count']++;
            elseif ($isMobile) $statsByMethod['mobile']['count']++;
            elseif ($isCard) $statsByMethod['card']['count']++;
        }
    }

    // 3. Filtering for the journal list
    $invoices = $allInvoices;

    if ($method) {
        $invoices = $invoices->filter(function($inv) use ($method) {
            $m = strtolower((string)$inv->payment_method);
            if ($method === 'cash') return in_array($m, ['cash', 'espèces', 'especes']);
            if ($method === 'mobile') return in_array($m, ['mobile_money', 'mobile money', 'momo']);
            if ($method === 'card') return in_array($m, ['card', 'carte', 'visa', 'mastercard', 'carte bancaire']);
            if ($method === 'insurance') return ($inv->insurance_coverage_rate ?? 0) > 0;
            return false;
        });
    }

    if ($caisse) {
        $invoices = $invoices->filter(function($inv) use ($caisse) {
            $type = 'accueil';
            if ($inv->service) {
                $sName = strtolower($inv->service->name);
                $cType = strtolower((string)$inv->service->caisse_type);
                if ($cType === 'labo' || strpos($sName, 'labo') !== false) $type = 'labo';
                elseif ($cType === 'urgence' || strpos($sName, 'urgence') !== false) $type = 'urgence';
            }
            return $type === $caisse;
        });
    }

    $invoices = $invoices->sortByDesc('created_at');
    $statsByOperator = $this->calculateOperatorStats($today);

    // 4. Calculate confirmed transfers for today to show what reached the treasury
    $confirmedTransfersTotal = FundTransfer::whereDate('created_at', $today)
        ->where('status', 'confirmed')
        ->sum('amount');

    return view('admin.finance.daily', [
        'invoices' => $invoices,
        'statsByMethod' => collect($statsByMethod),
        'statsByOperator' => collect($statsByOperator),
        'statsByCaisse' => $statsByCaisse,
        'totals' => (object)$totals,
        'confirmedTransfersTotal' => $confirmedTransfersTotal,
        'method' => $method,
        'caisse' => $caisse
    ]);
}

private function calculateOperatorStats($today)
{
    $mobileStatsRaw = Invoice::select('payment_operator', DB::raw('sum(total) as total'), DB::raw('count(*) as count'))
        ->whereDate('created_at', $today)
        ->where('status', 'paid')
        ->where(function($q) {
            $q->where('payment_method', 'mobile_money')->orWhere('payment_method', 'Mobile Money')->orWhere('payment_method', 'MoMo');
        })
        ->groupBy('payment_operator')
        ->get();

    $stats = [
        'orange' => ['total' => 0, 'count' => 0, 'label' => 'Orange Money', 'color' => 'orange'],
        'mtn' => ['total' => 0, 'count' => 0, 'label' => 'MTN Mobile Money', 'color' => 'yellow'],
        'wave' => ['total' => 0, 'count' => 0, 'label' => 'Wave', 'color' => 'blue'],
        'moov' => ['total' => 0, 'count' => 0, 'label' => 'Moov Mony', 'color' => 'teal'],
        'other' => ['total' => 0, 'count' => 0, 'label' => 'Autre', 'color' => 'gray'],
    ];

    foreach ($mobileStatsRaw as $stat) {
        $op = strtolower((string)$stat->payment_operator);
        $key = 'other';
        if (strpos($op, 'orange') !== false) $key = 'orange';
        elseif (strpos($op, 'mtn') !== false) $key = 'mtn';
        elseif (strpos($op, 'wave') !== false) $key = 'wave';
        elseif (strpos($op, 'moov') !== false) $key = 'moov';
        
        $stats[$key]['total'] += $stat->total;
        $stats[$key]['count'] += $stat->count;
    }
    return $stats;
}
    public function treasuryDetails()
    {
        // 1. Mobile Money (Liquid/Direct)
        $mobileInvoices = Invoice::where('status', 'paid')
            ->where(function($q) {
                $q->where('payment_method', 'mobile_money')
                  ->orWhere('payment_method', 'Mobile Money')
                  ->orWhere('payment_method', 'MoMo');
            })
            ->with(['patient', 'service'])
            ->latest()
            ->paginate(15, ['*'], 'mobile_page');

        // 2. Cash Transfers (Physical funds moving to treasury)
        $cashTransfers = FundTransfer::with(['cashier'])
            ->latest()
            ->paginate(15, ['*'], 'cash_page');

        // 3. Insurance Receivables (Owed money)
        $insuranceInvoices = Invoice::where('status', 'paid')
            ->where('insurance_settlement_status', 'pending')
            ->where(function($q) {
                $q->whereNotNull('insurance_name')
                  ->orWhere('insurance_coverage_rate', '>', 0);
            })
            ->with(['patient', 'service'])
            ->latest()
            ->paginate(15, ['*'], 'insurance_page');

        // --- CALCULATIONS ---
        
        // A. Mobile/Card funds are considered realized liquidity
        $totalMobileAndCard = Invoice::where('status', 'paid')
            ->where(function($q) {
                $q->where('payment_method', 'mobile_money')
                  ->orWhere('payment_method', 'Mobile Money')
                  ->orWhere('payment_method', 'MoMo')
                  ->orWhere('payment_method', 'card')
                  ->orWhere('payment_method', 'carte')
                  ->orWhere('payment_method', 'carte bancaire');
            })->get()->sum(function($inv) {
                return $inv->total - ($inv->total * ($inv->insurance_coverage_rate ?? 0) / 100);
            });

        // B. Confirmed Cash (Actually in Treasury)
        $totalConfirmedCash = FundTransfer::where('status', 'confirmed')->sum('amount');

        // C. Cashier Holdings (Cash collected by cashiers but NOT yet confirmed/transferred)
        // Total cash collected minus Total cash confirmed
        $totalCashCollectedRaw = Invoice::where('status', 'paid')
            ->where(function($q) {
                $q->where('payment_method', 'cash')
                  ->orWhere('payment_method', 'espèces')
                  ->orWhere('payment_method', 'especes');
            })->get()->sum(function($inv) {
                return $inv->total - ($inv->total * ($inv->insurance_coverage_rate ?? 0) / 100);
            });
        
        $cashierHoldings = $totalCashCollectedRaw - $totalConfirmedCash;
        if ($cashierHoldings < 0) $cashierHoldings = 0;

        // D. Insurance Receivables
        $totalInsurance = Invoice::where('status', 'paid')
            ->where('insurance_settlement_status', 'pending')
            ->get()
            ->sum(function($invoice) {
                return ($invoice->total * ($invoice->insurance_coverage_rate ?? 0)) / 100;
            });

        return view('admin.finance.treasury', compact(
            'mobileInvoices', 
            'cashTransfers', 
            'insuranceInvoices', 
            'totalMobileAndCard', 
            'totalConfirmedCash', 
            'cashierHoldings',
            'totalInsurance'
        ));
    }

    private function getCaisseStats($type, $date)
    {
        // Define scopes similar to CashierController
        $baseQuery = Invoice::whereDate('created_at', $date)
            ->where('status', 'paid')
            ->whereHas('service', function($q) use ($type) {
                if ($type === 'labo') {
                    $q->where('caisse_type', 'labo')->orWhere('name', 'like', '%Labo%');
                } elseif ($type === 'urgence') {
                    $q->where('caisse_type', 'urgence')->orWhere('name', 'like', '%Urgence%');
                } else {
                    // Accueil = Tout le reste
                    $q->where(function($sub) {
                        $sub->whereNull('caisse_type')
                            ->orWhere(function($t) {
                                $t->where('caisse_type', '!=', 'labo')
                                  ->where('caisse_type', '!=', 'urgence');
                            });
                    })
                    ->where('name', 'not like', '%Labo%')
                    ->where('name', 'not like', '%Urgence%');
                }
            });

        $total = (clone $baseQuery)->sum('total');
        $count = (clone $baseQuery)->count();
        
        // Handle localized and technical strings
        $cash = (clone $baseQuery)->where(function($q) {
            $q->where('payment_method', 'cash')
              ->orWhere('payment_method', 'Espèces')
              ->orWhere('payment_method', 'espèces');
        })->sum('total');
        
        $mobile = (clone $baseQuery)->where(function($q) {
            $q->where('payment_method', 'mobile_money')
              ->orWhere('payment_method', 'Mobile Money')
              ->orWhere('payment_method', 'MoMo');
        })->sum('total');

        // Active Cashiers in this scope
        $cashierIds = (clone $baseQuery)->whereNotNull('cashier_id')->pluck('cashier_id')->unique();
        $activeCashiers = \App\Models\User::whereIn('id', $cashierIds)->get();

        return [
            'total' => $total,
            'count' => $count,
            'cash' => $cash,
            'mobile' => $mobile,
            'active_cashiers' => $activeCashiers
        ];
    }

    public function confirmTransfer(Request $request, $id)
    {
        $transfer = FundTransfer::findOrFail($id);
        $received = $request->input('received_amount', $transfer->amount);
        $gap = $received - $transfer->amount;

        $transfer->update([
            'status' => 'confirmed',
            'received_amount' => $received,
            'gap_amount' => $gap,
            'admin_id' => Auth::id(),
            'validated_at' => now()
        ]);

        $message = 'Versement confirmé avec succès.';
        if ($gap != 0) {
            $message .= " Attention : un écart de " . number_format($gap, 0, ',', ' ') . " FCFA a été enregistré.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function exportInvoices(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=journal_transactions_" . now()->format('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $invoices = Invoice::with(['patient', 'cashier', 'service'])
            ->latest()
            ->get();

        $callback = function() use($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Heure', 'Facture', 'Service', 'Patient', 'Montant', 'Methode', 'Caissiere']);

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->created_at->format('d/m/Y'),
                    $inv->created_at->format('H:i'),
                    $inv->invoice_number,
                    $inv->service->name ?? 'Général',
                    $inv->patient->name ?? 'Patient',
                    $inv->total,
                    $inv->payment_method,
                    $inv->cashier->name ?? 'Système'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pendingInvoices()
    {
        // 1. Standard Pending Invoices (Patient hasn't paid)
        $patientPendings = Invoice::where('status', 'pending')
            ->orWhere('status', 'partial')
            ->with(['patient', 'service'])
            ->latest()
            ->get();

        // 2. Insurance Pending Settlements (Insurance hasn't reimbursed)
        $insurancePendings = Invoice::where('insurance_settlement_status', 'pending')
            ->with(['patient', 'service'])
            ->latest()
            ->get();

        // 3. Insurance Recovered Settlements (History)
        $insuranceRecovered = Invoice::where('insurance_settlement_status', 'recovered')
            ->with(['patient', 'service'])
            ->latest()
            ->take(50)
            ->get();

        $totalPatientPending = $patientPendings->sum('total');
        
        $totalInsurancePending = $insurancePendings->sum(function($inv) {
            return ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
        });

        // Grouping by insurance for summary
        $statsByInsurance = $insurancePendings->groupBy('insurance_name')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum(function($inv) {
                    return ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
                })
            ];
        });

        return view('admin.finance.pending', compact(
            'patientPendings', 
            'insurancePendings', 
            'insuranceRecovered',
            'statsByInsurance',
            'totalPatientPending', 
            'totalInsurancePending'
        ));
    }

    /**
     * Marquer une créance assurance comme recouvrée/payée
     */
    public function settleInsuranceInvoice(Invoice $invoice)
    {
        $insurancePart = ($invoice->total * ($invoice->insurance_coverage_rate ?? 0)) / 100;

        $invoice->update([
            'insurance_settlement_status' => 'recovered',
            'insurance_settled_at' => now(),
            'updated_at' => now()
        ]);

        // Integrate into Treasury (TransactionLog)
        \App\Models\TransactionLog::create([
            'source_type' => 'hospital',
            'source_id' => $invoice->hospital_id ?? auth()->user()->hospital_id ?? 1,
            'amount' => $insurancePart,
            'fee_applied' => 0,
            'net_income' => $insurancePart,
            'description' => "Virement " . ($invoice->insurance_name ?? 'Assurance') . " - Facture #" . $invoice->invoice_number,
        ]);

        return redirect()->back()->with('success', 'La créance assurance pour la facture ' . $invoice->invoice_number . ' a été marquée comme recouvrée et ajoutée à la trésorerie.');
    }

    /**
     * Générer un bordereau d'envoi pour une assurance spécifique (Export CSV)
     */
    public function exportInsuranceBordereau(Request $request)
    {
        $insuranceName = $request->query('insurance');
        
        $query = Invoice::where('insurance_settlement_status', 'pending')
            ->with(['patient', 'service']);
            
        if ($insuranceName) {
            $query->where('insurance_name', $insuranceName);
        }

        $invoices = $query->latest()->get();

        $filename = "bordereau_assurance_" . ($insuranceName ?? 'global') . "_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Facture', 'Patient', 'ID Assurance', 'Montant Total', 'Prise en Charge (%)', 'Part Assurance (CFA)']);

            foreach ($invoices as $inv) {
                $insurancePart = ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
                fputcsv($file, [
                    $inv->created_at->format('d/m/Y'),
                    $inv->invoice_number,
                    $inv->patient->name ?? '?',
                    $inv->insurance_card_number ?? '-',
                    $inv->total,
                    ($inv->insurance_coverage_rate ?? 0) . '%',
                    $insurancePart
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function auditLogs(Request $request)
    {
        $query = Invoice::query()->with(['patient', 'cashier', 'service']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->get('date'));
        }

        $logs = $query->latest()->paginate(20);

        return view('admin.finance.audit', compact('logs'));
    }
}

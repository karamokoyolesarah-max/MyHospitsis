<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Insurance\InsuranceService;
use App\Models\InsuranceProvider;
use App\Models\InsuranceVerificationLog;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsuranceController extends Controller
{
    protected $insuranceService;

    public function __construct(InsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'simulator');
        $now = Carbon::now();

        // Data for Simulator
        $providerName = $this->insuranceService->getProviderName();
        $endpoints = config('insurance.endpoints');

        // Data for Connectors
        $connectors = InsuranceProvider::latest()->get();

        // Data for Recovery
        $pendingInvoices = Invoice::whereNotNull('insurance_name')
            ->where('insurance_settlement_status', 'pending')
            ->with('patient')
            ->latest()
            ->paginate(10, ['*'], 'recovery_page');

        // History of Received Payments
        $receivedPayments = Invoice::whereNotNull('insurance_name')
            ->where('insurance_settlement_status', 'recovered')
            ->with('patient')
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        // Data for Audit & Stats
        $logs = InsuranceVerificationLog::with('hospital')
            ->latest()
            ->take(20)
            ->get();
            
        $fraudAlertsCount = InsuranceVerificationLog::where('status', 'expire')->count();

        // Summary Stats (Month)
        $totalPaidMonth = Invoice::whereNotNull('insurance_name')
            ->where('insurance_settlement_status', 'recovered')
            ->whereMonth('insurance_settled_at', $now->month)
            ->whereYear('insurance_settled_at', $now->year)
            ->get()
            ->sum(function($inv) {
                return ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
            });

        $totalPending = Invoice::whereNotNull('insurance_name')
            ->where('insurance_settlement_status', 'pending')
            ->get()
            ->sum(function($inv) {
                return ($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100;
            });

        $stats = Invoice::whereNotNull('insurance_name')
            ->select('insurance_name', DB::raw('count(*) as count'), DB::raw('sum(total) as total_amount'))
            ->groupBy('insurance_name')
            ->get();

        return view('admin.insurance.index', compact(
            'providerName', 
            'endpoints', 
            'connectors', 
            'pendingInvoices', 
            'receivedPayments',
            'logs', 
            'fraudAlertsCount',
            'stats',
            'activeTab',
            'totalPaidMonth',
            'totalPending'
        ));
    }

    public function testVerification(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string',
        ]);

        $result = $this->insuranceService->verify($request->matricule);

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('admin.insurance.index', ['tab' => 'simulator'])
            ->with('insurance_result', $result);
    }

    public function storeConnector(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'provider_type' => 'required|string',
            'api_key' => 'nullable|string',
            'base_url' => 'nullable|url',
        ]);

        InsuranceProvider::create([
            'hospital_id' => auth()->user()->hospital_id ?? 1,
            'name' => $data['name'],
            'provider_type' => $data['provider_type'],
            'api_key' => $data['api_key'],
            'base_url' => $data['base_url'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.insurance.index', ['tab' => 'connectors'])
            ->with('success', 'Connecteur d\'assurance ajouté avec succès.');
    }
}

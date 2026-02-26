<?php

namespace App\Http\Controllers;

use App\Models\{Medication, PharmacyStock, PharmacyStockLog, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:pharmacist,admin');
    }

    public function dashboard()
    {
        $hospital_id = auth()->user()->hospital_id;
        $stocks = PharmacyStock::where('hospital_id', $hospital_id)
            ->with('medication')
            ->get();

        // 1. Urgences : Déjà périmés ou Stock à Zéro
        $alerts_urgent = $stocks->filter(function($stock) {
            return ($stock->expiry_date && $stock->expiry_date->isPast()) || $stock->quantity <= 0;
        });

        // 2. Vigilance : Périme sous 90 j ou Stock Bas
        $alerts_vigilance = $stocks->filter(function($stock) use ($alerts_urgent) {
            // Exclure ceux déjà dans urgent
            if ($alerts_urgent->contains($stock->id)) return false;
            
            $isExpiringSoon = $stock->expiry_date && $stock->expiry_date->diffInDays(now()) <= 90;
            $isLowStock = $stock->quantity <= $stock->min_threshold;
            
            return $isExpiringSoon || $isLowStock;
        });

        // 3. Derniers Arrivages (15 derniers jours)
        $recentArrivals = PharmacyStockLog::where('hospital_id', $hospital_id)
            ->where('type', 'entry')
            ->where('created_at', '>=', now()->subDays(15))
            ->with('stock.medication')
            ->latest()
            ->take(5)
            ->get();

        // Statistiques pour les cartes
        $stats = [
            'total_items' => $stocks->count(),
            'total_units' => $stocks->sum('quantity'),
            'expired_count' => $stocks->filter(fn($s) => $s->expiry_date && $s->expiry_date->isPast())->count(),
            'soon_expired_count' => $stocks->filter(fn($s) => $s->expiry_date && !$s->expiry_date->isPast() && $s->expiry_date->diffInDays(now()) <= 90)->count(),
        ];

        return view('pharmacy.dashboard', compact('stocks', 'alerts_urgent', 'alerts_vigilance', 'recentArrivals', 'stats'));
    }

    public function medsCatalog(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;
        $query = Medication::query()->where('is_active', true);
        // Join with hospital-specific stock to allow filtering by stock status
        $query->leftJoin('pharmacy_stocks', function($join) use ($hospital_id) {
            $join->on('medications.id', '=', 'pharmacy_stocks.medication_id')
                 ->where('pharmacy_stocks.hospital_id', '=', $hospital_id);
        })
        ->select('medications.*', 'pharmacy_stocks.quantity', 'pharmacy_stocks.expiry_date', 'pharmacy_stocks.batch_number');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('medications.name', 'like', "%{$search}%")
                  ->orWhere('medications.brand_name', 'like', "%{$search}%")
                  ->orWhere('medications.active_ingredient', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('medications.category', $request->category);
        }

        if ($request->filled('form')) {
            $query->where('medications.form', $request->form);
        }

        // Advanced Filters
        if ($request->filter === 'expired') {
            $query->whereNotNull('pharmacy_stocks.expiry_date')
                  ->where('pharmacy_stocks.expiry_date', '<', now());
        } elseif ($request->filter === 'out_of_stock') {
            $query->where(function($q) {
                $q->whereNull('pharmacy_stocks.quantity')
                  ->orWhere('pharmacy_stocks.quantity', '<=', 0);
            });
        }

        $medications = $query->orderBy('medications.name')->get();

        // On récupère les catégories et formes distinctes pour les filtres
        $categories = Medication::where('is_active', true)->whereNotNull('category')->distinct()->pluck('category');
        $forms = Medication::where('is_active', true)->whereNotNull('form')->distinct()->pluck('form');

        return view('pharmacy.catalog', compact('medications', 'categories', 'forms'));
    }

    public function addMedication(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'therapeutic_class' => 'nullable|string|max:255',
            'form' => 'nullable|string',
            'dosage' => 'nullable|string',
            'category' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        Medication::create($validated);
        return back()->with('success', 'Médicament ajouté au catalogue général.');
    }

    public function updateMedication(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'therapeutic_class' => 'nullable|string|max:255',
            'form' => 'nullable|string',
            'dosage' => 'nullable|string',
            'category' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $medication->update($validated);
        return back()->with('success', 'Médicament mis à jour avec succès.');
    }

    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:entry,exit,adjustment,expired',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $hospital_id = auth()->user()->hospital_id;
            
            $stock = PharmacyStock::firstOrCreate(
                ['hospital_id' => $hospital_id, 'medication_id' => $validated['medication_id']],
                ['quantity' => 0]
            );

            if ($validated['type'] === 'exit' && $stock->quantity < abs($validated['quantity'])) {
                return back()->withErrors(['quantity' => 'Stock insuffisant pour cette sortie.']);
            }

            $oldQuantity = $stock->quantity;
            $movement = ($validated['type'] === 'exit' || $validated['type'] === 'expired') ? -abs($validated['quantity']) : abs($validated['quantity']);
            
            $stock->quantity += $movement;
            if ($validated['batch_number']) $stock->batch_number = $validated['batch_number'];
            if ($validated['expiry_date']) $stock->expiry_date = $validated['expiry_date'];
            $stock->save();

            PharmacyStockLog::create([
                'hospital_id' => $hospital_id,
                'pharmacy_stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'quantity' => $movement,
                'type' => $validated['type'],
                'reason' => $validated['reason'],
            ]);

            AuditLog::log('update', 'PharmacyStock', $stock->id, [
                'type' => $validated['type'],
                'quantity' => $movement,
                'new_total' => $stock->quantity,
            ]);

            DB::commit();
            return back()->with('success', 'Stock mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du stock : ' . $e->getMessage()]);
        }
    }

    public function movementHistory(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;
        $query = PharmacyStockLog::where('hospital_id', $hospital_id)
            ->with(['stock.medication', 'user'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('medication_id')) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('medication_id', $request->medication_id);
            });
        }

        $logs = $query->paginate(20);
        $medications = Medication::where('is_active', true)->orderBy('name')->get();

        return view('pharmacy.history', compact('logs', 'medications'));
    }
}

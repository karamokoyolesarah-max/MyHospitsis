@extends('layouts.admin_finance')

@section('title', 'Tableau de Bord Financier')

@section('finance_content')

{{-- 1. KPI CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Recettes du Jour -->
    <a href="{{ route('admin.finance.daily') }}" class="group relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all border border-gray-100">
        <div class="absolute right-0 top-0 p-4 opacity-5">
            <i class="fas fa-coins text-8xl text-indigo-600 group-hover:scale-110 transition-transform"></i>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Recettes du Jour</p>
            <h3 class="text-3xl font-black text-gray-900 mb-2">{{ number_format($revenueToday, 0, ',', ' ') }} <span class="text-sm text-gray-400 font-bold">FCFA</span></h3>
            
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="{{ $growth >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-md">
                    <i class="fas fa-chart-line mr-1"></i> {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                </span>
                <span class="text-gray-400">vs hier</span>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    </a>

    <!-- Mobile Money (Day) -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
        <div class="absolute right-0 top-0 p-4 opacity-5">
            <i class="fas fa-mobile-alt text-8xl text-orange-500"></i>
        </div>
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Mobile Money (Auj.)</p>
        <h3 class="text-3xl font-black text-gray-900 mb-2">{{ number_format($totalMobileToday, 0, ',', ' ') }} <span class="text-sm text-gray-400 font-bold">FCFA</span></h3>
        <div class="flex items-center gap-2 text-xs font-bold">
            <span class="{{ $momoReconciliationStatus === 'balanced' ? 'text-emerald-600 bg-emerald-50' : 'text-orange-600 bg-orange-50' }} px-2 py-1 rounded-md flex items-center gap-1">
                <i class="fas {{ $momoReconciliationStatus === 'balanced' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                {{ $momoReconciliationStatus === 'balanced' ? 'Réconcilié' : 'Vérification requise' }}
            </span>
        </div>
    </div>

    <!-- Recettes du Mois -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="absolute right-0 top-0 p-4 opacity-5">
            <i class="fas fa-calendar-check text-8xl text-blue-500"></i>
        </div>
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Recettes du Mois</p>
        <h3 class="text-3xl font-black text-gray-900 mb-2">{{ number_format($revenueMonth, 0, ',', ' ') }} <span class="text-sm text-gray-400 font-bold">FCFA</span></h3>
        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
            <div class="bg-blue-500 h-1.5 rounded-full" style="width: 65%"></div> {{-- Placeholder progress --}}
        </div>
    </div>

    <!-- Impayés -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="absolute right-0 top-0 p-4 opacity-5">
            <i class="fas fa-file-invoice text-8xl text-red-500"></i>
        </div>
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Reste à Recouvrer</p>
        <h3 class="text-3xl font-black text-red-600 mb-2">{{ number_format($pendingRevenue, 0, ',', ' ') }} <span class="text-sm text-red-400 font-bold">FCFA</span></h3>
        <p class="text-[10px] text-gray-400 font-bold mt-1">Factures en attente de règlement</p>
    </div>
</div>

{{-- 2. ACTIONS REQUISES (Pending Transfers) --}}
@if($pendingTransfers->count() > 0)
<div class="mb-8 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-lg border-l-4 border-l-amber-500 overflow-hidden">
        <div class="p-4 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 p-2 rounded-lg text-amber-600">
                    <i class="fas fa-bell fa-lg animate-swing"></i>
                </div>
                <div>
                    <h3 class="font-black text-gray-900 text-sm uppercase tracking-wide">Versements en Attente de Validation</h3>
                    <p class="text-xs text-amber-700 font-medium">Ces versements nécessitent votre comptage physique.</p>
                </div>
            </div>
            <span class="bg-amber-500 text-white px-3 py-1 rounded-lg text-xs font-black">
                {{ $pendingTransfers->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-wider">Date & Heure</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-wider">Caissière</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Montant Déclaré</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingTransfers as $transfer)
                    <tr class="hover:bg-amber-50/30 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-gray-600">{{ $transfer->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600">{{ substr($transfer->cashier->name ?? '?', 0, 1) }}</span>
                                <span class="text-xs font-black text-gray-800">{{ $transfer->cashier->name ?? 'Inconnu' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-gray-900">{{ number_format($transfer->amount, 0, ',', ' ') }} F</span>
                        </td>
                        <td class="px-6 py-4 text-center" x-data="{ validating: false, received: {{ $transfer->amount }} }">
                             <button @click="validating = true" x-show="!validating" class="bg-gray-900 text-white hover:bg-black text-[10px] font-black uppercase px-4 py-2 rounded-lg transition-colors shadow-lg shadow-gray-200">
                                Valider
                            </button>
                            
                            <div x-show="validating" class="flex flex-col items-center gap-2">
                                <div class="flex gap-1">
                                    <input type="number" x-model="received" class="w-24 px-2 py-1 bg-white border border-gray-300 rounded text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                                    <form action="{{ route('admin.finance.confirm', $transfer->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="received_amount" :value="received">
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded text-xs shadow-md"><i class="fas fa-check"></i></button>
                                    </form>
                                    <button @click="validating = false" class="bg-gray-200 hover:bg-gray-300 text-gray-600 p-1.5 rounded text-xs"><i class="fas fa-times"></i></button>
                                </div>
                                <p class="text-[9px] font-bold text-red-500" x-show="received != {{ $transfer->amount }}">Écart: <span x-text="(received - {{ $transfer->amount }}).toLocaleString()"></span> F</p>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    {{-- 3. FLUX PAR CAISSE (Detailed Cards) --}}
    <div class="xl:col-span-2 space-y-6">
        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-store-alt text-gray-400"></i> Flux par Point d'Encaissement
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
             @foreach(['accueil' => 'Accueil', 'labo' => 'Laboratoire', 'urgence' => 'Urgences'] as $key => $label)
                @php 
                    $stats = $caisseStats[$key]; 
                    $color = match($key) { 'accueil' => 'indigo', 'labo' => 'teal', 'urgence' => 'rose', default => 'gray' };
                @endphp
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col h-full relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $label }}</span>
                            <h4 class="text-xl font-black text-gray-900 mt-1">{{ number_format($stats['total'], 0, ',', ' ') }} <small class="text-[10px]">F</small></h4>
                        </div>
                        <div class="bg-{{ $color }}-50 text-{{ $color }}-600 p-2 rounded-lg">
                            <i class="fas {{ $key == 'labo' ? 'fa-flask' : ($key == 'urgence' ? 'fa-ambulance' : 'fa-user-md') }}"></i>
                        </div>
                    </div>

                    {{-- Mini Progress --}}
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3 flex overflow-hidden">
                        @if($stats['total'] > 0)
                            <div class="bg-{{ $color }}-500 h-1.5" style="width: {{ ($stats['cash'] / $stats['total']) * 100 }}%"></div>
                            <div class="bg-orange-400 h-1.5" style="width: {{ ($stats['mobile'] / $stats['total']) * 100 }}%"></div>
                        @endif
                    </div>

                    <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-4">
                        <span title="Espèces"><i class="fas fa-money-bill-wave text-{{ $color }}-500 mr-1"></i> {{ number_format($stats['cash'], 0, ',', ' ') }}</span>
                        <span title="Mobile"><i class="fas fa-mobile text-orange-400 mr-1"></i> {{ number_format($stats['mobile'], 0, ',', ' ') }}</span>
                    </div>

                    <div class="mt-auto border-t border-gray-50 pt-3">
                        <p class="text-[9px] font-black text-gray-300 uppercase mb-2">Caissiers</p>
                        <div class="flex flex-wrap gap-1">
                            @forelse($stats['active_cashiers'] as $cashier)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-50 border border-gray-100 text-[10px] font-bold text-gray-600" title="{{ $cashier->name }}">
                                    {{ substr($cashier->name, 0, 10) }}..
                                </span>
                            @empty
                                <span class="text-[10px] text-gray-300 italic">Aucun</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- LATEST TRANSACTIONS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-list text-gray-400"></i> Dernières Factures Patients
                </h3>
                <a href="{{ route('admin.finance.export') }}" class="text-[10px] font-black uppercase text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fas fa-download mr-1"></i> Exporter Tout
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Heure</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Info</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Patient</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Montant</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Via</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($latestInvoices as $inv)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-5 py-3 text-[10px] font-bold text-gray-500 font-mono">{{ $inv->created_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="block text-[10px] font-black text-gray-700 uppercase">{{ $inv->service->name ?? 'Général' }}</span>
                                    <span class="block text-[9px] text-gray-400 font-bold">#{{ $inv->invoice_number }}</span>
                                </td>
                                <td class="px-5 py-3 text-xs font-bold text-gray-800">{{ $inv->patient->name ?? 'Anonyme' }}</td>
                                <td class="px-5 py-3 text-right text-xs font-black text-gray-900">{{ number_format($inv->total, 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3 text-center">
                                    @if(Str::contains(strtolower($inv->payment_method), ['mobile', 'momo']))
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-mobile-alt text-orange-500" title="{{ $inv->payment_method }}"></i>
                                            @if($inv->payment_operator)
                                                <span class="text-[8px] font-bold text-gray-400 uppercase mt-0.5">{{ $inv->payment_operator }}</span>
                                            @endif
                                        </div>
                                    @elseif(Str::contains(strtolower($inv->payment_method), ['assurance']))
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-id-card text-purple-500" title="{{ $inv->payment_method }}"></i>
                                            @if($inv->payment_operator)
                                                <span class="text-[8px] font-bold text-gray-400 uppercase mt-0.5">{{ $inv->payment_operator }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <i class="fas fa-money-bill-wave text-emerald-500" title="{{ $inv->payment_method }}"></i>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-gray-400 text-xs italic">Aucune donnée récente</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TREASURY MOVEMENTS (Logs) --}}
        <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden mt-8">
            <div class="p-5 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-vault text-blue-400"></i> Mouvements de Trésorerie
                </h3>
                <span class="text-[9px] font-black bg-blue-500/20 text-blue-400 px-2 py-1 rounded">Récent</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($latestTransactions as $log)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4 text-[10px] font-bold text-gray-500">{{ $log->created_at?->format('d/m H:i') ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-xs font-black text-white">{{ $log->description }}</p>
                                    <p class="text-[9px] text-gray-500 uppercase font-bold">{{ $log->source_type }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="text-sm font-black text-emerald-400">+ {{ number_format($log->amount, 0, ',', ' ') }} F</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-gray-500 text-xs italic">Aucun mouvement de trésorerie</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="space-y-6">
        
        {{-- PAYMENT METHODS CHART --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Répartition Paiements</h3>
            <div class="relative h-48 w-full">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <div class="flex items-center text-[10px] font-bold text-gray-600">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span> Espèces
                </div>
                <div class="flex items-center text-[10px] font-bold text-gray-600">
                    <span class="w-3 h-3 rounded-full bg-orange-500 mr-2"></span> Mobile
                </div>
            </div>
        </div>

        {{-- REVENUE BY SERVICE --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
             <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Top Services</h3>
             <div class="space-y-4">
                @foreach($revenueByService->take(5) as $service)
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-xs font-bold text-gray-700">{{ $service->name }}</span>
                            <span class="text-xs font-black text-gray-900">{{ number_format($service->total, 0, ',', ' ') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            @php $pct = $revenueToday > 0 ? ($service->total / $revenueToday) * 100 : 0; @endphp
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
             </div>
        </div>

        {{-- UNPAID INVOICES MINI LIST --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 flex justify-between">
                <span>Impayés Récents</span>
                <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded text-[10px]">{{ $unpaidInvoices->count() }}</span>
            </h3>
            <div class="space-y-3">
                @forelse($unpaidInvoices as $inv)
                    <div class="flex items-center justify-between p-3 bg-red-50/50 rounded-xl border border-red-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-red-500 shadow-sm text-xs font-black">
                                !
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $inv->patient->name ?? 'Patient' }}</p>
                                <p class="text-[10px] text-gray-500">{{ $inv->created_at?->diffForHumans() ?? '-' }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-red-600">{{ number_format($inv->total, 0, ',', ' ') }} F</span>
                    </div>
                @empty
                    <p class="text-[10px] text-gray-400 italic text-center py-4">Aucun impayé récent</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paymentMethodsChart');
        if(!ctx) return;

        const dataArr = @json($revenueByMethod);
        
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'Mobile'],
                datasets: [{
                    data: [dataArr.cash || 0, dataArr.mobile || 0],
                    backgroundColor: ['#10B981', '#F97316'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%',
            }
        });
    });
</script>
@endpush

@endsection

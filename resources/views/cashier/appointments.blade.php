@extends('layouts.cashier_layout')

@section('content')
<div class="p-4 sm:p-8 bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-gray-800 tracking-tight">Gestion des Encaissement</h2>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-1">Gérer et valider les paiements des rendez-vous</p>
        </div>
        <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date Contextuelle</p>
                <p class="text-sm font-black text-gray-800">{{ now()->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- BARRE HORIZONTALE DE FILTRE (DATE) --}}
    <div class="flex flex-wrap gap-2 mb-8 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm w-fit">
        @php
            $filters = [
                'yesterday' => ['label' => 'Hier', 'icon' => 'fa-history'],
                'today' => ['label' => "Aujourd'hui", 'icon' => 'fa-clock'],
                'tomorrow' => ['label' => 'Demain', 'icon' => 'fa-calendar-day'],
                'this_week' => ['label' => 'Cette Semaine', 'icon' => 'fa-calendar-week'],
            ];
        @endphp

        @foreach($filters as $key => $filter)
            <a href="{{ route('cashier.appointments.index', ['date_filter' => $key]) }}" 
               class="px-6 py-3 rounded-xl text-xs font-black transition-all flex items-center {{ $dateFilter === $key ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50' }}">
                <i class="fas {{ $filter['icon'] }} mr-2"></i>
                {{ $filter['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Filtres de statut & Recherche --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8 items-center">
        <div class="md:col-span-8 flex flex-wrap gap-2">
            <button onclick="filterStatus('all')" class="status-btn px-6 py-3 bg-gray-900 text-white rounded-xl font-black text-xs transition-all shadow-md active-filter">Tous les statuts</button>
            <button onclick="filterStatus('pending')" class="status-btn px-6 py-3 bg-white text-gray-500 border border-gray-100 rounded-xl font-black text-xs hover:bg-gray-50 transition-all">En attente</button>
            <button onclick="filterStatus('paid')" class="status-btn px-6 py-3 bg-white text-gray-500 border border-gray-100 rounded-xl font-black text-xs hover:bg-gray-50 transition-all">Paiements validés</button>
        </div>
        <div class="md:col-span-4 relative">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Rechercher patient..." 
                   class="w-full pl-14 pr-6 py-3 bg-white border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm text-gray-600">
        </div>
    </div>

    {{-- Liste des RDV --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left min-w-[1000px]" id="appointmentsTable">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Heure</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Service & Actes</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Date Prévue</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Montant Total</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Méthode</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($appointments as $apt)
                        @php 
                            $montantPrestations = $apt->prestations->sum('pivot.total');
                            $totalGeneral = (optional($apt->service)->price ?? 0) + $montantPrestations;
                            $paidInvoice = $apt->invoices->firstWhere('status', 'paid');
                            $isPaid = $apt->status == 'paid' || $paidInvoice;
                            $displayInvoice = $paidInvoice ?? $apt->invoices->first();
                            $method = $paidInvoice ? $paidInvoice->payment_method : '-';
                        @endphp
                        <tr class="appointment-row hover:bg-gray-50/50 transition-all" data-status="{{ $isPaid ? 'paid' : ($apt->status == 'cancelled' ? 'cancelled' : 'pending') }}">
                            <td class="px-8 py-5 text-sm font-black text-gray-900">
                                {{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('H:i') }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-lg shadow-indigo-100">
                                        {{ strtoupper(substr($apt->patient?->name ?? 'XX', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-800 text-sm">{{ $apt->patient?->name ?? 'Patient Supprimé' }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 italic">IPU: {{ $apt->patient?->ipu ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-black uppercase tracking-tighter">{{ $apt->service?->name ?? 'Service' }}</span>
                                @foreach($apt->prestations as $prestation)
                                    <div class="text-[10px] text-blue-500 font-bold uppercase mt-1">
                                        + {{ $prestation->name }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-8 py-5 font-bold text-gray-700 text-center text-sm">
                                {{ \Carbon\Carbon::parse($apt->appointment_datetime)->format('d/m/Y') }}
                            </td>
                            <td class="px-8 py-5 text-right font-black text-gray-900 text-lg">
                                {{ number_format($totalGeneral, 0, ',', ' ') }} <small class="text-[10px] text-gray-400 italic font-bold">FCFA</small>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($isPaid)
                                    @php
                                        $isMobile = str_contains(strtolower($method), 'mobile') || str_contains(strtolower($method), 'api');
                                    @endphp
                                    <span class="px-2 py-1 {{ $isMobile ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} border rounded text-[9px] font-black uppercase">
                                        {{ $method }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($isPaid)
                                    <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest">Payé</span>
                                @elseif($apt->status == 'cancelled')
                                    <span class="px-3 py-1.5 rounded-xl bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest">Annulé</span>
                                @else
                                    <span class="px-3 py-1.5 rounded-xl bg-orange-100 text-orange-700 text-[10px] font-black uppercase tracking-widest">En attente</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                @if(!$isPaid && $apt->status != 'cancelled')
                                    <button onclick="openPaymentModal({{ $apt->id }}, '{{ addslashes($apt->patient->name) }}', {{ $totalGeneral }}, '{{ $apt->payment_type }}')"
                                            class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                        Encaisser
                                    </button>
                                @elseif($isPaid && $displayInvoice)
                                    <a href="{{ route('cashier.invoices.show', $displayInvoice->id) }}"
                                       class="bg-gray-100 text-gray-600 px-5 py-2.5 rounded-xl font-black text-xs hover:bg-gray-200 transition-all inline-flex items-center gap-2">
                                        <i class="fas fa-file-invoice"></i> Facture
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-gray-200 text-6xl mb-4"></i>
                                    <p class="text-gray-400 font-bold">Aucun rendez-vous trouvé pour cette période</p>
                                    <p class="text-xs text-gray-300 mt-1">Utilisez les filtres en haut pour changer la date</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("searchInput").value.toUpperCase();
    let rows = document.querySelectorAll(".appointment-row");
    
    rows.forEach(row => {
        let text = row.innerText.toUpperCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

function filterStatus(status) {
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.classList.remove('bg-gray-900', 'text-white', 'shadow-md', 'active-filter');
        btn.classList.add('bg-white', 'text-gray-500', 'border', 'border-gray-100');
    });
    
    event.currentTarget.classList.add('bg-gray-900', 'text-white', 'shadow-md', 'active-filter');
    event.currentTarget.classList.remove('bg-white', 'text-gray-500', 'border', 'border-gray-100');

    let rows = document.querySelectorAll(".appointment-row");
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = "";
        } else {
            row.style.display = row.getAttribute('data-status') === status ? "" : "none";
        }
    });
}
</script>

@include('cashier.partials.payment_modal')
@endsection
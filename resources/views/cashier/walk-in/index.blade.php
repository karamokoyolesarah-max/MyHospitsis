@extends('layouts.cashier_layout')

@section('title', 'Consultations Sans Rendez-vous')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Consultations Sans Rendez-vous</h1>
            <p class="text-gray-500 mt-1">Gérez les admissions directes et les encaissements rapides.</p>
        </div>
        <button type="button" 
            onclick="document.getElementById('createWalkInModal').showModal()"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md transform hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Nouvelle Consultation
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm flex items-start" role="alert">
            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
            <div>
                <p class="font-bold text-green-700">Succès</p>
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm" role="alert">
            <div class="flex">
                 <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                 <div>
                    <h3 class="text-red-800 font-bold">Erreur</h3>
                    <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                 </div>
            </div>
        </div>
    @endif

    <!-- Stats Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center transform transition hover:scale-[1.02]">
            <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Aujourd'hui</p>
                <p class="text-2xl font-bold text-gray-800">{{ $walkInConsultations->total() + $pendingLabRequests->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center transform transition hover:scale-[1.02]">
             <div class="p-4 rounded-full bg-orange-50 text-orange-600 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">En Attente de Paiement</p>
                @php 
                    $unpaidWalk = $walkInConsultations->where('status', 'pending_payment')->count();
                    $unpaidLab = $pendingLabRequests->where('is_paid', false)->count();
                @endphp
                <p class="text-2xl font-bold text-gray-800">{{ $unpaidWalk + $unpaidLab }}</p>
            </div>
        </div>
         <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center transform transition hover:scale-[1.02]">
             <div class="p-4 rounded-full bg-green-50 text-green-600 mr-4">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Payés & Validés</p>
                @php 
                    $paidWalk = $walkInConsultations->where('status', 'paid')->count();
                    $paidLab = $pendingLabRequests->where('is_paid', true)->count();
                @endphp
                <p class="text-2xl font-bold text-gray-800">{{ $paidWalk + $paidLab }}</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div class="flex items-center gap-4">
               <h2 class="text-lg font-semibold text-gray-800">Dossiers et File d'attente</h2>
               <!-- Filter Dropdown -->
               <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="filter-menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        @php
                            $currentFilter = request('filter', 'today');
                            $filterLabels = [
                                'today' => "Aujourd'hui",
                                'yesterday' => 'Hier',
                                'week' => 'Cette semaine',
                                'month' => 'Ce mois',
                                'all' => 'Tout voir'
                            ];
                        @endphp
                        {{ $filterLabels[$currentFilter] ?? "Aujourd'hui" }}
                        <i class="fas fa-chevron-down ml-2 -mr-1"></i>
                    </button>
    
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-30" role="menu" aria-orientation="vertical" aria-labelledby="filter-menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            <a href="{{ route('cashier.walk-in.index', ['filter' => 'today']) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-50 hover:text-blue-600 transition-colors" role="menuitem">Aujourd'hui</a>
                            <a href="{{ route('cashier.walk-in.index', ['filter' => 'yesterday']) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-50 hover:text-blue-600 transition-colors" role="menuitem">Hier</a>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('cashier.walk-in.index', ['filter' => 'week']) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-50 hover:text-blue-600 transition-colors" role="menuitem">Cette semaine</a>
                            <a href="{{ route('cashier.walk-in.index', ['filter' => 'month']) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-50 hover:text-blue-600 transition-colors" role="menuitem">Ce mois</a>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('cashier.walk-in.index', ['filter' => 'all']) }}" class="text-gray-500 block px-4 py-2 text-xs uppercase font-black hover:bg-gray-50 hover:text-gray-700 transition-colors" role="menuitem">Tout voir</a>
                        </div>
                    </div>
               </div>
            </div>
            <!-- Search could go here -->
        </div>

        @if($walkInConsultations->count() > 0 || $pendingLabRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="py-4 px-6 font-semibold border-b border-gray-100">Patient</th>
                            <th class="py-4 px-6 font-semibold border-b border-gray-100">Service Demandé</th>
                            <th class="py-4 px-6 font-semibold border-b border-gray-100">Montant Total</th>
                            <th class="py-4 px-6 font-semibold border-b border-gray-100">Horodatage</th>
                            <th class="py-4 px-6 font-semibold border-b border-gray-100">Statut</th>
                            <th class="py-4 px-6 font-semibold border-b border-gray-100 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm font-light divide-y divide-gray-100">
                        {{-- Lab Requests First (Prioritize lab alerts) --}}
                        @foreach($pendingLabRequests as $lab)
                            <tr class="hover:bg-blue-50/50 bg-blue-50/10 transition-colors duration-200">
                                <td class="py-4 px-6 border-l-4 {{ $lab->test_category === 'imagerie' ? 'border-purple-500' : 'border-blue-500' }}">
                                    <div class="flex items-center">
                                        <div class="rounded-full h-10 w-10 flex items-center justify-center font-bold mr-3 shadow-sm text-xs {{ $lab->test_category === 'imagerie' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $lab->test_category === 'imagerie' ? 'RX' : 'LB' }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 block">{{ $lab->patient_name }}</span>
                                            @php
                                                $labelText = $lab->test_category === 'imagerie' ? 'Imagerie Médicale' : 'Analyse Labo';
                                                $labelBg = $lab->test_category === 'imagerie' ? 'bg-purple-100' : 'bg-blue-100';
                                                $labelColor = $lab->test_category === 'imagerie' ? 'text-purple-600' : 'text-blue-600';
                                            @endphp
                                            <span class="text-[10px] {{ $labelColor }} {{ $labelBg }} px-1.5 py-0.5 rounded font-black uppercase">{{ $labelText }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-gray-900 font-medium">{{ $lab->test_name }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $prestation = \App\Models\Prestation::where('name', $lab->test_name)->first();
                                        $labTotal = $prestation ? $prestation->price : 5000;
                                    @endphp
                                    <span class="font-bold text-gray-900">{{ number_format($labTotal, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium">{{ $lab->created_at->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500 flex items-center mt-0.5">
                                            <i class="far fa-clock mr-1"></i> {{ $lab->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                @if($lab->is_paid)
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-tighter shadow-sm border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Payé
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-black rounded-full uppercase tracking-tighter shadow-sm border border-orange-200">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(!$lab->is_paid)
                                    <button onclick="openPaymentModal({{ $lab->id }}, 'lab_request')" 
                                            class="inline-flex items-center px-4 py-2 bg-white border border-blue-600 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-cash-register mr-2"></i> Encaisser
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 italic">Prêt pour Labo</span>
                                @endif
                            </td>
                            </tr>
                        @endforeach

                        @foreach($walkInConsultations as $consultation)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full h-10 w-10 flex items-center justify-center font-bold mr-3 shadow-md text-sm">
                                            {{ substr($consultation->patient->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900 block">{{ $consultation->patient->name }}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs text-gray-500 flex items-center">
                                                    <i class="fas fa-phone-alt text-[10px] mr-1"></i> {{ $consultation->patient->phone }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium">{{ $consultation->service->name }}</span>
                                        @if($consultation->prestations->isNotEmpty())
                                            <span class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full w-fit mt-1">
                                                + {{ $consultation->prestations->count() }} prestation(s)
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $servicePrice = $consultation->service->price ?? 0;
                                        $prestationsTotal = $consultation->prestations->sum('pivot.total');
                                        $total = $servicePrice + $prestationsTotal;
                                    @endphp
                                    <span class="font-bold text-gray-900">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-medium">{{ $consultation->consultation_datetime->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500 flex items-center mt-0.5">
                                            <i class="far fa-clock mr-1"></i> {{ $consultation->consultation_datetime->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($consultation->status === 'pending_payment')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                                            En attente
                                        </div>
                                    @elseif($consultation->status === 'paid')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1.5"></i> Payé
                                        </div>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold shadow-sm border border-gray-200">
                                            {{ ucfirst($consultation->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($consultation->status === 'pending_payment')
                                        <button 
                                            onclick="openPaymentModal('{{ $consultation->id }}', 'walk-in')"
                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-green-500 text-green-600 rounded-lg hover:bg-green-50 hover:shadow-md transition-all duration-200 font-semibold text-xs group">
                                            <i class="fas fa-cash-register mr-1.5 group-hover:rotate-12 transition-transform"></i> Encaisser
                                        </button>
                                    @else
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors p-2 rounded-full hover:bg-blue-50">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $walkInConsultations->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="bg-gray-50 rounded-full p-8 mb-6 shadow-inner">
                     <i class="fas fa-clipboard-list fa-4x text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune consultation</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">La liste des consultations sans rendez-vous est vide pour le moment. Commencez par créer une nouvelle consultation.</p>
                <button 
                     onclick="document.getElementById('createWalkInModal').showModal()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-plus mr-2"></i> Nouvelle Consultation
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal Création (Dialog HTML5 + Tailwind) -->
<dialog id="createWalkInModal" class="modal bg-transparent p-0 w-full h-full max-w-full max-h-full overflow-hidden backdrop:bg-gray-900/60 backdrop:backdrop-blur-sm z-50 fixed inset-0">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <form action="{{ route('cashier.walk-in.store') }}" method="POST" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all border border-gray-100 flex flex-col">
            @csrf
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 px-8 py-5 flex justify-between items-center sticky top-0 z-20 shadow-md flex-shrink-0">
                <div>
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-plus mr-3 text-blue-200"></i> Nouvelle Admission Sans RDV
                    </h3>
                    <p class="text-blue-200 text-xs mt-1 ml-8">Enregistrement rapide d'un patient externe</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="submit" class="md:hidden px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-lg shadow-sm transition">
                        <i class="fas fa-check mr-1"></i> Valider
                    </button>
                    <button type="button" onclick="document.getElementById('createWalkInModal').close()" class="text-white/70 hover:text-white transition rounded-lg p-2 hover:bg-white/10">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8">
                <!-- Section Information Patient -->
                <div class="mb-8 p-6 bg-white rounded-xl border border-gray-200 shadow-sm relative overflow-hidden group hover:border-blue-300 transition-colors">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <h4 class="text-gray-800 font-bold mb-6 flex items-center text-lg">
                        <span class="bg-blue-100 text-blue-700 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm font-bold shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-colors">1</span> 
                        Informations du Patient
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-11">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nom complet <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="patient_name" required 
                                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition shadow-sm h-11"
                                    placeholder="Nom Prénom" value="{{ old('patient_name') }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" name="patient_phone" required 
                                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition shadow-sm h-11"
                                    placeholder="Numéro de téléphone" value="{{ old('patient_phone') }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Âge (Ans) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-birthday-cake text-gray-400"></i>
                                </div>
                                <input type="number" name="patient_age" required min="0" max="120"
                                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition shadow-sm h-11"
                                    placeholder="Âge du patient" value="{{ old('patient_age') }}">
                            </div>
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-gray-400 font-normal text-xs">(Optionnel)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="patient_email" 
                                    class="w-full rounded-lg border-gray-300 pl-10 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition shadow-sm h-11"
                                    placeholder="email@exemple.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Services & Prestations -->
                <div class="mb-2 p-6 bg-indigo-50/30 rounded-xl border border-indigo-100 shadow-sm relative overflow-hidden group hover:border-indigo-300 transition-colors">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <h4 class="text-gray-800 font-bold mb-6 flex items-center text-lg">
                         <span class="bg-indigo-100 text-indigo-700 w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm font-bold shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-colors">2</span>
                        Consultation & Prestations
                    </h4>
                    
                    <div class="pl-11">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Service (Département) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select id="serviceSelect" name="service_id" required onchange="filterConsultationTypes()"
                                            class="appearance-none w-full rounded-xl border-gray-300 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition shadow-sm h-14 pl-5 pr-10 text-base font-medium">
                                            <option value="">-- Sélectionner le département --</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Type de Consultation <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select id="consultationTypeSelect" name="consultation_prestation_id" required disabled
                                            class="appearance-none w-full rounded-xl border-gray-300 bg-gray-100 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition shadow-sm h-14 pl-5 pr-10 text-base font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                                            <option value="">-- D'abord sélectionner un service --</option>
                                            @foreach($prestations as $prestation)
                                                <option value="{{ $prestation->id }}" data-service-id="{{ $prestation->service_id }}" data-price="{{ $prestation->price }}" class="prestation-option hidden">
                                                    {{ $prestation->name }} — {{ number_format($prestation->price, 0, ',', ' ') }} FCFA
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> La sélection du type de consultation déterminera le prix de base.</p>

                        <div class="space-y-4 mt-6">
                             <div class="flex items-center justify-between">
                                 <label class="block text-sm font-bold text-gray-800">Mode de Paiement <span class="text-red-500">*</span></label>
                             </div>
                             
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_mode" value="cash" class="peer sr-only" checked onchange="toggleMobileMoneyFields()">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-800 hover:border-green-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-money-bill-wave mb-2 text-2xl text-gray-400 group-hover:text-green-500 peer-checked:text-green-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Espèces</span>
                                        <span class="text-xs text-gray-500 peer-checked:text-green-600">Paiement immédiat</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-green-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                                
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_mode" value="mobile_money" class="peer sr-only" onchange="toggleMobileMoneyFields()">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-orange-50 peer-checked:border-orange-500 peer-checked:text-orange-800 hover:border-orange-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-mobile-alt mb-2 text-2xl text-gray-400 group-hover:text-orange-500 peer-checked:text-orange-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Mobile Money</span>
                                        <span class="text-xs text-gray-500 peer-checked:text-orange-600">MTN / Orange / Moov</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_mode" value="assurance" class="peer sr-only" onchange="toggleMobileMoneyFields()">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-800 hover:border-purple-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-id-card mb-2 text-2xl text-gray-400 group-hover:text-purple-500 peer-checked:text-purple-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Assurance</span>
                                        <span class="text-xs text-gray-500 peer-checked:text-purple-600">Tiers Payant</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                             </div>
                             
                             <!-- Mobile Money Fields (Hidden by default) -->
                             </div>
                             
                             <!-- Mobile Money Fields (Hidden by default) -->
                             <div id="mobileMoneyFields" class="hidden mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div>
                                         <label class="block text-sm font-semibold text-gray-800 mb-2">Opérateur Mobile <span class="text-red-500">*</span></label>
                                         <select name="mobile_operator" id="mobileOperator" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                             <option value="">-- Choisir --</option>
                                             <option value="wave">Wave</option>
                                             <option value="mtn">MTN Mobile Money</option>
                                             <option value="orange">Orange Money</option>
                                             <option value="moov">Moov Money</option>
                                         </select>
                                     </div>
                                     <div>
                                         <label class="block text-sm font-semibold text-gray-800 mb-2">Numéro Mobile Money <span class="text-red-500">*</span></label>
                                         <input type="tel" name="mobile_number" id="mobileNumber" placeholder="Ex: 0701234567" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                 </div>
                             </div>
                             
                             <!-- QR Code Display -->
                             <div id="qrCodeDisplay" class="hidden mt-4 p-5 bg-white rounded-2xl border-2 border-orange-400 shadow-lg">
                                 <div class="text-center">
                                     <p class="text-sm font-black text-orange-700 uppercase tracking-wide mb-3 flex items-center justify-center">
                                         <i class="fas fa-qrcode mr-2"></i> Scannez ce QR Code pour payer
                                     </p>
                                     <div id="qrCodeImage" class="flex justify-center mb-4 bg-gray-50 p-4 rounded-xl">
                                         <!-- QR Code sera affiché ici -->
                                     </div>
                                     <div class="bg-orange-50 p-3 rounded-lg">
                                         <p class="text-xs text-gray-600">Numéro de réception :</p>
                                         <p id="operatorNumber" class="font-bold text-lg text-orange-700"></p>
                                     </div>
                                 </div>
                             </div>

                             <!-- Payment Reference Field -->
                             <div id="paymentReferenceField" class="hidden mt-4">
                                 <label class="block text-sm font-bold text-gray-800 mb-2">
                                     Référence de Paiement <span class="text-red-500">*</span>
                                 </label>
                                 <input type="text" name="payment_reference" id="paymentReference"
                                        placeholder="Ex: MP240212123456"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-mono font-bold uppercase text-center text-lg tracking-wider">
                                 <p class="text-xs text-orange-700 mt-2 flex items-start bg-orange-50 p-3 rounded-lg">
                                     <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                     <span><strong>Important :</strong> Demandez au patient la référence de paiement qu'il a reçue après avoir payé via Mobile Money</span>
                                 </p>
                             </div>
                             
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-0">
                                 <div style="display: none;">
                                     <input type="tel" name="mobile_number_hidden" id="mobileNumberHidden" placeholder="Ex: 0701234567" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                     </div>
                                 </div>
                                 <p class="text-xs text-orange-700 mt-2 flex items-start">
                                     <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                     <span>Le patient recevra une notification de paiement sur son téléphone. La consultation sera enregistrée après confirmation du paiement.</span>
                                 </p>
                             </div>

                             <!-- Insurance Fields (Hidden by default) -->
                             <div id="insuranceFields" class="hidden mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                                 <div class="space-y-4">
                                     <div>
                                         <label class="block text-sm font-semibold text-gray-800 mb-2">Compagnie d'Assurance <span class="text-red-500">*</span></label>
                                         <input type="text" name="insurance_name" id="insuranceName" placeholder="Ex: MCI, SUNU, NSIA..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11 uppercase">
                                     </div>
                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                         <div>
                                             <label class="block text-sm font-semibold text-gray-800 mb-2">N° Carte / Matricule <span class="text-red-500">*</span></label>
                                             <input type="text" name="insurance_card_number" id="insuranceCardNumber" placeholder="N° Carte" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                         </div>
                                         <div>
                                             <label class="block text-sm font-semibold text-gray-800 mb-2">Taux de Couverture (%) <span class="text-red-500">*</span></label>
                                             <input type="number" name="insurance_coverage_rate" id="insuranceCoverageRate" min="0" max="100" placeholder="Ex: 80" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <!-- Co-payment Display -->
                                 <div id="coPaymentDisplay" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                                     <div class="flex justify-between items-center">
                                         <div>
                                             <p class="text-xs font-bold text-red-600 uppercase">Reste à payer (Ticket Modérateur)</p>
                                             <p class="text-sm text-red-500">Montant à encaisser maintenant</p>
                                         </div>
                                         <div class="text-right">
                                             <p class="text-2xl font-black text-red-700" id="patientPartDisplay">0 FCFA</p>
                                             <input type="hidden" name="patient_part" id="patientPartInput">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </div>

                        <div class="space-y-4 mt-6">
                             <div class="flex items-center justify-between">
                                 <label class="block text-sm font-bold text-gray-800">Prestations complémentaires</label>
                                 <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-semibold">Optionnel</span>
                             </div>
                             
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-64 overflow-y-auto p-1 custom-scrollbar">
                                @foreach($prestations as $prestation)
                                    <label class="relative flex items-center p-4 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:shadow-md transition-all group select-none">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="prestation_ids[]" value="{{ $prestation->id }}" data-price="{{ $prestation->price }}" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition">
                                        </div>
                                        <div class="ml-3 flex-1 flex flex-col">
                                            <span class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $prestation->name }}</span>
                                            <span class="text-xs text-gray-500 font-medium">{{ number_format($prestation->price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </label>
                                @endforeach
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-5 flex items-center justify-end space-x-4 border-t border-gray-200 sticky bottom-0 z-30 flex-shrink-0">
                <button type="button" onclick="document.getElementById('createWalkInModal').close()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium shadow-sm transition">
                    Annuler
                </button>
                <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 font-bold shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> Enregistrer et Encaisser
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Payment Modal (Hidden form for logic, Dialog for UI) -->
<dialog id="paymentModal" class="modal bg-transparent p-0 w-full h-full max-w-full max-h-full overflow-hidden backdrop:bg-gray-900/70 backdrop:backdrop-blur-sm z-50 fixed inset-0">
     <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center text-white shadow-md flex-shrink-0">
                <h3 class="font-bold text-lg flex items-center"><i class="fas fa-cash-register mr-3 text-green-400"></i> Encaissement</h3>
                 <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-white transition bg-gray-800 hover:bg-gray-700 rounded-lg p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="overflow-y-auto flex-1">
                <div class="p-6">
                    <div id="paymentDetailsContent" class="mb-8">
                        <div class="flex flex-col items-center justify-center py-8 space-y-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                            <p class="text-gray-500 font-medium">Chargement des détails...</p>
                        </div>
                    </div>
                    
                    <form id="actualPaymentForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-800 mb-3 px-1">Choisir le moyen de paiement</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_method" value="Espèces" class="peer sr-only" checked onchange="toggleMobileOperators(false); toggleInsuranceOperators(false)">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-800 hover:border-green-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-money-bill-wave mb-2 text-2xl text-gray-400 group-hover:text-green-500 peer-checked:text-green-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Espèces</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-green-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_method" value="Carte bancaire" class="peer sr-only" onchange="toggleMobileOperators(false); toggleInsuranceOperators(false)">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-800 hover:border-blue-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-credit-card mb-2 text-2xl text-gray-400 group-hover:text-blue-500 peer-checked:text-blue-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Carte</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                                 <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_method" value="Mobile Money" class="peer sr-only" onchange="toggleMobileOperators(true); toggleInsuranceOperators(false)">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-orange-50 peer-checked:border-orange-500 peer-checked:text-orange-800 hover:border-green-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-mobile-alt mb-2 text-2xl text-gray-400 group-hover:text-orange-500 peer-checked:text-orange-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Mobile</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                                 <label class="cursor-pointer group relative">
                                    <input type="radio" name="payment_method" value="Assurance" class="peer sr-only" onchange="toggleMobileOperators(false); toggleInsuranceOperators(true)">
                                    <div class="p-4 text-center border-2 border-gray-200 rounded-xl peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-800 hover:border-purple-200 transition-all flex flex-col items-center justify-center h-24 shadow-sm">
                                        <i class="fas fa-id-card mb-2 text-2xl text-gray-400 group-hover:text-purple-500 peer-checked:text-purple-600 transition-colors"></i>
                                        <span class="font-bold text-sm">Assurance</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i class="fas fa-check text-[8px]"></i>
                                    </div>
                                </label>
                            </div>
                        </div>


                        <!-- Insurance Payment Details -->
                        <div id="paymentInsuranceDetails" class="hidden mb-6 p-5 bg-purple-50 rounded-2xl border border-purple-200 animate-fadeIn">
                             <div class="space-y-4">
                                     <div>
                                         <label class="block text-sm font-semibold text-gray-800 mb-2">Compagnie d'Assurance <span class="text-red-500">*</span></label>
                                         <input type="text" id="paymentInsuranceName" name="insurance_name" placeholder="Ex: MCI, SUNU, NSIA..." class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11 uppercase">
                                     </div>
                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                         <div>
                                             <label class="block text-sm font-semibold text-gray-800 mb-2">N° Carte / Matricule <span class="text-red-500">*</span></label>
                                             <input type="text" id="paymentInsuranceCard" name="insurance_card_number" placeholder="N° Carte" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                         </div>
                                         <div>
                                             <label class="block text-sm font-semibold text-gray-800 mb-2">Taux (%) <span class="text-red-500">*</span></label>
                                             <input type="number" id="paymentInsuranceRate" name="insurance_coverage_rate" min="0" max="100" placeholder="Ex: 80" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition shadow-sm h-11">
                                         </div>
                                     </div>
                                 </div>
                        </div>

                        <!-- Mobile Payment Details (MTN, Orange, Moov, Wave) -->
                        <div id="paymentMobileDetails" class="hidden mb-6 p-5 bg-orange-50 rounded-2xl border border-orange-200 animate-fadeIn">
                            <label class="block text-xs font-black text-orange-700 uppercase tracking-widest mb-3">Détails Mobile Money</label>
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all has-[:checked]:border-orange-500 has-[:checked]:ring-2 has-[:checked]:ring-orange-200">
                                    <input type="radio" name="mobile_operator" value="wave" class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-500">
                                    <span class="text-xs font-bold text-gray-700">Wave</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all has-[:checked]:border-orange-500 has-[:checked]:ring-2 has-[:checked]:ring-orange-200">
                                    <input type="radio" name="mobile_operator" value="orange" class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-500">
                                    <span class="text-xs font-bold text-gray-700">Orange</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all has-[:checked]:border-orange-500 has-[:checked]:ring-2 has-[:checked]:ring-orange-200">
                                    <input type="radio" name="mobile_operator" value="mtn" class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-500">
                                    <span class="text-xs font-bold text-gray-700">MTN</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all has-[:checked]:border-orange-500 has-[:checked]:ring-2 has-[:checked]:ring-orange-200">
                                    <input type="radio" name="mobile_operator" value="moov" class="w-4 h-4 text-orange-500 border-gray-300 focus:ring-orange-500">
                                    <span class="text-xs font-bold text-gray-700">Moov</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-orange-600 uppercase mb-1 ml-1">Numéro de téléphone</label>
                                <input type="text" name="mobile_number" id="paymentMobileNumber" placeholder="Ex: 0708091011" 
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none text-sm font-bold transition-all">
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4 border-t border-gray-100">
                             <button type="button" onclick="closePaymentModal()" class="flex-1 py-3.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                                Annuler
                            </button>
                            <button type="submit" id="actualSubmitBtn" class="flex-[2] py-3.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-lg hover:shadow-green-500/30 transition transform active:scale-95 flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Valider le Paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
     </div>
</dialog>

<script>
    function toggleMobileOperators(show) {
        const details = document.getElementById('paymentMobileDetails');
        const numberInput = document.getElementById('paymentMobileNumber');
        const operators = details.querySelectorAll('input[type="radio"]');
        
        if (show) {
            details.classList.remove('hidden');
            numberInput.required = true;
            operators.forEach(op => op.required = true);
        } else {
            details.classList.add('hidden');
            numberInput.required = false;
            operators.forEach(op => op.required = false);
        }
    }

    function toggleInsuranceOperators(show) {
        const details = document.getElementById('paymentInsuranceDetails');
        const insuranceInputs = details.querySelectorAll('input');
        
        if (show) {
            details.classList.remove('hidden');
            insuranceInputs.forEach(input => input.required = true);
        } else {
            details.classList.add('hidden');
            insuranceInputs.forEach(input => input.required = false);
        }
    }

    function toggleMobileMoneyFields() {
        const paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;
        const mobileMoneyFields = document.getElementById('mobileMoneyFields');
        const mobileOperator = document.getElementById('mobileOperator');
        const mobileNumber = document.getElementById('mobileNumber');
        
        const insuranceFields = document.getElementById('insuranceFields');
        const insuranceName = document.getElementById('insuranceName');
        const insuranceCardNumber = document.getElementById('insuranceCardNumber');
        const insuranceCoverageRate = document.getElementById('insuranceCoverageRate');
        const coPaymentDisplay = document.getElementById('coPaymentDisplay');
        
        // Reset everything first
        mobileMoneyFields.classList.add('hidden');
        mobileOperator.required = false;
        mobileNumber.required = false;
        
        insuranceFields.classList.add('hidden');
        insuranceName.required = false;
        insuranceCardNumber.required = false;
        insuranceCoverageRate.required = false;
        coPaymentDisplay.classList.add('hidden');

        if (paymentMode === 'mobile_money') {
            mobileMoneyFields.classList.remove('hidden');
            mobileOperator.required = true;
            mobileNumber.required = true;
        } else if (paymentMode === 'assurance') {
            insuranceFields.classList.remove('hidden');
            insuranceName.required = true;
            insuranceCardNumber.required = true;
            insuranceCoverageRate.required = true;
            
            // Add listeners for calculation
            insuranceCoverageRate.addEventListener('input', calculateCoPayment);
            document.getElementById('serviceSelect').addEventListener('change', calculateCoPayment);
            document.getElementById('consultationTypeSelect').addEventListener('change', calculateCoPayment);
            document.querySelectorAll('input[name="prestation_ids[]"]').forEach(cb => {
                cb.addEventListener('change', calculateCoPayment);
            });
        }
    }

    function calculateCoPayment() {
        const rateInput = document.getElementById('insuranceCoverageRate');
        const rate = parseInt(rateInput.value) || 0;
        const coPaymentDisplay = document.getElementById('coPaymentDisplay');
        const patientPartDisplay = document.getElementById('patientPartDisplay');
        
        if (rate >= 100 || rate < 0) {
            coPaymentDisplay.classList.add('hidden');
            return;
        }

        // Calculate Total Amount
        let total = 0;
        
        // Consultation Price
        const consultationSelect = document.getElementById('consultationTypeSelect');
        const selectedOption = consultationSelect.options[consultationSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            total += parseInt(selectedOption.dataset.price);
        }

        // Extra Prestations
        document.querySelectorAll('input[name="prestation_ids[]"]:checked').forEach(cb => {
            // Find the price label or data attribute? 
            // In the HTML we rendered the price in text. Let's assume we need to parse or store it.
            // Simplified: We need the price. 
            // Better to add data-price to the checkbox inputs in a separate edit.
            // For now, let's try to grab it from the sibling span text.
            // Structure: input -> div -> div -> input | div -> span -> span (price)
            // Actually, let's just make sure we add data-price to checkboxes first.
        });
        
        // For now, let's implement parsing logic if data-price is missing, but best to add it.
        // Assuming checkboxes will have data-price added.
        document.querySelectorAll('input[name="prestation_ids[]"]:checked').forEach(cb => {
             total += parseInt(cb.dataset.price || 0);
        });

        if (total > 0) {
            const insurancePart = Math.round(total * (rate / 100));
            const patientPart = total - insurancePart;
            
            patientPartDisplay.textContent = new Intl.NumberFormat('fr-FR').format(patientPart) + ' FCFA';
            coPaymentDisplay.classList.remove('hidden');
        } else {
             coPaymentDisplay.classList.add('hidden');
        }
    }

    // Auto-reopen modal if validation errors exist
    @if($errors->any())
    window.onload = function() {
        const modal = document.getElementById('createWalkInModal');
        if (modal) {
            modal.showModal();
            // Also trigger filter if service was selected
            const serviceSelect = document.getElementById('serviceSelect');
            if (serviceSelect && serviceSelect.value) {
                filterConsultationTypes();
            }
        }
    };
    @endif

    function filterConsultationTypes() {
        const serviceId = document.getElementById('serviceSelect').value;
        const typeSelect = document.getElementById('consultationTypeSelect');
        const options = typeSelect.querySelectorAll('.prestation-option');
        
        // Reset selection
        typeSelect.value = "";
        
        if (!serviceId) {
            typeSelect.disabled = true;
            typeSelect.classList.add('bg-gray-100');
            typeSelect.classList.remove('bg-white');
            return;
        }

        let hasOptions = false;

        options.forEach(option => {
            const optionServiceId = option.getAttribute('data-service-id');
            // Assuming loose match if service_id matches OR if it's a generic consultation? 
            // For now, strict match on data-service-id which comes from DB
            if (optionServiceId == serviceId) {
                option.classList.remove('hidden');
                hasOptions = true;
            } else {
                option.classList.add('hidden');
            }
        });

        if (hasOptions) {
            typeSelect.disabled = false;
            typeSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            typeSelect.classList.add('bg-white');
             // Remove "D'abord sélectionner" option text roughly or change it
             typeSelect.options[0].text = "-- Sélectionner le type --";
        } else {
            typeSelect.disabled = true;
            typeSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            typeSelect.classList.remove('bg-white');
            typeSelect.options[0].text = "-- Aucune prestation pour ce service --";
        }
    }

    function openPaymentModal(id, type = 'walk-in') {
        // Show modal immediately with loading state
        const modal = document.getElementById('paymentModal');
        const content = document.getElementById('paymentDetailsContent');
        const form = document.getElementById('actualPaymentForm');
        
        modal.showModal();
        
        // Reset content to loading
        content.innerHTML = '<div class="flex flex-col items-center justify-center py-8 space-y-4"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div><p class="text-gray-500 font-medium">Chargement des détails...</p></div>';
        
        // Determine URLs based on type
        let fetchUrl = '';
        let actionUrl = '';
        
        if (type === 'lab_request') {
            fetchUrl = '/cashier/lab-requests/' + id + '/details';
            actionUrl = '/cashier/lab-requests/' + id + '/pay';
        } else {
            fetchUrl = '/cashier/walk-in/' + id + '/details';
            actionUrl = '/cashier/walk-in/' + id + '/validate-payment';
        }

        // Fetch details
        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                content.innerHTML = html;
                form.action = actionUrl;
                
                // Auto-fill insurance details if present
                const storedName = document.getElementById('storedInsuranceName');
                if (storedName) {
                    // Force select "Espèces" (Cash) as default for co-payment
                    const cashRadio = document.querySelector('input[name="payment_method"][value="Espèces"]');
                    const assuranceRadio = document.querySelector('input[name="payment_method"][value="Assurance"]');
                    
                    if (cashRadio) {
                        cashRadio.checked = true;
                        cashRadio.dispatchEvent(new Event('change'));
                        
                        // Disable Assurance to prevent re-entering insurance details
                        if (assuranceRadio) {
                             const label = assuranceRadio.closest('label');
                             if (label) {
                                 label.style.opacity = '0.5';
                                 label.style.pointerEvents = 'none';
                                 label.title = "Assurance déjà enregistrée";
                             }
                             assuranceRadio.disabled = true;
                        }

                        // Auto-fill hidden source fields for calculation
                        const nameInput = document.getElementById('paymentInsuranceName');
                        const cardInput = document.getElementById('paymentInsuranceCard');
                        const rateInput = document.getElementById('paymentInsuranceRate');

                        if(nameInput) nameInput.value = storedName.value;
                        if(cardInput) cardInput.value = document.getElementById('storedInsuranceCard').value;
                        if(rateInput) rateInput.value = document.getElementById('storedInsuranceRate').value;
                        
                        // Trigger calculation to show co-payment breakdown
                        calculateModalCoPayment();
                    }
                }
            })
            .catch(error => {
                content.innerHTML = '<div class="bg-red-50 p-4 rounded-lg text-red-600 text-center border border-red-200"><i class="fas fa-exclamation-triangle mr-2"></i> Erreur de chargement des détails. Réessayez.</div>';
                console.error(error);
            });
    }

    function calculateModalCoPayment() {
        const rateInput = document.getElementById('paymentInsuranceRate');
        const modalFullTotalEl = document.getElementById('modalFullTotal');
        
        if (!rateInput || !modalFullTotalEl) return;

        const rate = parseInt(rateInput.value) || 0;
        const fullTotal = parseInt(modalFullTotalEl.dataset.value) || 0;
        
        const fullTotalRow = document.getElementById('fullTotalRow');
        const breakdown = document.getElementById('coPaymentBreakdown');
        
        const insurancePartDisplay = document.getElementById('modalInsurancePart');
        const patientPartDisplay = document.getElementById('modalPatientPart');
        const coverageRateDisplay = document.getElementById('modalCoverageRate');

        if (rate > 0 && rate <= 100) {
            const insurancePart = Math.round(fullTotal * (rate / 100));
            const patientPart = fullTotal - insurancePart;

            if (breakdown) breakdown.classList.remove('hidden');
            if (fullTotalRow) fullTotalRow.classList.add('hidden'); // Hide the standard total row

            if (insurancePartDisplay) insurancePartDisplay.textContent = new Intl.NumberFormat('fr-FR').format(insurancePart) + ' FCFA';
            if (patientPartDisplay) patientPartDisplay.textContent = new Intl.NumberFormat('fr-FR').format(patientPart) + ' FCFA';
            if (coverageRateDisplay) coverageRateDisplay.textContent = rate;
        } else {
            if (breakdown) breakdown.classList.add('hidden');
            if (fullTotalRow) fullTotalRow.classList.remove('hidden');
        }
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').close();
    }

    document.getElementById('actualPaymentForm').onsubmit = function() {
        const btn = document.getElementById('actualSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Traitement...';
    };

    // Fonction pour afficher le QR Code selon l'opérateur sélectionné
    function toggleMobileMoneyFields() {
        const mmFields = document.getElementById('mobileMoneyFields');
        const insuranceFields = document.getElementById('insuranceFields');
        const paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;
        const mmInputs = mmFields.querySelectorAll('select, input');
        const insuranceInputs = insuranceFields.querySelectorAll('input');

        // Reset
        mmFields.classList.add('hidden');
        insuranceFields.classList.add('hidden');
        mmInputs.forEach(input => input.required = false);
        insuranceInputs.forEach(input => input.required = false);
        document.getElementById('paymentReference').required = false;

        if (paymentMode === 'mobile_money') {
            mmFields.classList.remove('hidden');
            document.getElementById('mobileOperator').required = true;
            document.getElementById('mobileNumber').required = true;
            // Note: paymentReference required est géré par showQRCode
        } else if (paymentMode === 'assurance') {
            insuranceFields.classList.remove('hidden');
            insuranceInputs.forEach(input => input.required = true);
        }
    }

    function showQRCode(operator) {
        console.log('Changement opérateur:', operator); // Debug
        const qrDisplay = document.getElementById('qrCodeDisplay');
        const qrImage = document.getElementById('qrCodeImage');
        const operatorNumber = document.getElementById('operatorNumber');
        const refField = document.getElementById('paymentReferenceField');
        
        // Données de l'hôpital (QR Codes et numéros) - Utilisation de auth()->user() pour être sûr
        const hospitalData = {
            qr_orange: '{{ auth()->user()->hospital->payment_qr_orange ?? "" }}',
            qr_mtn: '{{ auth()->user()->hospital->payment_qr_mtn ?? "" }}',
            qr_moov: '{{ auth()->user()->hospital->payment_qr_moov ?? "" }}',
            qr_wave: '{{ auth()->user()->hospital->payment_qr_wave ?? "" }}',
            number_orange: '{{ auth()->user()->hospital->payment_orange_number ?? "" }}',
            number_mtn: '{{ auth()->user()->hospital->payment_mtn_number ?? "" }}',
            number_moov: '{{ auth()->user()->hospital->payment_moov_number ?? "" }}',
            number_wave: '{{ auth()->user()->hospital->payment_wave_number ?? "" }}'
        };
        
        console.log('Données Hôpital:', hospitalData); // Debug

        
        if (operator && hospitalData['qr_' + operator]) {
            // Afficher le QR Code
            qrImage.innerHTML = `<img src="/storage/${hospitalData['qr_' + operator]}" 
                                      alt="QR ${operator.toUpperCase()}" 
                                      class="w-56 h-56 object-contain border-4 border-orange-200 rounded-2xl shadow-lg bg-white p-2">`;
            operatorNumber.textContent = hospitalData['number_' + operator] || 'Non configuré';
            qrDisplay.classList.remove('hidden');
            refField.classList.remove('hidden');
            
            // Rendre le champ référence obligatoire
            document.getElementById('paymentReference').required = true;
        } else {
            qrDisplay.classList.add('hidden');
            refField.classList.add('hidden');
            document.getElementById('paymentReference').required = false;
        }
    }

    // Écouter les changements d'opérateur Mobile Money
    document.addEventListener('DOMContentLoaded', function() {
        const mobileOperatorSelect = document.getElementById('mobileOperator');
        if (mobileOperatorSelect) {
            mobileOperatorSelect.addEventListener('change', function() {
                showQRCode(this.value);
            });
        }
    });
</script>
@endsection

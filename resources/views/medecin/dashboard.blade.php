@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc]"> 
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-6">
                    <div class="h-20 w-20 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg flex items-center justify-center text-white ring-4 ring-blue-50">
                        <span class="text-3xl font-black">
                            {{ strtoupper(substr($user->name ?? $medecin->name ?? 'DR', 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dr. {{ $user->name ?? $medecin->name ?? 'Médecin' }}</h1>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-bold">
                                ⚕️ {{ $user->service->name ?? $medecin->service->name ?? ($user->specialite ?? $medecin->specialite ?? 'Spécialiste Externe') }}
                            </span>
                            <span class="text-gray-400 font-medium flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ now()->isoFormat('dddd D MMMM') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <div class="text-right">
                        <div class="text-2xl font-black text-gray-800">{{ now()->format('H:i') }}</div>
                        <p class="text-xs font-bold text-green-600 uppercase tracking-widest">En service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-all cursor-pointer group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Mes Patients</p>
                        <p class="text-5xl font-black text-gray-900">{{ $hospitalizedPatients->count() }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-2xl group-hover:bg-blue-600 transition-colors">
                        <svg class="w-8 h-8 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">En attente</p>
                        <p class="text-5xl font-black text-orange-500">{{ $pendingDossiers ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-2xl">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm {{ ($criticalPatients ?? 0) > 0 ? 'ring-2 ring-red-500 animate-pulse' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Critique</p>
                        <p class="text-5xl font-black text-red-600">{{ $criticalPatients ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-2xl">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Mes Rendez-vous d'aujourd'hui -->
        @if(isset($todayAppointments) && $todayAppointments->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter mb-6">🗓️ Mes Rendez-vous d'Aujourd'hui</h2>
            <div class="bg-white rounded-[2rem] border border-blue-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="p-6 text-xs font-black text-blue-800 uppercase tracking-wider">Heure</th>
                                <th class="p-6 text-xs font-black text-blue-800 uppercase tracking-wider">Patient</th>
                                <th class="p-6 text-xs font-black text-blue-800 uppercase tracking-wider">Motif</th>
                                <th class="p-6 text-xs font-black text-blue-800 uppercase tracking-wider">Statut</th>
                                <th class="p-6 text-xs font-black text-blue-800 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-50">
                            @foreach($todayAppointments as $appointment)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-6">
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded-lg font-black text-sm">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('H:i') }}
                                    </span>
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-gray-800">{{ $appointment->patient->full_name ?? 'Inconnu' }}</div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">IPU: {{ $appointment->patient->ipu ?? 'N/A' }}</div>
                                </td>
                                <td class="p-6 text-gray-600">{{ $appointment->reason }}</td>
                                <td class="p-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                        @if($appointment->status === 'confirmed') bg-green-100 text-green-700
                                        @elseif($appointment->status === 'scheduled') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ $appointment->status === 'confirmed' ? 'Confirmé' : 'Programmé' }}
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <a href="{{ route('patients.show', $appointment->patient_id) }}" class="inline-flex items-center gap-2 text-blue-600 font-black text-xs uppercase tracking-widest hover:text-blue-800 transition-all">
                                        Voir Dossier
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="mb-12">
            <h2 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter mb-6">🗓️ Mes Rendez-vous d'Aujourd'hui</h2>
            <div class="bg-white rounded-[2rem] border border-dashed border-gray-200 p-10 text-center">
                <p class="text-gray-400 font-bold uppercase tracking-widest">Aucun rendez-vous confirmé pour aujourd'hui.</p>
            </div>
        </div>
        @endif

        <!-- Section Mes Prochains Rendez-vous -->
        @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter mb-6">📅 Mes Prochains Rendez-vous</h2>
            <div class="bg-white rounded-[2rem] border border-indigo-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="p-6 text-xs font-black text-indigo-800 uppercase tracking-wider">Date & Heure</th>
                                <th class="p-6 text-xs font-black text-indigo-800 uppercase tracking-wider">Patient</th>
                                <th class="p-6 text-xs font-black text-indigo-800 uppercase tracking-wider">Motif</th>
                                <th class="p-6 text-xs font-black text-indigo-800 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50">
                            @foreach($upcomingAppointments as $appointment)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="p-6">
                                    <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->isoFormat('dddd D MMMM') }}</div>
                                    <div class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded inline-block font-black mt-1">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-gray-900">{{ $appointment->patient->full_name ?? 'Inconnu' }}</div>
                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-tight">IPU: {{ $appointment->patient->ipu ?? 'N/A' }}</div>
                                </td>
                                <td class="p-6 text-gray-600 font-medium">{{ Str::limit($appointment->reason, 40) }}</td>
                                <td class="p-6 text-right">
                                    <a href="{{ route('patients.show', $appointment->patient_id) }}" class="inline-flex items-center gap-2 text-indigo-600 font-black text-xs uppercase tracking-widest hover:text-indigo-800 transition-all">
                                        Détails
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Section Demandes de Rendez-vous -->
        @if(isset($pendingServiceAppointments) && $pendingServiceAppointments->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter mb-6">🤝 Rendez-vous à prendre / Traiter</h2>
            <div class="bg-white rounded-[2rem] border border-orange-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-orange-50">
                            <tr>
                                <th class="p-6 text-xs font-black text-orange-800 uppercase tracking-wider">Patient</th>
                                <th class="p-6 text-xs font-black text-orange-800 uppercase tracking-wider">Date souhaitée</th>
                                <th class="p-6 text-xs font-black text-orange-800 uppercase tracking-wider">Motif</th>
                                <th class="p-6 text-xs font-black text-orange-800 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-orange-50">
                            @foreach($pendingServiceAppointments as $appointment)
                            <tr class="hover:bg-orange-50/30 transition-colors">
                                <td class="p-6 font-bold text-gray-800">{{ $appointment->patient->full_name ?? 'Inconnu' }}</td>
                                <td class="p-6">
                                    <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->isoFormat('dddd D MMMM HH:mm') }}</div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->diffForHumans() }}</div>
                                </td>
                                <td class="p-6 text-gray-600">{{ $appointment->reason }}</td>
                                <td class="p-6 text-right">
                                    <form action="{{ route('appointments.approve', $appointment->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('POST') <!-- Ou PUT selon ta route -->
                                        <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-green-700 transition-all shadow-md flex items-center gap-2 ml-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Approuver & Assigner
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div id="patients-section">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-gray-800 italic uppercase tracking-tighter">Suivi des hospitalisations</h2>
                <div class="flex gap-2 bg-gray-100 p-1 rounded-2xl">
                    <button onclick="filterPatients('all')" id="btn-all" class="filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all bg-white shadow-sm text-blue-600">Tous</button>
                    <button onclick="filterPatients('critical')" id="btn-critical" class="filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all text-gray-500 hover:bg-gray-200">Critiques</button>
                </div>
            </div>

            <div id="patients-list">
                @forelse($hospitalizedPatients as $admission)
                    @php
                        $patient = $admission->patient;
                        if (!$patient) continue;

                        $signes = $admission->derniersSignes;
                        $isCritical = ($admission->alert_level === 'critical') ||
                                      ($signes && ($signes->temperature >= 38.5 || $signes->temperature <= 35.0)) ||
                                      ($signes && ($signes->pulse >= 120 || $signes->pulse <= 50));
                    @endphp

                    <div class="patient-card relative mb-6 bg-white rounded-[2rem] border {{ $isCritical ? 'border-red-100 ring-2 ring-red-50' : 'border-gray-100' }} shadow-sm overflow-hidden transition-all hover:shadow-xl" data-alert="{{ $isCritical ? 'critical' : 'stable' }}">
                        {{-- BOUTON PARTAGER EN BORDURE --}}
                        <div class="absolute top-4 right-4 z-10 flex gap-2">
                            <form action="{{ route('medical_records.share', $patient->id) }}" method="POST" onsubmit="return confirm('Partager tout le dossier au patient ?')">
                                @csrf
                                <button type="submit" class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm group" title="Partager le dossier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                </button>
                            </form>
                            @if($isCritical)
                                <span class="bg-red-600 text-white px-4 h-10 flex items-center rounded-xl font-black text-[10px] uppercase tracking-widest animate-pulse shadow-lg ring-4 ring-red-50">Urgence Critique</span>
                            @endif
                        </div>

                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row gap-8">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-6">
                                        <div class="h-16 w-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-xl border border-blue-100 uppercase">
                                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-black text-gray-900 leading-tight uppercase">{{ $patient->full_name }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <p class="text-gray-500 font-medium">{{ $patient->age }} ANS • IPU: <span class="font-mono text-xs">{{ $patient->ipu }}</span></p>
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase border border-blue-100 italic">
                                                    📍 {{ $admission->room->service->name ?? 'Service Inconnu' }} • CH. {{ $admission->room->room_number ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="flex flex-wrap gap-6">
                                        <div class="flex items-center space-x-3">
                                            <span class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shadow-sm">🌡️</span>
                                            <div>
                                                <p class="text-[9px] text-gray-400 font-black uppercase">Température</p>
                                                <p class="font-black {{ ($signes && ($signes->temperature >= 38.5 || $signes->temperature <= 35.0)) ? 'text-red-600' : 'text-gray-800' }}">
                                                    {{ $signes->temperature ?? '--' }}°C
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shadow-sm">💓</span>
                                            <div>
                                                <p class="text-[9px] text-gray-400 font-black uppercase">Pouls / TA</p>
                                                <p class="font-black {{ ($signes && ($signes->pulse >= 120 || $signes->pulse <= 50)) ? 'text-red-600' : 'text-gray-800' }}">
                                                    {{ $signes->pulse ?? '--' }} <span class="text-[10px] text-gray-400 uppercase">BPM</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center text-pink-500 font-black text-xs shadow-sm">{{ $patient->blood_group ?? '??' }}</span>
                                            <div>
                                                <p class="text-[9px] text-gray-400 font-black uppercase">Groupe</p>
                                                <p class="font-black text-gray-800 italic uppercase">Sanguin</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                    <div class="lg:w-64 flex flex-col gap-3 justify-center">
                                        <a href="{{ route('patients.show', $patient->id ?? '#') }}" class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 text-white rounded-2xl font-black hover:bg-black transition-all shadow-lg text-xs tracking-widest uppercase">
                                            Ouvrir Dossier
                                        </a>

                                        <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id ?? '#']) }}" class="flex items-center justify-center gap-2 w-full py-4 bg-blue-50 text-blue-600 rounded-2xl font-black hover:bg-blue-100 transition-all border border-blue-100 text-xs tracking-widest uppercase">
                                            Prescription
                                        </a>
                                        <form action="{{ route('medical_records.discharge', $admission->id) }}" method="POST" onsubmit="return confirm('Confirmer la sortie du patient ?')">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="flex items-center justify-center gap-2 w-full py-4 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 transition-all shadow-lg text-xs tracking-widest uppercase">
                                                Sortir Patient
                                            </button>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-gray-200">
                        <p class="text-gray-400 font-bold uppercase tracking-widest">Aucun patient hospitalisé.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function filterPatients(type) {
        const cards = document.querySelectorAll('.patient-card');
        const btnAll = document.getElementById('btn-all');
        const btnCritical = document.getElementById('btn-critical');

        cards.forEach(card => {
            if (type === 'all') {
                card.style.display = 'block';
                // Update UI Buttons
                btnAll.className = "filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all bg-white shadow-sm text-blue-600";
                btnCritical.className = "filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all text-gray-500 hover:bg-gray-200";
            } else {
                if (card.getAttribute('data-alert') === 'critical') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
                // Update UI Buttons
                btnCritical.className = "filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all bg-white shadow-sm text-red-600";
                btnAll.className = "filter-btn px-5 py-2 rounded-xl text-sm font-bold transition-all text-gray-500 hover:bg-gray-200";
            }
        });
    }
    
    // S'assurer que tout est visible au chargement
    window.onload = () => filterPatients('all');
</script>
@endsection
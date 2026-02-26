<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dossier Médical - Portail Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .nav-link.active { color: #2563eb; border-bottom-color: #2563eb; background-color: #eff6ff; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">
    
    <!-- Navbar Premium -->
    <nav class="glass sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight">Mon Dossier Médical</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                     <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center border border-emerald-200">
                        <span class="h-2 w-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                        Dossier à jour
                    </span>
                    <form method="POST" action="{{ route('patient.logout') }}">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition flex items-center justify-center">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="flex space-x-1 overflow-x-auto scrollbar-hide pb-1">
                <a href="{{ route('patient.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-th-large mr-2.5"></i>Tableau de bord
                </a>
                <a href="{{ route('patient.appointments') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-calendar-check mr-2.5"></i>Rendez-vous
                </a>
                <a href="{{ route('patient.medical-history') }}" class="nav-link active flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-file-medical-alt mr-2.5"></i>Dossier Médical
                </a>
                <a href="{{ route('patient.prescriptions') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-pills mr-2.5"></i>Ordonnances
                </a>
                <a href="{{ route('patient.profile') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-user-circle mr-2.5"></i>Profil
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-5 transition hover:shadow-md">
                <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Identifiant IPU</p>
                    <p class="text-xl font-black text-slate-900">{{ Auth::guard('patients')->user()->ipu }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-5 transition hover:shadow-md">
                <div class="h-14 w-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Groupe Sanguin</p>
                    <p class="text-xl font-black text-slate-900">{{ Auth::guard('patients')->user()->blood_group ?? '--' }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-5 transition hover:shadow-md">
                <div class="h-14 w-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Dernière Visite</p>
                    <p class="text-xl font-black text-slate-900">
                        {{ $records->count() > 0 ? $records->first()->created_at->format('d/m/Y') : 'Aucune' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Historique des Consultations</h2>
                    <p class="text-slate-400 text-sm font-medium">Vos examens médicaux passés</p>
                </div>
                
                <button class="px-6 py-3 bg-slate-50 text-slate-600 rounded-[1.2rem] text-sm font-bold hover:bg-slate-100 transition flex items-center">
                    <i class="fas fa-cloud-download-alt mr-2.5"></i> Exporter
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date & Heure</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Médecin / Service</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Observation / Diagnostic</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Admission</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                            $displayedAdmissions = [];
                        @endphp

                        @forelse($records as $record)
                            @php
                                $admission = $record->related_admission;
                                
                                if ($admission) {
                                    if ($record->created_at < $admission->admission_date) {
                                        $isGroupRow = false;
                                        $displayDate = $record->created_at;
                                        $displayDoctor = $record->doctor ?? $admission->doctor;
                                    } 
                                    else {
                                        if (in_array($admission->id, $displayedAdmissions)) {
                                            continue;
                                        }
                                        $displayedAdmissions[] = $admission->id;
                                        $displayDate = $admission->admission_date;
                                        $displayDoctor = $admission->doctor;
                                        $isGroupRow = true;
                                    }
                                } else {
                                    $displayDate = $record->created_at;
                                    $displayDoctor = $record->doctor;
                                    $isGroupRow = false;
                                }
                            @endphp

                            <tr class="hover:bg-slate-50/50 transition-colors group {{ $isGroupRow ? 'bg-amber-50/20' : '' }}">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex flex-col items-center justify-center bg-white border border-slate-100 rounded-xl w-12 h-12 shadow-sm mr-4 text-xs font-black {{ $isGroupRow ? 'text-amber-600 border-amber-100' : 'text-slate-700' }}">
                                            <span class="text-[9px] uppercase text-slate-400 leading-none mb-0.5">{{ $displayDate->translatedFormat('M') }}</span>
                                            {{ $displayDate->format('d') }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900">{{ $displayDate->translatedFormat('l') }}</div>
                                            <div class="text-xs text-slate-400">{{ $displayDate->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-slate-900">{{ $displayDoctor?->name ?? 'Médecin non assigné' }}</div>
                                    
                                    @if($isGroupRow)
                                        <span class="inline-flex items-center mt-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase tracking-widest border border-amber-200">
                                            <i class="fas fa-procedures mr-2"></i> Episode Hospitalisation
                                        </span>
                                    @else
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-600 uppercase tracking-wide">
                                            {{ $record->service->name ?? 'Service Général' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    @if($isGroupRow)
                                        <p class="text-sm text-slate-500 italic flex items-center">
                                            <i class="fas fa-layer-group mr-2 text-amber-400"></i>
                                            Détail de l'hospitalisation...
                                        </p>
                                    @else
                                        <p class="text-sm text-slate-600 leading-relaxed max-w-xs">
                                            {{ Str::limit($record->observations ?? $record->reason, 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    @if($admission)
                                        <div class="flex flex-col">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-orange-50 text-orange-600 border border-orange-100 uppercase tracking-tighter w-max mb-1">
                                                <i class="fas fa-procedures mr-1"></i> Admis
                                            </span>
                                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                                                <div class="flex items-center"><i class="fas fa-sign-in-alt mr-1 opacity-50"></i> {{ $admission->admission_date->format('d/m/Y') }}</div>
                                                @if($admission->discharge_date)
                                                    <div class="flex items-center"><i class="fas fa-sign-out-alt mr-1 opacity-50"></i> {{ $admission->discharge_date->format('d/m/Y') }}</div>
                                                @else
                                                    <div class="flex items-center text-emerald-600"><i class="fas fa-clock mr-1"></i> En cours</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-400 w-max uppercase italic">
                                            <i class="fas fa-minus mr-1 opacity-30"></i> Externe
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @if($isGroupRow)
                                        <a href="{{ route('patient.medical-history.admission.show', $admission->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-100 rounded-xl text-[10px] font-black text-slate-600 hover:border-amber-200 hover:text-amber-700 hover:bg-amber-50 transition-all shadow-sm uppercase tracking-widest">
                                            Détails <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('patient.medical-history.show', $record->id) }}" class="h-10 w-10 bg-white border border-slate-100 rounded-xl text-slate-400 hover:border-indigo-100 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm flex items-center justify-center">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">
                                            <i class="fas fa-file-medical-alt text-4xl"></i>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-900 mb-2">Aucun historique disponible</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto text-sm">
                                            Une fois vos consultations effectuées, elles apparaîtront automatiquement ici.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/20">
                    {{ $records->links() }}
                </div>
            @endif
        </div>

        <div class="mt-8 p-6 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-3 rounded-xl text-emerald-600 shadow-sm">
                    <i class="fas fa-shield-alt text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-emerald-900 text-sm">Sécurité Garantie</h4>
                    <p class="text-xs text-emerald-700/80 font-medium mt-0.5">
                        Vos données de santé sont cryptées et protégées conformément à la réglementation.
                    </p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
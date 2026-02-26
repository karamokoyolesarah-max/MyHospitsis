@extends('layouts.app')

@section('title', 'Historique des Assignations')

@section('content')
<div class="p-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 animate-in fade-in slide-in-from-top-4 duration-700">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-slate-900 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic">Archives & Historique</h1>
            </div>
            <p class="text-slate-400 font-bold text-sm ml-4 tracking-wide uppercase">Traçabilité complète des patients orientés</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('secretary.dashboard') }}" 
               class="px-6 py-3 bg-white border border-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/40 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 animate-in fade-in slide-in-from-top-4 duration-1000">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-amber-50 rounded-[2rem] flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-user-clock text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">En attente</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['pending_count'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-green-50 rounded-[2rem] flex items-center justify-center text-green-500 group-hover:bg-green-500 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Assignés Aujourd'hui</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['today_assigned_count'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-blue-50 rounded-[2rem] flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Total ce jour</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_today'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 mb-8 animate-in fade-in duration-700">
        <form action="{{ route('secretary.history') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Rechercher un Patient</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nom, Prénom ou IPU..." 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 pl-14 pr-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                    </div>
                </div>

                {{-- Date Filter --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Période d'Orientation</label>
                    <select name="date_filter" class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <option value="">Toutes les dates</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                        <option value="2_days" {{ request('date_filter') == '2_days' ? 'selected' : '' }}>Il y a 2 jours</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    </select>
                </div>

                {{-- Doctor Filter --}}
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Filtrer par Médecin</label>
                    <select name="doctor_id" class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <option value="">Tous les médecins</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                <div class="flex items-center gap-4">
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-lg shadow-slate-900/20">
                        Appliquer les Filtres
                    </button>
                    <a href="{{ route('secretary.history') }}" class="px-8 py-3 bg-slate-50 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-100 hover:text-slate-600 transition-all">
                        Réinitialiser
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-2 text-slate-400 italic">
                    <i class="fas fa-info-circle text-xs"></i>
                    <span class="text-[10px] font-bold">L'historique est conservé de manière sécurisée.</span>
                </div>
            </div>
        </form>
    </div>

    {{-- Archives Table --}}
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Registre des Orientations</h3>
            <span class="bg-slate-900 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase">{{ $archives->total() }} Assignations Trouvées</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.15em]">
                    <tr>
                        <th class="px-8 py-5">Patient & IPU</th>
                        <th class="px-8 py-5">Service / Spécialité</th>
                        <th class="px-8 py-5 text-center">Médecin Traitant</th>
                        <th class="px-8 py-5">Date d'Assignation</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($archives as $archive)
                    <tr class="group hover:bg-slate-50 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-inner">
                                    {{ substr($archive->patient->name, 0, 1) }}{{ substr($archive->patient->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 uppercase text-sm tracking-tight">{{ $archive->patient->name }} {{ $archive->patient->first_name }}</p>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mt-0.5">IPU: {{ $archive->patient->ipu }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 bg-slate-50 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100">
                                {{ $archive->service->name }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="font-bold text-slate-700 text-sm">Dr. {{ $archive->doctor->name }}</span>
                                <span class="text-[9px] text-slate-400 uppercase font-black tracking-tighter">{{ $archive->doctor->service->name ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-500 text-xs">{{ $archive->secretary_archived_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-bold text-slate-300 mt-0.5 uppercase tracking-widest">{{ $archive->secretary_archived_at->format('H:i') }}</div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end items-center gap-2">
                                <button onclick="showPatientDetails({{ json_encode($archive) }})" class="w-10 h-10 bg-slate-50 hover:bg-blue-600 text-slate-400 hover:text-white rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm" title="Détails">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <form action="{{ route('secretary.appointments.destroy', $archive->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette assignation ? Cette action est irréversible.')" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-rose-50 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm" title="Supprimer Définitivement">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mx-auto mb-6">
                                <i class="fas fa-folder-open fa-3x"></i>
                            </div>
                            <h4 class="text-slate-400 font-black uppercase tracking-[0.2em] text-sm">Historique Vide</h4>
                            <p class="text-slate-300 text-xs mt-2 font-bold italic">Les assignations archivées apparaîtront ici.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="px-8">
        {{ $archives->links() }}
    </div>
</div>

{{-- Detail Modal --}}
<div id="modal-details" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter italic">Fiche de Synthèse Patient</h3>
            <button onclick="document.getElementById('modal-details').classList.add('hidden')" class="w-12 h-12 bg-white border border-slate-100 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-500 transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modal-content" class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
            {{-- Content will be injected via JS --}}
        </div>
    </div>
</div>

<script>
function showPatientDetails(appointment) {
    const modal = document.getElementById('modal-details');
    const content = document.getElementById('modal-content');
    
    content.innerHTML = `
        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Identité du Patient</h4>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-inner">
                        <p class="text-xl font-black text-slate-900 uppercase mb-1">${appointment.patient.name} ${appointment.patient.first_name}</p>
                        <p class="text-[11px] font-bold text-blue-600 bg-blue-50 inline-block px-3 py-1 rounded-full uppercase tracking-widest mb-4">IPU: ${appointment.patient.ipu}</p>
                        <div class="space-y-2 mt-2">
                            <p class="text-xs font-bold text-slate-600 flex items-center gap-3"><i class="fas fa-venus-mars w-4 text-slate-400"></i> ${appointment.patient.gender || 'N/A'}</p>
                            <p class="text-xs font-bold text-slate-600 flex items-center gap-3"><i class="fas fa-birthday-cake w-4 text-slate-400"></i> ${appointment.patient.dob || 'N/A'}</p>
                            <p class="text-xs font-bold text-slate-600 flex items-center gap-3"><i class="fas fa-phone w-4 text-slate-400"></i> ${appointment.patient.phone || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Détails de l'Assignation</h4>
                    <div class="bg-blue-50/30 p-6 rounded-3xl border border-blue-100 shadow-inner">
                        <div class="mb-4">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Médecin Assigné</p>
                            <p class="text-md font-black text-slate-900 uppercase tracking-tighter">Dr. ${appointment.doctor.name}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Service / Unité</p>
                            <p class="text-md font-black text-slate-900 uppercase tracking-tighter">${appointment.service.name}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Date du Rendez-vous</p>
                            <p class="text-md font-black text-slate-900 uppercase tracking-tighter">${appointment.appointment_datetime}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pt-6 border-t border-slate-50">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Motif & Notes</h4>
            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 italic text-slate-500 font-bold text-sm">
                ${appointment.reason || 'Aucun motif spécifié.'}
                <hr class="my-3 border-white/50">
                <span class="text-[10px] font-black uppercase text-slate-400">Notes Secrétariat :</span><br>
                ${appointment.notes || 'Aucune note.'}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Close on backdrop click
document.getElementById('modal-details').onclick = (e) => {
    if (e.target.id === 'modal-details') {
        document.getElementById('modal-details').classList.add('hidden');
    }
}
</script>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-in { animation: fade-in-down 0.7s ease-out; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection

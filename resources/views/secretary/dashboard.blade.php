@extends('layouts.app')

@section('title', 'Dashboard Secrétariat')

@section('content')
<div class="p-8">
    {{-- Header Premium --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 animate-in fade-in slide-in-from-top-4 duration-700">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic">Secrétariat</h1>
            </div>
            <p class="text-slate-400 font-bold text-sm ml-4 tracking-wide uppercase">Pilotage des Admissions & Assignations Médicales</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('secretary.patients.create') }}" 
               class="group relative px-8 py-4 bg-white border-2 border-slate-100 text-slate-900 rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200/50 hover:scale-105 active:scale-95 transition-all duration-300">
                <span class="relative z-10 flex items-center gap-3">
                    <i class="fas fa-user-plus text-blue-600 text-lg"></i>
                    Nouveau Patient
                </span>
            </a>
            <a href="{{ route('secretary.appointments.create') }}" 
               class="group relative px-8 py-4 bg-blue-600 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all duration-300">
                <span class="relative z-10 flex items-center gap-3">
                    <i class="fas fa-calendar-plus text-lg"></i>
                    Nouveau Rendez-vous
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-amber-50 rounded-[2rem] flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-user-clock text-3xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">En attente</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['pending_count'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-green-50 rounded-[2rem] flex items-center justify-center text-green-500 group-hover:bg-green-500 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-check-double text-3xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Assignés Aujourd'hui</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['today_assigned_count'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 group">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-blue-50 rounded-[2rem] flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                    <i class="fas fa-calendar-day text-3xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Total ce jour</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['total_today'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content Area with Tabs --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden" x-data="{ activeTab: 'pending' }">
                {{-- Tabs Header --}}
                <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-50/30">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-50 text-blue-600">
                            <i class="fas fa-stream text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter italic">Gestion des Orientations</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilotage des flux patients</p>
                        </div>
                    </div>
                    
                    <div class="flex bg-white/50 p-1.5 rounded-2xl border border-slate-100/50 shadow-inner">
                        <button @click="activeTab = 'pending'" 
                                :class="activeTab === 'pending' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'"
                                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            En attente
                        </button>
                        <button @click="activeTab = 'archived'" 
                                :class="activeTab === 'archived' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'"
                                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-archive"></i>
                            Archives
                        </button>
                    </div>
                </div>

                {{-- Tab: Pending --}}
                <div x-show="activeTab === 'pending'" class="animate-in fade-in duration-500">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.15em]">
                                <tr>
                                    <th class="px-8 py-5">Patient Information</th>
                                    <th class="px-8 py-5 text-center">Spécialité</th>
                                    <th class="px-8 py-5">Date / Heure</th>
                                    <th class="px-8 py-5">Médecin Référent</th>
                                    <th class="px-8 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($pendingAppointments as $appointment)
                                <tr class="group hover:bg-blue-50/40 transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 font-black text-sm group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                                                {{ substr($appointment->patient->name, 0, 1) }}{{ substr($appointment->patient->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-slate-800 uppercase text-sm tracking-tight">{{ $appointment->patient->name }} {{ $appointment->patient->first_name }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-tighter">IPU: {{ $appointment->patient->ipu }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-4 py-1.5 bg-slate-50 text-slate-600 group-hover:bg-blue-100 group-hover:text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest transition-all">
                                            {{ $appointment->service->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-black text-slate-700 text-xs">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y') }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('H:i') }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <form action="{{ route('secretary.appointments.assign', $appointment->id) }}" method="POST" class="flex items-center gap-3">
                                            @csrf
                                            <div class="relative flex-1">
                                                <select name="doctor_id" required class="w-full text-[11px] font-black bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer transition-all">
                                                    <option value="">SÉLECTIONNER...</option>
                                                    @php
                                                        $preferredDoctors = $doctors->filter(fn($d) => $d->service_id == $appointment->service_id);
                                                        $otherDoctors = $doctors->filter(fn($d) => $d->service_id != $appointment->service_id);
                                                    @endphp
                                                    
                                                    @if($preferredDoctors->count() > 0)
                                                        <optgroup label="SPÉCIALISTES DU SERVICE">
                                                            @foreach($preferredDoctors as $doctor)
                                                                <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }} ({{ $doctor->service->name ?? 'N/A' }})</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif

                                                    @if($otherDoctors->count() > 0)
                                                        <optgroup label="AUTRES DISPONIBLES">
                                                            @foreach($otherDoctors as $doctor)
                                                                <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }} ({{ $doctor->service->name ?? 'N/A' }})</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                </select>
                                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fas fa-chevron-down text-[8px]"></i>
                                                </div>
                                            </div>
                                            <button type="submit" class="w-10 h-10 bg-green-50 hover:bg-green-600 text-green-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-90" title="Assigner">
                                                <i class="fas fa-user-plus text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button onclick="showPatientDetails({{ json_encode($appointment) }})" class="w-10 h-10 bg-slate-50 hover:bg-slate-900 text-slate-400 hover:text-white rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm" title="Voir Détails">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mx-auto mb-4">
                                            <i class="fas fa-check-circle fa-3x"></i>
                                        </div>
                                        <p class="text-slate-400 font-black italic uppercase tracking-[0.2em] text-xs">Aucune assignation en attente</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: Archived (Preview of latest archives) --}}
                <div x-show="activeTab === 'archived'" class="animate-in fade-in duration-500" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.15em]">
                                <tr>
                                    <th class="px-8 py-5">Patient Information</th>
                                    <th class="px-8 py-5">Service</th>
                                    <th class="px-8 py-5">Date d'Archivage</th>
                                    <th class="px-8 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($recentArchives as $archive)
                                <tr class="group hover:bg-slate-50/60 transition-all duration-300 italic opacity-70 hover:opacity-100">
                                    <td class="px-8 py-6">
                                        <p class="font-black text-slate-800 uppercase text-xs">{{ $archive->patient->name }} {{ $archive->patient->first_name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400">IPU: {{ $archive->patient->ipu }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-200/50">
                                            {{ $archive->service->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-black text-slate-400 text-[10px]">{{ $archive->secretary_archived_at->format('d/m/Y') }}</div>
                                        <div class="text-[9px] font-bold text-slate-300 mt-0.5 uppercase tracking-widest">{{ $archive->secretary_archived_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            <button onclick="showPatientDetails({{ json_encode($archive) }})" class="w-8 h-8 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all active:scale-95" title="Détails">
                                                <i class="fas fa-eye text-[10px]"></i>
                                            </button>
                                            <form action="{{ route('secretary.appointments.destroy', $archive->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette assignation ?')" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 bg-rose-50 text-rose-400 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all active:scale-95" title="Supprimer Définitivement">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <p class="text-slate-300 font-bold italic uppercase tracking-[0.2em] text-xs">Aucune archive récente</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-8 bg-slate-50 text-center border-t border-slate-100">
                        <a href="{{ route('secretary.history') }}" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black text-slate-600 uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-500 shadow-sm">
                            Accéder au Registre Complet <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Recent Assignments --}}
        <div class="space-y-8">
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-green-50/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-history text-green-600"></i>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Assignés à l'instant</h3>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black">{{ $assignedToday->count() }}</span>
                    </div>
                </div>
                <div class="divide-y divide-slate-50 max-h-[45rem] overflow-y-auto custom-scrollbar">
                    @forelse($assignedToday as $assigned)
                    <div class="p-6 hover:bg-slate-50 transition-colors group/item">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shadow-inner group-hover/item:bg-blue-600 group-hover/item:text-white transition-all duration-500">
                                <i class="fas fa-user-md text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="font-black text-slate-800 text-xs truncate uppercase">{{ $assigned->patient->name }} {{ $assigned->patient->first_name }}</p>
                                    <div class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                        <button onclick="showPatientDetails({{ json_encode($assigned) }})" class="w-6 h-6 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition-all active:scale-90" title="Détails">
                                            <i class="fas fa-info-circle text-[8px]"></i>
                                        </button>
                                        <form action="{{ route('secretary.appointments.archive', $assigned->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-6 h-6 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all active:scale-90" title="Archiver l'assignation">
                                                <i class="fas fa-archive text-[8px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-slate-400 flex items-center gap-1">
                                        <i class="fas fa-long-arrow-alt-right"></i>
                                        Dr. {{ $assigned->doctor->name }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $assigned->updated_at->diffForHumans() }}</span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[8px] font-black uppercase">Assigné</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-inbox text-slate-100 text-4xl mb-4 block"></i>
                        <p class="text-slate-300 font-bold italic text-[10px] uppercase tracking-widest">Pas d'historique récent</p>
                    </div>
                    @endforelse
                </div>
                @if($assignedToday->count() > 0)
                <div class="p-6 bg-slate-50 text-center">
                    <a href="{{ route('secretary.history') }}" class="text-[9px] font-black text-blue-600 uppercase tracking-widest hover:text-slate-900 transition-colors">Voir l'historique complet</a>
                </div>
                @endif
            </div>

            {{-- Info Card --}}
            <div class="bg-slate-900 rounded-[3rem] p-8 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="text-xl font-black mb-4 tracking-tight italic">Assistance Secrétariat</h4>
                    <p class="text-slate-400 text-xs font-bold leading-relaxed mb-6">
                        L'assignation d'un médecin confirme automatiquement le rendez-vous. Assurez-vous de vérifier les disponibilités dans l'agenda.
                    </p>
                    <a href="{{ route('secretary.agendas') }}" class="inline-flex items-center gap-3 text-blue-400 font-black text-[10px] uppercase tracking-widest group-hover:gap-4 transition-all">
                        Consulter les agendas
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <i class="fas fa-info-circle absolute -bottom-4 -right-4 text-slate-800 text-8xl opacity-30 transform group-hover:rotate-12 transition-all duration-700"></i>
            </div>
        </div>
    </div>
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
            {{-- Content injected via JS --}}
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
                        ${appointment.doctor ? `
                            <div class="mb-4">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Médecin Assigné</p>
                                <p class="text-md font-black text-slate-900 uppercase tracking-tighter">Dr. ${appointment.doctor.name}</p>
                            </div>
                        ` : '<p class="text-xs font-black text-amber-500 mb-4 italic">En attente d\'assignation...</p>'}
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

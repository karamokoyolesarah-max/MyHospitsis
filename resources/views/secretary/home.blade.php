@extends('layouts.app')

@section('title', 'Accueil Secrétariat')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter italic">Secrétariat Général</h1>
            </div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs opacity-70">Pilotage global de l'établissement et des flux patients.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('secretary.patients.create') }}" class="px-6 py-4 bg-white border-2 border-slate-100 text-slate-900 rounded-[2rem] font-black uppercase tracking-widest text-[10px] hover:bg-slate-50 transition shadow-xl shadow-slate-200/50 flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-600"></i> Nouveau Patient
            </a>
            <a href="{{ route('secretary.appointments.create') }}" class="px-8 py-4 bg-blue-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[10px] hover:bg-blue-700 transition shadow-2xl shadow-blue-200 flex items-center gap-2">
                <i class="fas fa-calendar-plus"></i> Nouveau RDV
            </a>
        </div>
    </div>

    <!-- Stats Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mb-1">Total Patients</p>
                <p class="text-3xl font-black text-slate-900 italic">{{ number_format($stats['total_patients']) }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mb-1">Inscriptions (24h)</p>
                <p class="text-3xl font-black text-slate-900 italic">{{ $stats['new_patients_today'] }}</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mb-1">Attente Assignation</p>
                <p class="text-3xl font-black text-slate-900 italic">{{ $stats['pending_assignments'] }}</p>
                <a href="{{ route('secretary.dashboard') }}" class="mt-4 text-[9px] font-black text-amber-600 uppercase flex items-center gap-1 hover:underline">
                    Gérer maintenant <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-user-md text-xl"></i>
                </div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mb-1">Médecins Actifs</p>
                <p class="text-3xl font-black text-slate-900 italic">{{ $stats['active_doctors'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Dernières Assignations</h2>
                    <a href="{{ route('secretary.history') }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Voir l'historique</a>
                </div>

                <div class="space-y-6">
                    @forelse($recentAssignments as $assignment)
                    <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-200 transition-all group">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-900 uppercase italic text-sm">{{ $assignment->patient->name }}</h4>
                            <p class="text-[10px] text-slate-500 font-bold">Assigné au Dr. {{ $assignment->doctor->name }} ({{ $assignment->service->name ?? 'N/A' }})</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $assignment->updated_at->diffForHumans() }}</p>
                            <span class="inline-block px-3 py-1 bg-white border border-slate-200 rounded-full text-[8px] font-black text-slate-600 uppercase mt-1 italic">Succès</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                            <i class="fas fa-clipboard-list text-3xl"></i>
                        </div>
                        <p class="text-slate-400 font-black uppercase text-xs">Aucune activité récente pour le moment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar / Shortcuts -->
        <div class="space-y-8">
            <!-- Doctor Agendas Widget -->
            <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-slate-300 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/20 blur-3xl"></div>
                <div class="relative z-10">
                    <h3 class="text-lg font-black uppercase italic tracking-tighter mb-4">Planning Médical</h3>
                    <p class="text-xs text-slate-400 font-bold mb-8 leading-relaxed italic">Vérifiez les disponibilités avant chaque assignation.</p>
                    <a href="{{ route('secretary.agendas') }}" class="block w-full py-4 bg-white text-slate-900 rounded-2xl font-black uppercase tracking-widest text-[10px] text-center hover:bg-slate-100 transition shadow-xl">
                        Consulter les agendas
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <h3 class="font-black text-slate-900 uppercase italic tracking-tighter mb-8">Accès Rapides</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('secretary.patients.index') }}" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-blue-50 transition text-center group">
                        <i class="fas fa-users text-slate-400 group-hover:text-blue-600 text-xl mb-3"></i>
                        <p class="text-[9px] font-black text-slate-600 uppercase group-hover:text-blue-600">Patients</p>
                    </a>
                    <a href="{{ route('secretary.appointments.index') }}" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-emerald-50 transition text-center group">
                        <i class="fas fa-calendar-alt text-slate-400 group-hover:text-emerald-600 text-xl mb-3"></i>
                        <p class="text-[9px] font-black text-slate-600 uppercase group-hover:text-emerald-600">Registre RDV</p>
                    </a>
                    <a href="{{ route('secretary.dashboard') }}" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-amber-50 transition text-center group">
                        <i class="fas fa-exchange-alt text-slate-400 group-hover:text-amber-600 text-xl mb-3"></i>
                        <p class="text-[9px] font-black text-slate-600 uppercase group-hover:text-amber-600">Assigner</p>
                    </a>
                    <a href="{{ route('secretary.history') }}" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-purple-50 transition text-center group">
                        <i class="fas fa-history text-slate-400 group-hover:text-purple-600 text-xl mb-3"></i>
                        <p class="text-[9px] font-black text-slate-600 uppercase group-hover:text-purple-600">Historique</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

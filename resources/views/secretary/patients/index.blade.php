@extends('layouts.app')

@section('title', 'Gestion des Patients - Secrétariat')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter italic">Base Patients</h1>
            </div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs opacity-70">Répertoire central analytique des dossiers patients.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('secretary.patients.create') }}" class="px-8 py-4 bg-blue-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[10px] hover:bg-blue-700 transition shadow-2xl shadow-blue-200 flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Nouveau Patient
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 mb-10">
        <form action="{{ route('secretary.patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par Nom, Prénom ou IPU..." 
                       class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">
            </div>
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-slate-800 transition">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Patients Grid/List -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">IPU / Patient</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Genre / Âge</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Contact</th>
                        <th class="px-8 py-6 text-[10px) font-black text-slate-400 uppercase tracking-widest italic">Date d'admission</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($patients as $patient)
                    <tr class="group hover:bg-blue-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 font-black group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    {{ substr($patient->name, 0, 1) }}{{ substr($patient->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-tighter">{{ $patient->ipu }}</p>
                                    <h4 class="font-black text-slate-900 uppercase italic text-sm">{{ $patient->full_name }}</h4>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-block px-3 py-1 bg-slate-100 rounded-full text-[8px] font-black text-slate-600 uppercase italic mb-1">{{ $patient->gender ?? 'N/A' }}</span>
                            <p class="text-[10px] font-bold text-slate-400 italic">{{ $patient->age }} ans</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-slate-700 text-[11px]">{{ $patient->phone }}</p>
                            <p class="text-[10px] font-bold text-slate-400 italic truncate max-w-[150px]">{{ $patient->email ?? 'Sans email' }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-[11px] font-black text-slate-600 italic uppercase">{{ $patient->created_at->format('d/m/Y') }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $patient->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('secretary.appointments.create', ['patient_id' => $patient->id]) }}" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Prendre RDV">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>
                                <a href="{{ route('patients.show', $patient) }}" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Voir profil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-user-slash text-3xl"></i>
                            </div>
                            <p class="text-slate-400 font-black uppercase text-xs">Aucun patient trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
        <div class="p-8 bg-slate-50/50 border-t border-slate-50">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

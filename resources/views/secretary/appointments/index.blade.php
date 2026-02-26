@extends('layouts.app')

@section('title', 'Registre des Rendez-vous - Secrétariat')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-emerald-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter italic">Registre RDV</h1>
            </div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs opacity-70">Journal central des rendez-vous et consultations.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('secretary.appointments.create') }}" class="px-8 py-4 bg-emerald-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[10px] hover:bg-emerald-700 transition shadow-2xl shadow-emerald-200 flex items-center gap-2">
                <i class="fas fa-calendar-plus"></i> Nouveau Rendez-vous
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 mb-10">
        <form action="{{ route('secretary.appointments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-calendar absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div class="flex-1">
                <select name="status" class="w-full px-8 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 appearance-none">
                    <option value="">Tous les statuts</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planifié</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-slate-800 transition">
                Filtrer le registre
            </button>
        </form>
    </div>

    <!-- Appointments List -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Date & Heure</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Patient</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Médecin / Service</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($appointments as $appointment)
                    <tr class="group hover:bg-emerald-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-black group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-slate-900 uppercase italic">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('H:i') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <h4 class="font-black text-slate-900 uppercase italic text-sm">{{ $appointment->patient->full_name }}</h4>
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-tighter">{{ $appointment->patient->ipu }}</p>
                        </td>
                        <td class="px-8 py-6">
                            @if($appointment->doctor)
                                <p class="text-[11px] font-black text-slate-700 italic">Dr. {{ $appointment->doctor->name }}</p>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-600 rounded text-[8px] font-black uppercase italic">En attente d'assignation</span>
                            @endif
                            <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $appointment->service->name ?? 'Service non défini' }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $statusClasses = [
                                    'scheduled' => 'bg-blue-100 text-blue-600',
                                    'confirmed' => 'bg-emerald-100 text-emerald-600',
                                    'completed' => 'bg-slate-100 text-slate-600',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    'no_show' => 'bg-amber-100 text-amber-600',
                                ];
                                $labels = [
                                    'scheduled' => 'Planifié',
                                    'confirmed' => 'Confirmé',
                                    'completed' => 'Terminé',
                                    'cancelled' => 'Annulé',
                                    'no_show' => 'Absent',
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 {{ $statusClasses[$appointment->status] ?? 'bg-slate-100' }} rounded-full text-[8px] font-black uppercase italic">
                                {{ $labels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 text-slate-400 text-xs italic font-bold">
                                #{{ $appointment->id }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-calendar-times text-3xl"></i>
                            </div>
                            <p class="text-slate-400 font-black uppercase text-xs">Aucun rendez-vous trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
        <div class="p-8 bg-slate-50/50 border-t border-slate-50">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

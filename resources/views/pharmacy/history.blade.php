@extends('layouts.app')
@section('title', 'Historique des Mouvements | HospitSIS')
@section('content')
<div class="p-4 md:p-8 space-y-8 bg-slate-50/50 min-h-screen">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-in fade-in slide-in-from-top-4 duration-1000">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg shadow-slate-200">
                    <i class="fas fa-history text-lg"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight uppercase italic">Historique Stock</h1>
            </div>
            <p class="text-slate-500 font-bold text-sm flex items-center gap-2">
                <span class="w-2 h-2 bg-slate-400 rounded-full animate-pulse"></span>
                Audit complet des flux et mouvements • {{ $logs->total() }} entrées
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pharmacy.dashboard') }}" 
               class="px-6 py-3 bg-white text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/50 hover:-translate-y-1 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour Dashboard
            </a>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100">
        <form action="{{ route('pharmacy.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative group">
                <select name="type" onchange="this.form.submit()"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                    <option value="">Tous les types</option>
                    <option value="entry" {{ request('type') == 'entry' ? 'selected' : '' }}>Entrées (Achats)</option>
                    <option value="exit" {{ request('type') == 'exit' ? 'selected' : '' }}>Sorties (Ventes/Usage)</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustements</option>
                    <option value="expired" {{ request('type') == 'expired' ? 'selected' : '' }}>Périmés</option>
                </select>
            </div>

            <div class="relative">
                <select name="medication_id" onchange="this.form.submit()"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                    <option value="">Tous les médicaments</option>
                    @foreach($medications as $med)
                        <option value="{{ $med->id }}" {{ request('medication_id') == $med->id ? 'selected' : '' }}>{{ $med->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                       class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none cursor-pointer">
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('pharmacy.history') }}" class="text-xs font-black text-blue-600 hover:text-black uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-times-circle"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="px-8 py-6">Date & Heure</th>
                        <th class="px-8 py-6">Médicament</th>
                        <th class="px-8 py-6">Type</th>
                        <th class="px-8 py-6 text-right">Quantité</th>
                        <th class="px-8 py-6">Opérateur</th>
                        <th class="px-8 py-6">Détails / Raison</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $index => $log)
                    <tr class="hover:bg-blue-50/20 transition-all duration-300 group opacity-0 animate-in fade-in slide-in-from-bottom-2 fill-mode-forwards"
                        style="animation-delay: {{ $index * 50 }}ms">
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-900 text-sm">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-bold text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-black text-slate-900 text-sm uppercase group-hover:text-blue-600 transition-colors">
                                {{ $log->stock->medication->name }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lot: {{ $log->stock->batch_number ?? 'N/A' }}</div>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $badgeClass = match($log->type) {
                                    'entry' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'exit' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'expired' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                                };
                                $typeName = match($log->type) {
                                    'entry' => 'Entrée',
                                    'exit' => 'Sortie',
                                    'expired' => 'Périmé',
                                    'adjustment' => 'Ajustement',
                                    default => $log->type
                                };
                            @endphp
                            <span class="{{ $badgeClass }} border px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ $typeName }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="font-black {{ $log->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }} text-base">
                                {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200 uppercase">
                                    {{ substr($log->user->name, 0, 2) }}
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ $log->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-medium text-slate-500 italic max-w-xs">{{ $log->reason ?? '-' }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-32 text-center bg-slate-50/30">
                            <div class="max-w-xs mx-auto space-y-4">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto border-4 border-white shadow-lg">
                                    <i class="fas fa-search-minus text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-black uppercase tracking-widest text-xs">Aucun mouvement</p>
                                    <p class="text-slate-400 font-medium text-xs mt-1">Aucun mouvement ne correspond à ces filtres.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-8 border-t border-slate-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

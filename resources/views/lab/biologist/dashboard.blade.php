@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#f8fafc]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Glassmorphism Hero Section --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-600 to-emerald-700 p-8 mb-8 shadow-2xl shadow-teal-900/20">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="hidden sm:flex w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-inner">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-[10px] font-black text-white uppercase tracking-widest mb-3 border border-white/20">
                            🔬 Pôle Technique (Diagnostic)
                        </div>
                        <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight">Espace Biologiste</h1>
                        <p class="text-teal-50 mt-1 font-medium opacity-90 flex items-center gap-2">
                            {{ auth()->user()->hospital->name ?? 'Système Hospitalier' }} • {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-teal-100 uppercase font-bold tracking-widest mb-1">Status Session</p>
                        <div class="flex items-center gap-2 text-white font-bold bg-black/10 px-4 py-2 rounded-2xl backdrop-blur-md border border-white/10">
                            <span class="w-2.5 h-2.5 bg-green-400 rounded-full shadow-[0_0_8px_rgba(74,222,128,0.8)] animate-pulse"></span>
                            Connecté en tant que Biologiste
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main KPI Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            {{-- Pending Validations Card --}}
            <a href="{{ route('lab.biologist.validation') }}" class="group bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @if($toValidateCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-bounce">A VALIDER</span>
                    @endif
                </div>
                <h3 class="text-gray-500 font-bold text-sm tracking-wide mb-1 uppercase">Validation en attente</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900">{{ $toValidateCount }}</span>
                    <span class="text-gray-400 text-sm font-medium">examens</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center text-amber-600 text-xs font-bold uppercase tracking-widest gap-2">
                    Traiter maintenant
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Urgent Card --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
                </div>
                <h3 class="text-gray-500 font-bold text-sm tracking-wide mb-1 uppercase">Alertes Critiques</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-rose-600">{{ $urgentPendingCount }}</span>
                    <span class="text-gray-400 text-sm font-medium">urgences</span>
                </div>
                <p class="mt-4 text-xs font-semibold text-gray-400">Priorité de traitement élevée</p>
            </div>

            {{-- Completed Today --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <h3 class="text-gray-500 font-bold text-sm tracking-wide mb-1 uppercase">Produit ce jour</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-emerald-600">{{ $completedToday }}</span>
                    <span class="text-gray-400 text-sm font-medium">complet</span>
                </div>
                <p class="mt-4 text-xs font-semibold text-gray-400">Objectif journalier : En cours</p>
            </div>

            {{-- Avg TAT Card --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-gray-500 font-bold text-sm tracking-wide mb-1 uppercase">Délai moyen (TAT)</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-indigo-600">{{ round($averageTAT ?? 0) }}</span>
                    <span class="text-gray-400 text-sm font-medium">min</span>
                </div>
                <p class="mt-4 text-xs font-semibold text-gray-400">Temps de réponse moyen</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Workload Chart (Simplified) --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-black text-gray-900">Volume d'Activité</h2>
                        <p class="text-sm text-gray-400 font-medium">Nombre d'analyses validées (7 derniers jours)</p>
                    </div>
                </div>
                
                <div class="flex items-end justify-between h-48 gap-4 px-2">
                    @php $maxCount = collect($workloadData)->max('count') ?: 1; @endphp
                    @foreach($workloadData as $data)
                        <div class="flex-1 flex flex-col items-center gap-3 group">
                            <div class="relative w-full flex flex-col justify-end h-full">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-900 text-white text-[10px] py-1 px-2 rounded-lg font-bold">
                                    {{ $data['count'] }}
                                </div>
                                <div class="w-full bg-teal-50 rounded-xl group-hover:bg-teal-100 transition-colors" style="height: {{ ($data['count'] / $maxCount) * 100 }}%">
                                    <div class="w-full h-full bg-gradient-to-t from-teal-500 to-teal-400 rounded-xl opacity-80 group-hover:opacity-100 transition-all shadow-lg shadow-teal-500/20"></div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-gray-400 group-hover:text-gray-900 uppercase tracking-tighter">{{ $data['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <h2 class="text-xl font-black text-gray-900 mb-6 font-display italic">Dernières Validations</h2>
                
                <div class="space-y-6">
                    @forelse($recentValidations as $validation)
                        <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-xs">
                                {{ substr($validation->patient_name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $validation->patient_name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ $validation->test_name }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    <span class="text-[10px] text-gray-500 font-medium italic">{{ $validation->validated_at ? $validation->validated_at->diffForHumans() : 'Non validé' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('lab.history') }}" class="mt-8 block w-full py-4 text-center text-gray-500 hover:text-teal-600 font-bold text-xs uppercase tracking-[0.2em] border-2 border-dashed border-gray-100 rounded-2xl hover:border-teal-200 transition-all">
                    Consulter l'historique
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
    
    body {
        font-family: 'Outfit', sans-serif;
    }

    .font-display {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endsection

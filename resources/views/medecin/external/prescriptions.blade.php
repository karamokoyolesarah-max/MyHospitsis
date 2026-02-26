@extends('layouts.external_doctor')

@section('title', 'Prescriptions')
@section('page-title', 'Prescriptions')
@section('page-subtitle', 'Gérer vos ordonnances')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Prescriptions</h1>
            <p class="text-gray-500">Créez et gérez vos ordonnances médicales</p>
        </div>
        <a href="{{ route('external.prescriptions.create') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-indigo-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nouvelle Prescription</span>
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $prescriptions->total() }}</p>
                    <p class="text-gray-500 text-sm">Prescriptions totales</p>
                </div>
            </div>
        </div>
        <!-- Placeholder Stats -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 opacity-60">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-gray-500 text-sm">En attente (Bientôt)</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 opacity-60">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-gray-500 text-sm">Analytique (Bientôt)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescriptions List -->
    @if($prescriptions->isEmpty())
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-16 text-center">
        <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
            <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Aucune prescription trouvée</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-8">Commencez par créer une nouvelle prescription pour vos patients. Elle sera instantanément disponible dans leur espace.</p>
        <a href="{{ route('external.prescriptions.create') }}" class="inline-flex items-center space-x-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-widest text-sm rounded-xl transition shadow-xl shadow-indigo-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Créer ma première prescription</span>
        </a>
    </div>
    @else
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Patient</th>
                        <th class="text-left px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Date / ID</th>
                        <th class="text-left px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Contenu & Instructions</th>
                        <th class="text-center px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="text-right px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($prescriptions as $prescription)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($prescription->patient->prenom ?? 'P', 0, 1) }}{{ substr($prescription->patient->nom ?? 'T', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $prescription->patient->full_name ?? 'Patient Inconnu' }}</p>
                                    <p class="text-xs text-gray-500 font-medium">Né(e) le {{ $prescription->patient->date_naissance ? \Carbon\Carbon::parse($prescription->patient->date_naissance)->format('d/m/Y') : '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-700">{{ $prescription->created_at->format('d M Y') }}</span>
                                <span class="text-xs text-gray-400 font-mono">#{{ $prescription->id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 max-w-xs">
                            <p class="text-sm font-medium text-gray-700 line-clamp-2" title="{{ $prescription->medication }}">{{ \Illuminate\Support\Str::limit($prescription->medication, 60) }}</p>
                            @if($prescription->instructions)
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1 italic">{{ \Illuminate\Support\Str::limit($prescription->instructions, 40) }}</p>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($prescription->category === 'nurse')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700">
                                    Soins
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100 text-blue-700">
                                    Médicaments
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Voir les détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('external.prescriptions.pdf', $prescription->id) }}" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Télécharger PDF" target="_blank">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $prescriptions->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

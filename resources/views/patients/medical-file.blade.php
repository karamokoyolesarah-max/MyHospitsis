@extends('layouts.app')

@section('title', 'Dossier Médical - ' . $patient->full_name)

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- En-tête du patient -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">{{ $patient->full_name }}</h1>
                        <div class="flex items-center space-x-3 text-sm text-slate-500 mt-1">
                            <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $patient->ipu }}</span>
                            <span>•</span>
                            <span>{{ $patient->age }} ans</span>
                            <span>•</span>
                            <span>{{ $patient->gender }}</span>
                            <span>•</span>
                            <span>{{ $patient->blood_group ?? 'Groupe sanguin inconnu' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 font-medium transition">
                        Retour au profil
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 font-medium transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimer
                    </button>
                </div>
            </div>
            
            @if($patient->allergies)
            <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start space-x-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-red-800">Allergies connues</h3>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($patient->allergies as $allergy)
                            <span class="px-2 py-1 bg-white rounded border border-red-200 text-xs font-medium text-red-600">{{ $allergy }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Observations Cliniques -->
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Dernières Constantes & Soins
                </h2>
                
                @forelse($patient->clinicalObservations as $obs)
                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            {{ $obs->observation_datetime ? $obs->observation_datetime->format('d/m/Y H:i') : $obs->created_at->format('d/m/Y H:i') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                            {{ str_replace('_', ' ', $obs->type) }}
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-slate-800 mb-2">{{ $obs->value }}</div>
                    <div class="flex items-center text-xs text-slate-400">
                        <span class="mr-2">Par :</span>
                        @if($obs->user)
                            <span class="font-medium text-slate-600">{{ $obs->user->name }}</span>
                        @else
                            <span class="italic">Système</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center text-slate-400">
                    Aucune observation clinique enregistrée.
                </div>
                @endforelse
            </div>

            <!-- Historique Médical -->
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Dossiers & Comptes-rendus
                </h2>

                @forelse($patient->medicalRecords as $record)
                <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start border-b border-slate-50 pb-3 mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="fas fa-file-medical"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Note Médicale</div>
                                <div class="text-xs text-slate-400">
                                    {{ $record->created_at->format('d/m/Y à H:i') }} par {{ $record->recordedBy->name ?? 'Inconnu' }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('medical-records.pdf', $record->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Télécharger PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="prose prose-sm text-slate-600 max-w-none">
                        {!! nl2br(e($record->content)) !!}
                    </div>
                </div>
                @empty
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center text-slate-400">
                    Aucune historique médical disponible.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="space-y-6 mt-8">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ordonnances & Prescriptions
            </h2>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médicament / Soin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prescrit par</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($patient->prescriptions as $prescription)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prescription->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $prescription->medication }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prescription->doctor->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('prescriptions.pdf', $prescription) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    PDF
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">
                                Aucune prescription enregistrée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($patient->documents->count() > 0)
        <!-- Documents -->
        <div class="space-y-6 mt-8 avoid-break">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                Documents Joints
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($patient->documents as $doc)
                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="block p-4 bg-white border border-slate-200 rounded-xl hover:shadow-md transition group">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-10 w-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $doc->title }}</p>
                            <p class="text-xs text-slate-500">{{ $doc->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<style media="print">
    @page { size: auto; margin: 20mm; }
    nav, header, footer, .no-print { display: none !important; }
    body { background: white !important; }
    .avoid-break { page-break-inside: avoid; }
</style>
@endsection

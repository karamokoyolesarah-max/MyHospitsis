@extends('layouts.app')

@section('content')
@php
    // Configuration dynamique par service
    // SI l'utilisateur est un médecin, on utilise SON service pour l'affichage (Cardio voit Cardio).
    // SINON (Admin/Infirmière), on garde le service d'origine du dossier.
    $user = auth()->user();
    $serviceName = ($user->role === 'doctor' || $user->role === 'internal_doctor') 
        ? $user->service->name 
        : ($record->service?->name ?? 'Général');
    $serviceConfig = [
        'Urgences' => [
            'color' => 'red',
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-700',
            'icon' => '🚨',
            'title' => 'Dossier Urgences'
        ],
        'Pédiatrie' => [
            'color' => 'pink',
            'bg' => 'bg-pink-50',
            'border' => 'border-pink-200',
            'text' => 'text-pink-700',
            'icon' => '👶',
            'title' => 'Dossier Pédiatrique'
        ],
        'Cardiologie' => [
            'color' => 'blue',
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-700',
            'icon' => '❤️',
            'title' => 'Dossier Cardiologique'
        ],
        'Maternité' => [
            'color' => 'purple',
            'bg' => 'bg-purple-50',
            'border' => 'border-purple-200',
            'text' => 'text-purple-700',
            'icon' => '🤰',
            'title' => 'Dossier Maternité'
        ],
        'Chirurgie' => [
            'color' => 'indigo',
            'bg' => 'bg-indigo-50',
            'border' => 'border-indigo-200',
            'text' => 'text-indigo-700',
            'icon' => '🔪',
            'title' => 'Dossier Chirurgical'
        ],
    ];
    
    // Trouver la config correspondante (insensible à la casse)
    $config = collect($serviceConfig)->first(function($value, $key) use ($serviceName) {
        return stripos($serviceName, $key) !== false;
    }) ?? [
        'color' => 'gray',
        'bg' => 'bg-gray-50',
        'border' => 'border-gray-200',
        'text' => 'text-gray-700',
        'icon' => '📋',
        'title' => 'Dossier Médical'
    ];

    // Configuration par défaut des tests de laboratoire (si le service n'en a pas)
    $defaultLabTests = [
        'quick_tests' => ['NFS', 'CRP', 'Glycémie', 'Créatininémie', 'Transaminases', 'Paludisme TDR'],
        'categories' => [
            'Hématologie' => ['NFS', 'VS', 'TP/TCA', 'Groupe Sanguin'],
            'Biochimie' => ['Glycémie', 'Créatininémie', 'Urée', 'Transaminases', 'Bilan Lipidique'],
            'Microbiologie' => ['TDR Palu', 'ECBU', 'Hémoculture', 'Coproculture'],
            'Imagerie' => ['Radio Thorax', 'Échographie Abdominale', 'Échographie Pelvienne'],
        ]
    ];

    // Utiliser la config du service ou la config par défaut
    $labTestsConfig = $record->service?->diagnostic_config['lab_tests'] ?? $defaultLabTests;
@endphp

<div class="p-6 {{ $config['bg'] }} min-h-screen">
    <div class="max-w-4xl mx-auto">
        {{-- Alerte de succès --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black {{ $config['text'] }} flex items-center gap-3">
                    <span class="text-4xl">{{ $config['icon'] }}</span>
                    {{ $config['title'] }}
                </h1>
                <p class="text-sm text-gray-500 font-medium mt-2">
                    <span class="font-bold text-gray-900">{{ $record->patient_name }}</span> • 
                    {{ $record->patient?->age ?? 'N/A' }} ans • 
                    IPU: <span class="font-mono text-xs">{{ $record->patient_ipu }}</span>
                </p>
            </div>
            <a href="{{ route('medical_records.index') }}" class="px-4 py-2 bg-white border-2 border-{{ $config['color'] }}-600 {{ $config['text'] }} rounded-xl font-bold hover:bg-{{ $config['color'] }}-600 hover:text-white transition-all">
                ← Retour
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6 border-t-4 border-{{ $config['color'] }}-500">
            {{-- BLOC ACTIONS : STYLE ICONES CIRCULAIRES --}}
            <div class="mb-6 p-4 bg-gray-50 border border-gray-100 rounded-xl">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Actions rapides</h4>
                <div class="flex items-center gap-3">
                    {{-- Bouton PDF (Indigo) --}}
                    <a href="{{ route('medical-records.pdf', $record->id) }}" title="Télécharger PDF" class="w-10 h-10 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition-all active:scale-90">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </a>

                    {{-- Bouton Modifier (Bleu) --}}
                    <a href="#formulaire-constantes" title="Modifier dans le carnet" class="w-10 h-10 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition-all active:scale-90">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>

                    {{-- Bouton Supprimer (Rouge) --}}
                    <form action="#" method="POST" class="inline" onsubmit="return confirm('Voulez-vous supprimer cet enregistrement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Supprimer" class="w-10 h-10 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-md transition-all active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- INFOS DE BASE (Identité) --}}
            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">IPU du Patient</p>
                    <p class="text-lg font-medium text-gray-800">{{ $record->patient_ipu }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Niveau d'Urgence</p>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $record->urgency === 'critique' ? 'bg-red-100 text-red-800' : ($record->urgency === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ strtoupper($record->urgency) }}
                    </span>
                </div>
            </div>

            {{-- PROFIL CLINIQUE DU PATIENT --}}
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
                <h3 class="text-sm font-bold text-blue-800 uppercase flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    Informations Médicales (Portail Patient)
                </h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Groupe Sanguin</p>
                        <p class="text-lg font-black text-red-600">{{ $record->blood_group ?? $record->patient?->blood_group ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Allergies</p>
                        <p class="text-sm font-bold text-gray-800">
                            @php
                                $allergies = $record->allergies ?? $record->patient?->allergies;
                            @endphp
                            @if($allergies)
                                {{ is_array($allergies) ? implode(', ', $allergies) : $allergies }}
                            @else
                                <span class="text-gray-400 font-normal italic">Aucune allergie connue</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="pt-2 border-t border-blue-100">
                    <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Antécédents / Histoire Médicale</p>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $record->medical_history ?? $record->patient?->medical_history ?? 'Aucun antécédent renseigné.' }}
                    </p>
                </div>
            </div>

            {{-- FORMULAIRE DE MISE À JOUR (Constantes + Diagnostic) --}}
            <form id="formulaire-constantes" action="{{ route('medical_records.update', $record->id) }}" method="POST">
                @csrf
                @method('PUT')

                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    Vérification des constantes & Motif
                </h3>

                {{-- CONSTANTES VITALES --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 py-4 bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Temp. (°C)</label>
                        <input type="text" name="temperature" value="{{ old('temperature', $record->temperature) }}" 
                            class="w-full text-center text-lg font-bold text-orange-600 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tension Art.</label>
                        <input type="text" name="blood_pressure" value="{{ old('blood_pressure', $record->blood_pressure) }}" 
                            class="w-full text-center text-lg font-bold text-blue-600 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pouls (BPM)</label>
                        <input type="text" name="pulse" value="{{ old('pulse', $record->pulse) }}" 
                            class="w-full text-center text-lg font-bold text-emerald-600 border border-gray-300 rounded-md focus:ring-2 focus:ring-emerald-400 outline-none">
                    </div>
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Poids (Kg)</label>
                        <input type="text" name="weight" value="{{ old('weight', $record->weight) }}" 
                            class="w-full text-center text-lg font-bold text-purple-600 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-400 outline-none">
                    </div>
                    <div class="text-center">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Taille (cm)</label>
                        <input type="text" name="height" value="{{ old('height', $record->height) }}" 
                            class="w-full text-center text-lg font-bold text-indigo-600 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-400 outline-none">
                    </div>
                </div>

                {{-- DONNÉES SPÉCIFIQUES AU SERVICE --}}
                @if($record->custom_vitals && is_array($record->custom_vitals) && count($record->custom_vitals) > 0)
                <div class="mb-6">
                    <h4 class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Signes Spécifiques ({{ $record->service?->name }})
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-orange-50/50 p-4 rounded-xl border border-orange-100">
                        @foreach($record->custom_vitals as $key => $value)
                            @if($value)
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-0.5">
                                    {{-- On essaie de trouver le label dans la config du service si possible, sinon on formate la clé --}}
                                    @php
                                        $fieldConfig = collect($record->service?->form_config)->firstWhere('name', $key);
                                        $label = $fieldConfig ? $fieldConfig['label'] : ucfirst(str_replace('_', ' ', $key));
                                    @endphp
                                    {{ $label }}
                                </label>
                                <p class="text-sm font-black text-gray-800">{{ $value }}</p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- MOTIF --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-800 underline mb-2">Motif de consultation :</label>
                    <textarea name="reason" rows="2" 
                        class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">{{ old('reason', $record->reason) }}</textarea>
                </div>

                <hr class="border-gray-100 my-6">

                {{-- FICHES SPÉCIALISÉES --}}
                @php
                    $serviceCode = $record->service ? strtoupper(strtok($record->service->code, '-')) : null;
                    $ficheMapping = [
                        'GYNE' => 'grossesse',
                        'CARD' => 'cardio',
                        'PED' => 'pediatrie',
                        'RHUM' => 'nutrition',
                        'PSY' => 'psycho',
                        'URG' => 'urgence',
                        'GEN' => 'generic',
                        'CERT' => 'certificat',
                        'ORD' => 'ordonnance',
                        'REF' => 'referral',
                        'EXT' => 'generic',
                    ];
                    
                    $ficheName = $ficheMapping[$serviceCode] ?? 'generic';
                    $meta = $record->meta ?? [];
                @endphp

                @if($ficheName)
                    <div class="mb-6">
                        @include('medical_records.fiches.' . $ficheName, ['meta' => $meta])
                    </div>
                @endif

                {{-- ESPACE PRESCRIPTION & DIAGNOSTIC DYNAMIQUE --}}
                <div class="bg-{{ $config['color'] }}-50 p-6 rounded-xl border border-{{ $config['color'] }}-100 space-y-4 shadow-inner">
                    <h3 class="font-bold {{ $config['text'] }} flex items-center gap-2 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Espace Prescription & Diagnostic
                    </h3>
                    
                    {{-- DIAGNOSTICS RAPIDES (si configurés) --}}
                    @if($record->service?->diagnostic_config && isset($record->service->diagnostic_config['quick_diagnoses']))
                    <div class="mb-4">
                        <label class="block text-sm font-bold {{ $config['text'] }} mb-2">🚀 Diagnostics Rapides</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($record->service->diagnostic_config['quick_diagnoses'] as $diagnosis)
                                <button type="button" 
                                        onclick="document.querySelector('[name=observations]').value += '{{ $diagnosis }}; '"
                                        class="px-3 py-1 bg-white border-2 border-{{ $config['color'] }}-300 {{ $config['text'] }} rounded-lg text-xs font-bold hover:bg-{{ $config['color'] }}-600 hover:text-white transition-all">
                                    {{ $diagnosis }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- CHAMPS SPÉCIFIQUES AU DIAGNOSTIC --}}
                    @if($record->service?->diagnostic_config && isset($record->service->diagnostic_config['fields']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @foreach($record->service->diagnostic_config['fields'] as $field)
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">{{ $field['label'] }}</label>
                                @if($field['type'] === 'select')
                                    <select name="diagnostic_{{ $field['name'] }}" class="w-full p-2 border border-{{ $config['color'] }}-200 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none bg-white text-sm">
                                        <option value="">Sélectionner...</option>
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $field['type'] }}" name="diagnostic_{{ $field['name'] }}" 
                                           class="w-full p-2 border border-{{ $config['color'] }}-200 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none bg-white text-sm">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- EXAMENS COMPLÉMENTAIRES (Cardiologie, etc.) --}}
                    @if($record->service?->diagnostic_config && isset($record->service->diagnostic_config['exams']))
                    <div class="mb-4">
                        <label class="block text-sm font-bold {{ $config['text'] }} mb-2">🔬 Examens Complémentaires</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($record->service->diagnostic_config['exams'] as $exam)
                                <label class="flex items-center gap-2 p-2 bg-white border border-{{ $config['color'] }}-200 rounded-lg cursor-pointer hover:bg-{{ $config['color'] }}-100 transition-all">
                                    <input type="checkbox" name="exams[]" value="{{ $exam }}" class="rounded">
                                    <span class="text-xs font-medium">{{ $exam }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-bold {{ $config['text'] }} mb-2">Observations (Diagnostic)</label>
                        <textarea name="observations" rows="3" 
                            class="w-full p-3 border border-{{ $config['color'] }}-200 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none bg-white" 
                            placeholder="Ex: TDR Palu Positif...">{{ old('observations', $record->observations) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold {{ $config['text'] }} mb-2">Ordonnance Digitale</label>
                        <textarea name="ordonnance" rows="5" 
                            class="w-full p-3 border border-{{ $config['color'] }}-200 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none bg-white" 
                            placeholder="1. Médicament A...">{{ old('ordonnance', $record->ordonnance) }}</textarea>
                    </div>

                    {{-- BOUTON PRESCRIRE UN EXAMEN --}}
                    @if($labTestsConfig)
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="document.getElementById('labTestModal').classList.remove('hidden')"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md flex items-center justify-center gap-2 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                            Prescrire un Examen
                        </button>
                    </div>
                    @endif

                    {{-- AFFICHAGE DES EXAMENS EN ATTENTE --}}
                    @php
                        $pendingTests = \App\Models\LabRequest::where('patient_vital_id', $record->id)
                            ->whereIn('status', ['pending', 'sample_received', 'in_progress', 'to_be_validated'])
                            ->get();
                        $completedTests = \App\Models\LabRequest::where('patient_vital_id', $record->id)
                            ->where('status', 'completed')
                            ->get();
                    @endphp

                    @if($pendingTests->count() > 0)
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="text-sm font-bold text-yellow-800 mb-2">⏳ Examens en cours ({{ $pendingTests->count() }})</h4>
                        <div class="space-y-2">
                            @foreach($pendingTests as $test)
                                <div class="flex items-center justify-between p-2 bg-white rounded border border-yellow-100">
                                    <span class="text-sm font-medium">{{ $test->test_name }}</span>
                                    <span class="text-xs px-2 py-1 rounded
                                        {{ $test->status === 'pending' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $test->status === 'sample_received' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $test->status === 'in_progress' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $test->status === 'to_be_validated' ? 'bg-purple-100 text-purple-700 font-bold animate-pulse' : '' }}">
                                        {{ $test->status === 'to_be_validated' ? 'À valider' : ucfirst(str_replace('_', ' ', $test->status)) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($completedTests->count() > 0)
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="text-sm font-bold text-green-800 mb-2">✅ Résultats disponibles ({{ $completedTests->count() }})</h4>
                        <div class="space-y-2">
                            @foreach($completedTests as $test)
                                <div class="p-3 bg-white rounded border border-green-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-bold">{{ $test->test_name }}</span>
                                        <span class="text-xs text-gray-500">{{ $test->completed_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($test->result)
                                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $test->result }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-{{ $config['color'] }}-600 hover:bg-{{ $config['color'] }}-700 text-white font-bold py-4 rounded-lg shadow-lg flex items-center justify-center gap-2 transition-all transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>

            {{-- DÉCISION D'ADMISSION DYNAMIQUE --}}
            @if($record->status !== 'admitted')
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="font-bold {{ $config['text'] }} mb-4 flex items-center gap-2 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $config['text'] }}"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/><path d="M8 5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2H8V5z"/></svg>
                    Décision d'Admission
                </h3>

                @php
                    // Filtrer les lits selon la configuration du service
                    $roomFilters = $record->service?->admission_config['room_filters'] ?? [];
                    $filteredBeds = $availableBeds;
                    
                    if (!empty($roomFilters)) {
                        $filteredBeds = $availableBeds->filter(function($bed) use ($roomFilters) {
                            if (!$bed->room) return false;
                            foreach ($roomFilters as $filter) {
                                if (stripos($bed->room->room_number, $filter) !== false || 
                                    stripos($bed->room->room_type ?? '', $filter) !== false) {
                                    return true;
                                }
                            }
                            return false;
                        });
                    }
                @endphp

                @if($filteredBeds->count() > 0)
                <form action="{{ route('medical_records.admit', $record->id) }}" method="POST">
                    @csrf
                    
                    {{-- Afficher les filtres actifs --}}
                    @if(!empty($roomFilters))
                    <div class="mb-4 p-3 bg-{{ $config['color'] }}-50 border border-{{ $config['color'] }}-200 rounded-lg">
                        <p class="text-xs font-bold {{ $config['text'] }} uppercase mb-2">🏥 Lits recommandés pour {{ $record->service?->name }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($roomFilters as $filter)
                                <span class="px-2 py-1 bg-white border border-{{ $config['color'] }}-300 rounded text-xs font-medium">{{ $filter }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Sélectionner un lit disponible</label>
                        <select name="bed_id" class="w-full p-3 border border-{{ $config['color'] }}-300 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none bg-white" required>
                            <option value="">Choisir un lit...</option>
                            @foreach($filteredBeds as $bed)
                                @if($bed->room)
                                    <option value="{{ $bed->id }}">
                                        {{ $bed->room->room_number }} - Lit {{ $bed->bed_number }}
                                        @if($bed->room->room_type) ({{ $bed->room->room_type }}) @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-{{ $config['color'] }}-600 hover:bg-{{ $config['color'] }}-700 text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-3 transition-all transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Admettre le Patient
                    </button>
                </form>
                @else
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 font-medium">
                        ⚠️ Aucun lit disponible correspondant aux critères du service {{ $record->service?->name }}.
                        @if(!empty($roomFilters))
                            <br><span class="text-xs">Filtres actifs : {{ implode(', ', $roomFilters) }}</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>
            @endif

            {{-- BOUTON TERMINER --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <form action="{{ route('medical_records.archive', $record->id) }}" method="POST" onsubmit="return confirm('Voulez-vous clôturer ce dossier et l\'envoyer aux archives ?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-3 transition-all transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Terminer la consultation & Archiver
                    </button>
                </form>
            </div>

        </div> {{-- Fin du bg-white --}}
    </div>
</div>

{{-- MODAL PRESCRIPTION D'EXAMENS --}}
@if($labTestsConfig)
<div id="labTestModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-{{ $config['color'] }}-600 to-{{ $config['color'] }}-500 p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black text-white flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/></svg>
                    Prescrire des Examens
                </h2>
                <button onclick="document.getElementById('labTestModal').classList.add('hidden')" 
                        class="text-white hover:bg-white/20 rounded-full p-2 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <form action="{{ route('lab.request.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="patient_vital_id" value="{{ $record->id }}">
            <input type="hidden" name="patient_ipu" value="{{ $record->patient_ipu }}">
            <input type="hidden" name="patient_name" value="{{ $record->patient_name }}">

            {{-- TESTS RAPIDES --}}
            @if(isset($labTestsConfig['quick_tests']))
            <div>
                <h3 class="text-sm font-black {{ $config['text'] }} uppercase tracking-wide mb-3">🚀 Tests Fréquents</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($labTestsConfig['quick_tests'] as $test)
                        <label class="flex items-center gap-2 p-3 bg-gray-50 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-{{ $config['color'] }}-500 hover:bg-{{ $config['color'] }}-50 transition-all">
                            <input type="checkbox" name="tests[]" value="{{ $test }}" class="rounded text-{{ $config['color'] }}-600">
                            <span class="text-sm font-medium">{{ $test }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- TESTS PAR CATÉGORIE --}}
            @if(isset($labTestsConfig['categories']))
            <div class="space-y-4">
                @foreach($labTestsConfig['categories'] as $category => $tests)
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">📋 {{ $category }}</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($tests as $test)
                                <label class="flex items-center gap-2 p-2 bg-white border border-gray-200 rounded cursor-pointer hover:bg-{{ $config['color'] }}-50 transition-all">
                                    <input type="checkbox" name="tests[]" value="{{ $test }}" class="rounded text-{{ $config['color'] }}-600">
                                    <span class="text-xs font-medium">{{ $test }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- SAISIE LIBRE --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">✍️ Autre examen (saisie libre)</label>
                <input type="text" name="custom_test" placeholder="Ex: IRM cérébrale, Ponction lombaire..." 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none">
            </div>

            {{-- INFORMATIONS CLINIQUES --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">📝 Informations cliniques pour le labo</label>
                <textarea name="clinical_info" rows="2" placeholder="Ex: Suspicion de paludisme, fièvre depuis 3 jours..." 
                          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $config['color'] }}-500 outline-none"></textarea>
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="button" 
                        onclick="document.getElementById('labTestModal').classList.add('hidden')"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 rounded-lg transition-all">
                    Annuler
                </button>
                <button type="submit" 
                        class="flex-1 bg-{{ $config['color'] }}-600 hover:bg-{{ $config['color'] }}-700 text-white font-bold py-3 rounded-lg shadow-lg transition-all">
                    Envoyer au Laboratoire
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
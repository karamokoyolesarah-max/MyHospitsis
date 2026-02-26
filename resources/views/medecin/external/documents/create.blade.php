@extends('layouts.external_doctor')

@section('title', 'Nouveau Document')
@section('page-title', 'Édition de Document Médical')

@section('content')
<div class="animate-fade-in-up">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('external.doctor.external.dashboard') }}" class="inline-flex items-center text-sm font-bold text-indigo-600 mb-8 hover:text-indigo-800 group">
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au tableau de bord
        </a>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-indigo-900/5 border border-gray-100 overflow-hidden">
            @php
                $config = [
                    'report' => ['title' => 'Compte-Rendu de Consultation', 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    'certificate' => ['title' => 'Certificat Médical', 'color' => 'purple', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    'liaison' => ['title' => 'Lettre de Liaison', 'color' => 'rose', 'icon' => 'm22 2-7 20-4-9-9-4Z'],
                    'cardio' => ['title' => 'Examen Cardiologique', 'color' => 'rose', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ];
                $current = $config[$type] ?? $config['report'];
            @endphp

            <div class="bg-gradient-to-r from-{{ $current['color'] }}-600 to-{{ $current['color'] }}-800 px-10 py-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10 flex items-center">
                    <div class="p-4 bg-white/20 rounded-2xl mr-6 backdrop-blur-md">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $current['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black uppercase tracking-tight">{{ $current['title'] }}</h2>
                        <p class="text-{{ $current['color'] }}-100 font-medium mt-1">Édition professionnelle et sécurisée</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('external.documents.store') }}" method="POST" class="p-10">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                
                <div class="space-y-10">
                    <!-- Patient & Title Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-8 bg-gray-50/50 rounded-[2rem] border-2 border-gray-100 flex-1">
                            <label class="block text-xs font-black uppercase tracking-widest text-{{ $current['color'] }}-600 mb-6">Patient Concerné</label>
                            <div class="relative">
                                <select name="patient_id" required class="w-full appearance-none rounded-2xl border-2 border-gray-100 bg-white p-5 font-bold text-gray-700 focus:ring-4 focus:ring-{{ $current['color'] }}-500/10 focus:border-{{ $current['color'] }}-500 transition-all">
                                    <option value="">Sélectionnez un patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-gray-50/50 rounded-[2rem] border-2 border-gray-100 flex-1">
                            <label class="block text-xs font-black uppercase tracking-widest text-{{ $current['color'] }}-600 mb-6">Titre du Document</label>
                            <input type="text" name="title" value="{{ $current['title'] }}" required
                                class="w-full rounded-2xl border-2 border-gray-100 bg-white p-5 font-bold text-gray-700 focus:ring-4 focus:ring-{{ $current['color'] }}-500/10 focus:border-{{ $current['color'] }}-500 transition-all">
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="space-y-4">
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-widest pl-2">Contenu du Document</label>
                        <textarea name="content" rows="15" required
                            class="w-full rounded-[2.5rem] border-2 border-gray-50 bg-gray-50/50 p-10 font-medium focus:bg-white focus:border-{{ $current['color'] }}-500 focus:ring-8 focus:ring-{{ $current['color'] }}-500/5 transition-all duration-500 whitespace-pre-line"
                            placeholder="Rédigez ici le contenu détaillé de votre document..."></textarea>
                    </div>
                </div>

                <div class="mt-16 pt-10 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-3 text-gray-400 group">
                        <svg class="w-5 h-5 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Document officiel certifié par HospitSIS</p>
                    </div>
                    
                    <button type="submit" 
                        class="w-full md:w-auto text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-sm transition-all transform active:scale-95 shadow-2xl shadow-{{ $current['color'] }}-200 bg-{{ $current['color'] }}-600 hover:bg-{{ $current['color'] }}-700">
                        Enregistrer le Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.external_doctor')

@section('title', 'Nouvelle Ordonnance')
@section('page-title', 'Prescription Numérique')

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
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-10 py-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl font-black uppercase tracking-tight">Nouvelle Ordonnance</h2>
                    <p class="text-indigo-100 font-medium mt-2">Délivrez une prescription numérique sécurisée</p>
                </div>
            </div>

            <form action="{{ route('external.prescriptions.store') }}" method="POST" class="p-10" x-data="{ category: 'pharmacy' }">
                @csrf
                <div class="space-y-10">
                    <!-- Patient Selection -->
                    <div class="p-8 bg-gray-50/50 rounded-[2rem] border-2 border-gray-100">
                        <label class="block text-xs font-black uppercase tracking-widest text-indigo-600 mb-6">Patient Concerné</label>
                        <div class="relative">
                            <select name="patient_id" required class="w-full appearance-none rounded-2xl border-2 border-gray-100 bg-white p-5 font-bold text-gray-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                <option value="">Sélectionnez un patient...</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ (isset($selected_patient) && $selected_patient->id == $patient->id) ? 'selected' : '' }}>
                                        {{ $patient->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Category Toggle -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label class="relative flex flex-col items-center p-8 bg-white rounded-[2rem] border-2 cursor-pointer transition-all duration-500 group"
                               :class="category === 'pharmacy' ? 'border-indigo-500 ring-8 ring-indigo-500/5 bg-indigo-50/30' : 'border-gray-50 hover:border-gray-200'">
                            <input type="radio" name="category" value="pharmacy" x-model="category" class="hidden">
                            <div class="w-16 h-16 rounded-2xl mb-4 flex items-center justify-center transition-all duration-500"
                                 :class="category === 'pharmacy' ? 'bg-indigo-600 text-white rotate-6' : 'bg-gray-100 text-gray-400'">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86 .517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-black text-gray-800 uppercase tracking-tight">Pharmacie</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Ordonnance patient</p>
                        </label>
                        
                        <label class="relative flex flex-col items-center p-8 bg-white rounded-[2rem] border-2 cursor-pointer transition-all duration-500 group"
                               :class="category === 'nurse' ? 'border-emerald-500 ring-8 ring-emerald-500/5 bg-emerald-50/30' : 'border-gray-50 hover:border-gray-200'">
                            <input type="radio" name="category" value="nurse" x-model="category" class="hidden">
                            <div class="w-16 h-16 rounded-2xl mb-4 flex items-center justify-center transition-all duration-500"
                                 :class="category === 'nurse' ? 'bg-emerald-600 text-white -rotate-6' : 'bg-gray-100 text-gray-400'">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <p class="text-sm font-black text-gray-800 uppercase tracking-tight">Soins Infirmiers</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Consignes de soins</p>
                        </label>
                    </div>

                    <!-- Prescription Content -->
                    <div class="grid grid-cols-1 gap-10">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-black uppercase text-gray-400 tracking-widest pl-2">Médicaments & Posologie / Consignes</label>
                                <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg uppercase tracking-wider" x-show="category === 'nurse'">Soins Infirmiers</span>
                                <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg uppercase tracking-wider" x-show="category === 'pharmacy'">Pharmacie</span>
                            </div>
                            <textarea name="medication" rows="6" required
                                class="w-full rounded-[2rem] border-2 border-gray-50 bg-gray-50/50 p-8 font-medium focus:bg-white focus:border-indigo-500 focus:ring-8 focus:ring-indigo-500/5 transition-all duration-300"
                                placeholder="Détaillez ici la liste des médicaments avec leur posologie, ou les consignes de soins pour l'infirmier..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="block text-xs font-black uppercase text-gray-400 tracking-widest pl-2">Type de Traitement</label>
                                <select name="type" class="w-full rounded-2xl border-2 border-gray-50 bg-gray-50/50 p-5 font-bold text-gray-700 focus:bg-white focus:border-indigo-500 transition-all">
                                    <option value="curatif">Curatif</option>
                                    <option value="preventif">Préventif</option>
                                    <option value="symptomatique">Symptomatique</option>
                                </select>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-xs font-black uppercase text-gray-400 tracking-widest pl-2">Durée Totale</label>
                                <input type="text" name="duration" placeholder="Ex: 10 jours" 
                                    class="w-full rounded-2xl border-2 border-gray-50 bg-gray-50/50 p-5 font-bold text-gray-700 focus:bg-white focus:border-indigo-500 transition-all">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-xs font-black uppercase text-gray-400 tracking-widest pl-2">Instructions Complémentaires</label>
                            <textarea name="instructions" rows="3" 
                                class="w-full rounded-[1.5rem] border-2 border-gray-50 bg-gray-50/50 p-6 font-medium focus:bg-white focus:border-indigo-500 transition-all"
                                placeholder="Repos, régime alimentaire, contre-indications..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-16 pt-10 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-3 text-gray-400 group">
                        <svg class="w-5 h-5 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Une version PDF sera générée automatiquement</p>
                    </div>
                    
                    <button type="submit" 
                        class="w-full md:w-auto text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-sm transition-all transform active:scale-95 shadow-2xl shadow-indigo-200"
                        :class="category === 'nurse' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700'">
                        Valider la Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900">🖋️ Validation des Résultats</h1>
            <p class="text-gray-500 mt-2 font-medium">Vérifiez et publiez les résultats saisis par les techniciens.</p>
        </div>

        @if($resultsToValidate->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-20 h-20 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Tout est à jour !</h2>
                <p class="text-gray-500">Aucun résultat en attente de validation pour le moment.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($resultsToValidate as $result)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:border-teal-300 transition-colors">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-black uppercase tracking-wider">
                                            {{ $result->test_category }}
                                        </span>
                                        <span class="text-gray-400 text-sm">•</span>
                                        <span class="text-gray-500 text-sm italic">Saisi par {{ $result->labTechnician->name ?? 'Inconnu' }}</span>
                                    </div>

                                    <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $result->test_name }}</h3>
                                    
                                    <div class="flex items-center gap-6 text-sm">
                                        <div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px]">Patient</p>
                                            <p class="text-gray-900 font-black">{{ $result->patient_name }}</p>
                                            <p class="text-gray-500 text-xs">IPU: {{ $result->patient_ipu }}</p>
                                        </div>
                                        <div class="h-8 w-px bg-gray-100"></div>
                                        <div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px]">Prescripteur</p>
                                            <p class="text-gray-900 font-bold">Dr. {{ $result->doctor->name ?? 'Inconnu' }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <p class="text-gray-400 font-bold uppercase text-[10px] mb-2">🔬 Résultat Saisi :</p>
                                        <div class="text-gray-900 font-medium whitespace-pre-wrap leading-relaxed">{{ $result->result }}</div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 min-w-[200px]">
                                    <form action="{{ route('lab.requests.validate', $result->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-6 py-4 bg-teal-600 hover:bg-teal-700 text-white font-black rounded-xl shadow-lg shadow-teal-500/30 transition-all flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Valider & Publier
                                        </button>
                                    </form>
                                    
                                    <button onclick="openEditResultModal({{ $result->id }})" class="w-full px-4 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-200 transition-all flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Ajuster le résultat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

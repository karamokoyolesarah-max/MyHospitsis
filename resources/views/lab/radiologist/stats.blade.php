@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-purple-900">📊 Statistiques & Qualité</h1>
            <p class="text-purple-500 mt-2 font-medium">Analyse de la performance de l'imagerie médicale.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-purple-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    Volume d'Examens (30 derniers jours)
                </h3>
                <div class="h-64 flex items-center justify-center border-2 border-dashed border-purple-100 rounded-xl bg-purple-50/50">
                    <p class="text-purple-400 italic font-medium">Graphique de tendance à charger...</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-purple-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Répartition par Modalité
                </h3>
                <div class="h-64 flex items-center justify-center border-2 border-dashed border-purple-100 rounded-xl bg-purple-50/50">
                    <p class="text-purple-400 italic font-medium">Graphique sectoriel à charger...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

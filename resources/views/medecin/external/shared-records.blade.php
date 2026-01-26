@extends('layouts.external_doctor')

@section('title', 'Dossiers Partagés')
@section('page-title', 'Dossiers Partagés')
@section('page-subtitle', 'Dossiers médicaux reçus des hôpitaux partenaires')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dossiers Partagés</h1>
            <p class="text-gray-500">Consultez les dossiers médicaux partagés avec vous</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Rechercher un dossier..." class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Tous les hôpitaux</option>
            </select>
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Toutes les dates</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>
        </div>
    </div>

    <!-- Records List -->
    @if($records->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun dossier partagé</h3>
        <p class="text-gray-500 max-w-md mx-auto">Les hôpitaux partenaires pourront partager des dossiers médicaux avec vous. Ils apparaîtront ici.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($records as $record)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition card-hover">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Nouveau</span>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">{{ $record->patient_name ?? 'Patient' }}</h3>
                <p class="text-gray-500 text-sm mb-4">{{ $record->hospital_name ?? 'Hôpital' }}</p>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <span class="text-gray-400 text-sm">{{ $record->shared_at ?? 'Date' }}</span>
                    <button class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">Consulter</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection

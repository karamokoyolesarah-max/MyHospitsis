@extends('layouts.external_doctor')

@section('title', 'Mes Patients')
@section('page-title', 'Mes Patients')
@section('page-subtitle', 'Gérer vos patients')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Liste des Patients</h1>
            <p class="text-gray-500">Gérez et suivez vos patients</p>
        </div>
        <button class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-indigo-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Nouveau Patient</span>
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Rechercher un patient..." class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Tous les statuts</option>
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
            </select>
        </div>
    </div>

    <!-- Patients List -->
    @if($patients->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun patient pour le moment</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">Commencez à ajouter des patients pour les suivre et gérer leurs dossiers médicaux.</p>
        <button class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Ajouter un patient</span>
        </button>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Patient</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Contact</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Dernière visite</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Statut</th>
                    <th class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($patients as $patient)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <span class="text-indigo-600 font-bold">{{ substr($patient->name ?? 'P', 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $patient->name ?? 'Patient' }}</p>
                                <p class="text-sm text-gray-500">ID: {{ $patient->id ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-700">{{ $patient->phone ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $patient->email ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $patient->last_visit ?? 'Jamais' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Actif</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen animate-fade-in">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Tableau de Bord Administratif</h1>
            <p class="text-gray-600 text-lg">{{ auth()->user()->hospital->name }}</p>
            <p class="text-sm text-gray-500 mt-1">Vue d'ensemble des services et performances</p>
        </div>
        <div class="flex space-x-3">
            <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                {{ now()->format('F Y') }}
            </span>
            <button class="bg-white text-gray-700 px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exporter
            </button>
        </div>
    </div>

    <!-- Navigation des onglets -->
    <div class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#" id="overview-tab-btn" class="tab-button border-indigo-500 text-indigo-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm active" data-tab="overview">
                    Aperçu
                </a>
                <a href="#" id="activation-tab-btn" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="activation">
                    Activation de Comptes
                </a>
                <a href="#" id="invoices-tab-btn" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="invoices">
                    Factures & Revenus
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenu de l'onglet Aperçu -->
    <div id="overview-tab" class="tab-content">

    <!-- KPI Cards with Professional Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Médecins Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full -mr-10 -mt-10 opacity-20"></div>
            <div class="flex items-center relative z-10">
                <div class="p-4 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl text-white shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Médecins Actifs</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalDoctors }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1">↗️ +12% ce mois</p>
                </div>
            </div>
        </div>

        <!-- Patients Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full -mr-10 -mt-10 opacity-20"></div>
            <div class="flex items-center relative z-10">
                <div class="p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Patients Totaux</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPatients }}</p>
                    <p class="text-xs text-blue-600 font-medium mt-1">↗️ +8% ce mois</p>
                </div>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full -mr-10 -mt-10 opacity-20"></div>
            <div class="flex items-center relative z-10">
                <div class="p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m0 10V4m-4 11h.01"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Services Médicaux</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalServices }}</p>
                    <p class="text-xs text-purple-600 font-medium mt-1">↗️ +5% ce mois</p>
                </div>
            </div>
        </div>

        <!-- Occupation Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full -mr-10 -mt-10 opacity-20"></div>
            <div class="flex items-center relative z-10">
                <div class="p-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl text-white shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Taux d'Occupation</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $occupancyRate }}%</p>
                    <p class="text-xs text-orange-600 font-medium mt-1">↗️ +3% ce mois</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Management Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Quick Actions with Enhanced Design -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Actions Rapides</h3>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-4">
                <a href="{{ route('users.create', ['role' => 'doctor']) }}" class="group flex items-center p-4 text-gray-700 bg-gradient-to-r from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-xl transition-all duration-200 border border-indigo-200/50 hover:border-indigo-300">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm mr-4 group-hover:bg-indigo-50 transition">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Ajouter un Médecin</p>
                        <p class="text-sm text-gray-600">Recruter un nouveau professionnel</p>
                    </div>
                </a>
                <a href="{{ route('services.create') }}" class="group flex items-center p-4 text-gray-700 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl transition-all duration-200 border border-green-200/50 hover:border-green-300">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm mr-4 group-hover:bg-green-50 transition">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m0 10V4m-4 11h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Créer un Service</p>
                        <p class="text-sm text-gray-600">Ajouter un nouveau département</p>
                    </div>
                </a>
                <a href="{{ route('rooms.bed-management') }}" class="group flex items-center p-4 text-gray-700 bg-gradient-to-r from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 rounded-xl transition-all duration-200 border border-orange-200/50 hover:border-orange-300">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm mr-4 group-hover:bg-orange-50 transition">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Gérer les Chambres</p>
                        <p class="text-sm text-gray-600">Administrer les lits et chambres</p>
                    </div>
                </a>
                <a href="{{ route('users.index') }}" class="group flex items-center p-4 text-gray-700 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 rounded-xl transition-all duration-200 border border-red-200/50 hover:border-red-300">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm mr-4 group-hover:bg-red-50 transition">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Gestion des Utilisateurs</p>
                        <p class="text-sm text-gray-600">Activer et gérer les comptes</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activities with Enhanced Design -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100 card-hover">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Activités Récentes du Service</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">En direct</span>
                </div>
            </div>
            <div class="space-y-4">
                <!-- Activity Item 1 -->
                <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200/50">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">Dr. Traoré</p>
                            <span class="text-xs text-gray-500">Il y a 5 min</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">A ajouté une nouvelle observation clinique pour le patient M. Diallo</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium">Observation Médicale</span>
                        </div>
                    </div>
                </div>

                <!-- Activity Item 2 -->
                <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl border border-green-200/50">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">Admin Système</p>
                            <span class="text-xs text-gray-500">Il y a 12 min</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Patient Mme. Konaté admise en cardiologie - Chambre 204</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Admission Patient</span>
                        </div>
                    </div>
                </div>

                <!-- Activity Item 3 -->
                <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl border border-purple-200/50">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">Dr. Diop</p>
                            <span class="text-xs text-gray-500">Il y a 28 min</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Rapport d'urgence traité - Intervention chirurgicale programmée</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-purple-100 text-purple-700 text-xs px-3 py-1 rounded-full font-medium">Rapport Médical</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 text-center">
                <span class="text-gray-500 text-sm">Activités mises à jour en temps réel</span>
            </div>
        </div>
    </div>
    </div>

    <!-- Contenu de l'onglet Activation de Comptes -->
    <div id="activation-tab" class="tab-content hidden">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Activation de Comptes</h3>
                <div class="flex items-center space-x-2">
                    <span class="bg-orange-100 text-orange-700 text-sm px-3 py-1 rounded-full font-medium">
                        {{ $inactiveUsers->count() }} comptes en attente
                    </span>
                </div>
            </div>

            @if($inactiveUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pro.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrit le</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inactiveUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($user->role === 'doctor') bg-blue-100 text-blue-800
                                            @elseif($user->role === 'nurse') bg-green-100 text-green-800
                                            @elseif($user->role === 'internal_doctor') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->numero_ordre)
                                            <span class="text-xs font-bold text-indigo-600">Ordre: {{ $user->numero_ordre }}</span>
                                        @elseif($user->numero_matricule)
                                            <span class="text-xs font-bold text-emerald-600">Matr: {{ $user->numero_matricule }}</span>
                                        @elseif($user->numero_diplome)
                                            <span class="text-xs font-bold text-blue-600">Dipl: {{ $user->numero_diplome }}</span>
                                        @else
                                            <span class="text-xs text-red-400 italic">Aucun ID</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $user->service->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                                Activer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun compte en attente</h3>
                    <p class="mt-1 text-sm text-gray-500">Tous les comptes sont activés.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Contenu de l'onglet Factures & Revenus -->
    <div id="invoices-tab" class="tab-content hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 text-left gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Factures & Revenus de l'Hôpital</h2>
                <p class="text-slate-500 font-medium">Suivez tous les revenus et paiements de votre hôpital.</p>
            </div>
            <button onclick="refreshInvoicesData()" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-3xl font-bold transition shadow-xl shadow-green-200 flex items-center justify-center gap-3">
                <i class="bi bi-arrow-clockwise"></i>
                Actualiser
            </button>
        </div>

        <!-- Revenue Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 text-left" id="invoiceStats">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100">
                            <th class="px-8 py-6">Facture</th>
                            <th class="px-8 py-6">Patient</th>
                            <th class="px-8 py-6 text-right">Montant Total</th>
                            <th class="px-8 py-6 text-right">Payé</th>
                            <th class="px-8 py-6 text-right">Restant</th>
                            <th class="px-8 py-6 text-center">Statut</th>
                            <th class="px-8 py-6">Date</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTable">
                        <!-- Invoices will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active classes
            tabButtons.forEach(btn => {
                btn.classList.remove('border-indigo-500', 'text-indigo-600', 'active');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Add active class to clicked button
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-indigo-500', 'text-indigo-600', 'active');

            // Show corresponding content
            const tabId = this.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.remove('hidden');

            // Load data for invoices tab
            if (tabId === 'invoices-tab') {
                refreshInvoicesData();
            }
        });
    });
});

// === INVOICES MANAGEMENT ===
function refreshInvoicesData() {
    loadInvoiceStats();
    loadInvoicesTable();
}

function loadInvoiceStats() {
    fetch(`{{ url('dashboard/invoices/stats') }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const statsContainer = document.getElementById('invoiceStats');
        statsContainer.innerHTML = '';

        const stats = [
            { label: 'Revenus Totaux', value: data.total_revenue, icon: 'bi-cash-stack', color: 'green' },
            { label: 'Montant Payé', value: data.total_paid, icon: 'bi-check-circle', color: 'blue' },
            { label: 'Montant Restant', value: data.total_pending, icon: 'bi-clock-history', color: 'orange' },
            { label: 'Factures Payées', value: data.paid_invoices, icon: 'bi-receipt', color: 'purple' }
        ];

        stats.forEach(stat => {
            const statDiv = document.createElement('div');
            statDiv.className = `card-stat bg-white rounded-3xl p-6 border border-slate-200/60 shadow-sm`;
            statDiv.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-${stat.color}-50 p-3 rounded-2xl text-${stat.color}-600">
                        <i class="bi ${stat.icon} text-xl"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-slate-900 tracking-tighter">${stat.value.toLocaleString()}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">${stat.label}</div>
            `;
            statsContainer.appendChild(statDiv);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors du chargement des statistiques', 'error');
    });
}

function loadInvoicesTable() {
    fetch(`{{ url('dashboard/invoices/data') }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById('invoicesTable');
        tableBody.innerHTML = '';

        if (data.invoices && data.invoices.length > 0) {
            data.invoices.forEach(invoice => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-blue-50/30 transition-colors';

                let statusClass = 'bg-gray-100 text-gray-800';
                if (invoice.status === 'PAYÉ') {
                    statusClass = 'bg-green-100 text-green-800';
                } else if (invoice.status === 'PARTIELLEMENT PAYÉ') {
                    statusClass = 'bg-yellow-100 text-yellow-800';
                } else {
                    statusClass = 'bg-red-100 text-red-800';
                }

                row.innerHTML = `
                    <td class="px-8 py-6">
                        <div class="font-bold text-slate-900">${invoice.invoice_number}</div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-slate-600">${invoice.patient_name}</div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="font-bold text-slate-900">${invoice.total_amount.toLocaleString()} FCFA</div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="font-medium text-green-600">${invoice.paid_amount.toLocaleString()} FCFA</div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="font-medium text-orange-600">${invoice.remaining_amount.toLocaleString()} FCFA</div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${statusClass}">
                            ${invoice.status}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm text-slate-500">${invoice.created_at}</div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-8 py-12 text-center text-slate-400">Aucune facture trouvée</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors du chargement des factures', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-xl transform transition-all duration-300 translate-x-full`;

    let bgColor, textColor, icon;
    switch(type) {
        case 'success':
            bgColor = 'bg-green-500';
            textColor = 'text-white';
            icon = 'bi-check-circle-fill';
            break;
        case 'error':
            bgColor = 'bg-red-500';
            textColor = 'text-white';
            icon = 'bi-exclamation-triangle-fill';
            break;
        case 'warning':
            bgColor = 'bg-yellow-500';
            textColor = 'text-white';
            icon = 'bi-exclamation-circle-fill';
            break;
        default:
            bgColor = 'bg-blue-500';
            textColor = 'text-white';
            icon = 'bi-info-circle-fill';
    }

    notification.classList.add(bgColor, textColor);
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="bi ${icon} text-xl"></i>
            <span class="font-bold">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection

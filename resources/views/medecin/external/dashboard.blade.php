@extends('layouts.external_doctor')

@section('title', 'Tableau de Bord - Médecin Externe')
@section('page-title', 'Tableau de Bord')
@section('page-subtitle', 'Bienvenue dans votre espace médecin')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl gradient-primary p-8 shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    Bienvenue, Dr. {{ $user->prenom ?? '' }} {{ $user->nom ?? '' }} 👋
                </h1>
                <p class="text-indigo-100 text-lg">
                    Spécialiste en <span class="font-semibold">{{ $user->specialite ?? 'Médecine' }}</span>
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <div class="w-2 h-2 {{ $user->statut === 'actif' ? 'bg-green-400' : 'bg-yellow-400' }} rounded-full {{ $user->statut === 'actif' ? 'animate-pulse' : '' }}"></div>
                        <span class="text-sm text-white font-medium">Compte {{ ucfirst($user->statut ?? 'En attente') }}</span>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm text-white font-medium">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 hidden md:block">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Patients Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 gradient-info rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_patients'] }}</h3>
            <p class="text-gray-500 font-medium">Patients suivis</p>
        </div>

        <!-- Prestations Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 gradient-teal rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_prestations'] }}</h3>
            <p class="text-gray-500 font-medium">Prestations</p>
        </div>

        <!-- Prescriptions Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 gradient-success rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_prescriptions'] }}</h3>
            <p class="text-gray-500 font-medium">Prescriptions</p>
        </div>

        <!-- Appointments Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 gradient-warning rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_appointments'] }}</h3>
            <p class="text-gray-500 font-medium">Rendez-vous</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Quick Actions -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Actions Rapides
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('external.patients') }}" class="group flex items-center space-x-4 p-5 bg-gray-50 hover:bg-indigo-50 rounded-xl border border-gray-100 hover:border-indigo-200 transition-all duration-300">
                        <div class="w-12 h-12 gradient-primary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="text-gray-900 font-semibold">Mes Patients</h3>
                            <p class="text-gray-500 text-sm">Gérer mes patients</p>
                        </div>
                    </a>

                    <a href="{{ route('external.prestations') }}" class="group flex items-center space-x-4 p-5 bg-gray-50 hover:bg-teal-50 rounded-xl border border-gray-100 hover:border-teal-200 transition-all duration-300">
                        <div class="w-12 h-12 gradient-teal rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="text-gray-900 font-semibold">Mes Prestations</h3>
                            <p class="text-gray-500 text-sm">Définir mes tarifs</p>
                        </div>
                    </a>

                    <a href="{{ route('external.appointments') }}" class="group flex items-center space-x-4 p-5 bg-gray-50 hover:bg-amber-50 rounded-xl border border-gray-100 hover:border-amber-200 transition-all duration-300">
                        <div class="w-12 h-12 gradient-warning rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="text-gray-900 font-semibold">Rendez-vous</h3>
                            <p class="text-gray-500 text-sm">Voir mes RDV</p>
                        </div>
                    </a>

                    <a href="{{ route('external.shared-records') }}" class="group flex items-center space-x-4 p-5 bg-gray-50 hover:bg-rose-50 rounded-xl border border-gray-100 hover:border-rose-200 transition-all duration-300">
                        <div class="w-12 h-12 gradient-rose rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="text-gray-900 font-semibold">Dossiers Partagés</h3>
                            <p class="text-gray-500 text-sm">Dossiers reçus</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon Profil
                </h2>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-20 h-20 gradient-primary rounded-2xl flex items-center justify-center shadow-lg mb-4">
                        <span class="text-2xl font-bold text-white">
                            {{ substr($user->prenom ?? 'D', 0, 1) }}{{ substr($user->nom ?? 'R', 0, 1) }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Dr. {{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</h3>
                    <p class="text-indigo-600 font-medium">{{ $user->specialite ?? 'Médecin' }}</p>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="text-sm text-gray-700 truncate">{{ $user->email ?? 'Non défini' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Téléphone</p>
                            <p class="text-sm text-gray-700">{{ $user->telephone ?? 'Non défini' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Statut</p>
                            <p class="text-sm font-medium {{ $user->statut === 'actif' ? 'text-green-600' : 'text-amber-600' }}">
                                {{ ucfirst($user->statut ?? 'En attente') }}
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('external.profile') }}" class="w-full mt-6 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Modifier le profil</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    @if(!$user->hasPlanActive())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-amber-800">Rechargez votre compte</h3>
                <p class="text-amber-700 mt-1">Pour recevoir des demandes de rendez-vous et accéder à toutes les fonctionnalités, veuillez recharger votre compte.</p>
                <a href="{{ route('external.recharge') }}" class="inline-flex items-center space-x-2 mt-4 px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Recharger maintenant</span>
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

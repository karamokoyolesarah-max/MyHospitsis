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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Patients Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-info rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_patients'] }}</h3>
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Patients suivis</p>
        </div>

        <!-- Appointments Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-warning rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_appointments'] }}</h3>
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Rendez-vous</p>
        </div>

        <!-- Prescriptions Card -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-success rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_prescriptions'] }}</h3>
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Ordonnances</p>
        </div>

        <!-- Revenue Card (NEW) -->
        <div class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-teal rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} <span class="text-xs text-gray-400">F</span></h3>
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Volume d'Affaire</p>
        </div>

        <!-- Balance Card (NEW) -->
        <div class="group bg-indigo-600 rounded-2xl p-6 shadow-indigo-200 shadow-lg hover:shadow-indigo-300 transition-all duration-300 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">{{ number_format($user->balance ?? 0, 0, ',', ' ') }} <span class="text-xs text-indigo-200">F</span></h3>
            <p class="text-indigo-100 text-xs font-medium uppercase tracking-wider">Solde Disponible</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Quick Actions & Agenda -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Outils Médicaux & Actions Rapides
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- New Report -->
                    <a href="{{ route('external.documents.create', ['type' => 'report']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-[2rem] border-2 border-transparent hover:border-blue-200 transition-all duration-500 overflow-hidden">
                        <div class="absolute inset-0 bg-blue-50/50 group-hover:bg-blue-100/80 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-xl shadow-blue-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-blue-900 font-black uppercase tracking-tight text-sm">Nouveau Rapport</h3>
                            <p class="text-blue-600 text-[10px] font-bold uppercase mt-2 tracking-widest opacity-70 group-hover:opacity-100 transition-opacity">Consultation générique</p>
                        </div>
                    </a>

                    <!-- Medical Certificate -->
                    <a href="{{ route('external.documents.create', ['type' => 'certificate']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-[2rem] border-2 border-transparent hover:border-purple-200 transition-all duration-500 overflow-hidden">
                        <div class="absolute inset-0 bg-purple-50/50 group-hover:bg-purple-100/80 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500 shadow-xl shadow-purple-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h3 class="text-purple-900 font-black uppercase tracking-tight text-sm">Certificat Médical</h3>
                            <p class="text-purple-600 text-[10px] font-bold uppercase mt-2 tracking-widest opacity-70 group-hover:opacity-100 transition-opacity">Repos, aptitude...</p>
                        </div>
                    </a>

                    <!-- Digital Prescription -->
                    <a href="{{ route('external.prescriptions.create') }}" class="group relative flex flex-col items-center text-center p-8 rounded-[2rem] border-2 border-transparent hover:border-emerald-200 transition-all duration-500 overflow-hidden">
                        <div class="absolute inset-0 bg-emerald-50/50 group-hover:bg-emerald-100/80 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-xl shadow-emerald-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-emerald-900 font-black uppercase tracking-tight text-sm">Ordonnance</h3>
                            <p class="text-emerald-600 text-[10px] font-bold uppercase mt-2 tracking-widest opacity-70 group-hover:opacity-100 transition-opacity">Numérique & PDF</p>
                        </div>
                    </a>
                </div>
                
                <!-- Sub-row actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <a href="{{ route('external.documents.create', ['type' => 'liaison']) }}" class="flex items-center space-x-5 p-5 bg-white hover:bg-rose-50 rounded-[1.5rem] border-2 border-gray-100 hover:border-rose-100 transition-all duration-300 shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-rose-200 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 uppercase tracking-tight text-xs">Lettre de Liaison</h4>
                            <p class="text-gray-400 text-[10px] font-bold uppercase mt-1">Référence patient</p>
                        </div>
                    </a>
                    <a href="{{ route('external.prestations') }}" class="flex items-center space-x-5 p-5 bg-white hover:bg-teal-50 rounded-[1.5rem] border-2 border-gray-100 hover:border-teal-100 transition-all duration-300 shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-200 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 uppercase tracking-tight text-xs">Mes Prestations</h4>
                            <p class="text-gray-400 text-[10px] font-bold uppercase mt-1">Ajuster mes tarifs</p>
                        </div>
                    </a>
                </div>

                <!-- Specialized Fiches Shortcuts (NEW) -->
                <div class="mt-10">
                    <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-8 h-[1px] bg-gray-200 mr-3"></span>
                        Examens Spécialisés
                        <span class="flex-1 h-[1px] bg-gray-200 ml-3"></span>
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('external.documents.create', ['type' => 'cardio']) }}" class="p-4 bg-white border-2 border-gray-50 rounded-2xl flex items-center space-x-3 hover:border-rose-200 hover:bg-rose-50 transition-all duration-300 group">
                            <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm group-hover:scale-125 transition-transform"></span>
                            <span class="text-[11px] font-black text-gray-600 uppercase tracking-tight">Cardiologie</span>
                        </a>
                        <a href="{{ route('external.documents.create', ['type' => 'pedio']) }}" class="p-4 bg-white border-2 border-gray-50 rounded-2xl flex items-center space-x-3 hover:border-blue-200 hover:bg-blue-50 transition-all duration-300 group">
                            <span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm group-hover:scale-125 transition-transform"></span>
                            <span class="text-[11px] font-black text-gray-600 uppercase tracking-tight">Pédiatrie</span>
                        </a>
                        <a href="{{ route('external.documents.create', ['type' => 'nutrition']) }}" class="p-4 bg-white border-2 border-gray-50 rounded-2xl flex items-center space-x-3 hover:border-green-200 hover:bg-green-50 transition-all duration-300 group">
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-sm group-hover:scale-125 transition-transform"></span>
                            <span class="text-[11px] font-black text-gray-600 uppercase tracking-tight">Nutrition</span>
                        </a>
                        <a href="{{ route('external.documents.create', ['type' => 'psy']) }}" class="p-4 bg-white border-2 border-gray-50 rounded-2xl flex items-center space-x-3 hover:border-purple-200 hover:bg-purple-50 transition-all duration-300 group">
                            <span class="w-3 h-3 rounded-full bg-purple-500 shadow-sm group-hover:scale-125 transition-transform"></span>
                            <span class="text-[11px] font-black text-gray-600 uppercase tracking-tight">Psychologie</span>
                        </a>
                    </div>
                </div>
            </div>
                </div>
            </div>

            <!-- Today's Agenda (NEW) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Agenda du Jour
                    </h2>
                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase rounded-full tracking-wider">
                        {{ $todayAppointments->count() }} RDV
                    </span>
                </div>
                <div class="p-0 overflow-x-auto">
                    @if($todayAppointments->isNotEmpty())
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 text-[10px] items-center uppercase font-black tracking-widest border-b border-gray-100">
                                <th class="px-6 py-4">Heure</th>
                                <th class="px-6 py-4">Patient</th>
                                <th class="px-6 py-4">Lieu</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($todayAppointments as $app)
                            <tr class="hover:bg-gray-50/80 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-gray-900">{{ $app->appointment_datetime->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center text-[10px] font-bold text-white mr-3">
                                            {{ substr($app->patient->first_name ?? 'P', 0, 1) }}{{ substr($app->patient->name ?? '', 0, 1) }}
                                        </div>
                                        <div class="text-sm">
                                            <p class="font-bold text-gray-900">{{ $app->patient->full_name }}</p>
                                            <p class="text-gray-500 text-xs">{{ $app->patient->phone ?? 'Pas de tél' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600 truncate max-w-[150px]">{{ $app->home_address ?? 'Domicile patient' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'En attente'],
                                            'accepted' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Accepté'],
                                            'on_the_way' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'label' => 'En route'],
                                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Terminé'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Annulé'],
                                        ];
                                        $config = $statusConfig[$app->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $app->status];
                                    @endphp
                                    <span class="px-2.5 py-1 {{ $config['bg'] }} {{ $config['text'] }} text-[10px] font-bold rounded-lg">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('external.appointments') }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs">Voir</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Aucun rendez-vous prévu pour aujourd'hui.</p>
                        <p class="text-gray-400 text-sm mt-1">Les nouveaux RDV apparaîtront ici.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Profile Summary & Activity -->
        <div class="space-y-6">
            <!-- Profile Summary (Mini) -->
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

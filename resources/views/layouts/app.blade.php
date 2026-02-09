<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HospitSIS') - Système d'Information de Santé</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }

        /* CORRECTION DES DEUX TRAITS (Scrollbar) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #111827;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #374151 #111827;
        }

        /* Professional gradient backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        /* Smooth animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Professional card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">




    <!-- Top Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 z-30">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none md:hidden">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden md:block">
                    <h2 class="text-lg font-semibold text-gray-800">Tableau de Bord</h2>
                    <p class="text-sm text-gray-500">Système d'Information de Santé</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @if(Auth::check() && Auth::user())
                <!-- Dynamic Notifications -->
                @php
                    $notifUser = Auth::user();
                    $notifQuery = \App\Models\PatientVital::where('hospital_id', $notifUser->hospital_id)
                        ->whereIn('status', ['active', 'consulting']);
                    
                    if ($notifUser->role === 'doctor' || $notifUser->role === 'internal_doctor') {
                        $notifQuery->where('doctor_id', $notifUser->id);
                    }
                    
                    $notifCount = $notifQuery->count();
                    $recentNotifs = $notifQuery->latest()->limit(5)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none relative">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if($notifCount > 0)
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center font-bold animate-pulse">{{ $notifCount }}</span>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 z-50 overflow-hidden">
                        <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Dossiers Reçus</h3>
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded text-[10px] font-black">{{ $notifCount }}</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($recentNotifs as $notif)
                                <a href="{{ route('medical-records.show', $notif->id) }}" class="block p-4 border-b border-gray-50 hover:bg-blue-50/50 transition-colors">
                                    <div class="flex gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs">
                                            {{ substr($notif->patient_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ $notif->patient_name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{ $notif->urgency }} · {{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Aucune alerte</p>
                                </div>
                            @endforelse
                        </div>
                        @if($notifCount > 0)
                            <a href="{{ route('medical_records.index') }}" class="block p-3 text-center bg-gray-50 text-[10px] font-black text-blue-600 uppercase tracking-widest hover:bg-blue-50 transition-colors">
                                Voir tous les dossiers
                            </a>
                        @endif
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none">
                        <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-white">{{ substr(Auth::user()?->name ?? 'U', 0, 2) }}</span>
                        </div>
                        <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()?->name ?? 'User' }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()?->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()?->getRoleLabel() ?? 'Role' }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                        <div class="border-t border-gray-200 mt-2 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </header>

    <div class="flex h-screen overflow-hidden">
        
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="flex flex-col bg-gray-900 text-white transition-all duration-300 ease-in-out z-20 shadow-xl h-screen">
            
            <div class="flex items-center justify-between px-4 py-6 border-b border-gray-800 flex-shrink-0">
                <div x-show="sidebarOpen" class="flex items-center space-x-3 overflow-hidden">
                    <div class="min-w-[40px] w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-900/20 overflow-hidden">
                        @if(Auth::user()?->hospital?->logo)
                            <img src="{{ asset('storage/' . Auth::user()->hospital->logo) }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="truncate">
                        <h1 class="text-lg font-bold tracking-tight truncate">{{ Auth::user()?->hospital?->name ?? 'HospitSIS' }}</h1>
                        <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">{{ Auth::user()?->hospital ? 'Espace Hospitalier' : 'Medical Suite' }}</p>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-800 transition focus:outline-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7" />
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            @auth
                @if(auth()->user()?->isDoctor())
                    <div class="pb-4">
                        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Menu Principal</p>
                    </div>
                

                    {{-- TABLEAU DE BORD --}}
                    <a href="{{ route('medecin.dashboard') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('medecin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Tableau de bord</span>
                    </a>

                    {{-- DOSSIERS MÉDICAUX --}}
                    <a href="{{ route('medical_records.index') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('medical_records.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="sidebarOpen" class="font-semibold">Dossiers reçus</span>
                        </div>
                        {{-- Badge dynamique pour les dossiers actifs --}}
                        @php 
                            $badgeUser = auth()->user();
                            $activeCountQuery = \App\Models\PatientVital::whereIn('status', ['active', 'consulting'])
                                ->where('hospital_id', $badgeUser->hospital_id);
                            
                            if ($badgeUser->role === 'doctor' || $badgeUser->role === 'internal_doctor') {
                                $activeCountQuery->where('doctor_id', $badgeUser->id);
                            }
                            $activeCount = $activeCountQuery->count();
                        @endphp
                        @if($activeCount > 0)
                            <span x-show="sidebarOpen" class="bg-red-500 text-[10px] px-2 py-0.5 rounded-lg font-black text-white">{{ $activeCount }}</span>
                        @endif
                    </a>

                    {{-- ARCHIVES --}}
                    <a href="{{ route('medical_records.archives') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('medical_records.archives') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Archives</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Patientèle</p>
                    </div>

                    {{-- RENDEZ-VOUS --}}
                    <a href="{{ route('appointments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Rendez-vous</span>
                    </a>

                    {{-- PATIENTS --}}
                    <a href="{{ route('patients.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Patients</span>
                    </a>
                @endif

                {{-- MENU TECHNICIEN DE LABORATOIRE --}}
                {{-- MENU TECHNICIEN DE LABORATOIRE --}}
                @if(auth()->user()?->role === 'lab_technician')
                    <div class="pt-4 pb-2">
                        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Laboratoire</p>
                    </div>

                    {{-- TABLEAU DE BORD (Résumé) --}}
                    <a href="{{ route('lab.dashboard') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.dashboard') && !request()->has('filter') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <div class="{{ request()->routeIs('lab.dashboard') && !request()->has('filter') ? 'text-white' : 'text-teal-500 group-hover:text-white' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </div>
                        <span x-show="sidebarOpen" class="font-semibold tracking-wide text-sm">Tableau de bord</span>
                    </a>

                    {{-- ANALYSES EN COURS --}}
                     <a href="{{ route('lab.worklist') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.worklist') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <div class="{{ request()->routeIs('lab.worklist') ? 'text-white' : 'text-teal-500 group-hover:text-white' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <span x-show="sidebarOpen" class="font-semibold tracking-wide text-sm">Analyses en cours</span>
                    </a>

                    {{-- HISTORIQUE --}}
                     <a href="{{ route('lab.history') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.history') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <div class="{{ request()->routeIs('lab.history') ? 'text-white' : 'text-gray-500 group-hover:text-white' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span x-show="sidebarOpen" class="font-semibold tracking-wide text-sm">Historique</span>
                    </a>

                    {{-- STOCK --}}
                     <a href="{{ route('lab.inventory.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.inventory.index') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <div class="{{ request()->routeIs('lab.inventory.index') ? 'text-white' : 'text-gray-500 group-hover:text-white' }} transition-colors">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span x-show="sidebarOpen" class="font-semibold tracking-wide text-sm">Stock & Matériel</span>
                    </a>
                @endif
                @if(auth()->user()?->role === 'cashier')
                     <div class="pb-4">
                        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Caisse</p>
                    </div>

                    <a href="{{ route('cashier.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Tableau de bord</span>
                    </a>

                    <a href="{{ route('cashier.closing.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span x-show="sidebarOpen" class="font-semibold">Clôture de Caisse</span>
                    </a>
                @endif

                {{-- BIOLOGIE MÉDICALE (Visible pour Biologistes et Techniciens Labo) --}}
                @if(auth()->user()->role === 'doctor_lab' || auth()->user()->role === 'lab_technician')
                    <div class="px-6 py-4">
                        <h3 x-show="sidebarOpen" class="text-xs uppercase text-gray-500 font-semibold tracking-wider mb-3">Biologie Médicale</h3>
                        <div class="space-y-1">
                            @if(auth()->user()->role === 'doctor_lab')
                                <a href="{{ route('lab.biologist.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.biologist.dashboard') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.biologist.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Tableau de bord
                                </a>
                                <a href="{{ route('lab.biologist.validation') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.biologist.validation') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.biologist.validation') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Validation <span class="ml-auto bg-amber-100 text-amber-800 py-0.5 px-2 rounded-full text-xs font-bold">{{ \App\Models\LabRequest::where('hospital_id', auth()->user()->hospital_id)->where('status', 'to_be_validated')->count() }}</span>
                                </a>
                            @else
                                <a href="{{ route('lab.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.dashboard') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Tableau de bord
                                </a>
                                <a href="{{ route('lab.worklist') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.worklist') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.worklist') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    Liste de travail
                                </a>
                            @endif
                            
                            <a href="{{ route('lab.history') }}" 
                               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.history') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.history') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Historique
                            </a>
                            <a href="{{ route('lab.inventory.index') }}" 
                               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.inventory.index') ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.inventory.index') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Stock
                            </a>
                        </div>
                    </div>
                @endif

                {{-- IMAGERIE MÉDICALE (Visible pour Radiologues et Techniciens Radio) --}}
                @if(auth()->user()->role === 'doctor_radio' || auth()->user()->role === 'radio_technician')
                    <div class="px-6 py-4">
                        <h3 x-show="sidebarOpen" class="text-xs uppercase text-gray-500 font-semibold tracking-wider mb-3">Imagerie Médicale</h3>
                        <div class="space-y-1">
                            @if(auth()->user()->role === 'doctor_radio')
                                <a href="{{ route('lab.radiologist.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radiologist.dashboard') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radiologist.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Tableau de bord
                                </a>
                                <a href="{{ route('lab.radiologist.validation') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radiologist.validation') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radiologist.validation') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Validation 
                                    {{-- Count could be optimized with a View Composer --}}
                                    <span class="ml-auto bg-amber-100 text-amber-800 py-0.5 px-2 rounded-full text-xs font-bold">{{ \App\Models\LabRequest::where('hospital_id', auth()->user()->hospital_id)->where('test_category', 'imagerie')->where('status', 'to_be_validated')->count() }}</span>
                                </a>
                                <a href="{{ route('lab.history') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.history') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.history') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Historique
                                </a>
                                <a href="{{ route('lab.radiologist.stats') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radiologist.stats') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radiologist.stats') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Statistiques
                                </a>
                            @endif

                            @if(auth()->user()->role === 'radio_technician')
                                <a href="{{ route('lab.radio_technician.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radio_technician.dashboard') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radio_technician.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Tableau de bord
                                </a>
                                <a href="{{ route('lab.radio_technician.worklist') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radio_technician.worklist') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radio_technician.worklist') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    Liste de travail
                                </a>
                                <a href="{{ route('lab.radio_technician.inventory') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radio_technician.inventory') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radio_technician.inventory') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Stock
                                </a>
                                <a href="{{ route('lab.radio_technician.history') }}" 
                                   class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('lab.radio_technician.history') ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('lab.radio_technician.history') ? 'text-white' : 'text-gray-400 group-hover:text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Historique
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if(auth()->user() && auth()->user()->role === 'admin')
    <div class="pb-4 pt-4">
        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Administration</p>
    </div>

    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Dashboard Admin</span>
    </a>

    <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('users.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Médecins & Staff</span>
    </a>

    <a href="{{ route('patients.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('patients.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Patients</span>
    </a>

    <a href="{{ route('admin.finance.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.finance.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <span x-show="sidebarOpen" class="font-black tracking-tight text-sm">Gestion de la Caisse</span>
    </a>

    <a href="{{ route('prestations.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('prestations.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Prestations</span>
    </a>

    <a href="{{ route('admin.insurance.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.insurance.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span x-show="sidebarOpen" class="font-black tracking-tight text-sm">Assurances</span>
    </a>
@endif
            @endif
            </nav>

            {{-- FOOTER SIDEBAR --}}
            <div class="border-t border-gray-800 p-4 bg-gray-900/50">
                @auth
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="min-w-[40px] w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-blue-500 flex items-center justify-center shadow-lg shadow-blue-900/20">
                            <span class="text-sm font-black uppercase text-white">{{ substr(Auth::user()?->name ?? 'U', 0, 2) }}</span>
                        </div>
                        <div x-show="sidebarOpen" class="truncate">
                            <p class="text-sm font-bold truncate text-white">{{ Auth::user()?->name ?? 'User' }}</p>
                            <p class="text-[10px] text-blue-400 font-black uppercase tracking-tighter">{{ Auth::user()?->role ?? 'Role' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-red-500/20 rounded-xl transition text-gray-500 hover:text-red-500 group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto bg-gray-50 custom-scrollbar">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-20 right-6 z-50 animate-fade-in">
                        <div class="bg-teal-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="font-medium">{{ session('success') }}</span>
                            <button @click="show = false" class="ml-4 text-teal-200 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="fixed top-20 right-6 z-50 animate-fade-in">
                        <div class="bg-red-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">{{ session('error') }}</span>
                            <button @click="show = false" class="ml-4 text-red-200 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">HospitSIS</p>
                        <p class="text-xs text-gray-500">© 2024 - Système d'Information de Santé</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>Version 2.1.0</span>
                <span>•</span>
                <a href="{{ route('help') }}" class="hover:text-blue-600 transition">Aide</a>
                <span>•</span>
                <a href="{{ route('contact') }}" class="hover:text-blue-600 transition">Contact</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
 </body>
</html>

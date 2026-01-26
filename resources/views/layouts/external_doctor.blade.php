<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Médecin') - HospitSIS</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        * { font-family: 'Inter', sans-serif; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9; }

        /* Gradient backgrounds */
        .gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .gradient-success { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
        .gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
        .gradient-info { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); }
        .gradient-rose { background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%); }
        .gradient-teal { background: linear-gradient(135deg, #14b8a6 0%, #5eead4 100%); }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .pulse-soft { animation: pulse-soft 2s infinite; }

        /* Sidebar nav link transitions */
        .nav-link {
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            transform: translateX(4px);
        }
        .nav-link.active {
            background: linear-gradient(90deg, #eef2ff 0%, transparent 100%);
            border-left: 3px solid #6366f1;
            color: #4f46e5;
        }

        /* Card hover */
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-72' : 'w-20'" 
            class="flex flex-col bg-white text-gray-800 transition-all duration-300 ease-in-out z-20 shadow-xl h-screen border-r border-gray-100">
            
            <!-- Logo Section -->
            <div class="flex items-center justify-between px-5 py-5 border-b border-gray-100 flex-shrink-0">
                <div x-show="sidebarOpen" x-transition class="flex items-center space-x-3 overflow-hidden">
                    <div class="min-w-[48px] w-12 h-12 gradient-primary rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="truncate">
                        <h1 class="text-xl font-bold text-gray-900">HospitSIS</h1>
                        <p class="text-[10px] text-indigo-600 font-bold uppercase tracking-widest">Médecin Externe</p>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 rounded-xl hover:bg-gray-100 transition focus:outline-none">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7" />
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                <!-- Section Title -->
                <div class="pb-3" x-show="sidebarOpen">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Navigation</p>
                </div>

                <!-- Dashboard -->
                <a href="{{ route('external.doctor.external.dashboard') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('external.doctor.external.dashboard') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Tableau de Bord</span>
                </a>

                <!-- Section Patients -->
                <div class="pt-5 pb-3" x-show="sidebarOpen">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gestion Patients</p>
                </div>

                <!-- Mes Patients -->
                <a href="{{ route('external.patients') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.patients') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Mes Patients</span>
                </a>

                <!-- Dossiers Partagés -->
                <a href="{{ route('external.shared-records') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.shared-records') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Dossiers Partagés</span>
                </a>

                <!-- Section Activité -->
                <div class="pt-5 pb-3" x-show="sidebarOpen">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Activité</p>
                </div>

                <!-- Prescriptions -->
                <a href="{{ route('external.prescriptions') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.prescriptions') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Prescriptions</span>
                </a>

                <!-- Rendez-vous -->
                <a href="{{ route('external.appointments') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.appointments') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Rendez-vous</span>
                </a>

                <!-- Prestations -->
                <a href="{{ route('external.prestations') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.prestations') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Mes Prestations</span>
                </a>

                <!-- Section Compte -->
                <div class="pt-5 pb-3" x-show="sidebarOpen">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mon Compte</p>
                </div>

                <!-- Rechargement -->
                <a href="{{ route('external.recharge') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.recharge') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Recharger</span>
                </a>

                <!-- Mon Profil -->
                <a href="{{ route('external.profile') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.profile') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Mon Profil</span>
                </a>

                <!-- Paramètres -->
                <a href="{{ route('external.settings') }}" 
                   class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('external.settings') ? 'active bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="min-w-[24px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-semibold">Paramètres</span>
                </a>
            </nav>

            <!-- User Footer -->
            <div class="border-t border-gray-100 p-4 bg-gray-50/50">
                @php
                    $externalUser = Auth::guard('medecin_externe')->user();
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="min-w-[44px] w-11 h-11 rounded-xl gradient-primary flex items-center justify-center shadow-lg overflow-hidden relative">
                            @if($externalUser->profile_photo_path)
                                <img src="{{ asset('storage/' . $externalUser->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-black uppercase text-white">
                                    {{ substr($externalUser->prenom ?? 'D', 0, 1) }}{{ substr($externalUser->nom ?? 'R', 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <div x-show="sidebarOpen" class="truncate">
                            <p class="text-sm font-bold truncate text-gray-900">Dr. {{ $externalUser->prenom ?? '' }} {{ $externalUser->nom ?? '' }}</p>
                            <p class="text-[10px] text-indigo-600 font-bold uppercase tracking-tighter">{{ $externalUser->specialite ?? 'Spécialiste' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('external.logout') }}" x-show="sidebarOpen">
                        @csrf
                        <button type="submit" class="p-2.5 hover:bg-red-50 rounded-xl transition text-gray-400 hover:text-red-500 group" title="Déconnexion">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Header with Availability & Balance -->
            <header class="bg-white border-b border-gray-100 z-30 flex-shrink-0 shadow-sm">
                <!-- Status Bar -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <!-- Availability Toggle -->
                            @php
                                $isPlanActive = $externalUser->hasPlanActive();
                                $isActuallyAvailable = $externalUser->is_available && $isPlanActive;
                            @endphp
                            <form method="POST" action="{{ route('external.toggle-availability') }}" class="flex items-center space-x-3">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-full transition-all {{ $isActuallyAvailable ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-600 hover:bg-gray-700' }}">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $isActuallyAvailable ? 'bg-white animate-pulse' : 'bg-gray-400' }}"></div>
                                    <span class="text-white text-sm font-semibold">
                                        {{ $isActuallyAvailable ? 'Disponible' : 'Indisponible' }}
                                    </span>
                                </button>
                            </form>
                            <div class="hidden md:block h-6 w-px bg-white/30"></div>
                            <span class="hidden md:block text-white/80 text-sm">
                                @if($isActuallyAvailable)
                                    Vous recevez des demandes de RDV
                                @elseif(!$isPlanActive)
                                    <span class="text-yellow-200">Compte inactif - Recharger pour activer</span>
                                @else
                                    Activez pour recevoir des RDV
                                @endif
                            </span>
                        </div>
                        
                        <!-- Balance Display -->
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                                <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white font-bold">{{ number_format($externalUser->balance ?? 0, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <a href="{{ route('external.recharge') }}" class="flex items-center space-x-2 bg-white text-indigo-600 rounded-full px-4 py-2 font-semibold text-sm hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Recharger</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Page Title Bar -->
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none md:hidden">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">@yield('page-title', 'Tableau de Bord')</h2>
                            <p class="text-sm text-gray-500">@yield('page-subtitle', 'Espace Médecin Externe')</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="p-2.5 rounded-xl hover:bg-gray-100 transition focus:outline-none relative">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute -top-1 -right-1 h-5 w-5 bg-indigo-500 rounded-full text-[10px] text-white flex items-center justify-center font-bold">0</span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                                </div>
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-gray-400 text-sm">Aucune notification</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Quick Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 transition focus:outline-none">
                                <div class="w-10 h-10 gradient-primary rounded-xl flex items-center justify-center overflow-hidden relative">
                                    @if($externalUser->profile_photo_path)
                                        <img src="{{ asset('storage/' . $externalUser->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-sm font-bold text-white">
                                            {{ substr($externalUser->prenom ?? 'D', 0, 1) }}{{ substr($externalUser->nom ?? 'R', 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-gray-900">Dr. {{ $externalUser->prenom ?? '' }}</p>
                                    <p class="text-xs text-gray-500">{{ $externalUser->specialite ?? 'Médecin' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 z-50">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="text-sm font-bold text-gray-900">Dr. {{ $externalUser->prenom ?? '' }} {{ $externalUser->nom ?? '' }}</p>
                                    <p class="text-xs text-indigo-600">{{ $externalUser->email ?? '' }}</p>
                                </div>
                                <div class="p-2">
                                    <a href="{{ route('external.profile') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-sm font-medium">Mon Profil</span>
                                    </a>
                                    <a href="{{ route('external.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium">Paramètres</span>
                                    </a>
                                </div>
                                <div class="border-t border-gray-100 p-2">
                                    <form method="POST" action="{{ route('external.logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-red-500 hover:bg-red-50 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="text-sm font-medium">Déconnexion</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-6 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            @if(session('info'))
            <div class="mx-6 mt-4">
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 custom-scrollbar p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

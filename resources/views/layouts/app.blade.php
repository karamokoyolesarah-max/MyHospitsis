<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HospitSIS') - Système d'Information de Santé</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
<body class="bg-gray-100 font-sans antialiased">

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
                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none relative">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                                <p class="text-sm text-gray-800">Nouveau patient admis</p>
                                <p class="text-xs text-gray-500">Il y a 5 minutes</p>
                            </div>
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                                <p class="text-sm text-gray-800">Rendez-vous confirmé</p>
                                <p class="text-xs text-gray-500">Il y a 12 minutes</p>
                            </div>
                            <div class="p-4 hover:bg-gray-50">
                                <p class="text-sm text-gray-800">Rapport mensuel disponible</p>
                                <p class="text-xs text-gray-500">Il y a 1 heure</p>
                            </div>
                        </div>
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
                            <p class="text-xs text-gray-500">{{ Auth::user()?->role ?? 'Role' }}</p>
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

    <div x-data="{ sidebarOpen: true, mobileMenuOpen: false }" class="flex h-screen overflow-hidden">
        
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="flex flex-col bg-gray-900 text-white transition-all duration-300 ease-in-out z-20 shadow-xl h-screen">
            
            <div class="flex items-center justify-between px-4 py-6 border-b border-gray-800 flex-shrink-0">
                <div x-show="sidebarOpen" class="flex items-center space-x-3 overflow-hidden">
                    <div class="min-w-[40px] w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-900/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div class="truncate">
                        <h1 class="text-xl font-bold tracking-tight">HospitSIS</h1>
                        <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Medical Suite</p>
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
                        @php $activeCount = \App\Models\PatientVital::whereIn('status', ['active'])->orWhereNull('status')->count(); @endphp
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
                @if(auth()->user() && auth()->user()->role === 'admin')
    <div class="pb-4 pt-4">
        <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Administration</p>
    </div>

    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Dashboard Admin</span>
    </a>

    <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Médecins & Staff</span>
    </a>

    <a href="{{ route('patients.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Patients</span>
    </a>

    <a href="{{ route('prestations.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        <span x-show="sidebarOpen" class="font-semibold">Prestations</span>
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

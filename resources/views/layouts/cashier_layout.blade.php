<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HospitSIS - Caisse')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
        /* Hide non-printable elements */
        @media print {
            aside, header, footer, .no-print {
                display: none !important;
            }
            main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }
            body {
                background: white !important;
            }
        }
    </style>

    @stack('styles')
    <style>
        #global-loader {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body x-data="{ 
    sidebarOpen: true, 
    mobileMenuOpen: false, 
    showClosingModal: false,
    closingLoading: false,
    isClosed: false,
    closingData: { cash_total: 0, mobile_total: 0, insurance_total: 0 },
    
    async openClosingModal() {
        this.closingLoading = true;
        this.showClosingModal = true;
        try {
            const res = await fetch('{{ route('cashier.closing.totals') }}');
            this.closingData = await res.json();
            this.isClosed = this.closingData.is_closed;
        } catch (e) { 
            console.error('Error fetching totals:', e);
            this.closingData = { cash_total: 0, mobile_total: 0, insurance_total: 0 };
        }
        this.closingLoading = false;
    },

    async confirmClosing() {
        if (this.isClosed) return;
        this.closingLoading = true;
        try {
            const res = await fetch('{{ route('cashier.transfer.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.isClosed = true;
                this.showClosingModal = false;
                alert(data.message);
            } else {
                alert(data.message);
            }
        } catch (e) { console.error('Error confirming closing:', e); }
        this.closingLoading = false;
    }
}" x-init="
    fetch('{{ route('cashier.closing.totals') }}')
        .then(res => res.json())
        .then(data => { isClosed = data.is_closed; })
" class="bg-gray-100 font-sans antialiased">

    <header class="bg-white shadow-sm border-b border-gray-200 z-30">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none md:hidden">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden md:block">
                    <h2 class="text-lg font-semibold text-gray-800">Caisse Hôpital</h2>
                    <p class="text-sm text-gray-500">Gestion des paiements</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
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
            </div>
        </div>
    </header>

    <div class="flex h-screen overflow-hidden">

        <aside
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="flex flex-col bg-gray-900 text-white transition-all duration-300 ease-in-out z-20 shadow-xl h-screen">

            <div class="flex items-center justify-between px-4 py-6 border-b border-gray-800 flex-shrink-0">
                <div x-show="sidebarOpen" class="flex items-center space-x-3 overflow-hidden">
                    <img src="{{ asset('logos/saint-jean-logo.svg') }}" alt="Logo Hôpital" class="w-10 h-10 rounded-full border-2 border-blue-400">
                    <div class="truncate">
                        <h1 class="text-xl font-bold tracking-tight">Clinique Médicale Saint-Jean</h1>
                        <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Caisse</p>
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
                <div class="pb-4">
                    <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Menu Principal</p>
                </div>

                <a href="{{ route('cashier.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('cashier.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Tableau de bord</span>
                </a>

                <a href="{{ route('cashier.appointments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('appointments.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Rendez-vous</span>
                </a>

                <a href="{{ route('cashier.walk-in.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('cashier.walk-in.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Sans RDV</span>
                </a>

                <a href="{{ route('cashier.payments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Paiements</span>
                </a>

                {{-- NEW INSURANCE ITEM --}}
                <a href="{{ route('cashier.insurance-cards.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('cashier.insurance-cards.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Cartes Assurance</span>
                </a>

                <a href="{{ route('cashier.invoices.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('invoices.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Factures</span>
                </a>

                <a href="{{ route('cashier.patients.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('patients.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Patients</span>
                </a>

                <button @click="openClosingModal()" 
                        :disabled="isClosed"
                        :class="isClosed ? 'bg-gray-800 text-gray-600 cursor-not-allowed' : 'text-orange-400 hover:bg-orange-500/10 hover:text-orange-300'"
                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group border border-dashed border-orange-500/20 mt-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span x-show="sidebarOpen" class="font-black text-xs uppercase tracking-widest">Fermer la Caisse</span>
                    <i x-show="isClosed && sidebarOpen" class="fas fa-check-circle ml-auto text-[10px]"></i>
                </button>

                <div class="pt-4 pb-2">
                    <p x-show="sidebarOpen" class="px-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Paramètres</p>
                </div>

                <a href="{{ route('cashier.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('settings') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" class="font-semibold">Paramètres</span>
                </a>
            </nav>

            {{-- FOOTER SIDEBAR --}}
            <div class="border-t border-gray-800 p-4 bg-gray-900/50">
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
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto bg-gray-50 custom-scrollbar">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    @if(session('success'))
                        <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-emerald-700 animate-fade-in shadow-sm">
                            <i class="fas fa-check-circle text-lg"></i>
                            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 animate-fade-in shadow-sm">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-exclamation-circle text-lg"></i>
                                <span class="text-xs font-black uppercase tracking-widest text-red-800">Une erreur est survenue</span>
                            </div>
                            <ul class="list-disc list-inside text-[10px] font-bold uppercase tracking-tight opacity-80">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @yield('content')
            </main>
        </div>
    </div>

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
    
    {{-- Modal de Clôture de Caisse --}}
    <div x-show="showClosingModal" 
         x-cloak
         class="fixed inset-0 bg-gray-900/80 backdrop-blur-md z-[100] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto custom-scrollbar" @click.away="showClosingModal = false">
            <div class="bg-orange-500 p-6 sm:p-8 text-white relative">
                <div class="absolute top-4 right-4 text-white/50 hover:text-white cursor-pointer" @click="showClosingModal = false">
                    <i class="fas fa-times text-xl"></i>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-vault text-2xl"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-black leading-tight">Voulez-vous clôturer votre journée ?</h3>
                <p class="text-orange-100 text-xs sm:text-sm mt-2 font-medium">Cette action générera une demande de versement vers l'administration.</p>
            </div>

            <div class="p-6 sm:p-8 space-y-4 sm:space-y-6">
                {{-- Loader --}}
                <div x-show="closingLoading" class="flex flex-col items-center py-10">
                    <div class="w-12 h-12 border-4 border-orange-100 border-t-orange-500 rounded-full animate-spin"></div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mt-4">Calcul des flux...</p>
                </div>

                <div x-show="!closingLoading" class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-sm">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-black text-gray-500 uppercase">Espèces</span>
                        </div>
                        <span class="text-base sm:text-lg font-black text-gray-900" x-text="new Intl.NumberFormat().format(closingData.cash_total) + ' FCFA'"></span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-sm">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-black text-gray-500 uppercase">M. Money</span>
                        </div>
                        <span class="text-base sm:text-lg font-black text-gray-900" x-text="new Intl.NumberFormat().format(closingData.mobile_total) + ' FCFA'"></span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-2xl border border-purple-100">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-9 h-9 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-sm">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-black text-purple-500 uppercase">Assurance</span>
                        </div>
                        <span class="text-base sm:text-lg font-black text-purple-900" x-text="new Intl.NumberFormat().format(closingData.insurance_total) + ' FCFA'"></span>
                    </div>

                    @auth
                    <div class="pt-2 sm:pt-4">
                        <button @click="confirmClosing()" 
                                :disabled="isClosed"
                                class="w-full py-3.5 sm:py-4 bg-gray-900 text-white rounded-2xl font-black text-[12px] sm:text-sm uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center justify-center gap-3">
                            <i class="fas fa-check-circle"></i>
                            Confirmer et Terminer
                        </button>
                        <p class="text-center text-[10px] text-gray-400 font-bold uppercase mt-4 tracking-tighter">
                            Opérateur : {{ Auth::user()->name }} · {{ now()->format('H:i') }}
                        </p>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Global Loading Overlay --}}
    <div id="global-loader">
        <div class="relative w-24 h-24 mb-4">
            <div class="absolute inset-0 border-4 border-blue-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-cash-register text-2xl text-blue-600"></i>
            </div>
        </div>
        <p class="text-gray-900 font-black uppercase tracking-widest text-sm animate-pulse">Traitement en cours...</p>
        <p class="text-gray-500 text-xs mt-2 italic">Veuillez patienter un instant</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('global-loader');
            
            // Show loader on ALL form submissions
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    // Don't show for search-only forms if any, but safer to show everywhere for a cashier
                    loader.style.display = 'flex';
                });
            });
        });
    </script>
</body>
</html>

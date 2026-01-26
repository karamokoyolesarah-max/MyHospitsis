<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HospitSIS | Super Admin Panel</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
            color: #1e293b;
        }
        /* Onglet Actif - Transition douce */
        .active-tab {
            color: #2563eb !important;
            border-bottom: 2px solid #2563eb;
            background: linear-gradient(to top, #eff6ff, transparent);
        }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        
        /* Personnalisation Scrollbar pour le menu horizontal */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Animation d'entrée des cartes */
        .card-stat { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-stat:hover { transform: translateY(-3px); }

        /* Hospital Modal Tabs */
        .active-hospital-tab {
            color: #2563eb !important;
            border-bottom: 2px solid #2563eb;
            background: linear-gradient(to top, #eff6ff, transparent);
        }
        .hospital-tab-pane { display: none; }
        .hospital-tab-pane.active { display: block; }
    </style>
</head>
<body class="min-h-screen antialiased">

    <div class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 text-left">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-3 rounded-2xl shadow-lg shadow-blue-200/50">
                        <i class="bi bi-shield-lock-fill text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">HospitSIS <span class="text-blue-600 italic font-medium text-lg uppercase ml-1 tracking-widest">SaaS</span></h1>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Super Admin Control Center</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <div class="text-[10px] text-slate-400 uppercase font-black tracking-tighter">Session Active</div>
                        <div class="font-bold text-slate-900 leading-tight">{{ Auth::guard('superadmin')->user()->name ?? 'Super Admin' }}</div>
                    </div>
                    <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg shadow-lg rotate-3 hover:rotate-0 transition-transform duration-300">
                        SA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border-b border-slate-200 sticky top-[81px] z-40">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex gap-2 overflow-x-auto no-scrollbar">
                <button onclick="switchTab('overview')" id="btn-overview" class="tab-btn active-tab flex items-center gap-2 px-6 py-4 font-bold text-sm transition-all whitespace-nowrap">
                    <i class="bi bi-grid-1x2-fill"></i> Vue d'ensemble
                </button>
                <button onclick="switchTab('hospitals')" id="btn-hospitals" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-hospital"></i> Gestion Hôpitaux
                </button>
                <button onclick="switchTab('specialists')" id="btn-specialists" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-person-badge-fill"></i> Validation Spécialistes
                </button>
                <button onclick="switchTab('subscription-plans')" id="btn-subscription-plans" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-credit-card-2-back-fill"></i> Catalogue Forfaits
                </button>
                <button onclick="switchTab('commission-rates')" id="btn-commission-rates" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-gear-fill"></i> Commissions Spécialistes
                </button>
                <button onclick="switchTab('financial-monitoring')" id="btn-financial-monitoring" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-graph-up"></i> Monitoring & Portefeuilles
                </button>
                <button onclick="switchTab('invoices')" id="btn-invoices" class="tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                    <i class="bi bi-receipt"></i> Factures & Revenus
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-green-600 text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        @include('superadmin.tabs.overview')

        @include('superadmin.tabs.hospitals')

        @include('superadmin.tabs.specialists')

        <!-- === SUBSCRIPTION PLANS MANAGEMENT === -->
        @include('superadmin.tabs.subscription-plans')

        <!-- === COMMISSION RATES MANAGEMENT === -->
        @include('superadmin.tabs.commission-rates')

        @include('superadmin.tabs.financial-monitoring')
        @include('superadmin.tabs.invoices')

    </div>

    <!-- Modal Installation Hôpital -->
    <div id="installModal" class="fixed inset-0 bg-black/70 backdrop-blur-md hidden z-50 flex items-center justify-center p-6 animate-in fade-in duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="p-10 border-b border-slate-200 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/90 to-teal-600/90"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-4xl font-black tracking-tight">Installer un Nouvel Hôpital</h3>
                        <p class="text-emerald-100 mt-3 font-medium text-lg">Configurez les paramètres de base pour déployer une nouvelle instance</p>
                    </div>
                    <button onclick="closeInstallModal()" class="text-emerald-200 hover:text-white p-4 hover:bg-white/20 rounded-3xl transition-all duration-300 hover:scale-110 backdrop-blur-sm">
                        <i class="bi bi-x-lg text-3xl"></i>
                    </button>
                </div>
                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/5 rounded-full"></div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1 bg-slate-50/30">
                <div class="p-10">
                    <form id="installForm" action="{{ route('superadmin.hospitals.store') }}" method="POST" class="space-y-10">
                        @csrf

                        <!-- Section 1: Informations de l'Hôpital -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-hospital-fill text-white text-sm"></i>
                                </div>
                                Informations de l'Hôpital
                            </h4>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Nom de l'Hôpital</label>
                                    <div class="relative">
                                        <input type="text" name="hospital_name" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                               placeholder="Ex: Centre Hospitalier Universitaire">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="bi bi-building text-xl"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Adresse</label>
                                    <div class="relative">
                                        <input type="text" name="hospital_address" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                               placeholder="Ex: Abidjan, Côte d'Ivoire">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="bi bi-geo-alt-fill text-xl"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Administrateur de l'Hôpital -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-person-badge-fill text-white text-sm"></i>
                                </div>
                                Administrateur de l'Hôpital
                            </h4>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Nom de l'Administrateur</label>
                                    <div class="relative">
                                        <input type="text" name="admin_name" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                               placeholder="Ex: Dr. Jean Dupont">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="bi bi-person-fill text-xl"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Email Administrateur</label>
                                    <div class="relative">
                                        <input type="email" name="admin_email" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                               placeholder="admin@hopital.com">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="bi bi-envelope-fill text-xl"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 space-y-4">
                                <label class="block text-xl font-bold text-slate-800">Mot de Passe Administrateur</label>
                                <div class="relative">
                                    <input type="password" name="admin_password" required
                                           class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                           placeholder="Minimum 8 caractères">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                        <i class="bi bi-shield-lock-fill text-xl"></i>
                                    </span>
                                </div>
                                <p class="text-base text-slate-500 font-medium">Le mot de passe doit contenir au moins 8 caractères</p>
                            </div>
                        </div>

                        <!-- Section 3: Résumé du Déploiement -->
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-3xl p-8 border-2 border-emerald-200">
                            <h4 class="text-2xl font-black text-emerald-900 mb-6 flex items-center gap-3">
                                <i class="bi bi-info-circle-fill text-2xl"></i>
                                Résumé du Déploiement
                            </h4>

                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center p-6 bg-white rounded-2xl border border-emerald-200">
                                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="bi bi-hospital text-2xl text-emerald-600"></i>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900">Nouvelle Instance</div>
                                    <div class="text-sm text-slate-500">Hôpital SaaS</div>
                                </div>

                                <div class="text-center p-6 bg-white rounded-2xl border border-emerald-200">
                                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="bi bi-person-check text-2xl text-blue-600"></i>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900">Administrateur</div>
                                    <div class="text-sm text-slate-500">Créé automatiquement</div>
                                </div>

                                <div class="text-center p-6 bg-white rounded-2xl border border-emerald-200">
                                    <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="bi bi-gear text-2xl text-purple-600"></i>
                                    </div>
                                    <div class="text-lg font-bold text-slate-900">Configuration</div>
                                    <div class="text-sm text-slate-500">Automatisée</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer with Actions -->
            <div class="p-10 border-t border-slate-200 bg-white">
                <div class="flex gap-6">
                    <button type="button" onclick="closeInstallModal()"
                            class="flex-1 px-10 py-6 border-2 border-slate-300 text-slate-700 rounded-3xl hover:bg-slate-100 hover:border-slate-400 transition-all duration-300 font-bold text-xl hover:scale-105">
                        <i class="bi bi-x-circle mr-3"></i>
                        Annuler
                    </button>
                    <button type="submit" form="installForm"
                            class="flex-1 px-10 py-6 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-3xl transition-all duration-300 font-bold shadow-2xl shadow-emerald-200 hover:shadow-emerald-300 flex items-center justify-center gap-4 text-xl transform hover:scale-105 border-2 border-emerald-500">
                        <i class="bi bi-rocket-takeoff-fill text-2xl"></i>
                        <span class="font-black">INSTALLER L'HÔPITAL</span>
                        <i class="bi bi-arrow-right text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Détails Hôpital -->
    <div id="hospitalDetailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
        <div class="bg-white shadow-2xl w-full h-full overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900" id="modalHospitalName">Détails de l'Hôpital</h3>
                        <p class="text-slate-500 mt-1">Surveillance et gestion de l'infrastructure hospitalière</p>
                    </div>
                    <button onclick="closeHospitalDetailsModal()" class="text-slate-400 hover:text-slate-600 p-2 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="px-8 py-4 bg-slate-50 border-b border-slate-200">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-black text-slate-900" id="statsUsers">0</div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Utilisateurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-slate-900" id="statsServices">0</div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Services</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-slate-900" id="statsPrestations">0</div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Prestations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-slate-900" id="statsActiveUsers">0</div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Utilisateurs Actifs</div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white border-b border-slate-200">
                <div class="flex gap-2 px-8 overflow-x-auto no-scrollbar">
                    <button onclick="switchHospitalTab('company')" id="btn-company" class="hospital-tab-btn active-hospital-tab flex items-center gap-2 px-6 py-4 font-bold text-sm transition-all whitespace-nowrap">
                        <i class="bi bi-building"></i> Entreprise
                    </button>
                    <button onclick="switchHospitalTab('users')" id="btn-users" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                        <i class="bi bi-people"></i> Utilisateurs
                    </button>
                    <button onclick="switchHospitalTab('services')" id="btn-services" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                        <i class="bi bi-hospital"></i> Services
                    </button>
                    <button onclick="switchHospitalTab('prestations')" id="btn-prestations" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-bold text-sm text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all whitespace-nowrap">
                        <i class="bi bi-cash-stack"></i> Prestations
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="overflow-y-auto" style="height: calc(100vh - 200px);">
                <!-- Tab Entreprise -->
                <div id="tab-company" class="hospital-tab-pane active p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nom de l'Hôpital</label>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="font-bold text-slate-900" id="companyName">-</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Adresse</label>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-slate-900" id="companyAddress">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Statut</label>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="font-bold" id="companyStatus">-</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Plan Souscrit</label>
                                <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                                    <span class="font-bold text-indigo-700">Premium Plan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Utilisateurs -->
                <div id="tab-users" class="hospital-tab-pane p-8">
                    <div class="space-y-4" id="usersList">
                        <!-- Users will be populated here -->
                    </div>
                </div>

                <!-- Tab Services -->
                <div id="tab-services" class="hospital-tab-pane p-8">
                    <div class="space-y-4" id="servicesList">
                        <!-- Services will be populated here -->
                    </div>
                </div>

                <!-- Tab Prestations -->
                <div id="tab-prestations" class="hospital-tab-pane p-8">
                    <div class="space-y-6" id="prestationsList">
                        <!-- Prestations will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === COMMISSION BRACKETS MANAGEMENT ===

        let commissionBrackets = [];
        let editingCommissionId = null;

        function addCommissionBracket(minPrice = '', maxPrice = '', percentage = '') {
            const bracketId = Date.now();
            const bracketHtml = `
                <div id="bracket-${bracketId}" class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-3xl border-2 border-green-200">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-xl font-bold text-green-900 flex items-center gap-2">
                            <i class="bi bi-cash-stack text-lg"></i>
                            Tranche ${commissionBrackets.length + 1}
                        </h6>
                        <button type="button" onclick="removeCommissionBracket(${bracketId})"
                                class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-2xl transition-all duration-300 hover:scale-110">
                            <i class="bi bi-trash-fill text-xl"></i>
                        </button>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="block text-lg font-bold text-slate-800">Prix Minimum (FCFA)</label>
                            <div class="relative">
                                <input type="number" name="brackets[${bracketId}][min_price]" step="0.01" min="0" value="${minPrice}" required
                                       class="w-full px-6 py-4 pl-12 bg-white border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-lg font-medium"
                                       placeholder="0">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">₣</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-lg font-bold text-slate-800">Prix Maximum (FCFA)</label>
                            <div class="relative">
                                <input type="number" name="brackets[${bracketId}][max_price]" step="0.01" min="0" value="${maxPrice}"
                                       class="w-full px-6 py-4 pl-12 bg-white border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-lg font-medium"
                                       placeholder="15000">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">₣</span>
                            </div>
                            <p class="text-sm text-slate-500">Laissez vide pour "et plus"</p>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-lg font-bold text-slate-800">% de Commission</label>
                            <div class="relative">
                                <input type="number" name="brackets[${bracketId}][percentage]" step="0.01" min="0" max="100" value="${percentage}" required
                                       class="w-full px-6 py-4 pr-12 bg-white border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-lg font-medium"
                                       placeholder="15">
                                <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('commissionBrackets').insertAdjacentHTML('beforeend', bracketHtml);
            commissionBrackets.push({ id: bracketId, minPrice, maxPrice, percentage });

            // Update bracket numbers
            updateBracketNumbers();
        }

        function removeCommissionBracket(bracketId) {
            const element = document.getElementById(`bracket-${bracketId}`);
            if (element) {
                element.remove();
                commissionBrackets = commissionBrackets.filter(b => b.id !== bracketId);
                updateBracketNumbers();
            }
        }

        function updateBracketNumbers() {
            const brackets = document.querySelectorAll('#commissionBrackets > div');
            brackets.forEach((bracket, index) => {
                const title = bracket.querySelector('h6');
                if (title) {
                    title.innerHTML = `
                        <i class="bi bi-cash-stack text-lg"></i>
                        Tranche ${index + 1}
                    `;
                }
            });
        }

        function testCommissionCalculation() {
            const testPrice = parseFloat(document.getElementById('testPrice').value) || 0;
            let applicableBracket = null;
            let commission = 0;

            // Find applicable bracket
            const bracketElements = document.querySelectorAll('#commissionBrackets > div');
            for (let bracketElement of bracketElements) {
                const minInput = bracketElement.querySelector('input[name*="[min_price]"]');
                const maxInput = bracketElement.querySelector('input[name*="[max_price]"]');
                const percentageInput = bracketElement.querySelector('input[name*="[percentage]"]');

                if (minInput && percentageInput) {
                    const minPrice = parseFloat(minInput.value) || 0;
                    const maxPrice = maxInput && maxInput.value ? parseFloat(maxInput.value) : Infinity;
                    const percentage = parseFloat(percentageInput.value) || 0;

                    if (testPrice >= minPrice && (maxPrice === Infinity || testPrice <= maxPrice)) {
                        applicableBracket = { minPrice, maxPrice, percentage };
                        commission = (testPrice * percentage) / 100;
                        break;
                    }
                }
            }

            // Update display
            const resultDiv = document.getElementById('testResult');
            const textDiv = document.getElementById('testText');

            if (applicableBracket) {
                resultDiv.textContent = `${commission.toLocaleString()} FCFA`;
                if (applicableBracket.maxPrice === Infinity) {
                    textDiv.textContent = `Tranche: ${applicableBracket.minPrice.toLocaleString()}+ FCFA → ${applicableBracket.percentage}%`;
                } else {
                    textDiv.textContent = `Tranche: ${applicableBracket.minPrice.toLocaleString()} - ${applicableBracket.maxPrice.toLocaleString()} FCFA → ${applicableBracket.percentage}%`;
                }
                resultDiv.className = 'text-3xl font-black text-green-600';
                textDiv.className = 'text-lg text-green-700';
            } else {
                resultDiv.textContent = '0 FCFA';
                textDiv.textContent = 'Aucune tranche applicable';
                resultDiv.className = 'text-3xl font-black text-slate-900';
                textDiv.className = 'text-lg text-slate-600';
            }
        }

        function switchTab(tabId) {
            // Logique de masquage
            document.querySelectorAll('.tab-pane').forEach(el => {
                el.classList.remove('active');
            });

            // Logique de style bouton
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.add('text-slate-500');
            });

            // Activation
            const targetContent = document.getElementById('tab-' + tabId);
            const targetBtn = document.getElementById('btn-' + tabId);

            if(targetContent && targetBtn) {
                targetContent.classList.add('active');
                targetBtn.classList.add('active-tab');
                targetBtn.classList.remove('text-slate-500');

                // Load data for specific tabs
                if (tabId === 'invoices') {
                    refreshInvoicesData();
                }

                // Feedback Haptique Visuel (Scroll au top lors du changement d'onglet)
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function openInstallModal() {
            document.getElementById('installModal').classList.remove('hidden');
            document.getElementById('installModal').classList.add('flex');
        }

        function closeInstallModal() {
            document.getElementById('installModal').classList.add('hidden');
            document.getElementById('installModal').classList.remove('flex');
        }

        // Fermer le modal en cliquant en dehors
        document.getElementById('installModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInstallModal();
            }
        });

        // Toggle hospital status
        function toggleHospitalStatus(hospitalId, isActive) {
            fetch(`/superadmin/hospitals/${hospitalId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status text
                    const statusSpan = event.target.closest('label').querySelector('span:last-child');
                    statusSpan.textContent = isActive ? 'ACTIF' : 'INACTIF';

                    // Show success message
                    showNotification('Statut de l\'hôpital mis à jour avec succès', 'success');
                } else {
                    // Revert the toggle
                    event.target.checked = !isActive;
                    showNotification('Erreur lors de la mise à jour du statut', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert the toggle
                event.target.checked = !isActive;
                showNotification('Erreur lors de la mise à jour du statut', 'error');
            });
        }

        // Open hospital details modal
        function openHospitalDetails(hospitalId) {
            // Fetch hospital details
            fetch(`/admin-system/hospitals/${hospitalId}/details`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                populateHospitalModal(data);
                document.getElementById('hospitalDetailsModal').classList.remove('hidden');
                document.getElementById('hospitalDetailsModal').classList.add('flex');
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors du chargement des détails de l\'hôpital', 'error');
            });
        }

        // Populate hospital details modal
        function populateHospitalModal(data) {
            const { hospital, stats } = data;

            // Update modal title
            document.getElementById('modalHospitalName').textContent = hospital.name;

            // Tab 1: Entreprise (Company Info)
            document.getElementById('companyName').textContent = hospital.name;
            document.getElementById('companyAddress').textContent = hospital.address || 'Adresse non spécifiée';
            document.getElementById('companyStatus').textContent = hospital.is_active ? 'ACTIF' : 'INACTIF';
            document.getElementById('companyStatus').className = hospital.is_active ?
                'px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800' :
                'px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800';

            // Tab 2: Utilisateurs (Users)
            const usersContainer = document.getElementById('usersList');
            usersContainer.innerHTML = '';

            if (hospital.users && hospital.users.length > 0) {
                hospital.users.forEach(user => {
                    const userDiv = document.createElement('div');
                    userDiv.className = 'flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200';
                    userDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">${user.name}</div>
                                <div class="text-sm text-slate-500">${user.email}</div>
                                <div class="text-xs text-slate-400">${user.service ? user.service.name : 'Aucun service'}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 rounded-full text-xs font-bold ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${user.is_active ? 'ACTIF' : 'INACTIF'}
                            </span>
                            <div class="text-xs text-slate-400 mt-1 capitalize">${user.role.replace('_', ' ')}</div>
                        </div>
                    `;
                    usersContainer.appendChild(userDiv);
                });
            } else {
                usersContainer.innerHTML = '<div class="text-center text-slate-500 py-8">Aucun utilisateur trouvé</div>';
            }

            // Tab 3: Services (Departments)
            const servicesContainer = document.getElementById('servicesList');
            servicesContainer.innerHTML = '';

            if (hospital.services && hospital.services.length > 0) {
                hospital.services.forEach(service => {
                    const serviceDiv = document.createElement('div');
                    serviceDiv.className = 'p-4 bg-slate-50 rounded-xl border border-slate-200';
                    serviceDiv.innerHTML = `
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-slate-900">${service.name}</h4>
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                ${service.users ? service.users.length : 0} utilisateurs
                            </span>
                        </div>
                        <div class="text-sm text-slate-600 mb-2">${service.description || 'Aucune description'}</div>
                        <div class="text-xs text-slate-400">
                            ${service.prestations ? service.prestations.length : 0} prestations
                        </div>
                    `;
                    servicesContainer.appendChild(serviceDiv);
                });
            } else {
                servicesContainer.innerHTML = '<div class="text-center text-slate-500 py-8">Aucun service trouvé</div>';
            }

            // Tab 4: Prestations (Services/Pricing)
            const prestationsContainer = document.getElementById('prestationsList');
            prestationsContainer.innerHTML = '';

            if (hospital.prestations && hospital.prestations.length > 0) {
                const groupedPrestations = {};
                hospital.prestations.forEach(prestation => {
                    if (!groupedPrestations[prestation.category]) {
                        groupedPrestations[prestation.category] = [];
                    }
                    groupedPrestations[prestation.category].push(prestation);
                });

                Object.keys(groupedPrestations).forEach(category => {
                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'mb-6';
                    categoryDiv.innerHTML = `
                        <h4 class="font-bold text-slate-900 mb-3 capitalize">${category.replace('_', ' ')}</h4>
                        <div class="space-y-3">
                    `;

                    groupedPrestations[category].forEach(prestation => {
                        const prestationDiv = document.createElement('div');
                        prestationDiv.className = 'flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200';
                        prestationDiv.innerHTML = `
                            <div>
                                <div class="font-medium text-slate-900">${prestation.name}</div>
                                <div class="text-sm text-slate-500">${prestation.description || ''}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-slate-900">${prestation.price} FCFA</div>
                                <span class="px-2 py-1 rounded-full text-xs font-bold ${prestation.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${prestation.is_active ? 'ACTIF' : 'INACTIF'}
                                </span>
                            </div>
                        `;
                        categoryDiv.querySelector('.space-y-3').appendChild(prestationDiv);
                    });

                    prestationsContainer.appendChild(categoryDiv);
                });
            } else {
                prestationsContainer.innerHTML = '<div class="text-center text-slate-500 py-8">Aucune prestation trouvée</div>';
            }

            // Update stats
            document.getElementById('statsUsers').textContent = stats.total_users;
            document.getElementById('statsServices').textContent = stats.total_services;
            document.getElementById('statsPrestations').textContent = stats.total_prestations;
            document.getElementById('statsActiveUsers').textContent = stats.active_users;
        }

        // Switch hospital tabs
        function switchHospitalTab(tabId) {
            // Hide all tab panes
            document.querySelectorAll('.hospital-tab-pane').forEach(el => {
                el.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.hospital-tab-btn').forEach(btn => {
                btn.classList.remove('active-hospital-tab');
                btn.classList.add('text-slate-500');
            });

            // Show target tab pane
            const targetContent = document.getElementById('tab-' + tabId);
            const targetBtn = document.getElementById('btn-' + tabId);

            if(targetContent && targetBtn) {
                targetContent.classList.add('active');
                targetBtn.classList.add('active-hospital-tab');
                targetBtn.classList.remove('text-slate-500');
            }
        }

        // Close hospital details modal
        function closeHospitalDetailsModal() {
            document.getElementById('hospitalDetailsModal').classList.add('hidden');
            document.getElementById('hospitalDetailsModal').classList.remove('flex');
        }

        // Modal functions for Subscription Plans
        function showNewPlanModal() {
            document.getElementById('newPlanModal').classList.remove('hidden');
            document.getElementById('newPlanModal').classList.add('flex');
        }

        function closeNewPlanModal() {
            document.getElementById('newPlanModal').classList.add('hidden');
            document.getElementById('newPlanModal').classList.remove('flex');
            document.getElementById('planForm').reset();
        }

        function showNewCommissionModal() {
            editingCommissionId = null;
            document.getElementById('newCommissionModal').classList.remove('hidden');
            document.getElementById('newCommissionModal').classList.add('flex');
        }

        function closeNewCommissionModal() {
            editingCommissionId = null;
            document.getElementById('newCommissionModal').classList.add('hidden');
            document.getElementById('newCommissionModal').classList.remove('flex');
            document.getElementById('commissionForm').reset();
            // Reset modal title
            const modalTitle = document.querySelector('#newCommissionModal h3');
            if (modalTitle) {
                modalTitle.textContent = 'Nouvelle Règle de Commission';
            }
            // Reset button text
            const submitButton = document.querySelector('#newCommissionModal button[type="submit"] span');
            if (submitButton) {
                submitButton.textContent = 'VALIDER LA RÈGLE';
            }
        }

        function populateCommissionForm(rate) {
            editingCommissionId = rate.id;
            
            // Update modal title
            const modalTitle = document.querySelector('#newCommissionModal h3');
            if (modalTitle) {
                modalTitle.textContent = 'Modifier la Règle de Commission';
            }
            
            // Update button text
            const submitButton = document.querySelector('#newCommissionModal button[type="submit"] span');
            if (submitButton) {
                submitButton.textContent = 'MODIFIER LA RÈGLE';
            }
            
            // Populate form fields
            document.querySelector('input[name="activation_fee"]').value = rate.activation_fee;
            document.querySelector('input[name="is_active"]').checked = rate.is_active;
            
            // Clear existing brackets
            document.getElementById('commissionBrackets').innerHTML = '';
            commissionBrackets = [];
            
            // Add brackets
            if (rate.brackets && rate.brackets.length > 0) {
                rate.brackets.forEach(bracket => {
                    addCommissionBracket(bracket.min_price, bracket.max_price, bracket.percentage);
                });
            }
        }

        // Submit new subscription plan
        async function submitNewPlan() {
            const formData = new FormData(document.getElementById('planForm'));
            const features = formData.get('features').split('\n').filter(f => f.trim());

            try {
                const response = await fetch('/admin-system/subscription-plans', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        target_type: formData.get('target_type'),
                        price: parseFloat(formData.get('price')),
                        duration_unit: formData.get('duration_unit'),
                        duration_value: parseInt(formData.get('duration_value')),
                        features: features,
                        is_active: formData.get('is_active') === 'on'
                    })
                });

                if (!response.ok) {
                    if (response.status === 422) {
                        // Validation errors — attempt to parse JSON, otherwise log raw text
                        const err = await response.json().catch(async () => {
                            const txt = await response.text().catch(() => null);
                            return txt ? {__raw: txt} : null;
                        });

                        // debug logs removed for production — handle errors silently

                        if (err && err.errors) {
                            let message = 'Erreurs de validation: ';
                            for (let field in err.errors) {
                                message += field + ': ' + err.errors[field].join(', ') + '; ';
                            }
                            showNotification(message, 'error');
                            return;
                        } else if (err && err.message) {
                            showNotification(err.message, 'error');
                            return;
                        } else if (err && err.__raw) {
                            // Show truncated server HTML/text and advise to check logs
                            const truncated = err.__raw.substring(0, 300);
                            showNotification('Erreur serveur: ' + truncated, 'error');
                            return;
                        } else {
                            throw new Error('HTTP 422 Unprocessable Content');
                        }
                    }
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }

                let result;
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error('Unexpected server response: ' + text.substring(0, 200));
                }

                if (result.errors) {
                    let message = 'Erreurs de validation: ';
                    for (let field in result.errors) {
                        message += field + ': ' + result.errors[field].join(', ') + '; ';
                    }
                    showNotification(message, 'error');
                } else if (result.success) {
                    showNotification('Plan créé avec succès!', 'success');
                    closeNewPlanModal();
                    loadSubscriptionPlans();
                } else {
                    showNotification(result.message || 'Erreur lors de la création du plan', 'error');
                }
            } catch (error) {
                showNotification('Erreur réseau: ' + error.message, 'error');
            }
        }

        // Update features preview in real-time
        function updateFeaturesPreview() {
            const textarea = document.getElementById('featuresTextarea');
            const previewContainer = document.getElementById('featuresList');
            const features = textarea.value.split('\n').filter(f => f.trim() && f.startsWith('•'));

            previewContainer.innerHTML = '';

            if (features.length > 0) {
                features.forEach(feature => {
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                    badge.innerHTML = `<i class="bi bi-check-circle-fill mr-1"></i>${feature.substring(1).trim()}`;
                    previewContainer.appendChild(badge);
                });
            } else {
                previewContainer.innerHTML = '<span class="text-sm text-slate-400 italic">Aucune fonctionnalité définie</span>';
            }
        }

        // Submit new commission rule
        async function submitNewCommission() {
            console.log('submitNewCommission called, editingCommissionId:', editingCommissionId);
            const formData = new FormData(document.getElementById('commissionForm'));

            // Collect bracket data
            const brackets = [];
            const bracketElements = document.querySelectorAll('#commissionBrackets > div');

            bracketElements.forEach((bracketElement, index) => {
                const minInput = bracketElement.querySelector('input[name*="[min_price]"]');
                const maxInput = bracketElement.querySelector('input[name*="[max_price]"]');
                const percentageInput = bracketElement.querySelector('input[name*="[percentage]"]');

                if (minInput && percentageInput) {
                    const minPrice = parseFloat(minInput.value) || 0;
                    const maxPrice = maxInput && maxInput.value ? parseFloat(maxInput.value) : null;
                    const percentage = parseFloat(percentageInput.value) || 0;

                    brackets.push({
                        min_price: minPrice,
                        max_price: maxPrice,
                        percentage: percentage,
                        order: index + 1
                    });
                }
            });

            // Validate that we have at least one bracket
            if (brackets.length === 0) {
                showNotification('Veuillez ajouter au moins une tranche de commission', 'error');
                return;
            }

            const isEditing = editingCommissionId !== null;
            const method = isEditing ? 'PUT' : 'POST';
            const url = isEditing ? `/admin-system/commission-rates/${editingCommissionId}` : '/admin-system/commission-rates';

            console.log('Submitting:', { isEditing, method, url, editingCommissionId, bracketsCount: brackets.length });

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        brackets: brackets,
                        activation_fee: parseFloat(formData.get('activation_fee') || 4000),
                        is_active: formData.get('is_active') === 'on'
                    })
                });

                const result = await response.json();
                console.log('Response:', { status: response.status, result });

                if (result.success) {
                    const message = isEditing ? 'Règle de commission modifiée avec succès!' : 'Règle de commission créée avec succès!';
                    showNotification(message, 'success');
                    closeNewCommissionModal();
                    loadCommissionRates();
                } else {
                    showNotification(result.message || 'Erreur lors de la sauvegarde de la règle', 'error');
                }
            } catch (error) {
                console.error('Error submitting commission:', error);
                showNotification('Erreur réseau', 'error');
            }
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-2xl shadow-lg z-50 ${
                type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' :
                type === 'error' ? 'bg-red-50 border border-red-200 text-red-800' :
                'bg-blue-50 border border-blue-200 text-blue-800'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-triangle-fill' : 'info-circle-fill'} text-xl"></i>
                    <span class="font-bold">${message}</span>
                </div>
            `;
            document.body.appendChild(notification);

            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // === SUBSCRIPTION PLANS MANAGEMENT ===

        function openSubscriptionPlanModal(planId = null) {
            if (planId) {
                // Edit existing plan - load data first
                showNotification('Fonctionnalité d\'édition à implémenter', 'info');
            } else {
                // New plan
                showNewPlanModal();
            }
        }

        function loadSubscriptionPlans() {
            fetch('/admin-system/subscription-plans', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('subscriptionPlansTable');
                tableBody.innerHTML = '';

                if (data.plans && data.plans.length > 0) {
                    data.plans.forEach(plan => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-blue-50/30 transition-colors';
                        row.innerHTML = `
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900">${plan.name}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900">${plan.price.toLocaleString()} FCFA</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-slate-600">${plan.duration_value} ${plan.duration_unit === 'month' ? 'mois' : 'an'}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-slate-600">${plan.features ? plan.features.length : 0} fonctionnalités</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold ${plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${plan.is_active ? 'ACTIF' : 'INACTIF'}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right text-lg">
                                <button onclick="editSubscriptionPlan(${plan.id})" class="text-slate-400 hover:text-blue-600 p-2 hover:bg-white rounded-xl transition-all">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button onclick="deleteSubscriptionPlan(${plan.id})" class="text-slate-400 hover:text-red-600 p-2 hover:bg-white rounded-xl transition-all ml-2">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6" class="px-8 py-12 text-center text-slate-400">Aucun plan d\'abonnement trouvé</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors du chargement des plans', 'error');
            });
        }

        function editSubscriptionPlan(planId) {
            showNotification('Fonctionnalité à implémenter', 'info');
        }

        function deleteSubscriptionPlan(planId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce plan ?')) {
                fetch(`/admin-system/subscription-plans/${planId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadSubscriptionPlans();
                    } else {
                        showNotification('Erreur lors de la suppression', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur lors de la suppression', 'error');
                });
            }
        }

        // === COMMISSION RATES MANAGEMENT ===

        function openCommissionRateModal(rateId = null) {
            if (rateId) {
                // Edit existing rate - load data first
                console.log('Loading commission rate:', rateId);
                fetch(`/admin-system/commission-rates/${rateId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    if (data.success && data.rate) {
                        populateCommissionForm(data.rate);
                        document.getElementById('newCommissionModal').classList.remove('hidden');
                        document.getElementById('newCommissionModal').classList.add('flex');
                    } else {
                        showNotification('Règle non trouvée', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading commission rate:', error);
                    showNotification('Erreur lors du chargement de la règle', 'error');
                });
            } else {
                // New commission rate
                showNewCommissionModal();
            }
        }

        function loadCommissionRates() {
            fetch('/admin-system/commission-rates', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('commissionRatesContainer');
                container.innerHTML = '';

                if (data.rates && data.rates.length > 0) {
                    data.rates.forEach(rate => {
                        const card = document.createElement('div');
                        card.className = 'bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow';
                        card.innerHTML = `
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-slate-900">${rate.service_type}</h4>
                                    <p class="text-sm text-slate-500">${rate.bracket_count} tranche(s) configurée(s)</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold ${rate.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${rate.is_active ? 'ACTIF' : 'INACTIF'}
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider">Frais d'Activation</p>
                                    <p class="font-bold text-slate-900">${rate.activation_fee} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider">Commission Moyenne</p>
                                    <p class="font-bold text-slate-900">${rate.commission_percentage}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider">Tranches de Prix</p>
                                    <div class="text-sm text-slate-600 space-y-1">
                                        ${rate.brackets_summary.split(' | ').map(bracket => `<div class="bg-slate-50 px-2 py-1 rounded">${bracket}</div>`).join('')}
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button onclick="editCommissionRate(${rate.id})" class="text-slate-400 hover:text-purple-600 p-2 hover:bg-slate-50 rounded-xl transition-all">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button onclick="deleteCommissionRate(${rate.id})" class="text-slate-400 hover:text-red-600 p-2 hover:bg-slate-50 rounded-xl transition-all">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400">Aucune règle de commission trouvée</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors du chargement des commissions', 'error');
            });
        }

        function editCommissionRate(rateId) {
            openCommissionRateModal(rateId);
        }

        function deleteCommissionRate(rateId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette règle ?')) {
                fetch(`/admin-system/commission-rates/${rateId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadCommissionRates();
                    } else {
                        showNotification('Erreur lors de la suppression', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur lors de la suppression', 'error');
                });
            }
        }

        // === FINANCIAL MONITORING ===

        function refreshFinancialData() {
            loadFinancialStats();
            loadFinancialLists();
        }

        function loadFinancialStats() {
            fetch('/admin-system/financial-monitoring', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const statsContainer = document.getElementById('financialStats');
                statsContainer.innerHTML = '';

                const stats = [
                    { label: 'Revenus SaaS Total', value: data.stats.total_revenue, icon: 'bi-bank', color: 'blue' },
                    { label: 'Frais Activation', value: data.stats.activation_fees, icon: 'bi-person-check', color: 'indigo' },
                    { label: 'Commissions Actes', value: data.stats.specialist_commissions, icon: 'bi-percent', color: 'purple' },
                    { label: 'Abonnements Hôpitaux', value: data.stats.hospital_subscriptions, icon: 'bi-building-check', color: 'emerald' }
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

        function loadFinancialLists() {
            fetch('/admin-system/financial-monitoring', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Load hospitals financial data
                const hospitalsContainer = document.getElementById('hospitalsFinancialList');
                hospitalsContainer.innerHTML = '';

                if (data.hospitals && data.hospitals.length > 0) {
                    data.hospitals.forEach(hospital => {
                        const hospitalDiv = document.createElement('div');
                        hospitalDiv.className = 'flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200';
                        hospitalDiv.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                                    <i class="bi bi-hospital"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">${hospital.name}</div>
                                    <div class="text-sm text-slate-500">Statut: ${hospital.is_active ? 'Actif' : 'Inactif'}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-slate-400">Plan: ${hospital.subscription ? hospital.subscription.plan.name : 'Aucun'}</div>
                            </div>
                        `;
                        hospitalsContainer.appendChild(hospitalDiv);
                    });
                } else {
                    hospitalsContainer.innerHTML = '<div class="text-center text-slate-500 py-8">Aucun hôpital trouvé</div>';
                }

                // Load specialists financial data
                const specialistsContainer = document.getElementById('specialistsFinancialList');
                specialistsContainer.innerHTML = '';

                if (data.specialists && data.specialists.length > 0) {
                    data.specialists.forEach(specialist => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-purple-50/30 transition-colors';
                        row.innerHTML = `
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">${specialist.name}</div>
                                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">ID: ${specialist.specialist_id}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900">${specialist.balance.toLocaleString()} FCFA</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold ${specialist.status === 'ACTIF' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${specialist.status}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right text-lg">
                                <button onclick="viewSpecialistHistory(${specialist.specialist_id})" class="text-slate-400 hover:text-purple-600 p-2 hover:bg-white rounded-xl transition-all">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                <button onclick="blockSpecialistWallet(${specialist.specialist_id})" class="text-red-400 hover:text-red-600 p-2 hover:bg-white rounded-xl transition-all ml-2">
                                    <i class="bi bi-shield-x"></i>
                                </button>
                            </td>
                        `;
                        specialistsContainer.appendChild(row);
                    });
                } else {
                    specialistsContainer.innerHTML = '<tr><td colspan="4" class="px-8 py-12 text-center text-slate-400">Aucun spécialiste trouvé</td></tr>';
                }

                // Load recent transactions
                const transactionsContainer = document.getElementById('recentTransactionsList');
                transactionsContainer.innerHTML = '';

                if (data.recent_transactions && data.recent_transactions.length > 0) {
                    data.recent_transactions.forEach(transaction => {
                        const transactionDiv = document.createElement('div');
                        transactionDiv.className = 'flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200';
                        transactionDiv.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 ${transaction.source_type === 'hospital' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600'} rounded-xl flex items-center justify-center">
                                    <i class="bi ${transaction.source_type === 'hospital' ? 'bi-hospital' : 'bi-person'}"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">${transaction.description}</div>
                                    <div class="text-sm text-slate-500">${new Date(transaction.created_at).toLocaleDateString('fr-FR')}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-slate-900">${transaction.net_income} FCFA</div>
                                <div class="text-xs text-slate-400 capitalize">${transaction.source_type}</div>
                            </div>
                        `;
                        transactionsContainer.appendChild(transactionDiv);
                    });
                } else {
                    transactionsContainer.innerHTML = '<div class="text-center text-slate-500 py-8">Aucune transaction récente</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors du chargement des données financières', 'error');
            });
        }

        function blockSpecialistWallet(specialistId) {
            if (confirm('Êtes-vous sûr de vouloir bloquer le portefeuille de ce spécialiste ?')) {
                fetch(`/admin-system/specialists/${specialistId}/block-wallet`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        refreshFinancialData();
                    } else {
                        showNotification('Erreur lors du blocage', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur lors du blocage', 'error');
                });
            }
        }

        function openTestRechargeModal() {
            // Create a modal matching the dashboard style
            const modal = document.createElement('div');
            modal.id = 'testRechargeModal';
            modal.className = 'fixed inset-0 bg-black/70 backdrop-blur-md z-50 flex items-center justify-center p-6 animate-in fade-in duration-300';
            modal.innerHTML = `
                <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100">
                    <!-- Header -->
                    <div class="p-10 border-b border-slate-200 bg-gradient-to-r from-orange-600 via-orange-500 to-red-600 text-white relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-600/90 to-red-600/90"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <h3 class="text-4xl font-black tracking-tight">Test Recharge 10k</h3>
                                <p class="text-orange-100 mt-3 font-medium text-lg">Simulation de paiement CinetPay pour tests</p>
                            </div>
                            <button onclick="closeTestRechargeModal()" class="text-orange-200 hover:text-white p-4 hover:bg-white/20 rounded-3xl transition-all duration-300 hover:scale-110 backdrop-blur-sm">
                                <i class="bi bi-x-lg text-3xl"></i>
                            </button>
                        </div>
                        <!-- Decorative elements -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/5 rounded-full"></div>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="overflow-y-auto flex-1 bg-slate-50/30">
                        <div class="p-10">
                            <form id="testRechargeForm" onsubmit="runTestRecharge(); return false;" class="space-y-10">
                                <!-- Section 1: Informations générales -->
                                <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                                    <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-orange-600 rounded-2xl flex items-center justify-center">
                                            <i class="bi bi-info-circle-fill text-white text-sm"></i>
                                        </div>
                                        Simulation de Paiement
                                    </h4>

                                    <div class="space-y-6">
                                        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl border-2 border-blue-200">
                                            <h5 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-3">
                                                <i class="bi bi-cash-coin text-lg"></i>
                                                Comment ça fonctionne ?
                                            </h5>
                                            <div class="text-base text-blue-800 space-y-2">
                                                <p>• Cette simulation reproduit exactement le paiement réel de 10 000 FCFA</p>
                                                <p>• <strong>4 000 FCFA</strong> seront ajoutés aux <strong>Revenus SaaS Total</strong></p>
                                                <p>• <strong>6 000 FCFA</strong> seront crédités dans le portefeuille du spécialiste</p>
                                                <p>• Le portefeuille sera automatiquement activé</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Sélection du spécialiste -->
                                <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                                    <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-green-600 rounded-2xl flex items-center justify-center">
                                            <i class="bi bi-person-badge-fill text-white text-sm"></i>
                                        </div>
                                        Sélection du Spécialiste
                                    </h4>

                                    <div class="space-y-6">
                                        <div class="space-y-4">
                                            <label class="block text-xl font-bold text-slate-800">Spécialiste à tester</label>
                                            <select id="testSpecialistSelect" name="specialist_id" required
                                                    class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-xl font-medium">
                                                <option value="">Chargement des spécialistes...</option>
                                            </select>
                                            <p class="text-sm text-slate-500 font-medium">Sélectionnez le spécialiste qui recevra les 6 000 FCFA</p>
                                        </div>

                                        <!-- Informations du paiement simulé -->
                                        <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-3xl border-2 border-green-200">
                                            <h5 class="text-xl font-bold text-green-900 mb-4 flex items-center gap-3">
                                                <i class="bi bi-calculator-fill text-lg"></i>
                                                Détail du paiement simulé
                                            </h5>
                                            <div class="grid md:grid-cols-2 gap-6">
                                                <div class="text-center p-4 bg-white rounded-2xl border border-green-200">
                                                    <div class="text-3xl font-black text-green-600 mb-2">10 000 FCFA</div>
                                                    <div class="text-sm font-bold text-slate-600">Paiement simulé</div>
                                                    <div class="text-xs text-slate-400 mt-1">Via CinetPay (mode test)</div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl border border-blue-200">
                                                        <span class="font-bold text-blue-900">Super Admin</span>
                                                        <span class="font-black text-blue-600">+4 000 FCFA</span>
                                                    </div>
                                                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl border border-purple-200">
                                                        <span class="font-bold text-purple-900">Spécialiste</span>
                                                        <span class="font-black text-purple-600">+6 000 FCFA</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Confirmation -->
                                <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                                    <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-red-600 rounded-2xl flex items-center justify-center">
                                            <i class="bi bi-exclamation-triangle-fill text-white text-sm"></i>
                                        </div>
                                        Confirmation
                                    </h4>

                                    <div class="space-y-6">
                                        <div class="p-6 bg-gradient-to-r from-red-50 to-pink-50 rounded-3xl border-2 border-red-200">
                                            <h5 class="text-xl font-bold text-red-900 mb-4 flex items-center gap-3">
                                                <i class="bi bi-shield-check text-lg"></i>
                                                Points importants
                                            </h5>
                                            <div class="text-base text-red-800 space-y-2">
                                                <p>• Cette action est <strong>irréversible</strong> dans la base de données de test</p>
                                                <p>• Les compteurs financiers seront mis à jour immédiatement</p>
                                                <p>• Le portefeuille du spécialiste sera activé automatiquement</p>
                                                <p>• Utilisez cette fonction uniquement pour les tests</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center p-6 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-3xl border-2 border-yellow-200">
                                            <input type="checkbox" id="confirmTest" required
                                                   class="w-6 h-6 text-yellow-600 bg-slate-50 border-yellow-300 rounded focus:ring-yellow-500 focus:ring-2">
                                            <label for="confirmTest" class="ml-6 text-xl font-bold text-slate-800">
                                                J'ai lu et compris que ceci est une simulation de test
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Footer with Actions -->
                    <div class="p-10 border-t border-slate-200 bg-white">
                        <div class="flex gap-6">
                            <button type="button" onclick="closeTestRechargeModal()"
                                    class="flex-1 px-10 py-6 border-2 border-slate-300 text-slate-700 rounded-3xl hover:bg-slate-100 hover:border-slate-400 transition-all duration-300 font-bold text-xl hover:scale-105">
                                <i class="bi bi-x-circle mr-3"></i>
                                Annuler
                            </button>
                            <button type="submit" form="testRechargeForm"
                                    class="flex-1 px-10 py-6 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-3xl transition-all duration-300 font-bold shadow-2xl shadow-orange-200 hover:shadow-orange-300 flex items-center justify-center gap-4 text-xl transform hover:scale-105 border-2 border-orange-500">
                                <i class="bi bi-play-circle-fill text-2xl"></i>
                                <span class="font-black">LANCER LA SIMULATION</span>
                                <i class="bi bi-arrow-right text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Load specialists
            loadSpecialistsForTest();
        }

        function closeTestRechargeModal() {
            const modal = document.getElementById('testRechargeModal');
            if (modal) {
                modal.remove();
            }
        }

        function loadSpecialistsForTest() {
            fetch('/admin-system/test-specialists')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('testSpecialistSelect');
                    select.innerHTML = '<option value="">Sélectionnez un spécialiste...</option>';

                    if (data.specialists && data.specialists.length > 0) {
                        data.specialists.forEach(specialist => {
                            const option = document.createElement('option');
                            option.value = specialist.id;
                            option.textContent = `${specialist.name} (${specialist.specialty})`;
                            select.appendChild(option);
                        });
                    } else {
                        select.innerHTML = '<option value="">Aucun spécialiste disponible</option>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des spécialistes:', error);
                    const select = document.getElementById('testSpecialistSelect');
                    select.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }

        function runTestRecharge() {
            const specialistId = document.getElementById('testSpecialistSelect').value;
            const confirmCheckbox = document.getElementById('confirmTest');

            if (!specialistId) {
                showNotification('Veuillez sélectionner un spécialiste', 'error');
                return;
            }

            if (!confirmCheckbox.checked) {
                showNotification('Veuillez confirmer que vous comprenez que ceci est une simulation', 'error');
                return;
            }

            // Disable button and show loading
            const submitBtn = document.querySelector('#testRechargeForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin text-2xl"></i> <span class="font-black">SIMULATION EN COURS...</span>';

            // Send request
            fetch('/superadmin/specialists/test-recharge', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    specialist_id: specialistId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Simulation de recharge réussie !', 'success');
                    closeTestRechargeModal();

                    // Refresh dashboard data after 2 seconds
                    setTimeout(() => {
                        loadFinancialData();
                        loadRecentTransactions();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Erreur lors de la simulation', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur réseau lors de la simulation', 'error');
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        function loadSpecialistsForTest() {
            fetch('/admin-system/test-specialists')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('testSpecialistSelect');
                    select.innerHTML = '<option value="">Sélectionnez un spécialiste...</option>';

                    if (data.specialists && data.specialists.length > 0) {
                        data.specialists.forEach(specialist => {
                            const option = document.createElement('option');
                            option.value = specialist.id;
                            option.textContent = `${specialist.name} (${specialist.specialty})`;
                            select.appendChild(option);
                        });
                    } else {
                        select.innerHTML = '<option value="">Aucun spécialiste disponible</option>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des spécialistes:', error);
                    const select = document.getElementById('testSpecialistSelect');
                    select.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }

        function runTestRecharge() {
            const specialistId = document.getElementById('testSpecialistSelect').value;

            if (!specialistId) {
                showNotification('Veuillez sélectionner un spécialiste', 'error');
                return;
            }

            // Show loading
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-hourglass-split text-xl animate-spin"></i> Simulation en cours...';
            button.disabled = true;

            fetch('/admin-system/specialists/test-recharge', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    specialist_id: specialistId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Simulation réussie ! Vérifiez les compteurs.', 'success');
                    closeTestRechargeModal();
                    // Refresh financial data after a short delay
                    setTimeout(refreshFinancialData, 1000);
                } else {
                    showNotification('Erreur: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors de la simulation', 'error');
            })
            .finally(() => {
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        // === INVOICES MANAGEMENT ===

        function refreshInvoicesData() {
            loadInvoiceStats();
            loadInvoicesTable();
        }


        function loadInvoiceStats() {
            fetch('/admin-system/invoices', {
        // Simple notification function
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
                    { label: 'Revenus Totaux', value: data.stats.total_revenue, icon: 'bi-cash-stack', color: 'green' },
                    { label: 'Montant Payé', value: data.stats.total_paid, icon: 'bi-check-circle', color: 'blue' },
                    { label: 'Montant Restant', value: data.stats.total_pending, icon: 'bi-clock-history', color: 'orange' },
                    { label: 'Factures Payées', value: data.stats.paid_invoices, icon: 'bi-receipt', color: 'purple' }
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
            fetch('/admin-system/invoices', {
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
                                <div class="font-medium text-slate-900">${invoice.hospital_name}</div>
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
                    tableBody.innerHTML = '<tr><td colspan="8" class="px-8 py-12 text-center text-slate-400">Aucune facture trouvée</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erreur lors du chargement des factures', 'error');
            });
        }

        // Load data when tabs are activated
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for tab switching to load data
            const subscriptionPlansTab = document.getElementById('btn-subscription-plans');
            const commissionRatesTab = document.getElementById('btn-commission-rates');
            const financialMonitoringTab = document.getElementById('btn-financial-monitoring');
            const invoicesTab = document.getElementById('btn-invoices');

            if (subscriptionPlansTab) {
                subscriptionPlansTab.addEventListener('click', function() {
                    setTimeout(loadSubscriptionPlans, 100);
                });
            }

            if (commissionRatesTab) {
                commissionRatesTab.addEventListener('click', function() {
                    setTimeout(loadCommissionRates, 100);
                });
            }

            if (financialMonitoringTab) {
                financialMonitoringTab.addEventListener('click', function() {
                    setTimeout(refreshFinancialData, 100);
                });
            }

            if (invoicesTab) {
                invoicesTab.addEventListener('click', function() {
                    setTimeout(refreshInvoicesData, 100);
                });
            }

            // Initialize features preview
            updateFeaturesPreview();

            // Initialize commission brackets
            addCommissionBracket('0', '15000', '15');
            addCommissionBracket('15001', '30000', '20');
            addCommissionBracket('30001', '', '25');
        });
        function showNotification(message, type = 'info') {
            // Create notification element
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

    <!-- Onglet Financial Monitoring -->
    <div id="tab-financial-monitoring" class="tab-pane animate-in slide-in-from-bottom-8 duration-500">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 text-left gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Monitoring Financier & Portefeuilles</h2>
                <p class="text-slate-500 font-medium">Surveillez les revenus, commissions et gestion des portefeuilles spécialistes.</p>
            </div>
            <button onclick="openTestRechargeModal()" class="bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white px-8 py-4 rounded-3xl font-bold transition shadow-2xl shadow-orange-200 flex items-center justify-center gap-3 group">
                <i class="bi bi-play-circle-fill group-hover:scale-125 transition-transform"></i>
                Test Recharge 10k
            </button>
        </div>

        <!-- Statistiques Financières -->
        <div id="financialStats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 text-left">
            <!-- Les statistiques seront chargées dynamiquement -->
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Liste des Spécialistes -->
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden text-left">
                <div class="p-8 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                        <span class="w-2 h-8 bg-purple-600 rounded-full"></span>
                        Portefeuilles Spécialistes
                    </h3>
                    <p class="text-slate-500 mt-2 font-medium">État des comptes et soldes des spécialistes</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100">
                                <th class="px-8 py-6">Spécialiste</th>
                                <th class="px-8 py-6">Solde</th>
                                <th class="px-8 py-6 text-center">Statut</th>
                                <th class="px-8 py-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="specialistsFinancialList">
                            <!-- Les données seront chargées dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions Récentes -->
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm text-left">
                <div class="p-8 border-b border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                        <span class="w-2 h-8 bg-green-600 rounded-full"></span>
                        Transactions Récentes
                    </h3>
                    <p class="text-slate-500 mt-2 font-medium">Historique des mouvements financiers</p>
                </div>
                <div class="p-8 space-y-4" id="recentTransactionsList">
                    <!-- Les transactions seront chargées dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Subscription Plan -->
    <div id="newPlanModal" class="fixed inset-0 bg-black/70 backdrop-blur-md hidden z-50 flex items-center justify-center p-6 animate-in fade-in duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="p-10 border-b border-slate-200 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 to-indigo-600/90"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-4xl font-black tracking-tight">Nouveau Plan d'Abonnement</h3>
                        <p class="text-blue-100 mt-3 font-medium text-lg">Configurez un nouveau plan SaaS pour vos hôpitaux</p>
                    </div>
                    <button onclick="closeNewPlanModal()" class="text-blue-200 hover:text-white p-4 hover:bg-white/20 rounded-3xl transition-all duration-300 hover:scale-110 backdrop-blur-sm">
                        <i class="bi bi-x-lg text-3xl"></i>
                    </button>
                </div>
                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/5 rounded-full"></div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1 bg-slate-50/30">
                <div class="p-10">
                    <form id="planForm" onsubmit="submitNewPlan(); return false;" class="space-y-10">
                        <!-- Section 1: Informations de base -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-info-circle-fill text-white text-sm"></i>
                                </div>
                                Informations de base
                            </h4>

                            <div class="grid md:grid-cols-2 gap-8">
                                <!-- Nom du Forfait -->
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Nom du Forfait</label>
                                    <input type="text" name="name" required
                                           class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-xl font-medium placeholder-slate-400"
                                           placeholder="Ex: Plan Premium, Plan Basic...">
                                </div>

                                <!-- Type de cible -->
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Type de Cible</label>
                                    <select name="target_type" required
                                            class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-xl font-medium">
                                        <option value="">Sélectionner le type d'établissement</option>
                                        <option value="hopital_physique">🏥 Hôpital Physique</option>
                                        <option value="clinique_privee">🏨 Clinique Privée</option>
                                    </select>
                                    <p class="text-sm text-slate-500 font-medium mt-2">Détermine le type d'établissement cible pour ce plan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Tarification -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-cash-stack text-white text-sm"></i>
                                </div>
                                Tarification & Durée
                            </h4>

                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Prix (FCFA)</label>
                                    <div class="relative">
                                        <input type="number" name="price" step="0.01" min="0" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-xl font-medium"
                                               placeholder="50000">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">₣</span>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Fréquence</label>
                                    <select name="duration_unit" required
                                            class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-xl font-medium">
                                        <option value="month">📅 Mensuel</option>
                                        <option value="year">📆 Annuel</option>
                                    </select>
                                </div>
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Durée</label>
                                    <input type="number" name="duration_value" min="1" value="1" required
                                           class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-xl font-medium"
                                           placeholder="1">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Fonctionnalités -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-star-fill text-white text-sm"></i>
                                </div>
                                Fonctionnalités Incluses
                            </h4>

                            <div class="space-y-6">
                                <textarea name="features" rows="10" id="featuresTextarea"
                                          class="w-full px-6 py-5 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 text-xl font-medium resize-none"
                                          placeholder="• Nombre de patients illimité&#10;• Support technique 24/7&#10;• Rapports avancés et analytics&#10;• Intégration API complète&#10;• Sauvegarde automatique"
                                          oninput="updateFeaturesPreview()"></textarea>
                                <p class="text-base text-slate-500 font-medium">Une fonctionnalité par ligne, commencez par • pour une meilleure présentation</p>

                                <!-- Aperçu des fonctionnalités -->
                                <div id="featuresPreview" class="p-8 bg-gradient-to-r from-purple-50 to-pink-50 rounded-3xl border-2 border-purple-200">
                                    <h5 class="text-2xl font-bold text-purple-900 mb-6 flex items-center gap-3">
                                        <i class="bi bi-eye-fill text-xl"></i>
                                        Aperçu des fonctionnalités
                                    </h5>
                                    <div id="featuresList" class="flex flex-wrap gap-4">
                                        <!-- Les badges seront générés ici -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Statut -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-toggle-on text-white text-sm"></i>
                                </div>
                                Paramètres finaux
                            </h4>

                            <div class="flex items-center p-6 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-3xl border-2 border-orange-200">
                                <input type="checkbox" name="is_active" id="plan_active" checked
                                       class="w-6 h-6 text-orange-600 bg-slate-50 border-orange-300 rounded focus:ring-orange-500 focus:ring-2">
                                <label for="plan_active" class="ml-6 text-xl font-bold text-slate-800">Activer ce plan immédiatement</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer with Actions -->
            <div class="p-10 border-t border-slate-200 bg-white">
                <div class="flex gap-6">
                    <button type="button" onclick="closeNewPlanModal()"
                            class="flex-1 px-10 py-6 border-2 border-slate-300 text-slate-700 rounded-3xl hover:bg-slate-100 hover:border-slate-400 transition-all duration-300 font-bold text-xl hover:scale-105">
                        <i class="bi bi-x-circle mr-3"></i>
                        Annuler
                    </button>
                    <button type="submit" form="planForm"
                            class="flex-1 px-10 py-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-3xl transition-all duration-300 font-bold shadow-2xl shadow-blue-200 hover:shadow-blue-300 flex items-center justify-center gap-4 text-xl transform hover:scale-105 border-2 border-blue-500">
                        <i class="bi bi-check-circle-fill text-2xl"></i>
                        <span class="font-black">VALIDER LE PLAN</span>
                        <i class="bi bi-arrow-right text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Commission Rule -->
    <div id="newCommissionModal" class="fixed inset-0 bg-black/70 backdrop-blur-md hidden z-50 flex items-center justify-center p-6 animate-in fade-in duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100">
            <!-- Header -->
            <div class="p-10 border-b border-slate-200 bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-600 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/90 to-indigo-600/90"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-4xl font-black tracking-tight">Nouvelle Règle de Commission</h3>
                        <p class="text-purple-100 mt-3 font-medium text-lg">Configurez une nouvelle règle de prélèvement pour les spécialistes</p>
                    </div>
                    <button onclick="closeNewCommissionModal()" class="text-purple-200 hover:text-white p-4 hover:bg-white/20 rounded-3xl transition-all duration-300 hover:scale-110 backdrop-blur-sm">
                        <i class="bi bi-x-lg text-3xl"></i>
                    </button>
                </div>
                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/5 rounded-full"></div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1 bg-slate-50/30">
                <div class="p-10">
                    <form id="commissionForm" onsubmit="submitNewCommission(); return false;" class="space-y-10">
                        <!-- Section 1: Configuration des Commissions -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-percent text-white text-sm"></i>
                                </div>
                                Configuration des Commissions par Tranche de Prix
                            </h4>

                            <div class="space-y-6">
                                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl border-2 border-blue-200">
                                    <h5 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-3">
                                        <i class="bi bi-info-circle-fill text-lg"></i>
                                        Comment ça fonctionne ?
                                    </h5>
                                    <div class="text-base text-blue-800 space-y-2">
                                        <p>• Les <strong>spécialistes définissent</strong> leurs propres noms de prestations et prix</p>
                                        <p>• Le <strong>système applique automatiquement</strong> le pourcentage de commission selon la tranche de prix</p>
                                        <p>• <strong>Exemple:</strong> Si un spécialiste fixe sa consultation à 7,000 FCFA et que vous avez défini 5,000-10,000 FCFA à 10%, le système prélève automatiquement 10%</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Tranches de Prix et Commissions -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-bar-chart-line-fill text-white text-sm"></i>
                                </div>
                                Tranches de Prix et Commissions
                            </h4>

                            <div class="space-y-6">
                                <!-- Liste des tranches -->
                                <div id="commissionBrackets" class="space-y-4">
                                    <!-- Les tranches seront ajoutées ici dynamiquement -->
                                </div>

                                <!-- Bouton ajouter une tranche -->
                                <button type="button" onclick="addCommissionBracket(); return false;"
                                        class="w-full py-4 px-6 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-3xl transition-all duration-300 font-bold text-lg flex items-center justify-center gap-3 hover:scale-105">
                                    <i class="bi bi-plus-circle-fill text-xl"></i>
                                    Ajouter une Tranche de Prix
                                </button>

                                <!-- Exemple d'utilisation -->
                                <div class="p-6 bg-blue-50 rounded-3xl border-2 border-blue-200">
                                    <h5 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-3">
                                        <i class="bi bi-info-circle-fill text-xl"></i>
                                        Comment ça fonctionne ?
                                    </h5>
                                    <div class="text-base text-blue-800 space-y-2">
                                        <p>• <strong>Tranche 1:</strong> 0 - 15,000 FCFA → 15% de commission</p>
                                        <p>• <strong>Tranche 2:</strong> 15,001 - 30,000 FCFA → 20% de commission</p>
                                        <p>• <strong>Tranche 3:</strong> 30,001+ FCFA → 25% de commission</p>
                                        <p class="mt-3 font-medium">Si un spécialiste fixe sa consultation à 30,000 FCFA, le système applique automatiquement 20% → 6,000 FCFA de commission.</p>
                                    </div>
                                </div>

                                <!-- Test du calcul -->
                                <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-3xl border-2 border-green-200">
                                    <h5 class="text-xl font-bold text-green-900 mb-4 flex items-center gap-3">
                                        <i class="bi bi-calculator-fill text-xl"></i>
                                        Test du Calcul
                                    </h5>
                                    <div class="flex gap-4 items-end">
                                        <div class="flex-1">
                                            <label class="block text-lg font-bold text-slate-800 mb-2">Prix de Test (FCFA)</label>
                                            <div class="relative">
                                                <input type="number" id="testPrice" step="0.01" min="0"
                                                       class="w-full px-6 py-4 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 text-xl font-medium"
                                                       placeholder="30000" oninput="testCommissionCalculation()">
                                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">₣</span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="testCommissionCalculation()"
                                                class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-3xl transition-all duration-300 font-bold text-lg hover:scale-105">
                                            Calculer
                                        </button>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <div class="text-3xl font-black text-slate-900" id="testResult">0 FCFA</div>
                                        <p class="text-lg text-slate-600" id="testText">Entrez un prix pour tester le calcul</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Paramètres avancés -->
                        <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200/50">
                            <h4 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-600 rounded-2xl flex items-center justify-center">
                                    <i class="bi bi-gear-fill text-white text-sm"></i>
                                </div>
                                Paramètres Avancés
                            </h4>

                            <div class="space-y-6">
                                <!-- Frais d'activation -->
                                <div class="space-y-4">
                                    <label class="block text-xl font-bold text-slate-800">Frais d'Activation (FCFA)</label>
                                    <div class="relative">
                                        <input type="number" name="activation_fee" step="0.01" min="0" value="4000" required
                                               class="w-full px-6 py-5 pl-12 bg-slate-50 border-2 border-slate-200 rounded-3xl focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all duration-300 text-xl font-medium"
                                               placeholder="4000">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-bold text-lg">₣</span>
                                    </div>
                                    <p class="text-base text-slate-500 font-medium">Montant retiré à la première recharge du portefeuille</p>
                                </div>

                                <!-- Statut -->
                                <div class="flex items-center p-6 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-3xl border-2 border-orange-200">
                                    <input type="checkbox" name="is_active" id="commission_active" checked
                                           class="w-6 h-6 text-orange-600 bg-slate-50 border-orange-300 rounded focus:ring-orange-500 focus:ring-2">
                                    <label for="commission_active" class="ml-6 text-xl font-bold text-slate-800">Activer cette règle immédiatement</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer with Actions -->
            <div class="p-10 border-t border-slate-200 bg-white">
                <div class="flex gap-6">
                    <button type="button" onclick="closeNewCommissionModal()"
                            class="flex-1 px-10 py-6 border-2 border-slate-300 text-slate-700 rounded-3xl hover:bg-slate-100 hover:border-slate-400 transition-all duration-300 font-bold text-xl hover:scale-105">
                        <i class="bi bi-x-circle mr-3"></i>
                        Annuler
                    </button>
                    <button type="submit" form="commissionForm"
                            class="flex-1 px-10 py-6 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-3xl transition-all duration-300 font-bold shadow-2xl shadow-purple-200 hover:shadow-purple-300 flex items-center justify-center gap-4 text-xl transform hover:scale-105 border-2 border-purple-500">
                        <i class="bi bi-check-circle-fill text-2xl"></i>
                        <span class="font-black">VALIDER LA RÈGLE</span>
                        <i class="bi bi-arrow-right text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

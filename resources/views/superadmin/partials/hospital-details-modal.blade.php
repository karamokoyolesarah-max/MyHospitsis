<!-- Modal Détails Hôpital (Shared Partial) -->
<div id="hospitalDetailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[200] hidden">
    <div class="bg-white shadow-2xl w-full h-full overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-900" id="modalHospitalName">Détails de l'Hôpital</h3>
                    <p class="text-slate-500 text-sm font-medium">Infrastructure et gestion hospitalière</p>
                </div>
                <button onclick="closeHospitalDetailsModal()" class="w-10 h-10 flex items-center justify-center bg-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all hover:rotate-90">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Compact Stats Bar -->
        <div class="px-8 py-4 bg-slate-50 border-b border-slate-200">
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="text-xl font-black text-slate-900" id="statsUsers">0</div>
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Utilisateurs</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="text-xl font-black text-slate-900" id="statsServices">0</div>
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Services</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="text-xl font-black text-blue-600" id="statsPatients">0</div>
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Patients</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col items-center justify-center text-center ring-2 ring-blue-50">
                    <div class="text-xl font-black text-blue-600" id="statsCashiers">0</div>
                    <div class="text-[9px] font-black text-blue-500 uppercase tracking-widest mt-0.5">Caissiers</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="text-xl font-black text-slate-900" id="statsPrestations">0</div>
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Prestations</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="text-xl font-black text-emerald-600" id="statsActiveUsers">0</div>
                    <div class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-0.5">Actifs</div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white border-b border-slate-200">
            <div class="flex gap-2 px-8 overflow-x-auto no-scrollbar">
                <button onclick="switchHospitalTab('company')" id="btn-company" class="hospital-tab-btn active-hospital-tab flex items-center gap-2 px-6 py-4 font-black text-[10px] uppercase tracking-widest transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-building"></i> Entreprise
                </button>
                <button onclick="switchHospitalTab('users')" id="btn-users" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-people"></i> Utilisateurs
                </button>
                <button onclick="switchHospitalTab('services')" id="btn-services" class="hospital-tab-btn flex items-center gap-2 px-8 py-6 font-black text-xs uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-hospital"></i> Services
                </button>
                <button onclick="switchHospitalTab('patients')" id="btn-patients" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-person-lines-fill"></i> Patients
                </button>
                <button onclick="switchHospitalTab('prestations')" id="btn-prestations" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-cash-stack"></i> Prestations
                </button>
                <button onclick="switchHospitalTab('caisses')" id="btn-caisses" class="hospital-tab-btn flex items-center gap-2 px-6 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400 hover:text-orange-600 transition-all whitespace-nowrap border-b-4 border-transparent">
                    <i class="bi bi-cash-register"></i> Caisses
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="overflow-y-auto flex-1 bg-slate-50/30">
            <!-- Tab: Entreprise -->
            <div id="tab-company" class="hospital-tab-pane active p-10">
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Informations Générales</label>
                            <div class="space-y-4">
                                <div>
                                    <div class="text-xs font-bold text-slate-400 mb-1">Nom de l'établissement</div>
                                    <div class="text-xl font-black text-slate-900" id="companyName">-</div>
                                </div>
                                <div class="pt-4 border-t border-slate-50">
                                    <div class="text-xs font-bold text-slate-400 mb-1">Localisation</div>
                                    <div class="text-slate-700 font-medium" id="companyAddress">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">État de l'Instance</label>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-bold text-slate-600">Statut du Serveur</div>
                                    <span class="font-black" id="companyStatus">-</span>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                    <div class="text-sm font-bold text-slate-600">Offre Active</div>
                                    <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-100">PREMIUM CLOUD</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Instance SaaS</label>
                            <div class="p-6 bg-blue-50 rounded-[1.5rem] border border-blue-100 flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                                    <i class="bi bi-cloud-check text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-900">Domaine Host</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">host.hospitsis.com</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Utilisateurs -->
            <div id="tab-users" class="hospital-tab-pane hidden p-10">
                <!-- Role Filter Bar (Horizontal Sidebar style) -->
                <div class="flex gap-4 mb-8 overflow-x-auto no-scrollbar pb-2 border-b border-slate-100">
                    <div class="flex p-2 bg-slate-100/50 border-2 border-slate-200/50 rounded-[2rem] shadow-inner w-fit gap-2">
                        <button onclick="filterUsers('all')" class="user-filter-btn active-filter px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center gap-3 whitespace-nowrap" data-role="all">
                            <i class="bi bi-grid-fill"></i>
                            Tous
                        </button>
                        <button onclick="filterUsers('doctor')" class="user-filter-btn px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all flex items-center gap-3 whitespace-nowrap bg-white border border-slate-200" data-role="doctor">
                            <i class="bi bi-person-pulse-fill"></i>
                            Docteurs
                        </button>
                        <button onclick="filterUsers('nurse')" class="user-filter-btn px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all flex items-center gap-3 whitespace-nowrap bg-white border border-slate-200" data-role="nurse">
                            <i class="bi bi-bandaid-fill"></i>
                            Infirmiers
                        </button>
                        <button onclick="filterUsers('cashier')" class="user-filter-btn px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all flex items-center gap-3 whitespace-nowrap bg-white border border-slate-200" data-role="cashier">
                            <i class="bi bi-cash-stack"></i>
                            Caissiers
                        </button>
                        <button onclick="filterUsers('technician')" class="user-filter-btn px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all flex items-center gap-3 whitespace-nowrap bg-white border border-slate-200" data-role="technician">
                            <i class="bi bi-flask-fill"></i>
                            Labo / Tech
                        </button>
                        <button onclick="filterUsers('administrative')" class="user-filter-btn px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-all flex items-center gap-3 whitespace-nowrap bg-white border border-slate-200" data-role="administrative">
                            <i class="bi bi-shield-lock-fill"></i>
                            Admin
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="usersList">
                    <!-- Users populated by JS -->
                </div>
            </div>

            <!-- Tab: Services -->
            <div id="tab-services" class="hospital-tab-pane hidden p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="servicesList">
                    <!-- Services populated by JS -->
                </div>
            </div>

            <!-- Tab: Patients -->
            <div id="tab-patients" class="hospital-tab-pane hidden p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="patientsList">
                    <!-- Patients populated by JS -->
                </div>
            </div>

            <!-- Tab: Prestations -->
            <div id="tab-prestations" class="hospital-tab-pane hidden p-10">
                <div class="space-y-10" id="prestationsList">
                    <!-- Prestations populated by JS -->
                </div>
            </div>

            <!-- Tab: Caisses -->
            <div id="tab-caisses" class="hospital-tab-pane hidden p-10">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white p-12 rounded-[3rem] border-2 border-slate-100 shadow-xl text-center">
                        <div class="w-24 h-24 bg-orange-100 text-orange-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-lg shadow-orange-100">
                            <i class="bi bi-cash-register text-5xl"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tighter">Configuration des Caisses</h3>
                        <p class="text-slate-500 font-medium text-lg mb-10 max-w-xl mx-auto">
                            Initialisez les 3 caisses réglementaires (Accueil, Pharmacie/Labo, Urgences) avec leurs comptes utilisateurs par défaut.
                        </p>
                        
                        <div class="grid md:grid-cols-3 gap-6 mb-12 text-left">
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-200">
                                <div class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Caisse 1</div>
                                <div class="font-black text-slate-900">ACCUEIL</div>
                                <div class="text-xs text-slate-400 font-bold">Consultations & Hospitalisations</div>
                            </div>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-200">
                                <div class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Caisse 2</div>
                                <div class="font-black text-slate-900">RECOUVREMENT</div>
                                <div class="text-xs text-slate-400 font-bold">Pharmacie, Labo & Imagerie</div>
                            </div>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-200">
                                <div class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Caisse 3</div>
                                <div class="font-black text-slate-900">URGENCES</div>
                                <div class="text-xs text-slate-400 font-bold">Salles de soins 24h/24</div>
                            </div>
                        </div>

                        <div id="caisseActionContainer">
                            <button onclick="initializeCashiers(currentHospitalId)" class="w-full py-6 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-3xl font-black text-lg hover:from-orange-700 hover:to-orange-600 transition-all shadow-2xl shadow-orange-200 flex items-center justify-center gap-4 group hover:scale-[1.02]">
                                <i class="bi bi-magic text-2xl"></i>
                                INITIALISER LES 3 CAISSES TYPES
                                <i class="bi bi-arrow-right text-xl group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                        
                        <div class="mt-8 flex items-center justify-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            <i class="bi bi-shield-lock-fill text-orange-400"></i>
                            Action irréversible • Déploiement groupé
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hospital-tab-pane.hidden { display: none; }
    .hospital-tab-pane.active { display: block; }
    .active-hospital-tab {
        color: #2563eb !important;
        border-bottom-color: #2563eb !important;
        background: rgba(37, 99, 235, 0.05);
    }
    #btn-caisses.active-hospital-tab {
        color: #ea580c !important;
        border-bottom-color: #ea580c !important;
        background: rgba(234, 88, 12, 0.05);
    }
    .user-filter-btn.active-filter {
        background: #2563eb;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }
</style>

<script>
    let currentHospitalId = null;
    let cachedUsers = [];

    function openHospitalDetails(hospitalId) {
        currentHospitalId = hospitalId;
        fetch(`{{ url('admin-system/hospitals') }}/${hospitalId}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                populateHospitalModal(data);
                const modal = document.getElementById('hospitalDetailsModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        })
        .catch(err => console.error('Error:', err));
    }

    function closeHospitalDetailsModal() {
        const modal = document.getElementById('hospitalDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentHospitalId = null;
        cachedUsers = [];
    }

    function initializeCashiers(hospitalId) {
        if(!confirm('Voulez-vous déployer les 3 caisses réglementaires pour cet hôpital ?')) return;

        fetch(`{{ url('admin-system/hospitals') }}/${hospitalId}/initialize-cashiers`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showNotification(data.message);
                // Rafraîchir les détails pour voir les nouveaux caissiers
                openHospitalDetails(hospitalId);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showNotification('Erreur réseau', 'error');
        });
    }

    function switchHospitalTab(tabId) {
        document.querySelectorAll('.hospital-tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.hospital-tab-pane').forEach(el => el.classList.remove('active'));
        
        document.querySelectorAll('.hospital-tab-btn').forEach(btn => {
            btn.classList.remove('active-hospital-tab');
            btn.classList.add('text-slate-400');
        });

        const target = document.getElementById('tab-' + tabId);
        const btn = document.getElementById('btn-' + tabId);
        
        if(target) {
            target.classList.remove('hidden');
            target.classList.add('active');
        }
        if(btn) {
            btn.classList.add('active-hospital-tab');
            btn.classList.remove('text-slate-400');
        }
    }

    function populateHospitalModal(data) {
        const { hospital, stats } = data;
        
        document.getElementById('modalHospitalName').textContent = hospital.name;
        document.getElementById('companyName').textContent = hospital.name;
        document.getElementById('companyAddress').textContent = hospital.address || 'Non spécifiée';
        
        const statusSpan = document.getElementById('companyStatus');
        statusSpan.textContent = hospital.is_active ? 'ACTIF' : 'INACTIF';
        statusSpan.className = hospital.is_active ? 'text-emerald-600 font-black' : 'text-red-600 font-black';

        document.getElementById('statsUsers').textContent = stats.total_users;
        document.getElementById('statsServices').textContent = stats.total_services;
        document.getElementById('statsPatients').textContent = stats.total_patients;
        document.getElementById('statsCashiers').textContent = stats.total_cashiers;
        document.getElementById('statsPrestations').textContent = stats.total_prestations;
        document.getElementById('statsActiveUsers').textContent = stats.active_users;

        // Cache users for filtering
        cachedUsers = hospital.users || [];
        
        // Reset role filter UI
        document.querySelectorAll('.user-filter-btn').forEach(btn => {
            btn.classList.remove('active-filter');
            btn.classList.add('text-slate-400');
            if(btn.dataset.role === 'all') {
                btn.classList.add('active-filter');
                btn.classList.remove('text-slate-400');
            }
        });

        // Render Lists
        renderUsers(cachedUsers);
        renderServices(hospital.services);
        renderPatients(hospital.patients);
        renderPrestations(hospital.prestations);

        // Update Caisses button status
        const caisseContainer = document.getElementById('caisseActionContainer');
        if (stats.total_cashiers >= 3) {
            caisseContainer.innerHTML = `
                <div class="p-6 bg-emerald-50 rounded-3xl border-2 border-emerald-100 flex items-center justify-center gap-4 text-emerald-700 font-black">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                    LES 3 CAISSES ONT ÉTÉ INITIALISÉES AVEC SUCCÈS
                </div>
            `;
        } else {
            caisseContainer.innerHTML = `
                <button onclick="initializeCashiers(${hospital.id})" class="w-full py-6 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-3xl font-black text-lg hover:from-orange-700 hover:to-orange-600 transition-all shadow-2xl shadow-orange-200 flex items-center justify-center gap-4 group hover:scale-[1.02]">
                    <i class="bi bi-magic text-2xl"></i>
                    INITIALISER LES 3 CAISSES TYPES
                    <i class="bi bi-arrow-right text-xl group-hover:translate-x-1 transition-transform"></i>
                </button>
            `;
        }
    }

    function filterUsers(category) {
        // Toggle UI
        document.querySelectorAll('.user-filter-btn').forEach(btn => {
            btn.classList.remove('active-filter');
            btn.classList.add('text-slate-400');
            if(btn.dataset.role === category) {
                btn.classList.add('active-filter');
                btn.classList.remove('text-slate-400');
            }
        });

        // Role Groupings
        const groupings = {
            'doctor': ['doctor', 'internal_doctor', 'external_doctor', 'medecin', 'medecin_externe', 'doctor_lab'],
            'nurse': ['nurse'],
            'cashier': ['cashier', 'receptionist', 'receptioniste'],
            'technician': ['doctor_lab', 'technician', 'biologist', 'technicien', 'lab_doctor', 'laborantin'],
            'administrative': ['admin', 'administrative', 'superadmin']
        };

        // Apply filter
        if(category === 'all') {
            renderUsers(cachedUsers);
        } else {
            const roles = groupings[category] || [category];
            const filtered = cachedUsers.filter(u => {
                const role = (u.role || '').toLowerCase().trim();
                // Check exact match or match after replacing underscores with spaces
                return roles.some(r => {
                    const normalizedR = r.toLowerCase().trim();
                    return normalizedR === role || normalizedR === role.replace('_', ' ') || normalizedR.replace('_', ' ') === role;
                });
            });
            renderUsers(filtered);
        }
    }

    function renderUsers(users) {
        const container = document.getElementById('usersList');
        container.innerHTML = users.map(user => `
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 font-black">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="font-black text-slate-900 tracking-tight">${user.name}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${user.role.replace('_', ' ')}</div>
                    </div>
                </div>
                <div class="space-y-2 pt-4 border-t border-slate-50 text-sm">
                    <div class="text-slate-600"><i class="bi bi-envelope mr-2"></i>${user.email}</div>
                    <div class="text-slate-600"><i class="bi bi-hospital mr-2"></i>${user.service ? user.service.name : 'Aucun Service'}</div>
                </div>
            </div>
        `).join('') || '<div class="col-span-full py-20 text-center text-slate-400 font-bold">Aucun utilisateur actif</div>';
    }

    function renderServices(services) {
        const container = document.getElementById('servicesList');
        container.innerHTML = services.map(service => `
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                        <i class="bi bi-hospital-fill text-xl"></i>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="px-3 py-1 bg-slate-50 rounded-lg text-[10px] font-black uppercase tracking-widest text-slate-500">
                            ${service.users ? service.users.length : 0} Staff
                        </span>
                        <span class="px-2 py-0.5 ${service.type === 'medical' ? 'bg-blue-50 text-blue-600' : (service.type === 'technical' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600')} rounded text-[9px] font-black uppercase tracking-widest border ${service.type === 'medical' ? 'border-blue-100' : (service.type === 'technical' ? 'border-emerald-100' : 'border-amber-100')}">
                            ${service.type || 'Medical'}
                        </span>
                    </div>
                </div>
                <div class="font-black text-slate-900 text-lg tracking-tight mb-2">${service.name}</div>
                <p class="text-xs text-slate-500 line-clamp-2">${service.description || 'Aucune description fournie.'}</p>
            </div>
        `).join('') || '<div class="col-span-full py-20 text-center text-slate-400 font-bold">Aucun service configuré</div>';
    }

    function renderPatients(patients) {
        const container = document.getElementById('patientsList');
        container.innerHTML = patients.map(patient => `
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 font-black">
                        ${patient.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="font-black text-slate-900 tracking-tight">${patient.first_name} ${patient.name}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${patient.ipu}</div>
                    </div>
                </div>
                <div class="space-y-2 pt-4 border-t border-slate-50 text-sm">
                    <div class="text-slate-600"><i class="bi bi-gender-ambiguous mr-2"></i>${patient.gender === 'M' ? 'Masculin' : 'Féminin'}</div>
                    <div class="text-slate-600"><i class="bi bi-calendar3 mr-2"></i>${new Date(patient.dob).toLocaleDateString('fr-FR')}</div>
                    <div class="text-slate-600"><i class="bi bi-telephone mr-2"></i>${patient.phone || 'Non renseigné'}</div>
                </div>
            </div>
        `).join('') || '<div class="col-span-full py-20 text-center text-slate-400 font-bold">Aucun patient enregistré</div>';
    }

    function renderPrestations(prestations) {
        const container = document.getElementById('prestationsList');
        if(!prestations.length) {
            container.innerHTML = '<div class="py-20 text-center text-slate-400 font-bold">Catalogue vide</div>';
            return;
        }

        const grouped = prestations.reduce((acc, p) => {
            const cat = p.category || 'Général';
            if(!acc[cat]) acc[cat] = [];
            acc[cat].push(p);
            return acc;
        }, {});

        container.innerHTML = Object.entries(grouped).map(([cat, list]) => `
            <div>
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-4">
                    ${cat}
                    <div class="h-[1px] flex-1 bg-slate-200"></div>
                </h4>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${list.map(p => `
                        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex flex-col justify-between">
                            <div class="mb-4">
                                <div class="font-black text-slate-900 tracking-tight leading-tight mb-2">${p.name}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${p.service ? p.service.name : 'Global'}</div>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <span class="text-lg font-black text-blue-600 tracking-tighter">${new Intl.NumberFormat('fr-FR').format(p.price)} <span class="text-[10px]">FCFA</span></span>
                                <span class="w-2 h-2 rounded-full ${p.is_active ? 'bg-emerald-500' : 'bg-red-500'}"></span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }
</script>

@extends('layouts.nurse')

@section('title', 'Dashboard Infirmière')
@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-pink-50 to-purple-50 min-h-screen">

<div x-data="nurseDashboard()" class="pb-10">
    <header class="bg-gradient-to-r from-pink-600 to-purple-600 text-white p-4 shadow-lg">
        <div class="max-w-[1600px] mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-xl">
                    <i data-lucide="shield-plus" class="w-7 h-7"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">HospitISIS</h1>
                    <p class="text-pink-100 text-xs">{{ auth()->user()?->role === 'nurse' ? 'Infirmière' : 'Infirmier' }} - {{ auth()->user()?->name ?? 'User' }} | Service : {{ auth()->user()->service->name ?? 'Général/Urgence' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
                <button onclick="document.getElementById('logout-form').submit();" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition text-sm font-medium">
                    Déconnexion
                </button>
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                </div>
            </div>
        </div>
    </header>

    <nav class="bg-white shadow-md border-b sticky top-0 z-40">
        <div class="max-w-[1600px] mx-auto px-4">
            <div class="flex gap-1 overflow-x-auto">
                <template x-for="tab in tabs" :key="tab.id">
                    <button 
                        @click="selectedTab = tab.id"
                        class="px-4 py-3 font-medium transition flex items-center gap-2 whitespace-nowrap border-b-2"
                        :class="selectedTab === tab.id ? 'text-pink-600 border-pink-600' : 'text-gray-600 border-transparent hover:text-pink-600'"
                    >
                        <i :class="'w-4 h-4'" :data-lucide="tab.icon"></i>
                        <span x-text="tab.label"></span>
                        <span x-show="tab.badge > 0" class="bg-pink-500 text-white text-xs px-2 py-0.5 rounded-full font-bold" x-text="tab.badge"></span>
                    </button>
                </template>
            </div>
        </div>
    </nav>

    <main class="max-w-[1600px] mx-auto p-4">
        
        <div x-show="selectedTab === 'dashboard'" class="space-y-4" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-pink-500 flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase">RDV Aujourd'hui</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1" x-text="getStats().rdvAujourdhui"></p>
                    </div>
                    <div class="text-pink-600 opacity-20"><i data-lucide="calendar" class="w-8 h-8"></i></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500 flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase">Dossiers Envoyés</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1" x-text="getStats().dossiersEnvoyes"></p>
                    </div>
                    <div class="text-purple-600 opacity-20"><i data-lucide="send" class="w-8 h-8"></i></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500 flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase">Mes Patients</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1" x-text="getStats().patients"></p>
                    </div>
                    <div class="text-blue-600 opacity-20"><i data-lucide="users" class="w-8 h-8"></i></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500 flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase">À Préparer</p>
                        <p class="text-3xl font-bold text-slate-800 mt-1" x-text="getStats().aPreparer"></p>
                    </div>
                    <div class="text-orange-600 opacity-20"><i data-lucide="clock" class="w-8 h-8"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4">
                <h2 class="text-xl font-bold text-slate-800 mb-4">Rendez-vous à Préparer</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="apt in doctorAppointments.filter(a => a.status === 'pending')" :key="apt.id">
                        <div class="border-2 border-pink-200 rounded-lg p-4 bg-pink-50">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg" x-text="apt.patientName"></h3>
                                    <p class="text-sm text-gray-600" x-text="apt.patientId + ' • ' + apt.age + ' ans'"></p>
                                </div>
                                <span class="bg-pink-500 text-white px-3 py-1 rounded-full text-sm font-bold" x-text="apt.time"></span>
                            </div>
                            <div class="bg-white rounded p-3 mb-3 text-sm text-gray-700">
                                <p><span class="font-medium">Motif:</span> <span x-text="apt.reason"></span></p>
                                <p><span class="font-medium">Médecin:</span> <span x-text="apt.doctor"></span></p>
                            </div>
                            <button @click="handlePreparePatient(apt.id)" class="w-full bg-pink-600 text-white py-2 rounded-lg font-medium hover:bg-pink-700 transition flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i> Préparer et Envoyer
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="selectedTab === 'appointments'" x-cloak x-transition>
            <div class="bg-white rounded-xl shadow-md p-4">
                <h2 class="text-xl font-bold text-slate-800 mb-4">Rendez-vous du Médecin</h2>
                <div class="space-y-3">
                    <template x-for="apt in doctorAppointments" :key="apt.id">
                        <div class="border-2 rounded-lg p-4" :class="apt.status === 'sent' ? 'bg-green-50 border-green-300' : 'bg-white border-gray-200'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-pink-100 p-3 rounded-lg text-pink-600"><i data-lucide="calendar"></i></div>
                                    <div>
                                        <h3 class="font-bold text-slate-800" x-text="apt.patientName"></h3>
                                        <p class="text-sm text-pink-600 font-medium" x-text="apt.date + ' à ' + apt.time"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <template x-if="apt.status === 'sent'">
                                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">ENVOYÉ</span>
                                    </template>
                                    <template x-if="apt.status === 'pending'">
                                        <button @click="handlePreparePatient(apt.id)" class="bg-pink-600 text-white px-4 py-2 rounded-lg text-sm">Préparer</button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="selectedTab === 'patients'" x-cloak x-transition>
            <div class="bg-white rounded-xl shadow-md p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Liste des Patients (Sans RDV Aujourd'hui)</h2>
                    <div class="flex gap-2">
                        <input type="text" x-model="searchTerm" placeholder="Rechercher..." class="px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="patient in filteredPatients()" :key="patient.id">
                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 hover:bg-white hover:shadow-md transition group overflow-hidden relative">
                            <div class="flex justify-between items-start">
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center font-bold text-lg group-hover:scale-110 transition" x-text="patient.name.split(' ').map(n => n[0]).join('')"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-slate-800" x-text="patient.name"></p>
                                            <template x-if="patient.isWalkIn">
                                                <span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-0.5 rounded-full font-bold">SANS RDV</span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium" x-text="patient.ipu"></p>
                                        <p class="text-xs text-pink-600 font-bold mt-1" x-text="patient.age + ' ans'"></p>
                                    </div>
                                </div>
                                <button @click="openPrepareFromPatient(patient)" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2">
                                    <i data-lucide="send" class="w-3 h-3"></i> Préparer
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="filteredPatients().length === 0">
                        <div class="col-span-full py-20 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <i data-lucide="users" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">Aucun patient disponible</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="selectedTab === 'hospitalization'" x-cloak x-transition>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter">Suivi Hospitalisation</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="adm in admissions" :key="adm.id">
                    <div class="bg-white rounded-[2rem] border-2 border-purple-50 shadow-sm hover:shadow-xl transition-all overflow-hidden relative group">
                        <!-- Statut du dossier vital -->
                        <div class="absolute top-4 right-4 z-10">
                            <template x-if="adm.vitals.status === 'active'">
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-200 animate-pulse">En attente</span>
                            </template>
                            <template x-if="adm.vitals.status === 'consulting'">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-200">En cours</span>
                            </template>
                            <template x-if="adm.vitals.status === 'archived'">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-200">Traité</span>
                            </template>
                            <template x-if="adm.vitals.status === 'none'">
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200">Aucun soin</span>
                            </template>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg ring-4 ring-purple-50">
                                    <template x-if="adm.vitals.status === 'consulting'">
                                        <i data-lucide="loader-2" class="w-8 h-8 animate-spin"></i>
                                    </template>
                                    <template x-if="adm.vitals.status !== 'consulting'">
                                        <span x-text="adm.name.split(' ').map(n => n[0]).join('')"></span>
                                    </template>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 uppercase leading-none mb-1" x-text="adm.name"></h3>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 font-bold" x-text="adm.ipu"></span>
                                        <span class="text-[10px] bg-purple-50 text-purple-600 px-2 py-0.5 rounded font-black uppercase tracking-tighter border border-purple-100" x-text="'CH. ' + (adm.room || 'N/A')"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Vitals Grid -->
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <div class="bg-pink-50 rounded-2xl p-3 border border-pink-100">
                                    <p class="text-[9px] text-pink-600 font-black uppercase mb-1">Température</p>
                                    <p class="text-lg font-black text-slate-800" x-text="adm.vitals.temp + '°C'"></p>
                                </div>
                                <div class="bg-purple-50 rounded-2xl p-3 border border-purple-100">
                                    <p class="text-[9px] text-purple-600 font-black uppercase mb-1">Pouls</p>
                                    <p class="text-lg font-black text-slate-800" x-text="adm.vitals.pulse + ' BPM'"></p>
                                </div>
                                <div class="bg-orange-50 rounded-2xl p-3 border border-orange-100">
                                    <p class="text-[9px] text-orange-600 font-black uppercase mb-1">Groupe</p>
                                    <p class="text-lg font-black text-slate-800" x-text="adm.bloodType || '??'"></p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button @click="openCareDashboard(adm.id)" class="flex-1 bg-gradient-to-r from-pink-600 to-purple-600 text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:scale-[1.02] transition-all">
                                    Démarrer Soins
                                </button>
                                <button class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                                    <i data-lucide="history" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="admissions.length === 0">
                    <div class="col-span-full py-20 text-center bg-white rounded-[2rem] border-2 border-dashed border-purple-100">
                        <i data-lucide="hospital" class="w-12 h-12 mx-auto text-purple-200 mb-3"></i>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Aucun patient hospitalisé actuellement dans votre service.</p>
                    </div>
                </template>
            </div>

            <!-- DASHBOARD DE SOINS (SUB-VIEW) -->
            <div x-show="showCareDashboard" x-cloak x-transition class="fixed inset-0 bg-slate-50 z-[60] overflow-y-auto">
                <div class="max-w-4xl mx-auto p-4 md:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <button @click="closeCareDashboard()" class="flex items-center gap-2 text-slate-500 font-bold hover:text-pink-600 transition">
                            <i data-lucide="arrow-left"></i> Retour au tableau de bord
                        </button>
                        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100">
                            <span class="text-xs font-black text-purple-600 uppercase tracking-widest">Dashboard de Soins Hospitaliers</span>
                        </div>
                    </div>

                    <div x-show="viewingAdmission" class="space-y-8">
                        <!-- Patient Header Card -->
                        <div class="bg-gradient-to-r from-pink-600 to-purple-600 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                                <div class="flex items-center gap-6">
                                    <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center font-black text-4xl shadow-inner border border-white/30">
                                        <span x-text="viewingAdmission?.name.split(' ').map(n => n[0]).join('')"></span>
                                    </div>
                                    <div>
                                        <h2 class="text-4xl font-black uppercase tracking-tighter" x-text="viewingAdmission?.name"></h2>
                                        <div class="flex flex-wrap items-center gap-3 mt-2 text-pink-100 font-bold">
                                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs" x-text="'IPU: ' + viewingAdmission?.ipu"></span>
                                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs" x-text="viewingAdmission?.age + ' ANS'"></span>
                                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs" x-text="'CHAMBRE: ' + viewingAdmission?.room"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center min-w-32">
                                    <p class="text-[10px] uppercase font-black opacity-60">Groupe Sanguin</p>
                                    <p class="text-3xl font-black" x-text="viewingAdmission?.bloodType || '--'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left Column: Actions & Prescriptions -->
                            <div class="lg:col-span-2 space-y-8">
                                <!-- Vitals Summary -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-show="viewingAdmission?.vitals">
                                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 text-center">
                                        <div class="w-10 h-10 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="thermometer"></i></div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase">Température</p>
                                        <p class="text-xl font-black text-slate-800" x-text="viewingAdmission?.vitals.temp + '°C'"></p>
                                    </div>
                                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 text-center">
                                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="heart"></i></div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase">Pouls (BPM)</p>
                                        <p class="text-xl font-black text-slate-800" x-text="viewingAdmission?.vitals.pulse"></p>
                                    </div>
                                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 text-center">
                                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="activity"></i></div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase">Tension Art.</p>
                                        <p class="text-xl font-black text-slate-800" x-text="viewingAdmission?.vitals.bp"></p>
                                    </div>
                                    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 text-center">
                                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3"><i data-lucide="user"></i></div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase">Poids / Taille</p>
                                        <p class="text-base font-black text-slate-800" x-text="viewingAdmission?.vitals.weight + 'kg / ' + viewingAdmission?.vitals.height + 'cm'"></p>
                                    </div>
                                </div>

                                <!-- Current Prescriptions -->
                                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex items-center justify-between">
                                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-widest flex items-center gap-2">
                                            <i data-lucide="pill" class="w-4 h-4 text-purple-600"></i> Prescriptions Actives
                                        </h3>
                                        <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-[10px] font-black uppercase" x-text="(viewingAdmission?.prescriptions?.length || 0) + ' MÉDICAMENTS'"></span>
                                    </div>
                                    <div class="p-4">
                                        <div class="space-y-3">
                                            <template x-for="p in viewingAdmission?.prescriptions" :key="p.id">
                                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex justify-between items-center group hover:bg-white hover:border-purple-200 transition-all shadow-sm">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-purple-600 border border-slate-100"><i data-lucide="package"></i></div>
                                                        <div>
                                                            <p class="font-black text-slate-800 uppercase text-xs" x-text="p.medication"></p>
                                                            <p class="text-[10px] text-gray-500 font-bold" x-text="p.dosage + ' • ' + p.frequency"></p>
                                                        </div>
                                                    </div>
                                                    <span class="text-[9px] font-black text-slate-400 uppercase" x-text="p.date"></span>
                                                </div>
                                            </template>
                                            <template x-if="!viewingAdmission?.prescriptions || viewingAdmission?.prescriptions.length === 0">
                                                <div class="text-center py-10 text-gray-400 italic text-sm">Aucun traitement prescrit actuellement.</div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Care Checklist & Actions -->
                            <div class="space-y-8">
                                <div class="bg-white rounded-[2rem] shadow-xl border-t-4 border-pink-500 overflow-hidden">
                                    <div class="p-8">
                                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-widest mb-6 flex items-center gap-2">
                                            <i data-lucide="clipboard-check" class="w-5 h-5 text-pink-500"></i> Actions de Soins
                                        </h3>
                                        
                                        <div class="space-y-4">
                                            <button @click="openPrepareFromPatient(viewingAdmission)" class="w-full bg-pink-600 hover:bg-pink-700 text-white p-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg transition-transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                                <i data-lucide="activity"></i> Prendre Nouvelles Constantes
                                            </button>

                                            <button @click="openAddCareNote()" class="w-full bg-slate-800 hover:bg-black text-white p-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg transition-transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                                <i data-lucide="clipboard-list"></i> Noter un Soin / Observation
                                            </button>
                                            
                                            <div class="pt-6 border-t border-slate-100">
                                                <p class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest text-center">Historique Patient</p>
                                                <a :href="'{{ url('nurse/patient') }}/' + viewingAdmission?.patient_id + '/dashboard'" class="flex items-center justify-center gap-2 w-full py-4 bg-slate-800 text-white rounded-2xl font-black hover:bg-black transition-all shadow-lg text-xs tracking-widest uppercase">
                                                    Consulter Dossier Complet
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nurse Note Info -->
                                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-[2rem] p-6 border border-blue-100">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center"><i data-lucide="info" class="w-4 h-4"></i></div>
                                        <h4 class="font-black text-blue-800 uppercase text-xs">Note de Service</h4>
                                    </div>
                                    <p class="text-xs text-blue-700 leading-relaxed font-medium">
                                        Vérifiez toujours l'identité du patient avant d'administrer les médicaments listés. Les signes critiques doivent être signalés immédiatement au médecin de garde.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="selectedTab === 'archive'" x-cloak x-transition>
            <div class="bg-white rounded-xl shadow-md p-4">
                <h2 class="text-xl font-bold mb-4">Archives du Jour (Terminés)</h2>
                <div class="space-y-3">
                    <template x-if="sentFiles.filter(f => f.status === 'archived').length === 0">
                        <div class="text-center py-10 text-gray-400">
                            <i data-lucide="archive" class="w-12 h-12 mx-auto text-gray-200 mb-3"></i>
                            <p>Aucun dossier terminé pour le moment.</p>
                        </div>
                    </template>
                    <template x-for="file in sentFiles.filter(f => f.status === 'archived')" :key="file.id">
                        <div class="border-2 rounded-lg p-4 flex justify-between items-center transition-all bg-green-50 border-green-300">
                            <div>
                                <p class="font-black text-slate-800" x-text="file.patientName"></p>
                                <p class="text-sm text-gray-600" x-text="'Motif: ' + file.reason"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] text-gray-500 font-bold uppercase" x-text="'Terminé à: ' + file.sentAt"></p>
                                    <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded font-black uppercase tracking-tighter border border-green-200" x-text="file.assignedDoctor"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-black">TRAITÉ</span>
                                <a :href="'{{ url('nurse/patient') }}/' + (file.patient_id || '') + '/dashboard'" class="text-pink-600 hover:text-pink-800 p-2 rounded-xl hover:bg-pink-50 transition">
                                    <i data-lucide="external-link" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="selectedTab === 'sent'" x-cloak x-transition>
            <div class="bg-white rounded-xl shadow-md p-4">
                <h2 class="text-xl font-bold mb-4">Dossiers au Cabinet (En attente/En cours)</h2>
                <div class="space-y-3">
                    <template x-if="sentFiles.filter(f => f.status !== 'archived').length === 0">
                        <div class="text-center py-10 text-gray-400">Aucun dossier en attente au cabinet</div>
                    </template>
    <template x-for="file in sentFiles.filter(f => f.status !== 'archived')" :key="file.id">
    <div class="border-2 rounded-lg p-4 flex justify-between items-center transition-all bg-white"
         :class="{
            'border-yellow-300 bg-yellow-50 shadow-sm': file.status === 'active',
            'border-blue-300 bg-blue-50': file.status === 'consulting',
            'border-purple-300 bg-purple-50': file.status === 'admitted',
            'border-green-300 bg-green-50': file.status === 'archived'
         }">
        <div>
            <p class="font-bold text-slate-800" x-text="file.patientName"></p>
            <p class="text-sm text-gray-600" x-text="'Motif: ' + file.reason"></p>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-[10px] text-gray-500 font-bold uppercase" x-text="'Envoyé à: ' + file.sentAt"></p>
                <span class="bg-white/50 text-slate-700 text-[10px] px-2 py-0.5 rounded font-black uppercase tracking-tighter border border-slate-200" x-text="file.assignedDoctor"></span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <template x-if="file.status === 'active'">
                <span class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold animate-pulse">EN ATTENTE</span>
            </template>
            <template x-if="file.status === 'consulting'">
                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                    <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i> EN COURS
                </span>
            </template>
            <template x-if="file.status === 'admitted'">
                <span class="bg-purple-200 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">HOSPITALISÉ</span>
            </template>
            <template x-if="file.status === 'archived'">
                <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-bold">TRAITÉ</span>
            </template>
            
            <button @click="deleteVital(file.id)" class="text-red-400 hover:text-red-600 transition p-1 rounded-full hover:bg-red-50" title="Supprimer">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
        </div>
    </div>
    </template>
                </div>
            </div>
        </div>
    </main>

    <div x-show="showSuccessToast"
         x-transition
         class="fixed bottom-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-2xl z-[100] flex items-center gap-2">
        <i data-lucide="check-circle"></i>
        <span>Dossier transmis avec succès !</span>
    </div>

    <div x-show="showSendModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] p-4" x-cloak x-transition>
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-2xl font-bold text-slate-800 mb-4">Envoyer au Médecin</h2>
            <div x-show="selectedPatient" class="bg-pink-50 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-bold text-lg text-slate-800" x-text="selectedPatient?.patientName || selectedPatient?.name"></p>
                        <p class="text-sm text-gray-600" x-text="(selectedPatient?.patientId || selectedPatient?.ipu) + ' • ' + selectedPatient?.age + ' ans'"></p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-pink-500 uppercase block mb-1">Prestation choisie</span>
                        <span class="bg-pink-100 text-pink-700 px-3 py-1 rounded-lg text-sm font-bold border border-pink-200" x-text="selectedPatient?.serviceName || 'Consultation Générale'"></span>
                    </div>
                </div>
            </div>

            <!-- Section Informations Médicales (Facultatif) -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
                <h3 class="text-sm font-bold text-blue-800 uppercase flex items-center gap-2">
                    <i data-lucide="file-medical" class="w-4 h-4"></i> Informations Médicales (Portail Patient)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Groupe Sanguin</label>
                        <select x-model="sendFormData.blood_group" class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Non renseigné</option>
                            <template x-for="group in ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']" :key="group">
                                <option :value="group" x-text="group" :selected="sendFormData.blood_group == group"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Allergies</label>
                        <input type="text" x-model="sendFormData.allergies" placeholder="Ex: Pénicilline, Pollen..." class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Antécédents / Histoire Médicale</label>
                    <textarea x-model="sendFormData.medical_history" rows="2" placeholder="Antécédents connus..." class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Urgence</label>
                    <select x-model="sendFormData.urgency" class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="normale">Urgence Normale</option>
                        <option value="urgent">Urgent</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Motif de consultation *</label>
                    <textarea x-model="sendFormData.reason" placeholder="Ex: Douleurs abdominales..." class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-pink-500" rows="3"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Température (°C) *</label>
                        <input type="number" step="0.1" x-model="sendFormData.vitals.temp" placeholder="37.5" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Pouls (BPM) *</label>
                        <input type="number" x-model="sendFormData.vitals.pulse" placeholder="80" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Poids (Kg)</label>
                        <input type="number" step="0.1" x-model="sendFormData.vitals.weight" placeholder="70" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Taille (cm)</label>
                        <input type="number" x-model="sendFormData.vitals.height" placeholder="175" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>

                <!-- Section Dynamique par Service -->
                <template x-if="formConfig && formConfig.length > 0">
                    <div class="mt-6 p-4 bg-orange-50 border border-orange-100 rounded-xl space-y-4">
                        <h3 class="text-sm font-bold text-orange-800 uppercase flex items-center gap-2">
                            <i data-lucide="custom-vitals" class="w-4 h-4"></i> Signes Spécifiques : <span x-text="'{{ auth()->user()->service->name ?? 'Général' }}'"></span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="field in formConfig" :key="field.name">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase" x-text="field.label"></label>
                                    <input :type="field.type" 
                                           x-model="sendFormData.custom_vitals[field.name]" 
                                           :placeholder="field.placeholder" 
                                           class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <div class="flex gap-3 mt-6">
                <button @click="showSendModal = false" class="flex-1 bg-gray-200 py-3 rounded-lg font-medium">Annuler</button>
                <button @click="handleSendToDoctor" class="flex-1 bg-pink-600 text-white py-3 rounded-lg font-medium">Envoyer</button>
            </div>
        </div>
    </div>

    <div x-show="showAddPatientModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] p-4" x-cloak x-transition>
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 text-center">
            <h2 class="text-2xl font-bold mb-4">Nouveau Patient</h2>
            <div class="space-y-4 text-left">
                <input type="text" x-model="newPatientData.name" placeholder="Nom complet *" class="w-full px-3 py-2 border rounded-lg">
                <input type="number" x-model="newPatientData.age" placeholder="Âge *" class="w-full px-3 py-2 border rounded-lg">
                <select x-model="newPatientData.bloodType" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Groupe Sanguin *</option>
                    <option value="A+">A+</option><option value="O+">O+</option><option value="AB+">AB+</option>
                </select>
            </div>
            <div class="flex gap-3 mt-6">
                <button @click="showAddPatientModal = false" class="flex-1 bg-gray-200 py-3 rounded-lg">Fermer</button>
                <button @click="handleAddPatient" class="flex-1 bg-pink-600 text-white py-3 rounded-lg font-medium">Ajouter</button>
            </div>
        </div>
    </div>

    <!-- Modal Note de Soin -->
    <div x-show="showCareNoteModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[110] p-4" x-cloak x-transition>
        <div @click.stop class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden border-t-8 border-pink-500 relative z-[111]">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter">Noter un Soin</h2>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest" x-text="viewingAdmission?.name"></p>
                    </div>
                    <button @click="showCareNoteModal = false" class="text-gray-400 hover:text-red-500"><i data-lucide="x"></i></button>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Date & Heure du Soin</label>
                        <input type="datetime-local" x-model="careNoteData.date" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 font-bold outline-none focus:border-pink-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Observations / Soins effectués</label>
                        <textarea x-model="careNoteData.notes" 
                                  id="care_note_textarea"
                                  rows="5" 
                                  placeholder="Ex: Injection de Ceftriaxone 1g, Pansement refait..." 
                                  class="w-full bg-white border-2 border-slate-200 rounded-2xl p-4 font-medium outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                                  style="pointer-events: auto !important;"></textarea>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button @click="showCareNoteModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-200 transition-all">
                        Annuler
                    </button>
                    <button @click="handleSaveCareNote()" class="flex-1 py-4 bg-pink-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg shadow-pink-200 hover:bg-pink-700 transition-all">
                        Enregistrer le Soin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
function nurseDashboard() {
    return {
        selectedTab: 'dashboard',
        showSendModal: false,
        showAddPatientModal: false,
        showSuccessToast: false,
        selectedPatient: null,
        searchTerm: '',
        tabs: [
            { id: 'dashboard', icon: 'layout-dashboard', label: 'Tableau de Bord', badge: 0 },
            { id: 'appointments', icon: 'calendar-days', label: 'RDV Médecin', badge: 3 },
            { id: 'patients', icon: 'users', label: 'Mes Patients', badge: 0 },
            { id: 'sent', icon: 'send', label: 'Envoyés', badge: 1 },
            { id: 'hospitalization', icon: 'hospital', label: 'Hospitalisation', badge: 0 },
            { id: 'archive', icon: 'archive', label: 'Archive', badge: 0 }
        ],
        showCareDashboard: false,
        showCareNoteModal: false,
        viewingAdmission: null,
        careNoteData: { notes: '', date: '' },
        
        doctorAppointments: [
            @foreach($appointments as $apt)
            {
                id: {{ $apt->id }},
                patientName: {!! json_encode($apt->patient?->name ?? 'Patient Inconnu') !!},
                patientId: {!! json_encode($apt->patient?->ipu ?? 'N/A') !!},
                age: {{ $apt->patient ? \Carbon\Carbon::parse($apt->patient->dob)->age : 0 }},
                date: {!! json_encode(\Carbon\Carbon::parse($apt->appointment_datetime)->format('d/m/Y')) !!},
                time: {!! json_encode($apt->appointment_datetime->format('H:i')) !!},
                reason: {!! json_encode($apt->reason) !!},
                doctor: {!! json_encode($apt->doctor ? $apt->doctor->name : "N/A") !!},
                serviceName: {!! json_encode($apt->prestations->pluck("name")->implode(", ") ?: $apt->service?->name) !!},
                blood_group: {!! json_encode($apt->patient?->blood_group ?? 'N/A') !!},
                allergies: {!! json_encode($apt->patient ? (is_array($apt->patient->allergies) ? implode(", ", $apt->patient->allergies) : $apt->patient->allergies) : '') !!},
                medical_history: {!! json_encode($apt->patient?->medical_history ?? '') !!},
                status: 'pending'
            },
            @endforeach
        ],

        sentFiles: [
            @foreach($sentFiles as $file)
            {
                id: {{ $file->id }},
                patient_id: {{ $file->patient?->id ?? 'null' }},
                patientName: {!! json_encode($file->patient_name) !!},
                reason: {!! json_encode($file->reason) !!},
                sentAt: {!! json_encode($file->created_at->format('H:i')) !!},
                assignedDoctor: {!! json_encode($file->doctor ? "Dr. " . $file->doctor->name : "Non assigné") !!},
                status: '{{ $file->status }}'
            },
            @endforeach
        ],

        availablePatients: [
            {{-- 1. Patients "Sans RDV" (Walk-ins) d'aujourd'hui --}}
            @foreach($walkIns as $w)
            { 
               id: 'walkin_{{ $w->id }}', 
               name: {!! json_encode(($w->patient?->name ?? 'Inconnu') . " " . ($w->patient?->first_name ?? '')) !!}, 
               ipu: {!! json_encode($w->patient?->ipu ?? 'N/A') !!}, 
               age: {{ $w->patient?->age ?? 0 }}, 
               bloodType: {!! json_encode($w->patient?->blood_group ?? 'N/A') !!},
               allergies: {!! json_encode($w->patient ? (is_array($w->patient->allergies) ? implode(", ", $w->patient->allergies) : $w->patient->allergies) : '') !!},
               medical_history: {!! json_encode($w->patient?->medical_history ?? '') !!},
               reason: {!! json_encode($w->reason) !!},
               serviceName: {!! json_encode($w->prestations->pluck("name")->implode(", ") ?: $w->service?->name) !!},
               isWalkIn: true,
               hasAppointment: false 
            },
            @endforeach

            {{-- 2. Patients du service sans activité aujourd'hui --}}
            @foreach($patientsWithoutApt as $p)
            { 
               id: {{ $p->id }}, 
               name: {!! json_encode(($p->name ?? 'Inconnu') . " " . ($p->first_name ?? '')) !!}, 
               ipu: {!! json_encode($p->ipu ?? 'N/A') !!}, 
               age: {{ $p->age ?? 0 }}, 
               bloodType: {!! json_encode($p->blood_group ?? 'N/A') !!},
               allergies: {!! json_encode(is_array($p->allergies) ? implode(", ", $p->allergies) : $p->allergies) !!},
               medical_history: {!! json_encode($p->medical_history) !!},
               reason: '',
               isWalkIn: false,
               hasAppointment: false 
            },
            @endforeach
        ],

        admissions: [
            @foreach($myPatients as $admission)
            @php $signes = $admission->derniersSignes; @endphp
            { 
                id: {{ $admission->id }}, 
                patient_id: {{ $admission->patient_id }},
                name: {!! json_encode(($admission->patient?->name ?? 'Inconnu') . " " . ($admission->patient?->first_name ?? '')) !!}, 
                ipu: {!! json_encode($admission->patient?->ipu ?? 'N/A') !!}, 
                age: {{ $admission->patient?->age ?? 0 }}, 
                bloodType: {!! json_encode($admission->patient?->blood_group ?? 'N/A') !!},
                allergies: {!! json_encode($admission->patient ? (is_array($admission->patient->allergies) ? implode(", ", $admission->patient->allergies) : $admission->patient->allergies) : '') !!},
                medical_history: {!! json_encode($admission->patient?->medical_history ?? '') !!},
                reason: {!! json_encode($admission->admission_reason) !!},
                room: {!! json_encode($admission->room ? $admission->room->room_number : "") !!},
                vitals: {
                    temp: {!! json_encode($signes?->temperature ?? "--") !!},
                    pulse: {!! json_encode($signes?->pulse ?? "--") !!},
                    weight: {!! json_encode($signes?->weight ?? "--") !!},
                    height: {!! json_encode($signes?->height ?? "--") !!},
                    bp: {!! json_encode($signes?->blood_pressure ?? "--") !!},
                    last_check: {!! json_encode($signes?->created_at?->format("H:i") ?? "N/A") !!},
                    status: {!! json_encode($signes?->status ?? "none") !!}
                },
                prescriptions: [
                    @foreach($admission->patient->prescriptions as $presc)
                    {
                        id: {{ $presc->id }},
                        medication: {!! json_encode($presc->medication) !!},
                        dosage: {!! json_encode($presc->dosage) !!},
                        frequency: {!! json_encode($presc->frequency) !!},
                        date: {!! json_encode($presc->created_at->format("d/m H:i")) !!}
                    },
                    @endforeach
                ],
                hasAppointment: false 
            },
            @endforeach
        ],

        sendFormData: { 
            urgency: 'normale', 
            reason: '', 
            vitals: { temp: '', pulse: '', weight: '', height: '' },
            blood_group: '',
            allergies: '',
            medical_history: '',
            custom_vitals: {}
        },

        formConfig: {!! json_encode(auth()->user()->service?->form_config ?? []) !!},

        newPatientData: { name: '', age: '', bloodType: '' },

        init() {
            lucide.createIcons();
            this.$watch('selectedTab', () => { this.$nextTick(() => lucide.createIcons()); });
            
            // POLLING AUTOMATIQUE : Rafraîchir les statuts toutes les 5 secondes
            setInterval(() => {
                this.refreshSentFiles();
            }, 5000);
        },

        async refreshSentFiles() {
            try {
                const response = await fetch('{{ route("nurse.fetch-sent-files") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (response.ok) {
                    this.sentFiles = await response.json();
                }
            } catch (e) {
                console.error("Erreur polling:", e);
            }
        },

        // FONCTION DE RÉINITIALISATION (Correctif pour le mélange Ana/Ama)
        resetForm() {
            this.sendFormData = { 
                urgency: 'normale', 
                reason: '', 
                vitals: { temp: '', pulse: '', weight: '', height: '' },
                blood_group: '',
                allergies: '',
                medical_history: '',
                custom_vitals: {}
            };
        },

        getStats() {
            const pendings = this.doctorAppointments.filter(a => a.status === 'pending').length;
            const sentAtCabinet = this.sentFiles.filter(f => f.status !== 'archived').length;
            const archivedToday = this.sentFiles.filter(f => f.status === 'archived').length;
            
            this.tabs[1].badge = pendings;
            this.tabs[3].badge = sentAtCabinet;
            this.tabs[2].badge = this.availablePatients.length;
            this.tabs[4].badge = this.admissions.length;
            this.tabs[5].badge = archivedToday;
            
            return { rdvAujourdhui: pendings, dossiersEnvoyes: sentAtCabinet, patients: this.availablePatients.length, aPreparer: pendings };
        },

        filteredPatients() {
            return this.availablePatients.filter(p => p.name.toLowerCase().includes(this.searchTerm.toLowerCase()) || p.ipu.toLowerCase().includes(this.searchTerm.toLowerCase()));
        },

        handlePreparePatient(id) {
            this.resetForm(); // On vide avant d'ouvrir
            this.selectedPatient = this.doctorAppointments.find(a => a.id === id);
            this.sendFormData.blood_group = this.selectedPatient.blood_group;
            this.sendFormData.allergies = this.selectedPatient.allergies;
            this.sendFormData.medical_history = this.selectedPatient.medical_history;
            this.sendFormData.reason = this.selectedPatient.reason || '';
            this.showSendModal = true;
        },

        openPrepareFromPatient(patient) {
            this.resetForm(); // On vide avant d'ouvrir
            this.selectedPatient = patient;
            this.sendFormData.blood_group = patient.bloodType || patient.blood_group;
            this.sendFormData.allergies = patient.allergies;
            this.sendFormData.medical_history = patient.medical_history;
            this.sendFormData.reason = patient.reason || '';
            this.showSendModal = true;
        },

        async handleSendToDoctor() {
            // Validation avec alerte pour aider l'utilisateur
            if (!this.sendFormData.reason) return alert("Le motif de consultation est obligatoire.");
            if (!this.sendFormData.vitals.temp) return alert("La température est obligatoire.");
            if (!this.sendFormData.vitals.pulse) return alert("Le pouls est obligatoire.");

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`{{ url('nurse/send') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        patient_name: this.selectedPatient.patientName || this.selectedPatient.name,
                        patient_ipu: this.selectedPatient.patientId || this.selectedPatient.ipu,
                        urgency: this.sendFormData.urgency,
                        reason: this.sendFormData.reason,
                        temperature: this.sendFormData.vitals.temp, 
                        pulse: this.sendFormData.vitals.pulse,
                        weight: this.sendFormData.vitals.weight,
                        height: this.sendFormData.vitals.height,
                        blood_pressure: "12/8",
                        blood_group: this.sendFormData.blood_group,
                        allergies: this.sendFormData.allergies,
                        medical_history: this.sendFormData.medical_history,
                        custom_vitals: this.sendFormData.custom_vitals
                    })
                });

                if (response.status === 419) {
                    alert("Votre session a expiré. La page va se recharger.");
                    window.location.reload();
                    return;
                }

                const result = await response.json();

                if (response.ok && result.success) {
                    this.resetForm(); // On vide après succès
                    this.showSendModal = false;
                    this.showSuccessToast = true;
                    setTimeout(() => {
                        this.showSuccessToast = false;
                        window.location.reload(); 
                    }, 1500);
                } else {
                    alert("Erreur serveur : " + (result.message || "Vérifiez les données."));
                }
            } catch (error) {
                console.error("Erreur:", error);
                alert("Impossible de joindre le serveur. Vérifiez votre connexion.");
            }
        },

        handleAddPatient() {
            if (!this.newPatientData.name || !this.newPatientData.age) return alert('Veuillez remplir le nom et l\'âge');
            this.myPatients.push({ id: Date.now(), name: this.newPatientData.name, age: this.newPatientData.age, ipu: 'PAT' + Math.floor(Math.random()*9000), bloodType: this.newPatientData.bloodType, hasAppointment: false });
            this.showAddPatientModal = false;
            this.newPatientData = { name: '', age: '', bloodType: '' };
        },

        openCareDashboard(admissionId) {
            this.viewingAdmission = this.admissions.find(a => a.id === admissionId);
            this.showCareDashboard = true;
            this.$nextTick(() => lucide.createIcons());
        },

        closeCareDashboard() {
            this.showCareDashboard = false;
            this.viewingAdmission = null;
        },

        openAddCareNote() {
            this.careNoteData.notes = '';
            this.careNoteData.date = new Date().toISOString().slice(0, 16);
            this.showCareNoteModal = true;
            this.$nextTick(() => {
                const el = document.getElementById('care_note_textarea');
                if (el) el.focus();
            });
        },

        async handleSaveCareNote() {
            if (!this.careNoteData.notes) return alert("Veuillez saisir une observation.");
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`{{ route('nurse.care-note.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        patient_id: this.viewingAdmission.patient_id,
                        notes: this.careNoteData.notes,
                        observation_datetime: this.careNoteData.date
                    })
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    this.showCareNoteModal = false;
                    this.showSuccessToast = true;
                    setTimeout(() => { this.showSuccessToast = false; }, 2000);
                    // Pas de reload nécessaire ici if we want to stay on dashboard, but maybe refresh data?
                } else {
                    alert("Erreur: " + (result.message || "Erreur lors de l'enregistrement"));
                }
            } catch (e) {
                console.error(e);
                alert("Erreur de connexion");
            }
        },

        async deleteVital(id) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')) return;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`{{ url('nurse/vital') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.status === 419) {
                    alert("Votre session a expiré. La page va se recharger.");
                    window.location.reload();
                    return;
                }

                const result = await response.json();
                if (response.ok && result.success) {
                    this.sentFiles = this.sentFiles.filter(file => file.id !== id);
                    this.tabs[3].badge = this.sentFiles.length;
                    alert('Dossier supprimé avec succès !');
                } else {
                    alert('Erreur lors de la suppression : ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Erreur réseau:', error);
            }
        }
    }
}
</script>
@endpush
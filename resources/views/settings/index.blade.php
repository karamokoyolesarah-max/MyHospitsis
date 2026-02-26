@extends('layouts.app')

@section('title', 'Paramètres - HospitSIS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 p-6 lg:p-10">
    <div class="max-w-6xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Premium Header Area -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic flex items-center gap-4">
                    <div class="w-2 h-10 bg-blue-600 rounded-full"></div>
                    Paramètres Système
                </h1>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs mt-2 ml-6">
                    Gestion de votre compte, sécurité et préférences de l'espace hospitalier
                </p>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="px-5 py-2.5 bg-white shadow-sm border border-slate-100 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">
                    Version 2.5.0
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Section: Sécurité -->
            <div class="space-y-6">
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <i class="fas fa-shield-alt text-9xl text-slate-900"></i>
                    </div>

                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-slate-50">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-lock text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Sécurité & Accès</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Protégez votre espace de travail</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- MFA Card -->
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-transparent hover:border-blue-100 hover:bg-white hover:shadow-lg transition-all duration-300">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="font-black text-slate-900 uppercase italic text-sm mb-1">Authentification Multi-Facteurs (MFA)</h3>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">Ajoutez une barrière de sécurité mobile pour vos connexions.</p>
                                    
                                    @if(auth()->user()->mfa_enabled)
                                        <div class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full font-black text-[10px] uppercase tracking-widest border border-emerald-100 mb-4">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                            Activé & Sécurisé
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-4 py-1.5 bg-slate-100 text-slate-400 rounded-full font-black text-[10px] uppercase tracking-widest border border-slate-200 mb-4">
                                            Désactivé
                                        </div>
                                    @endif

                                    <div class="flex gap-2">
                                        @if(auth()->user()->mfa_enabled)
                                            <form method="POST" action="{{ route('mfa.disable') }}">
                                                @csrf
                                                <button type="submit" class="px-6 py-2.5 bg-white text-red-600 border border-red-100 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 transition-all">
                                                    Désactiver
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('mfa.setup') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                                                Configurer maintenant
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Card -->
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-transparent hover:border-amber-100 hover:bg-white hover:shadow-lg transition-all duration-300">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="font-black text-slate-900 uppercase italic text-sm mb-1">Mot de Passe</h3>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">Modifiez régulièrement vos accès pour une sécurité maximale.</p>
                                    
                                    <a href="{{ route('profile.edit') }}#security" class="px-6 py-2.5 bg-amber-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 shadow-lg shadow-amber-200 transition-all">
                                        Mettre à jour
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Préférences -->
            <div class="space-y-6">
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                        <i class="fas fa-sliders-h text-9xl text-slate-900"></i>
                    </div>

                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-slate-50">
                        <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-user-gear text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Préférences Profil</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Identité & Information</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Profile Card -->
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-transparent hover:border-indigo-100 hover:bg-white hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center gap-6">
                                <div class="h-16 w-16 rounded-2xl bg-white p-1 shadow-sm border border-slate-100 overflow-hidden">
                                    @if(auth()->user()->profile_photo)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-300 font-black text-2xl uppercase">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-black text-slate-900 uppercase italic text-sm mb-1">Informations du profil</h3>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">Contact, fonction et photo de identité.</p>
                                    
                                    <a href="{{ route('profile.edit') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                        Modifier le profil
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Card -->
                        <div class="p-6 rounded-[2rem] bg-white border border-slate-100 shadow-xl shadow-slate-200/20 transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <h3 class="font-black text-slate-900 uppercase italic text-sm mb-1">Système d'Alertes</h3>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">Gérez la fréquence des alertes SMS et Email.</p>
                                    
                                    <form action="{{ route('profile.notifications.update') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-blue-100 transition-all group/toggle">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover/toggle:scale-110 transition-transform">
                                                    <i class="fas fa-envelope text-xs"></i>
                                                </div>
                                                <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Alertes Email</span>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="email_notifications" class="sr-only peer" {{ auth()->user()->email_notifications ? 'checked' : '' }} onchange="this.form.submit()">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>

                                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-indigo-100 transition-all group/toggle">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center group-hover/toggle:scale-110 transition-transform">
                                                    <i class="fas fa-sms text-xs"></i>
                                                </div>
                                                <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Alertes SMS</span>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ auth()->user()->sms_notifications ? 'checked' : '' }} onchange="this.form.submit()">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            </label>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Footer Stats -->
        <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl text-white relative overflow-hidden group">
            <div class="flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="flex items-center gap-6">
                    <div class="h-16 w-16 bg-white/10 rounded-[1.5rem] flex items-center justify-center border border-white/10">
                        <i class="fas fa-server text-blue-400 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-black uppercase italic tracking-tighter text-lg">Informations Système</h4>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Connecté à HospitSIS Cloud v2</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-12">
                    <div class="text-center md:text-left">
                        <p class="text-[10px] text-slate-500 font-black uppercase mb-1">Statut API</p>
                        <p class="text-emerald-400 font-black uppercase text-xs italic tracking-widest">Opérationnel</p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-[10px] text-slate-500 font-black uppercase mb-1">Dernière Sync</p>
                        <p class="text-slate-100 font-black uppercase text-xs italic tracking-widest">Il y a 2m</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

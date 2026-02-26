@extends('layouts.app')

@section('title', 'Mon Profil - ' . $user->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 p-6 lg:p-10">
    <div class="max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Premium Header Card -->
        <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative">
            <div class="h-40 bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 flex items-center justify-end px-12">
                <div class="opacity-10">
                    <i class="fas fa-id-card text-8xl text-white"></i>
                </div>
            </div>
            
            <div class="px-10 pb-12">
                <div class="flex flex-col md:flex-row items-end gap-8 -mt-20">
                    <!-- Profile Photo with Enhanced Upload -->
                    <div class="relative group">
                        <div class="h-44 w-44 rounded-[2.5rem] bg-white p-2 shadow-2xl ring-8 ring-white overflow-hidden transform group-hover:scale-[1.02] transition-all duration-500">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" class="h-full w-full object-cover rounded-[2rem]" alt="{{ $user->name }}">
                            @else
                                <div class="h-full w-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 font-black text-5xl rounded-[2rem] uppercase italic">
                                    {{ substr($user->name, 0, 1) }}{{ substr($user->first_name ?? '', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <button onclick="document.getElementById('photoUploadModal').classList.remove('hidden')" 
                                class="absolute bottom-2 right-2 p-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl transition-all hover:scale-110 active:scale-90 border-4 border-white">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                    </div>
                    
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full font-black text-[10px] uppercase tracking-widest border border-blue-100">
                                {{ strtoupper($user->role) }}
                            </span>
                            @if($user->is_active)
                                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full font-black text-[10px] uppercase tracking-widest border border-emerald-100 italic">Compte Actif</span>
                            @endif
                        </div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic">{{ $user->name }}</h1>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mt-2 flex items-center gap-2">
                            <i class="fas fa-hospital-alt"></i> {{ $user->hospital->name ?? 'Établissement' }} 
                            <span class="text-slate-200">/</span> 
                            <i class="fas fa-layer-group"></i> {{ $user->service->name ?? 'Service Général' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Information Column -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <div class="flex items-center gap-4 mb-8 pb-4 border-b border-slate-50">
                        <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Informations Personnelles</h2>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
                        @csrf
                        @method('patch')

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nom Complet</label>
                                <div class="relative group">
                                    <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                           class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" required>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Adresse Email</label>
                                <div class="relative group">
                                    <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" required>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">N° de Téléphone</label>
                                <div class="relative group">
                                    <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">N° d'Enregistrement / Matricule</label>
                                <div class="relative group">
                                    <i class="fas fa-id-badge absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="text" name="registration_number" value="{{ old('registration_number', $user->registration_number) }}" 
                                           class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-900/10">
                                Mettre à jour le profil
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password Security --}}
                <div id="security" class="bg-white p-10 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <div class="flex items-center gap-4 mb-8 pb-4 border-b border-slate-50">
                        <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Sécurité du Compte</h2>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-8">
                        @csrf
                        @method('put')

                        <div class="grid md:grid-cols-1 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Mot de passe actuel</label>
                                <input type="password" name="current_password" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none">
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nouveau mot de passe</label>
                                    <input type="password" name="password" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Confirmer le mot de passe</label>
                                    <input type="password" name="password_confirmation" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-50 flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-amber-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-700 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-amber-900/10">
                                Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats/Activity Side -->
            <div class="space-y-8">
                <div class="bg-gradient-to-tr from-slate-900 to-blue-950 p-10 rounded-[3rem] shadow-2xl text-white relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-125 transition-transform duration-700">
                        <i class="fas fa-shield-alt text-9xl"></i>
                    </div>
                    <h3 class="text-lg font-black uppercase italic tracking-tighter mb-4">Statut de Connexion</h3>
                    <div class="space-y-6 relative z-10">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase mb-1">Dernière activité</p>
                            <p class="font-bold text-blue-400">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Actuellement en ligne' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase mb-1">Localisation IP</p>
                            <p class="font-bold text-slate-200">Connecté via Intranet</p>
                        </div>
                        <div class="pt-6 border-t border-white/10">
                            <p class="text-[10px] text-slate-500 font-black uppercase mb-2">Protection des données</p>
                            <p class="text-xs text-slate-400 italic">Votre compte est protégé par des protocoles de sécurité hospitaliers de grade militaire.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 p-10 rounded-[3rem] shadow-xl shadow-blue-200 text-white">
                    <h3 class="text-lg font-black uppercase italic tracking-tighter mb-4">Besoin d'aide ?</h3>
                    <p class="text-blue-100 text-sm font-bold leading-relaxed mb-6">Si vous rencontrez des difficultés avec votre profil ou vos accès, contactez l'administrateur informatique de votre hôpital.</p>
                    <button class="w-full py-4 bg-white text-blue-600 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] hover:bg-blue-50 transition-all">Support Technique</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

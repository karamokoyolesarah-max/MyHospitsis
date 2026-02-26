@extends('layouts.app')

@section('title', 'Sécurité MFA - HospitSIS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 p-6 lg:p-10">
    <div class="max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <!-- Header Area -->
            <div class="p-10 bg-slate-900 text-white relative overflow-hidden">
                <div class="absolute -right-2 -top-2 opacity-10">
                    <i class="fas fa-shield-alt text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <h1 class="text-2xl font-black uppercase italic tracking-tighter mb-2">Authentification Multi-Facteurs (MFA)</h1>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Niveau de sécurité : Maximum
                    </p>
                </div>
            </div>

            <div class="p-10 space-y-8">
                <div class="flex items-start gap-6 p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                    <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm shrink-0">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-blue-900 uppercase italic text-xs mb-1">Pourquoi activer la MFA ?</h3>
                        <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                            La MFA ajoute une couche de protection cruciale. Même si quelqu'un découvre votre mot de passe, il ne pourra pas accéder à votre compte sans votre code mobile.
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="font-black text-slate-900 uppercase italic text-sm border-b border-slate-50 pb-4">Applications recommandées</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl flex items-center gap-3 border border-slate-100">
                            <i class="fab fa-google text-blue-500"></i>
                            <span class="text-[10px] font-black uppercase text-slate-600">Google Auth</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl flex items-center gap-3 border border-slate-100">
                            <i class="fab fa-windows text-blue-600"></i>
                            <span class="text-[10px] font-black uppercase text-slate-600">Microsoft Auth</span>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-50">
                    @if(auth()->user()->mfa_enabled)
                        <div class="p-8 bg-emerald-50 rounded-[2rem] border border-emerald-100 text-center space-y-4">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-emerald-500 mx-auto shadow-lg shadow-emerald-100">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-emerald-900 uppercase italic text-sm">Protection Activée</h3>
                                <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest mt-1">Votre compte est actuellement sous haute protection.</p>
                            </div>
                            
                            <form method="post" action="{{ route('mfa.disable') }}" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-white text-red-600 border border-red-100 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 transition-all">
                                    Désactiver la protection
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-6">
                            <div class="text-center">
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    Cliquez sur le bouton ci-dessous pour générer votre clé de sécurité unique et l'ajouter à votre application d'authentification.
                                </p>
                            </div>

                            <form method="post" action="{{ route('mfa.setup.post') }}">
                                @csrf
                                <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-95 transition-all">
                                    Activer la sécurité MFA
                                </button>
                            </form>

                            <a href="{{ route('settings') }}" class="block text-center text-slate-400 font-black uppercase tracking-widest text-[9px] hover:text-slate-600 transition-colors">
                                Retourner aux paramètres
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest italic">
                    <i class="fas fa-shield-virus mr-2"></i> Protégé par le système de sécurité HospitSIS
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

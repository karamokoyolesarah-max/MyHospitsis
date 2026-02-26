@extends('layouts.app')

@section('title', 'Confirmation de Sécurité - HospitSIS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 p-6 lg:p-10 flex items-center justify-center">
    <div class="max-w-md w-full animate-in fade-in zoom-in duration-500">
        
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden text-center">
            <!-- Header Area -->
            <div class="p-10 bg-blue-900 text-white relative overflow-hidden">
                <div class="absolute -right-2 -top-2 opacity-10">
                    <i class="fas fa-user-shield text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <h1 class="text-2xl font-black uppercase italic tracking-tighter mb-2">Zone Sécurisée</h1>
                    <p class="text-blue-400 text-[10px] font-bold uppercase tracking-widest">Confirmation d'identité requise</p>
                </div>
            </div>

            <div class="p-10 space-y-8">
                <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-sm border border-amber-100">
                    <i class="fas fa-lock text-3xl"></i>
                </div>

                <div class="space-y-4">
                    <h3 class="font-black text-slate-900 uppercase italic text-sm">Vérification du mot de passe</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Pour accéder à cette section sensible des paramètres, veuillez confirmer votre mot de passe actuel.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2 text-left">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Votre Mot de Passe</label>
                        <div class="relative group">
                            <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="password" name="password" required autocomplete="current-password" autofocus
                                   class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-[1.5rem] font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
                        </div>
                        @error('password')
                            <p class="text-[10px] text-red-500 font-black uppercase tracking-widest mt-2 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-95 transition-all">
                        Confirmer l'identité
                    </button>
                </form>

                <div class="pt-4 border-t border-slate-50">
                    <a href="{{ route('settings') }}" class="text-[9px] text-slate-400 font-black uppercase tracking-widest hover:text-slate-600 transition-colors">
                        Retourner en lieu sûr
                    </a>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100 italic">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">
                    <i class="fas fa-user-lock mr-2"></i> Protection Active HospitSIS
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

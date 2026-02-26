@extends('layouts.app')

@section('title', 'Vérification MFA - HospitSIS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 p-6 lg:p-10 flex items-center justify-center">
    <div class="max-w-md w-full animate-in fade-in zoom-in duration-500">
        
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden text-center">
            <!-- Header Area -->
            <div class="p-10 bg-slate-900 text-white relative overflow-hidden">
                <div class="absolute -right-2 -top-2 opacity-10">
                    <i class="fas fa-key text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <h1 class="text-2xl font-black uppercase italic tracking-tighter mb-2">Vérification Sécurisée</h1>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Saisie du code MFA</p>
                </div>
            </div>

            <div class="p-10 space-y-8">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-sm border border-blue-100">
                    <i class="fas fa-mobile-alt text-3xl"></i>
                </div>

                <div class="space-y-4">
                    <h3 class="font-black text-slate-900 uppercase italic text-sm">Entrez votre code</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Veuillez saisir le code à 6 chiffres généré par votre application d'authentification pour confirmer votre identité.
                    </p>
                </div>

                <form method="POST" action="{{ route('mfa.verify.post') }}" class="space-y-6">
                    @csrf

                    <div>
                        <input type="text" name="code" maxlength="6" autofocus placeholder="000000"
                               class="w-full text-center text-4xl font-black tracking-[1rem] py-6 bg-slate-50 border border-slate-100 rounded-[2rem] focus:bg-white focus:ring-8 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-slate-900">
                        @error('code')
                            <p class="text-[10px] text-red-500 font-black uppercase tracking-widest mt-4 animate-bounce">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-90 transition-all">
                        Valider l'accès
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-50">
                    @csrf
                    <button type="submit" class="text-[9px] text-slate-400 font-black uppercase tracking-widest hover:text-red-500 transition-colors">
                        Annuler et se déconnecter
                    </button>
                </form>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100 italic">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">
                    <i class="fas fa-lock mr-2"></i> Session sécurisée HospitSIS
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Admission Nouveau Patient - Secrétariat')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-12 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter italic">Admission Patient</h1>
            </div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs opacity-70">Enregistrement d'un nouveau dossier dans le système.</p>
        </div>
        <a href="{{ route('secretary.patients.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('secretary.patients.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations État Civil -->
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase italic tracking-tighter">État Civil & Identité</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nom de famille</label>
                        <input type="text" name="name" required placeholder="Ex: KOFFI" value="{{ old('name') }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Prénoms</label>
                        <input type="text" name="first_name" required placeholder="Ex: Joana" value="{{ old('first_name') }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Date de naissance</label>
                        <input type="date" name="dob" value="{{ old('dob') }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Genre</label>
                        <select name="gender" class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 appearance-none">
                            <option value="Homme" {{ old('gender') == 'Homme' ? 'selected' : '' }}>Homme</option>
                            <option value="Femme" {{ old('gender') == 'Femme' ? 'selected' : '' }}>Femme</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Coordonnées -->
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase italic tracking-tighter">Coordonnées de Contact</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Téléphone mobile</label>
                        <input type="text" name="phone" required placeholder="+225 00 00 00 00 00" value="{{ old('phone') }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Adresse Email</label>
                        <input type="email" name="email" placeholder="patient@exemple.com" value="{{ old('email') }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Adresse physique / Quartier</label>
                        <textarea name="address" rows="3" placeholder="Description de l'adresse..."
                                  class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 placeholder-slate-300">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pb-12">
                <button type="reset" class="px-8 py-4 bg-white border border-slate-200 text-slate-400 rounded-3xl font-black uppercase tracking-widest text-[10px] hover:bg-slate-50 transition">
                    Réinitialiser
                </button>
                <button type="submit" class="px-12 py-4 bg-blue-600 text-white rounded-3xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-700 transition shadow-2xl shadow-blue-200 shadow-blue-200 italic">
                    Enregistrer le Patient <i class="fas fa-save ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.external_doctor')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérer vos informations personnelles')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="gradient-primary p-8 text-center">
                <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 overflow-hidden relative">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-bold text-white">
                            {{ substr($user->prenom ?? 'D', 0, 1) }}{{ substr($user->nom ?? 'R', 0, 1) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-white">Dr. {{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</h2>
                <p class="text-indigo-200">{{ $user->specialite ?? 'Médecin' }}</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-500">Statut du compte</span>
                    <span class="px-3 py-1 {{ $user->statut === 'actif' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} rounded-full text-sm font-semibold">
                        {{ ucfirst($user->statut ?? 'En attente') }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-500">Disponibilité</span>
                    <span class="px-3 py-1 {{ $user->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} rounded-full text-sm font-semibold">
                        {{ $user->is_available ? 'Disponible' : 'Indisponible' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-500">Solde actuel</span>
                    <span class="font-bold text-indigo-600">{{ number_format($user->balance ?? 0, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-500">Membre depuis</span>
                    <span class="font-semibold text-gray-700">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier mes informations
                </h2>
            </div>
            <form method="POST" action="{{ route('external.profile.update') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Photo Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Photo de profil</label>
                        <div class="flex items-center space-x-6">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-200">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="profile_photo" accept="image/*" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-xl file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                ">
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF jusqu'à 10MB</p>
                                @error('profile_photo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('prenom')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('nom')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                        <p class="text-gray-400 text-xs mt-1">L'email ne peut pas être modifié</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone *</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('telephone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Spécialité *</label>
                        <input type="text" name="specialite" value="{{ old('specialite', $user->specialite) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('specialite')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">N° Ordre</label>
                        <input type="text" value="{{ $user->numero_ordre }}" disabled class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse du cabinet</label>
                        <input type="text" name="adresse_cabinet" value="{{ old('adresse_cabinet', $user->adresse_cabinet) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Adresse de votre cabinet médical">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse de résidence</label>
                        <input type="text" name="adresse_residence" value="{{ old('adresse_residence', $user->adresse_residence) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Votre adresse personnelle">
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit" class="inline-flex items-center space-x-2 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Documents
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Diplôme</p>
                            <p class="text-sm text-gray-500">{{ $user->diplome_path ? 'Téléversé' : 'Non fourni' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">CNI Recto</p>
                            <p class="text-sm text-gray-500">{{ $user->id_card_recto_path ? 'Téléversé' : 'Non fourni' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">CNI Verso</p>
                            <p class="text-sm text-gray-500">{{ $user->id_card_verso_path ? 'Téléversé' : 'Non fourni' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('users.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Modifier l'Utilisateur : {{ $user->name }}</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <!-- Informations personnelles -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations Personnelles
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-1 md:col-span-2">
                           <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                               <p class="text-sm text-blue-700 font-medium">Laissez les champs de mot de passe vides pour ne pas le modifier.</p>
                           </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Informations Professionnelles
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rôle *</label>
                            <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                                <option value="">Sélectionner un rôle...</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="doctor" {{ old('role', $user->role) == 'doctor' ? 'selected' : '' }}>Médecin</option>
                                <option value="nurse" {{ old('role', $user->role) == 'nurse' ? 'selected' : '' }}>Infirmier</option>
                                <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Caissier(ère)</option>
                                <option value="lab_technician" {{ old('role', $user->role) == 'lab_technician' ? 'selected' : '' }}>Technicien de Laboratoire</option>
                                <option value="radio_technician" {{ old('role', $user->role) == 'radio_technician' ? 'selected' : '' }}>Technicien Radio</option>
                                <option value="doctor_lab" {{ old('role', $user->role) == 'doctor_lab' ? 'selected' : '' }}>Médecin Biologiste</option>
                                <option value="administrative" {{ old('role', $user->role) == 'administrative' ? 'selected' : '' }}>Administratif</option>
                                <option value="pharmacist" {{ old('role', $user->role) == 'pharmacist' ? 'selected' : '' }}>Pharmacien(ne)</option>
                                <option value="secretary" {{ old('role', $user->role) == 'secretary' ? 'selected' : '' }}>Secrétaire Général(e)</option>
                            </select>
                            @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pôle Hospitalier *</label>
                            <select id="poleSelector" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-slate-50 font-bold">
                                <option value="">Choisir un pôle...</option>
                                <option value="medical" {{ ($user->service && $user->service->type === 'medical') ? 'selected' : '' }}>🏥 Pôle de Soins (Médical)</option>
                                <option value="technical" {{ ($user->service && $user->service->type === 'technical') ? 'selected' : '' }}>🔬 Pôle Technique (Diagnostic)</option>
                                <option value="support" {{ ($user->service && $user->service->type === 'support') ? 'selected' : '' }}>💳 Pôle de Caisse (Support)</option>
                                <option value="pharmacy" {{ ($user->service && $user->service->type === 'pharmacy') ? 'selected' : '' }}>💊 Pôle Pharmacie (Logistique)</option>
                                <option value="administrative" {{ ($user->service && $user->service->type === 'administrative') ? 'selected' : '' }}>📁 Pôle Administration (Gestion)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Affecté *</label>
                            <select name="service_id" id="serviceSelector" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="" data-pole="all">Sélectionner un service...</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" data-pole="{{ $service->type }}" {{ old('service_id', $user->service_id) == $service->id ? 'selected' : '' }} class="hidden">
                                    {{ $service->name }} @if($service->parent) <span class="text-xs text-gray-500 italic">({{ $service->parent->name }})</span> @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const poleSelector = document.getElementById('poleSelector');
                                const serviceSelector = document.getElementById('serviceSelector');
                                const options = serviceSelector.querySelectorAll('option:not([data-pole="all"])');

                                function filterServices(selectedPole, keepValue = false) {
                                    options.forEach(option => {
                                        if (option.dataset.pole === selectedPole) {
                                            option.classList.remove('hidden');
                                            option.disabled = false;
                                        } else {
                                            option.classList.add('hidden');
                                            option.disabled = true;
                                            if(!keepValue && option.selected) serviceSelector.value = '';
                                        }
                                    });
                                    
                                    serviceSelector.disabled = !selectedPole;
                                    if(!selectedPole && !keepValue) serviceSelector.value = '';
                                }

                                poleSelector.addEventListener('change', function() {
                                    filterServices(this.value);
                                });

                                // Initialisation au chargement
                                if(poleSelector.value) {
                                    filterServices(poleSelector.value, true);
                                }
                            });
                        </script>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Numéro d'enregistrement</label>
                            <input type="text" name="registration_number" value="{{ old('registration_number', $user->registration_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Numéro d'ordre professionnel ou d'enregistrement</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+225 07 00 00 00"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Compte actif</label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

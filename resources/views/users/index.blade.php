{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $users->total() }} utilisateurs enregistrés</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvel Utilisateur
            </a>
        </div>

        <!-- Filtres et Recherche Haut de Gamme -->
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-100/50 p-10 mb-10">
            <form method="GET" action="{{ route('users.index') }}" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-10 gap-y-8">
                    
                    <!-- Ligne 1 : Recherche et Rôle -->
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-normal text-slate-400 uppercase tracking-widest mb-3 ml-1">Rechercher un collaborateur</label>
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Jean Dupont, email@clic.com..." 
                                class="w-full px-6 py-4 pl-14 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-slate-600 placeholder:text-slate-300">
                            <i class="bi bi-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-normal text-slate-400 uppercase tracking-widest mb-3 ml-1">Rôle</label>
                        <select name="role" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-slate-600 appearance-none cursor-pointer">
                            <option value="">Tous les rôles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                            <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Médecin</option>
                            <option value="nurse" {{ request('role') == 'nurse' ? 'selected' : '' }}>Infirmier</option>
                            <option value="lab_technician" {{ request('role') == 'lab_technician' ? 'selected' : '' }}>Technicien Spécialisé</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Caissier</option>
                            <option value="pharmacist" {{ request('role') == 'pharmacist' ? 'selected' : '' }}>Pharmacien</option>
                            <option value="secretary" {{ request('role') == 'secretary' ? 'selected' : '' }}>Secrétariat</option>
                            <option value="administrative" {{ request('role') == 'administrative' ? 'selected' : '' }}>Staff Administratif</option>
                        </select>
                    </div>

                    <!-- Ligne 2 : Pôle, Service et Statut -->
                    <div>
                        <label class="block text-[11px] font-normal text-slate-400 uppercase tracking-widest mb-3 ml-1">Pôle Hospitalier</label>
                        <select name="pole" id="poleFilter" class="w-full px-6 py-4 bg-blue-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-blue-700 appearance-none cursor-pointer font-normal">
                            <option value="">Tous les pôles</option>
                            <option value="medical" {{ request('pole') == 'medical' ? 'selected' : '' }}>🏥 Soins (Médical)</option>
                            <option value="technical" {{ request('pole') == 'technical' ? 'selected' : '' }}>🔬 Technique (Labo)</option>
                            <option value="support" {{ request('pole') == 'support' ? 'selected' : '' }}>💳 Caisse (Support)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-normal text-slate-400 uppercase tracking-widest mb-3 ml-1">Service</label>
                        <select name="service_id" id="serviceFilter" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-slate-600 appearance-none cursor-pointer">
                            <option value="" data-pole="all">Tous les services</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" data-pole="{{ $service->type }}" {{ request('service_id') == $service->id ? 'selected' : '' }} class="{{ request('pole') && request('pole') != $service->type ? 'hidden' : '' }}">
                                {{ $service->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-normal text-slate-400 uppercase tracking-widest mb-3 ml-1">Statut</label>
                        <select name="is_active" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-slate-600 appearance-none cursor-pointer">
                            <option value="">Tous les statuts</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Comptes Actifs</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Comptes Inactifs</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <p class="text-xs text-slate-400 italic">Personnalisez votre vue en combinant les filtres ci-dessus.</p>
                    <div class="flex gap-4">
                        <a href="{{ route('users.index') }}" class="px-8 py-4 bg-slate-100 text-slate-500 rounded-2xl hover:bg-slate-200 transition-all text-sm tracking-wide">
                            Réinitialiser
                        </a>
                        <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 text-sm tracking-wide flex items-center gap-3">
                            Filtrer la liste <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const poleFilter = document.getElementById('poleFilter');
                    const serviceFilter = document.getElementById('serviceFilter');
                    const options = serviceFilter.querySelectorAll('option:not([data-pole="all"])');

                    poleFilter.addEventListener('change', function() {
                        const selectedPole = this.value;
                        options.forEach(option => {
                            if (!selectedPole || option.dataset.pole === selectedPole) {
                                option.classList.remove('hidden');
                                option.disabled = false;
                            } else {
                                option.classList.add('hidden');
                                option.disabled = true;
                                if(option.selected) serviceFilter.value = '';
                            }
                        });
                    });
                });
            </script>
        </div>

        <!-- Table des utilisateurs -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        @if($user->registration_number)
                                        <div class="text-sm text-gray-500">N° {{ $user->registration_number }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleColors = [
                                        'admin' => 'red',
                                        'doctor' => 'blue',
                                        'nurse' => 'green',
                                        'administrative' => 'purple'
                                    ];
                                    $color = $roleColors[$user->role] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->service->name ?? 'Non assigné' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Actif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-{{ $user->is_active ? 'red' : 'green' }}-600 hover:text-{{ $user->is_active ? 'red' : 'green' }}-900" title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                            @if($user->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0 0L5 21m13.364-15.364L5.636 18.364"></path>
                                            </svg>
                                            @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @endif
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="mt-4 text-gray-500">Aucun utilisateur trouvé</p>
                                <a href="{{ route('users.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                                    Créer le premier utilisateur →
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

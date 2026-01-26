@extends('layouts.external_doctor')

@section('title', 'Mes Prestations')
@section('page-title', 'Mes Prestations')
@section('page-subtitle', 'Définissez vos prestations et tarifs')

@section('content')
<div class="space-y-6" 
     x-data="{ 
        showModal: false, 
        editMode: false, 
        currentPrestation: null,
        price: '',
        commissionRate: 0,
        commissionAmount: 0,
        brackets: {{ $activeRate ? $activeRate->brackets->toJson() : '[]' }},
        baseRate: {{ $activeRate ? $activeRate->commission_percentage : 0 }},

        calculateCommission() {
            let p = parseFloat(this.price);
            if (isNaN(p) || p <= 0) {
                this.commissionRate = 0;
                this.commissionAmount = 0;
                return;
            }

            let found = false;
            // Trier les tranches par ordre pour être sûr
            let sortedBrackets = this.brackets.sort((a, b) => parseFloat(a.min_price) - parseFloat(b.min_price));
            
            for (let i = 0; i < sortedBrackets.length; i++) {
                let b = sortedBrackets[i];
                let min = parseFloat(b.min_price);
                // Si max_price est null, c'est l'infini
                let max = b.max_price ? parseFloat(b.max_price) : Infinity;

                if (p >= min && p <= max) {
                    this.commissionRate = parseFloat(b.percentage);
                    found = true;
                    break;
                }
            }

            if (!found && this.brackets.length === 0) {
                this.commissionRate = this.baseRate;
            } else if (!found && this.brackets.length > 0) {
                 // Si on n'a pas trouvé de tranche mais qu'il y en a, on peut avoir un cas hors limites ou défaut
                 // Généralement le dernier bracket a max_price=null donc couvre tout le reste.
                 // Si on est en dessous du min du premier bracket ?
                 this.commissionRate = this.baseRate; 
            }

            this.commissionAmount = Math.round((p * this.commissionRate) / 100);
        },

        openAddModal() {
            this.showModal = true;
            this.editMode = false;
            this.currentPrestation = null;
            this.price = '';
            this.commissionRate = 0;
            this.commissionAmount = 0;
        },

        init() {
            this.$watch('price', () => this.calculateCommission());
        }
     }"
     x-init="init()">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Prestations</h1>
            <p class="text-gray-500">Définissez les prestations que vous proposez et leurs tarifs</p>
        </div>
        <button @click="openAddModal()" class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-lg shadow-indigo-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nouvelle Prestation</span>
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $prestations->count() }}</p>
                    <p class="text-gray-500 text-sm">Total prestations</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $prestations->where('is_active', true)->count() }}</p>
                    <p class="text-gray-500 text-sm">Prestations actives</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($prestations->avg('price') ?? 0, 0, ',', ' ') }} FCFA</p>
                    <p class="text-gray-500 text-sm">Prix moyen</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prestations List -->
    @if($prestations->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune prestation</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">Commencez par définir les prestations que vous proposez avec leurs tarifs.</p>
        <button @click="openAddModal()" class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Ajouter une prestation</span>
        </button>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($prestations as $prestation)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition card-hover">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 {{ $prestation->is_active ? 'bg-teal-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $prestation->is_active ? 'text-teal-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 {{ $prestation->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} rounded-full text-xs font-semibold">
                        {{ $prestation->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $prestation->name }}</h3>
                <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $prestation->description ?? 'Aucune description' }}</p>
                
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($prestation->price, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 text-sm">Commission</p>
                        <p class="font-semibold text-teal-600">{{ number_format($prestation->commission_percentage, 2) }}%</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('external.prestations.toggle', $prestation->id) }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium {{ $prestation->is_active ? 'text-amber-600 hover:text-amber-700' : 'text-green-600 hover:text-green-700' }}">
                            {{ $prestation->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                    <div class="flex space-x-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('external.prestations.destroy', $prestation->id) }}" onsubmit="return confirm('Supprimer cette prestation ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 hover:bg-red-100 rounded-lg transition text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Modal Add/Edit Prestation -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" x-text="editMode ? 'Modifier la prestation' : 'Nouvelle prestation'"></h3>
                    <button @click="showModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('external.prestations.store') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom de la prestation *</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ex: Consultation générale">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Description de la prestation..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Prix (FCFA) *</label>
                            <input type="number" name="price" x-model="price" required min="0" step="100" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="15000">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Commission (%)</label>
                            <div class="px-4 py-3 border border-gray-100 bg-indigo-50 rounded-xl flex items-center justify-between">
                                <span class="text-indigo-700 font-bold text-lg" x-text="commissionRate + '%'"></span>
                                <span class="text-xs text-indigo-500" x-show="price > 0" x-text="'Soit ' + commissionAmount + ' FCFA'"></span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Calculé automatiquement selon le prix</p>
                        </div>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="button" @click="showModal = false" class="flex-1 py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

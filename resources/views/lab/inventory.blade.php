@extends('layouts.app')

@section('title', 'Gestion de Stock - Laboratoire')

@section('content')
<div x-data="{ 
    addItemModalOpen: false, 
    editItemModalOpen: false,
    itemToEdit: null,
    editItem(item) {
        this.itemToEdit = item;
        this.editItemModalOpen = true;
    }
}" class="px-6 py-8">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Stock & Matériel Laboratoire</h1>
            <p class="text-gray-500 mt-1">Gestion des réactifs, tubes de prélèvement et consommables</p>
        </div>
        
        <button @click="addItemModalOpen = true" class="bg-teal-600 text-white px-5 py-2.5 rounded-lg shadow-lg shadow-teal-500/30 hover:bg-teal-700 transition flex items-center gap-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter un article
        </button>
    </div>

    @php
        $inventory = $inventory ?? collect([]); 
    @endphp

    <!-- Stats Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Articles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $inventory->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Stock Critique</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $inventory->filter(fn($i) => $i->quantity <= $i->min_threshold)->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Péremption Proche</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $inventory->filter(fn($i) => $i->expiry_date && $i->expiry_date->diffInDays(now()) < 30)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                        <th class="px-6 py-4">Article</th>
                        <th class="px-6 py-4">Lot / Série</th>
                        <th class="px-6 py-4">Quantité</th>
                        <th class="px-6 py-4">Expiration</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->description }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-600">
                            {{ $item->batch_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold {{ $item->quantity <= $item->min_threshold ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item->quantity }}
                                </span>
                                <span class="text-xs text-gray-500 uppercase">{{ $item->unit }}</span>
                                @if($item->quantity <= $item->min_threshold)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 uppercase">Bas</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->expiry_date)
                                <div class="flex items-center gap-2 {{ $item->expiry_date->isPast() ? 'text-red-600 font-bold' : ($item->expiry_date->diffInDays(now()) < 30 ? 'text-orange-600 font-medium' : 'text-gray-600') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $item->expiry_date->format('d/m/Y') }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editItem({{ $item }})" class="p-2 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('lab.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                            Aucun article en stock actuellement.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL AJOUT -->
    <div x-show="addItemModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="addItemModalOpen = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <form action="{{ route('lab.inventory.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Nouvel Article</h3>
                        <button type="button" @click="addItemModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Désignation du produit</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none" placeholder="Ex: Réactif créatinine">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Quantité Initiale</label>
                                <input type="number" name="quantity" required min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Unité</label>
                                <select name="unit" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                                    <option value="pcs">Pièces</option>
                                    <option value="kit">Kit</option>
                                    <option value="bt">Boîte</option>
                                    <option value="fl">Flacon</option>
                                    <option value="test">Test</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Seuil Alerte</label>
                                <input type="number" name="min_threshold" value="5" min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Date d'Expiration</label>
                                <input type="date" name="expiry_date" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">N° Lot / Série</label>
                            <input type="text" name="batch_number" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none" placeholder="Facultatif">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="addItemModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500 uppercase tracking-widest hover:text-gray-700">Annuler</button>
                        <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-teal-700 shadow-lg shadow-teal-500/20 transition-all">Ajouter au stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITION -->
    <div x-show="editItemModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="editItemModalOpen = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <form x-bind:action="'/lab/inventory/' + (itemToEdit ? itemToEdit.id : '')" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Mettre à jour : <span x-text="itemToEdit?.name"></span></h3>
                        <button type="button" @click="editItemModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Quantité Actuelle</label>
                            <input type="number" name="quantity" x-bind:value="itemToEdit?.quantity" required min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Seuil Alerte</label>
                            <input type="number" name="min_threshold" x-bind:value="itemToEdit?.min_threshold" min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="editItemModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500 uppercase tracking-widest hover:text-gray-700">Annuler</button>
                        <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-teal-700 shadow-lg shadow-teal-500/20 transition-all">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

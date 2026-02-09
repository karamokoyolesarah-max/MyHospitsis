@extends('layouts.app')

@section('title', 'Gestion de Stock - Imagerie')

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
            <h1 class="text-3xl font-bold text-gray-900">Stock & Inventaire Imagerie</h1>
            <p class="text-gray-500 mt-1">Gestion des films, contrastes et consommables pour la radiologie</p>
        </div>
        
        <button @click="addItemModalOpen = true" class="bg-purple-600 text-white px-5 py-2.5 rounded-lg shadow-lg shadow-purple-500/30 hover:bg-purple-700 transition flex items-center gap-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter un article
        </button>
    </div>

    @php
        // Fetch inventory directly or via controller. Check if variable $inventory is passed.
        // Assuming controller passes $inventory.
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
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                    <th class="px-6 py-4">Article</th>
                    <th class="px-6 py-4">Lot / Série</th>
                    <th class="px-6 py-4">Quantité</th>
                    <th class="px-6 py-4">Expiration</th>
                    <th class="px-6 py-4 text-right">Mise à jour</th>
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
                            <button @click="editItem({{ $item }})" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form action="{{ route('lab.radio_technician.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
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
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Stock vide</h3>
                        <p class="text-gray-500 mt-1 mb-6">Commencez par ajouter vos films et réactifs.</p>
                        <button @click="addItemModalOpen = true" class="text-purple-600 hover:underline font-medium">Ajouter le premier article</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL AJOUT -->
    <div x-show="addItemModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addItemModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="addItemModalOpen = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="addItemModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('lab.radio_technician.inventory.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Nouvel Article</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nom du produit</label>
                                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Quantité</label>
                                            <input type="number" name="quantity" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Unité</label>
                                            <select name="unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
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
                                            <label class="block text-sm font-medium text-gray-700">Seuil Alerte</label>
                                            <input type="number" name="min_threshold" value="5" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Expiration</label>
                                            <input type="date" name="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">N° Lot (Optionnel)</label>
                                        <input type="text" name="batch_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ajouter
                        </button>
                        <button type="button" @click="addItemModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITION (Simplifié pour mise à jour rapide stock) -->
    <div x-show="editItemModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editItemModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="editItemModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="editItemModalOpen" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form x-bind:action="'/lab/radio_technician/inventory/' + (itemToEdit ? itemToEdit.id : '')" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Mettre à jour le stock : <span x-text="itemToEdit?.name"></span></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nouvelle Quantité</label>
                                <input type="number" name="quantity" x-bind:value="itemToEdit?.quantity" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seuil Alerte</label>
                                <input type="number" name="min_threshold" x-bind:value="itemToEdit?.min_threshold" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Enregistrer
                        </button>
                        <button type="button" @click="editItemModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

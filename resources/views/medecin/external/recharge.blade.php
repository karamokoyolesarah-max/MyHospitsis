@extends('layouts.external_doctor')

@section('title', 'Recharger')
@section('page-title', 'Recharger mon compte')
@section('page-subtitle', 'Rechargez votre solde via Mobile Money')

@section('content')
<div class="space-y-6">
    
    <!-- Current Balance -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-indigo-200 mb-2">Solde actuel</p>
                <h1 class="text-4xl font-bold">{{ number_format($user->balance ?? 0, 0, ',', ' ') }} FCFA</h1>
                @if($user->plan_expires_at)
                <p class="text-indigo-200 mt-2">Expire le {{ $user->plan_expires_at->format('d/m/Y') }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recharge Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau rechargement
                </h2>
            </div>
            <form method="POST" action="{{ route('external.recharge.initiate') }}" class="p-6">
                @csrf
                
                <!-- Amount Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Sélectionnez un montant</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="relative">
                            <input type="radio" name="amount" value="1000" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition text-center">
                                <p class="text-xl font-bold text-gray-900">1 000</p>
                                <p class="text-sm text-gray-500">FCFA</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="amount" value="2500" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition text-center">
                                <p class="text-xl font-bold text-gray-900">2 500</p>
                                <p class="text-sm text-gray-500">FCFA</p>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="amount" value="5000" class="peer sr-only" checked>
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition text-center">
                                <p class="text-xl font-bold text-gray-900">5 000</p>
                                <p class="text-sm text-gray-500">FCFA</p>
                                <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-indigo-600 text-white text-xs rounded-full">Populaire</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="amount" value="10000" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition text-center">
                                <p class="text-xl font-bold text-gray-900">10 000</p>
                                <p class="text-sm text-gray-500">FCFA</p>
                            </div>
                        </label>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm text-gray-500 mb-2">Ou entrez un montant personnalisé</label>
                        <input type="number" name="custom_amount" min="500" step="100" placeholder="Montant personnalisé" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Mode de paiement</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative">
                            <input type="radio" name="payment_method" value="mtn" class="peer sr-only" checked>
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-yellow-300 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-yellow-400 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">MTN</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">MTN Mobile Money</p>
                                        <p class="text-sm text-gray-500">Paiement instantané</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="payment_method" value="orange" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">Orange</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Orange Money</p>
                                        <p class="text-sm text-gray-500">Paiement instantané</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="payment_method" value="wave" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">Wave</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Wave</p>
                                        <p class="text-sm text-gray-500">Paiement instantané</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro de téléphone</label>
                    <input type="tel" name="phone_number" required value="{{ $user->telephone }}" placeholder="Ex: 07XXXXXXXX" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-gray-400 text-sm mt-2">Vous recevrez une demande de confirmation sur ce numéro</p>
                </div>

                <button type="submit" class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg shadow-indigo-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Procéder au paiement</span>
                </button>
            </form>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Historique</h2>
            </div>
            <div class="p-4">
                @if($recharges->isEmpty())
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">Aucune transaction</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recharges as $recharge)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-gray-900">{{ number_format($recharge->amount, 0, ',', ' ') }} FCFA</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $recharge->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $recharge->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $recharge->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ $recharge->status === 'completed' ? 'Succès' : ($recharge->status === 'pending' ? 'En attente' : 'Échoué') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ strtoupper($recharge->payment_method) }}</span>
                            <span class="text-gray-400">{{ $recharge->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-blue-800">Comment ça marche ?</h3>
                <ul class="text-blue-700 mt-2 space-y-1 text-sm">
                    <li>1. Sélectionnez le montant et le mode de paiement</li>
                    <li>2. Confirmez la transaction sur votre téléphone</li>
                    <li>3. Votre solde est crédité instantanément</li>
                    <li>4. Activez votre disponibilité pour recevoir des RDV</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <!-- Header avec photo de profil -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
            <div class="h-32 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"></div>
            <div class="px-8 pb-8">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-16">
                    <div class="flex flex-col md:flex-row md:items-end gap-6">
                        <!-- Photo de profil -->
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-3xl bg-white p-2 shadow-2xl">
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Photo de profil" class="w-full h-full rounded-2xl object-cover">
                                @else
                                    <div class="w-full h-full rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                        <span class="text-white text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <button onclick="document.getElementById('photoUploadModal').classList.remove('hidden')" 
                                    class="absolute bottom-2 right-2 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-xl shadow-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Infos utilisateur -->
                        <div class="mb-4">
                            <h1 class="text-3xl font-bold text-slate-900 mb-1">{{ $user->name }}</h1>
                            <p class="text-lg text-slate-600 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $user->hospital->name ?? 'Hôpital' }}
                            </p>
                            <p class="text-sm text-slate-500 mt-1">Administrateur Système</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 mt-4 md:mt-0 mb-4">
                        <span class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-semibold shadow-lg">
                            {{ now()->format('F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Colonne principale (2/3) -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informations Personnelles
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                            @csrf
                            @method('patch')

                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nom complet</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Téléphone</label>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">N° d'enregistrement</label>
                                    <input type="text" name="registration_number" value="{{ old('registration_number', $user->registration_number) }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Configuration API de Paiement -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Configuration API de Paiement
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-slate-600 mb-6">Configurez les numéros et QR Codes pour les paiements Mobile Money de votre hôpital.</p>
                        
                        <form action="{{ route('admin.payment.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Orange Money -->
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                        Orange Money
                                    </label>
                                    <input type="text" name="orange_money_number" value="{{ $user->hospital->payment_orange_number ?? '' }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 transition-all font-semibold"
                                           placeholder="+225 07 00 00 00 00">
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-semibold text-slate-600 mb-2">QR Code</label>
                                        @if($user->hospital->payment_qr_orange)
                                            <div class="mb-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                                <img src="{{ asset('storage/' . $user->hospital->payment_qr_orange) }}" alt="QR Orange" class="w-24 h-24 object-contain mx-auto">
                                            </div>
                                        @endif
                                        <input type="file" name="qr_orange" accept="image/*" 
                                               class="w-full px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
                                    </div>
                                </div>

                                <!-- MTN Money -->
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                        MTN Money
                                    </label>
                                    <input type="text" name="mtn_money_number" value="{{ $user->hospital->payment_mtn_number ?? '' }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-yellow-500 focus:bg-white focus:ring-4 focus:ring-yellow-500/10 transition-all font-semibold"
                                           placeholder="+225 05 00 00 00 00">
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-semibold text-slate-600 mb-2">QR Code</label>
                                        @if($user->hospital->payment_qr_mtn)
                                            <div class="mb-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                                <img src="{{ asset('storage/' . $user->hospital->payment_qr_mtn) }}" alt="QR MTN" class="w-24 h-24 object-contain mx-auto">
                                            </div>
                                        @endif
                                        <input type="file" name="qr_mtn" accept="image/*" 
                                               class="w-full px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all">
                                    </div>
                                </div>

                                <!-- Moov Money -->
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                        Moov Money
                                    </label>
                                    <input type="text" name="moov_money_number" value="{{ $user->hospital->payment_moov_number ?? '' }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-semibold"
                                           placeholder="+225 01 00 00 00 00">
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-semibold text-slate-600 mb-2">QR Code</label>
                                        @if($user->hospital->payment_qr_moov)
                                            <div class="mb-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                                <img src="{{ asset('storage/' . $user->hospital->payment_qr_moov) }}" alt="QR Moov" class="w-24 h-24 object-contain mx-auto">
                                            </div>
                                        @endif
                                        <input type="file" name="qr_moov" accept="image/*" 
                                               class="w-full px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                    </div>
                                </div>

                                <!-- Wave -->
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                        <span class="w-3 h-3 rounded-full bg-cyan-400"></span>
                                        Wave
                                    </label>
                                    <input type="text" name="wave_number" value="{{ $user->hospital->payment_wave_number ?? '' }}" 
                                           class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-400/10 transition-all font-semibold"
                                           placeholder="+225 07 00 00 00 00">
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-semibold text-slate-600 mb-2">QR Code</label>
                                        @if($user->hospital->payment_qr_wave)
                                            <div class="mb-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                                <img src="{{ asset('storage/' . $user->hospital->payment_qr_wave) }}" alt="QR Wave" class="w-24 h-24 object-contain mx-auto">
                                            </div>
                                        @endif
                                        <input type="file" name="qr_wave" accept="image/*" 
                                               class="w-full px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 transition-all">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-blue-800">Ces paramètres seront utilisés pour tous les paiements de votre hôpital.</p>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                        class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Sécurité du Compte
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                            @csrf
                            @method('put')

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Mot de passe actuel</label>
                                <input type="password" name="current_password" 
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-green-500 focus:bg-white focus:ring-4 focus:ring-green-500/10 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nouveau mot de passe</label>
                                <input type="password" name="password" 
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-green-500 focus:bg-white focus:ring-4 focus:ring-green-500/10 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-green-500 focus:bg-white focus:ring-4 focus:ring-green-500/10 transition-all">
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" 
                                        class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Changer le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar (1/3) -->
            <div class="space-y-6">
                <!-- Informations Hôpital -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Hôpital
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Nom</p>
                            <p class="font-bold text-slate-900">{{ $user->hospital->name ?? 'Non assigné' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Adresse</p>
                            <p class="text-slate-700">{{ $user->hospital->address ?? 'Non spécifiée' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Téléphone</p>
                            <p class="text-slate-700">{{ $user->hospital->phone ?? 'Non spécifié' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Email</p>
                            <p class="text-slate-700">{{ $user->hospital->email ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Plan d'Abonnement -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Abonnement
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Plan actuel</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $user->hospital->subscriptionPlan->name ?? 'Plan Gratuit' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Prix mensuel</p>
                            <p class="text-lg font-bold text-emerald-600">{{ $user->hospital->subscriptionPlan ? number_format($user->hospital->subscriptionPlan->price, 0) . ' FCFA' : '0 FCFA' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-2">Statut</p>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $user->hospital->subscriptionPlan ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $user->hospital->subscriptionPlan ? 'Actif' : 'Gratuit' }}
                            </span>
                        </div>
                        <div class="pt-4">
                            <a href="{{ route('admin.subscription.manage') }}" 
                               class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-4 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Gérer
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statut -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Statut
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Compte</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Rôle</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                Administrateur
                            </span>
                        </div>
                        <div class="pt-2 border-t border-slate-200">
                            <p class="text-xs text-slate-500 mb-1">Dernière connexion</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Jamais' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Vérification Professionnelle -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-user-check"></i> Vérification Pro.
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <p class="text-sm text-slate-600 mb-4">Liens officiels pour vérifier les praticiens :</p>
                        
                        <a href="https://www.ordremedecins.ci/" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors border border-slate-200 group">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Ordre des Médecins</p>
                                <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider">ONMCI CI</p>
                            </div>
                        </a>

                        <a href="https://diplomes-infas.net/" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors border border-slate-200 group">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Vérification INFAS</p>
                                <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider">Diplômes Infirmiers</p>
                            </div>
                        </a>

                        <a href="http://sante.gouv.ci/" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors border border-slate-200 group">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Ministère de la Santé</p>
                                <p class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider">Portail E-DEPPS</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Photo -->
<div id="photoUploadModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-slate-900">Photo de profil</h3>
            <button onclick="document.getElementById('photoUploadModal').classList.add('hidden')" 
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:border-blue-500 transition-colors">
                    <input type="file" name="profile_photo" accept="image/*" class="hidden" id="photoInput" onchange="previewPhoto(event)">
                    <label for="photoInput" class="cursor-pointer">
                        <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-slate-600 font-medium">Cliquez pour choisir une photo</p>
                        <p class="text-xs text-slate-500 mt-1">JPG, PNG (max 2MB)</p>
                    </label>
                </div>
                <div id="photoPreview" class="hidden">
                    <img id="preview" class="w-32 h-32 rounded-2xl mx-auto object-cover">
                </div>
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('photoPreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
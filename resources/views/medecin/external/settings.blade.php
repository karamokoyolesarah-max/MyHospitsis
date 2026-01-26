@extends('layouts.external_doctor')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('page-subtitle', 'Gérer les paramètres de votre compte')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar Settings -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="#security" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Sécurité</span>
                        </a>
                        <a href="#notifications" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span>Notifications</span>
                        </a>
                        <a href="#preferences" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            <span>Préférences</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h3 class="text-lg font-bold text-red-800 mb-2">Zone dangereuse</h3>
                <p class="text-red-600 text-sm mb-4">Ces actions sont irréversibles. Procédez avec prudence.</p>
                <button class="w-full py-3 px-4 border border-red-300 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition">
                    Supprimer mon compte
                </button>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Security -->
            <div id="security" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Sécurité du compte
                    </h2>
                </div>
                <form method="POST" action="{{ route('external.settings.password') }}" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="font-semibold text-gray-900 mb-4">Changer le mot de passe</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe actuel</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="inline-flex items-center space-x-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Mettre à jour</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications -->
            <div id="notifications" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <p class="font-semibold text-gray-900">Nouveaux rendez-vous</p>
                            <p class="text-sm text-gray-500">Recevoir une notification pour chaque nouveau RDV</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <p class="font-semibold text-gray-900">Rappels de RDV</p>
                            <p class="text-sm text-gray-500">Rappel 1h avant chaque rendez-vous</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-4">
                        <div>
                            <p class="font-semibold text-gray-900">Notifications par email</p>
                            <p class="text-sm text-gray-500">Recevoir les notifications par email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

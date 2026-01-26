@extends('layouts.external_doctor')

@section('title', 'Rendez-vous')
@section('page-title', 'Rendez-vous')
@section('page-subtitle', 'Gérer vos rendez-vous')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Rendez-vous</h1>
            <p class="text-gray-500">Consultez et gérez vos rendez-vous à venir</p>
        </div>
    </div>

    <!-- Info Banner -->
    @if(!$user->is_available)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-amber-800">Vous êtes actuellement indisponible</h3>
                <p class="text-amber-700 mt-1">Activez votre disponibilité pour recevoir de nouvelles demandes de rendez-vous.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                    <p class="text-gray-500 text-sm">Aujourd'hui</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                    <p class="text-gray-500 text-sm">En attente</p>
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
                    <p class="text-2xl font-bold text-gray-900">0</p>
                    <p class="text-gray-500 text-sm">Confirmés</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                    <p class="text-gray-500 text-sm">Cette semaine</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    @if($appointments->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun rendez-vous</h3>
        <p class="text-gray-500 max-w-md mx-auto">
            @if($user->is_available)
                Vous n'avez pas encore de rendez-vous. Les patients peuvent vous contacter via la plateforme.
            @else
                Activez votre disponibilité pour recevoir des demandes de rendez-vous.
            @endif
        </p>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Patient</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date & Heure</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Prestation</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Statut</th>
                    <th class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($appointments as $appointment)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <span class="text-indigo-600 font-bold">{{ substr($appointment->patient_name ?? 'P', 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient_name ?? 'Patient' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $appointment->datetime ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $appointment->prestation ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium">En attente</span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button class="p-2 hover:bg-green-100 rounded-lg transition text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                        <button class="p-2 hover:bg-red-100 rounded-lg transition text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

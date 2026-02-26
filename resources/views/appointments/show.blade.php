@extends('layouts.app')

@section('title', 'Détails du Rendez-vous')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Détails du Rendez-vous</h1>
                <p class="text-slate-500 text-sm mt-1">Ref: #{{ $appointment->id }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 font-medium transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
                <a href="{{ route('appointments.edit', $appointment) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium transition flex items-center shadow-lg shadow-amber-200">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center">
                <i class="fas fa-check-circle mr-3"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <!-- Status Bar -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Statut actuel</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold capitalize
                    {{ match($appointment->status) {
                        'pending' => 'bg-amber-100 text-amber-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    } }}">
                    <span class="w-2 h-2 rounded-full mr-2 
                        {{ match($appointment->status) {
                            'pending' => 'bg-amber-500',
                            'confirmed' => 'bg-blue-500',
                            'completed' => 'bg-emerald-500',
                            'cancelled' => 'bg-red-500',
                            default => 'bg-slate-500',
                        } }}"></span>
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Patient Info -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Patient</h3>
                            <a href="{{ route('patients.show', $appointment->patient) }}" class="text-lg font-bold text-slate-900 hover:text-blue-600 transition">
                                {{ $appointment->patient->full_name }}
                            </a>
                            <p class="text-xs font-mono text-slate-500 bg-slate-100 inline-block px-2 py-0.5 rounded mt-1">{{ $appointment->patient->ipu }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-md text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Médecin</h3>
                            <p class="text-lg font-bold text-slate-900">{{ $appointment->doctor->name ?? 'Non assigné' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                            <i class="fas fa-hospital-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Service</h3>
                            <p class="text-lg font-bold text-slate-900">{{ $appointment->service->name ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-600">
                            <i class="fas fa-calendar-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Date & Heure</h3>
                            <p class="text-lg font-bold text-slate-900 capitalize">{{ $appointment->appointment_datetime->translatedFormat('l d F Y') }}</p>
                            <p class="text-blue-600 font-bold">à {{ $appointment->appointment_datetime->format('H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center text-teal-600">
                            <i class="fas fa-stopwatch text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Durée</h3>
                            <p class="text-lg font-bold text-slate-900">{{ $appointment->duration_minutes }} minutes</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center text-rose-600">
                            <i class="fas fa-map-marker-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wide">Lieu</h3>
                            <p class="text-lg font-bold text-slate-900">{{ $appointment->location ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="px-8 pb-8 pt-4">
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3 flex items-center">
                        <i class="fas fa-sticky-note mr-2"></i> Motif de consultation
                    </h3>
                    <p class="text-slate-700 italic leading-relaxed">
                        "{{ $appointment->reason ?? 'Aucun motif détaillé renseigné pour ce rendez-vous.' }}"
                    </p>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-slate-50 px-8 py-6 border-t border-slate-100 flex justify-between items-center">
                <div class="text-xs text-slate-400">
                    Créé le {{ $appointment->created_at->format('d/m/Y à H:i') }}
                </div>
                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler/supprimer ce rendez-vous ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition border border-red-200 shadow-sm">
                        <i class="fas fa-trash-alt mr-2"></i> Supprimer le rendez-vous
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
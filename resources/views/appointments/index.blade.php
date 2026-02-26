 {{-- Fichier : resources/views/appointments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Rendez-vous')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Rendez-vous</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $appointments->total() }} rendez-vous</p>
            </div>
            @if(auth()->user()?->isDoctor() !== true)
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau Rendez-vous
            </a>
            @endif
        </div>

       {{-- REMPLACER LE BLOC DU FORMULAIRE PAR CELUI-CI --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
   <form action="{{ route('appointments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Tous</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Programmé</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>Absent</option>
            </select>
        </div>

        @if(auth()->user()?->isAdmin())
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
            <select name="doctor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tous</option>
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->name }}
                </option>
                @endforeach
            </select>
        </div>
        @else
            {{-- Champ vide pour maintenir la grille de 4 colonnes si pas admin --}}
            <div></div>
        @endif

        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Filtrer
            </button>
        </div>
    </form>
</div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médecin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $appointment->appointment_datetime->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $appointment->appointment_datetime->format('H:i') }} ({{ $appointment->duration }}min)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($appointment->patient)
                                <div class="text-sm font-medium text-gray-900">{{ $appointment->patient->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->patient->ipu }}</div>
                            @else
                                <div class="text-sm font-medium text-red-600">Patient introuvable</div>
                                <div class="text-sm text-gray-500">N/A</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($appointment->doctor)
                                {{ $appointment->doctor->name }}
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Non assigné
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->service ? $appointment->service->name : 'Service supprimé' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($appointment->reason, 30) ?? 'Non spécifié' }}
                        </td>
                        
                        {{-- Colonne Statut MODIFIÉE en Select --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select 
                                onchange="updateStatus({{ $appointment->id }}, this.value)"
                                class="status-select text-sm rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 cursor-pointer
                                    {{ $appointment->status === 'scheduled' ? 'bg-yellow-50 text-yellow-800 border-yellow-300' : '' }}
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-50 text-green-800 border-green-300' : '' }}
                                    {{ $appointment->status === 'completed' ? 'bg-gray-50 text-gray-800 border-gray-300' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-50 text-red-800 border-red-300' : '' }}
                                    {{ $appointment->status === 'no_show' ? 'bg-orange-50 text-orange-800 border-orange-300' : '' }}">
                                <option value="scheduled" {{ $appointment->status === 'scheduled' ? 'selected' : '' }}>Programmé</option>
                                <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Terminé</option>
                                <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                <option value="no_show" {{ $appointment->status === 'no_show' ? 'selected' : '' }}>Absent</option>
                            </select>
                        </td>
                        {{-- Fin de la colonne Statut modifiée --}}

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-3">
                                @if(in_array($appointment->status, ['confirmed', 'completed']))
                                    <div class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-200" title="Rendez-vous validé">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-xs font-bold uppercase">Validé</span>
                                    </div>
                                @endif

                                <a href="{{ route('appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition" title="Voir les détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Aucun rendez-vous trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($appointments->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function updateStatus(appointmentId, newStatus) {
        if (!confirm('Voulez-vous vraiment changer le statut de ce rendez-vous ?')) {
            // Recharger pour annuler le changement si l'utilisateur dit non
            location.reload(); 
            return;
        }

        // Récupérer le token CSRF à partir de la balise meta
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`{{ url('appointments') }}/${appointmentId}/status`, {
            method: 'POST', // On utilise POST pour simuler PATCH, si nécessaire
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                // Ligne optionnelle, mais souvent nécessaire si la route est définie comme PATCH
                'X-HTTP-Method-Override': 'PATCH' 
            },
            body: JSON.stringify({ status: newStatus, _method: 'PATCH' })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => Promise.reject(data));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('✓ Statut mis à jour avec succès', 'success');
                // Recharger la page après 1 seconde pour mettre à jour la couleur et les filtres (si appliqués)
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('❌ Erreur: ' + (data.message || 'Problème inconnu.'), 'error');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const errorMessage = error.message || 'Une erreur est survenue lors de la connexion au serveur.';
            showNotification('❌ ' + errorMessage, 'error');
            location.reload();
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white font-semibold`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.remove(), 3000);
    }
</script>

<style>
.status-select {
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    position: relative;
    z-index: 10;
}

.status-select:hover {
    opacity: 0.8;
}

/* Allow dropdown to overflow table container */
.bg-white.rounded-lg.shadow.overflow-hidden {
    overflow: visible;
}

/* Ensure table cell allows overflow */
tbody tr td {
    position: relative;
}
</style>
@endsection
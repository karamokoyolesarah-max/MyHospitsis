<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Rendez-vous') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 border-l-4 border-blue-600 pl-3">Mes Rendez-vous</h3>
                        <a href="{{ route('patient.book-appointment') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            <i class="fas fa-plus mr-2"></i> Prendre un rendez-vous
                        </a>
                    </div>

                    @if($appointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($appointments as $appointment)
                                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200 
                                    {{ $appointment->status === 'confirmed' ? 'bg-blue-50/50 border-blue-200' : '' }}
                                    {{ $appointment->status === 'completed' ? 'bg-green-50/50 border-green-200' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-50/50 border-red-200' : '' }}">
                                    
                                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                        <!-- Info Gauche -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-4 mb-2">
                                                <div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm text-center min-w-[60px]">
                                                    <div class="text-xs text-gray-500 font-bold uppercase">{{ $appointment->appointment_datetime->format('M') }}</div>
                                                    <div class="text-xl font-black text-gray-900">{{ $appointment->appointment_datetime->format('d') }}</div>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-lg text-gray-900 border-b border-gray-100 pb-1 mb-1">
                                                        {{ $appointment->service ? $appointment->service->name : 'Service non défini' }}
                                                    </h4>
                                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                                        <i class="fas fa-user-md text-blue-600"></i>
                                                        @if($appointment->doctor)
                                                            <span class="font-bold">Dr. {{ $appointment->doctor->name }} {{ $appointment->doctor->first_name ?? '' }}</span>
                                                        @else
                                                            <span class="italic text-gray-400">Médecin non assigné</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                                                        <i class="far fa-calendar-alt mr-1"></i> {{ $appointment->appointment_datetime->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="bi bi-clock"></i> {{ $appointment->appointment_datetime->format('H:i') }}
                                                </span>
                                                @if($appointment->hospital)
                                                    <span class="flex items-center gap-1">
                                                        <i class="bi bi-hospital"></i> {{ $appointment->hospital->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions Droite -->
                                        <div class="flex flex-col items-end gap-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                                @if($appointment->status === 'scheduled' || $appointment->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                                @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800 border border-blue-200
                                                @elseif($appointment->status === 'completed') bg-green-100 text-green-800 border border-green-200
                                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800 border border-red-200
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($appointment->status === 'pending' ? 'En attente' : ($appointment->status === 'confirmed' ? 'Confirmé' : ($appointment->status === 'cancelled' ? 'Annulé' : $appointment->status))) }}
                                            </span>

                                            <div class="flex items-center gap-2">
                                                <button onclick="openAppointmentModal({{ json_encode($appointment) }})" 
                                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition">
                                                    <i class="fas fa-eye mr-2"></i> Voir
                                                </button>

                                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                                    <form method="POST" action="{{ route('patient.cancel-appointment', $appointment) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-lg font-semibold text-xs text-red-600 uppercase tracking-widest hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                                            <i class="fas fa-trash-alt mr-2"></i> Annuler
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $appointments->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-calendar-event text-3xl"></i>
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-gray-900">Aucun rendez-vous</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de rendez-vous programmés.</p>
                            <div class="mt-6">
                                <a href="{{ route('patient.book-appointment') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-md text-base font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-all transform hover:scale-105">
                                    <i class="bi bi-plus-lg mr-2"></i> Prendre un rendez-vous
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div id="modalBackdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div id="modalPanel" class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        <i class="bi bi-calendar-check-fill"></i> Détails du Rendez-vous
                    </h3>
                    <button type="button" class="text-blue-100 hover:text-white transition-colors" onclick="closeModal()">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 bg-gray-50">
                    <!-- Status Banner -->
                    <div id="modalStatus" class="mb-6 rounded-xl p-4 flex items-center gap-3 font-bold text-sm border">
                        <!-- Filled by JS -->
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date & Location -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Date & Lieu</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Date et Heure</p>
                                    <p id="modalDate" class="font-bold text-gray-900 text-lg"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Lieu de consultation</p>
                                    <p id="modalLocation" class="font-medium text-gray-900 flex items-center gap-2"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Hôpital</p>
                                    <p id="modalHospital" class="font-medium text-gray-900"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Info -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Professionnel</h4>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <p id="modalDoctor" class="font-bold text-gray-900 text-lg"></p>
                                    <p id="modalService" class="text-sm text-blue-600 font-medium"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Reason -->
                    <div class="mt-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Informations Médicales</h4>
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">Motif du rendez-vous</p>
                            <p id="modalReason" class="font-medium text-gray-900"></p>
                        </div>
                        <div id="modalNotesContainer" class="hidden">
                            <p class="text-xs text-gray-500 mb-1">Notes complémentaires</p>
                            <p id="modalNotes" class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100"></p>
                        </div>
                    </div>

                    <!-- Financials (Prestations) -->
                    <div id="modalFinancials" class="mt-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm hidden">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Services & Facturation</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2">Prestation</th>
                                        <th class="px-3 py-2 text-right">Prix</th>
                                    </tr>
                                </thead>
                                <tbody id="modalPrestationsBody" class="divide-y divide-gray-100">
                                    <!-- Filled by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-100 px-6 py-4 flex justify-end">
                    <button type="button" class="px-6 py-2.5 bg-gray-800 text-white font-bold rounded-lg shadow hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900" onclick="closeModal()">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Ensure bootstrap icons are loaded
             if (!document.querySelector('link[href*="bootstrap-icons"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css';
                document.head.appendChild(link);
            }
        });

        function openAppointmentModal(appointment) {
            const modal = document.getElementById('appointmentModal');
            const backdrop = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');

            // --- Populate Data ---
            
            // Format Date
            const date = new Date(appointment.appointment_datetime);
            const dateFormatted = date.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            document.getElementById('modalDate').textContent = dateFormatted.charAt(0).toUpperCase() + dateFormatted.slice(1);

            // Hospital
            document.getElementById('modalHospital').textContent = appointment.hospital ? appointment.hospital.name : 'Non spécifié';

            // Location Logic
            const location = appointment.consultation_type === 'home' 
                ? `<i class="bi bi-house-door-fill text-orange-500"></i> À Domicile ` + (appointment.home_address ? `(${appointment.home_address})` : '')
                : `<i class="bi bi-hospital-fill text-blue-500"></i> À l'Hôpital`;
            document.getElementById('modalLocation').innerHTML = location;

            // Doctor & Service
            document.getElementById('modalDoctor').textContent = appointment.doctor ? 'Dr. ' + appointment.doctor.name : 'Médecin non assigné';
            document.getElementById('modalService').textContent = appointment.service ? appointment.service.name : 'Service non défini';

            // Reason & Notes
            document.getElementById('modalReason').textContent = appointment.reason || 'Aucun motif spécifié';
            
            const notesContainer = document.getElementById('modalNotesContainer');
            if (appointment.notes) {
                document.getElementById('modalNotes').textContent = appointment.notes;
                notesContainer.classList.remove('hidden');
            } else {
                notesContainer.classList.add('hidden');
            }

            // Status Styling
            const statusDiv = document.getElementById('modalStatus');
            let statusConfig = {
                'pending': { color: 'yellow', text: 'En attente de confirmation', icon: 'bi-hourglass-split' },
                'scheduled': { color: 'blue', text: 'Programmé', icon: 'bi-calendar-check' },
                'confirmed': { color: 'blue', text: 'Confirmé', icon: 'bi-check-circle-fill' },
                'completed': { color: 'green', text: 'Terminé', icon: 'bi-flag-fill' },
                'cancelled': { color: 'red', text: 'Annulé', icon: 'bi-x-circle-fill' },
            };

            const config = statusConfig[appointment.status] || { color: 'gray', text: appointment.status, icon: 'bi-info-circle' };
            
            statusDiv.className = `mb-6 rounded-xl p-4 flex items-center gap-3 font-bold text-sm border bg-${config.color}-50 text-${config.color}-800 border-${config.color}-200`;
            statusDiv.innerHTML = `<i class="bi ${config.icon} text-xl"></i> ${config.text}`;


            // Prestations
            const financialsDiv = document.getElementById('modalFinancials');
            const tbody = document.getElementById('modalPrestationsBody');
            tbody.innerHTML = '';

            if (appointment.prestations && appointment.prestations.length > 0) {
                financialsDiv.classList.remove('hidden');
                appointment.prestations.forEach(p => {
                    const row = document.createElement('tr');
                    row.className = 'bg-white border-b';
                    row.innerHTML = `
                        <td class="px-3 py-2 font-medium text-gray-900">${p.name}</td>
                        <td class="px-3 py-2 text-right">${new Intl.NumberFormat('fr-FR').format(p.pivot.unit_price)} FCFA</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                financialsDiv.classList.add('hidden');
            }

            // --- Show Modal ---
            modal.classList.remove('hidden');
            // Small timeout for transition animation if we added classes for that (omitted for simplicity but kept structure)
        }

        function closeModal() {
            document.getElementById('appointmentModal').classList.add('hidden');
        }

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });
    </script>
</x-portal-layout>

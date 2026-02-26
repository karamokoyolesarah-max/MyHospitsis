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
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Patient</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date & Heure</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase">Type / Adresse</th>
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
                                <span class="text-indigo-600 font-bold">{{ substr($appointment->patient->nom ?? 'P', 0, 1) }}{{ substr($appointment->patient->prenom ?? 'P', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->prenom }} {{ $appointment->patient->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->patient->telephone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_datetime->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $appointment->appointment_datetime->format('H:i') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold {{ $appointment->consultation_type === 'home' ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $appointment->consultation_type === 'home' ? '🏠 DOMICILE' : '🏥 HÔPITAL' }}
                        </span>
                        @if($appointment->consultation_type === 'home')
                            <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ $appointment->home_address }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'accepted' => 'bg-blue-100 text-blue-700',
                                'on_the_way' => 'bg-purple-100 text-purple-700 animate-pulse',
                                'arrived' => 'bg-indigo-100 text-indigo-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'accepted' => 'Confirmé',
                                'on_the_way' => 'En route',
                                'arrived' => 'Arrivé',
                                'completed' => 'Terminé',
                                'cancelled' => 'Annulé',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses[$appointment->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <button onclick="viewDetails({{ json_encode([
                                'id' => $appointment->id,
                                'patient_name' => $appointment->patient->prenom . ' ' . $appointment->patient->nom,
                                'patient_phone' => $appointment->patient->telephone,
                                'address' => $appointment->home_address,
                                'lat' => $appointment->patient->latitude,
                                'lon' => $appointment->patient->longitude,
                                'reason' => $appointment->reason,
                                'status' => $appointment->status,
                                'total' => number_format($appointment->total_amount ?? 15000) . ' FCFA',
                                'invoice_url' => route('external.appointments.invoice', $appointment->id)
                            ]) }})" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-bold hover:bg-gray-200">
                                <i class="fas fa-eye mr-1"></i> Détails
                            </button>

                            @if($appointment->status === 'pending')
                                <button onclick="updateStatus({{ $appointment->id }}, 'accepted', this)" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700">Accepter</button>
                            @endif

                            @if($appointment->status === 'accepted' && $appointment->consultation_type === 'home')
                                <button onclick="startTrip({{ $appointment->id }}, this)" class="bg-purple-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-purple-700">🚀 Partir</button>
                            @endif

                            @if($appointment->status === 'on_the_way')
                                <button onclick="updateStatus({{ $appointment->id }}, 'arrived', this)" class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-indigo-700">🚩 Arrivé</button>
                            @endif

                            @if($appointment->status === 'arrived')
                                <button onclick="updateStatus({{ $appointment->id }}, 'completed', this)" class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-green-700">✅ Terminer</button>
                            @endif

                            @if(in_array($appointment->status, ['accepted', 'on_the_way', 'arrived', 'completed']))
                                <a href="{{ route('external.appointments.invoice', $appointment->id) }}" class="bg-slate-800 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-slate-900 flex items-center space-x-1" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Facture</span>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Modal Détails Patient -->
    <div id="details_modal" class="fixed inset-0 z-[3000] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDetails()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Détails de la demande</h2>
                    <p class="text-slate-500 text-sm font-medium" id="modal_appointment_id"></p>
                </div>
                <button onclick="closeDetails()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto space-y-8">
                <!-- Patient Info -->
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 mb-6">
                    <div class="flex items-center space-x-6">
                        <div class="w-20 h-20 bg-white rounded-[1.5rem] flex items-center justify-center text-3xl text-indigo-600 shadow-sm border-4 border-indigo-50">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Patient à consulter</p>
                            <h3 class="text-2xl font-black text-slate-900 mb-1" id="modal_patient_name"></h3>
                            <a id="modal_call_link" href="#" class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-700 transition">
                                <i class="fas fa-phone-alt mr-2"></i> <span id="modal_patient_phone"></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reason Section -->
                <div class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100">
                    <p class="text-[10px] text-amber-600 font-black uppercase tracking-widest mb-1">Motif de consultation</p>
                    <p class="text-amber-900 font-medium text-lg leading-relaxed" id="modal_reason"></p>
                </div>

                <!-- Map & Address -->
                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Adresse de visite</p>
                            <p class="text-slate-900 font-bold text-lg" id="modal_address"></p>
                        </div>
                        <a id="modal_gps_link" href="#" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-location-arrow mr-2"></i> Lancer GPS
                        </a>
                    </div>
                    <div id="modal_map" class="h-64 bg-slate-100 rounded-[2rem] border border-slate-200 overflow-hidden z-10"></div>
                </div>

                <!-- Billing -->
                <div class="p-6 bg-slate-900 rounded-[2rem] text-white">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Information Facturation</p>
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-full text-[10px] font-black uppercase" id="modal_status"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-2xl font-black" id="modal_total"></p>
                            <p class="text-xs text-slate-500">Montant total à percevoir après soin</p>
                        </div>
                        <a id="modal_invoice_btn" href="#" target="_blank" class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-2xl text-sm font-black transition flex items-center border border-white/10">
                            <i class="fas fa-file-pdf mr-2"></i> Voir Facture PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
    @endif

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let trackingInterval = null;
        let currentAppointmentId = null;
        let detailMap = null;
        let detailMarker = null;

        function viewDetails(data) {
            document.getElementById('modal_appointment_id').innerText = 'RDV #' + data.id;
            document.getElementById('modal_patient_name').innerText = data.patient_name;
            document.getElementById('modal_patient_phone').innerText = data.patient_phone;
            document.getElementById('modal_call_link').href = 'tel:' + data.patient_phone;
            document.getElementById('modal_reason').innerText = data.reason || 'Aucun motif précisé';
            document.getElementById('modal_address').innerText = data.address;
            document.getElementById('modal_total').innerText = data.total;
            document.getElementById('modal_status').innerText = data.status;
            document.getElementById('modal_gps_link').href = `https://www.google.com/maps/dir/?api=1&destination=${data.lat},${data.lon}`;
            document.getElementById('modal_invoice_btn').href = data.invoice_url;
            
            document.getElementById('details_modal').classList.remove('hidden');

            // Map init
            setTimeout(() => {
                if (!detailMap) {
                    detailMap = L.map('modal_map').setView([data.lat, data.lon], 15);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(detailMap);
                    detailMarker = L.marker([data.lat, data.lon]).addTo(detailMap);
                } else {
                    detailMap.setView([data.lat, data.lon], 15);
                    detailMarker.setLatLng([data.lat, data.lon]);
                    detailMap.invalidateSize();
                }
            }, 100);
        }

        function closeDetails() {
            document.getElementById('details_modal').classList.add('hidden');
        }

        async function updateStatus(appointmentId, status, btn) {
            // Désactiver le bouton pour éviter les doubles clics
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            }

            try {
                // Utilisation d'un placeholder plus sûr pour l'ID
                const url = "{{ route('external.ajax.update-status', '__ID__') }}".replace('__ID__', appointmentId);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                });

                let data;
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error("Réponse serveur non-JSON (Erreur " + response.status + ")");
                }
                
                if (response.ok && data.status === 'success') {
                    if (status !== 'on_the_way') stopTracking();
                    location.reload(); 
                } else {
                    throw new Error(data.error || data.message || 'Erreur inconnue (Status: ' + response.status + ')');
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                alert('Désolé, une erreur est survenue : ' + error.message);
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = status === 'accepted' ? 'Accepter' : (status === 'on_the_way' ? '🚀 Partir' : 'Mettre à jour');
                }
            }
        }

        function startTrip(appointmentId, btn) {
            if (!confirm('Voulez-vous commencer le trajet ? Votre position sera partagée avec le patient.')) return;
            
            currentAppointmentId = appointmentId;
            updateStatus(appointmentId, 'on_the_way', btn).then(() => {
                startTracking();
            });
        }

        function startTracking() {
            if ("geolocation" in navigator) {
                // Première mise à jour immédiate
                sendLocation();
                // Puis toutes les 30 secondes
                trackingInterval = setInterval(sendLocation, 15000); // 15s pour plus de réactivité Yango
            } else {
                alert("La géolocalisation n'est pas supportée par votre navigateur.");
            }
        }

        function stopTracking() {
            if (trackingInterval) {
                clearInterval(trackingInterval);
                trackingInterval = null;
            }
        }

        function sendLocation() {
            navigator.geolocation.getCurrentPosition(async (position) => {
                try {
                    await fetch('{{ route("external.ajax.update-location") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            appointment_id: currentAppointmentId
                        })
                    });
                } catch (error) {
                    console.error("Erreur d'envoi de position:", error);
                }
            }, (error) => {
                console.warn("Erreur de géolocalisation:", error.message);
            }, {
                enableHighAccuracy: true
            });
        }

        // Si rechargement de page et statut "en route" ou "accepté", relancer le tracking
        @foreach($appointments as $appointment)
            @if(in_array($appointment->status, ['accepted', 'on_the_way']))
                currentAppointmentId = {{ $appointment->id }};
                startTracking();
            @endif
        @endforeach
    </script>

</div>
@endsection

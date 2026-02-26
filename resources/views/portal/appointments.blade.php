<x-portal-layout>
    <!-- Dependencies: Leaflet, FontAwesome, Tailwind (CDN for immediate fix) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .leaflet-routing-container { display: none !important; } /* Cacher les instructions textuelles */
        /* Fix for Map Z-Index context */
        .leaflet-pane { z-index: 0 !important; }
        .leaflet-control { z-index: 5 !important; }
        /* Scrollbar Styling */
        #pmodal_no_map::-webkit-scrollbar { width: 6px; }
        #pmodal_no_map::-webkit-scrollbar-track { background: transparent; }
        #pmodal_no_map::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Mes Rendez-vous</h2>

            <div class="space-y-6">
                @forelse($appointments as $appointment)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 hover:shadow-md transition">
                        <!-- Header avec Date et Statut -->
                        <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-lg px-3 py-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase">{{ $appointment->appointment_datetime->format('M') }}</span>
                                    <span class="text-lg font-black text-slate-800">{{ $appointment->appointment_datetime->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-slate-900">{{ $appointment->appointment_datetime->format('H:i') }}</p>
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                                        {{ $appointment->consultation_type === 'home' ? 'À Domicile' : 'Hôpital' }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                @if($appointment->status === 'on_the_way') bg-indigo-100 text-indigo-700 animate-pulse
                                @elseif($appointment->status === 'arrived') bg-emerald-100 text-emerald-700
                                @elseif($appointment->status === 'accepted') bg-blue-100 text-blue-700
                                @elseif($appointment->status === 'confirmed') bg-green-100 text-green-700
                                @elseif($appointment->status === 'completed') bg-gray-100 text-gray-700
                                @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-600 @endif">
                                @if($appointment->status === 'on_the_way') En Route
                                @elseif($appointment->status === 'arrived') Arrivé
                                @elseif($appointment->status === 'accepted') Accepté
                                @elseif($appointment->status === 'confirmed') Confirmé
                                @elseif($appointment->status === 'completed') Terminé
                                @elseif($appointment->status === 'pending') En attente
                                @else {{ ucfirst($appointment->status) }}
                                @endif
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ $appointment->medecinExterne ? 'Dr. ' . $appointment->medecinExterne->prenom . ' ' . $appointment->medecinExterne->nom : ($appointment->doctor ? 'Dr. ' . $appointment->doctor->name : 'Médecin non assigné') }}
                            </h3>
                            <p class="text-slate-500 text-sm mb-4">
                                {{ $appointment->medecinExterne ? $appointment->medecinExterne->specialite : ($appointment->service ? $appointment->service->name : 'Généraliste') }}
                            </p>

                            @if($appointment->reason)
                                <p class="text-sm text-slate-400 italic mb-4">"{{ $appointment->reason }}"</p>
                            @endif

                            <!-- Action Buttons - Flux séquentiel clair -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-6">
                                
                                <!-- 1. Détails -->
                                <button onclick="viewDoctorDetails({{ json_encode([
                                    'id' => $appointment->id,
                                    'doctor_name' => $appointment->medecinExterne ? $appointment->medecinExterne->prenom . ' ' . $appointment->medecinExterne->nom : ($appointment->doctor ? $appointment->doctor->name : 'N/A'),
                                    'doctor_phone' => $appointment->medecinExterne ? $appointment->medecinExterne->telephone : ($appointment->doctor ? $appointment->doctor->phone : 'N/A'),
                                    'doctor_photo' => $appointment->medecinExterne ? ($appointment->medecinExterne->profile_photo_path ? asset('storage/' . $appointment->medecinExterne->profile_photo_path) : asset('assets/img/default-avatar.png')) : asset('assets/img/default-avatar.png'),
                                    'specialty' => $appointment->medecinExterne ? $appointment->medecinExterne->specialite : ($appointment->service ? $appointment->service->name : 'Généraliste'),
                                    'lat' => $appointment->doctor_current_latitude ?? 5.3484,
                                    'lon' => $appointment->doctor_current_longitude ?? -4.0305,
                                    'patient_lat' => Auth::guard('patients')->user()->latitude ?? 5.3484,
                                    'patient_lon' => Auth::guard('patients')->user()->longitude ?? -4.0305,
                                    'status' => $appointment->status,
                                    'reason' => $appointment->reason ?? 'Aucun motif spécifié',
                                    'total_raw' => $appointment->total_amount ?? 0,
                                    'total' => number_format($appointment->total_amount ?? 0, 0, ',', ' ') . ' FCFA',
                                    'invoice_url' => route('patient.invoices.pdf', $appointment->id),
                                    'formatted_date' => $appointment->appointment_datetime->translatedFormat('l d F Y'),
                                    'formatted_time' => $appointment->appointment_datetime->format('H:i'),
                                    'created_at_human' => $appointment->created_at->diffForHumans(),
                                    'consultation_type_label' => $appointment->consultation_type === 'home' ? 'À Domicile' : 'À l\'Hôpital',
                                    'patient_name' => Auth::guard('patients')->user()->full_name,
                                    'patient_phone' => Auth::guard('patients')->user()->phone ?? 'Non renseigné',
                                    'patient_address' => Auth::guard('patients')->user()->address ?? 'Adresse non renseignée',
                                    'payment_status_label' => $appointment->payment_transaction_id ? 'Réglé' : 'En attente',
                                    'payment_status_color' => $appointment->payment_transaction_id ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50',
                                    'payment_info' => $appointment->medecinExterne ? [
                                        'orange' => [
                                            'number' => $appointment->medecinExterne->payment_orange_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_orange ? asset('storage/' . $appointment->medecinExterne->payment_qr_orange) : null
                                        ],
                                        'mtn' => [
                                            'number' => $appointment->medecinExterne->payment_mtn_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_mtn ? asset('storage/' . $appointment->medecinExterne->payment_qr_mtn) : null
                                        ],
                                        'moov' => [
                                            'number' => $appointment->medecinExterne->payment_moov_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_moov ? asset('storage/' . $appointment->medecinExterne->payment_qr_moov) : null
                                        ],
                                        'wave' => [
                                            'number' => $appointment->medecinExterne->payment_wave_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_wave ? asset('storage/' . $appointment->medecinExterne->payment_qr_wave) : null
                                        ],
                                    ] : []
                                ]) }}, false)" class="flex items-center justify-center px-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold text-xs uppercase hover:bg-slate-50 transition">
                                    <i class="fas fa-info-circle mr-2"></i> Détails
                                </button>

                                <!-- 2. Action principale selon l'étape -->
                                @if($appointment->status === 'arrived' && !$appointment->patient_confirmation_start_at)
                                    {{-- Étape 1 : Le médecin est arrivé, le patient confirme le début --}}
                                    <form action="{{ route('patient.appointments.confirm-start', $appointment) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-emerald-700 transition shadow-md">
                                            <i class="fas fa-play mr-2"></i> 🩺 Débuter la consultation
                                        </button>
                                    </form>
                                @elseif($appointment->patient_confirmation_start_at && !$appointment->patient_confirmation_end_at)
                                    {{-- Étape 2 : Consultation en cours, le patient confirme la fin --}}
                                    <form action="{{ route('patient.appointments.confirm-end', $appointment) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-slate-800 text-white rounded-xl font-bold text-xs uppercase hover:bg-black transition shadow-md">
                                            <i class="fas fa-stop mr-2"></i> 🏁 Terminer la consultation
                                        </button>
                                    </form>
                                @elseif($appointment->patient_confirmation_end_at && !$appointment->payment_transaction_id)
                                    {{-- Étape 3 : Consultation terminée, le patient paie --}}
                                    <button onclick="viewDoctorDetails({{ json_encode([
                                        'id' => $appointment->id,
                                        'doctor_name' => $appointment->medecinExterne ? $appointment->medecinExterne->prenom . ' ' . $appointment->medecinExterne->nom : ($appointment->doctor ? $appointment->doctor->name : 'N/A'),
                                        'doctor_phone' => $appointment->medecinExterne ? $appointment->medecinExterne->telephone : ($appointment->doctor ? $appointment->doctor->phone : 'N/A'),
                                        'doctor_photo' => $appointment->medecinExterne ? ($appointment->medecinExterne->profile_photo_path ? asset('storage/' . $appointment->medecinExterne->profile_photo_path) : asset('assets/img/default-avatar.png')) : asset('assets/img/default-avatar.png'),
                                        'specialty' => $appointment->medecinExterne ? $appointment->medecinExterne->specialite : ($appointment->service ? $appointment->service->name : 'Généraliste'),
                                        'lat' => $appointment->doctor_current_latitude ?? 5.3484,
                                        'lon' => $appointment->doctor_current_longitude ?? -4.0305,
                                        'patient_lat' => Auth::guard('patients')->user()->latitude ?? 5.3484,
                                        'patient_lon' => Auth::guard('patients')->user()->longitude ?? -4.0305,
                                        'status' => $appointment->status,
                                        'reason' => $appointment->reason ?? 'Aucun motif spécifié',
                                        'total_raw' => $appointment->total_amount ?? 0,
                                        'total' => number_format($appointment->total_amount ?? 0, 0, ',', ' ') . ' FCFA',
                                        'invoice_url' => route('patient.invoices.pdf', $appointment->id),
                                        'formatted_date' => $appointment->appointment_datetime->translatedFormat('l d F Y'),
                                        'formatted_time' => $appointment->appointment_datetime->format('H:i'),
                                        'created_at_human' => $appointment->created_at->diffForHumans(),
                                        'consultation_type_label' => $appointment->consultation_type === 'home' ? 'À Domicile' : 'À l\'Hôpital',
                                        'patient_name' => Auth::guard('patients')->user()->full_name,
                                        'patient_phone' => Auth::guard('patients')->user()->phone ?? 'Non renseigné',
                                        'patient_address' => Auth::guard('patients')->user()->address ?? 'Adresse non renseignée',
                                        'payment_status_label' => 'En attente de paiement',
                                        'payment_status_color' => 'text-amber-600 bg-amber-50',
                                        'payment_info' => $appointment->medecinExterne ? [
                                            'orange' => [
                                                'number' => $appointment->medecinExterne->payment_orange_number ?? null,
                                                'qr' => $appointment->medecinExterne->payment_qr_orange ? asset('storage/' . $appointment->medecinExterne->payment_qr_orange) : null
                                            ],
                                            'mtn' => [
                                                'number' => $appointment->medecinExterne->payment_mtn_number ?? null,
                                                'qr' => $appointment->medecinExterne->payment_qr_mtn ? asset('storage/' . $appointment->medecinExterne->payment_qr_mtn) : null
                                            ],
                                            'moov' => [
                                                'number' => $appointment->medecinExterne->payment_moov_number ?? null,
                                                'qr' => $appointment->medecinExterne->payment_qr_moov ? asset('storage/' . $appointment->medecinExterne->payment_qr_moov) : null
                                            ],
                                            'wave' => [
                                                'number' => $appointment->medecinExterne->payment_wave_number ?? null,
                                                'qr' => $appointment->medecinExterne->payment_qr_wave ? asset('storage/' . $appointment->medecinExterne->payment_qr_wave) : null
                                            ],
                                        ] : []
                                    ]) }}, false, true)" class="w-full flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-indigo-700 transition shadow-md">
                                        <i class="fas fa-credit-card mr-2"></i> 💳 Procéder au paiement
                                    </button>
                                @elseif($appointment->payment_transaction_id && !$appointment->rating_stars)
                                    {{-- Étape 4 : Payé, le patient note le médecin --}}
                                    <button onclick="openRatingModal('{{ route('patient.appointments.rate', $appointment->id) }}')" class="w-full flex items-center justify-center px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-xs uppercase hover:bg-amber-600 transition shadow-md">
                                        <i class="fas fa-star mr-2"></i> ⭐ Noter le médecin
                                    </button>
                                @else
                                    {{-- Étape 5 : Tout est terminé --}}
                                    <div class="flex items-center justify-center px-4 py-3 bg-slate-100 text-slate-400 rounded-xl font-bold text-xs uppercase cursor-not-allowed">
                                        <i class="fas fa-check-circle mr-2"></i> ✅ Terminé
                                    </div>
                                @endif

                                <!-- 3. Suivi GPS -->
                                @if($appointment->consultation_type === 'home' && in_array($appointment->status, ['accepted', 'on_the_way']))
                                    <button onclick="viewDoctorDetails({{ json_encode([
                                        'id' => $appointment->id,
                                        'doctor_name' => $appointment->medecinExterne ? $appointment->medecinExterne->prenom . ' ' . $appointment->medecinExterne->nom : 'N/A',
                                        'doctor_phone' => $appointment->medecinExterne ? $appointment->medecinExterne->telephone : 'N/A',
                                        'doctor_photo' => $appointment->medecinExterne ? ($appointment->medecinExterne->profile_photo_path ? asset('storage/' . $appointment->medecinExterne->profile_photo_path) : asset('assets/img/default-avatar.png')) : asset('assets/img/default-avatar.png'),
                                        'specialty' => $appointment->medecinExterne ? $appointment->medecinExterne->specialite : 'Généraliste',
                                        'lat' => $appointment->doctor_current_latitude ?? 5.3484,
                                        'lon' => $appointment->doctor_current_longitude ?? -4.0305,
                                        'patient_lat' => Auth::guard('patients')->user()->latitude ?? 5.3484,
                                        'patient_lon' => Auth::guard('patients')->user()->longitude ?? -4.0305,
                                    'status' => $appointment->status,
                                    'total' => number_format($appointment->total_amount ?? 0, 0, ',', ' ') . ' FCFA',
                                    'invoice_url' => route('patient.invoices.pdf', $appointment->id),
                                    'payment_info' => $appointment->medecinExterne ? [
                                        'orange' => [
                                            'number' => $appointment->medecinExterne->payment_orange_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_orange ? asset('storage/' . $appointment->medecinExterne->payment_qr_orange) : null
                                        ],
                                        'mtn' => [
                                            'number' => $appointment->medecinExterne->payment_mtn_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_mtn ? asset('storage/' . $appointment->medecinExterne->payment_qr_mtn) : null
                                        ],
                                        'moov' => [
                                            'number' => $appointment->medecinExterne->payment_moov_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_moov ? asset('storage/' . $appointment->medecinExterne->payment_qr_moov) : null
                                        ],
                                        'wave' => [
                                            'number' => $appointment->medecinExterne->payment_wave_number ?? null,
                                            'qr' => $appointment->medecinExterne->payment_qr_wave ? asset('storage/' . $appointment->medecinExterne->payment_qr_wave) : null
                                        ],
                                    ] : []
                                ]) }}, true)" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-blue-700 transition shadow-md animate-pulse">
                                        <i class="fas fa-map-marker-alt mr-2"></i> 📍 Suivre (GPS)
                                    </button>
                                @else
                                    <div class="flex items-center justify-center px-4 py-3 bg-slate-100 text-slate-300 rounded-xl font-bold text-xs uppercase cursor-not-allowed">
                                        <i class="fas fa-map-marker-alt mr-2"></i> Suivre
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
                        <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="far fa-calendar-alt text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Aucun rendez-vous</h3>
                        <p class="text-slate-500 text-sm mt-2">Vous n'avez pas encore de consultation prévue.</p>
                        <a href="{{ route('patient.book-appointment') }}" class="mt-6 inline-block px-6 py-3 bg-slate-900 text-white rounded-lg font-bold text-sm hover:bg-black transition">
                            Prendre Rendez-vous
                        </a>
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- REDESIGNED Modal Détails & Suivi GPS -->
    <div id="doctor_details_modal" class="fixed inset-0 z-[3000] hidden">
        <!-- Backdrop with blur -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeDoctorDetails()"></div>
        
        <!-- Modal Container -->
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white w-full max-w-4xl h-[90vh] md:h-auto md:max-h-[90vh] rounded-3xl shadow-2xl overflow-hidden flex flex-col pointer-events-auto transform transition-all scale-95 opacity-0" id="modal_content">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="fas fa-info-circle text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900" id="pmodal_title">Détails du Rendez-vous</h2>
                            <p class="text-slate-500 text-xs font-bold uppercase tracking-wider" id="pmodal_appointment_id">RDV #</p>
                        </div>
                    </div>
                    <button onclick="closeDoctorDetails()" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Main Content Area -->
                <div class="flex-grow overflow-y-auto bg-slate-50 relative">
                    
                    <!-- Map Container (Hidden by default, used for GPS tracking) -->
                    <div id="pmodal_map" class="absolute inset-0 w-full h-full bg-slate-200 hidden z-10"></div>

                    <!-- DETAILS VIEW (Default) -->
                    <div id="pmodal_no_map" class="p-6 md:p-8 space-y-6">
                        
                        <!-- Top Row: Doctor & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <!-- Doctor Card (Takes 2 cols) -->
                            <div class="md:col-span-2 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-start gap-5">
                                <img id="pmodal_doctor_photo" src="" class="w-24 h-24 rounded-2xl object-cover shadow-md border-2 border-white bg-slate-100">
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900" id="pmodal_doctor_name">Dr. --</h3>
                                            <p class="text-indigo-600 font-bold text-xs uppercase" id="pmodal_doctor_specialty">--</p>
                                        </div>
                                        <div class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-lg text-xs font-bold flex items-center border border-yellow-100">
                                            <i class="fas fa-star mr-1"></i> 4.9
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex gap-3">
                                        <a id="pmodal_call_btn" href="#" class="flex-1 px-4 py-2 bg-slate-900 text-white rounded-xl text-center text-xs font-bold uppercase hover:bg-black transition shadow-lg flex items-center justify-center gap-2">
                                            <i class="fas fa-phone"></i> Appeler
                                        </a>
                                        <!-- Placeholder for additional actions if needed -->
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Timing Card -->
                            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-center">
                                <div class="text-center mb-2">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase tracking-wider" id="pmodal_status_badge">
                                        --
                                    </span>
                                </div>
                                <p class="text-center text-xs font-bold text-indigo-500 uppercase tracking-widest mb-4" id="pdetail_mode">--</p>
                                
                                <div class="text-center">
                                    <p class="text-3xl font-black text-slate-900" id="pdetail_time">--:--</p>
                                    <p class="text-sm font-bold text-slate-500" id="pdetail_date">-- -- ----</p>
                                </div>
                                <p class="text-center text-xs text-slate-400 mt-3" id="pdetail_created">Réservé il y a --</p>
                            </div>
                        </div>

                        <!-- Middle Row: Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Patient Info -->
                            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="fas fa-user-injured text-indigo-500"></i> Patient
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-slate-500">Nom complet</span>
                                        <span class="text-sm font-bold text-slate-900" id="pdetail_patient_name">--</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-slate-500">Téléphone</span>
                                        <span class="text-sm font-bold text-slate-900" id="pdetail_patient_phone">--</span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm text-slate-500">Lieu</span>
                                        <div class="text-right">
                                            <span class="block text-sm font-bold text-slate-900" id="pdetail_type">--</span>
                                            <span class="block text-xs text-slate-400 max-w-[150px]" id="pdetail_address">--</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="fas fa-wallet text-emerald-500"></i> Paiement
                                </h4>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-3xl font-black text-slate-900" id="pdetail_amount">-- FCFA</span>
                                    <span class="text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-500" id="pdetail_payment_status">--</span>
                                </div>
                                <a href="#" id="pdetail_invoice_btn" class="w-full block py-2 text-center border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                    <i class="fas fa-file-invoice mr-2"></i> Télécharger la facture
                                </a>
                            </div>
                        </div>

                        <!-- Reason Section -->
                        <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100 shadow-inner">
                            <h4 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-stethoscope"></i> Motif de la consultation
                            </h4>
                            <p class="text-indigo-900 font-medium italic leading-relaxed" id="pdetail_reason">
                                --
                            </p>
                        </div>

                        <!-- PAYMENT SECTION (Hidden by default) -->
                        <div id="pdetail_payment_section" class="bg-slate-800 rounded-3xl p-6 md:p-8 text-center text-white hidden shadow-2xl">
                            <h3 class="text-xl font-bold mb-2">Effectuer le paiement</h3>
                            <p class="text-slate-400 text-sm mb-6">Scannez un QR Code ci-dessous</p>
                            
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="pdetail_qr_container">
                                <!-- QR Codes loaded via JS -->
                            </div>

                            <form id="confirm_payment_form" method="POST" action="">
                                @csrf
                                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg transition transform active:scale-95">
                                    J'ai effectué le paiement
                                </button>
                            </form>
                        </div>

                        <div class="h-4"></div> <!-- Spacer -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div id="rating_modal" class="fixed inset-0 z-[4000] hidden">
        <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" onclick="closeRatingModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[3rem] shadow-2xl p-10 text-center">
            <div class="w-20 h-20 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-inner">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Notez votre médecin</h3>
            <p class="text-slate-500 text-sm mb-8">Comment s'est passée votre consultation ?</p>
            
            <form id="rating_form" method="POST" action="">
                @csrf
                <div class="flex justify-center space-x-2 mb-8" id="star_container">
                    @for($i=1; $i<=5; $i++)
                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="hidden peer/star{{ $i }}">
                        <label for="star{{ $i }}" class="cursor-pointer text-4xl text-slate-200 hover:text-amber-400 peer-checked/star{{ $i }}:text-amber-500 transition-colors">
                            <i class="fas fa-star"></i>
                        </label>
                    @endfor
                </div>

                <textarea name="comment" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-amber-500 outline-none mb-6" placeholder="Laissez un commentaire..."></textarea>

                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-xl">
                    Envoyer l'avis
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        let pDetailMap = null;
        let pRoutingControl = null;

        function viewDoctorDetails(data, showMap = true, forcePaymentMode = false) {
            // Remplir les données de BASE (Modale Doctor Card)
            document.getElementById('pmodal_appointment_id').innerText = 'RDV #' + data.id;
            document.getElementById('pmodal_doctor_name').innerText = data.doctor_name;
            document.getElementById('pmodal_doctor_specialty').innerText = data.specialty;
            document.getElementById('pmodal_doctor_photo').src = data.doctor_photo;
            // document.getElementById('pmodal_total').innerText = data.total; // FIXED: Element removed in new design
            document.getElementById('pmodal_call_btn').href = 'tel:' + data.doctor_phone;
            document.getElementById('pmodal_status_badge').innerText = data.status;

            // Titre contextuel
            const titleElem = document.getElementById('pmodal_title');
            
            // Gestion de l'affichage de la carte vs Details View
            const mapContainer = document.getElementById('pmodal_map');
            const noMapContainer = document.getElementById('pmodal_no_map');
            const paymentSection = document.getElementById('pdetail_payment_section');

            // Afficher le Modal avec Animation
            const modal = document.getElementById('doctor_details_modal');
            const content = document.getElementById('modal_content');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 50);

            // Reset payment section initially
            paymentSection.classList.add('hidden');

            // LOGIQUE STRICTE : Map vs Details View
            if(showMap) {
                // *** MODE CARTE / GPS ***
                titleElem.innerText = "Suivi GPS & Trajet";
                mapContainer.classList.remove('hidden');
                noMapContainer.classList.add('hidden');
                
                // Initialiser la carte seulement si demandée
                setTimeout(() => {
                    if (!pDetailMap) {
                        pDetailMap = L.map('pmodal_map', {zoomControl: false}).setView([data.lat, data.lon], 13);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(pDetailMap);
                    } else {
                        pDetailMap.invalidateSize();
                    }

                    if (pRoutingControl) {
                        pDetailMap.removeControl(pRoutingControl);
                        pRoutingControl = null;
                    }
                    
                    // Nettoyer les marqueurs existants
                    pDetailMap.eachLayer((layer) => {
                        if (layer instanceof L.Marker) {
                            pDetailMap.removeLayer(layer);
                        }
                    });

                    // ROUTING ACTIF
                    pRoutingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(data.lat, data.lon), // Médecin
                            L.latLng(data.patient_lat, data.patient_lon) // Patient
                        ],
                        lineOptions: {
                            styles: [{color: '#6366f1', opacity: 0.8, weight: 6}]
                        },
                        createMarker: function(i, wp, nWps) {
                            if (i === 0) {
                                return L.marker(wp.latLng, {
                                    icon: L.divIcon({
                                        html: '<div class="w-10 h-10 bg-indigo-600 rounded-xl border-4 border-white shadow-xl flex items-center justify-center text-white text-lg"><i class="fas fa-user-md"></i></div>',
                                        className: '', iconSize: [40, 40], iconAnchor: [20, 20]
                                    })
                                });
                            } else {
                                return L.marker(wp.latLng, {
                                    icon: L.divIcon({
                                        html: '<div class="w-8 h-8 bg-blue-500 rounded-full border-4 border-white shadow-xl flex items-center justify-center text-white"><i class="fas fa-map-pin"></i></div>',
                                        className: '', iconSize: [32, 32], iconAnchor: [16, 16]
                                    })
                                });
                            }
                        },
                        addWaypoints: false,
                        routeWhileDragging: false,
                        fitSelectedRoutes: true,
                        show: false
                    }).addTo(pDetailMap);
                }, 400);

            } else {
                // *** MODE DETAILS (SANS CARTE) ***
                titleElem.innerText = forcePaymentMode ? "Validation & Paiement" : "Détails du Rendez-vous";
                mapContainer.classList.add('hidden');
                noMapContainer.classList.remove('hidden');

                // Remplir les détails textuels
                document.getElementById('pdetail_date').innerText = data.formatted_date;
                document.getElementById('pdetail_time').innerText = data.formatted_time;
                document.getElementById('pdetail_created').innerText = "Réservé " + data.created_at_human;
                
                // document.getElementById('pdetail_badge').innerText = data.status; // REMOVED (Fixed crash)
                document.getElementById('pdetail_mode').innerText = data.consultation_type_label; // ADDED

                document.getElementById('pdetail_patient_name').innerText = data.patient_name;
                document.getElementById('pdetail_patient_phone').innerText = data.patient_phone;
                document.getElementById('pdetail_type').innerText = data.consultation_type_label;
                document.getElementById('pdetail_address').innerText = data.patient_address;
                document.getElementById('pdetail_amount').innerText = data.total;
                document.getElementById('pdetail_payment_status').innerText = data.payment_status_label;
                // document.getElementById('pdetail_payment_status').className = "text-sm font-bold " + data.payment_status_color;
                
                document.getElementById('pdetail_reason').innerText = data.reason;
                document.getElementById('pdetail_invoice_btn').href = data.invoice_url;

                // PAYMENTS Logic
                if (forcePaymentMode && data.payment_info) {
                    paymentSection.classList.remove('hidden');
                    const qrContainer = document.getElementById('pdetail_qr_container');
                    qrContainer.innerHTML = ''; // Clear
                    
                    // Route for confirmation
                    document.getElementById('confirm_payment_form').action = "/portal/appointments/" + data.id + "/confirm-payment";

                    Object.keys(data.payment_info).forEach(key => {
                        const info = data.payment_info[key];
                        if (info.number || info.qr) {
                            const card = document.createElement('div');
                            card.className = "bg-slate-700/50 border border-slate-600 p-3 rounded-xl text-center backdrop-blur-sm";
                            
                            let qrHtml = info.qr ? `<div class="bg-white p-2 rounded-lg mb-2 shadow-inner h-24 flex items-center justify-center"><img src="${info.qr}" class="max-h-full object-contain"></div>` : `<div class="bg-slate-600 rounded-lg mb-2 h-24 flex items-center justify-center text-white/20 text-2xl"><i class="fas fa-qrcode"></i></div>`;
                            
                            card.innerHTML = `
                                <h4 class="text-slate-300 font-bold text-[10px] uppercase tracking-widest mb-2">${key}</h4>
                                ${qrHtml}
                                <p class="text-white font-bold text-xs">${info.number || 'N/A'}</p>
                            `;
                            qrContainer.appendChild(card);
                        }
                    });
                }
            }
        }

        function closeDoctorDetails() {
            const modal = document.getElementById('doctor_details_modal');
            const content = document.getElementById('modal_content');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function openRatingModal(actionUrl) {
            document.getElementById('rating_form').action = actionUrl;
            document.getElementById('rating_modal').classList.remove('hidden');
        }

        function closeRatingModal() {
            document.getElementById('rating_modal').classList.add('hidden');
        }
    </script>
</x-portal-layout>

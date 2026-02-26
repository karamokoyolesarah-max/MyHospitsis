<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi du Médecin | HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        #map { border-radius: 2rem; z-index: 10; }
        .tracking-status { animation: pulse-green 2s infinite; }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
        .marker-pulse { animation: marker-pulse 2s infinite; }
        @keyframes marker-pulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(2.5); opacity: 0; } }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 overflow-hidden h-screen flex flex-col">
    
    <header class="bg-white/80 sticky top-0 z-[2000] border-b border-slate-200 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.appointments') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left text-slate-600"></i>
                    </a>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">Arrivée du médecin</h1>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest rounded-full tracking-status border border-emerald-200">
                        <i class="fas fa-motorcycle mr-2"></i> En route
                    </span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow relative flex flex-col">
        <!-- Carte Plein Écran -->
        <div id="map" class="h-full w-full"></div>

        <!-- Overlay Haut: ETA & Distance -->
        <div class="absolute top-6 left-1/2 -translate-x-1/2 z-[1000] w-[90%] max-w-sm">
            <div class="bg-slate-900 text-white rounded-3xl p-4 shadow-2xl flex items-center justify-between border border-slate-700/50">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-blue-500/30">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Arrivée estimée</p>
                        <p id="eta" class="text-xl font-black">Calcul...</p>
                    </div>
                </div>
                <div class="text-right px-4 border-l border-slate-700">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Distance</p>
                    <p id="distance" class="text-lg font-bold">-- km</p>
                </div>
            </div>
        </div>

        <!-- Overlay Bas: Médecin -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000] w-[95%] max-w-lg">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 p-6 glass transition-all hover:scale-[1.01]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-5">
                        <div class="relative">
                            <img id="doctor_photo" src="{{ $appointment->medecinExterne->profile_photo_path ? asset('storage/' . $appointment->medecinExterne->profile_photo_path) : asset('assets/img/default-avatar.png') }}" 
                                class="w-20 h-20 rounded-3xl object-cover border-4 border-white shadow-xl">
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">{{ $appointment->medecinExterne->prenom }} {{ $appointment->medecinExterne->nom }}</h2>
                            <p class="text-blue-600 font-bold text-sm tracking-tight inline-flex items-center">
                                <i class="fas fa-stethoscope mr-2"></i> {{ $appointment->medecinExterne->specialite ?? 'Médecin Généraliste' }}
                            </p>
                            <div class="flex items-center mt-2 space-x-1">
                                @for($i=0; $i<5; $i++)
                                <i class="fas fa-star text-orange-400 text-xs text-shadow"></i>
                                @endfor
                                <span class="text-xs font-bold text-slate-400 ml-1">4.9 (120 avis)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-3">
                        <a href="tel:{{ $appointment->medecinExterne->telephone }}" class="w-14 h-14 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-200 flex items-center justify-center hover:bg-emerald-600 transition hover:rotate-12">
                            <i class="fas fa-phone-alt text-xl"></i>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Sécurité</p>
                            <p class="text-xs font-bold">ID: #SIS-{{ substr($appointment->id, 0, 8) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Paiement</p>
                            <p class="text-xs font-bold">{{ number_format($appointment->total_amount ?? 15000) }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // CONFIG
        const patientPos = [{{ $appointment->patient->latitude ?? 5.3484 }}, {{ $appointment->patient->longitude ?? -4.0305 }}];
        let doctorPos = [{{ $appointment->doctor_current_latitude ?? 5.35 }}, {{ $appointment->doctor_current_longitude ?? -4.04 }}];

        const map = L.map('map', {zoomControl: false}).setView(patientPos, 14);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Custom Icons
        const patientIcon = L.divIcon({
            html: '<div class="relative w-10 h-10"><div class="absolute inset-0 bg-blue-500/20 rounded-full animate-ping"></div><div class="relative bg-blue-600 w-full h-full rounded-full border-4 border-white shadow-xl flex items-center justify-center text-white"><i class="fas fa-house-user"></i></div></div>',
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        const doctorIcon = L.divIcon({
            html: '<div class="relative w-12 h-12"><div class="absolute inset-0 bg-emerald-500/30 rounded-full marker-pulse"></div><div class="relative bg-slate-900 w-full h-full rounded-2xl border-4 border-white shadow-2xl flex items-center justify-center text-white text-xl"><i class="fas fa-motorcycle"></i></div></div>',
            className: '',
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });

        const patientMarker = L.marker(patientPos, {icon: patientIcon}).addTo(map);
        const doctorMarker = L.marker(doctorPos, {icon: doctorIcon}).addTo(map);

        // Visual Route
        const routeLine = L.polyline([doctorPos, patientPos], {
            color: '#4f46e5',
            weight: 4,
            opacity: 0.6,
            dashArray: '10, 10',
            lineJoin: 'round'
        }).addTo(map);

        // Auto adjust view
        const bounds = L.latLngBounds([patientPos, doctorPos]);
        map.fitBounds(bounds.pad(0.5));

        async function fetchTrackingData() {
            try {
                const response = await fetch("{{ route('ajax.appointment.tracking-data', $appointment->id) }}");
                const data = await response.json();

                if (data.doctor_location && data.doctor_location.lat) {
                    const newPos = [data.doctor_location.lat, data.doctor_location.lng];
                    doctorMarker.setLatLng(newPos);
                    routeLine.setLatLngs([newPos, patientPos]);
                    
                    // Smooth pan
                    // map.panTo(newPos);

                    const d = map.distance(doctorMarker.getLatLng(), patientMarker.getLatLng());
                    document.getElementById('distance').innerText = (d / 1000).toFixed(1) + ' km';
                    
                    const etaMinutes = Math.round(d / 400); // approx 24km/h in city traffic
                    document.getElementById('eta').innerText = etaMinutes > 0 ? etaMinutes + ' min' : 'Arrivé';
                }

                if (data.status === 'arrived') {
                    document.getElementById('eta').innerText = "Arrivé !";
                    // Alert user
                } else if (data.status === 'completed') {
                    window.location.href = "{{ route('patient.appointments') }}";
                }

            } catch (error) {
                console.error("Tracking error:", error);
            }
        }

        setInterval(fetchTrackingData, 5000);
        fetchTrackingData();
    </script>
</body>
</html>


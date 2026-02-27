<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prendre un Rendez-vous | HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet JS & CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        .step-active { color: #3b82f6; border-bottom: 2px solid #3b82f6; }
        @keyframes pulse-blue { 0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        .pulse { animation: pulse-blue 2s infinite; }
        .doctor-card { transition: all 0.3s ease; cursor: pointer; border: 2px solid transparent; }
        .doctor-card.selected { border-color: #3b82f6; background-color: #eff6ff; }
        .doctor-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        #map { border-radius: 1rem; }
        .animate-marquee { display: inline-flex; animation: marquee 25s linear infinite; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900">
    
    <header class="bg-white/80 sticky top-0 z-[2000] border-b border-slate-200 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left text-slate-600"></i>
                    </a>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">Prendre un rendez-vous</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if($consultationType === 'home')
            <!-- Avertissement de sécurité -->
            <div class="mb-8 bg-red-600 text-white py-3 rounded-2xl overflow-hidden shadow-xl border-b-4 border-red-800">
                <div class="whitespace-nowrap animate-marquee font-bold flex items-center uppercase tracking-wider text-sm">
                    <span class="px-8"><i class="fas fa-biohazard mr-2"></i> TOUTE CONSULTATION HORS DE L'APPLICATION NE SERA PAS PRISE EN CHARGE.</span>
                    <span class="px-8"><i class="fas fa-biohazard mr-2"></i> TOUTE CONSULTATION HORS DE L'APPLICATION NE SERA PAS PRISE EN CHARGE.</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg">
                <p class="font-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($consultationType === 'home')
        <!-- Progress Steps (Home Only) -->
        <div class="flex justify-center mb-12">
            <div class="inline-flex bg-white/50 backdrop-blur-md p-1.5 rounded-[2rem] shadow-sm border border-slate-200/60 transition-all duration-500">
                <button type="button" onclick="goToStep(1)" class="step-btn px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300 step-active flex items-center" data-step="1">
                    <span class="w-6 h-6 rounded-full border-2 border-current flex items-center justify-center text-[10px] mr-2">1</span>
                    Localisation
                </button>
                <button type="button" onclick="goToStep(2)" class="step-btn px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300 text-slate-400 flex items-center" data-step="2">
                    <span class="w-6 h-6 rounded-full border-2 border-current flex items-center justify-center text-[10px] mr-2">2</span>
                    Spécialité
                </button>
                <button type="button" onclick="goToStep(3)" class="step-btn px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300 text-slate-400 flex items-center" data-step="3">
                    <span class="w-6 h-6 rounded-full border-2 border-current flex items-center justify-center text-[10px] mr-2">3</span>
                    Finalisation
                </button>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('patient.book-appointment.store') }}" id="appointmentForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            @csrf
            <input type="hidden" name="consultation_type" id="consultation_type_input" value="{{ $consultationType }}">
            
            <input type="hidden" name="latitude" id="patient_lat">
            <input type="hidden" name="longitude" id="patient_lon">
            <input type="hidden" name="calculated_distance" id="calculated_distance">
            <input type="hidden" name="calculated_travel_fee" id="calculated_travel_fee">
            <input type="hidden" name="tax_amount" id="tax_amount">
            <input type="hidden" name="total_amount" id="total_amount">
            
            @if($consultationType === 'home')
                <input type="hidden" name="hospital_id" id="hidden_hospital_id" value="{{ $hospitals[0]->id ?? 1 }}">
                <input type="hidden" name="medecin_externe_id" id="medecin_externe_id">
            @endif

            <!-- Content Area -->
            <div class="lg:col-span-8 space-y-6">
                
                @if($consultationType === 'home')
                <!-- STEP 1: LOCALISATION (HOME ONLY) -->
                <div id="step-1-content" class="step-panel animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-10 space-y-8">
                        <div class="flex items-center space-x-4 mb-2">
                            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xl shadow-sm border border-indigo-100/50">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 leading-tight">Lieu de consultation</h2>
                                <p class="text-slate-400 font-medium text-sm">Précisez l'adresse où le médecin doit se rendre</p>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors duration-300 group-focus-within:text-indigo-600 text-slate-400">
                                <i class="fas fa-map-marker-alt text-lg"></i>
                            </div>
                            <input type="text" name="home_address" id="home_address" {{ $consultationType === 'home' ? 'required' : '' }} 
                                class="w-full pl-14 pr-32 py-5 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none font-bold text-slate-700 placeholder:text-slate-300" 
                                placeholder="Entrez votre adresse exacte...">
                            
                            <div class="absolute inset-y-2 right-2 flex space-x-2">
                                <button type="button" id="btn_mylocation" class="w-12 h-12 bg-white text-slate-600 rounded-xl hover:bg-slate-50 border border-slate-200 transition-all flex items-center justify-center shadow-sm active:scale-95" title="Ma position actuelle">
                                    <i class="fas fa-location-arrow"></i>
                                </button>
                                <button type="button" id="btn_geocode" class="w-12 h-12 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all flex items-center justify-center shadow-lg shadow-indigo-200 active:scale-95 pulse" title="Rechercher">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div id="map_container" class="h-[450px] w-full bg-slate-100 rounded-[2rem] overflow-hidden relative border border-slate-200/60 shadow-inner group">
                            <div id="map" class="h-full w-full grayscale-[0.2] group-hover:grayscale-0 transition-all duration-700"></div>
                            <div class="absolute bottom-6 left-6 z-[1000] bg-white/95 backdrop-blur-md px-5 py-3 rounded-2xl text-[11px] font-black text-slate-700 shadow-2xl border border-white flex items-center uppercase tracking-widest">
                                <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full mr-3 animate-ping"></span>
                                Ajustez le marqueur sur la carte
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="button" onclick="goToStep(2)" class="group px-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-xs hover:bg-black transition-all duration-300 shadow-2xl shadow-slate-400/20 flex items-center active:scale-95">
                                Choisir la spécialité
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: SPÉCIALITÉ & MÉDECIN (HOME ONLY) -->
                <div id="step-2-content" class="step-panel hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-10 space-y-10">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-xl shadow-sm border border-emerald-100/50">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 leading-tight">Spécialité & Service</h2>
                                <p class="text-slate-400 font-medium text-sm">Quel type de soin recherchez-vous ?</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-2">
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Type de Service</label>
                                <div class="relative group">
                                    <select name="service_id_home" id="service_id_home" {{ $consultationType === 'home' ? 'required' : '' }} onchange="updatePrestations(this.value)"
                                        class="w-full py-5 px-6 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none appearance-none font-bold text-slate-700">
                                        <option value="">-- Choisir un service --</option>
                                        @foreach($hospitalsData[$hospitals[0]->id]['services'] ?? [] as $service)
                                            <option value="{{ $service['id'] }}" data-price="{{ $service['price'] }}">{{ $service['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Prestation Spécifique</label>
                                <div class="relative group">
                                    <select name="prestation_id_home" id="prestation_id_home" onchange="calculatePrices()"
                                        class="w-full py-5 px-6 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none appearance-none font-bold text-slate-700">
                                        <option value="">-- Choisir une prestation --</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Spécialité du médecin</label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($specialties as $specialty)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="specialty_choice" value="{{ $specialty }}" class="peer sr-only">
                                    <div class="p-4 py-5 text-center border-2 border-slate-50 bg-slate-50/50 rounded-2xl hover:border-indigo-200 hover:bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 peer-checked:shadow-lg peer-checked:shadow-indigo-100/50 transition-all duration-300">
                                        <div class="text-[11px] font-black text-slate-500 group-hover:text-indigo-600 peer-checked:text-indigo-700 uppercase tracking-widest">{{ $specialty }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div id="doctors_section" class="space-y-6 hidden animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="flex items-center justify-between px-2">
                                <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Médecins disponibles</h3>
                                <span id="doctors_count" class="text-[10px] bg-slate-900 text-white px-3 py-1 rounded-full font-black uppercase tracking-tighter shadow-sm">0 trouvés</span>
                            </div>
                            <div id="doctors_list" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- JS Populated -->
                            </div>
                        </div>

                        <div class="flex justify-between pt-6">
                            <button type="button" onclick="goToStep(1)" class="px-8 py-5 text-slate-400 font-bold hover:text-slate-900 transition-colors flex items-center">
                                <i class="fas fa-arrow-left mr-3"></i> Retour
                            </button>
                            <button type="button" id="btn_step2_next" onclick="goToStep(3)" disabled class="group px-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-xs opacity-50 cursor-not-allowed transition-all duration-300 shadow-2xl flex items-center active:scale-95">
                                Finaliser la demande
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: DÉTAILS & CONFIRMATION (HOME ONLY) -->
                <div id="step-3-content" class="step-panel hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-10 space-y-10">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 text-xl shadow-sm border border-purple-100/50">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 leading-tight">Confirmation</h2>
                                <p class="text-slate-400 font-medium text-sm">Prévoyez le moment idéal pour votre soin</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-2">
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Date souhaitée</label>
                                <input type="date" name="appointment_date" id="appointment_date_home" {{ $consultationType === 'home' ? 'required' : '' }} min="{{ date('Y-m-d') }}" 
                                    class="w-full py-5 px-6 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none font-bold text-slate-700">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Heure souhaitée</label>
                                <input type="time" name="appointment_time" id="appointment_time_home" {{ $consultationType === 'home' ? 'required' : '' }} 
                                    class="w-full py-5 px-6 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none font-bold text-slate-700">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Motif du rendez-vous</label>
                            <textarea name="reason" id="reason_home" {{ $consultationType === 'home' ? 'required' : '' }} rows="4" 
                                class="w-full py-5 px-6 bg-slate-50 border border-slate-200/60 rounded-[1.5rem] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 outline-none font-bold text-slate-700 placeholder:text-slate-300" 
                                placeholder="Décrivez brièvement votre besoin médical..."></textarea>
                        </div>

                        <div class="flex justify-between pt-6">
                            <button type="button" onclick="goToStep(2)" class="px-8 py-5 text-slate-400 font-bold hover:text-slate-900 transition-colors flex items-center">
                                <i class="fas fa-arrow-left mr-3"></i> Retour
                            </button>
                            <button type="submit" class="group px-10 py-5 bg-emerald-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest text-xs hover:bg-emerald-700 transition-all duration-300 shadow-2xl shadow-emerald-200 flex items-center active:scale-95">
                                <i class="fas fa-check-circle mr-3"></i> Confirmer le rendez-vous
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <!-- CLASSIC HOSPITAL FORM (HOSPITAL ONLY) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                        <h2 class="text-xl font-bold mb-8 text-slate-800 border-b pb-4">Informations du rendez-vous</h2>
                        
                        <div class="space-y-6">
                            <!-- Établissement -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700">Établissement <span class="text-red-500">*</span></label>
                                <select name="hospital_id" id="hospital_id" {{ $consultationType === 'hospital' ? 'required' : '' }} onchange="updateServicesByHospital(this.value)"
                                    class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none appearance-none font-medium">
                                    <option value="">Choisir un établissement</option>
                                    @foreach($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Service -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-700">Service <span class="text-red-500">*</span></label>
                                    <select name="service_id" id="service_id" {{ $consultationType === 'hospital' ? 'required' : '' }} onchange="updatePrestationsByHospital(this.value)"
                                        class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none appearance-none font-medium">
                                        <option value="">Choisir d'abord un établissement</option>
                                    </select>
                                </div>
                                <!-- Prestation -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-700">Prestation (Optionnel)</label>
                                    <select name="prestation_id" id="prestation_id" onchange="calculatePricesHospital()"
                                        class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none appearance-none font-medium">
                                        <option value="">Choisir d'abord un service</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Specialty Selection (Internal Doctors) -->
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-slate-700">Spécialité du médecin souhaité (Optionnel)</label>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($specialties as $specialty)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="specialty_choice_hospital" value="{{ $specialty }}" onchange="fetchDoctorsBySpecialtyHospital(this.value)" class="peer sr-only">
                                        <div class="p-3 text-center border-2 border-slate-100 bg-slate-50/50 rounded-xl hover:border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-300">
                                            <div class="text-[10px] font-bold text-slate-500 peer-checked:text-blue-700 uppercase tracking-wider">{{ $specialty }}</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div id="doctors_section_hospital" class="space-y-4 hidden">
                                <div class="flex items-center justify-between px-2">
                                    <h3 class="font-bold text-slate-800 text-sm">Médecins spécialisés disponibles</h3>
                                    <span id="doctors_count_hospital" class="text-[10px] bg-blue-600 text-white px-3 py-1 rounded-full font-bold">0 trouvés</span>
                                </div>
                                <div id="doctors_list_hospital" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- JS Populated -->
                                </div>
                                <input type="hidden" name="doctor_id" id="doctor_id_hospital">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Date -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-700">Date souhaitée <span class="text-red-500">*</span></label>
                                    <input type="date" name="appointment_date" id="appointment_date" {{ $consultationType === 'hospital' ? 'required' : '' }} min="{{ date('Y-m-d') }}" onchange="updateHospitalSummary()"
                                        class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                                </div>
                                <!-- Heure -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-slate-700">Heure souhaitée <span class="text-red-500">*</span></label>
                                    <input type="time" name="appointment_time" id="appointment_time" {{ $consultationType === 'hospital' ? 'required' : '' }} onchange="updateHospitalSummary()"
                                        class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                                </div>
                            </div>

                            <!-- Motif -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700">Motif de consultation <span class="text-red-500">*</span></label>
                                <textarea name="reason" id="reason" {{ $consultationType === 'hospital' ? 'required' : '' }} rows="4" oninput="updateHospitalSummary()"
                                    class="w-full py-4 px-6 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium" 
                                    placeholder="Ex: Douleurs abdominales, Fièvre, Contrôle annuel..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bloc RÉSUMÉ DE VOTRE DEMANDE -->
                    <div class="bg-blue-50 rounded-2xl border border-blue-100 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-800 mb-2">Résumé de votre demande</h3>
                        <p class="text-slate-600 text-sm italic mb-4" id="hospital_summary_text">Veuillez remplir le formulaire...</p>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Envoyer ma demande
                            </button>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Sidebar: Recap & Invoice -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
                        <div class="bg-slate-900 p-6 text-white text-center">
                            <h3 class="text-lg font-bold">Récapitulatif</h3>
                            <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Détails de la prestation</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Selected Doctor Mini Card -->
                            <div id="selected_doctor_preview" class="hidden animate-in slide-in-from-top fade-in duration-300">
                                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-2xl border border-blue-100">
                                    <img src="" id="mini_doctor_photo" class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-sm">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900" id="mini_doctor_name">Dr. ...</p>
                                        <p class="text-[10px] text-blue-600 font-bold uppercase" id="mini_doctor_specialty">...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Simplified Summary -->
                            <div id="booking_initial_summary" class="space-y-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-sm text-slate-500 italic">Veuillez remplir le formulaire pour voir le récapitulatif final sur la page de confirmation.</p>
                                <div class="flex items-center space-x-2 text-blue-600">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Paiement après consultation</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-orange-50 rounded-2xl border border-orange-100 flex items-start space-x-3 text-orange-800">
                        <i class="fas fa-info-circle mt-1"></i>
                        <p class="text-xs leading-relaxed">
                            Le paiement s'effectue après la consultation via Orange Money, Wave ou MTN Money.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Le script a été conservé mais les appels à showFullInvoice devront être supprimés ou adaptés si besoin -->
    <script>
        // CONFIG & STATE
        const hospitalsData = @json($hospitalsData);
        const selectedType = '{{ $consultationType }}';
        let map, marker;
        let doctorMarkers = L.layerGroup();
        let doctorsData = [];
        let currentStep = 1;
        
        // Detailed Pricing State
        let pricing = {
            consultation: 0,
            travel: 0,
            tax: 0,
            total: 0,
            distance: 0
        };

        const doctorIcon = L.icon({
            iconUrl: '/assets/img/doctor-marker.svg',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        // INIT
        document.addEventListener('DOMContentLoaded', function() {
            if (selectedType === 'home') {
                initMap();
                initGeolocation();
            }

            // Listen for specialty change
            document.querySelectorAll('input[name="specialty_choice"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    fetchDoctorsBySpecialty(this.value);
                });
            });
        });

        function initMap() {
            const defaultPos = [5.3484, -4.0305]; // Abidjan
            map = L.map('map', {zoomControl: false}).setView(defaultPos, 13);
            L.control.zoom({ position: 'topright' }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            marker = L.marker(defaultPos, {draggable: true, zIndexOffset: 1000}).addTo(map);
            doctorMarkers.addTo(map);
            
            marker.on('dragend', function() {
                updateCoordinates(marker.getLatLng());
            });
            
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng);
            });
        }

        function initGeolocation() {
            document.getElementById('btn_geocode').addEventListener('click', geocodeAddress);
            document.getElementById('btn_mylocation').addEventListener('click', () => {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(position => {
                        const pos = {lat: position.coords.latitude, lng: position.coords.longitude};
                        map.setView(pos, 16);
                        marker.setLatLng(pos);
                        updateCoordinates(pos);
                    }, err => {
                        alert("Impossible de récupérer votre position : " + err.message);
                    });
                }
            });
        }

        async function geocodeAddress() {
            const address = document.getElementById('home_address').value;
            if (!address) return;

            const btn = document.getElementById('btn_geocode');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch('{{ route("patient.ajax.calculate-home-fees") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ address: address, medecin_externe_id: document.getElementById('medecin_externe_id').value })
                });

                const data = await response.json();
                if (data.patient_geo) {
                    const pos = [data.patient_geo.latitude, data.patient_geo.longitude];
                    map.setView(pos, 16);
                    marker.setLatLng(pos);
                    updateCoordinates({lat: pos[0], lng: pos[1]});
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            } finally {
                btn.innerHTML = '<i class="fas fa-search"></i>';
            }
        }

        async function fetchDoctorsBySpecialty(specialty) {
            const doctorsList = document.getElementById('doctors_list');
            const doctorsSection = document.getElementById('doctors_section');
            const doctorsCount = document.getElementById('doctors_count');
            
            if (!doctorsList) return;
            
            doctorsSection.classList.remove('hidden');
            doctorsList.innerHTML = '<div class="col-span-full py-10 text-center"><i class="fas fa-spinner fa-spin text-3xl text-indigo-500 mb-3"></i><p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Recherche des médecins...</p></div>';
            
            try {
                const response = await fetch(`{{ url('/portal/ajax/doctors-by-specialty') }}/${encodeURIComponent(specialty)}`);
                const doctors = await response.json();
                
                doctorsList.innerHTML = '';
                doctorsCount.innerText = `${doctors.length} trouvés`;
                
                if (doctors.length === 0) {
                    doctorsList.innerHTML = '<div class="col-span-full py-8 text-center bg-slate-50 rounded-2xl border border-slate-100"><p class="text-slate-400 font-bold text-xs">Aucun médecin disponible pour cette spécialité.</p></div>';
                    return;
                }
                
                doctors.forEach(doc => {
                    const card = document.createElement('div');
                    card.className = 'doctor-card relative group cursor-pointer';
                    card.onclick = () => selectDoctor(doc.id, doc.full_name);
                    card.innerHTML = `
                        <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl group-hover:border-indigo-500 transition-all duration-300 flex items-center space-x-4">
                            <img src="${doc.photo}" class="w-12 h-12 rounded-xl object-cover" alt="${doc.full_name}">
                            <div class="flex-1">
                                <h4 class="font-black text-slate-900 text-sm leading-tight">${doc.full_name}</h4>
                                <p class="text-indigo-600 font-black text-[10px] uppercase tracking-tighter mt-1">${Math.round(doc.consultation_price).toLocaleString()} FCFA</p>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                        </div>
                    `;
                    doctorsList.appendChild(card);
                });
            } catch (error) {
                console.error('Fetch doctors error:', error);
                doctorsList.innerHTML = '<div class="col-span-full py-8 text-center text-red-500 font-bold text-xs">Erreur lors de la récupération des médecins.</div>';
            }
        }

        function selectDoctor(id, name) {
            document.getElementById('medecin_externe_id').value = id;
            
            // UI Update
            document.querySelectorAll('#doctors_list .doctor-card').forEach(card => {
                card.querySelector('div').classList.remove('border-indigo-500', 'bg-indigo-50/30');
                card.querySelector('.fa-check').parentElement.classList.replace('bg-indigo-500', 'bg-slate-50');
                card.querySelector('.fa-check').parentElement.classList.replace('text-white', 'text-slate-300');
            });
            
            const selectedCard = event.currentTarget.querySelector('div');
            selectedCard.classList.add('border-indigo-500', 'bg-indigo-50/30');
            const checkIcon = event.currentTarget.querySelector('.fa-check').parentElement;
            checkIcon.classList.replace('bg-slate-50', 'bg-indigo-500');
            checkIcon.classList.replace('text-slate-300', 'text-white');
            
            document.getElementById('btn_step2_next').disabled = false;
            document.getElementById('btn_step2_next').classList.remove('opacity-50', 'cursor-not-allowed');
            
            refreshFees();
        }

        async function fetchDoctorsBySpecialtyHospital(specialty) {
            const doctorsList = document.getElementById('doctors_list_hospital');
            const doctorsSection = document.getElementById('doctors_section_hospital');
            const doctorsCount = document.getElementById('doctors_count_hospital');
            
            if (!doctorsList) return;
            
            doctorsSection.classList.remove('hidden');
            doctorsList.innerHTML = '<div class="col-span-full py-6 text-center"><i class="fas fa-spinner fa-spin text-xl text-blue-500 mr-2"></i><span class="text-slate-400 text-xs font-bold">Chargement...</span></div>';
            
            try {
                const response = await fetch(`{{ url('/portal/ajax/internal-doctors-by-specialty') }}/${encodeURIComponent(specialty)}`);
                const doctors = await response.json();
                
                doctorsList.innerHTML = '';
                doctorsCount.innerText = `${doctors.length} trouvés`;
                
                if (doctors.length === 0) {
                    doctorsList.innerHTML = '<div class="col-span-full py-4 text-center bg-slate-50 rounded-xl"><p class="text-slate-400 text-xs">Aucun médecin spécialisé trouvé.</p></div>';
                    return;
                }
                
                doctors.forEach(doc => {
                    const card = document.createElement('div');
                    card.className = 'doctor-card-hospital relative group cursor-pointer';
                    card.onclick = (e) => selectDoctorHospital(doc.id, doc.full_name, e);
                    card.innerHTML = `
                        <div class="p-4 bg-white border border-slate-200 rounded-xl group-hover:border-blue-500 transition-all flex items-center space-x-3">
                            <img src="${doc.photo}" class="w-10 h-10 rounded-lg object-cover" alt="${doc.full_name}">
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 text-sm leading-tight">${doc.full_name}</h4>
                                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">${doc.specialty}</p>
                            </div>
                            <div class="check-circle w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center text-transparent group-hover:border-blue-500 transition-all">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                        </div>
                    `;
                    doctorsList.appendChild(card);
                });
            } catch (error) {
                console.error('Fetch hospital doctors error:', error);
                doctorsList.innerHTML = '<div class="col-span-full py-4 text-center text-red-500 text-xs">Erreur de chargement.</div>';
            }
        }

        function selectDoctorHospital(id, name, event) {
            document.getElementById('doctor_id_hospital').value = id;
            
            // UI Update
            document.querySelectorAll('#doctors_list_hospital .doctor-card-hospital').forEach(card => {
                card.querySelector('div').classList.remove('border-blue-500', 'bg-blue-50/30');
                const check = card.querySelector('.check-circle');
                check.classList.remove('bg-blue-500', 'border-blue-500', 'text-white');
                check.classList.add('border-slate-200', 'text-transparent');
            });
            
            const selectedCard = event.currentTarget.querySelector('div');
            selectedCard.classList.add('border-blue-500', 'bg-blue-50/30');
            const check = event.currentTarget.querySelector('.check-circle');
            check.classList.remove('border-slate-200', 'text-transparent');
            check.classList.add('bg-blue-500', 'border-blue-500', 'text-white');
            
            updateHospitalSummary();
        }

        function updateCoordinates(latlng) {
            document.getElementById('patient_lat').value = latlng.lat;
            document.getElementById('patient_lon').value = latlng.lng;
            
            // Reverse Geocoding via Nominatim
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('home_address').value = data.display_name;
                    }
                    refreshFees();
                })
                .catch(err => {
                    console.error('Reverse geocoding error:', err);
                    refreshFees();
                });
        }

        // STEP NAVIGATION
        function goToStep(step) {
            if (step === 2 && !document.getElementById('patient_lat').value) {
                alert('Veuillez d\'abord valider votre adresse.');
                return;
            }

            // Transition
            document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById(`step-${step}-content`).classList.remove('hidden');
            
            // Update UI
            document.querySelectorAll('.step-btn').forEach(b => {
                const bStep = parseInt(b.dataset.step);
                b.classList.remove('step-active', 'text-slate-400');
                if (bStep === step) b.classList.add('step-active');
                else if (bStep < step) b.classList.add('text-slate-900');
                else b.classList.add('text-slate-400');
            });

            currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            if (step === 1 && map) {
                setTimeout(() => map.invalidateSize(), 100);
            }
        }

        // DOCTORS FETCH
        async function fetchDoctorsBySpecialty(specialty) {
            const list = document.getElementById('doctors_list');
            list.innerHTML = '<div class="col-span-2 py-8 text-center"><i class="fas fa-circle-notch fa-spin text-2xl text-blue-500 mb-2"></i><p class="text-sm font-bold text-slate-500">Recherche des médecins à proximité...</p></div>';
            document.getElementById('doctors_section').classList.remove('hidden');
            
            try {
                const response = await fetch(`{{ url('/portal/ajax/doctors-by-specialty') }}/${encodeURIComponent(specialty)}`);
                doctorsData = await response.json();
                
                document.getElementById('doctors_count').innerText = `${doctorsData.length} trouvés`;
                renderDoctors();
                updateMapMarkers();
            } catch (error) {
                list.innerHTML = '<p class="text-red-500 text-sm">Erreur lors de la recherche.</p>';
            }
        }

        function renderDoctors() {
            const list = document.getElementById('doctors_list');
            list.innerHTML = '';

            if (doctorsData.length === 0) {
                list.innerHTML = '<div class="col-span-2 py-6 bg-slate-50 rounded-2xl text-center"><p class="text-slate-500 text-sm italic">Aucun médecin spécialisé n\'est disponible en ligne pour cette zone.</p></div>';
                return;
            }

            doctorsData.forEach(doc => {
                const card = document.createElement('div');
                card.className = 'doctor-card flex items-center p-4 bg-white border border-slate-100 rounded-3xl ' + (document.getElementById('medecin_externe_id').value == doc.id ? 'selected' : '');
                card.onclick = () => selectDoctor(doc);
                card.innerHTML = `
                    <div class="relative">
                        <img src="${doc.photo}" class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-sm">
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="font-bold text-slate-900">${doc.full_name}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-[10px] font-black text-blue-600 uppercase bg-blue-50 px-2 py-0.5 rounded">Consultation: ${doc.consultation_price.toLocaleString()} F</span>
                        </div>

                    </div>

                    <div class="text-blue-600 ` + (document.getElementById('medecin_externe_id').value == doc.id ? 'opacity-100' : 'opacity-0') + `"><i class="fas fa-check-circle text-xl"></i></div>

                `;
                list.appendChild(card);
            });
        }

        function updateMapMarkers() {
            doctorMarkers.clearLayers();
            doctorsData.forEach(doc => {
                if (doc.latitude && doc.longitude) {
                    const m = L.marker([doc.latitude, doc.longitude], {icon: doctorIcon});
                    m.bindPopup(`<div class="font-bold text-sm">${doc.full_name}</div><div class="text-xs text-blue-600">${doc.consultation_price} FCFA</div>`);
                    doctorMarkers.addLayer(m);
                }
            });
            
            // Adjust map view to show patient and doctors
            if (doctorsData.length > 0) {
                const group = new L.featureGroup([marker, ...doctorMarkers.getLayers()]);
                map.fitBounds(group.getBounds().pad(0.2));
            }
        }

        function selectDoctor(doc) {
            document.getElementById('medecin_externe_id').value = doc.id;
            
            // Update UI Cards
            document.querySelectorAll('.doctor-card').forEach(c => c.classList.remove('selected'));
            renderDoctors(); // Re-render to show selection (simple approach)
            
            // Show preview
            document.getElementById('selected_doctor_preview').classList.remove('hidden');
            document.getElementById('mini_doctor_photo').src = doc.photo;
            document.getElementById('mini_doctor_name').innerText = doc.full_name;
            document.getElementById('mini_doctor_specialty').innerText = document.querySelector('input[name="specialty_choice"]:checked').value;
            
            // Enable Next
            const btn = document.getElementById('btn_step2_next');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            refreshFees();
        }

        // PRICING LOGIC
        async function refreshFees() {
            if (selectedType !== 'home') return;
            const docId = document.getElementById('medecin_externe_id').value;
            if (!docId || !document.getElementById('patient_lat').value) return;

            try {
                const response = await fetch('{{ route("patient.ajax.calculate-home-fees") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        address: document.getElementById('home_address').value,
                        medecin_externe_id: docId,
                        latitude: document.getElementById('patient_lat').value,
                        longitude: document.getElementById('patient_lon').value
                    })
                });

                const data = await response.json();
                if (!data.error) {
                    pricing = {
                        consultation: data.consultation_price,
                        travel: data.fees.total_travel_fee,
                        tax: data.tax_amount,
                        total: data.total_amount,
                        distance: data.fees.distance_km
                    };
                    updateRecapUI();
                }
            } catch (error) {
                console.error('Price refresh error:', error);
            }
        }

        function updateRecapUI() {
            const recapBase = document.getElementById('recap_base');
            const recapTravel = document.getElementById('recap_travel');
            const recapTax = document.getElementById('recap_tax');
            const recapTotal = document.getElementById('recap_total');

            if (recapBase) recapBase.innerText = Math.round(pricing.consultation).toLocaleString() + ' FCFA';
            if (recapTravel) recapTravel.innerText = Math.round(pricing.travel).toLocaleString() + ' FCFA';
            if (document.getElementById('recap_distance')) document.getElementById('recap_distance').innerText = pricing.distance > 0 ? `(${pricing.distance.toFixed(1)} km)` : '';
            if (recapTax) recapTax.innerText = Math.round(pricing.tax).toLocaleString() + ' FCFA';
            if (recapTotal) recapTotal.innerText = Math.round(pricing.total).toLocaleString() + ' FCFA';
            
            // Hidden fields
            if(document.getElementById('calculated_distance')) document.getElementById('calculated_distance').value = pricing.distance;
            if(document.getElementById('calculated_travel_fee')) document.getElementById('calculated_travel_fee').value = pricing.travel;
            if(document.getElementById('tax_amount')) document.getElementById('tax_amount').value = pricing.tax;
            if(document.getElementById('total_amount')) document.getElementById('total_amount').value = pricing.total;

            // Modal Update
            if(document.getElementById('service_id')) {
                const sSelect = document.getElementById('service_id');
                if(document.getElementById('modal_service')) document.getElementById('modal_service').innerText = sSelect.options[sSelect.selectedIndex]?.text || '';
            }
            if(document.getElementById('modal_base')) document.getElementById('modal_base').innerText = Math.round(pricing.consultation).toLocaleString() + ' FCFA';
            if(document.getElementById('modal_distance')) document.getElementById('modal_distance').innerText = pricing.distance.toFixed(1);
            if(document.getElementById('modal_travel')) document.getElementById('modal_travel').innerText = Math.round(pricing.travel).toLocaleString() + ' FCFA';
            if(document.getElementById('modal_subtotal')) document.getElementById('modal_subtotal').innerText = Math.round(pricing.consultation + pricing.travel).toLocaleString() + ' FCFA';
            if(document.getElementById('modal_tax')) document.getElementById('modal_tax').innerText = Math.round(pricing.tax).toLocaleString() + ' FCFA';
            if(document.getElementById('modal_total')) document.getElementById('modal_total').innerText = Math.round(pricing.total).toLocaleString() + ' FCFA';
        }

        function updatePrestations(serviceId) {
            // Determine which ID to use based on mode
            let selectId = 'prestation_id';
            let hospitalInputId = 'hospital_id'; // Default to select (Hospital Mode)
            
            if (selectedType === 'home') {
                selectId = 'prestation_id_home';
                hospitalInputId = 'hidden_hospital_id'; // Use hidden input (Home Mode)
            }

            const prestSelect = document.getElementById(selectId);
            if (!prestSelect) return;
            prestSelect.innerHTML = '<option value="">-- Choisir une prestation --</option>';
            
            if (!serviceId) return;

            const hInput = document.getElementById(hospitalInputId);
            const hospitalId = hInput ? hInput.value : null;
            if (!hospitalId || !hospitalsData[hospitalId]) return;

            const prestations = hospitalsData[hospitalId].prestations.filter(p => String(p.service_id) === String(serviceId));
            
            prestations.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                opt.setAttribute('data-price', p.price);
                prestSelect.appendChild(opt);
            });
            
            calculatePrices();
        }

        function calculatePrices() {
            const prestSelect = document.getElementById(selectedType === 'home' ? 'prestation_id_home' : 'prestation_id');
            if (!prestSelect) return;
            const selectedOpt = prestSelect.options[prestSelect.selectedIndex];
            
            if (selectedOpt && selectedOpt.value) {
                pricing.consultation = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
            } else {
                pricing.consultation = 0;
            }

            // Recalculate everything
            const subtotal = pricing.consultation + pricing.travel;
            pricing.tax = subtotal * 0.18;
            pricing.total = subtotal + pricing.tax;
            
            updateRecapUI();
        }

        // HOSPITAL SPECIFIC JS
        function updateServicesByHospital(hospitalId) {
            const serviceSelect = document.getElementById('service_id');
            const prestSelect = document.getElementById('prestation_id');
            if (!serviceSelect || !prestSelect) return;
            
            serviceSelect.innerHTML = '<option value="">Chargement...</option>';
            prestSelect.innerHTML = '<option value="">Choisir d\'abord un service</option>';
            
            if (!hospitalId || !hospitalsData[hospitalId]) {
                serviceSelect.innerHTML = '<option value="">Choisir d\'abord un établissement</option>';
                return;
            }

            const data = hospitalsData[hospitalId];
            serviceSelect.innerHTML = '<option value="">Choisir un service</option>';
            data.services.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                opt.setAttribute('data-price', s.price);
                serviceSelect.appendChild(opt);
            });
        }

        function updatePrestationsByHospital(serviceId) {
            updatePrestations(serviceId);
            updateHospitalSummary();
        }

        function calculatePricesHospital() {
            calculatePrices();
            updateHospitalSummary();
        }

        function updateHospitalSummary() {
            const hSelect = document.getElementById('hospital_id');
            const sSelect = document.getElementById('service_id');
            const pSelect = document.getElementById('prestation_id');
            const dateInput = document.getElementById('appointment_date');
            const timeInput = document.getElementById('appointment_time');
            const reasonInput = document.getElementById('reason');

            if(!hSelect || !sSelect || !pSelect) return;

            const hospital = hSelect.options[hSelect.selectedIndex]?.text;
            const service = sSelect.options[sSelect.selectedIndex]?.text;
            const prestation = pSelect.options[pSelect.selectedIndex]?.text;
            const date = dateInput ? dateInput.value.split('-').reverse().join('/') : null;
            const time = timeInput ? timeInput.value : null;

            let text = "";
            let filled = false;

            if (hospital && hospital !== "Choisir un établissement") {
                text += `<span class="block mb-1"><i class="fas fa-hospital mr-2"></i> ${hospital}</span>`;
                filled = true;
            }
            if (service && service !== "Choisir un service" && service !== "Choisir d'abord un établissement") {
                text += `<span class="block mb-1 text-slate-800"><i class="fas fa-stethoscope mr-2"></i> Service: <b>${service}</b></span>`;
                filled = true;
            }
            if (prestation && prestation !== "Choisir une prestation" && prestation !== "Choisir d'abord un service") {
                text += `<span class="block mb-1"><i class="fas fa-notes-medical mr-2"></i> ${prestation}</span>`;
                filled = true;
            }
            
            if (date && time) {
                text += `<span class="block mt-2 font-bold text-blue-700"><i class="fas fa-clock mr-2"></i> Le ${date} à ${time}</span>`;
                filled = true;
            }

            if (reasonInput && reasonInput.value.trim().length > 0) {
                 text += `<span class="block mt-2 italic text-slate-500 border-l-2 border-blue-200 pl-2 text-xs">" ${reasonInput.value.substring(0, 50)}${reasonInput.value.length > 50 ? '...' : ''} "</span>`;
                 filled = true;
            }

            const summaryText = document.getElementById('hospital_summary_text');
            if(summaryText) {
                if (filled) {
                    summaryText.innerHTML = text;
                    summaryText.classList.remove('italic');
                } else {
                     summaryText.innerText = "Veuillez remplir le formulaire pour voir le résumé...";
                     summaryText.classList.add('italic');
                }
            }
        }

        function showFullInvoice() {
            document.getElementById('invoice_modal').classList.remove('hidden');
        }
        function hideFullInvoice() {
            document.getElementById('invoice_modal').classList.add('hidden');
        }

        function resetForm() {
            if (confirm('Voulez-vous vraiment recommencer ?')) location.reload();
        }

        // INITIALIZATION
        document.addEventListener('DOMContentLoaded', () => {
            const hSelect = document.getElementById('hospital_id');
            if (hSelect && hSelect.value && hSelect.tagName === 'SELECT') {
                updateServicesByHospital(hSelect.value);
            }
        });
    </script>
</body>
</html>

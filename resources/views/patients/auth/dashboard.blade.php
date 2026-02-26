<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Santé - {{ $patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
        .nav-link.active { color: #2563eb; border-bottom-color: #2563eb; background-color: #eff6ff; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">
    
    <!-- Navbar Premium -->
    <nav class="glass sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- User Profile Summary -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-200">
                            {{ substr($patient->first_name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-slate-900 tracking-tight">{{ $patient->full_name }}</h1>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">IPU: {{ $patient->ipu }}</p>
                    </div>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    <button class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm relative group">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                    
                    <form method="POST" action="{{ route('patient.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-power-off"></i>
                            <span class="hidden md:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Navigation Links -->
            <div class="flex space-x-1 overflow-x-auto scrollbar-hide pb-1">
                <a href="{{ route('patient.dashboard') }}" class="nav-link active flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-th-large mr-2.5"></i>Tableau de bord
                </a>
                <a href="{{ route('patient.appointments') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-calendar-check mr-2.5"></i>Rendez-vous
                </a>
                <a href="{{ route('patient.medical-history') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-file-medical-alt mr-2.5"></i>Dossier Médical
                </a>
                <a href="{{ route('patient.prescriptions') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-pills mr-2.5"></i>Ordonnances
                </a>
                <a href="{{ route('patient.documents') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-folder-open mr-2.5"></i>Documents
                </a>
                <a href="{{ route('patient.profile') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-user-circle mr-2.5"></i>Profil
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Welcome Message -->
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-black text-slate-900">Bonjour, {{ $patient->first_name }} 👋</h2>
                <p class="text-slate-500 font-medium mt-1">Voici le résumé de votre santé aujourd'hui.</p>
            </div>
            <a href="{{ route('patient.book-appointment') }}" class="hidden md:flex items-center px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:-translate-y-1">
                <i class="fas fa-plus-circle mr-2"></i> Nouveau Rendez-vous
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
                <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Age Card -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center p-6 transition hover:shadow-md">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Âge</p>
                    <p class="text-2xl font-black text-slate-900">{{ $patient->age }} ans</p>
                </div>
            </div>

            <!-- Blood Group Card -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center p-6 transition hover:shadow-md">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-tint"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Groupe Sanguin</p>
                    <p class="text-2xl font-black text-slate-900">{{ $patient->blood_group ?? '--' }}</p>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center p-6 transition hover:shadow-md">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Rendez-vous</p>
                    <p class="text-2xl font-black text-slate-900">{{ $totalAppointments }}</p>
                </div>
            </div>

            <!-- Prescriptions Card -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center p-6 transition hover:shadow-md">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-pills"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Ordonnances</p>
                    <p class="text-2xl font-black text-slate-900">{{ $totalPrescriptions }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content (2/3) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Upcoming Appointments -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Prochains Rendez-vous</h3>
                            <p class="text-slate-400 text-sm font-medium">Vos consultations à venir</p>
                        </div>
                        <a href="{{ route('patient.appointments') }}" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @forelse($upcomingAppointments as $appointment)
                            <div class="group relative flex items-center p-4 bg-slate-50 rounded-3xl border border-slate-100 hover:border-indigo-100 hover:bg-white transition-all duration-300 card-hover">
                                <!-- Date Badge -->
                                <div class="flex flex-col items-center justify-center bg-white rounded-2xl w-16 h-16 shadow-sm border border-slate-100 mr-5 group-hover:border-indigo-100 group-hover:bg-indigo-50 transition-colors">
                                    <span class="text-[10px] font-black uppercase text-slate-400 group-hover:text-indigo-400">{{ $appointment->appointment_datetime->translatedFormat('M') }}</span>
                                    <span class="text-xl font-black text-slate-900 group-hover:text-indigo-600">{{ $appointment->appointment_datetime->format('d') }}</span>
                                </div>

                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-slate-900 group-hover:text-indigo-700 transition-colors">{{ $appointment->doctor->name ?? 'Médecin' }}</h4>
                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-1">{{ $appointment->service->name ?? 'Service' }}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            {{ $appointment->status }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-xs text-slate-400 font-medium">
                                        <i class="far fa-clock mr-1.5"></i> {{ $appointment->appointment_datetime->format('H:i') }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-map-marker-alt mr-1.5"></i> {{ $appointment->consultation_type === 'home' ? 'À domicile' : 'En cabinet' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-3xl">
                                    <i class="far fa-calendar"></i>
                                </div>
                                <h4 class="text-slate-900 font-bold">Aucun rendez-vous prévu</h4>
                                <p class="text-slate-400 text-sm mt-1">Planifiez votre prochaine consultation dès maintenant.</p>
                                <a href="{{ route('patient.book-appointment') }}" class="inline-block mt-4 px-6 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-black transition">
                                    <i class="fas fa-plus mr-2"></i> Prendre RDV
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Medical Records -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Historique Médical</h3>
                            <p class="text-slate-400 text-sm font-medium">Vos derniers examens</p>
                        </div>
                        <a href="{{ route('patient.medical-history') }}" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="p-6">
                         @forelse($recentRecords as $record)
                            <div class="flex items-center py-4 border-b border-slate-50 last:border-0 hover:bg-slate-50/50 rounded-2xl px-4 transition -mx-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fas fa-file-medical-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-slate-900 text-sm">{{ Str::limit($record->diagnosis ?? 'Consultation', 50) }}</h5>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $record->created_at->translatedFormat('d F Y') }} • Dr. {{ $record->doctor->name ?? 'N/A' }}</p>
                                </div>
                                <a href="#" class="text-xs font-bold text-indigo-600 hover:underline">Voir</a>
                            </div>
                        @empty
                            <p class="text-center text-slate-400 text-sm italic py-6">Aucun dossier médical récent.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Sidebar (1/3) -->
            <div class="space-y-6">
                
                <!-- Quick Actions -->
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-slate-200">
                    <h3 class="text-lg font-black mb-6">Accès Rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('patient.book-appointment') }}" class="flex items-center p-4 bg-white/10 rounded-2xl hover:bg-white/20 transition backdrop-blur-sm border border-white/5">
                            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center mr-4 shadow-lg shadow-indigo-900/20">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="font-bold text-sm">Nouveau RDV</span>
                        </a>
                        <a href="{{ route('patient.prescriptions') }}" class="flex items-center p-4 bg-white/10 rounded-2xl hover:bg-white/20 transition backdrop-blur-sm border border-white/5">
                            <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center mr-4 shadow-lg shadow-purple-900/20">
                                <i class="fas fa-file-prescription"></i>
                            </div>
                            <span class="font-bold text-sm">Mes Ordonnances</span>
                        </a>
                        <a href="{{ route('patient.documents') }}" class="flex items-center p-4 bg-white/10 rounded-2xl hover:bg-white/20 transition backdrop-blur-sm border border-white/5">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center mr-4 shadow-lg shadow-emerald-900/20">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <span class="font-bold text-sm">Mes Documents</span>
                        </a>
                    </div>
                </div>

                <!-- Referring Doctor -->
                @if($patient->referringDoctor)
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-6">Médecin Référent</h3>
                    <div class="flex items-center">
                        <img src="{{ $patient->referringDoctor->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($patient->referringDoctor->name).'&background=EFF6FF&color=4F46E5' }}" 
                            class="w-16 h-16 rounded-2xl object-cover mr-4 border-2 border-slate-100">
                        <div>
                            <p class="font-bold text-slate-900">Dr. {{ $patient->referringDoctor->name }}</p>
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mt-1">{{ $patient->referringDoctor->specialty ?? 'Généraliste' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <button class="flex-1 py-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                            <i class="fas fa-phone-alt mr-2"></i> Appeler
                        </button>
                        <button class="flex-1 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-xs hover:bg-indigo-100 transition">
                            <i class="fas fa-comment-alt mr-2"></i> Message
                        </button>
                    </div>
                </div>
                @endif
                
                <!-- Info Box -->
                <div class="bg-orange-50 rounded-[2.5rem] p-8 border border-orange-100">
                    <div class="flex items-start">
                        <i class="fas fa-heartbeat text-orange-500 text-xl mt-1 mr-4"></i>
                        <div>
                            <h4 class="font-bold text-orange-900 text-sm">Urgence Médicale ?</h4>
                            <p class="text-xs font-medium text-orange-700/80 mt-2 leading-relaxed">
                                En cas d'urgence vitale, composez immédiatement le <strong>15</strong> ou le <strong>18</strong>. Ne pas utiliser cette application pour des urgences graves.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>
</body>
</html>
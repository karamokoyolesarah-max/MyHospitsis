<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - {{ $patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
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
                    <a href="{{ route('patient.dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight">Mon Profil</h1>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
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
                <a href="{{ route('patient.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
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
                <a href="{{ route('patient.profile') }}" class="nav-link active flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-user-circle mr-2.5"></i>Profil
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
                <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl animate-in fade-in slide-in-from-top-2">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-rose-500 mr-3 text-xl mt-1"></i>
                    <ul class="list-disc list-inside font-medium text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Sidebar / Identity Card (4 columns) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Main Identity Card -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-500 to-purple-600 opacity-10"></div>
                    
                    <div class="relative z-10">
                        <div class="w-32 h-32 mx-auto rounded-[2rem] bg-white p-1 mb-4 shadow-xl shadow-indigo-100">
                            <div class="w-full h-full rounded-[1.8rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-5xl">
                                {{ substr($patient->first_name, 0, 1) }}
                            </div>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 leading-tight">{{ $patient->full_name }}</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">IPU: {{ $patient->ipu }}</p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-birthday-cake mr-3 w-5 text-center"></i>
                                    <span class="text-sm font-bold">Âge</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $patient->age }} ans</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-venus-mars mr-3 w-5 text-center"></i>
                                    <span class="text-sm font-bold">Genre</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $patient->gender === 'M' ? 'Masculin' : ($patient->gender === 'F' ? 'Féminin' : 'Autre') }}</span>
                            </div>
                            @if($patient->blood_group)
                            <div class="flex items-center justify-between p-4 bg-rose-50 rounded-2xl border border-rose-100">
                                <div class="flex items-center text-rose-500">
                                    <i class="fas fa-tint mr-3 w-5 text-center"></i>
                                    <span class="text-sm font-bold">Groupe Sanguin</span>
                                </div>
                                <span class="text-sm font-black text-rose-900">{{ $patient->blood_group }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-calendar mr-3 w-5 text-center"></i>
                                    <span class="text-sm font-bold">Membre depuis</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $patient->created_at->format('Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="bg-emerald-50 rounded-[2.5rem] p-8 border border-emerald-100">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-4 shadow-sm flex-shrink-0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-emerald-900 text-sm">Données Protégées</h4>
                            <p class="text-xs font-medium text-emerald-700/80 mt-1 leading-relaxed">
                                Vos informations médicales sont chiffrées de bout en bout et stockées conformément aux normes HDS.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form (8 columns) -->
            <div class="lg:col-span-8">
                <form method="POST" action="{{ route('patient.profile.update') }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Personal Info Section -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xl">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900">Identité</h3>
                                <p class="text-slate-400 text-sm font-medium">Informations administratives</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Nom de famille</label>
                                <div class="relative">
                                    <input type="text" value="{{ $patient->name }}" disabled class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-500 cursor-not-allowed">
                                    <i class="fas fa-lock absolute right-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Prénoms</label>
                                <div class="relative">
                                    <input type="text" value="{{ $patient->first_name }}" disabled class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-500 cursor-not-allowed">
                                    <i class="fas fa-lock absolute right-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Date de naissance</label>
                                <input type="text" value="{{ $patient->dob->format('d/m/Y') }}" disabled class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-500 cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Numéro IPU</label>
                                <input type="text" value="{{ $patient->ipu }}" disabled class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-500 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Section Informations Médicales -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 text-xl">
                                <i class="fas fa-file-medical"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900">Médical</h3>
                                <p class="text-slate-400 text-sm font-medium">Informations de santé</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Groupe sanguin</label>
                                <select name="blood_group" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                    <option value="">Sélectionnez votre groupe sanguin</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}" {{ old('blood_group', $patient->blood_group) == $group ? 'selected' : '' }}>
                                            {{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Allergies</label>
                                <textarea name="allergies" rows="2" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none resize-none" placeholder="Ex: Pénicilline, Arachides...">{{ old('allergies', is_array($patient->allergies) ? implode(', ', $patient->allergies) : $patient->allergies) }}</textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Antécédents médicaux</label>
                                <textarea name="medical_history" rows="3" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none resize-none" placeholder="Vos antécédents médicaux ou chirurgicaux importants">{{ old('medical_history', $patient->medical_history) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details Section -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-xl">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900">Coordonnées</h3>
                                <p class="text-slate-400 text-sm font-medium">Pour vous contacter</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" required
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Téléphone</label>
                                    <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" required
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Adresse de résidence</label>
                                <textarea name="address" rows="2"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none resize-none">{{ old('address', $patient->address) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Ville</label>
                                    <input type="text" name="city" value="{{ old('city', $patient->city) }}"
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Code Postal</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code', $patient->postal_code) }}"
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 text-xl">
                                <i class="fas fa-heart-pulse"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900">Urgence</h3>
                                <p class="text-slate-400 text-sm font-medium">Personne à prévenir</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Nom complet</label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:bg-white transition-all outline-none" placeholder="Ex: Jean Kouassi (Père)">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Téléphone</label>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 focus:bg-white transition-all outline-none" placeholder="+225...">
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 text-xl">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900">Mot de passe</h3>
                                <p class="text-slate-400 text-sm font-medium">Sécurisez votre compte</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Nouveau mot de passe</label>
                                <input type="password" name="password" 
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none" placeholder="••••••••">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Confirmer</label>
                                <input type="password" name="password_confirmation" 
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all outline-none" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end pt-4 pb-12">
                        <button type="submit" class="px-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-bold shadow-xl shadow-slate-300 hover:bg-black hover:-translate-y-1 transition-all duration-300 flex items-center">
                            <i class="fas fa-save mr-3"></i> Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>

</body>
</html>
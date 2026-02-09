<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Patient - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-2 md:mr-3">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <h1 class="text-base md:text-xl font-bold text-gray-900 truncate">Espace Patient</h1>
                </div>
            </div>
            
            <div class="flex items-center space-x-2 md:space-x-4">
                <span class="hidden sm:inline text-sm text-gray-600 truncate max-w-[100px] md:max-w-none">{{ auth()->guard('patients')->user()->full_name }}</span>
                <form method="POST" action="{{ route('patient.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium">Déconnexion</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Bienvenue -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-6 md:p-8 text-white mb-8">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Bienvenue, {{ auth()->guard('patients')->user()->first_name }} !</h2>
            <p class="text-purple-100 flex items-center gap-2">
                <span class="opacity-70">IPU :</span>
                <span class="font-mono font-bold tracking-wider">{{ auth()->guard('patients')->user()->ipu }}</span>
            </p>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Prochains RDV</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingAppointments->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Documents</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $documents->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Mon Profil</p>
                        <p class="text-2xl font-bold text-gray-900">Actif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prochains Rendez-vous -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mes Prochains Rendez-vous</h3>
            </div>
            <div class="p-6">
                @forelse($upcomingAppointments as $appointment)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b last:border-b-0 gap-3">
                    <div class="space-y-1">
                        <p class="font-bold text-gray-900">{{ $appointment->appointment_datetime->format('d/m/Y à H:i') }}</p>
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                             <i class="fas fa-user-md text-xs"></i> Dr. {{ $appointment->doctor->name }} 
                             <span class="text-gray-300">•</span>
                             <span class="italic">{{ $appointment->service->name }}</span>
                        </p>
                    </div>
                    <div class="flex">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase tracking-wider">{{ ucfirst($appointment->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Aucun rendez-vous prévu</p>
                @endforelse
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('patient.appointments') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
                <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h4 class="font-semibold text-gray-900 mb-2">Mes Rendez-vous</h4>
                <p class="text-sm text-gray-500">Consulter et gérer mes RDV</p>
            </a>

            <a href="{{ route('patient.documents') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
                <svg class="w-12 h-12 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h4 class="font-semibold text-gray-900 mb-2">Mes Documents</h4>
                <p class="text-sm text-gray-500">Résultats et comptes-rendus</p>
            </a>

            <a href="{{ route('patient.profile') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
                <svg class="w-12 h-12 text-purple-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h4 class="font-semibold text-gray-900 mb-2">Mon Profil</h4>
                <p class="text-sm text-gray-500">Modifier mes informations</p>
            </a>
        </div>
    </main>
</body>
</html>
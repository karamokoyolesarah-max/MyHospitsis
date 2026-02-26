<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prendre un Rendez-vous</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Prendre un rendez-vous</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Choisissez votre type de consultation</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('patient.book-appointment.hospital') }}" 
                   class="consultation-type border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 cursor-pointer transition-all hover:shadow-lg group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-xl group-hover:bg-blue-200 transition">
                            <i class="fas fa-hospital text-blue-600 text-2xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-blue-600 opacity-0 group-hover:opacity-100 transition"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">À l'hôpital</h3>
                    <p class="text-sm text-gray-600">Rendez-vous dans nos locaux avec accès à tous les équipements.</p>
                </a>

                <a href="{{ route('patient.book-appointment.home') }}" 
                   class="consultation-type border-2 border-gray-200 rounded-xl p-6 hover:border-green-500 cursor-pointer transition-all hover:shadow-lg group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-xl group-hover:bg-green-200 transition">
                            <i class="fas fa-home text-green-600 text-2xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-green-600 opacity-0 group-hover:opacity-100 transition"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">À domicile</h3>
                    <p class="text-sm text-gray-600">Le médecin se déplace chez vous pour plus de confort.</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>

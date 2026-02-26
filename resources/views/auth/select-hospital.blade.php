<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection Hôpital - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    
    <x-navigation-buttons :back-url="route('select-portal')" />

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            {{-- En-tête --}}
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                    <i class="fas fa-hospital text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">HospitSIS</h1>
                <p class="text-gray-600 mt-2">Portail de santé sécurisé</p>
            </div>

            {{-- Carte du Formulaire --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Sélection de l'établissement</h2>
                    <p class="text-gray-600 mt-2">Saisissez le code ou le nom de votre établissement</p>
                </div>

                {{-- Formulaire de recherche --}}
                <form action="{{ route('hospital.select') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Champ de recherche --}}
                    <div>
                        <label for="hospital_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Établissement
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input
                                type="text"
                                id="hospital_search"
                                name="hospital_search"
                                required
                                placeholder="Ex: Saint Jean, HospisIS..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                autocomplete="off"
                            >
                        </div>
                        @error('hospital_search')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bouton de soumission --}}
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    >
                        <i class="fas fa-arrow-right mr-2"></i>
                        Continuer
                    </button>
                </form>

                {{-- Liste des hôpitaux disponibles --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Établissements disponibles :</p>
                    <div class="space-y-2">
                        @foreach($hospitals as $hospital)
                            <a href="{{ route('register', $hospital->slug) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    @if($hospital->logo)
                                        <img src="{{ asset('storage/' . $hospital->logo) }}" alt="{{ $hospital->name }}" class="w-8 h-8 rounded-lg mr-3 object-cover">
                                    @else
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-hospital text-blue-600 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $hospital->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $hospital->address }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                </div>
            </div>

            {{-- Pied de page --}}
            <div class="text-center text-sm text-gray-500">
                <p>© 2024 HospitSIS - YA CONSULTING</p>
                <p>Portail de santé sécurisé</p>
            </div>
        </div>
    </div>

    <script>
        function selectHospital(slug) {
            // Auto-fill the search field and submit
            document.getElementById('hospital_search').value = slug;
            document.querySelector('form').submit();
        }

        // Auto-focus on search field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('hospital_search').focus();
        });
    </script>
</body>
</html>

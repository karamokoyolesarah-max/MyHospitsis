<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Praticien - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    
    <x-navigation-buttons :back-url="route('hospital.login', $hospital->slug)" />

    <div class="max-w-md w-full py-8">
        {{-- En-tête (Logo & Titre) --}}
        <div class="text-center mb-8">
            @if($hospital->logo)
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-2xl mb-4 overflow-hidden">
                    <img src="{{ asset($hospital->logo) }}" alt="{{ $hospital->name }} Logo" class="w-full h-full object-cover">
                </div>
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            @endif
            <h1 class="text-3xl font-bold text-gray-900">{{ $hospital->name }}</h1>
            <p class="text-gray-600 mt-2">Création de compte professionnel</p>
        </div>

        {{-- Carte du Formulaire --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Inscription</h2>

            {{-- Gestion des Erreurs --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('register.submit', $hospital->slug) }}">
                @csrf

                <div class="space-y-4">
                    {{-- Nom complet --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Dr. Jean Dupont">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="votre@email.ci">
                    </div>

                    {{-- Matricule --}}
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">Numéro Matricule / Ordre</label>
                        <input id="registration_number" type="text" name="registration_number" value="{{ old('registration_number') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Ex: MED-12345">
                    </div>

                    {{-- Rôle --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Spécialité / Fonction</label>
                        <select id="role" name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition bg-white">
                            <option value="doctor">Médecin titulaire</option>
                            <option value="internal_doctor">Médecin interne</option>
                            <option value="nurse">Personnel infirmier</option>
                            <option value="administrative">Personnel administratif</option>
                            <option value="cashier">Caissier</option>
                        </select>
                    </div>

                    {{-- Service (Dynamique) --}}
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Service d'affectation</label>
                        <select id="service_id" name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition bg-white">
                            <option value="">Choisir un service...</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mots de passe --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <input id="password" type="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"
                                placeholder="••••••••">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmation</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- Bouton de soumission --}}
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg mt-8 transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Créer mon compte
                </button>
            </form>

            {{-- Lien de retour --}}
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-600">
                    Déjà inscrit ?
                    <a href="{{ route('hospital.login', $hospital->slug) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-sm text-gray-600 mt-8">
            © 2024 HospitSIS - YA CONSULTING<br>
            <span class="text-xs">Portail de santé sécurisé</span>
        </p>
    </div>
</body>
</html>
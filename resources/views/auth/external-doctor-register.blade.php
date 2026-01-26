<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Médecin Externe - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            {{-- En-tête --}}
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-2xl mb-4">
                    <i class="fas fa-user-md text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">HospitSIS</h1>
                <p class="text-gray-600 mt-2">Portail Médecin Externe</p>
            </div>

            {{-- Carte du Formulaire --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Rejoindre le réseau</h2>
                    <p class="text-gray-600 mt-2">Créez votre compte professionnel en quelques instants</p>
                </div>

                {{-- Formulaire d'inscription --}}
                <form action="{{ route('external.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nom --}}
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input type="text" id="nom" name="nom" required value="{{ old('nom') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Votre nom">
                            @error('nom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required value="{{ old('prenom') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Votre prénom">
                            @error('prenom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="votre@email.com">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" required value="{{ old('telephone') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="+225 ...">
                            @error('telephone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Spécialité --}}
                        <div>
                            <label for="specialite" class="block text-sm font-medium text-gray-700 mb-2">Spécialité</label>
                            <input type="text" id="specialite" name="specialite" required value="{{ old('specialite') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Ex: Cardiologie">
                            @error('specialite') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Numéro d'ordre --}}
                        <div>
                            <label for="numero_ordre" class="block text-sm font-medium text-gray-700 mb-2">N° Ordre des Médecins</label>
                            <input type="text" id="numero_ordre" name="numero_ordre" required value="{{ old('numero_ordre') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Ex: 123456">
                            @error('numero_ordre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Adresse de Résidence --}}
                    <div>
                        <label for="adresse_residence" class="block text-sm font-medium text-gray-700 mb-2">Adresse de résidence (pour vous localiser)</label>
                        <textarea id="adresse_residence" name="adresse_residence" rows="2"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="Votre adresse d'habitation">{{ old('adresse_residence') }}</textarea>
                        @error('adresse_residence') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Documents - Facultatifs --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-bold text-gray-900 mb-2 italic text-green-700">Documents justificatifs (Optionnel)</h3>
                        </div>
                        
                        {{-- Diplôme --}}
                        <div>
                            <label for="diplome" class="block text-sm font-medium text-gray-700 mb-2">Photo du diplôme</label>
                            <input type="file" id="diplome" name="diplome" accept="image/*,.pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all">
                            @error('diplome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- CNI Recto --}}
                        <div>
                            <label for="id_card_recto" class="block text-sm font-medium text-gray-700 mb-2">Carte d'identité (Recto)</label>
                            <input type="file" id="id_card_recto" name="id_card_recto" accept="image/*,.pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all">
                            @error('id_card_recto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- CNI Verso --}}
                        <div>
                            <label for="id_card_verso" class="block text-sm font-medium text-gray-700 mb-2">Carte d'identité (Verso)</label>
                            <input type="file" id="id_card_verso" name="id_card_verso" accept="image/*,.pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all">
                            @error('id_card_verso') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Mot de passe --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <input type="password" id="password" name="password" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Minimum 8 caractères">
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Confirmation Mot de passe --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Confirmez votre mot de passe">
                        </div>
                    </div>

                    {{-- Bouton de soumission --}}
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer mon compte
                    </button>
                </form>

                {{-- Liens supplémentaires --}}
                <div class="mt-6 text-center space-y-4">
                    <p class="text-sm text-gray-600">
                        Déjà inscrit ? 
                        <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:text-green-500">
                            Connectez-vous ici
                        </a>
                    </p>
                    <a href="{{ route('home') }}" class="block text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>

            {{-- Pied de page --}}
            <div class="text-center text-sm text-gray-500">
                <p>© 2024 HospitSIS - YA CONSULTING</p>
                <p>Portail de santé sécurisé</p>
            </div>
        </div>
    </div>
</body>
</html>

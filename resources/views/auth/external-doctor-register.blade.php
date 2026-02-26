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
    
    <x-navigation-buttons :back-url="route('select-portal')" />

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

                        {{-- Type d'identification --}}
                        <div>
                            <label for="id_type" class="block text-sm font-medium text-gray-700 mb-2">Type d'identification professionnelle</label>
                            <select id="id_type" name="id_type" onchange="toggleIdFields()"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                <option value="ordre" {{ old('id_type') == 'ordre' ? 'selected' : '' }}>N° Ordre des Médecins</option>
                                <option value="matricule" {{ old('id_type') == 'matricule' ? 'selected' : '' }}>Numéro Matricule</option>
                                <option value="diplome" {{ old('id_type') == 'diplome' ? 'selected' : '' }}>Numéro d'enregistrement du diplôme</option>
                            </select>
                        </div>

                        {{-- Champs dynamiques d'identification --}}
                        <div id="field_numero_ordre" class="id-field">
                            <label for="numero_ordre" class="block text-sm font-medium text-gray-700 mb-2">N° Ordre des Médecins</label>
                            <input type="text" id="numero_ordre" name="numero_ordre" value="{{ old('numero_ordre') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Ex: 123456">
                            @error('numero_ordre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div id="field_numero_matricule" class="id-field hidden">
                            <label for="numero_matricule" class="block text-sm font-medium text-gray-700 mb-2">Numéro Matricule</label>
                            <input type="text" id="numero_matricule" name="numero_matricule" value="{{ old('numero_matricule') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Votre numéro matricule">
                            @error('numero_matricule') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div id="field_numero_diplome" class="id-field hidden">
                            <label for="numero_diplome" class="block text-sm font-medium text-gray-700 mb-2">Numéro d'enregistrement du diplôme</label>
                            <input type="text" id="numero_diplome" name="numero_diplome" value="{{ old('numero_diplome') }}"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="Ex: DIP-123456">
                            @error('numero_diplome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <script>
                        function toggleIdFields() {
                            const type = document.getElementById('id_type').value;
                            document.querySelectorAll('.id-field').forEach(el => el.classList.add('hidden'));
                            
                            if (type === 'ordre') {
                                document.getElementById('field_numero_ordre').classList.remove('hidden');
                            } else if (type === 'matricule') {
                                document.getElementById('field_numero_matricule').classList.remove('hidden');
                            } else if (type === 'diplome') {
                                document.getElementById('field_numero_diplome').classList.remove('hidden');
                            }
                        }
                        
                        // Initial check
                        window.onload = toggleIdFields;
                    </script>

                    
                    {{-- Affiliation Professionnelle --}}
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 text-blue-800 flex items-center gap-2">
                            <i class="fas fa-hospital-user"></i> Affiliation Professionnelle (Obligatoire)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="affiliation_type" class="block text-sm font-medium text-gray-700 mb-2">Type d'affiliation</label>
                                <select id="affiliation_type" name="affiliation_type" required
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Sélectionnez...</option>
                                    <option value="hospital" {{ old('affiliation_type') == 'hospital' ? 'selected' : '' }}>Hôpital / Clinique</option>
                                    <option value="supervisor" {{ old('affiliation_type') == 'supervisor' ? 'selected' : '' }}>Indépendant (Superviseur)</option>
                                </select>
                                @error('affiliation_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="affiliation_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'Hôpital ou du Superviseur</label>
                                <input type="text" id="affiliation_name" name="affiliation_name" required value="{{ old('affiliation_name') }}"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Ex: CHU Cocody ou Pr. Kouassi">
                                @error('affiliation_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="affiliation_contact" class="block text-sm font-medium text-gray-700 mb-2">Contact du Référent (Téléphone ou Email)</label>
                                <input type="text" id="affiliation_contact" name="affiliation_contact" required value="{{ old('affiliation_contact') }}"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Pour vérification par nos services">
                                @error('affiliation_contact') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
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

                        {{-- Vidéo de vérification --}}
                        <div class="md:col-span-2">
                             <label for="video_verification" class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-video text-red-500"></i> Vidéo de vérification d'identité (Obligatoire - KYC)
                            </label>
                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 mb-2 text-sm text-yellow-800">
                                <i class="fas fa-info-circle"></i> Veuillez uploader une courte vidéo (5-10 secondes) où vous tenez votre pièce d'identité près de votre visage et prononcez : 
                                <strong>"Je suis Dr. [Votre Nom] et je m'inscris sur HospitSIS le {{ date('d/m/Y') }}"</strong>.
                            </div>
                            <input type="file" id="video_verification" name="video_verification" accept="video/mp4,video/webm,video/quicktime" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-all">
                            @error('video_verification') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

                    {{-- Avertissements Légaux --}}
                    <div class="bg-red-50 p-4 rounded-xl border border-red-200">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-5">
                                <input id="terms_accepted" name="terms_accepted" type="checkbox" required
                                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms_accepted" class="font-medium text-red-800">Déclaration sur l'honneur</label>
                                <p class="text-red-700 mt-1">
                                    Je certifie sur l'honneur l'exactitude des informations fournies. Je reconnais que toute fausse déclaration, notamment l'usurpation du titre de médecin, est passible de poursuites pénales immédiates et de bannissement définitif de la plateforme.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Captcha Placeholder --}}
                    <div class="text-xs text-center text-gray-400">
                        Protection anti-robot activée (ReCAPTCHA v3)
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

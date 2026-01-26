<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Médecin Externe - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-doctor {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="gradient-doctor p-8 text-white text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                    <i class="fas fa-user-md text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold">Espace Médecin Externe</h1>
                <p class="text-emerald-50 opacity-90">Accédez à votre tableau de bord spécialisé</p>
            </div>

            <div class="p-8">
                <!-- Messages d'erreur -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg animate-pulse">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <p class="text-sm">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('external.login.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse Email Professionnelle
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 outline-none"
                                placeholder="dr.smith@exemple.com"
                            >
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                required
                                class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 outline-none"
                                placeholder="••••••••"
                            >
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 transition-colors cursor-pointer">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-emerald-700 transition-colors">Rester connecté</span>
                        </label>
                        <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                            Oubli ?
                        </a>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full gradient-doctor hover:shadow-lg hover:opacity-90 text-white font-bold py-3.5 rounded-xl transition-all duration-300 transform active:scale-[0.98] flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-gray-600 text-sm">
                        Nouveau praticien ? 
                        <a href="{{ route('external.register') }}" class="text-emerald-600 font-bold hover:underline">
                            Créer votre compte
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-emerald-700 hover:text-emerald-900 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Retour à l'accueil
            </a>
            <p class="mt-4 text-xs text-emerald-800/60 font-medium uppercase tracking-widest">
                Portail Sécurisé HospitSIS
            </p>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>

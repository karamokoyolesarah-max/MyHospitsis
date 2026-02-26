<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HospitSIS - Votre santé au cœur de l\'innovation digitale')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%); }
        .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
            50% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.8); }
        }
        .btn-modern {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
    </style>
    @yield('extra_head')
</head>
<body class="bg-slate-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="bg-blue-600 text-white p-2 rounded-lg pulse-glow hover:scale-110 transition-transform duration-300">
                        <span class="font-black text-xl">HS</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 leading-none">HospitSIS</h1>
                        <p class="text-[10px] uppercase tracking-widest text-blue-600 font-bold">Santé Digitale</p>
                    </div>
                </a>

                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-full font-semibold shadow-lg transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                            Se connecter
                        </a>
                        <a href="{{ route('select-portal') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2">
                            <i class="fas fa-user-plus text-sm"></i>
                            Créer un compte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 px-6">
        <div class="container mx-auto max-w-6xl text-center">
            <div class="flex items-center justify-center space-x-4 mb-6">
                <div class="w-12 h-12 gradient-primary rounded-xl flex items-center justify-center shadow-2xl">
                    <span class="text-white font-black text-xl">HS</span>
                </div>
                <div class="text-left">
                    <span class="font-black text-2xl">HospitSIS</span>
                    <p class="text-xs text-blue-400 uppercase tracking-widest">Santé Digitale</p>
                </div>
            </div>
            <p class="text-gray-400 text-base mb-8 max-w-2xl mx-auto">
                Votre santé au cœur de l'innovation digitale. Une plateforme moderne pour une gestion optimale de votre santé.
            </p>
            <div class="flex justify-center gap-6 mb-8 text-gray-400">
                <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f text-xl"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin-in text-xl"></i></a>
            </div>
            <div class="border-t border-gray-800 pt-8 text-gray-500 text-sm">
                &copy; {{ date('Y') }} HospitSIS. Tous droits réservés.
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>

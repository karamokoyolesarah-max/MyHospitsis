<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir votre portail - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%); }
        
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(60px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
            50% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.8); }
        }

        .fade-up { animation: fadeInUp 1s ease-out; }
        .float { animation: float 3s ease-in-out infinite; }
        .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

        .portal-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
        }

        .portal-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 text-white p-2 rounded-lg pulse-glow hover:scale-110 transition-transform duration-300">
                        <span class="font-black text-xl">HS</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 leading-none">HospitSIS</h1>
                        <p class="text-[10px] uppercase tracking-widest text-blue-600 font-bold">Santé Digitale</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-slate-600 hover:text-slate-800 font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left text-sm"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-20 px-6">
        <div class="container mx-auto max-w-6xl text-center">
            <div class="float mb-8">
                <div class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border-2 border-white/40 shadow-2xl">
                    <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-3 shadow-lg"></span>
                    <span class="text-sm font-bold tracking-wide">🔒 Inscription Sécurisée</span>
                </div>
            </div>

            <h1 class="text-5xl md:text-6xl font-black mb-6 leading-tight drop-shadow-2xl fade-up">
                Créer votre <span class="text-blue-600">compte</span>
            </h1>
            <p class="text-xl text-slate-600 mb-12 max-w-2xl mx-auto">Choisissez votre profil pour commencer votre inscription sur la plateforme.</p>

            <div class="grid md:grid-cols-3 gap-8 mt-12">

                <div class="portal-card bg-gradient-to-br from-blue-50 to-cyan-50 shadow-xl border-t-4 border-blue-500 group">
                    <div class="p-8 text-center flex flex-col h-full">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full mx-auto mb-6 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-injured text-white text-3xl"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Portail Patient</h3>
                        <p class="text-slate-600 mb-8 text-lg leading-relaxed flex-grow">Gérez vos rendez-vous, consultez vos résultats et votre historique médical.</p>

                        <a href="{{ route('patient.register') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-5 rounded-2xl font-bold hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 shadow-lg w-full justify-center">
                            <i class="fas fa-user-plus text-xl"></i>
                            <span>S'inscrire comme Patient</span>
                        </a>
                    </div>
                </div>

                <div class="portal-card bg-gradient-to-br from-green-50 to-emerald-50 shadow-xl border-t-4 border-green-500 group">
                    <div class="p-8 text-center flex flex-col h-full">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full mx-auto mb-6 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-stethoscope text-white text-3xl"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Médecin Externe</h3>
                        <p class="text-slate-600 mb-8 text-lg leading-relaxed flex-grow">Rejoignez notre réseau pour suivre vos patients </p>
                        
                        <a href="{{ route('external.register') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-5 rounded-2xl font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 shadow-lg w-full justify-center">
                            <i class="fas fa-user-plus text-xl"></i>
                            <span>S'inscrire comme Médecin</span>
                        </a>
                    </div>
                </div>

                <div class="portal-card bg-gradient-to-br from-purple-50 to-violet-50 shadow-xl border-t-4 border-purple-500 group">
                    <div class="p-8 text-center flex flex-col h-full">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full mx-auto mb-6 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users-cog text-white text-3xl"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Portail Personnel</h3>
                        <p class="text-slate-600 mb-8 text-lg leading-relaxed flex-grow">Pour les médecins internes, infirmiers et l'équipe administrative.</p>
                        
                        <a href="{{ route('hospital.select') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-8 py-5 rounded-2xl font-bold hover:from-purple-600 hover:to-purple-700 transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 shadow-lg w-full justify-center">
                            <i class="fas fa-hospital text-xl"></i>
                            <span>Sélectionner mon Hôpital</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12 px-6 mt-auto">
        <div class="container mx-auto max-w-6xl text-center">
            <p class="text-gray-400 text-lg">Votre santé au cœur de l'innovation digitale • Certifié HDS & RGPD</p>
        </div>
    </footer>
</body>
</html>
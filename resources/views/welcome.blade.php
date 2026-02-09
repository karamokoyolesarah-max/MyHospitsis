<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HospitSIS - Votre santé au cœur de l'innovation digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Couleurs vibrantes */
        .gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%); }
        .gradient-secondary { background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%); }
        .gradient-accent { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
        
        /* Hero overlay */
        .hero-overlay { 
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.88) 0%, 
                rgba(139, 92, 246, 0.88) 50%, 
                rgba(217, 70, 239, 0.88) 100%
            ); 
        }
        
        /* Animations */
        @keyframes slideInFromLeft {
            0% { opacity: 0; transform: translateX(-100px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInFromRight {
            0% { opacity: 0; transform: translateX(100px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(60px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-60px); }
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
        
        .slide-left { animation: slideInFromLeft 1s ease-out; }
        .slide-right { animation: slideInFromRight 1s ease-out; }
        .fade-up { animation: fadeInUp 1s ease-out; }
        .fade-down { animation: fadeInDown 1s ease-out; }
        .float { animation: float 3s ease-in-out infinite; }
        .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        
        /* Carrousel */
        .carousel-container { 
            position: relative; 
            height: 650px; 
            overflow: hidden;
            border-radius: 0 0 50px 50px;
        }
        
        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
        }
        
        .carousel-slide.active { opacity: 1; }
        
        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .carousel-dot.active {
            width: 40px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
        }
        
        /* Cards santé avec effet 3D */
        .health-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .health-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s;
            z-index: 1;
        }
        
        .health-card:hover::before {
            left: 100%;
        }
        
        .health-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        
        /* Images défilantes plus petites */
        .image-slider {
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        
        .image-slider img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
        }
        
        .image-slider img.active {
            opacity: 1;
        }
        
        /* Modal */
        #portalsModal, #registerModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            backdrop-filter: blur(15px);
        }

        #portalsModal.active, #registerModal.active {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(100px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Boutons modernes */
        .btn-modern {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }
        
        .btn-modern:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Stats avec fond blanc */
        .stat-number {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .carousel-container { height: 450px; }
            .stat-number { font-size: 3rem; }
            .image-slider { height: 150px; }
        }
    </style>
</head>
<body class="bg-slate-50">
    <!-- Header -->
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
                    <!-- Mobile Menu Toggle -->
                    <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
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
        
        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-200 animate-slide-down">
            <div class="px-4 pt-2 pb-6 space-y-3">
                <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-50 text-gray-900 font-bold">
                    <i class="fas fa-sign-in-alt text-blue-600 w-5"></i>
                    Se connecter
                </a>
                <a href="{{ route('select-portal') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-600 font-bold">
                    <i class="fas fa-user-plus text-blue-600 w-5"></i>
                    Créer un compte
                </a>
                <div class="pt-4 border-t border-slate-100 flex justify-center gap-6">
                    <a href="#" class="text-slate-400 hover:text-blue-600"><i class="fab fa-facebook-f text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-blue-600"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-blue-600"><i class="fab fa-linkedin-in text-xl"></i></a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero avec carrousel -->
    <section class="carousel-container">
        <!-- Images du carrousel -->
        <div class="carousel-slide active"
             style="background-image: url('{{ asset('images/hero/banner1.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="carousel-slide"
             style="background-image: url('{{ asset('images/hero/banner2.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="carousel-slide"
             style="background-image: url('{{ asset('images/hero/banner3.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="carousel-slide"
             style="background-image: url('{{ asset('images/hero/banner4.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="carousel-slide"
             style="background-image: url('{{ asset('images/hero/banner5.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="carousel-slide"
             style="background-image: url('{{ asset('images/hero/banner6.jpg') }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 to-slate-700/70"></div>
        </div>

        <div class="relative z-10 flex items-center justify-center h-full text-white px-6">
            <div class="text-center max-w-5xl">
                <div class="float mb-8">
                    <div class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border-2 border-white/40 shadow-2xl">
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-3 shadow-lg"></span>
                        <span class="text-sm font-bold tracking-wide">🔒 Certifié HDS • RGPD • ISO 27001</span>
                    </div>
                </div>

                <h1 class="text-4xl md:text-7xl font-black mb-6 leading-tight drop-shadow-2xl fade-down">
                    Bienvenue sur<br/>
                    <span class="text-blue-300">HospitSIS</span>
                </h1>

                <p class="text-xl md:text-3xl mb-8 font-light leading-relaxed drop-shadow-xl fade-up">
                    🏥 Votre santé au cœur de l'innovation digitale
                </p>

                <p class="text-lg text-slate-100 mb-12 max-w-3xl mx-auto fade-up" style="animation-delay: 0.2s">
                    Une plateforme moderne et sécurisée pour une gestion optimale de votre santé
                </p>

                <a href="#sante-quotidien" class="inline-flex items-center gap-3 bg-white text-slate-700 px-10 py-4 rounded-2xl font-bold hover:bg-slate-50 transition-all shadow-2xl transform hover:scale-110 text-lg btn-modern">
                    <i class="fas fa-heartbeat text-2xl"></i>
                    <span>Découvrir nos services</span>
                </a>
            </div>
        </div>

        <!-- Dots carrousel -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-3 z-20">
            <button class="carousel-dot active"></button>
            <button class="carousel-dot"></button>
            <button class="carousel-dot"></button>
            <button class="carousel-dot"></button>
            <button class="carousel-dot"></button>
            <button class="carousel-dot"></button>
        </div>
    </section>

    <!-- Bande de confiance -->
    <section class="gradient-primary py-8 text-white shadow-2xl relative z-20">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12 text-center">
                <div class="flex items-center gap-3 hover:scale-110 transition duration-300 cursor-pointer">
                    <i class="fas fa-star text-4xl text-yellow-300 drop-shadow-lg"></i>
                    <div class="text-left">
                        <div class="text-2xl font-black">98%</div>
                        <div class="text-sm font-semibold opacity-90">Satisfaction</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 hover:scale-110 transition duration-300 cursor-pointer">
                    <i class="fas fa-shield-alt text-4xl text-green-300 drop-shadow-lg"></i>
                    <div class="text-left">
                        <div class="text-2xl font-black">Certifié</div>
                        <div class="text-sm font-semibold opacity-90">HDS & ISO</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 hover:scale-110 transition duration-300 cursor-pointer">
                    <i class="fas fa-users text-4xl text-blue-300 drop-shadow-lg"></i>
                    <div class="text-left">
                        <div class="text-2xl font-black">15K+</div>
                        <div class="text-sm font-semibold opacity-90">Patients</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 hover:scale-110 transition duration-300 cursor-pointer">
                    <i class="fas fa-user-md text-4xl text-pink-300 drop-shadow-lg"></i>
                    <div class="text-left">
                        <div class="text-2xl font-black">250+</div>
                        <div class="text-sm font-semibold opacity-90">Médecins</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 hover:scale-110 transition duration-300 cursor-pointer">
                    <i class="fas fa-clock text-4xl text-orange-300 drop-shadow-lg"></i>
                    <div class="text-left">
                        <div class="text-2xl font-black">24/7</div>
                        <div class="text-sm font-semibold opacity-90">Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Votre santé au quotidien - 6 SERVICES AVEC VOS IMAGES -->
    <section id="sante-quotidien" class="py-20 px-6 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center mb-16 fade-up">
                <span class="inline-block px-8 py-3 gradient-primary text-white rounded-full text-sm font-bold mb-6 shadow-xl">
                    💚 VOTRE SANTÉ AU QUOTIDIEN
                </span>
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                    Nous prenons soin de vous
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Des services complets pour accompagner votre santé à chaque étape de la vie
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- MATERNITÉ - Suivi de Grossesse (Images 1, 2, 3) -->
                <div class="health-card bg-gradient-to-br from-pink-50 to-rose-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-pink-500">
                    <div class="image-slider" id="slider1">
                        <img src="{{ asset('images/grossesse/image1.jpg') }}" alt="Grossesse 1" class="active">
                        <img src="{{ asset('images/grossesse/image2.jpg') }}" alt="Grossesse 2">
                        <img src="{{ asset('images/grossesse/image3.jpg') }}" alt="Grossesse 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-pink-500 text-white rounded-full text-xs font-bold mb-3">MATERNITÉ</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Suivi de Grossesse</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Accompagnement personnalisé pendant votre grossesse. Consultations prénatales, échographies, cours de préparation.
                        </p>
                        <a href="#" class="inline-flex items-center text-pink-600 font-bold hover:text-pink-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- CARDIOLOGIE (Images 4, 5, 6) -->
                <div class="health-card bg-gradient-to-br from-orange-50 to-red-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-orange-500">
                    <div class="image-slider" id="slider2">
                        <img src="{{ asset('images/cardio/image1.jpg') }}" alt="Cardio 1" class="active">
                        <img src="{{ asset('images/cardio/image2.jpg') }}" alt="Cardio 2">
                        <img src="{{ asset('images/cardio/image3.jpg') }}" alt="Cardio 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-orange-500 text-white rounded-full text-xs font-bold mb-3">CARDIOLOGIE</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Santé Cardiovasculaire</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Prévention et traitement des maladies cardiaques. Bilans cardiovasculaires complets et suivi personnalisé.
                        </p>
                        <a href="#" class="inline-flex items-center text-orange-600 font-bold hover:text-orange-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- PÉDIATRIE (Images 7, 8, 9) -->
                <div class="health-card bg-gradient-to-br from-blue-50 to-cyan-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-blue-500">
                    <div class="image-slider" id="slider3">
                        <img src="{{ asset('images/enfant/image1.jpg') }}" alt="Enfant 1" class="active">
                        <img src="{{ asset('images/enfant/image2.jpg') }}" alt="Enfant 2">
                        <img src="{{ asset('images/enfant/image3.jpg') }}" alt="Enfant 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-blue-500 text-white rounded-full text-xs font-bold mb-3">PÉDIATRIE</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Santé de l'Enfant</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Suivi médical de la naissance à l'adolescence. Vaccinations, bilans de santé et conseils aux parents.
                        </p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- NUTRITION (Images 10, 11, 12) -->
                <div class="health-card bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-green-500">
                    <div class="image-slider" id="slider4">
                        <img src="{{ asset('images/nutrition/image1.jpg') }}" alt="Nutrition 1" class="active">
                        <img src="{{ asset('images/nutrition/image2.jpg') }}" alt="Nutrition 2">
                        <img src="{{ asset('images/nutrition/image3.jpg') }}" alt="Nutrition 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-green-500 text-white rounded-full text-xs font-bold mb-3">NUTRITION</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Nutrition & Bien-être</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Conseils nutritionnels personnalisés. Programmes d'alimentation équilibrée et gestion du poids.
                        </p>
                        <a href="#" class="inline-flex items-center text-green-600 font-bold hover:text-green-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- PSYCHOLOGIE (Images 13, 14, 15) -->
                <div class="health-card bg-gradient-to-br from-purple-50 to-violet-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-purple-500">
                    <div class="image-slider" id="slider5">
                        <img src="{{ asset('images/psycho/image1.jpg') }}" alt="Psycho 1" class="active">
                        <img src="{{ asset('images/psycho/image2.jpg') }}" alt="Psycho 2">
                        <img src="{{ asset('images/psycho/image3.jpeg') }}" alt="Psycho 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-purple-500 text-white rounded-full text-xs font-bold mb-3">PSYCHOLOGIE</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Santé Mentale</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Accompagnement psychologique et soutien émotionnel. Gestion du stress et thérapies comportementales.
                        </p>
                        <a href="#" class="inline-flex items-center text-purple-600 font-bold hover:text-purple-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- URGENCES (Images 16, 17, 18) -->
                <div class="health-card bg-gradient-to-br from-yellow-50 to-amber-50 rounded-3xl shadow-xl overflow-hidden border-t-4 border-yellow-500">
                    <div class="image-slider" id="slider6">
                        <img src="{{ asset('images/urgence/image1.jpg') }}" alt="Urgence 1" class="active">
                        <img src="{{ asset('images/urgence/image2.jpg') }}" alt="Urgence 2">
                        <img src="{{ asset('images/urgence/image3.jpg') }}" alt="Urgence 3">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-4 py-1 bg-yellow-500 text-white rounded-full text-xs font-bold mb-3">URGENCES</span>
                        <h3 class="text-2xl font-black text-gray-900 mb-3">Urgences 24/7</h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            Service d'urgences disponible jour et nuit. Équipe médicale qualifiée prête à intervenir rapidement.
                        </p>
                        <a href="#" class="inline-flex items-center text-yellow-600 font-bold hover:text-yellow-700 text-sm">
                            En savoir plus <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques - FOND BLANC -->
    <section class="py-20 px-6 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl md:text-5xl font-black text-center text-gray-900 mb-4">Notre Impact</h2>
            <p class="text-lg md:text-xl text-center text-gray-600 mb-16">Des chiffres qui parlent d'eux-mêmes</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
                <div class="text-center transform hover:scale-110 transition duration-300">
                    <div class="stat-number">15K+</div>
                    <p class="text-lg font-bold text-gray-700 mt-2">Patients Enregistrés</p>
                </div>
                <div class="text-center transform hover:scale-110 transition duration-300">
                    <div class="stat-number">250+</div>
                    <p class="text-lg font-bold text-gray-700 mt-2">Médecins Actifs</p>
                </div>
                <div class="text-center transform hover:scale-110 transition duration-300">
                    <div class="stat-number">98%</div>
                    <p class="text-lg font-bold text-gray-700 mt-2">Satisfaction Client</p>
                </div>
                <div class="text-center transform hover:scale-110 transition duration-300">
                    <div class="stat-number">24/7</div>
                    <p class="text-lg font-bold text-gray-700 mt-2">Support Disponible</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 px-6">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center space-x-4 mb-6">
                    <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center shadow-2xl">
                        <span class="text-white font-black text-3xl">HS</span>
                    </div>
                    <div class="text-left">
                        <span class="font-black text-3xl">HospitSIS</span>
                        <p class="text-sm text-purple-400">Santé Digitale</p>
                    </div>
                </div>
                <p class="text-gray-400 text-lg mb-8 max-w-2xl mx-auto">
                    Votre santé au cœur de l'innovation digitale
                </p>
                
                <div class="flex justify-center gap-8 mb-8 flex-wrap text-gray-400">
                    <a href="mailto:contact@hospitsis.ci" class="hover:text-white transition flex items-center gap-2">
                        <i class="fas fa-envelope"></i> contact@hospitsis.ci
                    </a>
                    <a href="tel:+225" class="hover:text-white transition flex items-center gap-2">
                        <i class="fas fa-phone"></i> +225 XX XX XX XX
                    </a>
                    <a href="#" class="hover:text-white transition">
                        <i class="fas fa-globe"></i> Site Web
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Portails -->
    <div id="portalsModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-10 max-w-lg w-full mx-4 modal-content shadow-2xl border border-white/20 relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-50/30 via-purple-50/20 to-green-50/30 pointer-events-none"></div>
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-gradient-to-br from-green-400/20 to-blue-400/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 via-purple-500 to-green-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-xl animate-pulse">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <h3 class="text-4xl font-black text-gray-900 mb-3 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Choisir votre portail</h3>
                <p class="text-gray-600 text-lg leading-relaxed">Sélectionnez le portail approprié pour accéder à votre compte</p>
            </div>

            <div class="relative z-10 space-y-5">
                <a href="{{ route('login') }}" class="portal-btn group block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-5 rounded-2xl font-bold text-center hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-2xl border border-blue-400/20">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-user-injured mr-4 text-xl group-hover:animate-bounce"></i>
                        <span class="text-lg">Portail Patient</span>
                    </div>
                </a>
                <a href="{{ route('login') }}" class="portal-btn group block bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-5 rounded-2xl font-bold text-center hover:from-green-600 hover:to-green-700 transform hover:scale-105 hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-2xl border border-green-400/20">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-stethoscope mr-4 text-xl group-hover:animate-pulse"></i>
                        <span class="text-lg">Portail Médecine Externes</span>
                    </div>
                </a>
            </div>

            <div class="relative z-10 mt-8">
                <button id="closePortalsBtn" class="w-full bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg hover:shadow-xl border border-gray-300/50">
                    <i class="fas fa-times mr-3"></i>Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Inscription Patient -->
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4 modal-content max-h-screen overflow-y-auto">
            <h3 class="text-2xl font-bold mb-4">Créer un compte patient</h3>
            <form id="register-form" method="POST" action="{{ route('patient.register') }}" class="space-y-6">
                @csrf

                <!-- Prénom -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Jean"
                        value="{{ old('first_name') }}"
                        required autofocus autocomplete="given-name"
                    >
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Nom -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Dupont"
                        value="{{ old('last_name') }}"
                        required autocomplete="family-name"
                    >
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="votre@email.com"
                        value="{{ old('email') }}"
                        required autocomplete="username"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="+225 XX XX XX XX XX"
                        value="{{ old('phone') }}"
                        required
                    >
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Date de naissance -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('date_of_birth') }}"
                        required
                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                    >
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••"
                            required autocomplete="new-password"
                        >
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" id="toggle-password">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="bg-gray-200 rounded-full h-2" id="password-strength">
                            <div class="h-full bg-gray-400 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="password-text">Minimum 8 caractères</p>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••"
                            required autocomplete="new-password"
                        >
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" id="toggle-confirm-password">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Conditions d'utilisation -->
                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        J'accepte les <a href="#" class="text-blue-600 hover:text-blue-800">conditions d'utilisation</a> et la <a href="#" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>
                    </label>
                </div>

                <!-- Message d'erreur -->
                <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <span id="error-text"></span>
                </div>

                <!-- Message de succès -->
                <div id="success-message" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    <span id="success-text"></span>
                </div>

                <!-- Bouton d'inscription -->
                <button
                    type="submit"
                    id="register-btn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span id="register-btn-text">S'inscrire comme Patient</span>
                    <span id="register-btn-loading" class="hidden ml-2">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </form>
            <button id="closeRegisterBtn" class="mt-4 text-gray-500 hover:text-gray-700">Fermer</button>
        </div>
    </div>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                const icon = mobileMenuBtn.querySelector('i');
                if (mobileMenu.classList.contains('hidden')) {
                    icon.classList.replace('fa-times', 'fa-bars');
                } else {
                    icon.classList.replace('fa-bars', 'fa-times');
                }
            });
        }

        // Carrousel Hero
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        // Auto-slide every 5 seconds
        setInterval(nextSlide, 5000);

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        // Image sliders for service cards
        function initImageSliders() {
            const sliders = document.querySelectorAll('.image-slider');

            sliders.forEach(slider => {
                const images = slider.querySelectorAll('img');
                let currentImage = 0;

                function showImage(index) {
                    images.forEach(img => img.classList.remove('active'));
                    images[index].classList.add('active');
                    currentImage = index;
                }

                function nextImage() {
                    currentImage = (currentImage + 1) % images.length;
                    showImage(currentImage);
                }

                // Auto-cycle every 3 seconds
                setInterval(nextImage, 3000);
            });
        }

        // Initialize sliders when DOM is loaded
        document.addEventListener('DOMContentLoaded', initImageSliders);

        // Modal functionality
        const openPortalsBtn = document.getElementById('openPortalsBtn');
        const closePortalsBtn = document.getElementById('closePortalsBtn');
        const portalsModal = document.getElementById('portalsModal');

        const openRegisterBtn = document.getElementById('openRegisterBtn');
        const closeRegisterBtn = document.getElementById('closeRegisterBtn');
        const registerModal = document.getElementById('registerModal');

        if (openPortalsBtn) {
            openPortalsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                portalsModal.classList.add('active');
            });
        }

        if (closePortalsBtn) {
            closePortalsBtn.addEventListener('click', () => {
                portalsModal.classList.remove('active');
            });
        }

        // Close modal when clicking outside
        if (portalsModal) {
            portalsModal.addEventListener('click', (e) => {
                if (e.target === portalsModal) {
                    portalsModal.classList.remove('active');
                }
            });
        }

        if (openRegisterBtn) {
            openRegisterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                registerModal.classList.add('active');
            });
        }

        if (closeRegisterBtn) {
            closeRegisterBtn.addEventListener('click', () => {
                registerModal.classList.remove('active');
            });
        }

        // Close modal when clicking outside
        if (registerModal) {
            registerModal.addEventListener('click', (e) => {
                if (e.target === registerModal) {
                    registerModal.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
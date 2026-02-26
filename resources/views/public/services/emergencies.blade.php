@extends('layouts.public')

@section('title', 'Urgences 24/7 - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden bg-red-600 text-white">
        <div class="absolute inset-0 opacity-20">
             <img src="{{ asset('images/urgence/image1.jpg') }}" alt="Urgence" class="w-full h-full object-cover">
        </div>
        <div class="container mx-auto px-6 max-w-7xl relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full mb-8 backdrop-blur-md">
                <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                <span class="text-xs font-bold uppercase tracking-widest">Service d'Urgence Disponible Now</span>
            </div>
            <h1 class="text-5xl md:text-8xl font-black mb-8">URGENCES <span class="underline decoration-white/30">24/7</span></h1>
            <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-red-100">
                Une équipe médicale d'intervention rapide, prête à tout moment pour sauver des vies. Votre sécurité, notre priorité absolue.
            </p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="tel:112" class="bg-white text-red-600 px-10 py-5 rounded-2xl font-black text-2xl shadow-[0_0_50px_rgba(0,0,0,0.3)] hover:scale-110 transition-all flex items-center gap-4">
                    <i class="fas fa-phone-alt animate-pulse"></i>
                    Appeler les Urgences
                </a>
                <a href="{{ route('select-portal') }}" class="bg-red-700/50 backdrop-blur-md border-2 border-white/30 text-white px-10 py-5 rounded-2xl font-black text-xl hover:bg-red-700 transition-all">
                    Signaler une urgence via l'app
                </a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-3 gap-12">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-3xl flex items-center justify-center mb-8 rotate-3">
                        <i class="fas fa-ambulance text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">Ambulances Équipées</h3>
                    <p class="text-gray-600 text-sm">Flotte moderne avec soins intensifs mobiles pour une stabilisation immédiate.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-3xl flex items-center justify-center mb-8 -rotate-3">
                        <i class="fas fa-user-md text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">Spécialistes 24/7</h3>
                    <p class="text-gray-600 text-sm">Chirurgiens, urgentistes et réanimateurs présents sur site en permanence.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-3xl flex items-center justify-center mb-8 rotate-6">
                        <i class="fas fa-hospital text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4">Plateau Technique</h3>
                    <p class="text-gray-600 text-sm">Blocs opératoires et radiologie d'urgence accessibles sans délai.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

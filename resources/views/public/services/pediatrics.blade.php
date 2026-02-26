@extends('layouts.public')

@section('title', 'Pédiatrie & Santé de l\'Enfant - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden bg-blue-50/30">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="fade-up">
                    <span class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-bold mb-6">Soin des Tout-Petits</span>
                    <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Faire grandir vos enfants en <span class="text-blue-600">pleine santé</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        De la naissance à l'adolescence, HospitSIS accompagne la croissance de vos enfants avec douceur, expertise et des outils digitaux innovants pour les parents.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('select-portal') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all transform hover:scale-105">
                            Prendre RDV Pédiatrie
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-full h-[400px] md:h-[500px] rounded-[3rem] overflow-hidden shadow-2xl relative">
                        <img src="{{ asset('images/enfant/image2.jpg') }}" alt="Pédiatrie" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-blue-600/10"></div>
                    </div>
                    <!-- Decor -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-yellow-400 rounded-full blur-2xl opacity-50 animate-pulse"></div>
                    <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-blue-400 rounded-full blur-3xl opacity-20"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-4 gap-6">
                @foreach([
                    ['icon' => 'fa-baby', 'title' => 'Nouveaux-nés', 'desc' => 'Suivi premier mois et conseils allaitement.'],
                    ['icon' => 'fa-syringe', 'title' => 'Vaccination', 'desc' => 'Calendrier vaccinal complet et rappels digitaux.'],
                    ['icon' => 'fa-apple-alt', 'title' => 'Croissance', 'desc' => 'Courbes de poids et de taille digitalisées.'],
                    ['icon' => 'fa-brain', 'title' => 'Éveil', 'desc' => 'Suivi du développement psychomoteur.']
                ] as $s)
                <div class="p-8 rounded-[2rem] bg-slate-50 border border-transparent hover:border-blue-100 hover:bg-blue-50/50 transition-all text-center">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <i class="fas {{ $s['icon'] }} text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">{{ $s['title'] }}</h3>
                    <p class="text-xs text-gray-500">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-6 max-w-5xl text-center">
            <h2 class="text-3xl font-black mb-8">Un carnet de santé digital toujours à portée de main</h2>
            <p class="text-gray-600 mb-12">
                Chez HospitSIS, chaque enfant bénéficie d'un suivi numérique rigoureux accessible aux parents 24h/24. Ne perdez plus jamais une information cruciale.
            </p>
            <div class="bg-blue-600 p-1 rounded-3xl overflow-hidden shadow-2xl">
                 <img src="{{ asset('images/enfant/image1.jpg') }}" alt="Examen" class="w-full h-80 object-cover rounded-[1.4rem]">
            </div>
        </div>
    </section>
@endsection

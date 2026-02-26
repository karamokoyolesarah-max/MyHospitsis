@extends('layouts.public')

@section('title', 'Santé Mentale & Psychologie - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden bg-purple-50/30">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="fade-up">
                    <span class="inline-block px-4 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-bold mb-6">Soutien & Bien-être</span>
                    <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Parler est le premier pas vers la <span class="text-purple-600">guérison</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        HospitSIS offre un espace sécurisé et confidentiel pour prendre soin de votre santé mentale. Nos psychologues sont là pour vous écouter et vous guider.
                    </p>
                    <a href="{{ route('select-portal') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all transform hover:scale-105">
                        Trouver un psychologue
                    </a>
                </div>
                <div class="relative">
                    <div class="w-full h-[400px] md:h-[500px] rounded-[3rem] overflow-hidden shadow-2xl grayscale hover:grayscale-0 transition-all duration-1000">
                        <img src="{{ asset('images/psycho/image1.jpg') }}" alt="Psychologie" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl text-center">
            <h2 class="text-4xl font-black mb-12 text-gray-900 italic font-serif">"La santé mentale n'est pas une destination, mais un processus."</h2>
            <div class="grid md:grid-cols-2 gap-8 text-left">
                <div class="p-8 rounded-3xl bg-purple-50/50">
                    <h4 class="font-bold text-lg mb-4 text-purple-600">Thérapie Individuelle</h4>
                    <p class="text-sm text-gray-600">Un suivi personnalisé pour traiter le stress, l'anxiété ou la dépression.</p>
                </div>
                <div class="p-8 rounded-3xl bg-slate-50">
                    <h4 class="font-bold text-lg mb-4 text-gray-900">Thérapie de Couple</h4>
                    <p class="text-sm text-gray-600">Améliorer la communication et résoudre les conflits dans un cadre neutre.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

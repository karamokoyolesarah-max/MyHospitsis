@extends('layouts.public')

@section('title', 'Cardiologie & Santé Cardiovasculaire - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/5 -z-10"></div>
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="fade-up">
                    <span class="inline-block px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-bold mb-6">Expertise Cardiologique</span>
                    <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Prendre soin de votre <span class="text-orange-600">cœur</span>, chaque jour
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        HospitSIS propose une approche intégrée de la santé cardiovasculaire, alliant technologie de pointe et expertise médicale pour prévenir, diagnostiquer et traiter les maladies du cœur.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('select-portal') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all transform hover:scale-105">
                            Bilan Cardiaque
                        </a>
                        <a href="#details" class="bg-white border-2 border-orange-100 text-orange-600 px-8 py-4 rounded-2xl font-bold hover:bg-orange-50 transition-all">
                            Voir nos services
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-full h-[400px] md:h-[500px] rounded-[2rem] overflow-hidden shadow-2xl transform -rotate-3 hover:rotate-0 transition-transform duration-700 border-b-8 border-orange-500">
                        <img src="{{ asset('images/cardio/image2.jpg') }}" alt="Cardiologie" class="w-full h-full object-cover">
                    </div>
                    <!-- Floating info -->
                    <div class="absolute top-10 -right-6 bg-white p-6 rounded-2xl shadow-xl border-l-4 border-orange-500">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-heartbeat text-orange-500 text-2xl animate-pulse"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tension artérielle</p>
                                <p class="text-lg font-bold text-gray-900">Suivi 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Excellence Section -->
    <section id="details" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">Notre excellence au service de votre cœur</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Une équipe dédiée de cardiologues et de techniciens qualifiés pour une prise en charge optimale.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-10 rounded-[2rem] bg-slate-50 border border-gray-100 hover:bg-orange-600 hover:text-white transition-all group">
                    <div class="w-16 h-16 bg-white text-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-microscope text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Diagnostics Avancés</h3>
                    <p class="text-gray-600 group-hover:text-orange-50 leading-relaxed text-sm">
                        ECG, Échocardiographie Doppler, Holter et tests d'effort avec des équipements de dernière génération.
                    </p>
                </div>

                <div class="p-10 rounded-[2rem] bg-orange-600 text-white shadow-2xl transform scale-105 relative z-10">
                    <div class="w-16 h-16 bg-white text-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-md">
                        <i class="fas fa-user-md text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Consultations Experts</h3>
                    <p class="text-orange-50 leading-relaxed text-sm">
                        Évaluation clinique approfondie par nos spécialistes pour identifier les risques et traiter les pathologies cardiaques.
                    </p>
                </div>

                <div class="p-10 rounded-[2rem] bg-slate-50 border border-gray-100 hover:bg-orange-600 hover:text-white transition-all group">
                    <div class="w-16 h-16 bg-white text-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-running text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Prévention & Réadaptation</h3>
                    <p class="text-gray-600 group-hover:text-orange-50 leading-relaxed text-sm">
                        Programmes personnalisés d'activité physique et de nutrition pour maintenir un cœur fort et en bonne santé.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Innovation Section -->
    <section class="py-24 bg-gray-50 overflow-hidden">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="flex flex-col md:flex-row items-center gap-16">
                <div class="md:w-1/2 order-2 md:order-1">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="h-40 bg-orange-200 rounded-3xl"></div>
                            <div class="h-64 bg-slate-200 rounded-3xl overflow-hidden shadow-lg">
                                <img src="{{ asset('images/cardio/image2.jpg') }}" alt="Tech" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="space-y-4 pt-8">
                            <div class="h-64 bg-slate-200 rounded-3xl overflow-hidden shadow-lg">
                                <img src="{{ asset('images/cardio/image3.jpg') }}" alt="Doctor" class="w-full h-full object-cover">
                            </div>
                            <div class="h-40 bg-blue-200 rounded-3xl"></div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 order-1 md:order-2">
                    <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-8 leading-tight">L'innovation au service de la vie</h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Le suivi cardiovasculaire digital chez HospitSIS permet une détection précoce des anomalies grâce au monitoring à distance et à l'analyse intelligente des données.
                    </p>
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-start gap-3">
                            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Monitoring cardiaque en temps réel</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Téléconsultation spécialisée rapide</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-1 flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700 font-medium">Historique médical complet et sécurisé</span>
                        </li>
                    </ul>
                    <a href="{{ route('select-portal') }}" class="inline-flex items-center gap-3 text-orange-600 font-black text-lg group">
                        Réserver mon bilan complet
                        <i class="fas fa-arrow-right group-hover:translate-x-3 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <h2 class="text-3xl md:text-5xl font-black text-center text-gray-900 mb-16">Ils nous font confiance</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['name' => 'Jean-Paul M.', 'role' => 'Patient suivi pour hypertension', 'text' => "Grâce au suivi régulier d'HospitSIS, ma tension est enfin stabilisée. L'application est vraiment facile à utiliser."],
                    ['name' => 'Aminata S.', 'role' => 'Sportive de haut niveau', 'text' => "Le bilan cardiaque d'effort a été très pro. J'ai reçu mes résultats en format digital immédiatement."],
                    ['name' => 'Dr. Bernard T.', 'role' => 'Cardiologue Consultant', 'text' => "Une plateforme qui facilite réellement le lien entre le médecin et le patient pour un suivi de qualité."]
                ] as $item)
                <div class="p-8 rounded-3xl bg-slate-50 border border-transparent hover:border-orange-200 hover:bg-white hover:shadow-2xl transition-all">
                    <div class="flex gap-1 text-orange-500 mb-6">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 italic mb-8">"{{ $item['text'] }}"</p>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $item['name'] }}</h4>
                        <p class="text-xs text-orange-600 font-bold uppercase tracking-widest">{{ $item['role'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

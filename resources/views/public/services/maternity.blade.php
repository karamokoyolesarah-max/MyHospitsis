@extends('layouts.public')

@section('title', 'Maternité & Suivi de Grossesse - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 to-rose-500/5 -z-10"></div>
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="fade-up">
                    <span class="inline-block px-4 py-1 bg-pink-100 text-pink-600 rounded-full text-sm font-bold mb-6">Accompagnement Maternité</span>
                    <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Vivre votre grossesse en toute <span class="text-pink-600">sérénité</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Chez HospitSIS, nous plaçons la future maman et son bébé au cœur de nos préoccupations. Profitez d'un suivi personnalisé par nos experts pour chaque étape de cette magnifique aventure.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('select-portal') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all transform hover:scale-105">
                            Prendre rendez-vous
                        </a>
                        <a href="#details" class="bg-white border-2 border-pink-100 text-pink-600 px-8 py-4 rounded-2xl font-bold hover:bg-pink-50 transition-all">
                            En savoir plus
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-full h-[400px] md:h-[500px] rounded-[2rem] overflow-hidden shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('images/grossesse/image2.jpg') }}" alt="Maternité" class="w-full h-full object-cover">
                    </div>
                    <!-- Floating elements -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl animate-bounce">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-baby text-pink-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Bébés nés</p>
                                <p class="text-lg font-bold text-gray-900">2,500+</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Details Section -->
    <section id="details" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">Un parcours de soins complet</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">De la conception à l'accouchement, nous sommes à vos côtés.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-3xl bg-pink-50 border border-pink-100 hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-pink-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-stethoscope text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Suivi Prénatal</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Consultations mensuelles avec nos gynécologues-obstétriciens pour surveiller votre santé et celle de votre bébé.
                    </p>
                </div>

                <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-blue-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-video text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Échographies 3D/4D</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Des technologies de pointe pour visualiser votre enfant et détecter toute anomalie précocement.
                    </p>
                </div>

                <div class="p-8 rounded-3xl bg-pink-50 border border-pink-100 hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-pink-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Préparation Parentale</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Cours collectifs ou individuels pour aborder sereinement l'accouchement et les premiers jours avec bébé.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-slate-50 overflow-hidden">
        <div class="container mx-auto px-6 max-w-7xl">
            <h2 class="text-3xl md:text-5xl font-black text-center text-gray-900 mb-16">Témoignages de Mamans</h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm relative">
                    <i class="fas fa-quote-left absolute top-8 left-8 text-pink-100 text-6xl"></i>
                    <div class="relative z-10">
                        <p class="text-gray-600 italic mb-8 leading-relaxed">
                            "L'équipe d'HospitSIS a été incroyable tout au long de ma grossesse. Le suivi digital m'a permis de poser mes questions à tout moment et de me sentir rassurée."
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-pink-200"></div>
                            <div>
                                <h4 class="font-bold text-gray-900">Sarah K.</h4>
                                <p class="text-xs text-gray-500">Maman de Lucas, 3 mois</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm relative">
                    <i class="fas fa-quote-left absolute top-8 left-8 text-pink-100 text-6xl"></i>
                    <div class="relative z-10">
                        <p class="text-gray-600 italic mb-8 leading-relaxed">
                            "Les échographies 3D sont un moment magique. On voit tellement bien ! Merci pour votre professionnalisme et votre douceur."
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-200"></div>
                            <div>
                                <h4 class="font-bold text-gray-900">Marie L.</h4>
                                <p class="text-xs text-gray-500">Future maman (7ème mois)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="bg-gradient-to-r from-pink-600 to-rose-600 rounded-[3rem] p-12 text-center text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-5xl font-black mb-6">Prête à commencer l'aventure ?</h2>
                    <p class="text-pink-100 mb-10 text-lg max-w-2xl mx-auto">Rejoignez des milliers de mamans qui ont choisi HospitSIS pour un suivi de grossesse moderne et humain.</p>
                    <a href="{{ route('select-portal') }}" class="inline-block bg-white text-pink-600 px-10 py-4 rounded-2xl font-black hover:bg-pink-50 transition-all shadow-xl transform hover:scale-110">
                        Créer mon compte patient
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

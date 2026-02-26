@extends('layouts.public')

@section('title', 'Nutrition & Bien-être - HospitSIS')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden bg-green-50/30">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="fade-up">
                    <span class="inline-block px-4 py-1 bg-green-100 text-green-600 rounded-full text-sm font-bold mb-6">Équilibre & Vitalité</span>
                    <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Votre alimentation est votre <span class="text-green-600">première médecine</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Nos nutritionnistes vous accompagnent vers un mode de vie plus sain avec des programmes sur mesure adaptés à vos besoins et à vos objectifs.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('select-portal') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl transition-all transform hover:scale-105">
                            Démarrer mon programme
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-full h-[400px] md:h-[500px] rounded-[3rem] overflow-hidden shadow-2xl">
                        <img src="{{ asset('images/nutrition/image2.jpg') }}" alt="Nutrition" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-xl flex items-center gap-4">
                         <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                             <i class="fas fa-leaf"></i>
                         </div>
                         <div>
                             <p class="text-xs text-gray-500">100% Naturel</p>
                             <p class="text-lg font-bold">Bio & Santé</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl text-center">
             <div class="inline-grid md:grid-cols-3 gap-12">
                 @foreach([
                     ['t' => 'Perte de poids', 'd' => 'Programmes durables sans frustration.'],
                     ['t' => 'Nutrition Sportive', 'd' => 'Optimisez vos performances et votre récup.'],
                     ['t' => 'Pathologies', 'd' => 'Diabète, hypertension, allergies alimentaires.']
                 ] as $n)
                 <div class="text-center">
                     <h3 class="text-2xl font-black text-gray-900 mb-4">{{ $n['t'] }}</h3>
                     <p class="text-gray-600">{{ $n['d'] }}</p>
                 </div>
                 @endforeach
             </div>
        </div>
    </section>
@endsection

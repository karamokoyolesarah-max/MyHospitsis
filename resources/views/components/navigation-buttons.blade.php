@props(['backUrl' => null])

<div class="fixed top-6 left-6 flex flex-col sm:flex-row gap-4 z-[9999] pointer-events-none">
    {{-- Bouton Accueil --}}
    <a href="{{ route('home') }}" 
       class="pointer-events-auto group flex items-center justify-center w-12 h-12 bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-white/40 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 transform hover:scale-110 active:scale-95 slide-in-left"
       title="Accueil">
        <i class="fas fa-home text-lg"></i>
    </a>

    {{-- Bouton Retour --}}
    <a href="{{ $backUrl ?? url()->previous() }}" 
       class="pointer-events-auto group flex items-center gap-3 px-5 py-3 bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-white/40 text-gray-700 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105 active:scale-95 slide-in-left"
       style="animation-delay: 0.1s;">
        <i class="fas fa-arrow-left text-blue-600 group-hover:-translate-x-1 transition-transform"></i>
        <span class="font-bold text-sm">Retour</span>
    </a>
</div>

<style>
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .slide-in-left {
        animation: slideInLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>

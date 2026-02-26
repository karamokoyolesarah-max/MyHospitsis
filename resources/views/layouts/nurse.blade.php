<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'HospitSIS') - Système d'Information de Santé</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* CORRECTION DES DEUX TRAITS (Scrollbar) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #111827;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #374151 #111827;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    @include('components.notification-sound')
    @if(session('success'))
        <script>window.onload = () => window.playNotificationSound();</script>
    @endif
    @if(session('error'))
        <script>window.onload = () => window.playNotificationSound();</script>
    @endif

    <!-- Flash Messages -->
    <div class="fixed top-4 right-4 z-[9999] max-w-md w-full pointer-events-none">
        @if(session('success'))
            <div class="mb-3 bg-green-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-slide-in-right pointer-events-auto">
                <i class="bi bi-check-circle-fill text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 bg-red-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-slide-in-right pointer-events-auto">
                <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <style>
        @keyframes slide-in-right {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in-right {
            animation: slide-in-right 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>

    <div class="min-h-screen bg-gray-50">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>

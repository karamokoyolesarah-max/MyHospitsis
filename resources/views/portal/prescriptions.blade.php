<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Ordonnances - Portail Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .nav-link.active { color: #2563eb; border-bottom-color: #2563eb; background-color: #eff6ff; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">
    
    <!-- Navbar Premium -->
    <nav class="glass sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight">Mes Ordonnances</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                     <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center border border-indigo-100">
                        {{ count($prescriptions) }} Prévues
                    </span>
                    <form method="POST" action="{{ route('patient.logout') }}">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition flex items-center justify-center">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="flex space-x-1 overflow-x-auto scrollbar-hide pb-1">
                <a href="{{ route('patient.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-th-large mr-2.5"></i>Tableau de bord
                </a>
                <a href="{{ route('patient.appointments') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-calendar-check mr-2.5"></i>Rendez-vous
                </a>
                <a href="{{ route('patient.medical-history') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-file-medical-alt mr-2.5"></i>Dossier Médical
                </a>
                <a href="{{ route('patient.prescriptions') }}" class="nav-link active flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-pills mr-2.5"></i>Ordonnances
                </a>
                <a href="{{ route('patient.profile') }}" class="nav-link flex items-center px-4 py-3 text-sm font-bold text-slate-500 border-b-2 border-transparent rounded-t-xl hover:text-indigo-600 transition whitespace-nowrap">
                    <i class="fas fa-user-circle mr-2.5"></i>Profil
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
             <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Vos Ordonnances</h2>
                    <p class="text-slate-400 text-sm font-medium">Téléchargez vos prescriptions</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Médecin</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Hôpital</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($prescriptions as $prescription)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center">
                                         <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mr-4 shadow-sm text-sm font-bold">
                                            {{ $prescription->created_at->format('d') }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-900">
                                            {{ $prescription->created_at->translatedFormat('F Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div class="text-sm font-bold text-slate-900">
                                            {{ $prescription->doctor->name ?? 'Dr. Inconnu' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm text-slate-500 font-medium">
                                    {{ $prescription->hospital->name ?? 'Hôpital Général' }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('patient.prescriptions.download', $prescription->id) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-xs font-bold shadow-lg shadow-indigo-200 uppercase tracking-wide group-hover:scale-105">
                                        <i class="fas fa-cloud-download-alt mr-2"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">
                                            <i class="fas fa-file-prescription text-4xl"></i>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-900 mb-2">Aucune ordonnance</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto text-sm">
                                            Les prescriptions de vos médecins apparaîtront ici après validation.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($prescriptions->hasPages())
                <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/20">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        </div>

        <div class="mt-8 p-6 bg-blue-50/50 border border-blue-100 rounded-3xl flex items-center space-x-4">
            <div class="bg-white p-3 rounded-xl text-blue-600 shadow-sm">
                <i class="fas fa-info"></i>
            </div>
            <p class="text-sm text-blue-900/80 font-medium">
                Les ordonnances numériques sont valables dans toutes les pharmacies partenaires. Présentez le PDF sur votre téléphone.
            </p>
        </div>
    </main>

</body>
</html>
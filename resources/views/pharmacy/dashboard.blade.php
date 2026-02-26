@extends('layouts.app')

@section('title', 'Dashboard Pharmacie')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8 animate-in fade-in slide-in-from-top-4 duration-1000">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight italic uppercase">Tableau de Bord</h1>
            <p class="text-gray-500 font-medium">Système d'Information de Santé</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('pharmacy.history') }}" 
               class="bg-white hover:bg-slate-50 text-slate-900 px-6 py-3.5 rounded-[1.8rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-slate-200/50 transition-all flex items-center gap-2 hover:-translate-y-1">
                <i class="fas fa-history text-slate-400"></i> Historique
            </a>
            <button onclick="openUpdateModal()" 
                    class="bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded-[1.8rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-slate-200 transition-all flex items-center gap-3 active:scale-95 group hover:-translate-y-1">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-500"></i>
                Nouveau
            </button>
        </div>
    </div>

    @if($alerts_urgent->count() > 0)
    <div class="mb-8 bg-rose-50 border-l-4 border-rose-500 p-6 rounded-2xl flex items-center gap-6 animate-pulse shadow-xl shadow-rose-900/5">
        <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 shadow-inner">
            <i class="fas fa-biohazard fa-2x"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-rose-900 font-black uppercase text-xs tracking-[0.2em] mb-1">Alerte Critique</h4>
            <p class="text-rose-700 font-black text-sm">{{ $alerts_urgent->count() }} situation(s) requièrent votre attention immédiate (Péremption ou Rupture).</p>
        </div>
        <button onclick="document.getElementById('alerts-section').scrollIntoView({behavior: 'smooth'})" 
                class="bg-rose-600 text-white px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition-all">
            Voir les détails
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-7 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 group animate-in zoom-in-95 duration-700">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 transition-all duration-500">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Références</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['total_items'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-7 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 group animate-in zoom-in-95 duration-700 delay-75">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white group-hover:-rotate-12 transition-all duration-500">
                    <i class="fas fa-boxes-stacked text-xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Unités en Stock</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['total_units'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 group animate-in zoom-in-95 duration-700 delay-150">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white group-hover:scale-110 transition-all duration-500">
                    <i class="fas fa-skull-crossbones text-xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Périmés</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['expired_count'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 group animate-in zoom-in-95 duration-700 delay-300">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white group-hover:animate-spin-slow transition-all duration-500">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Sous 90 Jours</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $stats['soon_expired_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Center --}}
    <div id="alerts-section" class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        {{-- Section Urgences --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-rose-50/30">
                <div class="flex items-center gap-3">
                    <i class="fas fa-radiation text-rose-600 animate-spin-slow"></i>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Urgences Absolues</h3>
                </div>
                <span class="bg-rose-600 text-white text-[10px] font-black px-3 py-1 rounded-full">{{ $alerts_urgent->count() }}</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($alerts_urgent as $index => $alert)
                <div class="p-6 flex items-center justify-between hover:bg-rose-50/50 transition-all group opacity-0 animate-in fade-in slide-in-from-right-4 fill-mode-forwards"
                     style="animation-delay: {{ $index * 100 }}ms">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all duration-500 shadow-inner">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 text-sm group-hover:translate-x-1 transition-transform">{{ $alert->medication->name }}</p>
                            <p class="text-[10px] font-bold text-rose-600 uppercase">
                                @if($alert->quantity <= 0) Rupture Totale @else Périmé (Lot {{ $alert->batch_number }}) @endif
                            </p>
                        </div>
                    </div>
                    <button onclick="openUpdateModal({{ $alert->medication_id }})" class="w-10 h-10 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl transition-all active:scale-90 hover:shadow-lg">
                        <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    </button>
                </div>
                @empty
                <div class="p-12 text-center text-slate-400 font-bold italic text-sm">
                    Aucune urgence détectée. Bravo !
                </div>
                @endforelse
            </div>
        </div>

        {{-- Section Vigilance & Arrivages --}}
        <div class="space-y-8">
            {{-- Vigilance --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-amber-50/30">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-eye text-amber-600"></i>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Zone de Vigilance</h3>
                    </div>
                    <span class="bg-amber-600 text-white text-[10px] font-black px-3 py-1 rounded-full">{{ $alerts_vigilance->count() }}</span>
                </div>
                <div class="divide-y divide-slate-50 max-h-[15.5rem] overflow-y-auto">
                    @forelse($alerts_vigilance as $alert)
                    @php
                        $daysLeft = $alert->expiry_date ? now()->diffInDays($alert->expiry_date, false) : 999;
                        $isLowStock = $alert->quantity <= $alert->min_threshold;
                    @endphp
                    <div class="p-5 flex items-center justify-between hover:bg-amber-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 text-xs">{{ $alert->medication->name }}</p>
                                <div class="flex gap-2">
                                    @if($isLowStock)
                                    <span class="text-[9px] font-black text-amber-600 uppercase">Stock bas: {{ $alert->quantity }} un.</span>
                                    @endif
                                    @if($daysLeft <= 90 && $daysLeft > 0)
                                    <span class="text-[9px] font-black text-slate-500 uppercase">Exp. dans {{ $daysLeft }} j</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button onclick="openUpdateModal({{ $alert->medication_id }})" class="p-2 text-slate-400 hover:text-slate-900">
                            <i class="fas fa-shopping-cart text-xs"></i>
                        </button>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 font-bold italic text-xs">Tout est sous contrôle.</div>
                    @endforelse
                </div>
            </div>

            {{-- Derniers Arrivages --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 flex items-center gap-3 bg-blue-50/30">
                    <i class="fas fa-truck-ramp-box text-blue-600"></i>
                    <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Nouveaux Arrivages</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($recentArrivals as $arrival)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="font-black text-slate-800 text-[11px]">{{ $arrival->stock->medication->name }}</p>
                            <p class="text-[9px] text-slate-400 font-bold italic">{{ $arrival->created_at->format('d/m/Y') }} à {{ $arrival->created_at->format('H:i') }}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-3 py-1 rounded-lg">+{{ $arrival->quantity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-black text-gray-900 uppercase">Inventaire Actuel</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Rechercher..." class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Médicament</th>
                        <th class="px-6 py-4">Forme/Dosage</th>
                        <th class="px-6 py-4">Quantité</th>
                        <th class="px-6 py-4">Lot / Exp</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stocks as $stock)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $stock->medication->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $stock->medication->form }} {{ $stock->medication->dosage }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-black text-gray-700">
                                {{ $stock->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-500">
                            {{ $stock->batch_number ?? 'N/A' }}<br>
                            {{ $stock->expiry_date ? $stock->expiry_date->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($stock->quantity <= 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">Rupture</span>
                            @elseif($stock->quantity <= $stock->min_threshold)
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">Stock Bas</span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">Disponible</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openUpdateModal({{ $stock->medication_id }})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Mettre à jour le stock">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-bold italic">
                            Aucun médicament en stock.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Mise à Jour Stock --}}
<div id="modal-update-stock" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl relative">
        <button onclick="document.getElementById('modal-update-stock').classList.add('hidden')" 
                class="absolute top-6 right-6 text-gray-400 hover:text-gray-900 tranisiton">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h2 class="text-2xl font-black text-gray-900 mb-6 uppercase tracking-tight">Mouvement de Stock</h2>
        
        <form action="{{ route('pharmacy.stock.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Médicament</label>
                    <select name="medication_id" id="medication_select" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(App\Models\Medication::all() as $med)
                            <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->dosage }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Quantité</label>
                        <input type="number" name="quantity" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Type</label>
                        <select name="type" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="entry">Entrée (Achats)</option>
                            <option value="exit">Sortie (Usage/Vente)</option>
                            <option value="adjustment">Ajustement Inventaire</option>
                            <option value="expired">Périmé</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">N° Lot</label>
                        <input type="text" name="batch_number" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Date Péremption</label>
                        <input type="date" name="expiry_date" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Raison / Notes</label>
                    <textarea name="reason" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-900/20 transition-all uppercase tracking-widest mt-4">
                    Valider le Mouvement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUpdateModal(medId = null) {
    const modal = document.getElementById('modal-update-stock');
    const select = document.getElementById('medication_select');
    
    if (medId) {
        select.value = medId;
    }
    
    modal.classList.remove('hidden');
}
</script>
@endpush

@extends('layouts.app')
@section('title', 'Catalogue Médicaments | HospitSIS')
@section('content')
<div class="p-4 md:p-8 space-y-8 bg-slate-50/50 min-h-screen">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-in fade-in slide-in-from-top-4 duration-1000 print:hidden">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 group-hover:rotate-12 transition-transform duration-500">
                    <i class="fas fa-pills text-lg"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight uppercase italic">Catalogue Médicaments</h1>
            </div>
            <p class="text-slate-500 font-bold text-sm flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                Référentiel global & Pharmaceutique • {{ $medications->count() }} références
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" 
               class="px-5 py-3 bg-white text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/50 hover:-translate-y-1 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-print text-slate-400"></i> Exporter
            </button>
            <a href="{{ route('pharmacy.catalog', ['filter' => 'expired']) }}" 
               class="px-5 py-3 {{ request('filter') == 'expired' ? 'bg-rose-600 text-white shadow-rose-200' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }} rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl transition-all flex items-center gap-2 hover:-translate-y-1 active:scale-95 hover:shadow-rose-100">
                <i class="fas fa-history"></i> Périmés
            </a>
            <a href="{{ route('pharmacy.catalog', ['filter' => 'out_of_stock']) }}" 
               class="px-5 py-3 {{ request('filter') == 'out_of_stock' ? 'bg-amber-600 text-white shadow-amber-200' : 'bg-amber-50 text-amber-600 hover:bg-amber-100' }} rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl transition-all flex items-center gap-2 hover:-translate-y-1 active:scale-95 hover:shadow-amber-100">
                <i class="fas fa-exclamation-triangle"></i> En Rupture
            </a>
            <button onclick="document.getElementById('modal-add-med').classList.remove('hidden')" 
                    class="bg-slate-900 hover:bg-black text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-slate-200 transition-all flex items-center gap-3 active:scale-95 group hover:-translate-y-1">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-500"></i>
                Nouveau
            </button>
        </div>
    </div>
    
    {{-- Print-only Header (Hidden on screen) --}}
    <div class="hidden print:block mb-8 border-b-2 border-slate-900 pb-4">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tighter">HospitSIS • Catalogue Pharmaceutique</h1>
                <p class="font-bold text-slate-500 italic">Généré le: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="font-black text-xs uppercase tracking-widest">Référentiel Médical</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $medications->count() }} Références Actives</p>
            </div>
        </div>
    </div>

    {{-- Search & Filters Bar --}}
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-100 print:hidden">
        <form action="{{ route('pharmacy.catalog') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative group">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 placeholder:text-slate-400 text-sm"
                       placeholder="Rechercher par nom, marque ou molécule...">
            </div>

            <div>
                <select name="category" onchange="this.form.submit()"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative">
                <select name="form" onchange="this.form.submit()"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                    <option value="">Toutes les formes</option>
                    @foreach($forms as $f)
                        <option value="{{ $f }}" {{ request('form') == $f ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            @if(request()->anyFilled(['search', 'category', 'form', 'filter']))
            <div class="md:col-span-4 flex justify-end">
                <a href="{{ route('pharmacy.catalog') }}" class="text-xs font-black text-blue-600 hover:text-black uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-times-circle"></i> Effacer les filtres et voir tout
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- Medications Table --}}
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="px-8 py-6">Informations Produit</th>
                        <th class="px-8 py-6">Molécule (DCI)</th>
                        <th class="px-8 py-6">Classe Thérapeutique</th>
                        <th class="px-8 py-6">Forme & Dosage</th>
                        <th class="px-8 py-6 text-right">Prix Unitaire</th>
                        <th class="px-8 py-6 text-center">Statut</th>
                        <th class="px-8 py-6 text-right print:hidden">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($medications as $index => $med)
                    <tr class="hover:bg-blue-50/40 transition-all duration-300 group opacity-0 animate-in fade-in slide-in-from-bottom-2 fill-mode-forwards"
                        style="animation-delay: {{ $index * 50 }}ms">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-[10deg] group-hover:scale-110 transition-all duration-500 shadow-inner print:hidden">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div>
                                    <div class="font-black text-slate-900 text-sm uppercase group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-300">{{ $med->name }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $med->brand_name ?? 'Générique' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 group-hover:bg-white group-hover:border-blue-100 transition-all print:bg-white print:border-none print:px-0">{{ $med->active_ingredient ?? '-' }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-blue-600/70 italic group-hover:text-blue-600 transition-colors">{{ $med->therapeutic_class ?? '-' }}</span>
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <div class="font-black text-slate-700">{{ $med->form ?? '-' }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $med->dosage ?? '-' }}</div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="font-black text-slate-900 text-base group-hover:scale-110 transition-transform origin-right">{{ number_format($med->unit_price, 0, ',', ' ') }} <span class="text-[10px] text-slate-400">FCFA</span></div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($med->is_active)
                                <div class="relative inline-flex">
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm group-hover:shadow-emerald-100 transition-all print:bg-white print:text-emerald-700 print:font-bold print:border-none">Actif</span>
                                    <span class="absolute -top-1 -right-1 flex h-2 w-2 print:hidden">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                </div>
                            @else
                                <span class="bg-slate-100 text-slate-400 border border-slate-200 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest print:hidden">Inactif</span>
                                <span class="hidden print:inline text-slate-400 font-bold">Inactif</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right print:hidden">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditMedModal({{ $med }})" class="w-10 h-10 bg-slate-50 hover:bg-blue-600 hover:text-white text-slate-400 rounded-xl transition-all duration-300 flex items-center justify-center border border-slate-100 active:scale-90 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-100" title="Éditer">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="w-10 h-10 bg-slate-50 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-xl transition-all duration-300 flex items-center justify-center border border-slate-100 active:scale-90 hover:-translate-y-1 hover:shadow-lg hover:shadow-rose-100" title="Désactiver">
                                    <i class="fas fa-power-off text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-32 text-center bg-slate-50/30">
                            <div class="max-w-xs mx-auto space-y-4">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto border-4 border-white shadow-lg">
                                    <i class="fas fa-search-minus text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-black uppercase tracking-widest text-xs">Aucun résultat</p>
                                    <p class="text-slate-400 font-medium text-xs mt-1">Nous n'avons trouvé aucun médicament correspondant à vos critères.</p>
                                </div>
                                <a href="{{ route('pharmacy.catalog') }}" class="inline-block text-blue-600 font-black text-[10px] uppercase tracking-widest hover:underline mt-4">Voir tout le catalogue</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Ajout Médicament --}}
<div id="modal-add-med" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-300">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] p-8 md:p-12 shadow-2xl relative overflow-hidden animate-in zoom-in-95 duration-500">
        <div class="absolute top-0 right-0 p-8">
            <button onclick="document.getElementById('modal-add-med').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-900 rounded-2xl transition-all active:scale-90">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-10">
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Nouveau Médicament</h2>
            <p class="text-slate-400 font-bold text-sm tracking-wide uppercase mt-1">Ajout au référentiel global</p>
        </div>
        
        <form action="{{ route('pharmacy.catalog.store') }}" method="POST">
            @csrf
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Désignation Officielle *</label>
                        <input type="text" name="name" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700"
                               placeholder="ex: PARACETAMOL 500MG">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Nom Commercial (Marque)</label>
                        <input type="text" name="brand_name" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700"
                               placeholder="ex: DOLIPRANE">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Molécule (DCI)</label>
                        <input type="text" name="active_ingredient" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700"
                               placeholder="ex: Paracétamol">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Classe Thérapeutique</label>
                        <input type="text" name="therapeutic_class" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700"
                               placeholder="ex: Analgésique">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Forme Galénique</label>
                        <select name="form" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 appearance-none cursor-pointer">
                            <option value="Tablet">Comprimé</option>
                            <option value="Capsule">Gélule</option>
                            <option value="Syrup">Sirop</option>
                            <option value="Injection">Injection</option>
                            <option value="Cream">Pommade</option>
                            <option value="Drops">Gouttes</option>
                            <option value="Inhaler">Inhalateur</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Dosage</label>
                        <input type="text" name="dosage" placeholder="ex: 500mg" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Prix Unitaire (FCFA)</label>
                        <input type="number" name="unit_price" placeholder="0" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 mb-2">Catégorie / Rayon</label>
                    <input type="text" name="category" placeholder="ex: Diabète, Douleurs, etc." 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-black text-white font-black py-5 rounded-[2rem] shadow-2xl shadow-blue-200 transition-all uppercase tracking-[0.2em] text-xs mt-6 active:scale-95">
                    Ajouter au Catalogue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Modal Édition Médicament --}}
<div id="modal-edit-med" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-300">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] p-8 md:p-12 shadow-2xl relative overflow-hidden animate-in zoom-in-95 duration-500">
        <div class="absolute top-0 right-0 p-8">
            <button onclick="document.getElementById('modal-edit-med').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-900 rounded-2xl transition-all active:scale-90">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-10">
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Modifier Médicament</h2>
            <p class="text-slate-400 font-bold text-sm tracking-wide uppercase mt-1">Édition du référentiel</p>
        </div>
        
        <form id="form-edit-med" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Désignation Officielle *</label>
                        <input type="text" name="name" id="edit_name" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Nom Commercial (Marque)</label>
                        <input type="text" name="brand_name" id="edit_brand_name" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Molécule (DCI)</label>
                        <input type="text" name="active_ingredient" id="edit_active_ingredient" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Classe Thérapeutique</label>
                        <input type="text" name="therapeutic_class" id="edit_therapeutic_class" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Forme Galénique</label>
                        <select name="form" id="edit_form" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700 appearance-none cursor-pointer">
                            <option value="Tablet">Comprimé</option>
                            <option value="Capsule">Gélule</option>
                            <option value="Syrup">Sirop</option>
                            <option value="Injection">Injection</option>
                            <option value="Cream">Pommade</option>
                            <option value="Drops">Gouttes</option>
                            <option value="Inhaler">Inhalateur</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Dosage</label>
                        <input type="text" name="dosage" id="edit_dosage"
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Prix Unitaire (FCFA)</label>
                        <input type="number" name="unit_price" id="edit_unit_price"
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-bold text-slate-700">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-black text-white font-black py-5 rounded-[2rem] shadow-2xl shadow-blue-200 transition-all uppercase tracking-[0.2em] text-xs mt-6 active:scale-95">
                    Sauvegarder les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditMedModal(med) {
    const modal = document.getElementById('modal-edit-med');
    const form = document.getElementById('form-edit-med');
    
    // Set form action
    form.action = `/pharmacy/catalog/${med.id}`;
    
    // Fill fields
    document.getElementById('edit_name').value = med.name || '';
    document.getElementById('edit_brand_name').value = med.brand_name || '';
    document.getElementById('edit_active_ingredient').value = med.active_ingredient || '';
    document.getElementById('edit_therapeutic_class').value = med.therapeutic_class || '';
    document.getElementById('edit_form').value = med.form || 'Tablet';
    document.getElementById('edit_dosage').value = med.dosage || '';
    document.getElementById('edit_unit_price').value = med.unit_price || 0;
    
    modal.classList.remove('hidden');
}
</script>
@endpush

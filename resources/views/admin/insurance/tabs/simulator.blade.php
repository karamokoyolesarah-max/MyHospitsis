<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
    <!-- Left Column: Simulator -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fas fa-microchip text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Testeur de Connectivités</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Simulation de droits patient</p>
                    </div>
                </div>

                <form action="{{ route('admin.insurance.test') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="relative group/input">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Numéro de Matricule / Carte</label>
                        <div class="relative">
                            <input type="text" name="matricule" required
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 focus:bg-white focus:border-blue-600 transition-all outline-none"
                                placeholder="Ex: 11223344 (Valide)">
                            <button type="submit" 
                                class="absolute right-2 top-2 bottom-2 px-6 bg-gray-900 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">
                                Tester
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex-shrink-0 flex items-center justify-center text-amber-600">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="text-[10px] text-amber-800 leading-relaxed font-medium">
                        <span class="font-black block uppercase mb-1">Mode Simulation Actif :</span>
                        Utilisez <span class="font-black text-amber-900 underline">11</span> pour un succès (80%) et <span class="font-black text-amber-900 underline">00</span> pour un échec.
                    </div>
                </div>
            </div>

            @if(session('insurance_result'))
            <div class="border-t border-gray-50 bg-gray-50/50 p-8">
                @php $res = session('insurance_result'); @endphp
                <div class="flex items-center gap-6">
                    @if($res['status'] === 'valide')
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl animate-bounce">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-emerald-900 uppercase">Droits Confirmés</h4>
                            <p class="text-sm font-bold text-emerald-700 mt-1">{{ $res['patient'] ?? 'Patient Valide' }} (Couverture : {{ $res['couverture'] }}%)</p>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-times"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-rose-900 uppercase">Échec Vérification</h4>
                            <p class="text-sm font-bold text-rose-700 mt-1">{{ $res['message'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <div class="bg-gray-900 rounded-[2rem] p-8 text-white">
            <h3 class="text-sm font-black uppercase tracking-widest mb-6 py-1 border-b border-white/10">Active Settings</h3>
            <div class="space-y-4">
                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-[10px] font-black text-gray-500 uppercase mb-2">Service Actif</p>
                    <p class="text-sm font-bold">{{ $providerName }}</p>
                </div>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-[10px] font-black text-gray-500 uppercase mb-2">Statut Réseau</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-sm font-bold">Connecté</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

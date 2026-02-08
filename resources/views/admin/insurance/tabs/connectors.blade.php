<div class="space-y-8 animate-fade-in">
    <!-- Form to add connector -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Nouveau Connecteur</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Branchez un nouvel assureur</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.insurance.store-connector') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nom de l'Assureur</label>
                <input type="text" name="name" required placeholder="Ex: AXA Assurances"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 focus:bg-white focus:border-indigo-600 transition-all outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Type de Provider</label>
                <select name="provider_type" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-indigo-600 transition-all outline-none appearance-none">
                    <option value="cnam">CNAM (Public)</option>
                    <option value="saham">SAHAM (Privé)</option>
                    <option value="axa">AXA (Privé)</option>
                    <option value="custom text-gray-400">Autre / Custom API</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Clé API / Token de Connexion</label>
                <input type="password" name="api_key" placeholder="••••••••••••••••"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 focus:bg-white focus:border-indigo-600 transition-all outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">URL de l'API (Endpoint)</label>
                <input type="url" name="base_url" placeholder="https://api.votreassurance.com/v1"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 focus:bg-white focus:border-indigo-600 transition-all outline-none">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-200 transition-all active:scale-95">
                    Activer le Connecteur
                </button>
            </div>
        </form>
    </div>

    <!-- Active Connectors List -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Connecteurs Actifs</h3>
        </div>
        <div class="p-0">
            @if($connectors->isEmpty())
                <div class="p-12 text-center text-gray-400 font-bold text-sm">
                    Aucun connecteur configuré pour le moment.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-left border-b border-gray-100">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Connecteur</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($connectors as $con)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black">
                                            {{ strtoupper(substr($con->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-900">{{ $con->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-gray-100 rounded-lg text-[10px] font-black uppercase text-gray-600 tracking-tighter">{{ $con->provider_type }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $con->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                        <span class="text-xs font-bold text-gray-600">{{ $con->is_active ? 'Actif' : 'Désactivé' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

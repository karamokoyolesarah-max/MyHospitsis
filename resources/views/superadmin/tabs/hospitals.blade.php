<div id="tab-hospitals" class="tab-pane animate-in slide-in-from-bottom-8 duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 text-left gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Gestion des Hôpitaux</h2>
            <p class="text-slate-500 font-medium">Déployez et supervisez les infrastructures médicales.</p>
        </div>
        <button onclick="openInstallModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold transition shadow-xl shadow-blue-200 flex items-center justify-center gap-3 group">
            <i class="bi bi-plus-lg group-hover:scale-125 transition-transform"></i>
            Installer un Hôpital
        </button>
    </div>
    
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden text-left">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-6">Identité Hôpital</th>
                        <th class="px-8 py-6">Offre Souscrite</th>
                        <th class="px-8 py-6 text-center">Statut</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($hospitals as $hospital)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                    <i class="bi bi-hospital"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-base tracking-tight">{{ $hospital->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter flex items-center gap-1">
                                        <i class="bi bi-geo-alt-fill"></i> {{ $hospital->address ?? 'Adresse non spécifiée' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-100/50">
                                Premium Plan
                            </span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       {{ $hospital->is_active ? 'checked' : '' }}
                                       onchange="toggleHospitalStatus({{ $hospital->id }}, this.checked, this)"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ $hospital->is_active ? 'ACTIF' : 'INACTIF' }}
                                </span>
                            </label>
                        </td>
                        <td class="px-8 py-6 text-right text-lg">
                            <button onclick="openHospitalDetails({{ $hospital->id }})"
                                    class="text-slate-300 hover:text-blue-600 p-2 hover:bg-white rounded-xl shadow-sm transition-all"
                                    title="Voir les détails">
                                <i class="bi bi-gear-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-slate-400">
                            <i class="bi bi-hospital text-4xl mb-4 block"></i>
                            <div class="font-bold">Aucun hôpital trouvé</div>
                            <div class="text-sm">Commencez par installer votre premier hôpital</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

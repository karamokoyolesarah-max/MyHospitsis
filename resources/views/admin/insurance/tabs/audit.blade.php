<div class="space-y-8 animate-fade-in">
    <!-- Alerts & Stats Header -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Distribution by Insurer -->
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-50 pb-4">Part de Marché Assureurs</h3>
            <div class="space-y-4">
                @foreach($stats as $stat)
                @php $percentage = ($stat->count / max(1, $stats->sum('count'))) * 100; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-black text-gray-600 uppercase">{{ $stat->insurance_name }}</span>
                        <span class="text-[10px] font-black text-blue-600">{{ round($percentage) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Fraud Summary -->
        <div class="bg-rose-600 p-8 rounded-[2rem] shadow-xl text-white">
            <h3 class="text-sm font-black uppercase tracking-widest mb-6 opacity-80">Rapport de Vigilance</h3>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-4xl font-black">{{ $fraudAlertsCount }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Alertes Fraude / Invalide</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl">
                    <i class="fas fa-user-secret text-rose-200"></i>
                </div>
            </div>
            <div class="p-4 bg-white/10 rounded-2xl border border-white/10">
                <p class="text-[10px] font-medium leading-relaxed italic opacity-80">
                    "Surveillez les tentatives répétées de cartes expirées signalées par la caisse pour prévenir les pertes de revenus."
                </p>
            </div>
        </div>
    </div>

    <!-- Verification History (Live Logs) -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Logs de Vérification Temps-Réel</h3>
        </div>
        <div class="p-0">
            @if($logs->isEmpty())
                <div class="p-12 text-center text-gray-400 font-bold text-sm">
                    Aucun historique de vérification disponible.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs font-bold">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 uppercase text-[10px] font-black text-gray-400 tracking-widest">
                                <th class="px-8 py-4">Horodatage</th>
                                <th class="px-8 py-4">Matricule</th>
                                <th class="px-8 py-4">Statut</th>
                                <th class="px-8 py-4">Provider</th>
                                <th class="px-8 py-4">Message / Erreur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr class="border-b border-gray-50 group hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-4 text-gray-400 font-medium">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-8 py-4 text-gray-700 tracking-tight">{{ $log->matricule }}</td>
                                <td class="px-8 py-4">
                                    @if($log->status === 'valide')
                                        <span class="text-emerald-600 flex items-center gap-1"><i class="fas fa-check-circle text-[10px]"></i> VALIDE</span>
                                    @elseif($log->status === 'expire')
                                        <span class="text-rose-600 flex items-center gap-1 animate-pulse"><i class="fas fa-times-circle text-[10px]"></i> EXPIRÉ</span>
                                    @else
                                        <span class="text-gray-400 flex items-center gap-1"><i class="fas fa-question-circle text-[10px]"></i> INCONNU</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-blue-600 uppercase tracking-tighter">{{ $log->provider_name }}</td>
                                <td class="px-8 py-4 text-gray-500 font-medium">{{ $log->response_message }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

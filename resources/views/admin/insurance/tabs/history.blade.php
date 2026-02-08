<div class="space-y-8 animate-fade-in">
    <!-- Settlement History Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-emerald-50/30">
            <div>
                <h3 class="text-sm font-black text-emerald-900 uppercase tracking-widest">Historique des Paiements Reçus</h3>
                <p class="text-[10px] text-emerald-600 font-bold uppercase mt-1">Créances d'assurance validées et encaissées</p>
            </div>
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        <div class="p-0">
            @if($receivedPayments->isEmpty())
                <div class="p-12 text-center text-gray-400 font-bold text-sm">
                    Aucun paiement reçu enregistré pour le moment.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date Règlement</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Facture #</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Assureur</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Montant Encaissé</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receivedPayments as $inv)
                            <tr class="border-b border-gray-50 hover:bg-emerald-50/20 transition-colors">
                                <td class="px-8 py-5 text-xs font-bold text-gray-600">
                                    {{ $inv->insurance_settled_at ? $inv->insurance_settled_at->format('d/m/Y') : $inv->updated_at->format('d/m/Y') }}
                                </td>
                                <td class="px-8 py-5 font-black text-gray-900 text-xs">#{{ $inv->invoice_number }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg uppercase tracking-tighter">{{ $inv->insurance_name }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs font-bold text-gray-700">{{ $inv->patient->name ?? 'Inconnu' }}</p>
                                </td>
                                <td class="px-8 py-5 font-black text-emerald-600 text-xs text-right">
                                    {{ number_format(($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100, 0, ',', ' ') }} F
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black rounded-full uppercase">Réglée</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100 italic text-[10px] text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i> Ces montants ont été automatiquement ajoutés à la trésorerie de l'hôpital lors de la validation.
                </div>
                <div class="p-6 border-t border-gray-100">
                    {{ $receivedPayments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

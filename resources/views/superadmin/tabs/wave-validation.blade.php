<div id="tab-wave-validation" class="tab-pane">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-12">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-8 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden mb-6">
                <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Validation <span translate="no">Wave</span></h2>
                        <p class="text-blue-100 font-medium">Gestion des flux de rechargement manuel via Mobile Money</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md px-6 py-3 rounded-2xl flex items-center gap-2 border border-white/30">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        <span class="text-sm font-bold uppercase tracking-widest">{{ $pendingRecharges->total() }} Demandes en attente</span>
                    </div>
                </div>
                <i class="bi bi-coin absolute -right-4 -bottom-4 text-white/10 text-9xl"></i>
            </div>
        </div>

        <!-- Main Table -->
        <div class="col-lg-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-wallet2 text-blue-600"></i>
                        Rechargements à traiter
                    </h3>
                </div>
                
                @if($pendingRecharges->isEmpty())
                    <div class="px-8 py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="bi bi-check2-circle text-4xl text-slate-300"></i>
                        </div>
                        <p class="text-slate-500 font-bold text-lg">Aucune demande en attente pour le moment.</p>
                        <p class="text-slate-400 text-sm mt-1">Tout est à jour !</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Médecin</th>
                                    <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Montant</th>
                                    <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Transaction</th>
                                    <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($pendingRecharges as $recharge)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-lg shadow-sm">
                                                {{ strtoupper(substr($recharge->medecinExterne->prenom ?? 'D', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900">Dr. {{ $recharge->medecinExterne->prenom }} {{ $recharge->medecinExterne->nom }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $recharge->medecinExterne->specialite ?? 'Spécialiste' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-lg font-black text-slate-900">{{ number_format($recharge->amount, 0, ',', ' ') }} <span class="text-xs text-slate-400">FCFA</span></div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">VIA {{ $recharge->phone_number }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-mono text-sm text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl w-fit">
                                            {{ $recharge->cinetpay_transaction_id ?? $recharge->transaction_id }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold mt-1.5 flex items-center gap-1">
                                            <i class="bi bi-clock"></i>
                                            {{ $recharge->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('superadmin.wave.validate', $recharge) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="p-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all hover:scale-105 active:scale-95" title="Valider" onclick="return confirm('Créditer ce médecin ?')">
                                                    <i class="bi bi-check-lg text-lg"></i>
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $recharge->id }})" class="p-3 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 rounded-2xl transition-all hover:scale-105 active:scale-95 shadow-sm" title="Rejeter">
                                                <i class="bi bi-x-lg text-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Stats & History -->
        <div class="col-lg-4">
            <!-- Revenue Card -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-slate-200 relative overflow-hidden mb-6">
                <div class="relative z-10">
                    <h4 class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-2">Total Validé (Canal Wave)</h4>
                    @php $totalWave = \App\Models\ExternalDoctorRecharge::where('requires_manual_validation', true)->where('status', 'completed')->sum('amount'); @endphp
                    <div class="text-4xl font-black tracking-tighter mb-4">{{ number_format($totalWave, 0, ',', ' ') }} <span class="text-xl">FCFA</span></div>
                    <div class="flex items-center gap-2 bg-white/10 p-2 rounded-2xl">
                        <i class="bi bi-graph-up-arrow text-emerald-400"></i>
                        <span class="text-xs font-bold text-slate-300 uppercase">Performance du flux manuel</span>
                    </div>
                </div>
                <i class="bi bi-coin absolute -right-4 -top-4 text-white/5 text-8xl"></i>
            </div>

            <!-- History Card -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 font-bold text-slate-800 flex items-center justify-between">
                    <span>Traités récemment</span>
                    <i class="bi bi-clock-history text-blue-600"></i>
                </div>
                <div class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto">
                    @forelse($recentValidated as $recharge)
                        <div class="p-5 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $recharge->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $recharge->status === 'completed' ? 'Validé' : 'Rejeté' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold">{{ $recharge->validated_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="font-bold text-slate-900 text-sm">Dr. {{ $recharge->medecinExterne->nom ?? 'Inconnu' }}</div>
                            <div class="text-xs font-bold text-slate-500 uppercase">{{ number_format($recharge->amount, 0, ',', ' ') }} FCFA</div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm lowercase italic">Aucun historique récent</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejet (Inclus dans le tab pour simplicité, ou géré globalement) -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-100 duration-300">
        <div class="p-10">
            <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mb-6">
                <i class="bi bi-exclamation-triangle text-rose-500 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Rejeter la demande</h3>
            <p class="text-slate-500 font-medium mb-8">Veuillez indiquer la raison du rejet. Le médecin sera notifié par SMS.</p>
            
            <form id="rejectForm" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Motif du rejet</label>
                    <textarea name="rejection_reason" required rows="4" 
                              class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-rose-500 focus:bg-white transition-all outline-none font-medium resize-none"
                              placeholder="Ex: Paiement non reçu, référence invalide..."></textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 py-4 px-6 border-2 border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 py-4 px-6 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-md transition-all">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin-system/wave-validation/${id}/reject`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
    
    // Fermer en cliquant à l'extérieur
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>

<div id="tab-patient-payments" class="tab-pane animate-in fade-in duration-500">

    <!-- Header + Auto-refresh indicator -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white">
                    <i class="bi bi-credit-card-2-front text-lg"></i>
                </div>
                Suivi des Paiements Patients
            </h2>
            <p class="text-sm text-slate-500 mt-1">Vue en temps réel de tous les paiements effectués par les patients</p>
        </div>
        <div class="flex items-center gap-3">
            <div id="pp_live_indicator" class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-full">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Live</span>
            </div>
            <button onclick="refreshPatientPayments()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                <i class="bi bi-arrow-clockwise"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-emerald-50 p-2 rounded-xl text-emerald-600"><i class="bi bi-check-circle-fill text-lg"></i></div>
                <span class="text-emerald-600 text-[10px] font-black bg-emerald-50 px-2 py-0.5 rounded-full uppercase">Confirmés</span>
            </div>
            <div class="text-3xl font-black text-slate-900" id="pp_stat_confirmed">{{ $patientPayments->where('payment_transaction_id', '!=', null)->count() }}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Paiements confirmés</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-amber-50 p-2 rounded-xl text-amber-600"><i class="bi bi-hourglass-split text-lg"></i></div>
                <span class="text-amber-600 text-[10px] font-black bg-amber-50 px-2 py-0.5 rounded-full uppercase">En attente</span>
            </div>
            <div class="text-3xl font-black text-slate-900" id="pp_stat_pending">{{ $patientPayments->where('payment_transaction_id', null)->where('patient_confirmation_end_at', '!=', null)->count() }}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">En attente de paiement</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-blue-50 p-2 rounded-xl text-blue-600"><i class="bi bi-cash-stack text-lg"></i></div>
            </div>
            <div class="text-3xl font-black text-slate-900" id="pp_stat_total_amount">{{ number_format($patientPayments->where('payment_transaction_id', '!=', null)->sum('total_amount'), 0, ',', ' ') }}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Montant total (FCFA)</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600"><i class="bi bi-calendar-check text-lg"></i></div>
                <span class="text-indigo-600 text-[10px] font-black bg-indigo-50 px-2 py-0.5 rounded-full uppercase">Ce mois</span>
            </div>
            <div class="text-3xl font-black text-slate-900" id="pp_stat_monthly">{{ $patientPayments->where('payment_transaction_id', '!=', null)->filter(fn($a) => $a->updated_at->month === now()->month)->count() }}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Paiements ce mois</div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="pp_table">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Médecin</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Méthode</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Réf. Transaction</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patientPayments as $appt)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($appt->patient->prenom ?? 'P', 0, 1)) }}{{ strtoupper(substr($appt->patient->nom ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-900">{{ $appt->patient->full_name ?? 'Patient inconnu' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $appt->patient->phone ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($appt->medecinExterne)
                                    <div class="font-bold text-sm text-slate-700">Dr. {{ $appt->medecinExterne->prenom }} {{ $appt->medecinExterne->nom }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $appt->medecinExterne->specialite ?? 'Généraliste' }}</div>
                                @elseif($appt->doctor)
                                    <div class="font-bold text-sm text-slate-700">{{ $appt->doctor->name }}</div>
                                    <div class="text-[10px] text-slate-400">Interne</div>
                                @else
                                    <span class="text-slate-400 text-sm">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($appt->consultation_type === 'home')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-lg text-[10px] font-bold uppercase">
                                        <i class="bi bi-house-door-fill"></i> Domicile
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase">
                                        <i class="bi bi-hospital"></i> Hôpital
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-black text-sm text-slate-900">{{ number_format($appt->total_amount ?? 0, 0, ',', ' ') }} <span class="text-[10px] text-slate-400">FCFA</span></div>
                            </td>
                            <td class="px-6 py-4">
                                @if($appt->payment_method)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold uppercase">
                                        <i class="bi bi-phone"></i> {{ $appt->payment_method }}
                                    </span>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($appt->payment_transaction_id)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Payé
                                    </span>
                                @elseif($appt->patient_confirmation_end_at)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-black uppercase">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> En attente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-50 text-slate-500 rounded-full text-[10px] font-black uppercase">
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> En cours
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($appt->payment_transaction_id)
                                    <code class="text-[10px] bg-slate-100 px-2 py-1 rounded font-mono text-slate-600">{{ $appt->payment_transaction_id }}</code>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-700">{{ $appt->appointment_datetime->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $appt->appointment_datetime->format('H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="text-slate-300 text-5xl mb-4"><i class="bi bi-credit-card-2-front"></i></div>
                                <div class="font-bold text-slate-500">Aucun paiement à afficher</div>
                                <div class="text-sm text-slate-400 mt-1">Les paiements apparaîtront ici en temps réel</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        let ppRefreshInterval;
        
        function startPatientPaymentsRefresh() {
            // Refresh every 30 seconds
            ppRefreshInterval = setInterval(() => {
                refreshPatientPayments();
            }, 30000);
        }

        function stopPatientPaymentsRefresh() {
            if (ppRefreshInterval) clearInterval(ppRefreshInterval);
        }

        function refreshPatientPayments() {
            const indicator = document.getElementById('pp_live_indicator');
            indicator.classList.add('bg-blue-50', 'border-blue-200');
            indicator.classList.remove('bg-emerald-50', 'border-emerald-200');
            indicator.querySelector('span:last-child').textContent = 'Actualisation...';

            fetch("{{ route('superadmin.patient-payments.data') }}", {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Update stats
                    document.getElementById('pp_stat_confirmed').textContent = data.stats.confirmed;
                    document.getElementById('pp_stat_pending').textContent = data.stats.pending;
                    document.getElementById('pp_stat_total_amount').textContent = data.stats.total_amount;
                    document.getElementById('pp_stat_monthly').textContent = data.stats.monthly;

                    // Update table
                    const tbody = document.querySelector('#pp_table tbody');
                    if (data.html) {
                        tbody.innerHTML = data.html;
                    }
                }

                // Reset indicator
                indicator.classList.remove('bg-blue-50', 'border-blue-200');
                indicator.classList.add('bg-emerald-50', 'border-emerald-200');
                indicator.querySelector('span:last-child').textContent = 'Live';
            })
            .catch(() => {
                indicator.classList.remove('bg-blue-50', 'border-blue-200');
                indicator.classList.add('bg-emerald-50', 'border-emerald-200');
                indicator.querySelector('span:last-child').textContent = 'Live';
            });
        }

        // Start auto-refresh when the tab is active
        const ppTabBtn = document.getElementById('btn-patient-payments');
        if (ppTabBtn) {
            ppTabBtn.addEventListener('click', () => startPatientPaymentsRefresh());
        }
    </script>
</div>

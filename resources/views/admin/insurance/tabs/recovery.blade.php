<div class="space-y-8 animate-fade-in">
    <!-- Stats Row for Recovery -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Dossiers Attente</p>
            <p class="text-2xl font-black text-amber-600">{{ number_format($totalPending, 0, ',', ' ') }} <span class="text-xs text-gray-400">FCFA</span></p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-5">
                <i class="fas fa-building text-4xl"></i>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Assureur le plus sollicité</p>
            <p class="text-xl font-black text-blue-600 uppercase">{{ $stats->sortByDesc('count')->first()?->insurance_name ?? 'N/A' }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nombre de Factures</p>
            <p class="text-2xl font-black text-gray-900">{{ $pendingInvoices->total() }}</p>
        </div>
    </div>

    <!-- Pending Invoices Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-amber-50/20">
            <div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Factures en Attente de Règlement Assureur</h3>
                <p class="text-[10px] text-amber-600 font-bold uppercase mt-1">Veuillez valider dès réception du virement</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.finance.bordereau') }}" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg transition-all">
                    <i class="fas fa-file-export mr-2 text-amber-400"></i> Exporter Bordereau Global
                </a>
            </div>
        </div>
        <div class="p-0">
            @if($pendingInvoices->isEmpty())
                <div class="p-12 text-center text-gray-400 font-bold text-sm">
                    Toutes les créances d'assurance ont été recouvrées.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Facture #</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Assureur</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Part Assurance</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingInvoices as $inv)
                            <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors">
                                <td class="px-8 py-5 font-black text-gray-900 text-xs">#{{ $inv->invoice_number }}</td>
                                <td class="px-8 py-5">
                                    <p class="text-xs font-bold text-gray-700">{{ $inv->patient->name ?? 'Inconnu' }}</p>
                                    <p class="text-[9px] text-gray-400 font-medium">{{ $inv->insurance_card_number }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg uppercase tracking-tighter">{{ $inv->insurance_name }}</span>
                                </td>
                                <td class="px-8 py-5 font-black text-amber-600 text-xs">
                                    {{ number_format(($inv->total * ($inv->insurance_coverage_rate ?? 0)) / 100, 0, ',', ' ') }} F
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[9px] font-black rounded-full uppercase">En Attente</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <form action="{{ route('admin.finance.settle', $inv->id) }}" method="POST" onsubmit="return confirm('Confirmez-vous la réception du paiement pour cette facture ?')">
                                        @csrf
                                        <button type="submit" class="group flex items-center gap-2 ml-auto px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Encaisser</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100">
                    {{ $pendingInvoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

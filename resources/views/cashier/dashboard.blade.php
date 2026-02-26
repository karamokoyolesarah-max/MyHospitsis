@extends('layouts.cashier_layout')

@section('content')
    <div class="p-8">
        {{-- En-tête --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">Tableau de bord</h2>
                <p class="text-gray-500">Aperçu financier et encaissements</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-gray-700">{{ now()->translatedFormat('l d F Y') }}</p>
            </div>
        </div>

        {{-- Cartes de statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-8 border-green-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recettes</p>
                    <div class="mt-1">
                        <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} F <span class="text-xs text-gray-400 font-medium">Total</span></p>
                        <p class="text-sm font-bold text-green-600">{{ number_format($stats['today_revenue'], 0, ',', ' ') }} F <span class="text-xs text-green-400 font-medium">Aujourd'hui</span></p>
                    </div>
                </div>
                <div class="p-4 bg-green-50 rounded-xl text-green-600"><i class="fas fa-wallet text-2xl"></i></div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-8 border-purple-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Assurance (Aujourd'hui)</p>
                    <p class="text-2xl font-black text-purple-600 mt-1">{{ number_format($stats['today_insurance'], 0, ',', ' ') }} F</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Montant à recouvrer</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl text-purple-600"><i class="fas fa-shield-alt text-2xl"></i></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-8 border-blue-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Actes Payés</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ $stats['paid_total'] }}</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl text-blue-600"><i class="fas fa-check-double text-2xl"></i></div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-8 border-orange-500 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">En Attente</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl text-orange-600"><i class="fas fa-hourglass-half text-2xl"></i></div>
            </div>
        </div>

        {{-- Liste des dossiers en attente --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-black text-gray-700 uppercase text-sm tracking-tighter">Dossiers en Attente de Paiement</h3>
                <span class="px-2 py-1 bg-orange-100 text-orange-700 text-[10px] font-black rounded-md">{{ $pendingPayments->count() }} DOSSIERS</span>
            </div>
            <div class="divide-y divide-gray-50 max-h-[600px] overflow-y-auto overflow-x-auto">
                @forelse($pendingPayments as $appointment)
                    <div class="p-5 hover:bg-blue-50/30 transition-all flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center font-black text-gray-400 border border-gray-200 text-sm">
                                {{ strtoupper(substr($appointment->patient->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">{{ $appointment->patient->name }}</h4>
                                <p class="text-[11px] text-gray-500 uppercase tracking-tighter">
                                    {{ optional($appointment->service)->name ?? 'Service Inconnu' }} 
                                    @if(isset($appointment->prestations) && $appointment->prestations->count() > 0)
                                        <span class="text-blue-500">(+{{ $appointment->prestations->count() }} actes)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <div class="text-right font-black text-gray-900">
                                {{-- Calcul du total incluant les prestations --}}
                                @php 
                                    $servicePrice = optional($appointment->service)->price ?? 0;
                                    $prestationsTotal = optional($appointment->prestations)->sum('pivot.total') ?? 0;
                                    $totalApt = $servicePrice + $prestationsTotal;
                                @endphp
                                {{ number_format($totalApt, 0, ',', ' ') }} F
                            </div>
                            
                            {{-- Bouton Encaisser (Appelle la modale) --}}
                            <button onclick="openPaymentModal({{ $appointment->id }}, '{{ addslashes($appointment->patient->name) }}', {{ $totalApt }}, '{{ $appointment->payment_type }}')" 
                                    class="opacity-0 group-hover:opacity-100 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all hover:bg-blue-700 shadow-md">
                                <i class="fas fa-cash-register mr-1"></i> ENCAISSER
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 text-gray-400">Aucun dossier en attente.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODALE D'ENCAISSEMENT --}}
    <div id="paymentModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Finaliser l'encaissement</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="paymentForm" method="POST" action="" class="p-6">
                @csrf
                <div class="space-y-6">
                    {{-- Résumé --}}
                    <div class="bg-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-200">
                        <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Total à payer</p>
                        <p class="text-3xl font-black"><span id="modalAmount">0</span> F CFA</p>
                        <div class="mt-3 pt-3 border-t border-white/20">
                            <p class="text-sm font-medium"><i class="fas fa-user-circle mr-2 text-blue-200"></i><span id="modalPatientName"></span></p>
                        </div>
                    </div>

                    {{-- Mode de paiement --}}
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Mode de règlement</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex flex-col items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 group">
                                <input type="radio" name="payment_method" value="Espèces" class="hidden" checked>
                                <i class="fas fa-money-bill-wave text-xl mb-2 text-gray-400 group-has-[:checked]:text-blue-600"></i>
                                <span class="text-xs font-bold text-gray-600 group-has-[:checked]:text-blue-800">Espèces</span>
                            </label>
                            <label class="relative flex flex-col items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 group">
                                <input type="radio" name="payment_method" value="Mobile Money" class="hidden">
                                <i class="fas fa-mobile-alt text-xl mb-2 text-gray-400 group-has-[:checked]:text-blue-600"></i>
                                <span class="text-xs font-bold text-gray-600 group-has-[:checked]:text-blue-800">Mobile Money</span>
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="amount_paid" id="hiddenAmount">

                    <button type="submit" class="w-full bg-gray-900 text-white font-black py-4 rounded-2xl hover:bg-black transition-all shadow-xl flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        VALIDER LE PAIEMENT
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script de la Modale --}}
    <script>
        function openPaymentModal(id, name, amount, paymentType) {
            document.getElementById('modalPatientName').innerText = name;
            document.getElementById('modalAmount').innerText = amount.toLocaleString();
            document.getElementById('hiddenAmount').value = amount;
            
            let form = document.getElementById('paymentForm');
            
            if (paymentType === 'appointment') {
                form.action = `/cashier/appointments/${id}/validate-payment`;
            } else if (paymentType === 'walk-in') {
                form.action = `/cashier/walk-in/${id}/validate-payment`;
            } else if (paymentType === 'lab_request') {
                form.action = `/cashier/lab-requests/${id}/pay`;
            } else {
                 // Fallback or error
                 console.error('Unknown payment type');
                 form.action = '#';
            }
            
            const modal = document.getElementById('paymentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('paymentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection
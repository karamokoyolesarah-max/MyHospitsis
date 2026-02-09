<div class="space-y-4">
    @php
        $insName = $consultation->insurance_name ?? ($pendingInvoice->insurance_name ?? null);
        $insCard = $consultation->insurance_card_number ?? ($pendingInvoice->insurance_card_number ?? null);
        $insRate = $consultation->insurance_coverage_rate ?? ($pendingInvoice->insurance_coverage_rate ?? 0);
    @endphp
    @if($insName)
    <input type="hidden" id="storedInsuranceName" value="{{ $insName }}">
    <input type="hidden" id="storedInsuranceCard" value="{{ $insCard }}">
    <input type="hidden" id="storedInsuranceRate" value="{{ $insRate }}">
    @endif
    <div class="border-b border-gray-200 pb-3">
        <h4 class="font-bold text-gray-800 text-lg">{{ $consultation->patient->name }}</h4>
        <div class="flex items-center text-sm text-gray-500 mt-1">
            <i class="fas fa-phone-alt mr-2 text-gray-400"></i> {{ $consultation->patient->phone }}
            @if($consultation->patient->email)
                <span class="mx-2">•</span> <i class="fas fa-envelope mr-2 text-gray-400"></i> {{ $consultation->patient->email }}
            @endif
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg p-3">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 font-semibold">Service: <span class="text-gray-800">{{ $consultation->service->name }}</span></span>
        </div>
        
        <div class="mt-2 pt-2 border-t border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Actes & Prestations</p>
            <ul class="space-y-1">
                @foreach($consultation->prestations as $prestation)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $prestation->name }}</span>
                        <span class="font-medium">{{ number_format($prestation->pivot->total, 0, ',', ' ') }} FCFA</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="pt-2 border-t border-gray-200">
        <div class="flex justify-between items-center text-sm text-gray-600 mb-1">
            <span>Sous-total</span>
            <span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
            <span>TVA (18%)</span>
            <span>{{ number_format($tax, 0, ',', ' ') }} FCFA</span>
        </div>
        
        <!-- Full Total (Default) -->
        <div id="fullTotalRow" class="flex justify-between items-center text-xl font-bold text-blue-800 mt-2 bg-blue-50 p-3 rounded-lg border border-blue-100">
            <span>Total à Payer</span>
            <span id="modalFullTotal" data-value="{{ $grandTotal }}">{{ number_format($grandTotal, 0, ',', ' ') }} FCFA</span>
        </div>

        <!-- Co-payment Breakdown (Hidden by default) -->
        <div id="coPaymentBreakdown" class="hidden mt-3 space-y-2">
             <div class="flex justify-between items-center text-sm text-purple-600">
                <span>Part Assurance (<span id="modalCoverageRate">0</span>%)</span>
                <span id="modalInsurancePart" class="font-bold">0 FCFA</span>
            </div>
            <div class="flex justify-between items-center text-xl font-black text-red-600 bg-red-50 p-3 rounded-lg border border-red-100">
                <span>Reste à Payer (Patient)</span>
                <span id="modalPatientPart">0 FCFA</span>
            </div>
        </div>
    </div>
</div>

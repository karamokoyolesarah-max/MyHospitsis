@extends('layouts.external_doctor')

@section('title', 'Recharger')
@section('page-title', 'Recharger mon compte')
@section('page-subtitle', 'Rechargez votre solde via Mobile Money')

@section('content')
<div class="space-y-6">
    
    <!-- Current Balance -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-indigo-200 mb-2">Solde actuel</p>
                <h1 class="text-4xl font-bold">{{ number_format($user->balance ?? 0, 0, ',', ' ') }} FCFA</h1>
                @if($user->plan_expires_at)
                <p class="text-indigo-200 mt-2">Expire le {{ $user->plan_expires_at->format('d/m/Y') }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recharge Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau rechargement
                </h2>
            </div>
            <form method="POST" action="{{ route('external.recharge.initiate') }}" class="p-6" id="rechargeForm">
                @csrf
                
                <!-- Amount Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Sélectionnez un montant</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([1000, 2500, 5000, 10000] as $amt)
                        <label class="relative group">
                            <input type="radio" name="amount" value="{{ $amt }}" class="peer sr-only amount-radio" {{ $amt == 5000 ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-focus:ring-2 peer-focus:ring-indigo-500 peer-focus:ring-offset-2 hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition text-center h-full flex flex-col justify-center">
                                <p class="text-xl font-bold text-gray-900">{{ number_format($amt, 0, ',', ' ') }}</p>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">FCFA</p>
                                @if($amt == 5000)
                                <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm transform group-hover:scale-110 transition-transform">Populaire</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-4 relative">
                        <label class="block text-sm text-gray-500 mb-2 font-medium">Ou entrez un montant personnalisé</label>
                        <div class="relative">
                            <input type="number" id="custom_amount" name="custom_amount" min="500" step="100" placeholder="Ex: 15 000" class="w-full pl-4 pr-16 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-lg font-semibold text-gray-900 placeholder-gray-300">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Mode de paiement</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- MTN -->
                        <label class="relative group">
                            <input type="radio" name="payment_method" value="mtn" class="peer sr-only" checked onchange="toggleWaveField(false); toggleQRCode('mtn')">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-yellow-400 bg-white peer-checked:border-yellow-400 peer-checked:bg-yellow-50 transition flex items-center space-x-3 h-full">
                                <div class="w-10 h-10 bg-[#FFCC00] rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-[#000] font-black text-xs">MTN</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-sm text-gray-900">MTN MoMo</p>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Instantané</p>
                                </div>
                            </div>
                        </label>

                        <!-- Orange -->
                        <label class="relative group">
                            <input type="radio" name="payment_method" value="orange" class="peer sr-only" onchange="toggleWaveField(false); toggleQRCode('orange')">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-500 bg-white peer-checked:border-orange-500 peer-checked:bg-orange-50 transition flex items-center space-x-3 h-full">
                                <div class="w-10 h-10 bg-[#FF7900] rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-white font-black text-[10px]">Orange</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-sm text-gray-900">Orange Money</p>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Instantané</p>
                                </div>
                            </div>
                        </label>

                        <!-- Moov -->
                        <label class="relative group">
                            <input type="radio" name="payment_method" value="moov" class="peer sr-only" onchange="toggleWaveField(false); toggleQRCode('moov')">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 bg-white peer-checked:border-blue-500 peer-checked:bg-blue-50 transition flex items-center space-x-3 h-full">
                                <div class="w-10 h-10 bg-[#0066CC] rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-white font-black text-[10px]">MOOV</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-sm text-gray-900">Moov Money</p>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Instantané</p>
                                </div>
                            </div>
                        </label>

                        <!-- Wave -->
                        <label class="relative group">
                            <input type="radio" name="payment_method" value="wave" class="peer sr-only" onchange="toggleWaveField(true); toggleQRCode('wave')">
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#1DC4FF] bg-white peer-checked:border-[#1DC4FF] peer-checked:bg-sky-50 transition flex items-center space-x-3 h-full">
                                <div class="w-10 h-10 bg-[#1DC4FF] rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-white font-black text-[10px]" translate="no">Wave</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-sm text-gray-900" translate="no">Wave</p>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">Validation Admin</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Wave Specific Instructions & Ref (Hidden by default) -->
                <div id="wave_instructions" class="hidden mb-8 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 bg-[#E6F8FF] border border-[#B3EAFF] rounded-2xl relative">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-[#1DC4FF]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        </div>
                        
                        <div class="flex items-start space-x-4 mb-6 relative z-10">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm text-[#1DC4FF]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Paiement via <span translate="no" class="text-[#1DC4FF]">Wave</span></h3>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-[#1DC4FF] font-bold text-xs mr-2 shadow-sm border border-[#B3EAFF]">1</span>
                                        <p>Effectuez le transfert vers le numéro indiqué ci-dessous.</p>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-[#1DC4FF] font-bold text-xs mr-2 shadow-sm border border-[#B3EAFF]">2</span>
                                        <p>Une fois le paiement effectué, copiez l'<strong>ID de transaction</strong> reçu par SMS.</p>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-[#1DC4FF] font-bold text-xs mr-2 shadow-sm border border-[#B3EAFF]">3</span>
                                        <p>Collez cet ID dans le champ ci-dessous et validez.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4 border border-[#B3EAFF] mb-4 flex items-center justify-between shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500 font-semibold uppercase">Numéro Wave du Cabinet</span>
                                <span class="text-xl font-bold text-gray-900 font-mono tracking-wider select-all">{{ $paymentSettings['payment_wave_number'] ?? '07 00 00 00 00' }}</span>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $paymentSettings['payment_wave_number'] ?? '07 00 00 00 00' }}')" class="p-2 text-[#1DC4FF] hover:bg-[#E6F8FF] rounded-lg transition" title="Copier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ID de Transaction <span translate="no">Wave</span></label>
                            <input type="text" name="transaction_ref" placeholder="Ex: W-12345678" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1DC4FF] focus:border-[#1DC4FF] transition font-mono uppercase">
                        </div>
                    </div>
                </div>

                <!-- QR Code Display Section -->
                <div id="qr_code_display" class="mb-8 hidden opacity-0 max-h-0 scale-95 transition-all duration-300 ease-out overflow-hidden">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border-2 border-indigo-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="bi bi-qr-code text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Scanner pour payer</h3>
                                <p class="text-xs text-gray-600">Utilisez votre application mobile pour scanner ce QR Code</p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 flex items-center justify-center">
                            <img id="qr_code_image" src="" alt="QR Code" class="max-w-full h-auto" style="max-height: 300px;">
                        </div>
                        
                        <div class="mt-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <p class="text-xs text-blue-800 leading-relaxed">
                                <i class="bi bi-info-circle-fill text-blue-600 mr-2"></i>
                                <strong>Astuce :</strong> Après avoir scanné et effectué le paiement, n'oubliez pas de renseigner votre numéro de téléphone et de valider le formulaire.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro de téléphone Mobile Money</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">+225</span>
                        </div>
                        <input type="tel" id="mobile_money_number" name="phone_number" required value="{{ $user->telephone }}" placeholder="01 02 03 04 05" class="w-full pl-16 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-lg font-medium tracking-wide">
                    </div>
                    <p class="text-gray-400 text-xs mt-2 pl-1">Numéro utilisé pour débiter le compte (MTN/Orange/Moov) ou pour vérification (Wave).</p>
                </div>

                <button type="submit" id="submitBtn" class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg shadow-indigo-200 flex items-center justify-center space-x-2 disabled:opacity-75 disabled:cursor-wait">
                    <span id="submitBtnText" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Procéder au rechargement
                    </span>
                    <span id="submitBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Traitement en cours...
                    </span>
                </button>
            </form>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Historique récent</h2>
                </div>
                <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                    @if($recharges->isEmpty())
                    <div class="text-center py-8 px-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 grayscale opacity-50">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">Aucune transaction récente.<br>Commencez par recharger votre compte.</p>
                    </div>
                    @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recharges as $recharge)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-gray-900">{{ number_format($recharge->amount, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span></span>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full 
                                    {{ $recharge->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $recharge->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $recharge->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                ">
                                    {{ $recharge->status === 'completed' ? 'Succès' : ($recharge->status === 'pending' ? 'En attente' : 'Échoué') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center font-medium text-gray-600">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        {{ $recharge->payment_method === 'mtn' ? 'bg-[#FFCC00]' : '' }}
                                        {{ $recharge->payment_method === 'orange' ? 'bg-[#FF7900]' : '' }}
                                        {{ $recharge->payment_method === 'moov' ? 'bg-[#0066CC]' : '' }}
                                        {{ $recharge->payment_method === 'wave' ? 'bg-[#1DC4FF]' : '' }}
                                    "></span>
                                    <span translate="no">{{ $recharge->payment_method === 'wave' ? 'Wave' : strtoupper($recharge->payment_method) }}</span>
                                </span>
                                <span class="text-gray-400 tabular-nums">{{ $recharge->created_at->format('d/m H:i') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-100 rounded-full opacity-50 blur-2xl"></div>
                
                <h3 class="flex items-center text-sm font-bold text-indigo-900 mb-3 uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    Notes Importantes
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start text-xs text-indigo-800 leading-relaxed">
                        <span class="mr-2 text-indigo-400">•</span>
                        <span>Les paiements <strong>MTN, Orange, Moov</strong> sont traités instantanément par CinetPay.</span>
                    </li>
                    <li class="flex items-start text-xs text-indigo-800 leading-relaxed">
                        <span class="mr-2 text-indigo-400">•</span>
                        <span>Pour <strong>Wave</strong>, le délai de validation peut prendre jusqu'à 30 minutes. Assurez-vous que l'ID de transaction est correct.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // QR Code mapping from payment settings
        const qrCodes = {
            'orange': '{{ isset($paymentSettings["payment_qr_orange"]) && $paymentSettings["payment_qr_orange"] ? asset("storage/" . $paymentSettings["payment_qr_orange"]) : "" }}',
            'mtn': '{{ isset($paymentSettings["payment_qr_mtn"]) && $paymentSettings["payment_qr_mtn"] ? asset("storage/" . $paymentSettings["payment_qr_mtn"]) : "" }}',
            'moov': '{{ isset($paymentSettings["payment_qr_moov"]) && $paymentSettings["payment_qr_moov"] ? asset("storage/" . $paymentSettings["payment_qr_moov"]) : "" }}',
            'wave': '{{ isset($paymentSettings["payment_qr_wave"]) && $paymentSettings["payment_qr_wave"] ? asset("storage/" . $paymentSettings["payment_qr_wave"]) : "" }}'
        };

        // Function to show/hide QR Code based on selected payment method
        function toggleQRCode(paymentMethod) {
            const qrDisplay = document.getElementById('qr_code_display');
            const qrImage = document.getElementById('qr_code_image');
            const qrPath = qrCodes[paymentMethod];

            if (qrPath && qrPath !== '') {
                qrImage.src = qrPath;
                qrDisplay.classList.remove('hidden');
                setTimeout(() => {
                    qrDisplay.classList.remove('opacity-0', 'max-h-0', 'scale-95');
                    qrDisplay.classList.add('opacity-100', 'max-h-[1000px]', 'scale-100');
                }, 10);
            } else {
                qrDisplay.classList.remove('opacity-100', 'max-h-[1000px]', 'scale-100');
                qrDisplay.classList.add('opacity-0', 'max-h-0', 'scale-95');
                setTimeout(() => {
                    qrDisplay.classList.add('hidden');
                }, 300);
            }
        }

        // Toggle Wave instructions with animation
        function toggleWaveField(show) {
            const waveField = document.getElementById('wave_instructions');
            if (show) {
                waveField.classList.remove('hidden');
                // Small timeout to allow removing 'hidden' to render before applying opacity transition
                setTimeout(() => {
                    waveField.classList.remove('opacity-0', 'max-h-0', 'scale-95');
                    waveField.classList.add('opacity-100', 'max-h-[500px]', 'scale-100');
                }, 10);
            } else {
                waveField.classList.remove('opacity-100', 'max-h-[500px]', 'scale-100');
                waveField.classList.add('opacity-0', 'max-h-0', 'scale-95');
                // Wait for transition to finish before hiding
                setTimeout(() => {
                    waveField.classList.add('hidden');
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function(){
            const form = document.getElementById('rechargeForm');
            const customAmountInput = document.getElementById('custom_amount');
            const radioButtons = document.querySelectorAll('.amount-radio');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitBtnSpinner = document.getElementById('submitBtnSpinner');
            const phoneInput = document.getElementById('mobile_money_number');

            // Afficher le QR Code de l'opérateur par défaut (MTN) au chargement
            toggleQRCode('mtn');

            // 1. Phone number formatting: 00 00 00 00 00
            if(phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let v = e.target.value.replace(/\D/g, ''); // Remove non-digits
                    if (v.length > 10) v = v.substring(0, 10); // Limit to 10 digits
                    
                    // Format in pairs of 2
                    if (v.length > 0) {
                        v = v.match(new RegExp('.{1,2}', 'g')).join(' ');
                    }
                    e.target.value = v;
                });
            }

            // 2. Custom Amount Logic
            if(customAmountInput) {
                // When custom amount is focused or typed in
                customAmountInput.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        radioButtons.forEach(rb => rb.checked = false);
                    }
                });

                // When a radio is selected
                radioButtons.forEach(rb => {
                    rb.addEventListener('change', function() {
                        if(this.checked) {
                            customAmountInput.value = '';
                        }
                    });
                });
            }

            // 3. Form Submission Handling
            if (form) {
                form.addEventListener('submit', function(e){
                    
                    // Logic to ensure amount is sent correctly
                    if (customAmountInput.value && customAmountInput.value > 0) {
                        // Create a hidden input for amount because radio might be unchecked
                        // First remove existing hidden inputs named 'amount' to differentiate from radio
                        const existingHidden = form.querySelectorAll('input[type="hidden"][name="amount"]');
                        existingHidden.forEach(el => el.remove());

                        let hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'amount';
                        hidden.value = customAmountInput.value;
                        form.appendChild(hidden);
                    } else {
                        // Check if a radio is selected
                        const selectedRadio = document.querySelector('input[name="amount"]:checked');
                        if (!selectedRadio) {
                            e.preventDefault();
                            alert('Veuillez sélectionner ou entrer un montant.');
                            return;
                        }
                    }

                    // Mobile formatting clean-up before submit (remove spaces)
                    const cleanPhone = phoneInput.value.replace(/\s/g, '');
                    // We need to send the clean phone, so creates a hidden input or update value
                    // Updating value might disorient user seeing visual change, better hidden
                    let hiddenPhone = document.createElement('input');
                    hiddenPhone.type = 'hidden';
                    hiddenPhone.name = 'phone_number_clean';
                    hiddenPhone.value = cleanPhone;
                    form.appendChild(hiddenPhone);
                    // Note: Ensure backend handles 'phone_number_clean' OR validation allowing spaces
                    // Simpler: Just update the value immediately before submit
                    phoneInput.value = cleanPhone;

                    // Button loading state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('cursor-not-allowed', 'opacity-90');
                    submitBtnText.classList.add('hidden');
                    submitBtnSpinner.classList.remove('hidden');
                    submitBtnSpinner.classList.add('flex', 'items-center');
                });
            }
        });
    </script>

</div>
@endsection


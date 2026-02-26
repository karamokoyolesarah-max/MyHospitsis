<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Rendez-vous | HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        @keyframes success-check {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-success { animation: success-check 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900">

    <header class="bg-white/80 sticky top-0 z-[2000] border-b border-slate-200 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.appointments') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition">
                        <i class="fas fa-arrow-left text-slate-600"></i>
                    </a>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">Confirmation</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full mb-6 animate-success border-4 border-white shadow-xl">
                <i class="fas fa-check text-4xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-2">Demande Enregistrée !</h2>
            <p class="text-slate-500 font-medium">Votre rendez-vous a été planifié avec succès.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Details Card -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 space-y-8">
                <div>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Détails du Rendez-vous</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400">Date & Heure</p>
                                <p class="text-sm font-black text-slate-700">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->translatedFormat('l d F Y à H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400">Établissement</p>
                                <p class="text-sm font-black text-slate-700">{{ $appointment->hospital->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400">Service / Prestation</p>
                                <p class="text-sm font-black text-slate-700">
                                    {{ $appointment->service->name }} 
                                    @if($appointment->prestations->count() > 0)
                                        <span class="text-slate-400 font-medium">- {{ $appointment->prestations->first()->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($appointment->medecin_externe_id || $appointment->doctor_id)
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400">Médecin</p>
                                <p class="text-sm font-black text-slate-700">
                                    {{ $appointment->medecinExterne ? 'Dr. ' . $appointment->medecinExterne->full_name : ($appointment->doctor ? 'Dr. ' . $appointment->doctor->full_name : 'À confirmer') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    @if($appointment->payment_transaction_id)
                        <a href="{{ route('patient.appointments.bill', $appointment->id) }}" target="_blank" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest text-xs flex items-center justify-center hover:bg-black transition shadow-xl shadow-slate-200">
                            <i class="fas fa-print mr-3"></i> Imprimer mon reçu / Facture
                        </a>
                    @else
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start space-x-3">
                            <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                            <p class="text-[11px] font-bold text-amber-700 leading-relaxed">
                                Votre facture sera disponible au téléchargement une fois votre paiement validé par la caisse.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Card -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                <div class="bg-slate-50 p-6 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Récapitulatif Financier</h3>
                </div>
                <div class="p-8 flex-1 space-y-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-500">Consultation / Service</span>
                            <span class="text-sm font-black text-slate-700">{{ number_format($appointment->total_amount - ($appointment->calculated_travel_fee ?? 0) - ($appointment->tax_amount ?? 0)) }} FCFA</span>
                        </div>
                        @if($appointment->calculated_travel_fee > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-500">Frais de déplacement</span>
                            <span class="text-sm font-black text-slate-700">{{ number_format($appointment->calculated_travel_fee) }} FCFA</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-500">TVA (18%)</span>
                            <span class="text-sm font-black text-slate-700">{{ number_format($appointment->tax_amount) }} FCFA</span>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t-2 border-dashed border-slate-100">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-black text-slate-900 uppercase tracking-widest">Total à payer</span>
                            <span class="text-3xl font-black text-blue-600">{{ number_format($appointment->total_amount) }} FCFA</span>
                        </div>
                    </div>

                    <div class="mt-8 space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Modes de paiement acceptés</p>
                        
                        <div class="space-y-4">
                            <!-- Orange Money -->
                            @if(isset($paymentSettings['payment_orange_money_number']) || isset($paymentSettings['payment_qr_orange']))
                            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c8/Orange_logo.svg" class="h-6" alt="Orange">
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-orange-600 uppercase tracking-wider">Orange Money</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $paymentSettings['payment_orange_money_number'] ?? 'À la caisse' }}</p>
                                    </div>
                                </div>
                                @if(isset($paymentSettings['payment_qr_orange']))
                                <button type="button" onclick="showQR('{{ asset('storage/' . $paymentSettings['payment_qr_orange']) }}', 'Orange Money')" class="p-2 bg-white rounded-lg text-orange-600 hover:bg-orange-600 hover:text-white transition">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                                @endif
                            </div>
                            @endif

                            <!-- Wave -->
                            @if(isset($paymentSettings['payment_wave_number']) || isset($paymentSettings['payment_qr_wave']))
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                        <img src="https://vectorise.net/vector_logo/logotypes/Wave%20Money%20logo.png" class="h-6" alt="Wave">
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-wider">Wave</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $paymentSettings['payment_wave_number'] ?? 'À la caisse' }}</p>
                                    </div>
                                </div>
                                @if(isset($paymentSettings['payment_qr_wave']))
                                <button type="button" onclick="showQR('{{ asset('storage/' . $paymentSettings['payment_qr_wave']) }}', 'Wave')" class="p-2 bg-white rounded-lg text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                                @endif
                            </div>
                            @endif

                            <!-- MTN -->
                            @if(isset($paymentSettings['payment_mtn_money_number']) || isset($paymentSettings['payment_qr_mtn']))
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-2xl border border-yellow-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/af/MTN_Logo.svg" class="h-6" alt="MTN">
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-yellow-600 uppercase tracking-wider">MTN MoMo</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $paymentSettings['payment_mtn_money_number'] ?? 'À la caisse' }}</p>
                                    </div>
                                </div>
                                @if(isset($paymentSettings['payment_qr_mtn']))
                                <button type="button" onclick="showQR('{{ asset('storage/' . $paymentSettings['payment_qr_mtn']) }}', 'MTN MoMo')" class="p-2 bg-white rounded-lg text-yellow-600 hover:bg-yellow-600 hover:text-white transition">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-900 text-center">
                    <p class="text-[10px] font-bold text-white">Veuillez présenter votre reçu après paiement pour validation.</p>
                </div>
            </div>
        </div>

        <!-- QR Modal -->
        <div id="qr_modal" class="fixed inset-0 z-[5000] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in fade-in duration-300">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 id="qr_title" class="text-sm font-black text-slate-900 uppercase tracking-widest">Scanner pour payer</h3>
                    <button type="button" onclick="hideQR()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>
                <div class="p-8 text-center">
                    <div class="bg-slate-50 p-4 rounded-3xl inline-block mb-6 shadow-inner border border-slate-100">
                        <img id="qr_img" src="" class="w-48 h-48 rounded-xl mx-auto" alt="QR Code">
                    </div>
                    <p class="text-sm font-medium text-slate-500 mb-2">Utilisez votre application mobile pour scanner ce code et effectuer le paiement.</p>
                </div>
                <div class="p-6 bg-slate-50">
                    <button type="button" onclick="hideQR()" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-black transition">
                        Fermer
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center text-slate-400 font-bold hover:text-slate-900 transition">
                <i class="fas fa-home mr-2"></i> Retour au tableau de bord
            </a>
        </div>
    </main>

    <script>
        function showQR(url, title) {
            document.getElementById('qr_img').src = url;
            document.getElementById('qr_title').innerText = "Scanner pour payer (" + title + ")";
            document.getElementById('qr_modal').classList.remove('hidden');
        }

        function hideQR() {
            document.getElementById('qr_modal').classList.add('hidden');
        }
    </script>

</body>
</html>

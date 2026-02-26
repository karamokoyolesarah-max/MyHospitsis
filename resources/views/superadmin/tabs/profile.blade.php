<div id="tab-profile" class="tab-pane animate-in slide-in-from-right-8 duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 text-left gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Mon Profil Admnistrateur</h2>
            <p class="text-slate-500 font-medium">Gérez vos informations personnelles et vos paramètres de sécurité.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-left">
        <!-- Sidebar Profile Info -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col items-center p-8 text-center relative">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div class="relative mt-8">
                    <div class="w-32 h-32 bg-white rounded-full p-2 shadow-xl">
                        <div class="w-full h-full bg-gradient-to-tr from-blue-100 to-indigo-100 rounded-full flex items-center justify-center text-4xl font-black text-blue-600 border-4 border-white shadow-inner">
                            SA
                        </div>
                    </div>
                    <div class="absolute bottom-1 right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white"></div>
                </div>
                
                <h3 class="mt-6 text-2xl font-black text-slate-900 leading-tight">{{ Auth::guard('superadmin')->user()->name ?? 'Super Admin' }}</h3>
                <p class="text-blue-600 font-bold uppercase tracking-widest text-xs mt-1">Super Utilisateur Système</p>
                
                <div class="w-full mt-8 pt-8 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Statut</span>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold text-[10px] uppercase">Actif</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Dernière Connexion</span>
                        <span class="text-slate-800 font-bold">Aujourd'hui, 12:45</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Rôle</span>
                        <span class="text-slate-800 font-bold">Admin Système</span>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 space-y-6">
                <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="bi bi-activity text-blue-600"></i>
                    Résumé d'Activité
                </h4>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="bg-blue-50 text-blue-600 w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">12 Nouveaux Hôpitaux</div>
                            <div class="text-xs text-slate-500">Installés ce mois-ci</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="bg-emerald-50 text-emerald-600 w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">45 Spécialistes</div>
                            <div class="text-xs text-slate-500">Validés récemment</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vérification Professionnelle -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 rounded-t-[2.5rem]">
                    <h4 class="text-base font-black text-white flex items-center gap-2">
                        <i class="bi bi-person-check-fill"></i> Vérification Pro.
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <p class="text-xs text-slate-500 mb-3 font-medium">Portails officiels pour vérifier les praticiens :</p>

                    <a href="https://www.ordremedecins.ci/" target="_blank"
                       class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl hover:bg-indigo-50 transition-colors border border-slate-200 hover:border-indigo-200 group">
                        <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="bi bi-stethoscope"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">Ordre des Médecins</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">ONMCI — CI</p>
                        </div>
                    </a>

                    <a href="https://www.infas.ci/" target="_blank"
                       class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl hover:bg-green-50 transition-colors border border-slate-200 hover:border-green-200 group">
                        <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">Vérif. Diplôme</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">INFAS</p>
                        </div>
                    </a>

                    <a href="https://edepps.sante.gouv.ci/" target="_blank"
                       class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl hover:bg-blue-50 transition-colors border border-slate-200 hover:border-blue-200 group">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">Registre Santé</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">E-DEPPS</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Settings -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Profile Settings -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col p-10">
                <h4 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                    <i class="bi bi-person-gear text-blue-600 text-3xl"></i>
                    Paramètres du Compte
                </h4>
                
                <form action="#" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Nom Complet</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500">
                                    <i class="bi bi-person text-xl"></i>
                                </div>
                                <input type="text" name="name" value="{{ Auth::guard('superadmin')->user()->name ?? 'Super Admin' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="Votre nom complet">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Adresse Email</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500">
                                    <i class="bi bi-envelope text-xl"></i>
                                </div>
                                <input type="email" name="email" value="{{ Auth::guard('superadmin')->user()->email ?? 'admin@hospitsis.com' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="admin@hospitsis.com">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="showNotification('Mise à jour du profil bientôt disponible', 'info')" 
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-10 py-4 rounded-3xl font-bold transition-all duration-300 shadow-xl shadow-blue-200 hover:scale-105 flex items-center gap-3">
                            <i class="bi bi-check2-circle text-xl"></i>
                            Enregistrer les Modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payment API Configuration -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col p-10">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <i class="bi bi-wallet2 text-emerald-600 text-3xl"></i>
                        Configuration API de Paiement
                    </h4>
                    <span class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-2xl font-black text-xs uppercase tracking-widest">Connecté</span>
                </div>

                <p class="text-slate-500 font-medium mb-8">Définissez les numéros de réception pour chaque opérateur mobile relié à votre API de paiement globale.</p>
                
                <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Orange Money -->
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700 ml-1">
                                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                Orange Money
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="bi bi-phone text-orange-500 text-xl"></i>
                                </div>
                                <input type="text" name="orange_money_number" value="{{ $paymentSettings['payment_orange_money_number'] ?? '0700000000' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="+225 07 ...">
                            </div>
                            
                            <!-- QR Code Upload -->
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-600 mb-2 ml-1">QR Code Orange Money</label>
                                @if(isset($paymentSettings['payment_qr_orange']) && $paymentSettings['payment_qr_orange'])
                                    <div class="mb-2 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                        <img src="{{ asset('storage/' . $paymentSettings['payment_qr_orange']) }}" alt="QR Orange" class="w-32 h-32 object-contain mx-auto rounded-lg">
                                    </div>
                                @endif
                                <input type="file" name="qr_orange" accept="image/*" 
                                       class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-2xl text-sm focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all">
                            </div>
                        </div>

                        <!-- MTN Money -->
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700 ml-1">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                MTN Money
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="bi bi-phone text-yellow-500 text-xl"></i>
                                </div>
                                <input type="text" name="mtn_money_number" value="{{ $paymentSettings['payment_mtn_money_number'] ?? '0500000000' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-yellow-500 focus:bg-white focus:ring-4 focus:ring-yellow-500/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="+225 05 ...">
                            </div>
                            
                            <!-- QR Code Upload -->
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-600 mb-2 ml-1">QR Code MTN Money</label>
                                @if(isset($paymentSettings['payment_qr_mtn']) && $paymentSettings['payment_qr_mtn'])
                                    <div class="mb-2 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                        <img src="{{ asset('storage/' . $paymentSettings['payment_qr_mtn']) }}" alt="QR MTN" class="w-32 h-32 object-contain mx-auto rounded-lg">
                                    </div>
                                @endif
                                <input type="file" name="qr_mtn" accept="image/*" 
                                       class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-2xl text-sm focus:border-yellow-500 focus:ring-4 focus:ring-yellow-500/10 transition-all">
                            </div>
                        </div>

                        <!-- Moov Money -->
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700 ml-1">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Moov Money
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="bi bi-phone text-blue-500 text-xl"></i>
                                </div>
                                <input type="text" name="moov_money_number" value="{{ $paymentSettings['payment_moov_money_number'] ?? '0100000000' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="+225 01 ...">
                            </div>
                            
                            <!-- QR Code Upload -->
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-600 mb-2 ml-1">QR Code Moov Money</label>
                                @if(isset($paymentSettings['payment_qr_moov']) && $paymentSettings['payment_qr_moov'])
                                    <div class="mb-2 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                        <img src="{{ asset('storage/' . $paymentSettings['payment_qr_moov']) }}" alt="QR Moov" class="w-32 h-32 object-contain mx-auto rounded-lg">
                                    </div>
                                @endif
                                <input type="file" name="qr_moov" accept="image/*" 
                                       class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-2xl text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                            </div>
                        </div>

                        <!-- Wave -->
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-700 ml-1">
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                Wave
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="bi bi-phone text-cyan-400 text-xl"></i>
                                </div>
                                <input type="text" name="wave_number" value="{{ $paymentSettings['payment_wave_number'] ?? '0700000000' }}" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-400/10 transition-all duration-300 font-bold text-slate-900"
                                       placeholder="+225 07 ...">
                            </div>
                            
                            <!-- QR Code Upload -->
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-600 mb-2 ml-1">QR Code Wave</label>
                                @if(isset($paymentSettings['payment_qr_wave']) && $paymentSettings['payment_qr_wave'])
                                    <div class="mb-2 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                        <img src="{{ asset('storage/' . $paymentSettings['payment_qr_wave']) }}" alt="QR Wave" class="w-32 h-32 object-contain mx-auto rounded-lg">
                                    </div>
                                @endif
                                <input type="file" name="qr_wave" accept="image/*" 
                                       class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-2xl text-sm focus:border-cyan-400 focus:ring-4 focus:ring-cyan-400/10 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100 flex items-start gap-4">
                        <i class="bi bi-info-circle-fill text-blue-600 text-xl mt-1"></i>
                        <div class="text-sm text-blue-800 leading-relaxed">
                            <span class="font-bold">Note Importante :</span> Ces numéros sont utilisés par l'API de paiement système pour router les fonds collectés via les forfaits et commissions vers votre compte central. Assurez-vous que les numéros configurés sont correctement enregistrés auprès de vos fournisseurs Mobile Money respectifs.
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" 
                                class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-10 py-4 rounded-3xl font-bold transition-all duration-300 shadow-xl shadow-emerald-200 hover:scale-105 flex items-center gap-3">
                            <i class="bi bi-shield-check text-xl"></i>
                            Valider la Configuration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security / Password Change -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col p-10">
                <h4 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                    <i class="bi bi-shield-lock text-red-600 text-3xl"></i>
                    Sécurité / Mot de passe
                </h4>
                
                <form action="#" method="POST" class="space-y-8">
                    @csrf
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Mot de passe actuel</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500">
                                    <i class="bi bi-lock text-xl"></i>
                                </div>
                                <input type="password" name="current_password" 
                                       class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all duration-300 font-bold"
                                       placeholder="••••••••••••">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Nouveau mot de passe</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500">
                                        <i class="bi bi-key text-xl"></i>
                                    </div>
                                    <input type="password" name="new_password" 
                                           class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all duration-300 font-bold"
                                           placeholder="••••••••••••">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Confirmer le mot de passe</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500">
                                        <i class="bi bi-shield-check text-xl"></i>
                                    </div>
                                    <input type="password" name="new_password_confirmation" 
                                           class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all duration-300 font-bold"
                                           placeholder="••••••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="showNotification('Changement de mot de passe bientôt disponible', 'info')" 
                                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-10 py-4 rounded-3xl font-bold transition-all duration-300 shadow-xl shadow-red-200 hover:scale-105 flex items-center gap-3">
                            <i class="bi bi-shield-lock-fill text-xl"></i>
                            Mettre à jour la Sécurité
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

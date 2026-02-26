<div id="tab-specialists" class="tab-pane bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
    <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Validation des Spécialistes</h2>
            <p class="text-slate-500 font-medium">Examinez et validez les inscriptions des médecins externes</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-2xl text-sm font-bold border border-blue-100">
                {{ $pendingSpecialists->count() }} Demande(s) en attente
            </span>
        </div>
    </div>

    <div class="p-8">
        @if($allSpecialists->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">
                    <i class="bi bi-person-badge text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Aucun médecin inscrit</h3>
                <p class="text-slate-500 max-w-sm">Dès qu'un médecin externe s'inscrira, il apparaîtra ici pour validation.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($allSpecialists as $specialist)
                    <div class="group bg-slate-50 p-6 rounded-3xl border-2 {{ $specialist->statut === 'actif' ? 'border-green-100 bg-green-50/10' : 'border-transparent' }} hover:border-blue-500/30 hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300" id="specialist-card-{{ $specialist->id }}">
                        <div class="flex flex-col md:flex-row gap-8">
                            <!-- Infos Médecin -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex gap-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-200">
                                            {{ strtoupper(substr($specialist->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3">
                                                <h3 class="text-xl font-black text-slate-900">Dr. {{ $specialist->prenom }} {{ $specialist->nom }}</h3>
                                                @if($specialist->statut === 'actif')
                                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-green-200 flex items-center gap-1">
                                                        <i class="bi bi-check-circle-fill"></i> Approuvé
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-orange-200 flex items-center gap-1">
                                                        <i class="bi bi-hourglass-split"></i> En attente
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-blue-600 font-bold flex items-center gap-2 mt-1">
                                                <i class="bi bi-patch-check-fill"></i>
                                                {{ $specialist->specialite }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Inscrit le</div>
                                        <div class="text-sm font-bold text-slate-700">{{ $specialist->created_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                        <div class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-2">Contact</div>
                                        <div class="space-y-1">
                                            <div class="text-sm border-b border-slate-50 pb-1 mb-1 font-medium text-slate-700 flex items-center gap-2">
                                                <i class="bi bi-envelope text-blue-500"></i> {{ $specialist->email }}
                                            </div>
                                            <div class="text-sm font-medium text-slate-700 flex items-center gap-2">
                                                <i class="bi bi-telephone text-green-500"></i> {{ $specialist->telephone }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                        <div class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-2">Identification</div>
                                        <div class="space-y-1">
                                            @if($specialist->numero_ordre)
                                                <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                                    <i class="bi bi-hash text-blue-500"></i> N° Ordre: {{ $specialist->numero_ordre }}
                                                </div>
                                            @endif
                                            @if($specialist->numero_matricule)
                                                <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                                    <i class="bi bi-hash text-indigo-500"></i> Matricule: {{ $specialist->numero_matricule }}
                                                </div>
                                            @endif
                                            @if($specialist->numero_diplome)
                                                <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                                    <i class="bi bi-hash text-purple-500"></i> N° Diplôme: {{ $specialist->numero_diplome }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 mt-2 border-t border-slate-50 pt-1 line-clamp-2">{{ $specialist->adresse_residence ?? 'Adresse non spécifiée' }}</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200 lg:col-span-1 col-span-2">
                                        <div class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-3">Documents Justificatifs</div>
                                        <div class="flex flex-wrap gap-2">
                                            @if($specialist->diplome_path)
                                                <button onclick="openDocModal('{{ Storage::url($specialist->diplome_path) }}', 'Diplôme — Dr. {{ $specialist->prenom }} {{ $specialist->nom }}')"
                                                    class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors flex items-center gap-1.5">
                                                    <i class="bi bi-file-earmark-image-fill"></i> Diplôme
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Diplôme manquant</span>
                                            @endif

                                            @if($specialist->id_card_recto_path)
                                                <button onclick="openDocModal('{{ Storage::url($specialist->id_card_recto_path) }}', 'CNI Recto — Dr. {{ $specialist->prenom }} {{ $specialist->nom }}')"
                                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
                                                    <i class="bi bi-person-vcard-fill"></i> CNI Recto
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-400 italic">CNI Recto manquant</span>
                                            @endif

                                            @if($specialist->id_card_verso_path)
                                                <button onclick="openDocModal('{{ Storage::url($specialist->id_card_verso_path) }}', 'CNI Verso — Dr. {{ $specialist->prenom }} {{ $specialist->nom }}')"
                                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
                                                    <i class="bi bi-person-vcard-fill"></i> CNI Verso
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-400 italic">CNI Verso manquant</span>
                                            @endif

                                            @if($specialist->video_verification_path)
                                                <button onclick="openVideoModal('{{ Storage::url($specialist->video_verification_path) }}', 'Vidéo KYC — Dr. {{ $specialist->prenom }} {{ $specialist->nom }}')"
                                                    class="px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors flex items-center gap-1.5 border border-red-200">
                                                    <i class="bi bi-play-circle-fill"></i> ▶ Vidéo KYC
                                                </button>
                                            @else
                                                <span class="text-xs text-red-400 italic flex items-center gap-1">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Vidéo manquante
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    
                                    <!-- Nouvelle Section Sécurité & Affiliation -->
                                    <div class="col-span-2 lg:col-span-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 mt-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <div class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-2">Affiliation</div>
                                                <div class="text-sm">
                                                    <span class="font-bold text-slate-700">{{ $specialist->affiliation_type === 'hospital' ? 'Hôpital / Clinique' : 'Superviseur Indépendant' }}</span>
                                                    <div class="text-slate-600">{{ $specialist->affiliation_name }}</div>
                                                    <div class="text-slate-500 text-xs mt-1">Conatct Référent : {{ $specialist->affiliation_contact }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-slate-400 font-bold uppercase tracking-tight mb-2">Vérification de Sécurité</div>
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <i class="bi {{ $specialist->is_email_verified ? 'bi-check-circle-fill text-green-500' : 'bi-x-circle-fill text-red-500' }}"></i>
                                                        <span class="{{ $specialist->is_email_verified ? 'text-slate-700' : 'text-red-600 font-bold' }}">
                                                            {{ $specialist->is_email_verified ? 'Email Vérifié (OTP)' : 'Email NON Vérifié' }}
                                                        </span>
                                                    </div>
                                                    @if($specialist->video_verification_path)
                                                        <div class="flex items-center gap-2 text-sm text-green-600">
                                                            <i class="bi bi-check-circle-fill"></i> Vidéo KYC Reçue
                                                        </div>
                                                    @else
                                                        <div class="flex items-center gap-2 text-sm text-red-600 font-bold">
                                                            <i class="bi bi-exclamation-triangle-fill"></i> Vidéo KYC Manquante
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex md:flex-col justify-end gap-3 min-w-[180px]">
                                @if($specialist->statut === 'inactif')
                                    <button onclick="processSpecialistValidation({{ $specialist->id }}, 'approve')" class="flex-1 px-6 py-4 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:shadow-emerald-300 hover:scale-[1.03] transition-all flex items-center justify-center gap-2">
                                        <i class="bi bi-check-lg text-xl"></i> Approuver
                                    </button>
                                    <button onclick="processSpecialistValidation({{ $specialist->id }}, 'reject')" class="flex-1 px-6 py-4 bg-white border-2 border-red-200 text-red-600 rounded-2xl font-bold hover:bg-red-50 hover:border-red-300 transition-all flex items-center justify-center gap-2">
                                        <i class="bi bi-x-lg text-lg"></i> Rejeter
                                    </button>
                                @else
                                    <div class="flex-1 flex items-center justify-center p-4 bg-green-50 rounded-2xl border border-green-100">
                                        <span class="text-green-600 font-bold text-sm">Médecin Validé</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ====== MODAL: Image / Document Viewer ====== -->
<div id="docModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-[999] flex items-center justify-center p-4" onclick="closeDocModal(event)">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 id="docModalTitle" class="text-lg font-black text-slate-900 truncate"></h3>
            <button onclick="closeDocModal()" class="text-slate-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-xl">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto flex items-center justify-center p-4 bg-slate-50">
            <img id="docModalImg" src="" alt="Document" class="max-w-full max-h-[70vh] rounded-2xl shadow-lg object-contain">
        </div>
        <div class="px-6 py-3 border-t border-slate-100 flex justify-end gap-3">
            <a id="docModalDownload" href="#" target="_blank" class="px-5 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-blue-700 transition-colors">
                <i class="bi bi-download"></i> Ouvrir dans un nouvel onglet
            </a>
        </div>
    </div>
</div>

<!-- ====== MODAL: KYC Video Player ====== -->
<div id="videoModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden z-[999] flex items-center justify-center p-4" onclick="closeVideoModal(event)">
    <div class="bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h3 id="videoModalTitle" class="text-lg font-black text-white flex items-center gap-2 truncate">
                <i class="bi bi-camera-video-fill text-red-400"></i>
                <span></span>
            </h3>
            <button onclick="closeVideoModal()" class="text-slate-400 hover:text-red-400 transition-colors p-2 hover:bg-red-900/30 rounded-xl">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-4 bg-black">
            <video id="kycVideoPlayer" controls class="w-full rounded-2xl" style="max-height:60vh;">
                <source id="kycVideoSource" src="" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div>
        <div class="px-6 py-3 border-t border-slate-700 flex justify-between items-center">
            <span class="text-sm text-slate-400 font-medium">Vidéo de vérification d'identité (KYC)</span>
            <a id="videoModalDownload" href="#" target="_blank" class="px-5 py-2 bg-red-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-red-700 transition-colors">
                <i class="bi bi-download"></i> Télécharger
            </a>
        </div>
    </div>
</div>

<script>
    // --- Document (image) modal ---
    function openDocModal(url, title) {
        document.getElementById('docModalTitle').textContent = title;
        document.getElementById('docModalImg').src = url;
        document.getElementById('docModalDownload').href = url;
        const modal = document.getElementById('docModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDocModal(event) {
        if (event && event.target !== document.getElementById('docModal')) return;
        document.getElementById('docModal').classList.add('hidden');
        document.getElementById('docModal').classList.remove('flex');
        document.getElementById('docModalImg').src = '';
    }

    // --- Video modal ---
    function openVideoModal(url, title) {
        document.getElementById('videoModalTitle').querySelector('span').textContent = title;
        document.getElementById('kycVideoSource').src = url;
        document.getElementById('videoModalDownload').href = url;
        const player = document.getElementById('kycVideoPlayer');
        player.load();
        const modal = document.getElementById('videoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        player.play().catch(() => {});
    }

    function closeVideoModal(event) {
        if (event && event.target !== document.getElementById('videoModal')) return;
        const player = document.getElementById('kycVideoPlayer');
        player.pause();
        player.currentTime = 0;
        document.getElementById('videoModal').classList.add('hidden');
        document.getElementById('videoModal').classList.remove('flex');
        document.getElementById('kycVideoSource').src = '';
    }

    // --- Specialist validation ---
    function processSpecialistValidation(id, action) {
        if (!confirm(`Êtes-vous sûr de vouloir ${action === 'approve' ? 'approuver' : 'rejeter'} ce médecin ?`)) return;

        fetch(`{{ url('admin-system/specialists') }}/${id}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById(`specialist-card-${id}`);
                card.style.opacity = '0';
                card.style.transform = 'translateX(20px)';
                setTimeout(() => { location.reload(); }, 500);
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur serveur lors de la validation');
        });
    }
</script>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HospitSIS - {{ $patient->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    
    <style>
        :root { --med-primary: #4e73df; --bg-light: #f8f9fc; --danger-med: #e74a3b; --success-med: #1cc88a; }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        .top-banner { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); height: 160px; border-radius: 0 0 30px 30px; }
        .main-card { background: white; border-radius: 20px; margin-top: -70px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 25px; border: none; }
        .fiche-card { background: white; border-radius: 18px; border: 1px solid #e3e6f0; margin-bottom: 25px; overflow: hidden; transition: 0.3s; }
        .critical-fiche { border-left: 8px solid var(--danger-med) !important; box-shadow: 0 0 20px rgba(231, 74, 59, 0.25) !important; }
        .constante-label { font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
        .constante-value { font-size: 1.2rem; font-weight: 800; color: #1e293b; }
        .note-box { background: #f8fafc; border-radius: 14px; padding: 18px; border-left: 4px solid var(--med-primary); font-style: italic; }
        .btn-circle { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.3s; cursor: pointer; }
        .status-badge { font-size: 0.65rem; font-weight: 800; padding: 5px 12px; border-radius: 8px; text-transform: uppercase; }
        
        /* Nouveau style pour les prescriptions en mode "Card" */
        .presc-card { background: white; border-radius: 15px; border: 1px solid #eef2f7; transition: 0.3s; position: relative; overflow: hidden; }
        .presc-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .presc-card.signed { border-left: 5px solid var(--success-med); }
        .presc-card.pending { border-left: 5px solid #f6c23e; }
        .med-icon { width: 45px; height: 45px; border-radius: 12px; background: #f0f4ff; color: var(--med-primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        
        .bg-purple { background-color: #7e22ce !important; }
        .bg-purple-subtle { background-color: #f3e8ff !important; }
        .text-purple { color: #7e22ce !important; }
        .border-purple-subtle { border-color: #e9d5ff !important; }
    </style>
</head>
<body>

<div class="top-banner p-3">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-white mb-0">HospitSIS <span class="fw-light text-white-50">médical</span></h5>
        <a href="{{ route('medecin.dashboard') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold">Retour</a>
    </div>
</div>

<div class="container mb-5">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 fw-bold">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="main-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-auto text-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px; height:80px; font-size:28px; font-weight:bold; border: 4px solid white;">
                    {{ strtoupper(substr($patient->name, 0, 1)) }}{{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
            </div>
            <div class="col-md text-center text-md-start">
                <h2 class="fw-bold mb-1">{{ $patient->name }} {{ $patient->first_name }}
                    @if($patient->allergies)
                    <button class="btn btn-danger btn-sm rounded-pill ms-2" onclick="Swal.fire({title:'⚠️ ALLERGIES', text:'{{ is_array($patient->allergies) ? implode(', ', $patient->allergies) : $patient->allergies }}', icon:'error'})">ALLERGIES</button>
                    @endif
                </h2>
                <div class="text-muted small">IPU: {{ $patient->ipu }} • {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : '?' }} ans</div>
            </div>
            <div class="col-md-auto d-flex gap-2 mt-3 mt-md-0 justify-content-center">
                <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="confirmArchive()">
                    <i class="fas fa-check-double me-2"></i>Terminer
                </button>

                <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-prescription me-2"></i>Nouvelle Prescription
                </a>
                <div class="dropdown">
                    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-2"></i>Examen
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2">
                        <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalAddExamen"><i class="fas fa-heartbeat me-2 text-danger"></i>Signes vitaux</a></li>
                        <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalAddBiology"><i class="fas fa-vial me-2 text-primary"></i>Analyse de sang (Biologie)</a></li>
                        <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalAddImaging"><i class="fas fa-x-ray me-2 text-info"></i>Radiologie / Imagerie</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalAddDetailedExam"><i class="fas fa-file-medical-alt me-2 text-success"></i>Examen clinique détaillé</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm d-inline-flex">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-journal">Carnet de Santé</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-prescriptions">Prescriptions</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-labo">Analyses & Imagerie</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-coords">Coordonnées</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-journal">
            <div class="row">
                <div class="col-lg-8">
                    @forelse($allExams as $exam)
                        @php
                            $isClinical = $exam instanceof \App\Models\ClinicalObservation;
                            $isNurseNote = $isClinical && $exam->type === 'nurse_note';
                            $examType = $isClinical ? $exam->type : 'consultation';
                            
                            $isCriticalTemp = ($exam->temperature >= 38.5 || $exam->temperature <= 35.5);
                            $isCriticalPulse = ($exam->pulse >= 120 || $exam->pulse <= 50);
                            $isCriticalOverall = $exam->is_critical ?? ($isCriticalTemp || $isCriticalPulse);
                        @endphp
                        
                        <div class="fiche-card {{ $isCriticalOverall ? 'critical-fiche' : '' }} mb-4">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $isCriticalOverall ? 'bg-danger' : 'bg-light text-primary' }} rounded-pill px-3 py-2 fw-bold small">
                                        {{ \Carbon\Carbon::parse($exam->observation_datetime ?? $exam->created_at)->format('d/m/Y à H:i') }}
                                    </span>
                                    <span class="badge rounded-pill px-3 py-2 fw-bold small border 
                                        {{ $isNurseNote ? 'bg-secondary-subtle text-secondary border-secondary-subtle' : ($examType === 'detailed' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-blue-subtle text-blue border-blue-subtle') }}">
                                        @if($isNurseNote) <i class="fas fa-hand-holding-medical me-1"></i>Soin Infirmier
                                        @elseif($examType === 'detailed') <i class="fas fa-file-medical-alt me-1"></i>Examen Clinique 
                                        @else <i class="fas fa-notes-medical me-1"></i>Suivi @endif
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-{{ ($exam->urgency ?? '') === 'critique' ? 'danger' : (($exam->urgency ?? '') === 'urgent' ? 'warning' : 'info') }} rounded-pill px-2 py-1 fw-bold small">{{ ucfirst($exam->urgency ?? 'Normal') }}</span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                @if($examType !== 'detailed' && !$isNurseNote)
                                    <div class="row g-0 text-center border rounded-4 bg-light mb-3 overflow-hidden">
                                        <div class="col border-end p-2">
                                            <div class="constante-label">Temp</div>
                                            <div class="constante-value {{ $isCriticalTemp ? 'text-danger' : '' }}">{{ $exam->temperature ?? '--' }}°</div>
                                        </div>
                                        <div class="col border-end p-2">
                                            <div class="constante-label">Pouls</div>
                                            <div class="constante-value {{ $isCriticalPulse ? 'text-danger' : '' }}">{{ $exam->pulse ?? '--' }}</div>
                                        </div>
                                        <div class="col border-end p-2">
                                            <div class="constante-label">Poids / Taille</div>
                                            <div class="constante-value small">{{ $exam->weight ?? '--' }}kg / {{ $exam->height ?? '--' }}cm</div>
                                        </div>
                                        <div class="col p-2">
                                            <div class="constante-label">Agent</div>
                                            <div class="constante-value small text-truncate px-1">{{ $exam->user->name ?? $exam->doctor->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="note-box {{ $examType === 'detailed' ? 'bg-white border-0 p-0 text-dark fs-6' : 'small text-dark' }}">
                                    @if($examType === 'detailed')
                                        <div class="fw-bold mb-2 text-success">Notes d'examen :</div>
                                        <div style="white-space: pre-wrap; line-height: 1.6;">{{ $exam->notes ?? $exam->reason ?? 'Aucune note.' }}</div>
                                    @else
                                        "{{ $exam->notes ?? $exam->reason ?? 'Aucune note.' }}"
                                    @endif
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                                    @if($isClinical)
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick='editFiche(@json($exam))'>
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </button>
                                    @endif
                                    <form action="{{ $isClinical ? route('observations.destroy', $exam->id) : route('medical-records.destroy', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold btn-delete-trigger">
                                            <i class="fas fa-trash-alt me-1"></i>Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-5 text-center rounded-4 border text-muted">Aucun examen enregistré.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-prescriptions">
            <div class="row g-3">
                @forelse($patient->prescriptions->sortByDesc('created_at') as $p)
                <div class="col-md-6">
                    <div class="presc-card p-3 {{ $p->is_signed ? 'signed' : 'pending' }}">
                        <div class="d-flex align-items-center mb-3">
                            <div class="med-icon me-3">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small fw-bold">{{ $p->created_at->format('d/m/Y') }}</div>
                                <h6 class="fw-bold mb-0">{{ $p->medication }}</h6>
                            </div>
                            <div>
                                @if($p->category === 'nurse')
                                    <span class="status-badge bg-warning-subtle text-warning border border-warning-subtle">Consigne Infirmier</span>
                                @else
                                    <span class="status-badge bg-info-subtle text-info border border-info-subtle">Ordonnance</span>
                                @endif
                                
                                @if($p->is_signed)
                                    <span class="status-badge bg-success-subtle text-success">Signée</span>
                                @else
                                    <span class="status-badge bg-warning-subtle text-warning">En attente</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-light rounded-3 p-2 mb-3">
                            <span class="text-muted small"><i class="fas fa-clock me-1"></i> Dosage:</span>
                            <span class="fw-bold ms-1">{{ $p->dosage ?? 'N/A' }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <button class="btn-circle bg-success text-white" onclick="confirmSendPrescription({{ $p->id }})"><i class="fas fa-paper-plane fa-xs"></i></button>
                                <button class="btn-circle bg-primary text-white" onclick='editPrescription(@json($p))'><i class="fas fa-pen fa-xs"></i></button>
                                <form action="{{ route('prescriptions.destroy', $p->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-circle bg-danger text-white btn-delete-presc"><i class="fas fa-trash-alt fa-xs"></i></button>
                                </form>
                            </div>
                            
                            @if(!$p->is_signed && auth()->id() === $p->doctor_id)
                                <form action="{{ route('prescriptions.sign', $p) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">Signer</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="bg-white p-5 text-center rounded-4 border text-muted">Aucune prescription enregistrée.</div>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="tab-labo">
            <div class="row g-3">
                @forelse($patient->labRequests->sortByDesc('created_at') as $req)
                <div class="col-md-6">
                    <div class="presc-card p-3 {{ $req->status === 'completed' ? 'signed' : 'pending' }}">
                        <div class="d-flex align-items-center mb-3">
                            <div class="med-icon me-3 bg-purple-subtle text-purple">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small fw-bold">{{ $req->created_at->format('d/m/Y') }}</div>
                                <h6 class="fw-bold mb-0">{{ $req->test_name }}</h6>
                            </div>
                            <div>
                                <span class="status-badge bg-{{ $req->status === 'completed' ? 'success' : 'warning' }}-subtle text-{{ $req->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($req->clinical_info)
                        <div class="bg-light rounded-3 p-2 mb-2 small italic">
                            <i class="fas fa-info-circle me-1"></i> {{ $req->clinical_info }}
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-muted">Dr. {{ $req->doctor->name ?? 'N/A' }}</span>
                            <div class="d-flex gap-2">
                                @if($req->status === 'completed')
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick='showLabResult(@json($req))'>
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </button>
                                @endif
                                
                                @if($req->result_file)
                                    <a href="{{ asset('storage/'.$req->result_file) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-bold">
                                        <i class="fas fa-file-pdf me-1"></i>Fiche
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="bg-white p-5 text-center rounded-4 border text-muted">Aucune demande d'analyse enregistrée.</div>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="tab-coords">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Détails du Patient</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditPatient">Modifier</button>
                </div>
                <div class="row g-4">
                    <div class="col-md-4"><p class="text-muted small mb-1">Nom</p><p class="fw-bold">{{ $patient->name }} {{ $patient->first_name }}</p></div>
                    <div class="col-md-4"><p class="text-muted small mb-1">Téléphone</p><p class="fw-bold">{{ $patient->phone }}</p></div>
                    <div class="col-md-4"><p class="text-muted small mb-1">Email</p><p class="fw-bold">{{ $patient->email }}</p></div>
                    <div class="col-md-4"><p class="text-muted small mb-1">Groupe Sanguin</p><p class="fw-bold text-danger">{{ $patient->blood_group ?? 'Non renseigné' }}</p></div>
                    <div class="col-12">
                        <div class="p-3 bg-danger-subtle text-danger rounded-4 fw-bold mb-3">
                            Allergies : {{ is_array($patient->allergies) ? implode(', ', $patient->allergies) : ($patient->allergies ?? 'Néant') }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-4">
                            <p class="text-muted small mb-1 fw-bold">Antécédents Médicaux :</p>
                            <p class="mb-0">{{ $patient->medical_history ?? 'Aucun antécédent renseigné.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- MODAL : AJOUT EXAMEN (SIGNES VITAUX) --}}
    <div class="modal fade" id="modalAddExamen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('observations.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" name="type" value="vitals">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-black text-uppercase tracking-tight" style="color: #2D3748;"><i class="fas fa-heartbeat me-2 text-danger"></i>Signes Vitaux</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6"><label class="small fw-bold mb-1">Temp (°C)</label><input type="number" step="0.1" min="30" max="45" name="temperature" class="form-control rounded-3" required></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Pouls (BPM)</label><input type="number" min="20" max="250" name="pulse" class="form-control rounded-3"></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Poids (kg)</label><input type="number" step="0.1" min="1" max="500" name="weight" class="form-control rounded-3"></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Taille (cm)</label><input type="number" min="30" max="300" name="height" class="form-control rounded-3"></div>
                        <div class="col-12"><label class="small fw-bold mb-1">Notes / Observations</label><textarea name="notes" class="form-control rounded-3" rows="3" placeholder="RAS..."></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Enregistrer les constantes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL : ANALYSE DE SANG (BIOLOGIE) --}}
    <div class="modal fade" id="modalAddBiology" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('lab.request.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <input type="hidden" name="patient_vital_id" value="{{ $patientVitals->first()->id ?? '' }}">
                <input type="hidden" name="patient_ipu" value="{{ $patient->ipu }}">
                <input type="hidden" name="patient_name" value="{{ $patient->name }} {{ $patient->first_name }}">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-black text-uppercase tracking-tight text-primary"><i class="fas fa-vial me-2"></i>Analyse de Sang</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Examens demandés</label>
                        <div class="row g-2">
                            @foreach(['NFS / Hémogramme', 'Glycémie', 'Ionogramme', 'Urée / Créatinine', 'Bilan Hépatique', 'CRP', 'TSH'] as $test)
                                <div class="col-6">
                                    <div class="form-check p-2 border rounded-3 hover:bg-light transition-all">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="tests[]" value="{{ $test }}" id="chkBio{{ $loop->index }}">
                                        <label class="form-check-label small fw-bold" for="chkBio{{ $loop->index }}">{{ $test }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Autre examen spécifique</label>
                        <input type="text" name="custom_test" class="form-control rounded-3" placeholder="Ex: Groupage, Sérologie...">
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold mb-1">Renseignements cliniques</label>
                        <textarea name="clinical_info" class="form-control rounded-3" rows="2" placeholder="Fièvre, Douleurs..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Prescrire les analyses</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL : RADIOLOGIE / IMAGERIE --}}
    <div class="modal fade" id="modalAddImaging" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('lab.request.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <input type="hidden" name="patient_vital_id" value="{{ $patientVitals->first()->id ?? '' }}">
                <input type="hidden" name="patient_ipu" value="{{ $patient->ipu }}">
                <input type="hidden" name="patient_name" value="{{ $patient->name }} {{ $patient->first_name }}">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-black text-uppercase tracking-tight text-info"><i class="fas fa-x-ray me-2"></i>Imagerie Médicale</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Type d'examen</label>
                        <div class="row g-2">
                            @foreach(['Radiographie Thorax', 'Abdomen sans préparation', 'Échographie Abdominale', 'Scanner / TDM', 'IRM', 'Écho-Doppler'] as $test)
                                <div class="col-6">
                                    <div class="form-check p-2 border rounded-3">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="tests[]" value="{{ $test }}" id="chkImg{{ $loop->index }}">
                                        <label class="form-check-label small fw-bold" for="chkImg{{ $loop->index }}">{{ $test }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold mb-1">Indication / Région</label>
                        <textarea name="clinical_info" class="form-control rounded-3" rows="2" placeholder="Ex: Douleur flanc droit, Suspicion pneumopathie..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-info text-white w-100 rounded-pill fw-bold py-2 shadow-sm">Prescrire l'imagerie</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL : EXAMEN CLINIQUE DÉTAILLÉ --}}
    <div class="modal fade" id="modalAddDetailedExam" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('observations.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" name="type" value="detailed">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-black text-uppercase tracking-tight text-success"><i class="fas fa-file-medical-alt me-2"></i>Examen Clinique Détaillé</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Compte-rendu de l'examen</label>
                        <textarea name="notes" class="form-control rounded-4 shadow-sm" rows="10" 
                            placeholder="Décrivez ici vos observations cliniques détaillées, l'interrogatoire, l'examen physique complet..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">Enregistrer l'examen clinique</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditExamen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form id="editExamenForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-3">Modifier l'examen</h5>
                    <div class="row g-3">
                        <div class="col-6"><label class="small fw-bold mb-1">Temp</label><input type="number" step="0.1" min="30" max="45" name="temperature" id="edit_temp" class="form-control"></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Pouls</label><input type="number" min="20" max="250" name="pulse" id="edit_pouls" class="form-control"></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Poids</label><input type="number" step="0.1" min="1" max="500" name="weight" id="edit_weight" class="form-control"></div>
                        <div class="col-6"><label class="small fw-bold mb-1">Taille</label><input type="number" min="30" max="300" name="height" id="edit_height" class="form-control"></div>
                        <div class="col-12"><label class="small fw-bold mb-1">Notes</label><textarea name="notes" id="edit_notes" class="form-control"></textarea></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditPrescription" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Modifier la prescription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPrescriptionForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="small fw-bold mb-1">Médicament</label><input type="text" name="medication" id="edit_med_name" class="form-control rounded-3" required></div>
                    <div class="mb-3"><label class="small fw-bold mb-1">Dosage</label><input type="text" name="dosage" id="edit_med_dosage" class="form-control rounded-3"></div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditPatient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 p-4"><h5 class="fw-bold mb-0">Coordonnées</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('patients.update', $patient->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3"><label class="small fw-bold text-muted mb-1">Téléphone</label><input type="text" name="phone" class="form-control" value="{{ $patient->phone }}"></div>
                    <div class="mb-3"><label class="small fw-bold text-muted mb-1">Email</label><input type="email" name="email" class="form-control" value="{{ $patient->email }}"></div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Groupe Sanguin</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Sélectionner...</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                <option value="{{ $group }}" {{ $patient->blood_group == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="small fw-bold text-muted mb-1">Allergies</label><textarea name="allergies" class="form-control" rows="2">{{ is_array($patient->allergies) ? implode(', ', $patient->allergies) : $patient->allergies }}</textarea></div>
                    <div class="mb-3"><label class="small fw-bold text-muted mb-1">Antécédents Médicaux</label><textarea name="medical_history" class="form-control" rows="3">{{ $patient->medical_history }}</textarea></div>
                </div>
                <div class="modal-footer border-top-0 p-4"><button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">Enregistrer</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalViewLabResult" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-purple text-white border-0 p-4">
                <h5 class="modal-title fw-black text-uppercase tracking-tight" id="labResultTitle">Détails du Résultat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                            <h6 class="fw-bold text-muted mb-3 uppercase small">Conclusion / Résultat</h6>
                            <div id="labResultConclusion" class="p-3 bg-light rounded-3 italic" style="white-space: pre-wrap; min-height: 150px;"></div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                            <h6 class="fw-bold text-muted mb-3 uppercase small">Informations Supplémentaires</h6>
                            <div class="mb-3">
                                <span class="d-block text-muted small">Date du résultat :</span>
                                <span class="fw-bold" id="labResultDate"></span>
                            </div>
                            <div class="mb-3">
                                <span class="d-block text-muted small">Technicien :</span>
                                <span class="fw-bold text-primary" id="labResultTech"></span>
                            </div>
                            <div class="mb-3">
                                <span class="d-block text-muted small">Validé par (Biologiste) :</span>
                                <span class="fw-bold text-success" id="labResultBiologist"></span>
                            </div>
                            <div id="labResultDataContainer" class="hidden">
                                <hr>
                                <h6 class="fw-bold text-muted mb-2 uppercase small">Données numériques</h6>
                                <div id="labResultDataList" class="small"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // AJOUT DE LA FONCTION ARCHIVER
    function confirmArchive() {
        Swal.fire({
            title: 'Clôturer le dossier ?',
            text: "Le patient sera archivé et retiré de votre liste active.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, terminer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('patients.archive', $patient->id) }}";
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden'; csrfToken.name = '_token'; csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden'; methodField.name = '_method'; methodField.value = 'PATCH';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function editFiche(f) {
        const form = document.getElementById('editExamenForm');
        form.action = `/observations/${f.id}`;
        document.getElementById('edit_temp').value = f.temperature;
        document.getElementById('edit_pouls').value = f.pulse;
        document.getElementById('edit_weight').value = f.weight;
        document.getElementById('edit_height').value = f.height;
        document.getElementById('edit_notes').value = f.notes;
        new bootstrap.Modal(document.getElementById('modalEditExamen')).show();
    }

    function confirmSend(id) { 
        Swal.fire({ 
            title: 'Partager ?', 
            text: "Envoyer cet examen au portail patient ?", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonText: 'Oui, partager', 
            confirmButtonColor: '#1cc88a' 
        }).then((result) => { 
            if (result.isConfirmed) {
                fetch(`/observations/${id}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Transmis !', data.message, 'success');
                    } else {
                        Swal.fire('Erreur', 'Impossible de partager cet examen.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Erreur', 'Une erreur est survenue lors de l\'envoi.', 'error');
                });
            } 
        }); 
    }

    function editPrescription(p) {
        const form = document.getElementById('editPrescriptionForm');
        form.action = `/prescriptions/${p.id}`;
        document.getElementById('edit_med_name').value = p.medication;
        document.getElementById('edit_med_dosage').value = p.dosage;
        new bootstrap.Modal(document.getElementById('modalEditPrescription')).show();
    }

    function confirmSendPrescription(id) {
        Swal.fire({ 
            title: 'Envoyer ?', 
            text: "Transmettre cette ordonnance au patient ?", 
            icon: 'info', 
            showCancelButton: true, 
            confirmButtonText: 'Oui, envoyer', 
            confirmButtonColor: '#1cc88a' 
        }).then((result) => { 
            if (result.isConfirmed) {
                fetch(`/prescriptions/${id}/share`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Transmis !', data.message, 'success');
                    } else {
                        Swal.fire('Erreur', 'Impossible de partager cette ordonnance.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Erreur', 'Une erreur est survenue lors de l\'envoi.', 'error');
                });
            } 
        });
    }

    function showLabResult(r) {
        document.getElementById('labResultTitle').innerText = r.test_name;
        document.getElementById('labResultConclusion').innerText = r.result || "Aucun détail saisi";
        document.getElementById('labResultDate').innerText = r.completed_at ? new Date(r.completed_at).toLocaleString('fr-FR') : 'N/A';
        document.getElementById('labResultTech').innerText = r.lab_technician ? r.lab_technician.name : 'N/A';
        document.getElementById('labResultBiologist').innerText = r.biologist ? r.biologist.name : 'N/A';
        
        const dataContainer = document.getElementById('labResultDataContainer');
        const dataList = document.getElementById('labResultDataList');
        
        if (r.result_data && Object.keys(r.result_data).length > 0) {
            dataContainer.classList.remove('hidden');
            dataList.innerHTML = '';
            for (const [key, value] of Object.entries(r.result_data)) {
                dataList.innerHTML += `<div class="d-flex justify-content-between border-bottom py-1">
                    <span class="text-muted">${key}</span>
                    <span class="fw-bold">${value}</span>
                </div>`;
            }
        } else {
            dataContainer.classList.add('hidden');
        }
        
        new bootstrap.Modal(document.getElementById('modalViewLabResult')).show();
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-trigger') || e.target.closest('.btn-delete-presc')) {
            const btn = e.target.closest('button');
            Swal.fire({ title: 'Supprimer ?', text: "Action irréversible !", icon: 'warning', showCancelButton: true, confirmButtonColor: '#e74a3b', confirmButtonText: 'Oui' })
            .then(r => r.isConfirmed && btn.closest('form').submit());
        }
    });
</script>
</body>
</html>
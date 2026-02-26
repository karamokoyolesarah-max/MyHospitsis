 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/appointments">
                <i class="fas fa-calendar-check"></i> Gestion des Rendez-vous
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Nouveau Rendez-vous</h2>
                    <a href="/appointments" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="/appointments" method="POST">
                    {{-- Utiliser @csrf est la méthode moderne Laravel --}}
                    @csrf 

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Informations du Rendez-vous</h5>
                            
                            {{-- CHAMP PATIENT ID (OK) --}}
                            <div class="mb-3">
                                <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                                <select class="form-select @error('patient_id') is-invalid @enderror" 
                                        id="patient_id" name="patient_id" required>
                                    <option value="">Sélectionner un patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->ipu }} - {{ $patient->first_name }} {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CHAMP DOCTOR ID (OK) --}}
                            <div class="mb-3">
                                <label for="doctor_id" class="form-label">Médecin (Optionnel)</label>
                                <select class="form-select @error('doctor_id') is-invalid @enderror" 
                                        id="doctor_id" name="doctor_id">
                                    <option value="">Sélectionner un médecin...</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->first_name }} {{ $doctor->name }} 
                                            {{-- NOTE: J'ai enlevé l'affichage de specialty si ce n'est pas un attribut direct du modèle User --}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('doctor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- AJOUT CRITIQUE 1 : CHAMP SERVICE ID --}}
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_id') is-invalid @enderror" 
                                        id="service_id" name="service_id" required>
                                    <option value="">Sélectionner un service...</option>
                                    {{-- NOTE: Assurez-vous que $services est passé depuis le contrôleur --}}
                                    @if(isset($services))
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- CHAMP DATE (OK) --}}
                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Date du Rendez-vous <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" 
                                       id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                                @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CHAMP HEURE (OK) --}}
                            <div class="mb-3">
                                <label for="appointment_time" class="form-label">Heure du Rendez-vous <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('appointment_time') is-invalid @enderror" 
                                       id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}" required>
                                @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- AJOUT CRITIQUE 2 : CHAMP DURATION --}}
                            <div class="mb-3">
                                <label for="duration" class="form-label">Durée (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                        id="duration" name="duration" value="{{ old('duration', 30) }}" min="15" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Détails</h5>
                            
                            {{-- CHAMP STATUS (OK) --}}
                            <div class="mb-3">
                                <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Programmé</option>
                                    <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    <option value="no_show" {{ old('status') == 'no_show' ? 'selected' : '' }}>Absent</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CORRECTION 3 : CHAMP TYPE (name changé de 'appointment_type' à 'type' et rendu REQUIRED) --}}
                            <div class="mb-3">
                                <label for="type" class="form-label">Type de Rendez-vous <span class="text-danger">*</span></label>
                                {{-- Le nom du champ doit être 'type' pour correspondre à la validation dans le contrôleur --}}
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required> 
                                    <option value="">Sélectionner...</option>
                                    <option value="consultation" {{ old('type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                    <option value="follow_up" {{ old('type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                                    <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                                    <option value="routine_checkup" {{ old('type') == 'routine_checkup' ? 'selected' : '' }}>Contrôle de routine</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CHAMP REASON (OK) --}}
                            <div class="mb-3">
                                <label for="reason" class="form-label">Motif de la Consultation</label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" 
                                             id="reason" name="reason" rows="4">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CHAMP NOTES (OK) --}}
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                             id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le Rendez-vous
                            </button>
                            <a href="/appointments" class="btn btn-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
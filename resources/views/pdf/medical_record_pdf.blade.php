<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Document Médical - {{ $record->patient_name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 20px; line-height: 1.5; }
        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 5px; }
        
        .hospital-name { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .doctor-info { font-size: 11px; color: #4b5563; margin-top: 5px; }
        
        .document-header { background: #f3f4f6; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .doc-type { font-size: 16px; font-weight: bold; color: #111827; text-transform: uppercase; margin-bottom: 5px; }
        .doc-meta { font-size: 10px; color: #6b7280; }

        .patient-box { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .label { font-size: 10px; font-weight: bold; color: #6b7280; text-transform: uppercase; }
        .value { font-size: 12px; font-weight: bold; color: #111827; }

        .section-title { font-size: 12px; font-weight: bold; color: #4f46e5; background: #eef2ff; padding: 5px 10px; border-left: 4px solid #4f46e5; margin: 20px 0 10px 0; }
        .content-box { font-size: 11px; padding: 0 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .vitals-table td { border: 1px solid #e5e7eb; padding: 8px; text-align: center; font-size: 11px; }

        .prescription-list { background: #fdfbf7; border: 1px solid #f97316; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; font-size: 12px; }
        .signature-box { margin-top: 50px; text-align: right; }
        .signature-line { border-top: 1px solid #000; display: inline-block; width: 200px; margin-top: 40px; }
    </style>
</head>
<body>
    @php
        $serviceCode = $record->service ? strtoupper(strtok($record->service->code, '-')) : 'GEN';
        $meta = $record->meta ?? [];
        
        $titles = [
            'CERT' => 'CERTIFICAT MÉDICAL',
            'ORD' => 'ORDONNANCE MÉDICALE',
            'REF' => 'LETTRE DE LIAISON / RÉFÉRENCE',
            'GEN' => 'RAPPORT DE CONSULTATION',
            'EXT' => 'RAPPORT DE CONSULTATION',
        ];
        $docTitle = $titles[$serviceCode] ?? 'COMPTE-RENDU MÉDICAL';
    @endphp

    <div class="header">
        <table width="100%">
            <tr>
                <td width="60%">
                    <span class="hospital-name">HospitSIS / {{ $doctor->nom ?? 'Service Médical' }}</span><br>
                    <div class="doctor-info">
                        @if($doctor)
                            <strong>Dr. {{ $doctor->prenom }} {{ $doctor->nom }}</strong><br>
                            {{ $doctor->specialite ?? '' }}<br>
                            Tel: {{ $doctor->telephone ?? 'N/A' }} | Email: {{ $doctor->email ?? '' }}<br>
                            @if(isset($doctor->numero_ordre)) N° Ordre: {{ $doctor->numero_ordre }} @endif
                        @endif
                    </div>
                </td>
                <td width="40%" style="text-align: right;">
                    <div class="doc-type">{{ $docTitle }}</div>
                    <div class="doc-meta">Réf: #{{ $record->id }} | Date: {{ $record->created_at->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="patient-box">
        <table width="100%">
            <tr>
                <td width="50%">
                    <div class="label">Patient</div>
                    <div class="value">{{ $record->patient_name }}</div>
                </td>
                <td width="25%">
                    <div class="label">IPU</div>
                    <div class="value">{{ $record->patient_ipu }}</div>
                </td>
                <td width="25%">
                    <div class="label">Âge / Sexe</div>
                    <div class="value">{{ $record->patient->age ?? 'N/A' }} ans / {{ $record->patient->gender ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($serviceCode != 'CERT' && $serviceCode != 'REF')
        <div class="section-title">Constantes Vitales</div>
        <table class="vitals-table">
            <tr>
                <td><strong>Température</strong><br>{{ $record->temperature ?? 'N/A' }} °C</td>
                <td><strong>Tension</strong><br>{{ $record->blood_pressure ?? 'N/A' }}</td>
                <td><strong>Pouls</strong><br>{{ $record->pulse ?? 'N/A' }} BPM</td>
                <td><strong>Poids</strong><br>{{ $record->weight ?? 'N/A' }} kg</td>
            </tr>
        </table>
    @endif

    {{-- CONTENU DYNAMIQUE SELON LE TYPE --}}
    
    @if($serviceCode == 'ORD')
        <div class="section-title">Prescription Médicamenteuse</div>
        <div class="prescription-list">
@if($meta['prescriptions_list'] ?? false)
{{ $meta['prescriptions_list'] }}
@else
{{ $record->ordonnance }}
@endif
        </div>
        
        @if($meta['exams_requested'] ?? false)
            <div class="section-title">Examens demandés</div>
            <div class="content-box">{{ $meta['exams_requested'] }}</div>
        @endif

        @if($meta['lifestyle_advice'] ?? false)
            <div class="section-title">Conseils</div>
            <div class="content-box">{{ $meta['lifestyle_advice'] }}</div>
        @endif

    @elseif($serviceCode == 'CERT')
        <div class="section-title">Objet : {{ $meta['cert_type'] ?? 'Certificat Médical' }}</div>
        <div class="content-box" style="font-size: 14px; margin-top: 20px;">
            Je soussigné, Dr {{ $doctor->nom ?? '...' }}, certifie après examen clinique de M/Mme <strong>{{ $record->patient_name }}</strong>, que : <br><br>
            
            @if(($meta['cert_type'] ?? '') == 'repos')
                L'état de santé de l'intéressé(e) nécessite un repos médical de <strong>{{ $meta['rest_duration'] ?? '...' }}</strong> jours, 
                à compter du {{ \Carbon\Carbon::parse($meta['start_date'] ?? now())->format('d/m/Y') }}.
            @else
                {!! nl2br(e($meta['observations'] ?? 'L\'état de santé est compatible avec les activités citées.')) !!}
            @endif
            
            <br><br>
            Certificat établi pour servir et valoir ce que de droit.
            @if($meta['destination'] ?? false)
                <br>Document à l'attention de : {{ $meta['destination'] }}
            @endif
        </div>

    @elseif($serviceCode == 'REF')
        <div class="section-title">À l'attention de : {{ $meta['destinataire'] ?? 'Confrère' }}</div>
        <div class="content-box">
            <strong>Hôpital :</strong> {{ $meta['etablissement_dest'] ?? 'N/A' }}<br><br>
            <strong>Motif :</strong> {{ $meta['motif_reference'] ?? 'N/A' }}<br><br>
            <strong>Résumé clinique :</strong><br>
            {!! nl2br(e($meta['resume_clinique'] ?? 'N/A')) !!}<br><br>
            <strong>Questions posées :</strong><br>
            {!! nl2br(e($meta['questions_confrere'] ?? 'N/A')) !!}
        </div>

    @else {{-- GENERIC REPORT --}}
        <div class="section-title">Motif & Histoire</div>
        <div class="content-box">
            <strong>Motif :</strong> {{ $record->reason }}<br>
            <strong>Antécédents :</strong> {{ $meta['antecedents'] ?? 'RAS' }}<br>
            <strong>Histoire :</strong> {{ $meta['histoire_maladie'] ?? 'N/A' }}
        </div>

        <div class="section-title">Examen Clinique</div>
        <div class="content-box">
            <table width="100%" style="font-size: 10px;">
                <tr>
                    <td><strong>État Général:</strong> {{ $meta['etat_general'] ?? 'N/A' }}</td>
                    <td><strong>Conjonctives:</strong> {{ $meta['conjonctives'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Déshydratation:</strong> {{ $meta['deshydratation'] ?? 'N/A' }}</td>
                    <td><strong>Oedèmes:</strong> {{ $meta['oedemes'] ?? 'N/A' }}</td>
                </tr>
            </table>
            <br>
            <strong>Appareil Respi/CV :</strong> {{ $meta['examen_respi_cv'] ?? 'Sp' }}<br>
            <strong>Abdomen/Autres :</strong> {{ $meta['examen_abdominal'] ?? 'Sp' }}
        </div>

        <div class="section-title">Conclusion</div>
        <div class="content-box">
            <strong>Hypothèses Diagnostiques :</strong> {{ $meta['hypotheses'] ?? 'N/A' }}<br>
            <strong>Conduite à Tenir :</strong> {{ $meta['conduite_a_tenir'] ?? 'N/A' }}
        </div>
    @endif

    <div class="signature-box">
        <p>Fait à ......................., le {{ date('d/m/Y') }}</p>
        <p>Signature et Cachet du Médecin</p>
        <div class="signature-line"></div>
    </div>

    <div class="footer">
        HospitSIS - Système de Santé Intégré - Généré le {{ date('d/m/Y H:i') }} - ID: {{ $record->id }}
    </div>
</body>
</html>

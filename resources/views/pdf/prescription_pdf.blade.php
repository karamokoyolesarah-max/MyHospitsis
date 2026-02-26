<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance - {{ $patient->first_name }} {{ $patient->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 40px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .hospital-name { font-size: 24px; font-weight: bold; color: #4f46e5; }
        .document-title { text-align: right; font-size: 20px; font-weight: bold; color: #111827; }
        .doctor-info { margin-bottom: 30px; }
        .patient-info { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 40px; }
        .prescription-body { min-height: 400px; padding: 20px; border-left: 3px solid #4f46e5; }
        .signature { margin-top: 50px; text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <span class="hospital-name">HospitSIS</span><br>
                    <small>Système d'Information Sanitaire</small>
                </td>
                <td class="document-title">ORDONNANCE</td>
            </tr>
        </table>
    </div>

    <div class="doctor-info">
        <strong>Prescrit par :</strong> Dr. {{ $doctor->name }}<br>
        <strong>Spécialité :</strong> {{ $doctor->service->name ?? 'Médecine Générale' }}
    </div>

    <div class="patient-info">
        <table width="100%">
            <tr>
                <td><strong>Patient :</strong> {{ $patient->first_name }} {{ $patient->name }}</td>
                <td align="right"><strong>Date :</strong> {{ now()->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>IPU :</strong> {{ $patient->ipu }}</td>
                <td align="right"><strong>Sexe :</strong> {{ $patient->gender }}</td>
            </tr>
        </table>
    </div>

    <div class="prescription-body">
        <h2 style="font-size: 18px; color: #4f46e5;">PRESCRIPTION</h2>
        <div style="white-space: pre-wrap; font-size: 16px; line-height: 1.6;">
            {{ $prescription->medication }}
            <br><br>
            <strong>Instructions :</strong><br>
            {{ $prescription->instructions }}
        </div>
    </div>

    <div class="signature">
        <p>Signature numérique :</p>
        <div style="display: inline-block; padding: 10px; border: 2px dashed #e5e7eb; min-width: 200px;">
            @if($prescription->is_signed)
                <span style="color: #059669; font-weight: bold;">[SIGNÉ NUMÉRIQUEMENT]</span><br>
                <small>{{ $prescription->signed_at }}</small>
            @else
                <span style="color: #dc2626;">[NON SIGNÉ]</span>
            @endif
        </div>
    </div>

    <div class="footer">
        Document officiel généré par HospitSIS - Validité 3 mois.
    </div>
</body>
</html>

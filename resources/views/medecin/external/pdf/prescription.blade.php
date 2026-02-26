<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance #{{ $prescription->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .container {
            padding: 40px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
        }
        .doc-title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
        }
        .doctor-info {
            font-size: 12px;
            color: #555;
            margin-bottom: 30px;
        }
        .patient-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #4f46e5;
        }
        .content {
            margin-bottom: 40px;
            min-height: 400px;
        }
        .medication-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .medication-name {
            font-weight: bold;
            font-size: 16px;
            color: #111827;
        }
        .medication-instructions {
            color: #555;
            font-style: italic;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }
        .signature {
            text-align: right;
            margin-top: 50px;
            margin-right: 20px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="logo">HospitSIS</div>
                        <div class="doctor-info">
                            <strong>Dr. {{ $doctor->prenom }} {{ $doctor->nom }}</strong><br>
                            {{ $doctor->specialite }}<br>
                            N° Ordre: {{ $doctor->numero_ordre }}<br>
                            Tél: {{ $doctor->telephone }}
                        </div>
                    </td>
                    <td class="doc-title">ORDONNANCE</td>
                </tr>
            </table>
        </div>

        <div class="patient-info">
            <strong>Patient:</strong> {{ $patient->prenom }} {{ $patient->nom }}<br>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($prescription->created_at)->format('d/m/Y') }}<br>
            <strong>ID Prescription:</strong> #{{ $prescription->id }}
        </div>

        <div class="content">
            <div class="medication-item">
                <div class="medication-name">
                    {{ $prescription->medication }} 
                    @if($prescription->dosage)
                        - {{ $prescription->dosage }}
                    @endif
                </div>
                <div class="medication-instructions">
                    {{ $prescription->instructions }}
                </div>
            </div>
        </div>

        <div class="signature">
            <div class="signature-line">
                Signature & Cachet
            </div>
        </div>

        <div class="footer">
            HospitSIS - Système d'Information Sanitaire Intégré<br>
            Ce document est valide et généré numériquement.
        </div>
    </div>
</body>
</html>

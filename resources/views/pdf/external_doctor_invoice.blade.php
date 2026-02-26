<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $appointment->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
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
        .invoice-title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .details-table {
            width: 100%;
            margin-bottom: 40px;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }
        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #f3f4f6;
            text-align: left;
            padding: 12px;
            font-size: 13px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals {
            float: right;
            width: 250px;
        }
        .total-row {
            margin-bottom: 10px;
        }
        .total-label {
            display: inline-block;
            width: 120px;
            color: #6b7280;
        }
        .total-value {
            display: inline-block;
            width: 120px;
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 18px;
            color: #4f46e5;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="logo">HospitSIS</div>
                        <div style="font-size: 12px; color: #6b7280;">Plateforme de Gestion Médicale</div>
                    </td>
                    <td class="invoice-title">FACTURE PROFORMA</td>
                </tr>
            </table>
        </div>

        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Médecin (Prestataire)</div>
                    <div class="info-box">
                        <strong>Dr. {{ $doctor->prenom }} {{ $doctor->nom }}</strong><br>
                        {{ $doctor->specialite }}<br>
                        Tél: {{ $doctor->telephone }}<br>
                        {{ $doctor->email }}
                    </div>
                </td>
                <td style="padding-left: 20px;">
                    <div class="section-title">Informations Facture</div>
                    <div class="info-box">
                        N° de Facture: #FAC-{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}<br>
                        Date: {{ now()->format('d/m/Y') }}<br>
                        Rendez-vous: {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') }}<br>
                        Statut: {{ strtoupper($appointment->status) }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Patient</div>
        <div class="info-box" style="margin-bottom: 40px;">
            <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong><br>
            Tél: {{ $patient->telephone }}<br>
            Adresse: {{ $appointment->home_address ?? 'N/A' }}
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description de la prestation</th>
                    <th style="text-align: right;">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Consultation à domicile (Général)</strong><br>
                        <span style="font-size: 11px; color: #6b7280;">Motif: {{ $appointment->reason ?? 'Non spécifié' }}</span>
                    </td>
                    <td style="text-align: right;">{{ number_format($appointment->total_amount - ($appointment->tax_amount ?? 0)) }}</td>
                </tr>
                @if($appointment->tax_amount > 0)
                <tr>
                    <td>Frais de service & Taxes</td>
                    <td style="text-align: right;">{{ number_format($appointment->tax_amount) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span class="total-label">Sous-total:</span>
                <span class="total-value">{{ number_format($appointment->total_amount - ($appointment->tax_amount ?? 0)) }} FCFA</span>
            </div>
            <div class="total-row">
                <span class="total-label">TVA / Frais:</span>
                <span class="total-value">{{ number_format($appointment->tax_amount ?? 0) }} FCFA</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label" style="color: #111827;">Total TTC:</span>
                <span class="total-value">{{ number_format($appointment->total_amount) }} FCFA</span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <div style="margin-top: 60px; font-size: 13px;">
            <p><strong>Notes importantes:</strong></p>
            <ul style="color: #4b5563;">
                <li>Cette facture est générée automatiquement par la plateforme HospitSIS.</li>
                <li>Le paiement doit être effectué conformément aux conditions convenues lors du soin.</li>
                <li>Pour toute question relative à cette facture, veuillez contacter le support ou le médecin.</li>
            </ul>
        </div>

        <div class="footer">
            HospitSIS - Système d'Information Sanitaire Intégré<br>
            Abidjan, Côte d'Ivoire | support@hospitsis.com | www.hospitsis.com
        </div>
    </div>
</body>
</html>

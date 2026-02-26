<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Consultation #{{ $appointment->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .header { border-bottom: 2px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .hospital-name { font-size: 24px; font-weight: bold; color: #1e3a8a; margin: 0; }
        .hospital-info { font-size: 12px; color: #64748b; margin: 5px 0; }
        .document-title { font-size: 20px; font-weight: bold; text-transform: uppercase; text-align: right; color: #3b82f6; margin: 0; }
        
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #94a3b8; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px; margin-top: 30px; }
        
        .grid { width: 100%; }
        .col { width: 50%; vertical-align: top; }
        
        .info-label { font-size: 11px; font-weight: bold; color: #64748b; margin-bottom: 2px; }
        .info-value { font-size: 13px; font-weight: bold; color: #1e293b; margin-bottom: 10px; }
        
        table.items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items th { background-color: #f8fafc; text-align: left; padding: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        table.items td { padding: 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        
        .totals { margin-top: 30px; margin-left: auto; width: 40%; }
        .total-row { display: table; width: 100%; padding: 5px 0; }
        .total-label { display: table-cell; font-size: 12px; color: #64748b; }
        .total-value { display: table-cell; text-align: right; font-size: 13px; font-weight: bold; }
        .grand-total { border-top: 2px solid #3b82f6; margin-top: 10px; padding-top: 10px; }
        .grand-total .total-label { font-size: 14px; font-weight: bold; color: #1e293b; }
        .grand-total .total-value { font-size: 18px; font-weight: bold; color: #3b82f6; }
        
        .footer { position: fixed; bottom: 40px; left: 40px; right: 40px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .stamp { margin-top: 50px; text-align: right; }
        .stamp-box { display: inline-block; width: 150px; height: 100px; border: 2px dashed #e2e8f0; border-radius: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="grid">
                <tr>
                    <td class="col">
                        <h1 class="hospital-name">{{ $appointment->hospital->name }}</h1>
                        <p class="hospital-info">{{ $appointment->hospital->address }}</p>
                    </td>
                    <td class="col" style="text-align: right;">
                        <h2 class="document-title">Bon de Consultation</h2>
                        <p class="hospital-info">Référence: #{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="hospital-info">Émis le: {{ date('d/m/Y H:i') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <table class="grid">
            <tr>
                <td class="col">
                    <h3 class="section-title">Informations Patient</h3>
                    <div class="info-label">Nom Complet</div>
                    <div class="info-value">{{ $appointment->patient->full_name }}</div>
                    <div class="info-label">Identifiant (IPU)</div>
                    <div class="info-value">{{ $appointment->patient->ipu }}</div>
                </td>
                <td class="col" style="padding-left: 40px;">
                    <h3 class="section-title">Détails Rendez-vous</h3>
                    <div class="info-label">Date et Heure</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->translatedFormat('l d F Y à H:i') }}</div>
                    <div class="info-label">Type de Consultation</div>
                    <div class="info-value">{{ $appointment->consultation_type === 'home' ? 'À Domicile' : 'À l\'Hôpital' }}</div>
                </td>
            </tr>
        </table>

        <h3 class="section-title">Prestations & Services</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Prix Unitaire</th>
                    <th style="text-align: right;">Quantité</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $appointment->service->name }}</strong><br>
                        <small style="color: #64748b;">
                            @if($appointment->prestations->count() > 0)
                                {{ $appointment->prestations->first()->name }}
                            @else
                                Consultation standard
                            @endif
                        </small>
                    </td>
                    <td style="text-align: right;">{{ number_format($appointment->total_amount - ($appointment->calculated_travel_fee ?? 0) - ($appointment->tax_amount ?? 0)) }} FCFA</td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">{{ number_format($appointment->total_amount - ($appointment->calculated_travel_fee ?? 0) - ($appointment->tax_amount ?? 0)) }} FCFA</td>
                </tr>
                @if($appointment->calculated_travel_fee > 0)
                <tr>
                    <td>Frais de déplacement</td>
                    <td style="text-align: right;">{{ number_format($appointment->calculated_travel_fee) }} FCFA</td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">{{ number_format($appointment->calculated_travel_fee) }} FCFA</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span class="total-label">Sous-total HT</span>
                <span class="total-value">{{ number_format($appointment->total_amount - ($appointment->tax_amount ?? 0)) }} FCFA</span>
            </div>
            <div class="total-row">
                <span class="total-label">TVA (18%)</span>
                <span class="total-value">{{ number_format($appointment->tax_amount) }} FCFA</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label">NET À PAYER</span>
                <span class="total-value">{{ number_format($appointment->total_amount) }} FCFA</span>
            </div>
        </div>

        <div class="stamp">
            <p class="info-label">Cachet et Signature</p>
            <div class="stamp-box"></div>
        </div>

        <div class="footer">
            Document généré automatiquement par HospitSIS - Logiciel de gestion hospitalière.<br>
            Ceci est une facture proforma / bon de consultation à présenter au service de facturation ou au praticien.
        </div>
    </div>
</body>
</html>

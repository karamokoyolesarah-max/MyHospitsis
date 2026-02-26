<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Rendu d'Analyses Médicales - {{ strtoupper($labRequest->patient_name) }}</title>
    <style>
        @page {
            margin: 10mm 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #262626;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        
        /* Header Section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
        }
        .logo-hd-cell {
            width: 15%;
            vertical-align: top;
        }
        .header-center-cell {
            width: 70%;
            text-align: center;
            vertical-align: top;
        }
        .logo-biogroupe-cell {
            width: 15%;
            text-align: right;
            vertical-align: top;
        }
        
        .hd-logo {
            font-size: 26pt;
            font-weight: 900;
            color: #0b2e59;
            line-height: 0.9;
            margin: 0;
        }
        .hd-subtext {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 4px;
            color: #0b2e59;
            margin-top: -2px;
        }
        
        .lab-main-title {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
        }
        .biologist-header {
            font-size: 11pt;
            font-weight: bold;
            color: #262626;
            margin: 3px 0;
        }
        .header-address {
            font-size: 7.5pt;
            color: #404040;
            margin: 1px 0;
        }

        .biogroupe-logo {
            width: 90px;
        }
        .biogroupe-text {
            font-size: 6.5pt;
            color: #64748b;
            font-weight: bold;
        }

        /* Titles and Dates */
        .print-info {
            text-align: right;
            font-size: 8pt;
            margin-top: 2mm;
        }
        .blue-divider {
            border-bottom: 1.5pt solid #2563eb;
            margin: 2mm 0 6mm 0;
        }
        .document-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5mm;
        }

        /* Patient Info Block */
        .patient-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            border-top: 1pt solid #2563eb;
            border-bottom: 1pt solid #2563eb;
            margin-bottom: 10mm;
        }
        .patient-data-cell {
            padding: 3mm 0;
            vertical-align: top;
        }
        .patient-ipu-side {
            text-align: right;
            padding: 3mm 0;
            vertical-align: top;
        }
        .ipu-number {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        .patient-name-large {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        .patient-sub-info {
            font-size: 9pt;
            color: #262626;
        }
        .barcode-visual {
            font-family: 'Courier', monospace;
            font-size: 20pt;
            letter-spacing: 1px;
            color: #000;
            margin-top: 2mm;
        }

        /* Results Table */
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        .results-table thead th {
            border-bottom: 1pt solid #262626;
            padding: 4pt 3pt;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
        }
        
        .category-row {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10pt 0 4pt 0;
            text-align: left;
        }
        .test-title-row {
            font-size: 10pt;
            font-weight: bold;
            padding: 4pt 0 4pt 5pt;
            text-align: left;
        }
        .test-subtitle {
            font-size: 8pt;
            color: #666;
            font-weight: normal;
            font-style: italic;
            padding-left: 10pt;
        }
        
        .result-row td {
            padding: 2.5pt 3pt;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .result-label {
            padding-left: 20pt !important;
            width: 50%;
        }
        .result-value {
            width: 13%;
            text-align: center;
            font-weight: bold;
            color: #2563eb;
        }
        .result-unit {
            width: 10%;
            text-align: center;
            font-size: 9pt;
        }
        .result-range {
            width: 17%;
            text-align: center;
            font-size: 9pt;
            color: #262626;
        }
        .result-prev {
            width: 10%;
            text-align: center;
        }

        /* Footer */
        .footer-section {
            position: fixed;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-cell {
            width: 20%;
            vertical-align: bottom;
        }
        .signature-area {
            width: 80%;
            text-align: right;
            vertical-align: top;
        }
        .validation-stamp-wrapper {
            display: inline-block;
            text-align: center;
            position: relative;
            margin-top: 5mm;
        }
        .signature-text {
            font-size: 9.5pt;
            margin-bottom: 8mm;
        }
        .validation-stamp-box {
            border: 2pt solid #4a6fa5;
            color: #4a6fa5;
            padding: 5pt 12pt;
            border-radius: 6pt;
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            transform: rotate(-4deg);
            background: rgba(255, 255, 255, 0.8);
            display: inline-block;
        }
        .page-info {
            text-align: right;
            font-size: 8.5pt;
            color: #6b7280;
            margin-top: 5mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-hd-cell">
                    @if($labRequest->hospital && $labRequest->hospital->logo)
                        <img src="{{ public_path('storage/' . $labRequest->hospital->logo) }}" style="max-height: 70px; max-width: 100%;">
                    @else
                        <div class="hd-logo">H</div>
                        <div class="hd-subtext">LOGO</div>
                    @endif
                </td>
                <td class="header-center-cell">
                    <h1 class="lab-main-title">LABORATOIRE D'ANALYSES MÉDICALES</h1>
                    @php
                        $bioName = $labRequest->biologist->name ?? 'Responsable Biologiste';
                        if (!str_contains(strtolower($bioName), 'dr')) {
                            $bioName = 'Dr ' . $bioName;
                        }
                        $bioTitle = $labRequest->biologist->role === 'doctor_lab' ? 'Pharmacienne Biologiste' : 'Biologiste Responsable';
                    @endphp
                    <p class="biologist-header">{{ $bioName }}. {{ $bioTitle }}</p>
                    <p class="header-address">{{ $labRequest->hospital->address ?? '' }}</p>
                    <p class="header-address">Tél: {{ $labRequest->hospital->phone ?? '' }} - Email: {{ $labRequest->hospital->email ?? '' }}</p>
                </td>
                <td class="logo-biogroupe-cell">
                    <div style="font-weight: bold; color: #1e3a8a; font-size: 13pt;">BioGroupe</div>
                    <div class="biogroupe-text">Assurance qualité Santé</div>
                </td>
            </tr>
        </table>

        <div class="print-info">Imprimé le : {{ now()->format('d/m/Y') }}</div>
        
        <!-- Patient Row inside the border -->
        <table class="patient-info-table">
            <tr>
                <td class="patient-data-cell">
                    <div class="patient-name-large">{{ strtoupper($labRequest->patient_name) }}</div>
                    <div class="patient-sub-info">
                        Né le : {{ $labRequest->patientVital->dob ?? '-' }} &nbsp;&nbsp;&nbsp; Age : {{ $labRequest->patientVital->age ?? '-' }} Ans<br>
                        Prélèvement du : {{ $labRequest->sample_received_at ? $labRequest->sample_received_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </td>
                <td class="patient-ipu-side">
                    <div class="ipu-number">{{ $labRequest->patient_ipu }}</div>
                    <div class="barcode-visual">|||||||||||||||||||||||</div>
                </td>
            </tr>
        </table>

        <div class="document-title">Compte Rendu d'Analyses Médicales</div>

        <!-- Results Table -->
        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 50%; text-align: left;"></th>
                    <th style="width: 13%;">Résultats</th>
                    <th style="width: 10%;">Unités</th>
                    <th style="width: 17%;">Valeurs Usuelles</th>
                    <th style="width: 10%;">Antécédents</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="category-row">{{ strtoupper($labRequest->test_category ?? 'BIOLOGIE') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="test-title-row">
                        {{ $labRequest->test_name }}<br>
                        <span class="test-subtitle">Analyse effectuée sur automate qualifié</span>
                    </td>
                </tr>

                @php
                    // Logic to split result text into rows
                    $lines = explode("\n", str_replace("\r", "", $labRequest->result));
                @endphp

                @foreach($lines as $line)
                    @php
                        $line = trim($line);
                        if(empty($line)) continue;

                        $label = $line;
                        $resVal = '-';
                        $unit = '-';
                        $range = '-';

                        // Try to parse "Label : Result Unit [Range]"
                        if(str_contains($line, ':')) {
                            $parts = explode(':', $line, 2);
                            $label = trim($parts[0]);
                            $data = trim($parts[1]);

                            // Extract Range if ends with [ ... ]
                            if(preg_match('/\[(.*?)\]$/', $data, $rangeMatch)) {
                                $range = $rangeMatch[1];
                                $data = trim(str_replace($rangeMatch[0], '', $data));
                            }

                            // Split data into value and unit (e.g. "8.09 10^3/µl")
                            // We look for the first segment that is numeric
                            $dataParts = explode(' ', $data);
                            $resVal = $dataParts[0];
                            if(count($dataParts) > 1) {
                                array_shift($dataParts);
                                $unit = implode(' ', $dataParts);
                            }
                        }
                    @endphp
                    <tr class="result-row">
                        <td class="result-label">{{ $label }}</td>
                        <td class="result-value">{{ $resVal }}</td>
                        <td class="result-unit">{{ $unit }}</td>
                        <td class="result-range">{{ $range }}</td>
                        <td class="result-prev"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($labRequest->clinical_info)
            <div style="margin-top: 10mm; font-size: 8.5pt;">
                <span style="font-weight: bold; text-decoration: underline;">Note Clinique :</span> {{ $labRequest->clinical_info }}
            </div>
        @endif

        <!-- Footer fixed at bottom -->
        <div class="footer-section">
            <table class="footer-table">
                <tr>
                    <td class="qr-cell">
                        <!-- QR Code removed as requested -->
                    </td>
                    <td class="signature-area">
                        <div class="validation-stamp-wrapper">
                            <div class="signature-text">Résultat validé Par : Dr {{ $labRequest->biologist->name ?? 'OUERDANE' }}</div>
                            
                            @if($labRequest->status === 'completed')
                                <div class="validation-stamp-box">
                                    LABORATOIRE D'ANALYSES MÉDICALES<br>
                                    {{ strtoupper($bioName) }}<br>
                                    VALIDÉ LE {{ $labRequest->validated_at ? $labRequest->validated_at->format('d/m/Y') : now()->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
            <div class="page-info">Page 1 sur 1</div>
        </div>
    </div>
</body>
</html>
